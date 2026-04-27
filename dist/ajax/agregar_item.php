<?php
// dist/facturacion/ajax/agregar_item.php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) { header("location: ../login.php"); exit; }

include("../config/db.php");
include("../config/conexion.php");

$id = (int)($_POST['id'] ?? 0);
$id_producto = (int)($_POST['id_producto'] ?? 0);
$id_almacen = (int)($_POST['id_almacen'] ?? 0);
$lote = trim((string)($_POST['lote'] ?? ''));
$caducidad = trim((string)($_POST['caducidad'] ?? ''));
$referencia = trim((string)($_POST['referencia'] ?? ''));
$cantidad = (float)($_POST['cantidad'] ?? 0);
$precio = (float)($_POST['precio'] ?? 0);

if($id<=0 || $id_producto<=0 || $cantidad<=0){ die("Datos inválidos"); }

$qf = mysqli_query($con, "SELECT id_vendedor FROM fact_facturas WHERE id_fact_facturas=$id LIMIT 1");
$rf = mysqli_fetch_assoc($qf);
$id_vendedor = (int)($rf['id_vendedor'] ?? 0);

$qfCliente = mysqli_query($con, "SELECT ff.id_cliente, c.tipo_referencia
  FROM fact_facturas ff
  LEFT JOIN clientes c ON c.id_cliente = ff.id_cliente
  WHERE ff.id_fact_facturas = $id
  LIMIT 1");
$ffCli = $qfCliente ? mysqli_fetch_assoc($qfCliente) : null;
$tipo_referencia = strtoupper(trim((string)($ffCli['tipo_referencia'] ?? 'ORIGINAL')));

$qp = mysqli_query($con, "SELECT referencia, cve_alterna_1, cve_alterna_2, lote, caducidad FROM products WHERE id_producto=$id_producto LIMIT 1");
$rp = mysqli_fetch_assoc($qp);
$cve = (string)($rp['referencia'] ?? '');
if ($tipo_referencia === 'ALTERNA_1' && !empty($rp['cve_alterna_1'])) {
  $cve = (string)$rp['cve_alterna_1'];
} elseif ($tipo_referencia === 'ALTERNA_2' && !empty($rp['cve_alterna_2'])) {
  $cve = (string)$rp['cve_alterna_2'];
}
if ($referencia !== '' && $referencia !== $cve) {
  $cve = $referencia;
}
$lote = $lote !== '' ? $lote : (string)($rp['lote'] ?? '');
$caducidad = $caducidad !== '' ? $caducidad : (string)($rp['caducidad'] ?? '');

mysqli_query($con, "INSERT INTO detalle_fact_factura
  (numero_fact_factura, id_producto, cantidad, precio_venta, id_almacen, id_vendedor, lote, caducidad, cve_producto, tipo_producto, date_created)
  VALUES ($id, $id_producto, $cantidad, $precio, $id_almacen, $id_vendedor,
          '".mysqli_real_escape_string($con,$lote)."',
          '".mysqli_real_escape_string($con,$caducidad)."',
          '".mysqli_real_escape_string($con,$cve)."', 'P', NOW())");

header("Location: ../pages/facturacion/nueva_factura.php?id=".$id);
