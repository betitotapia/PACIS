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
$usr = $qUsr ? mysqli_fetch_assoc($qUsr) : null;
$nivel = (int)($usr['is_admin'] ?? 0);

if ($nivel !== 1) {
  die("Acceso restringido");
}

$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = trim((string)($_POST['accion'] ?? ''));

  if ($accion === 'crear') {
    $serie = strtoupper(trim((string)($_POST['serie'] ?? '')));
    $descripcion = trim((string)($_POST['descripcion'] ?? ''));
    $folio_inicial = (int)($_POST['folio_inicial'] ?? 1);

    if ($serie === '') {
      $msg = 'La serie es obligatoria.';
      $msgType = 'danger';
    } elseif ($folio_inicial <= 0) {
      $msg = 'El folio inicial debe ser mayor a cero.';
      $msgType = 'danger';
    } else {
      $serie_sql = mysqli_real_escape_string($con, $serie);
      $desc_sql = mysqli_real_escape_string($con, $descripcion);
      $folio_actual = $folio_inicial - 1;

      $ins = mysqli_query($con, "INSERT INTO remision_series (serie, descripcion, folio_actual, activo, created_at)
        VALUES ('$serie_sql', '$desc_sql', $folio_actual, 1, NOW())");

      if ($ins) {
        $msg = 'Serie creada correctamente.';
      } else {
        $msg = 'No se pudo crear la serie: ' . mysqli_error($con);
        $msgType = 'danger';
      }
    }
  }

  if ($accion === 'editar') {
    $id_serie = (int)($_POST['id_serie'] ?? 0);
    $descripcion = trim((string)($_POST['descripcion'] ?? ''));
    $folio_actual = (int)($_POST['folio_actual'] ?? 0);
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($id_serie <= 0) {
      $msg = 'Serie invalida.';
      $msgType = 'danger';
    } elseif ($folio_actual < 0) {
      $msg = 'Folio actual invalido.';
      $msgType = 'danger';
    } else {
      $desc_sql = mysqli_real_escape_string($con, $descripcion);
      $upd = mysqli_query($con, "UPDATE remision_series
        SET descripcion = '$desc_sql',
            folio_actual = $folio_actual,
            activo = $activo,
            updated_at = NOW()
        WHERE id_serie = $id_serie");

      if ($upd) {
        $msg = 'Serie actualizada correctamente.';
      } else {
        $msg = 'No se pudo actualizar la serie: ' . mysqli_error($con);
        $msgType = 'danger';
      }
    }
  }
}

$series = mysqli_query($con, "SELECT * FROM remision_series ORDER BY serie ASC");

include("../header.php");
?>
<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info bg-body-tertiary">
<div class="app-wrapper">
  <?php include("../navbar.php"); ?>
  <?php include("../aside_menu.php"); ?>

  <main class="app-main">
    <div class="app-content">
      <div class="container-fluid">

        <div class="card card-warning card-outline">
          <div class="card-header">
            <h4 class="m-0">Configuracion de series de remisiones</h4>
          </div>
          <div class="card-body">

            <?php if ($msg !== ''): ?>
              <div class="alert alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <div class="card mb-3">
              <div class="card-header"><strong>Nueva serie</strong></div>
              <div class="card-body">
                <form method="post" class="row g-2">
                  <input type="hidden" name="accion" value="crear">
                  <div class="col-md-2">
                    <label class="form-label">Serie</label>
                    <input type="text" maxlength="10" class="form-control" name="serie" placeholder="REM" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Descripcion</label>
                    <input type="text" class="form-control" name="descripcion" placeholder="Remision principal Cancun">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Folio inicial</label>
                    <input type="number" min="1" class="form-control" name="folio_inicial" value="1" required>
                  </div>
                  <div class="col-md-2 d-grid" style="align-content:end;">
                    <button class="btn btn-warning" type="submit">Crear</button>
                  </div>
                </form>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-sm table-bordered table-striped">
                <thead style="background:#ffc107;color:#000;">
                  <tr>
                    <th>Serie</th>
                    <th>Descripcion</th>
                    <th>Folio actual</th>
                    <th>Activo</th>
                    <th style="width:140px;">Accion</th>
                  </tr>
                </thead>
                <tbody>
                <?php if ($series && mysqli_num_rows($series) > 0): ?>
                  <?php while ($s = mysqli_fetch_assoc($series)): ?>
                    <tr>
                      <form method="post">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_serie" value="<?php echo (int)$s['id_serie']; ?>">
                        <td><strong><?php echo htmlspecialchars($s['serie']); ?></strong></td>
                        <td><input type="text" class="form-control form-control-sm" name="descripcion" value="<?php echo htmlspecialchars($s['descripcion'] ?? ''); ?>"></td>
                        <td><input type="number" min="0" class="form-control form-control-sm" name="folio_actual" value="<?php echo (int)$s['folio_actual']; ?>"></td>
                        <td class="text-center">
                          <input type="checkbox" name="activo" value="1" <?php echo ((int)$s['activo'] === 1) ? 'checked' : ''; ?>>
                        </td>
                        <td class="text-center"><button class="btn btn-sm btn-success">Guardar</button></td>
                      </form>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="text-center text-muted">No hay series registradas.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>

      </div>
    </div>
  </main>
</div>
<?php include("../footer.php"); ?>
</body>
</html>
