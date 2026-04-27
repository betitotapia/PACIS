<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  echo json_encode(['ok'=>false,'error'=>'auth']);
  exit;
}

include('../config/db.php');
include('../config/conexion.php');

$id_serie     = (int)($_GET['id_serie'] ?? 0);
$numero_factura = (int)($_GET['numero_factura'] ?? 0);

if ($id_serie <= 0) {
  echo json_encode(['ok'=>false,'error'=>'serie']);
  exit;
}

$qSerie = mysqli_query($con, "SELECT id_serie, serie, folio_actual, activo FROM remision_series WHERE id_serie = $id_serie LIMIT 1");
$serie  = $qSerie ? mysqli_fetch_assoc($qSerie) : null;
if (!$serie) {
  echo json_encode(['ok'=>false,'error'=>'Serie no encontrada']);
  exit;
}
if ((int)$serie['activo'] !== 1) {
  echo json_encode(['ok'=>false,'error'=>'La serie esta inactiva']);
  exit;
}

// Si esta remision ya tiene folio asignado con esta misma serie, devuelve el persistido.
if ($numero_factura > 0) {
  $qFact = mysqli_query($con, "SELECT id_serie_remision, folio_remision FROM facturas WHERE numero_factura = $numero_factura LIMIT 1");
  $fact  = $qFact ? mysqli_fetch_assoc($qFact) : null;
  if ($fact && (int)$fact['id_serie_remision'] === $id_serie && (int)$fact['folio_remision'] > 0) {
    echo json_encode(['ok'=>true, 'serie'=>$serie['serie'], 'folio'=>(int)$fact['folio_remision'], 'persistido'=>true]);
    exit;
  }
}

$serie_nombre = mysqli_real_escape_string($con, (string)$serie['serie']);
$qMax    = mysqli_query($con, "SELECT COALESCE(MAX(folio_remision),0) AS max_folio FROM facturas WHERE serie_remision = '$serie_nombre'");
$rwMax   = $qMax ? mysqli_fetch_assoc($qMax) : ['max_folio' => 0];
$maxFact = (int)($rwMax['max_folio'] ?? 0);
$preview = max((int)$serie['folio_actual'], $maxFact) + 1;

echo json_encode(['ok'=>true, 'serie'=>$serie['serie'], 'folio'=>$preview, 'persistido'=>false]);
