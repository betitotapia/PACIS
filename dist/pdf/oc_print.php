<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  header("location: ../../login.php");
  exit;
}

include("../config/db.php");
include("../config/conexion.php");

$id_oc = isset($_GET['id_oc']) ? (int)$_GET['id_oc'] : 0;
if ($id_oc <= 0) { die("OC inválida"); }

// Encabezado
$q = mysqli_query($con, "
  SELECT oc.*,
         p.nombre_provedor AS proveedor,
         a.numero_almacen, a.descripcion AS almacen_desc,
         u.nombre AS usuario
  FROM ordenes_compra oc
  INNER JOIN proveedores p ON oc.id_proveedor = p.id_proveedor
  INNER JOIN almacenes a   ON oc.id_almacen   = a.id_almacen
  INNER JOIN users u       ON oc.id_usuario   = u.user_id
  WHERE oc.id_oc = $id_oc
  LIMIT 1
");
$oc = mysqli_fetch_assoc($q);
if(!$oc){ die('No existe la OC'); }

// Detalle
$det = mysqli_query($con, "SELECT * FROM ordenes_compra_detalle WHERE id_oc = $id_oc");

// HTML
include(dirname(__FILE__).'/res/print_oc_html.php');
?>

<style>
@media print {
  .no-print { display: none !important; }
  body { margin: 0; }
}
</style>

<script>
window.onload = function () {
  window.print();
  window.onafterprint = function () { window.close(); };
};
</script>
