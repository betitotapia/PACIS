<?php
// dist/facturacion/ajax/eliminar_item.php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  echo json_encode(['ok'=>false,'error'=>'auth']); exit;
}
include("../../config/db.php");
include("../../config/conexion.php");
$id_detalle = (int)($_POST['id_detalle'] ?? 0);
if($id_detalle<=0){ echo json_encode(['ok'=>false,'error'=>'id_detalle']); exit; }
mysqli_query($con, "DELETE FROM detalle_fact_factura WHERE id_detalle_fact=$id_detalle");
echo json_encode(['ok'=>true]);
