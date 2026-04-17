<?php
// insert_cliente.php
// Guarda un nuevo cliente y (opcional) su CSF en PDF.
// Tabla destino: clientes (ver campos en tu dump). 
// ─────────────────────────────────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');

require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'error'=>'Método no permitido']);
  exit;
}

// 2) Helpers
function v($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : ''; }

// 3) Recoge campos del formulario
$nombre      = v('nombre');
$rfc         = strtoupper(v('rfc'));
$calle       = v('calle');
$num_ext     = v('no_exterior');
$num_int     = v('no_interior');
$colonia     = v('colonia');
$postal      = v('cp');
$telefono    = v('telefono');
$email       = v('email');
$uso_cfdi    = v('uso');
$forma_pago  = v('formaPago');
$metodo_pago = v('metodoPago');
$municipio   = v('municipio');
$localidad   = v('localidad');
$entidad     = v('entidad');
$credito     = v('credito');
if ($credito === '') { $credito = '0'; } // valor por defecto

// 4) Validaciones mínimas
if ($nombre === '' || $rfc === ''){
  http_response_code(422);
  echo json_encode(['ok'=>false,'error'=>'Nombre y RFC son obligatorios']);
  exit;
}
if ($postal !== '' && !preg_match('/^\d{5}$/', $postal)){
  http_response_code(422);
  echo json_encode(['ok'=>false,'error'=>'El CP debe tener 5 dígitos']);
  exit;
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){
  http_response_code(422);
  echo json_encode(['ok'=>false,'error'=>'Correo inválido']);
  exit;
}

// 5) Subida de PDF (opcional) → guardamos ruta en campo `cedula`
$cedula_path = '';
if (!empty($_FILES['csf_pdf']['name'])) {
  $f = $_FILES['csf_pdf'];
  if ($f['error'] === UPLOAD_ERR_OK) {
    $fi = new finfo(FILEINFO_MIME_TYPE);
    $mime = $fi->file($f['tmp_name']);
    if ($mime !== 'application/pdf') {
      http_response_code(400);
      echo json_encode(['ok'=>false,'error'=>'El archivo debe ser PDF']);
      exit;
    }
    $dir = __DIR__ . '/uploads/csf';
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
    $safeRfc = preg_replace('/[^A-Z0-9_-]/i','', $rfc ?: 'SINRFC');
    $newName = 'CSF_' . $safeRfc . '_' . date('Ymd_His') . '.pdf';
    $absPath = $dir . '/' . $newName;
    if (!move_uploaded_file($f['tmp_name'], $absPath)) {
      http_response_code(500);
      echo json_encode(['ok'=>false,'error'=>'No se pudo guardar el PDF']);
      exit;
    }
    // Ruta relativa para guardar en DB
    $cedula_path = 'uploads/csf/' . $newName;
  } else {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Error al subir el PDF','code'=>$f['error']]);
    exit;
  }
}

// 6) Insert preparado
$sql = "INSERT INTO clientes (
  nombre_cliente, rfc, calle, num_ext, num_int, colonia, postal,
  telefono, email, cedula, uso_cfdi, forma_pago, metodo_pago,
  municipio, localidad, entidad_federativa, credito
) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $con->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Prepare failed: '.$con->error]);
  exit;
}

$stmt->bind_param(
  'sssssssssssssssss',
  $nombre,
  $rfc,
  $calle,
  $num_ext,
  $num_int,
  $colonia,
  $postal,
  $telefono,
  $email,
  $cedula_path,
  $uso_cfdi,
  $forma_pago,
  $metodo_pago,
  $municipio,
  $localidad,
  $entidad,
  $credito
);

if (!$stmt->execute()) {
  // 1062: entrada duplicada (índice único por nombre_cliente)
  if ($con->errno === 1062) {
    http_response_code(409);
    echo json_encode(['ok'=>false,'error'=>'Ya existe un cliente con ese nombre']);
  } else {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'Execute failed: '.$con->error]);
  }
  $stmt->close();
  $con->close();
  exit;
}

$id = $stmt->insert_id;
$stmt->close();
$con->close();

echo json_encode(['ok'=>true,'id'=>$id,'pdf'=>$cedula_path]);
