<?php
// dist/facturacion/ajax/agregar_item.php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) { header("location: ../../login.php"); exit; }

include("../../config/db.php");
include("../../config/conexion.php");

$id = (int)($_POST['id'] ?? 0);
$id_producto = (int)($_POST['id_producto'] ?? 0);
$id_almacen = (int)($_POST['id_almacen'] ?? 0);
$cantidad = (float)($_POST['cantidad'] ?? 0);
$precio = (float)($_POST['precio'] ?? 0);

if($id<=0 || $id_producto<=0 || $cantidad<=0){ die("Datos inválidos"); }

$qf = mysqli_query($con, "SELECT id_vendedor FROM fact_facturas WHERE id_fact_facturas=$id LIMIT 1");
$rf = mysqli_fetch_assoc($qf);
$id_vendedor = (int)($rf['id_vendedor'] ?? 0);

$qp = mysqli_query($con, "SELECT referencia FROM products WHERE id_producto=$id_producto LIMIT 1");
$rp = mysqli_fetch_assoc($qp);
$cve = $rp['referencia'] ?? '';

mysqli_query($con, "INSERT INTO detalle_fact_factura
  (numero_fact_factura, id_producto, cantidad, precio_venta, id_almacen, id_vendedor, cve_producto, tipo_producto, date_created)
  VALUES ($id, $id_producto, $cantidad, $precio, $id_almacen, $id_vendedor,
          '".mysqli_real_escape_string($con,$cve)."', 'P', NOW())");

header("Location: ../nueva_factura.php?id=".$id);
