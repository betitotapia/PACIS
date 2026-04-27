<?php
// dist/facturacion/ajax/guardar_factura_header.php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  echo json_encode(['ok'=>false,'error'=>'auth']);
  exit;
}

include("../config/db.php");
include("../config/conexion.php");

$id = (int)($_POST['id'] ?? 0);
$id_cliente = (int)($_POST['id_cliente'] ?? 0);
$id_serie = (int)($_POST['id_serie'] ?? 0);
$metodo_pago = mysqli_real_escape_string($con, trim($_POST['metodo_pago'] ?? 'PUE'));
$forma_pago  = mysqli_real_escape_string($con, trim($_POST['forma_pago'] ?? ''));
$uso_cfdi    = mysqli_real_escape_string($con, trim($_POST['uso_cfdi'] ?? 'G03'));

if($id<=0){ echo json_encode(['ok'=>false,'error'=>'id']); exit; }
if($id_cliente<=0){ echo json_encode(['ok'=>false,'error'=>'cliente']); exit; }
if($id_serie<=0){ echo json_encode(['ok'=>false,'error'=>'serie']); exit; }

$qSerie = mysqli_query($con, "SELECT id_serie, serie, activo FROM facturacion_series WHERE id_serie = $id_serie LIMIT 1");
$rwSerie = $qSerie ? mysqli_fetch_assoc($qSerie) : null;
if (!$rwSerie) {
  echo json_encode(['ok'=>false,'error'=>'Serie no encontrada']);
  exit;
}
if ((int)$rwSerie['activo'] !== 1) {
  echo json_encode(['ok'=>false,'error'=>'La serie seleccionada esta inactiva']);
  exit;
}

$serie_nombre = mysqli_real_escape_string($con, (string)$rwSerie['serie']);

$qFact = mysqli_query($con, "SELECT id_serie_facturacion, folio FROM fact_facturas WHERE id_fact_facturas = $id LIMIT 1");
$rwFact = $qFact ? mysqli_fetch_assoc($qFact) : null;
if (!$rwFact) {
  echo json_encode(['ok'=>false,'error'=>'Factura no encontrada']);
  exit;
}

$folioAsignado = 0;
$serieActualFactura = (int)($rwFact['id_serie_facturacion'] ?? 0);
$folioActualFactura = (int)($rwFact['folio'] ?? 0);

if ($serieActualFactura === $id_serie && $folioActualFactura > 0) {
  $folioAsignado = $folioActualFactura;
} else {
  $upSerie = mysqli_query($con, "UPDATE facturacion_series SET folio_actual = LAST_INSERT_ID(folio_actual + 1), updated_at = NOW() WHERE id_serie = $id_serie");
  if (!$upSerie) {
    echo json_encode(['ok'=>false,'error'=>mysqli_error($con)]);
    exit;
  }

  $rsLast = mysqli_query($con, "SELECT LAST_INSERT_ID() AS folio");
  $rwLast = $rsLast ? mysqli_fetch_assoc($rsLast) : null;
  $folioAsignado = (int)($rwLast['folio'] ?? 0);

  // Evita colision con datos historicos cargados manualmente.
  while (true) {
    $qDup = mysqli_query($con, "SELECT id_fact_facturas FROM fact_facturas WHERE serie = '$serie_nombre' AND folio = $folioAsignado AND id_fact_facturas <> $id LIMIT 1");
    if (!$qDup || mysqli_num_rows($qDup) === 0) {
      break;
    }
    mysqli_query($con, "UPDATE facturacion_series SET folio_actual = LAST_INSERT_ID(folio_actual + 1), updated_at = NOW() WHERE id_serie = $id_serie");
    $rsLast = mysqli_query($con, "SELECT LAST_INSERT_ID() AS folio");
    $rwLast = $rsLast ? mysqli_fetch_assoc($rsLast) : null;
    $folioAsignado = (int)($rwLast['folio'] ?? 0);
  }
}

$sql = "UPDATE fact_facturas
    SET id_cliente=$id_cliente,
        id_serie_facturacion=$id_serie,
        serie='$serie_nombre',
        folio=$folioAsignado,
        metodo_pago='$metodo_pago',
        forma_pago='$forma_pago',
        uso_cfdi='$uso_cfdi'
    WHERE id_fact_facturas=$id";

$ok = mysqli_query($con, $sql);
if (!$ok) {
  echo json_encode(['ok'=>false,'error'=>mysqli_error($con)]);
  exit;
}

$msg_notif = mysqli_real_escape_string($con, "Factura {$rwSerie['serie']}-{$folioAsignado} guardada");
$folio_notif = mysqli_real_escape_string($con, $rwSerie['serie'] . '-' . $folioAsignado);
mysqli_query($con, "INSERT INTO notificaciones (tipo, mensaje, id_referencia, folio) VALUES ('factura', '$msg_notif', $id, '$folio_notif')");

echo json_encode(['ok'=>true, 'serie'=>$rwSerie['serie'], 'folio'=>$folioAsignado]);
