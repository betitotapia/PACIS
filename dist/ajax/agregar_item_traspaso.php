<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    echo "Sesión inválida.";
    exit;
}
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

$referencia         = trim($_POST['referencia'] ?? '');
$lote               = trim($_POST['lote'] ?? '');
$cantidad           = (float)($_POST['cantidad'] ?? 0);
$id_almacen_origen  = (int)($_POST['id_almacen_origen'] ?? 0);
$id_almacen_destino = (int)($_POST['id_almacen_destino'] ?? 0);

if ($referencia === '')            { echo "Captura la referencia del producto."; exit; }
if ($lote === '')                  { echo "Captura el lote del producto."; exit; }
if ($cantidad <= 0)                { echo "La cantidad debe ser mayor a cero."; exit; }
if ($id_almacen_origen <= 0)       { echo "Selecciona el almacén de origen."; exit; }
if ($id_almacen_destino <= 0)      { echo "Selecciona el almacén de destino."; exit; }
if ($id_almacen_origen === $id_almacen_destino) {
    echo "El almacén de origen y destino no pueden ser el mismo.";
    exit;
}

$ref_esc  = mysqli_real_escape_string($con, $referencia);
$lote_esc = mysqli_real_escape_string($con, $lote);

$q_prod = mysqli_query($con, "
    SELECT id_producto, referencia, descripcion, lote, caducidad, existencias, estatus
    FROM products
    WHERE (referencia = '$ref_esc' OR cve_alterna_1 = '$ref_esc' OR cve_alterna_2 = '$ref_esc')
      AND lote = '$lote_esc'
      AND id_almacen = $id_almacen_origen
    LIMIT 1
");

if (mysqli_num_rows($q_prod) === 0) {
    echo "Producto no encontrado en el almacén de origen con el lote indicado.";
    exit;
}

$prod = mysqli_fetch_assoc($q_prod);

if ((float)$prod['existencias'] < $cantidad) {
    echo "Stock insuficiente. Existencia disponible en origen: " . number_format((float)$prod['existencias'], 2);
    exit;
}

$desc_esc     = mysqli_real_escape_string($con, $prod['descripcion']);
$ref_real_esc = mysqli_real_escape_string($con, $prod['referencia']);
$caducidad_sql = (!empty($prod['caducidad']) && $prod['caducidad'] !== '0000-00-00')
    ? "'" . mysqli_real_escape_string($con, $prod['caducidad']) . "'"
    : "NULL";
$id_producto = (int)$prod['id_producto'];

mysqli_query($con, "
    INSERT INTO tmp_traspaso
        (session_id, id_producto_origen, referencia_tmp, descripcion_tmp, lote_tmp, caducidad_tmp,
         cantidad_tmp, id_almacen_origen_tmp, id_almacen_destino_tmp)
    VALUES
        ('$session_id', $id_producto, '$ref_real_esc', '$desc_esc', '$lote_esc', $caducidad_sql,
         $cantidad, $id_almacen_origen, $id_almacen_destino)
");

echo "OK";
