<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    echo "Sesion invalida";
    exit;
}

require_once("../config/db.php");
require_once("../config/conexion.php");

$id_usuario = (int)($_SESSION['user_id'] ?? 0);

$barcode = trim((string)($_POST['barcode'] ?? ''));
$referencia = trim((string)($_POST['referencia'] ?? ''));
$cve_alterna_1 = trim((string)($_POST['cve_alterna_1'] ?? ''));
$cve_alterna_2 = trim((string)($_POST['cve_alterna_2'] ?? ''));
$descripcion = trim((string)($_POST['descripcion'] ?? ''));
$lote = trim((string)($_POST['lote'] ?? ''));
$caducidad = trim((string)($_POST['caducidad'] ?? ''));
$existencias = (int)($_POST['existencias'] ?? 0);
$costo = (float)($_POST['costo'] ?? 0);
$precio_producto = (float)($_POST['precio_producto'] ?? 0);
$id_almacen = (int)($_POST['id_almacen'] ?? 0);
$exento_iva = isset($_POST['exento_iva']) ? 1 : 0;

if ($referencia === '') {
    echo "Captura la referencia.";
    exit;
}
if ($descripcion === '') {
    echo "Captura la descripcion.";
    exit;
}
if ($lote === '') {
    echo "Captura el lote.";
    exit;
}
if ($caducidad === '') {
    echo "Captura la caducidad.";
    exit;
}
if ($id_almacen <= 0) {
    echo "Almacen invalido.";
    exit;
}
if ($existencias <= 0) {
    echo "Las existencias deben ser mayores a cero.";
    exit;
}

$barcode_sql = mysqli_real_escape_string($con, $barcode);
$referencia_sql = mysqli_real_escape_string($con, $referencia);
$cve_alterna_1_sql = mysqli_real_escape_string($con, $cve_alterna_1);
$cve_alterna_2_sql = mysqli_real_escape_string($con, $cve_alterna_2);
$descripcion_sql = mysqli_real_escape_string($con, $descripcion);
$lote_sql = mysqli_real_escape_string($con, $lote);
$caducidad_sql = mysqli_real_escape_string($con, $caducidad);

$q = mysqli_query($con, "SELECT id_producto, existencias
    FROM products
    WHERE referencia = '$referencia_sql'
      AND lote = '$lote_sql'
      AND id_almacen = $id_almacen
    LIMIT 1");

if ($q && mysqli_num_rows($q) > 0) {
    $rw = mysqli_fetch_assoc($q);
    $id_producto = (int)$rw['id_producto'];

    $sql_upd = "UPDATE products SET
        barcode = '$barcode_sql',
        cve_alterna_1 = NULLIF('$cve_alterna_1_sql',''),
        cve_alterna_2 = NULLIF('$cve_alterna_2_sql',''),
        descripcion = '$descripcion_sql',
        existencias = existencias + $existencias,
        caducidad = '$caducidad_sql',
        costo = $costo,
        precio_producto = $precio_producto,
        exento_iva = $exento_iva,
        ultima_modificacion = NOW()
      WHERE id_producto = $id_producto";

    if (!mysqli_query($con, $sql_upd)) {
        echo "Error al actualizar producto: " . mysqli_error($con);
        exit;
    }

    mysqli_query($con, "INSERT INTO movimientos (id_producto, cantidad, tipo_movimiento, id_usuario, date_created)
      VALUES ($id_producto, $existencias, 1, $id_usuario, NOW())");

    echo "OK|actualizado";
    exit;
}

$sql_ins = "INSERT INTO products
    (barcode, referencia, cve_alterna_1, cve_alterna_2, descripcion, existencias, lote, caducidad, costo, precio_producto, exento_iva, id_almacen, estatus, ultima_modificacion)
    VALUES
    ('$barcode_sql', '$referencia_sql', NULLIF('$cve_alterna_1_sql',''), NULLIF('$cve_alterna_2_sql',''), '$descripcion_sql', $existencias, '$lote_sql', '$caducidad_sql', $costo, $precio_producto, $exento_iva, $id_almacen, 1, NOW())";

if (!mysqli_query($con, $sql_ins)) {
    echo "Error al insertar producto: " . mysqli_error($con);
    exit;
}

$id_producto = (int)mysqli_insert_id($con);
mysqli_query($con, "INSERT INTO movimientos (id_producto, cantidad, tipo_movimiento, id_usuario, date_created)
  VALUES ($id_producto, $existencias, 1, $id_usuario, NOW())");

echo "OK|insertado";
