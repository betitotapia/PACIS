<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
	$active_usuarios="";
	$active_productos="";
	$active_lista_productos="";
	$active_borrador="";
	$active_nueva="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
	$active_almacenes="";
    $active_terceros="active";
    $active_provedores='';
    $active_recepciones="";

require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
// ── Lee id ────────────────────────────────────────────────────────────────────
$id = isset($_GET['id_cliente']) ? (int)$_GET['id_cliente'] : 0;
if ($id <= 0) { die('Parámetro id_cliente inválido'); }

// ── Consulta ─────────────────────────────────────────────────────────────────
$sql = "SELECT * FROM clientes WHERE id_cliente = ? LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$cli = $res->fetch_assoc();
$stmt->close();
if (!$cli) { die('Cliente no encontrado'); }

function h($s){ return htmlspecialchars((string)$s ?? '', ENT_QUOTES, 'UTF-8'); }
function sel($cur, $val){ return trim((string)$cur) === trim((string)$val) ? 'selected' : ''; }
?>

<!DOCTYPE html>
<html lang="en">
<?php
include '../header.php';
?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info  bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <?php
include '../navbar.php';
include '../aside_menu.php';
    ?>
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <div class="container-fluid">
           <form id="clienteForm" class="needs-validation" novalidate enctype="multipart/form-data" method="post" action="../../ajax/update_cliente.php">
    <input type="hidden" name="id_cliente" value="<?= $id ?>">

    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="form-group">
            <label for="nombre">Nombre o razón social</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required value="<?= h($cli['nombre_cliente']) ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="rfc">RFC</label>
            <input type="text" class="form-control" id="rfc" name="rfc" style="text-transform:uppercase" required value="<?= h($cli['rfc']) ?>">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="calle">Calle (Nombre de la vialidad)</label>
            <input type="text" class="form-control" id="calle" name="calle" value="<?= h($cli['calle']) ?>" />
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="no_exterior">Número exterior</label>
            <input type="text" class="form-control" id="no_exterior" name="no_exterior" value="<?= h($cli['num_ext']) ?>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="no_interior">Número interior</label>
            <input type="text" class="form-control" id="no_interior" name="no_interior" value="<?= h($cli['num_int']) ?>">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="colonia">Colonia</label>
            <input type="text" class="form-control" id="colonia" name="colonia" value="<?= h($cli['colonia']) ?>" />
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label for="cp">Código Postal</label>
            <input type="text" pattern="\d{5}" maxlength="5" class="form-control" id="cp" name="cp" value="<?= h($cli['postal']) ?>">
            <div class="form-text">5 dígitos</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="municipio">Municipio / Alcaldía</label>
            <input type="text" class="form-control" id="municipio" name="municipio" value="<?= h($cli['municipio']) ?>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="localidad">Localidad</label>
            <input type="text" class="form-control" id="localidad" name="localidad" value="<?= h($cli['localidad']) ?>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="entidad">Entidad federativa</label>
            <input type="text" class="form-control" id="entidad" name="entidad" value="<?= h($cli['entidad_federativa']) ?>">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="telefono">Teléfono de contacto</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= h($cli['telefono']) ?>">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="uso">Uso del CFDI</label>
            <select class="form-control" id="uso" name="uso">
              <option value="">Selecciona…</option>
              <option value="G01" <?= sel($cli['uso_cfdi'],'G01') ?>>G01 - Adquisición de mercancías</option>
              <option value="G03" <?= sel($cli['uso_cfdi'],'G03') ?>>G03 - Gastos en general</option>
              <option value="D01" <?= sel($cli['uso_cfdi'],'D01') ?>>D01 - Honorarios médicos</option>
              <option value="I01" <?= sel($cli['uso_cfdi'],'I01') ?>>I01 - Construcciones</option>
              <option value="CP01" <?= sel($cli['uso_cfdi'],'CP01') ?>>CP01 - Pagos</option>
              <option value="S01" <?= sel($cli['uso_cfdi'],'S01') ?>>S01 - Sin efectos fiscales</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= h($cli['email']) ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="formaPago">Forma de pago</label>
            <select class="form-control" id="formaPago" name="formaPago">
              <option value="">Selecciona…</option>
              <option <?= sel($cli['forma_pago'],'01 - Efectivo') ?>>01 - Efectivo</option>
              <option <?= sel($cli['forma_pago'],'03 - Transferencia electrónica') ?>>03 - Transferencia electrónica</option>
              <option <?= sel($cli['forma_pago'],'04 - Tarjeta de crédito') ?>>04 - Tarjeta de crédito</option>
              <option <?= sel($cli['forma_pago'],'28 - Tarjeta de débito') ?>>28 - Tarjeta de débito</option>
              <option <?= sel($cli['forma_pago'],'99 - Por definir') ?>>99 - Por definir</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="metodoPago">Método de pago</label>
            <select class="form-control" id="metodoPago" name="metodoPago">
              <option value="">Selecciona…</option>
              <option <?= sel($cli['metodo_pago'],'PUE - Pago en una sola exhibición') ?>>PUE - Pago en una sola exhibición</option>
              <option <?= sel($cli['metodo_pago'],'PPD - Pago en parcialidades o diferido') ?>>PPD - Pago en parcialidades o diferido</option>
            </select>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="form-group">
            <label>Constancia de Situación Fiscal (PDF)</label>
            <div class="input-group">
              <input id="pdfInput" name="csf_pdf" type="file" class="form-control" accept="application/pdf">
            </div>
            <?php if(!empty($cli['cedula'])): ?>
              <small class="form-text text-muted">Actual: <a href="../../ajax/<?= h($cli['cedula']) ?>" target="_blank" rel="noopener">Ver CSF</a></small>
            <?php else: ?>
              <small class="form-text text-muted">No hay CSF cargada</small>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
      <a href="lista_clientes.php" class="btn btn-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </div>
  </form>
</div>
           
            <?php
include("../footer.php");
include("../modal/registro_usuarios.php");
	?>
            <script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
            <script type="text/javascript" src="../../js/clientes.js"> </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" crossorigin="anonymous"></script>
  <script>pdfjsLib.GlobalWorkerOptions.workerSrc='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';</script>
<script>
  // Envío vía fetch para no abandonar la página
  const form = document.getElementById('clienteForm');
  form?.addEventListener('submit', async (e)=>{
    e.preventDefault();
    e.stopPropagation();
    form.classList.add('was-validated');
    if(!form.checkValidity()) return;

    const fd = new FormData(form);
    const btn = form.querySelector('button[type="submit"]');
    btn?.setAttribute('disabled','disabled');
    try{
      const res = await fetch(form.action, { method:'POST', body: fd });
      const json = await res.json();
      if(!res.ok || !json.ok) throw new Error(json.error || 'Error desconocido');
      Swal.fire('OK!','Cambios guardados correctamente','success');
      // Si quieres regresar a la lista:
    }catch(err){
      alert('No se pudo actualizar: ' + err.message);
    }finally{
      btn?.removeAttribute('disabled');
    }
  });
</script>



</body>

</html>