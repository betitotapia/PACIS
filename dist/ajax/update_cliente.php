<?php

header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'error'=>'Método no permitido']);
  exit;
}

require_once("../config/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
require_once("../config/conexion.php"); //Contiene funcion que conecta a la base de datos

function v($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : ''; }

$id = isset($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : 0;
if ($id <= 0){ http_response_code(422); echo json_encode(['ok'=>false,'error'=>'id_cliente inválido']); exit; }

// Cargar registro actual para conocer ruta de CSF actual
$stmt = $con->prepare('SELECT cedula FROM clientes WHERE id_cliente = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$cur = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$cur){ http_response_code(404); echo json_encode(['ok'=>false,'error'=>'Cliente no encontrado']); exit; }
$cedula_path = $cur['cedula'] ?? '';

// Campos
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
if ($credito === '') { $credito = '0'; }

// Validaciones clave
if ($nombre === '' || $rfc === ''){ http_response_code(422); echo json_encode(['ok'=>false,'error'=>'Nombre y RFC son obligatorios']); exit; }
if ($postal !== '' && !preg_match('/^\d{5}$/', $postal)){ http_response_code(422); echo json_encode(['ok'=>false,'error'=>'El CP debe tener 5 dígitos']); exit; }
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){ http_response_code(422); echo json_encode(['ok'=>false,'error'=>'Correo inválido']); exit; }

// Subida de nuevo PDF (si viene) y reemplazo
if (!empty($_FILES['csf_pdf']['name'])) {
  $f = $_FILES['csf_pdf'];
  if ($f['error'] !== UPLOAD_ERR_OK){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Error al subir el PDF','code'=>$f['error']]); exit; }
  $fi = new finfo(FILEINFO_MIME_TYPE);
  $mime = $fi->file($f['tmp_name']);
  if ($mime !== 'application/pdf'){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'El archivo debe ser PDF']); exit; }
  $dir = __DIR__ . '/uploads/csf';
  if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
  $safeRfc = preg_replace('/[^A-Z0-9_-]/i','', $rfc ?: 'SINRFC');
  $newName = 'CSF_' . $safeRfc . '_' . date('Ymd_His') . '.pdf';
  $absPath = $dir . '/' . $newName;
  if (!move_uploaded_file($f['tmp_name'], $absPath)) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'No se pudo guardar el PDF']); exit; }
  // Borrar anterior (si estaba dentro de nuestra carpeta y existe)
  if ($cedula_path && strpos($cedula_path, 'uploads/csf/') !== false) {
    $oldAbs = __DIR__ . '/' . $cedula_path;
    if (is_file($oldAbs)) { @unlink($oldAbs); }
  }
  $cedula_path = 'uploads/csf/' . $newName;
}

$sql = "UPDATE clientes SET
  nombre_cliente = ?, rfc = ?, calle = ?, num_ext = ?, num_int = ?, colonia = ?, postal = ?,
  telefono = ?, email = ?, cedula = ?, uso_cfdi = ?, forma_pago = ?, metodo_pago = ?,
  municipio = ?, localidad = ?, entidad_federativa = ?, credito = ?
  WHERE id_cliente = ?";

$stmt = $con->prepare($sql);
if (!$stmt){ http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Prepare failed: '.$con->error]); exit; }

$stmt->bind_param(
  'sssssssssssssssssi',
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
  $credito,
  $id
);

if (!$stmt->execute()){
  if ($con->errno === 1062) { http_response_code(409); echo json_encode(['ok'=>false,'error'=>'Ya existe un cliente con ese nombre']); }
  else { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Execute failed: '.$con->error]); }
  $stmt->close();
  $con->close();
  exit;
}

$stmt->close();
$con->close();

echo json_encode(['ok'=>true,'id'=>$id,'pdf'=>$cedula_path]);