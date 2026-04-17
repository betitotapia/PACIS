<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Sesión inválida.");
    }
    $id_usuario = (int)$_SESSION['user_id'];

    $fecha_oc     = isset($_POST['fecha_oc']) ? trim($_POST['fecha_oc']) : '';
    $id_proveedor = isset($_POST['id_proveedor']) ? (int)$_POST['id_proveedor'] : 0;
    $id_almacen   = isset($_POST['id_almacen']) ? (int)$_POST['id_almacen'] : 0;
    $obs          = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

    if ($fecha_oc === '') throw new Exception("Captura la fecha.");
    if ($id_proveedor <= 0) throw new Exception("Selecciona proveedor.");
    if ($id_almacen <= 0) throw new Exception("Selecciona almacén.");

    $fecha_oc = mysqli_real_escape_string($con, $fecha_oc);
    $obs      = mysqli_real_escape_string($con, $obs);

    $tmp = mysqli_query($con, "SELECT * FROM tmp_oc WHERE session_id = '$session_id'");
    if (mysqli_num_rows($tmp) == 0) throw new Exception("No hay partidas para guardar.");

    mysqli_begin_transaction($con);

    // Folio PRO (requiere consecutivos('folio_oc'))
    mysqli_query($con, "
        UPDATE consecutivos
        SET valor = LAST_INSERT_ID(valor + 1)
        WHERE nombre = 'folio_oc'
    ");
    if (mysqli_affected_rows($con) <= 0) {
        throw new Exception("No existe consecutivo 'folio_oc' en tabla consecutivos.");
    }
    $res_f = mysqli_query($con, "SELECT LAST_INSERT_ID() AS folio");
    $folio_oc = (int)mysqli_fetch_assoc($res_f)['folio'];

    // Insert encabezado
    mysqli_query($con, "
      INSERT INTO ordenes_compra
      (folio_oc, id_proveedor, id_almacen, id_usuario, fecha_oc, estatus, observaciones, total)
      VALUES
      ('$folio_oc', $id_proveedor, $id_almacen, $id_usuario, '$fecha_oc', 'ABIERTA', '$obs', 0)
    ");
    $id_oc = (int)mysqli_insert_id($con);

    // Insert detalle
    $total = 0;
    mysqli_data_seek($tmp, 0);

    while ($r = mysqli_fetch_assoc($tmp)) {
        $ref  = mysqli_real_escape_string($con, $r['referencia_tmp']);
        $desc = mysqli_real_escape_string($con, $r['descripcion_tmp']);
        $cant = (float)$r['cantidad_tmp'];
        $cost = (float)$r['costo_tmp'];

        if ($cant <= 0) continue;

        $importe = $cant * $cost;
        $total += $importe;

        mysqli_query($con, "
          INSERT INTO ordenes_compra_detalle
          (id_oc, id_producto, referencia, descripcion, cantidad_solicitada, cantidad_recibida, costo_unitario, importe)
          VALUES
          ($id_oc, NULL, '$ref', '$desc', $cant, 0, $cost, $importe)
        ");
    }

    // Actualizar total
    mysqli_query($con, "UPDATE ordenes_compra SET total = $total WHERE id_oc = $id_oc");

    // Limpiar tmp
    mysqli_query($con, "DELETE FROM tmp_oc WHERE session_id = '$session_id'");

    mysqli_commit($con);

    echo "OK|$id_oc|$folio_oc";
    exit;

} catch (Exception $e) {
    try { mysqli_rollback($con); } catch (Throwable $t) {}
    echo "Error: " . $e->getMessage();
    exit;
}
