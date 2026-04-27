<?php
/**
 * guardar_traspaso.php
 * Guarda un traspaso entre almacenes en una transacción atómica.
 * Respuesta: OK|id_traspaso|folio
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
        throw new Exception("Sesión inválida.");
    }
    $id_usuario = (int)$_SESSION['user_id'];

    $fecha        = trim($_POST['fecha_traspaso'] ?? '');
    $id_origen    = (int)($_POST['id_almacen_origen'] ?? 0);
    $id_destino   = (int)($_POST['id_almacen_destino'] ?? 0);
    $observaciones = trim($_POST['observaciones'] ?? '');

    if ($fecha === '')    throw new Exception("Debe capturar la fecha del traspaso.");
    if ($id_origen <= 0)  throw new Exception("Selecciona el almacén de origen.");
    if ($id_destino <= 0) throw new Exception("Selecciona el almacén de destino.");
    if ($id_origen === $id_destino) throw new Exception("El almacén de origen y destino no pueden ser el mismo.");

    $fecha_esc = mysqli_real_escape_string($con, $fecha);
    $obs_esc   = mysqli_real_escape_string($con, $observaciones);

    // Verificar que haya items en tmp
    $res_tmp = mysqli_query($con, "SELECT * FROM tmp_traspaso WHERE session_id = '$session_id'");
    if (mysqli_num_rows($res_tmp) == 0) {
        throw new Exception("No hay productos en el traspaso.");
    }

    // Verificar que todos tengan lote y caducidad
    $chk = mysqli_query($con, "
        SELECT COUNT(*) AS faltantes FROM tmp_traspaso
        WHERE session_id = '$session_id'
          AND (lote_tmp = '' OR lote_tmp IS NULL
               OR caducidad_tmp IS NULL OR caducidad_tmp = '0000-00-00')
    ");
    $rw_chk = mysqli_fetch_assoc($chk);
    if ((int)$rw_chk['faltantes'] > 0) {
        throw new Exception("Hay renglones sin lote o caducidad. Verifica los productos.");
    }

    mysqli_begin_transaction($con);

    // Generar folio
    mysqli_query($con, "
        UPDATE consecutivos
        SET valor = LAST_INSERT_ID(valor + 1)
        WHERE nombre = 'folio_traspaso'
    ");
    if (mysqli_affected_rows($con) <= 0) {
        throw new Exception("No existe consecutivo 'folio_traspaso'. Ejecuta el SQL de migración.");
    }
    $res_f = mysqli_query($con, "SELECT LAST_INSERT_ID() AS folio");
    $folio = (int)mysqli_fetch_assoc($res_f)['folio'];
    if ($folio <= 0) throw new Exception("No se pudo generar folio válido.");

    // Insertar encabezado
    mysqli_query($con, "
        INSERT INTO traspasos
            (folio, fecha_traspaso, id_usuario, id_almacen_origen, id_almacen_destino, observaciones)
        VALUES
            ($folio, '$fecha_esc', $id_usuario, $id_origen, $id_destino, '$obs_esc')
    ");
    $id_traspaso = (int)mysqli_insert_id($con);

    // Procesar cada item
    mysqli_data_seek($res_tmp, 0);
    while ($row = mysqli_fetch_assoc($res_tmp)) {
        $referencia   = mysqli_real_escape_string($con, $row['referencia_tmp']);
        $descripcion  = mysqli_real_escape_string($con, $row['descripcion_tmp']);
        $lote         = mysqli_real_escape_string($con, $row['lote_tmp']);
        $caducidad    = mysqli_real_escape_string($con, $row['caducidad_tmp'] ?? '');
        $cantidad     = (float)$row['cantidad_tmp'];
        $id_alma_orig = (int)$row['id_almacen_origen_tmp'];
        $id_alma_dest = (int)$row['id_almacen_destino_tmp'];
        $id_prod_orig = (int)$row['id_producto_origen'];
        $caducidad_sql = ($caducidad !== '' && $caducidad !== '0000-00-00')
            ? "'$caducidad'"
            : "NULL";

        if ($cantidad <= 0) throw new Exception("Cantidad inválida en renglón '$referencia'.");

        // Insertar detalle
        mysqli_query($con, "
            INSERT INTO traspasos_detalle
                (id_traspaso, referencia, descripcion, lote, caducidad, cantidad, id_almacen_origen, id_almacen_destino)
            VALUES
                ($id_traspaso, '$referencia', '$descripcion', '$lote', $caducidad_sql, $cantidad, $id_alma_orig, $id_alma_dest)
        ");

        // Bloquear y verificar origen
        $q_orig = mysqli_query($con, "
            SELECT id_producto, existencias, costo, descripcion
            FROM products
            WHERE id_producto = $id_prod_orig
              AND id_almacen = $id_alma_orig
            LIMIT 1
            FOR UPDATE
        ");
        if (mysqli_num_rows($q_orig) === 0) {
            throw new Exception("Producto origen no encontrado (id=$id_prod_orig, almacén=$id_alma_orig).");
        }
        $prod_orig = mysqli_fetch_assoc($q_orig);
        if ((float)$prod_orig['existencias'] < $cantidad) {
            throw new Exception(
                "Stock insuficiente para '$referencia' lote '$lote'. "
                . "Disponible: " . number_format((float)$prod_orig['existencias'], 2)
                . ", solicitado: " . number_format($cantidad, 2)
            );
        }

        // Descontar del origen
        mysqli_query($con, "
            UPDATE products
            SET existencias = existencias - $cantidad, ultima_modificacion = NOW()
            WHERE id_producto = $id_prod_orig
        ");

        // Buscar producto en destino (misma referencia + lote + almacen destino)
        $q_dest = mysqli_query($con, "
            SELECT id_producto
            FROM products
            WHERE (referencia = '$referencia' OR cve_alterna_1 = '$referencia' OR cve_alterna_2 = '$referencia')
              AND lote = '$lote'
              AND id_almacen = $id_alma_dest
            LIMIT 1
            FOR UPDATE
        ");

        if (mysqli_num_rows($q_dest) > 0) {
            $id_prod_dest = (int)mysqli_fetch_assoc($q_dest)['id_producto'];
            mysqli_query($con, "
                UPDATE products
                SET existencias = existencias + $cantidad, ultima_modificacion = NOW()
                WHERE id_producto = $id_prod_dest
            ");
        } else {
            $costo_orig = (float)$prod_orig['costo'];
            mysqli_query($con, "
                INSERT INTO products
                    (barcode, referencia, descripcion, existencias, lote, caducidad,
                     costo, precio_producto, id_almacen, estatus, ultima_modificacion)
                VALUES
                    ('', '$referencia', '$descripcion', $cantidad, '$lote', $caducidad_sql,
                     $costo_orig, 0, $id_alma_dest, 1, NOW())
            ");
        }
    }

    // Limpiar tmp
    mysqli_query($con, "DELETE FROM tmp_traspaso WHERE session_id = '$session_id'");

    mysqli_commit($con);

    // Notificación (best-effort)
    try {
        $folio_esc = mysqli_real_escape_string($con, (string)$folio);
        $msg_esc   = mysqli_real_escape_string($con, "Traspaso $folio_esc registrado");
        mysqli_query($con, "INSERT INTO notificaciones (tipo, mensaje, id_referencia, folio) VALUES ('traspaso', '$msg_esc', $id_traspaso, '$folio_esc')");
    } catch (Throwable $t) { /* ignorar */ }

    echo "OK|$id_traspaso|$folio";

} catch (Exception $e) {
    try { mysqli_rollback($con); } catch (Throwable $t) { }
    echo "Error: " . $e->getMessage();
}
