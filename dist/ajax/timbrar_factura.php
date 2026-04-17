<?php
// dist/facturacion/ajax/timbrar_factura.php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  echo json_encode(['ok'=>false,'error'=>'auth']); exit;
}

include("../../config/db.php");
include("../../config/conexion.php");
include("../config/../config/../pages/funciones.php"); // fallback
include("../config/facturama_config.php");
require_once("../lib/FacturamaClient.php");
require_once("../lib/cfdi_helpers.php");

// Nota: paths pueden variar según tu estructura; si falla, ajusta includes a tu repo.

$cfg = require(__DIR__."/../config/facturama_config.php");
$api = new FacturamaClient($cfg);

$id = (int)($_POST['id'] ?? 0);
if($id<=0){ echo json_encode(['ok'=>false,'error'=>'id']); exit; }

$ff = mysqli_query($con, "SELECT * FROM fact_facturas WHERE id_fact_facturas=$id LIMIT 1");
$fact = mysqli_fetch_assoc($ff);
if(!$fact){ echo json_encode(['ok'=>false,'error'=>'no_factura']); exit; }

if((int)$fact['status_factura'] === 2 && !empty($fact['uuid'])){
  echo json_encode(['ok'=>true,'uuid'=>$fact['uuid'], 'already'=>true]); exit;
}

$id_cliente = (int)$fact['id_cliente'];
if($id_cliente<=0){ echo json_encode(['ok'=>false,'error'=>'cliente']); exit; }

$qc = mysqli_query($con, "SELECT * FROM clientes WHERE id_cliente=$id_cliente LIMIT 1");
$cli = mysqli_fetch_assoc($qc);
if(!$cli){ echo json_encode(['ok'=>false,'error'=>'cliente_no']); exit; }

$rfc = trim($cli['rfc'] ?? '');
$nombre = trim($cli['nombre_fiscal'] ?? $cli['nombre_cliente'] ?? '');
$uso_cfdi = sat_code_only($cli['uso_cfdi'] ?? 'G03');
$metodo_pago = sat_code_only($cli['metodo_pago'] ?? 'PUE');
$forma_pago  = sat_code_only($cli['forma_pago'] ?? '03');

$metodo_pago_override = null;
$forma_pago_override = null;
$uso_cfdi_override = null;

// Si guardaste overrides en request_json, úsalo
if(!empty($fact['request_json'])){
  $rj = json_decode($fact['request_json'], true);
  if(is_array($rj)){
    $metodo_pago_override = sat_code_only($rj['PaymentMethod'] ?? null);
    $forma_pago_override  = sat_code_only($rj['PaymentForm'] ?? null);
    $uso_cfdi_override    = sat_code_only($rj['CfdiUse'] ?? null);
  }
}

$metodo_pago = $metodo_pago_override ?: $metodo_pago;
$forma_pago  = $forma_pago_override ?: $forma_pago;
$uso_cfdi    = $uso_cfdi_override ?: $uso_cfdi;

// CFDI 4.0 exige régimen y CP fiscal del receptor
$regimen_receptor = sat_code_only($cli['regimen_fiscal'] ?? '');
$cp_fiscal = trim($cli['cp_fiscal'] ?? $cli['postal'] ?? '');

if($rfc==='' || $nombre==='' || $regimen_receptor==='' || $cp_fiscal===''){
  echo json_encode(['ok'=>false,'error'=>'Faltan datos fiscales del cliente (RFC/Nombre/Régimen/CP fiscal).']); exit;
}

$nombre = upper_name_for_sat($nombre);

// Items
$itemsQ = mysqli_query($con, "SELECT d.*, p.referencia, p.descripcion, p.exento_iva, p.sat_product_code, p.sat_unit_code, p.sat_unit, p.is_service
  FROM detalle_fact_factura d
  INNER JOIN products p ON p.id_producto = d.id_producto
  WHERE d.numero_fact_factura=$id
  ORDER BY d.id_detalle_fact ASC
");
$items = [];
$subtotal = 0;
$iva_total = 0;

while($it = mysqli_fetch_assoc($itemsQ)){
  $qty = (float)$it['cantidad'];
  $price = (float)$it['precio_venta'];
  $sub = money2($qty*$price);
  $subtotal += $sub;

  $prodCode = trim($it['sat_product_code'] ?? '');
  $unitCode = trim($it['sat_unit_code'] ?? '');
  $unitName = trim($it['sat_unit'] ?? 'PIEZA');

  if($prodCode==='' || $unitCode===''){
    echo json_encode(['ok'=>false,'error'=>'Faltan claves SAT en products (sat_product_code / sat_unit_code) para: '.$it['referencia']]); exit;
  }

  $taxes = [];
  $totalLine = $sub;

  $exento = (int)($it['exento_iva'] ?? 0) === 1;

  if(!$exento){
    $iva = money2($sub * 0.16);
    $iva_total += $iva;
    $totalLine = money2($sub + $iva);
    $taxes[] = [
      "Total" => $iva,
      "Name" => "IVA",
      "Base" => $sub,
      "Rate" => 0.16,
      "IsRetention" => false
    ];
  }

  $items[] = [
    "ProductCode" => $prodCode,
    "IdentificationNumber" => $it['referencia'],
    "Description" => $it['descripcion'],
    "Unit" => $unitName,
    "UnitCode" => $unitCode,
    "UnitPrice" => money2($price),
    "Quantity" => money2($qty),
    "Subtotal" => $sub,
    "Taxes" => $taxes,
    "Total" => $totalLine
  ];
}

if(count($items)===0){
  echo json_encode(['ok'=>false,'error'=>'Sin conceptos']); exit;
}

$total = money2($subtotal + $iva_total);

$cfdi = [
  "Serie" => $fact['serie'] ?? $cfg['default_serie'],
  "Folio" => $fact['folio'] ?? null,
  "ExpeditionPlace" => $cfg['expedition_place'],
  "CfdiType" => "I",
  "NameId" => $cfg['name_id_factura'],
  "PaymentForm" => ($metodo_pago === 'PPD') ? null : $forma_pago,
  "PaymentMethod" => $metodo_pago, // PUE o PPD
  "Exportation" => $fact['exportacion'] ?? '01',
  "Issuer" => $cfg['issuer'],
  "Receiver" => [
    "Rfc" => $rfc,
    "Name" => $nombre,
    "FiscalRegime" => $regimen_receptor,
    "CfdiUse" => $uso_cfdi,
    "TaxZipCode" => $cp_fiscal
  ],
  "Items" => $items
];

$res = $api->createCfdi($cfdi);

mysqli_query($con, "UPDATE fact_facturas
  SET request_json='".mysqli_real_escape_string($con, json_encode($cfdi, JSON_UNESCAPED_UNICODE))."',
      response_json='".mysqli_real_escape_string($con, $res['raw'] ?? '')."',
      subtotal=$subtotal,
      iva=$iva_total,
      total=$total
  WHERE id_fact_facturas=$id
");

if(!$res['ok']){
  mysqli_query($con, "UPDATE fact_facturas SET status_factura=3 WHERE id_fact_facturas=$id");
  echo json_encode(['ok'=>false,'error'=>$res['error']]); exit;
}

// Facturama responde modelo con datos; buscamos UUID / Id
$data = $res['data'] ?? [];
$uuid = $data['Complement']['TaxStamp']['Uuid'] ?? $data['CfdiComplement']['TaxStamp']['Uuid'] ?? $data['Uuid'] ?? null;
$fmId = $data['Id'] ?? null;

mysqli_query($con, "UPDATE fact_facturas
  SET status_factura=2,
      facturama_id=".($fmId?"'".mysqli_real_escape_string($con,$fmId)."'":"NULL").",
      uuid=".($uuid?"'".mysqli_real_escape_string($con,$uuid)."'":"NULL").",
      fecha_timbrado=NOW()
  WHERE id_fact_facturas=$id
");

echo json_encode(['ok'=>true,'uuid'=>$uuid, 'facturama_id'=>$fmId]);
