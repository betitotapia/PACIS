<?php
// dist/facturacion/ajax/guardar_factura_header.php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  echo json_encode(['ok'=>false,'error'=>'auth']);
  exit;
}

include("../../config/db.php");
include("../../config/conexion.php");

$id = (int)($_POST['id'] ?? 0);
$id_cliente = (int)($_POST['id_cliente'] ?? 0);
$metodo_pago = mysqli_real_escape_string($con, trim($_POST['metodo_pago'] ?? 'PUE'));
$forma_pago  = mysqli_real_escape_string($con, trim($_POST['forma_pago'] ?? ''));
$uso_cfdi    = mysqli_real_escape_string($con, trim($_POST['uso_cfdi'] ?? 'G03'));
$serie       = mysqli_real_escape_string($con, trim($_POST['serie'] ?? 'A'));
$folio       = mysqli_real_escape_string($con, trim($_POST['folio'] ?? ''));

if($id<=0){ echo json_encode(['ok'=>false,'error'=>'id']); exit; }
if($id_cliente<=0){ echo json_encode(['ok'=>false,'error'=>'cliente']); exit; }

mysqli_query($con, "UPDATE fact_facturas
  SET id_cliente=$id_cliente,
      serie='$serie',
      folio=".($folio!==''?"'$folio'":"NULL").",
      request_json=JSON_SET(COALESCE(request_json,'{}'),
        '$.PaymentMethod', '$metodo_pago',
        '$.PaymentForm', '$forma_pago',
        '$.CfdiUse', '$uso_cfdi'
      )
  WHERE id_fact_facturas=$id
");

echo json_encode(['ok'=>true]);
