<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();

if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  header("location: ../../login.php");
  exit;
}

ini_set('display_errors', 1);

include("../config/db.php");
include("../config/conexion.php");
include("../pages/funciones.php");

$numero_factura = isset($_GET['numero_factura']) ? (int)$_GET['numero_factura'] : 0;
$id_vendedor    = isset($_GET['id_vendedor']) ? (int)$_GET['id_vendedor'] : 0;

if ($numero_factura <= 0 || $id_vendedor <= 0) {
  die("Remisión inválida");
}

// Validar que haya detalle
$sql_count = mysqli_query($con, "
  SELECT 1
  FROM detalle_factura
  WHERE numero_factura = '".$numero_factura."'
    AND id_vendedor = '".$id_vendedor."'
  LIMIT 1
");
if (mysqli_num_rows($sql_count) == 0) {
  echo "<script>alert('No hay productos agregados a la factura')</script>";
  echo "<script>window.close();</script>";
  exit;
}

// Cargar encabezado desde facturas (como ver_remision.php)
$sql_facturas = mysqli_query($con, "
  SELECT *
  FROM facturas
  WHERE numero_factura = '".$numero_factura."'
    AND id_vendedor = '".$id_vendedor."'
  LIMIT 1
");
$rw_factura = mysqli_fetch_array($sql_facturas);
if(!$rw_factura){ die("No existe la remisión"); }

$id_cliente    = (int)$rw_factura['id_cliente'];
$fecha         = date("d/m/Y", strtotime($rw_factura['fecha_factura']));
$compra        = $rw_factura['compra'];
$cotizacion    = $rw_factura['cotizacion'];
$doctor        = $rw_factura['doctor'];
$paciente      = $rw_factura['paciente'];
$material      = $rw_factura['material'];
$pago          = $rw_factura['pago'];
$d_factura     = $rw_factura['d_factura'];
$observaciones = $rw_factura['observaciones'];
$hospital      = $rw_factura['hospital'];
$proveedor     = $rw_factura['no_proveedor'];

// Vendedor
$sql_user = mysqli_query($con, "SELECT nombre, letra FROM users WHERE user_id = '".$id_vendedor."' LIMIT 1");
$rw_usuario = mysqli_fetch_array($sql_user);
$nombre_vendedor = $rw_usuario['nombre'];
$letra_ventas    = $rw_usuario['letra'];

// Render con la MISMA plantilla "estilo OC"
include(dirname(__FILE__).'/res/print_remision_oc_html.php');