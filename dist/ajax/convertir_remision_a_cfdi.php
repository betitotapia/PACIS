<?php
// pacis/dist/ajax/convertir_remision_a_cfdi.php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  echo json_encode(['ok' => false, 'error' => 'auth']);
  exit;
}

include("../config/db.php");
include("../config/conexion.php");

function copiar_detalle_remision_a_factura(mysqli $con, int $id_remision, int $id_fact, int $id_vendedor): array {
  $qd = mysqli_query($con, "SELECT *
    FROM detalle_factura
    WHERE numero_factura = $id_remision");

  if (!$qd) {
    return ['ok' => false, 'error' => 'Error al leer detalle de remision: '.mysqli_error($con)];
  }

  $copiados = 0;

  while ($d = mysqli_fetch_assoc($qd)) {
    $id_producto = (int)$d['id_producto'];
    $cantidad = (float)$d['cantidad'];
    $precio = (float)$d['precio_venta'];
    $id_almacen = (int)($d['id_almacen'] ?? ($d['almacen'] ?? 0));
    $id_vend_det = (int)($d['id_vendedor'] ?? $id_vendedor);

    $qp = mysqli_query($con, "SELECT referencia, lote, caducidad FROM products WHERE id_producto = $id_producto LIMIT 1");
    $pr = $qp ? mysqli_fetch_assoc($qp) : null;
    $cve = mysqli_real_escape_string($con, (string)($d['referencia'] ?? ($pr['referencia'] ?? '')));
    $lote = mysqli_real_escape_string($con, (string)($pr['lote'] ?? ''));
    $caducidad = mysqli_real_escape_string($con, (string)($pr['caducidad'] ?? ''));

    $sqlDet = "INSERT INTO detalle_fact_factura
      (numero_fact_factura, id_producto, cantidad, precio_venta, id_almacen, id_vendedor, lote, caducidad, cve_producto, tipo_producto, date_created)
      VALUES
      ($id_fact, $id_producto, $cantidad, $precio, $id_almacen, $id_vend_det, '$lote', '$caducidad', '$cve', 'P', NOW())";

    if (mysqli_query($con, $sqlDet)) {
      $copiados++;
    }
  }

  return ['ok' => true, 'copiados' => $copiados];
}

$id_remision = (int)($_POST['id_remision'] ?? 0);
if ($id_remision <= 0) {
  echo json_encode(['ok' => false, 'error' => 'id_remision inválido']);
  exit;
}

// 1) Validar que exista remisión (tu remisión vive en facturas con numero_factura)
$qr = mysqli_query($con, "SELECT * FROM facturas WHERE numero_factura = $id_remision LIMIT 1");
$rem = mysqli_fetch_assoc($qr);

if (!$rem) {
  echo json_encode(['ok' => false, 'error' => 'Remisión no encontrada']);
  exit;
}

$id_cliente = (int)($rem['id_cliente'] ?? 0);
$id_vendedor = (int)($rem['id_vendedor'] ?? ($_SESSION['user_id'] ?? 0));

if ($id_cliente <= 0) {
  echo json_encode(['ok' => false, 'error' => 'La remisión no tiene cliente']);
  exit;
}

// 2) Evitar duplicados: si ya existe factura (borrador o timbrada) ligada a esa remisión, reusarla
$qe = mysqli_query($con, "SELECT id_fact_facturas
  FROM fact_facturas
  WHERE id_remision = $id_remision
  ORDER BY id_fact_facturas DESC
  LIMIT 1");

if ($ex = mysqli_fetch_assoc($qe)) {
  $id_fact = (int)$ex['id_fact_facturas'];

  // Asegura que el encabezado quede alineado con la remisión origen.
  mysqli_query($con, "UPDATE fact_facturas
    SET id_cliente = $id_cliente,
        id_vendedor = $id_vendedor
    WHERE id_fact_facturas = $id_fact");

  $qc = mysqli_query($con, "SELECT COUNT(*) AS total
    FROM detalle_fact_factura
    WHERE numero_fact_factura = $id_fact");
  $rc = $qc ? mysqli_fetch_assoc($qc) : ['total' => 0];

  if ((int)($rc['total'] ?? 0) <= 0) {
    $copiado = copiar_detalle_remision_a_factura($con, $id_remision, $id_fact, $id_vendedor);
    if (!$copiado['ok']) {
      echo json_encode(['ok' => false, 'error' => $copiado['error']]);
      exit;
    }
    if ((int)($copiado['copiados'] ?? 0) <= 0) {
      echo json_encode(['ok' => false, 'error' => 'La remision no tiene partidas para facturar']);
      exit;
    }
    echo json_encode(['ok' => true, 'id' => $id_fact, 'reused' => true, 'copiados' => (int)$copiado['copiados']]);
    exit;
  }

  echo json_encode(['ok' => true, 'id' => $id_fact, 'reused' => true]);
  exit;
}

// 3) Crear encabezado de factura borrador en fact_facturas
// Nota: aquí guardamos total_factura=0 y status_factura=0 (borrador)
$sqlIns = "INSERT INTO fact_facturas
  (id_remision, no_fact_factura, id_cliente, id_vendedor, total_factura, status_factura, validacion, date_created)
  VALUES
  ($id_remision, 0, $id_cliente, $id_vendedor, 0, 0, 0, NOW())";

if (!mysqli_query($con, $sqlIns)) {
  echo json_encode(['ok' => false, 'error' => 'No se pudo crear factura: '.mysqli_error($con)]);
  exit;
}

$id_fact = (int)mysqli_insert_id($con);

// 4) Copiar detalle de remisión -> detalle_fact_factura
$copiado = copiar_detalle_remision_a_factura($con, $id_remision, $id_fact, $id_vendedor);

if (!$copiado['ok']) {
  mysqli_query($con, "DELETE FROM fact_facturas WHERE id_fact_facturas = $id_fact");
  echo json_encode(['ok' => false, 'error' => $copiado['error']]);
  exit;
}

if ((int)($copiado['copiados'] ?? 0) <= 0) {
  // Si no hay conceptos, mejor borrar el encabezado para no dejar basura
  mysqli_query($con, "DELETE FROM fact_facturas WHERE id_fact_facturas = $id_fact");
  echo json_encode(['ok' => false, 'error' => 'La remisión no tiene partidas para facturar']);
  exit;
}

echo json_encode(['ok' => true, 'id' => $id_fact, 'copiados' => (int)$copiado['copiados']]);