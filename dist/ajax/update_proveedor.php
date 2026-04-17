
<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ob_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); if (ob_get_level()) { @ob_end_clean(); } echo json_encode(['ok'=>false,'error'=>'Método no permitido']); exit; }

require_once("../config/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
require_once("../config/conexion.php"); //Contiene funcion que conecta a la base de datos

 
function v($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : ''; }
$TABLE='proveedores';
$PK = v('pk') ?: 'id_proveedor';
$NAMECOL = v('namecol') ?: 'nombre_proveedor';
$id = (int) ( $_POST['id'] ?? 0 );
if ($id <= 0){ http_response_code(422); echo json_encode(['ok'=>false,'error'=>'ID inválido']); exit; }

// Carga actual (para conocer CSF vigente)
$stmt = $con->prepare("SELECT cedula FROM `$TABLE` WHERE `$PK` = ? LIMIT 1");
$stmt->bind_param('i',$id); $stmt->execute(); $cur = $stmt->get_result()->fetch_assoc(); $stmt->close();
if(!$cur){ http_response_code(404); echo json_encode(['ok'=>false,'error'=>'Proveedor no encontrado']); exit; }
$cedula_path = $cur['cedula'] ?? '';

$nombre = v('nombre');
$rfc = strtoupper(v('rfc'));
$calle = v('calle');
$num_ext = v('no_exterior');
$num_int = v('no_interior');
$colonia = v('colonia');
$postal = v('cp');
$telefono = v('telefono');
$email = v('email');
$municipio = v('municipio');
$localidad = v('localidad');
$entidad = v('entidad');

if($nombre === '' || $rfc === ''){ http_response_code(422); echo json_encode(['ok'=>false,'error'=>'Nombre y RFC son obligatorios']); exit; }
if($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){ http_response_code(422); echo json_encode(['ok'=>false,'error'=>'Correo inválido']); exit; }
if($postal !== '' && !preg_match('/^\d{5}$/',$postal)){ http_response_code(422); echo json_encode(['ok'=>false,'error'=>'El CP debe tener 5 dígitos']); exit; }

// Subida de nueva CSF (opcional)
if(!empty($_FILES['csf_pdf']['name'])){
  $f = $_FILES['csf_pdf'];
  if($f['error'] !== UPLOAD_ERR_OK){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Error al subir el PDF','code'=>$f['error']]); exit; }
  $fi = new finfo(FILEINFO_MIME_TYPE); $mime = $fi->file($f['tmp_name']);
  if($mime !== 'application/pdf'){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'El archivo debe ser PDF']); exit; }
  $dir = __DIR__ . '/uploads/csf_proveedores'; if(!is_dir($dir)){ @mkdir($dir,0775,true); }
  $safeRfc = preg_replace('/[^A-Z0-9_-]/i','', $rfc ?: 'SINRFC');
  $newName = 'CSF_PROV_' . $safeRfc . '_' . date('Ymd_His') . '.pdf';
  $absPath = $dir . '/' . $newName;
  if(!move_uploaded_file($f['tmp_name'],$absPath)){ http_response_code(500); echo json_encode(['ok'=>false,'error'=>'No se pudo guardar el PDF']); exit; }
  // Elimina anterior si estaba en nuestra carpeta
  if($cedula_path && strpos($cedula_path,'uploads/csf_proveedores/') !== false){ $oldAbs = __DIR__ . '/' . $cedula_path; if(is_file($oldAbs)) @unlink($oldAbs); }
  $cedula_path = 'uploads/csf_proveedores/' . $newName;
}

// UPDATE dinámico (considera nombre_proveedor o nombre_provedor)
$sql = "UPDATE `$TABLE` SET
  `$NAMECOL`=?, rfc=?, calle=?, num_ext=?, num_int=?, colonia=?, postal=?,
  telefono=?, email=?, cedula=?, municipio=?, localidad=?, entidad_federativa=?
  WHERE `$PK` = ?";
$stmt = $con->prepare($sql);
if(!$stmt){ http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Prepare failed: '.$con->error]); exit; }
$stmt->bind_param('sssssssssssssi',
  $nombre, $rfc, $calle, $num_ext, $num_int, $colonia, $postal,
  $telefono, $email, $cedula_path, $municipio, $localidad, $entidad, $id
);

if(!$stmt->execute()){
  http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Execute failed: '.$con->error]); $stmt->close(); $con->close(); exit;
}
$stmt->close(); $con->close();

echo json_encode(['ok'=>true,'id'=>$id,'pdf'=>$cedula_path]);
?>
