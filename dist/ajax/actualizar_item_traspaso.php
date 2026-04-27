<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

$id_tmp   = (int)($_POST['id_tmp'] ?? 0);
$cantidad = (float)($_POST['cantidad'] ?? 0);

if ($id_tmp <= 0)   { echo "ID inválido."; exit; }
if ($cantidad <= 0) { echo "La cantidad debe ser mayor a cero."; exit; }

$q = mysqli_query($con, "
    SELECT id_producto_origen, id_almacen_origen_tmp
    FROM tmp_traspaso
    WHERE id_tmp = $id_tmp AND session_id = '$session_id'
    LIMIT 1
");
if (mysqli_num_rows($q) === 0) { echo "Renglón no encontrado."; exit; }

$tmp      = mysqli_fetch_assoc($q);
$id_prod  = (int)$tmp['id_producto_origen'];
$id_alma  = (int)$tmp['id_almacen_origen_tmp'];

$q_ex = mysqli_query($con, "SELECT existencias FROM products WHERE id_producto = $id_prod AND id_almacen = $id_alma LIMIT 1");
if ($q_ex && ($rw_ex = mysqli_fetch_assoc($q_ex))) {
    if ((float)$rw_ex['existencias'] < $cantidad) {
        echo "Stock insuficiente. Existencia disponible: " . number_format((float)$rw_ex['existencias'], 2);
        exit;
    }
}

mysqli_query($con, "UPDATE tmp_traspaso SET cantidad_tmp = $cantidad WHERE id_tmp = $id_tmp AND session_id = '$session_id'");
echo "OK";
