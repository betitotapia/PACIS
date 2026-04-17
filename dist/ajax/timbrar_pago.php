<?php
// dist/facturacion/ajax/timbrar_pago.php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) { echo json_encode(['ok'=>false,'error'=>'auth']); exit; }

include("../../config/db.php");
include("../../config/conexion.php");
require_once("../lib/FacturamaClient.php");
require_once("../lib/cfdi_helpers.php");

$cfg = require(__DIR__."/../config/facturama_config.php");
$api = new FacturamaClient($cfg);

$id_pago = (int)($_POST['id_pago'] ?? 0);
if($id_pago<=0){ echo json_encode(['ok'=>false,'error'=>'id_pago']); exit; }

$qp = mysqli_query($con, "SELECT * FROM fact_pagos WHERE id_pago=$id_pago LIMIT 1");
$pago = mysqli_fetch_assoc($qp);
if(!$pago){ echo json_encode(['ok'=>false,'error'=>'no_pago']); exit; }

$id_fact = (int)$pago['id_fact_facturas'];
$ff = mysqli_query($con, "SELECT ff.*, c.* FROM fact_facturas ff
  INNER JOIN clientes c ON c.id_cliente = ff.id_cliente
  WHERE ff.id_fact_facturas=$id_fact LIMIT 1");
$fact = mysqli_fetch_assoc($ff);
if(!$fact || empty($fact['uuid'])){ echo json_encode(['ok'=>false,'error'=>'factura_no_timbrada']); exit; }

$rfc = trim($fact['rfc'] ?? '');
$nombre = trim($fact['nombre_fiscal'] ?? $fact['nombre_cliente'] ?? '');
$regimen = sat_code_only($fact['regimen_fiscal'] ?? '');
$cp_fiscal = trim($fact['cp_fiscal'] ?? $fact['postal'] ?? '');

if($rfc==='' || $nombre==='' || $regimen==='' || $cp_fiscal===''){
  echo json_encode(['ok'=>false,'error'=>'Faltan datos fiscales del cliente']); exit;
}
$nombre = upper_name_for_sat($nombre);

$cfdi = [
  "NameId" => $cfg['name_id_pago'],
  "CfdiType" => "P",
  "ExpeditionPlace" => $cfg['expedition_place'],
  "Serie" => $cfg['default_serie'],
  "Issuer" => $cfg['issuer'],
  "Receiver" => [
    "Rfc" => $rfc,
    "Name" => $nombre,
    "FiscalRegime" => $regimen,
    "CfdiUse" => "P01",
    "TaxZipCode" => $cp_fiscal
  ],
  "Complemento" => [
    "Payments" => [[
      "Date" => date('c', strtotime($pago['fecha_pago'])),
      "PaymentForm" => sat_code_only($pago['forma_pago']),
      "Currency" => $pago['moneda'] ?? 'MXN',
      "ExchangeRate" => (float)($pago['tipo_cambio'] ?? 1),
      "Amount" => (float)$pago['monto'],
      "RelatedDocuments" => [[
        "Uuid" => $fact['uuid'],
        "Currency" => "MXN",
        "PaymentMethod" => "PPD",
        "PartialityNumber" => (int)$pago['num_parcialidad'],
        "PreviousBalanceAmount" => (float)$pago['saldo_anterior'],
        "AmountPaid" => (float)$pago['monto_pagado'],
        "RemainingBalance" => (float)$pago['saldo_insoluto']
      ]]
    ]]
  ]
];

$res = $api->createCfdi($cfdi);

mysqli_query($con, "INSERT INTO fact_complementos_pago
  (id_pago, request_json, response_json, status_comp, date_created)
  VALUES
  ($id_pago,
   '".mysqli_real_escape_string($con, json_encode($cfdi, JSON_UNESCAPED_UNICODE))."',
   '".mysqli_real_escape_string($con, $res['raw'] ?? '')."',
   ".($res['ok']?1:2).",
   NOW()
  )
");

if(!$res['ok']){
  mysqli_query($con, "UPDATE fact_pagos SET status_pago=2 WHERE id_pago=$id_pago");
  echo json_encode(['ok'=>false,'error'=>$res['error']]); exit;
}

$data = $res['data'] ?? [];
$uuid = $data['Complement']['TaxStamp']['Uuid'] ?? $data['CfdiComplement']['TaxStamp']['Uuid'] ?? $data['Uuid'] ?? null;
$fmId = $data['Id'] ?? null;

mysqli_query($con, "UPDATE fact_pagos SET status_pago=1 WHERE id_pago=$id_pago");

// actualizar complemento recién insertado
$last = mysqli_insert_id($con);
mysqli_query($con, "UPDATE fact_complementos_pago
  SET facturama_id=".($fmId?"'".mysqli_real_escape_string($con,$fmId)."'":"NULL").",
      uuid=".($uuid?"'".mysqli_real_escape_string($con,$uuid)."'":"NULL").",
      fecha_timbrado=NOW()
  WHERE id_comp_pago=$last
");

echo json_encode(['ok'=>true,'uuid'=>$uuid,'facturama_id'=>$fmId]);
