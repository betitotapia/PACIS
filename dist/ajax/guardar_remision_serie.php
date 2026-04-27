<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  echo json_encode(['ok'=>false,'error'=>'auth']);
  exit;
}

include('../config/db.php');
include('../config/conexion.php');

$numero_factura = (int)($_POST['numero_factura'] ?? 0);
$id_vendedor    = (int)($_POST['id_vendedor'] ?? 0);
$id_serie       = (int)($_POST['id_serie'] ?? 0);

if ($numero_factura <= 0) { echo json_encode(['ok'=>false,'error'=>'remision']); exit; }
if ($id_serie <= 0)       { echo json_encode(['ok'=>false,'error'=>'serie']);    exit; }

$qSerie = mysqli_query($con, "SELECT id_serie, serie, activo FROM remision_series WHERE id_serie = $id_serie LIMIT 1");
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

$qFact = mysqli_query($con, "SELECT id_serie_remision, folio_remision FROM facturas WHERE numero_factura = $numero_factura LIMIT 1");
$rwFact = $qFact ? mysqli_fetch_assoc($qFact) : null;
if (!$rwFact) {
  echo json_encode(['ok'=>false,'error'=>'Remision no encontrada']);
  exit;
}

$folioAsignado = 0;
$serieActual   = (int)($rwFact['id_serie_remision'] ?? 0);
$folioActual   = (int)($rwFact['folio_remision'] ?? 0);

// Si ya tiene folio asignado con la misma serie, reutilizarlo.
if ($serieActual === $id_serie && $folioActual > 0) {
  $folioAsignado = $folioActual;
} else {
  // Incrementar folio_actual en la serie usando LAST_INSERT_ID para atomicidad.
  $upSerie = mysqli_query($con, "UPDATE remision_series SET folio_actual = LAST_INSERT_ID(folio_actual + 1), updated_at = NOW() WHERE id_serie = $id_serie");
  if (!$upSerie) {
    echo json_encode(['ok'=>false,'error'=>mysqli_error($con)]);
    exit;
  }

  $rsLast = mysqli_query($con, "SELECT LAST_INSERT_ID() AS folio");
  $rwLast = $rsLast ? mysqli_fetch_assoc($rsLast) : null;
  $folioAsignado = (int)($rwLast['folio'] ?? 0);

  // Detectar colision con folios historicos ingresados manualmente.
  while (true) {
    $qDup = mysqli_query($con, "SELECT numero_factura FROM facturas WHERE serie_remision = '$serie_nombre' AND folio_remision = $folioAsignado AND numero_factura <> $numero_factura LIMIT 1");
    if (!$qDup || mysqli_num_rows($qDup) === 0) {
      break;
    }
    mysqli_query($con, "UPDATE remision_series SET folio_actual = LAST_INSERT_ID(folio_actual + 1), updated_at = NOW() WHERE id_serie = $id_serie");
    $rsLast = mysqli_query($con, "SELECT LAST_INSERT_ID() AS folio");
    $rwLast = $rsLast ? mysqli_fetch_assoc($rsLast) : null;
    $folioAsignado = (int)($rwLast['folio'] ?? 0);
  }
}

$ok = mysqli_query($con, "UPDATE facturas
  SET id_serie_remision = $id_serie,
      serie_remision = '$serie_nombre',
      folio_remision = $folioAsignado
  WHERE numero_factura = $numero_factura");

if (!$ok) {
  echo json_encode(['ok'=>false,'error'=>mysqli_error($con)]);
  exit;
}

$msg_notif = mysqli_real_escape_string($con, "Remisión {$rwSerie['serie']}-{$folioAsignado} finalizada");
$folio_notif = mysqli_real_escape_string($con, $rwSerie['serie'] . '-' . $folioAsignado);
mysqli_query($con, "INSERT INTO notificaciones (tipo, mensaje, id_referencia, folio) VALUES ('remision', '$msg_notif', $numero_factura, '$folio_notif')");

echo json_encode(['ok'=>true, 'serie'=>$rwSerie['serie'], 'folio'=>$folioAsignado]);
