<?php
// dist/facturacion/ajax/guardar_pago.php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) { echo json_encode(['ok'=>false,'error'=>'auth']); exit; }
include("../../config/db.php");
include("../../config/conexion.php");

$id_fact = (int)($_POST['id_fact'] ?? 0);
$fecha = trim($_POST['fecha'] ?? '');
$forma = trim($_POST['forma'] ?? '03');
$monto = (float)($_POST['monto'] ?? 0);
$parc = (int)($_POST['parcialidad'] ?? 1);

if($id_fact<=0 || $fecha==='' || $monto<=0){ echo json_encode(['ok'=>false,'error'=>'datos']); exit; }

$ff = mysqli_query($con, "SELECT total, uuid FROM fact_facturas WHERE id_fact_facturas=$id_fact LIMIT 1");
$fact = mysqli_fetch_assoc($ff);
if(!$fact || empty($fact['uuid'])){ echo json_encode(['ok'=>false,'error'=>'factura_no_timbrada']); exit; }

$total_fact = (float)$fact['total'];
$saldo_ant = $total_fact; // versión mínima: asume 1er pago. Luego puedes calcular saldo real con SUM(pagos).
$qsum = mysqli_query($con, "SELECT COALESCE(SUM(monto),0) AS pagado FROM fact_pagos WHERE id_fact_facturas=$id_fact");
$sum = mysqli_fetch_assoc($qsum);
$pagado_prev = (float)$sum['pagado'];
$saldo_ant = max(0, $total_fact - $pagado_prev);

$saldo_ins = max(0, $saldo_ant - $monto);

mysqli_query($con, "INSERT INTO fact_pagos
  (id_fact_facturas, fecha_pago, forma_pago, moneda, tipo_cambio, monto, num_parcialidad, saldo_anterior, monto_pagado, saldo_insoluto, status_pago, date_created)
  VALUES
  ($id_fact, '".mysqli_real_escape_string($con, str_replace('T',' ', $fecha))."', '".mysqli_real_escape_string($con, $forma)."', 'MXN', 1, $monto, $parc, $saldo_ant, $monto, $saldo_ins, 0, NOW())
");

echo json_encode(['ok'=>true]);
