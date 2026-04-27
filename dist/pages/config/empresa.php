<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  header("location: ../login");
  exit;
}

include("../../config/db.php");
include("../../config/conexion.php");

$id_usuario = (int)($_SESSION['user_id'] ?? 0);
$qUsr = mysqli_query($con, "SELECT is_admin FROM users WHERE user_id = $id_usuario LIMIT 1");
$usr  = $qUsr ? mysqli_fetch_assoc($qUsr) : null;
$nivel = (int)($usr['is_admin'] ?? 0);
if ($nivel !== 1) { die("Acceso restringido"); }

// ── Crear tabla si no existe ──────────────────────────────────────────────────
mysqli_query($con, "CREATE TABLE IF NOT EXISTS empresa_config (
  id              INT(11)       NOT NULL AUTO_INCREMENT PRIMARY KEY,
  razon_social    VARCHAR(200)  DEFAULT '',
  rfc             VARCHAR(20)   DEFAULT '',
  domicilio_fiscal TEXT         DEFAULT NULL,
  email           VARCHAR(150)  DEFAULT '',
  telefono        VARCHAR(30)   DEFAULT '',
  codigo_postal   VARCHAR(10)   DEFAULT '',
  regimen_fiscal  VARCHAR(100)  DEFAULT '',
  logo_path       VARCHAR(300)  DEFAULT NULL,
  cer_path        VARCHAR(300)  DEFAULT NULL,
  key_path        VARCHAR(300)  DEFAULT NULL,
  contrasena_key  VARCHAR(300)  DEFAULT NULL,
  updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$cnt = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM empresa_config"));
if ((int)$cnt['c'] === 0) {
  mysqli_query($con, "INSERT INTO empresa_config (id) VALUES (1)");
}

// ── Directorios de carga ──────────────────────────────────────────────────────
$DIR_LOGO  = __DIR__ . '/../../uploads/empresa/';
$DIR_CERTS = __DIR__ . '/../../uploads/certs/';
foreach ([$DIR_LOGO, $DIR_CERTS] as $d) {
  if (!is_dir($d)) { mkdir($d, 0755, true); }
}

// Proteger certs con .htaccess la primera vez
$htaccess = $DIR_CERTS . '.htaccess';
if (!file_exists($htaccess)) {
  file_put_contents($htaccess,
    "<IfModule mod_authz_core.c>\n  Require all denied\n</IfModule>\n" .
    "<IfModule !mod_authz_core.c>\n  Deny from all\n</IfModule>\n");
}

$msg     = '';
$msgType = 'success';
$tab     = 'datos';   // pestaña activa tras POST

// ── Procesar POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = trim((string)($_POST['accion'] ?? ''));

  // ── Datos generales ──────────────────────────────────────────
  if ($accion === 'datos_generales') {
    $tab = 'datos';
    $razon_social    = mysqli_real_escape_string($con, trim($_POST['razon_social']    ?? ''));
    $rfc             = strtoupper(mysqli_real_escape_string($con, trim($_POST['rfc'] ?? '')));
    $domicilio_fiscal= mysqli_real_escape_string($con, trim($_POST['domicilio_fiscal']?? ''));
    $email           = mysqli_real_escape_string($con, trim($_POST['email']           ?? ''));
    $telefono        = mysqli_real_escape_string($con, trim($_POST['telefono']        ?? ''));
    $codigo_postal   = mysqli_real_escape_string($con, trim($_POST['codigo_postal']   ?? ''));
    $regimen_fiscal  = mysqli_real_escape_string($con, trim($_POST['regimen_fiscal']  ?? ''));

    $logo_set = '';
    if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
      $ext     = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg','jpeg','png','gif','svg','webp'];
      if (!in_array($ext, $allowed)) {
        $msg = 'El logo debe ser una imagen (jpg, png, gif, svg, webp).';
        $msgType = 'danger';
      } else {
        $fname = 'logo_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $DIR_LOGO . $fname)) {
          $p = mysqli_real_escape_string($con, 'uploads/empresa/' . $fname);
          $logo_set = ", logo_path = '$p'";
        } else {
          $msg = 'No se pudo guardar el logo.';
          $msgType = 'danger';
        }
      }
    }

    if ($msg === '') {
      $ok = mysqli_query($con, "UPDATE empresa_config SET
        razon_social     = '$razon_social',
        rfc              = '$rfc',
        domicilio_fiscal = '$domicilio_fiscal',
        email            = '$email',
        telefono         = '$telefono',
        codigo_postal    = '$codigo_postal',
        regimen_fiscal   = '$regimen_fiscal'
        $logo_set
        WHERE id = 1");
      $msg = $ok ? 'Datos guardados correctamente.' : 'Error: ' . mysqli_error($con);
      if (!$ok) $msgType = 'danger';
    }
  }

  // ── Certificados CFDI ────────────────────────────────────────
  if ($accion === 'certificados') {
    $tab = 'certs';
    $contrasena = mysqli_real_escape_string($con, trim($_POST['contrasena_key'] ?? ''));
    $sets = ["contrasena_key = '$contrasena'"];

    foreach ([
      'archivo_cer' => ['cer', 'certificado', 'cer_path'],
      'archivo_key' => ['key', 'llave',       'key_path'],
    ] as $field => [$ext_req, $prefix, $col]) {
      if (!empty($_FILES[$field]['name']) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if ($ext !== $ext_req) {
          $msg = "El archivo de $col debe tener extensión .$ext_req";
          $msgType = 'danger';
          break;
        }
        $fname = $prefix . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES[$field]['tmp_name'], $DIR_CERTS . $fname)) {
          $p = mysqli_real_escape_string($con, 'uploads/certs/' . $fname);
          $sets[] = "$col = '$p'";
        } else {
          $msg = "No se pudo guardar el archivo $col.";
          $msgType = 'danger';
          break;
        }
      }
    }

    if ($msg === '') {
      $ok = mysqli_query($con, "UPDATE empresa_config SET " . implode(', ', $sets) . " WHERE id = 1");
      $msg = $ok ? 'Certificados actualizados correctamente.' : 'Error: ' . mysqli_error($con);
      if (!$ok) $msgType = 'danger';
    }
  }
}

// ── Cargar datos actuales ─────────────────────────────────────────────────────
$emp = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM empresa_config WHERE id = 1 LIMIT 1"));
if (!$emp) $emp = [];

// Catálogo de regímenes fiscales SAT
$regimenes = [
  '601' => '601 – General de Ley Personas Morales',
  '603' => '603 – Personas Morales con Fines no Lucrativos',
  '605' => '605 – Sueldos y Salarios',
  '606' => '606 – Arrendamiento',
  '607' => '607 – Enajenación o Adquisición de Bienes',
  '608' => '608 – Demás ingresos',
  '610' => '610 – Residentes en el Extranjero sin EP en México',
  '611' => '611 – Ingresos por Dividendos',
  '612' => '612 – Personas Físicas con Actividades Empresariales y Profesionales',
  '614' => '614 – Ingresos por Intereses',
  '616' => '616 – Sin obligaciones fiscales',
  '620' => '620 – Sociedades Cooperativas de Producción',
  '621' => '621 – Incorporación Fiscal',
  '622' => '622 – Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
  '623' => '623 – Opcional para Grupos de Sociedades',
  '624' => '624 – Coordinados',
  '625' => '625 – Ingresos por Plataformas Tecnológicas',
  '626' => '626 – Régimen Simplificado de Confianza',
];

include("../header.php");
?>
<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info bg-body-tertiary">
<div class="app-wrapper">
  <?php include("../navbar.php"); ?>
  <?php include("../aside_menu.php"); ?>

  <main class="app-main">
    <div class="app-content">
      <div class="container-fluid">

        <div class="card card-primary card-outline mt-3">
          <div class="card-header">
            <h4 class="m-0"><i class="bi bi-building me-2"></i>Configuración de Empresa</h4>
          </div>
          <div class="card-body">

            <?php if ($msg !== ''): ?>
              <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" role="tablist">
              <li class="nav-item">
                <button class="nav-link <?php echo $tab === 'datos' ? 'active' : ''; ?>"
                  data-bs-toggle="tab" data-bs-target="#tab-datos" type="button">
                  <i class="bi bi-info-circle me-1"></i>Datos Generales
                </button>
              </li>
              <li class="nav-item">
                <button class="nav-link <?php echo $tab === 'certs' ? 'active' : ''; ?>"
                  data-bs-toggle="tab" data-bs-target="#tab-certs" type="button">
                  <i class="bi bi-shield-lock me-1"></i>Certificados CFDI
                </button>
              </li>
            </ul>

            <div class="tab-content">

              <!-- ── TAB: Datos Generales ─────────────────────────────────── -->
              <div class="tab-pane fade <?php echo $tab === 'datos' ? 'show active' : ''; ?>" id="tab-datos">
                <form method="post" enctype="multipart/form-data">
                  <input type="hidden" name="accion" value="datos_generales">

                  <div class="row g-3">

                    <!-- Logo actual -->
                    <?php if (!empty($emp['logo_path'])): ?>
                    <div class="col-12 text-center">
                      <p class="mb-1 text-muted small">Logo actual</p>
                      <img src="../../<?php echo htmlspecialchars($emp['logo_path']); ?>"
                           alt="Logo empresa" style="max-height:100px; max-width:300px;"
                           class="border rounded p-1">
                    </div>
                    <?php endif; ?>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Logotipo</label>
                      <input type="file" name="logo" class="form-control" accept="image/*">
                      <div class="form-text">Formatos: jpg, png, gif, svg, webp</div>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Razón Social <span class="text-danger">*</span></label>
                      <input type="text" name="razon_social" class="form-control"
                             value="<?php echo htmlspecialchars($emp['razon_social'] ?? ''); ?>"
                             maxlength="200" required>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold">RFC <span class="text-danger">*</span></label>
                      <input type="text" name="rfc" class="form-control text-uppercase"
                             value="<?php echo htmlspecialchars($emp['rfc'] ?? ''); ?>"
                             maxlength="13" placeholder="XAXX010101000" required>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Código Postal <span class="text-danger">*</span></label>
                      <input type="text" name="codigo_postal" class="form-control"
                             value="<?php echo htmlspecialchars($emp['codigo_postal'] ?? ''); ?>"
                             maxlength="5" placeholder="00000" required>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Teléfono</label>
                      <input type="text" name="telefono" class="form-control"
                             value="<?php echo htmlspecialchars($emp['telefono'] ?? ''); ?>"
                             maxlength="30" placeholder="+52 998 000 0000">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Correo Electrónico</label>
                      <input type="email" name="email" class="form-control"
                             value="<?php echo htmlspecialchars($emp['email'] ?? ''); ?>"
                             maxlength="150">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Régimen Fiscal <span class="text-danger">*</span></label>
                      <select name="regimen_fiscal" class="form-select" required>
                        <option value="">-- Selecciona --</option>
                        <?php foreach ($regimenes as $clave => $desc): ?>
                          <option value="<?php echo $clave; ?>"
                            <?php echo (($emp['regimen_fiscal'] ?? '') === $clave) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($desc); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="col-12">
                      <label class="form-label fw-semibold">Domicilio Fiscal</label>
                      <textarea name="domicilio_fiscal" class="form-control" rows="3"
                                placeholder="Calle, número, colonia, municipio, estado"><?php
                        echo htmlspecialchars($emp['domicilio_fiscal'] ?? '');
                      ?></textarea>
                    </div>

                    <div class="col-12">
                      <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy me-1"></i>Guardar datos generales
                      </button>
                    </div>

                  </div><!-- /row -->
                </form>
              </div>
              <!-- ── FIN TAB Datos Generales ───────────────────────────────── -->

              <!-- ── TAB: Certificados CFDI ──────────────────────────────── -->
              <div class="tab-pane fade <?php echo $tab === 'certs' ? 'show active' : ''; ?>" id="tab-certs">

                <!-- Estado actual de archivos -->
                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <div class="card border-secondary">
                      <div class="card-body py-2">
                        <p class="mb-1 text-muted small">Certificado (.cer)</p>
                        <?php if (!empty($emp['cer_path'])): ?>
                          <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>
                            <?php echo htmlspecialchars(basename($emp['cer_path'])); ?></span>
                        <?php else: ?>
                          <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>No cargado</span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card border-secondary">
                      <div class="card-body py-2">
                        <p class="mb-1 text-muted small">Llave privada (.key)</p>
                        <?php if (!empty($emp['key_path'])): ?>
                          <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>
                            <?php echo htmlspecialchars(basename($emp['key_path'])); ?></span>
                        <?php else: ?>
                          <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>No cargada</span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="alert alert-warning d-flex align-items-center" role="alert">
                  <i class="bi bi-shield-exclamation fs-5 me-2"></i>
                  <div>
                    Los archivos de certificado se almacenan en un directorio protegido del servidor.
                    La contraseña se guarda en la base de datos para uso interno del sistema de timbrado.
                  </div>
                </div>

                <form method="post" enctype="multipart/form-data">
                  <input type="hidden" name="accion" value="certificados">

                  <div class="row g-3">

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Certificado CSD (.cer)</label>
                      <input type="file" name="archivo_cer" class="form-control" accept=".cer">
                      <div class="form-text">Solo archivos .cer del SAT</div>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Llave Privada (.key)</label>
                      <input type="file" name="archivo_key" class="form-control" accept=".key">
                      <div class="form-text">Solo archivos .key del SAT</div>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Contraseña de la llave privada</label>
                      <div class="input-group">
                        <input type="password" name="contrasena_key" id="contrasena_key"
                               class="form-control"
                               value="<?php echo htmlspecialchars($emp['contrasena_key'] ?? ''); ?>"
                               autocomplete="off" placeholder="Contraseña del archivo .key">
                        <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePass()" title="Mostrar/ocultar">
                          <i class="bi bi-eye" id="ico-pass"></i>
                        </button>
                      </div>
                    </div>

                    <div class="col-12">
                      <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i>Guardar certificados
                      </button>
                    </div>

                  </div><!-- /row -->
                </form>
              </div>
              <!-- ── FIN TAB Certificados ───────────────────────────────────── -->

            </div><!-- /tab-content -->
          </div><!-- /card-body -->
        </div><!-- /card -->

      </div>
    </div>
  </main>
</div>

<script>
function togglePass() {
  var inp = document.getElementById('contrasena_key');
  var ico = document.getElementById('ico-pass');
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.classList.replace('bi-eye', 'bi-eye-slash');
  } else {
    inp.type = 'password';
    ico.classList.replace('bi-eye-slash', 'bi-eye');
  }
}
// RFC a mayúsculas
document.querySelector('[name="rfc"]')?.addEventListener('input', function(){
  this.value = this.value.toUpperCase();
});
</script>

<?php include("../footer.php"); ?>
</body>
</html>
