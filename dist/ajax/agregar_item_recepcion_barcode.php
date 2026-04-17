<?php
session_start();
$session_id = session_id();

require_once("../config/db.php");
require_once("../config/conexion.php");

$barcode    = isset($_POST['barcode'])    ? trim($_POST['barcode'])    : '';
$cantidad   = isset($_POST['cantidad'])   ? (int)$_POST['cantidad']    : 0;
$referencia = isset($_POST['referencia']) ? trim($_POST['referencia']) : '';
$id_almacen = isset($_POST['id_almacen']) ? (int)$_POST['id_almacen']  : 0;

if ($barcode === '' || $cantidad <= 0 || $id_almacen <= 0) {
    echo "Datos incompletos (código, cantidad o almacén).";
    exit;
}

// Buscar producto por código de barras
$barcode_esc = mysqli_real_escape_string($con, $barcode);

$sql_prod = mysqli_query(
    $con,
    "SELECT id_producto, referencia, descripcion, lote, caducidad, costo
     FROM products
     WHERE barcode = '$barcode_esc'
     LIMIT 1"
);

if (!$sql_prod || mysqli_num_rows($sql_prod) == 0) {
    echo "Producto no encontrado para ese código de barras.";
    exit;
}

$row = mysqli_fetch_assoc($sql_prod);

$ref_db = $row['referencia'];
$descr  = $row['descripcion'];
$lote   = $row['lote'];
$cad    = $row['caducidad'];
$costo  = (float)$row['costo'];

// Si el usuario capturó referencia manual, usarla.
// Si no, usamos la referencia del producto.
$ref_final = ($referencia !== '') ? $referencia : $ref_db;

// Escapar para evitar problemas de SQL
$ref_final = mysqli_real_escape_string($con, $ref_final);
$descr     = mysqli_real_escape_string($con, $descr);
$lote      = mysqli_real_escape_string($con, $lote);
$cad       = mysqli_real_escape_string($con, $cad);

// Insertar en la tabla temporal de recepción
$sql_ins = "INSERT INTO tmp_recepcion 
    (referencia_tmp, descripcion_tmp, lote_tmp, caducidad_tmp, cantidad_tmp, costo_tmp, id_almacen_tmp, session_id)
    VALUES
    ('$ref_final', '$descr', '$lote', '$cad', $cantidad, $costo, $id_almacen, '$session_id')";

if (mysqli_query($con, $sql_ins)) {
    echo "OK";
} else {
    echo "Error al agregar: " . mysqli_error($con);
}
