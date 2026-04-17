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

require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
// ── Lee id ────

if (!function_exists('h')) {
  function h($s){
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
}

function fieldExists($con,$table,$col){
  // Usa information_schema con prepared statements (SHOW no soporta bind en MySQL)
  $sql = "SELECT 1 FROM information_schema.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?
          LIMIT 1";
  $st = $con->prepare($sql);
  if(!$st){ return false; }
  $st->bind_param('ss', $table, $col);
  $st->execute();
  $st->store_result();
  $ok = $st->num_rows > 0;
  $st->close();
  return $ok;
}


// Detecta columna PK y columna nombre (por si el esquema trae 'provedor')
$TABLE = 'proveedores';
$PK = fieldExists($con,$TABLE,'id_proveedor') ? 'id_proveedor' : (fieldExists($con,$TABLE,'id_provedor') ? 'id_provedor' : 'id_proveedor');
$NAMECOL = fieldExists($con,$TABLE,'nombre_proveedor') ? 'nombre_proveedor' : (fieldExists($con,$TABLE,'nombre_provedor') ? 'nombre_provedor' : 'nombre_proveedor');

// Lee id desde GET (acepta id_proveedor, id_provedor o id)
$id = 0;
if (isset($_GET['id_proveedor'])) $id = (int)$_GET['id_proveedor'];
elseif (isset($_GET['id_provedor'])) $id = (int)$_GET['id_provedor'];
elseif (isset($_GET['id'])) $id = (int)$_GET['id'];
if ($id <= 0) { die('Parámetro de ID inválido'); }

// Carga registro
$sql = "SELECT * FROM `$TABLE` WHERE `$PK` = ? LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->bind_param('i',$id);
$stmt->execute();
$prov = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$prov) { die('Proveedor no encontrado'); }
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
            <form id="proveedorForm" class="needs-validation" novalidate enctype="multipart/form-data" action="../../ajax/update_proveedor.php" method="post">
    <input type="hidden" name="pk" value="<?= h($PK) ?>">
    <input type="hidden" name="namecol" value="<?= h($NAMECOL) ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="form-group">
            <label for="nombre">Nombre o razón social</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required value="<?= h($prov[$NAMECOL] ?? '') ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="rfc">RFC</label>
            <input type="text" class="form-control" id="rfc" name="rfc" style="text-transform:uppercase" required value="<?= h($prov['rfc'] ?? '') ?>">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="calle">Calle (Nombre de la vialidad)</label>
            <input type="text" class="form-control" id="calle" name="calle" value="<?= h($prov['calle'] ?? '') ?>" />
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="no_exterior">Número exterior</label>
            <input type="text" class="form-control" id="no_exterior" name="no_exterior" value="<?= h($prov['num_ext'] ?? '') ?>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="no_interior">Número interior</label>
            <input type="text" class="form-control" id="no_interior" name="no_interior" value="<?= h($prov['num_int'] ?? '') ?>">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="colonia">Colonia</label>
            <input type="text" class="form-control" id="colonia" name="colonia" value="<?= h($prov['colonia'] ?? '') ?>" />
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label for="cp">Código Postal</label>
            <input type="text" pattern="\d{5}" maxlength="5" class="form-control" id="cp" name="cp" value="<?= h($prov['postal'] ?? '') ?>">
            <div class="form-text">5 dígitos</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="municipio">Municipio / Alcaldía</label>
            <input type="text" class="form-control" id="municipio" name="municipio" value="<?= h($prov['municipio'] ?? '') ?>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="localidad">Localidad</label>
            <input type="text" class="form-control" id="localidad" name="localidad" value="<?= h($prov['localidad'] ?? '') ?>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="entidad">Entidad federativa</label>
            <input type="text" class="form-control" id="entidad" name="entidad" value="<?= h($prov['entidad_federativa'] ?? '') ?>">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="telefono">Teléfono de contacto</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= h($prov['telefono'] ?? '') ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= h($prov['email'] ?? '') ?>" required>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="form-group">
            <label>Constancia de Situación Fiscal (PDF)</label>
            <div class="input-group">
              <input id="pdfInput" name="csf_pdf" type="file" class="form-control" accept="application/pdf">
            </div>
            <?php if(!empty($prov['cedula'])): ?>
              <small class="form-text text-muted">Actual: <a href="../../ajax/<?= h($prov['cedula']) ?>" target="_blank" rel="noopener">Ver CSF</a></small>
            <?php else: ?>
              <small class="form-text text-muted">No hay CSF cargada</small>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
      <a href="lista_proveedores.php" class="btn btn-secondary">Cancelar</a>
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
  <script>pdfjsLib.GlXobalWorkerOptions.workerSrc='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const $ = (id) => document.getElementById(id);
  const form = $('proveedorForm');
  const parseBtn = $('parseBtn');
  const pdfInput = $('pdfInput');
  const status = $('parseStatus');

  const setStatus = (t, c) => { if(status){ status.textContent = t; status.className = c; } };
  const setValue  = (id, v) => { const el = $(id); if(el && v){ el.value = v; } };

  // === PDF helpers ===
  function normalizeText(s){
    const NBSP = String.fromCharCode(160);
    return s.replace(/\r/g,'\n').replace(new RegExp(NBSP,'g'),' ')
            .replace(/[\t ]+/g,' ').replace(/\n{2,}/g,'\n').trim();
  }
  function extractByLabel(txt, patterns, opts={}){
    const stop = /^(Número|Núm\.|No\.|Tipo|Nombre\s*(?:de\s*la)?\s*vialidad|Colonia|C\.?P\.?|Código|Localidad|Municipio|Entidad|Régimen|Correo|Fecha|Denominación|Razón|RFC)\b/i;
    const skipSet = new Set((opts.skip||[]).map(s=>String(s).replace(/\./g,'').trim().toUpperCase()));
    for (const r of patterns){
      const same = txt.match(new RegExp(r + "\\s*:?\\s*([^\\n,]+)", "i"));
      if (same){ const cand = same[1].trim(); if (!skipSet.has(cand.replace(/\./g,'').toUpperCase())) return cand; }
      const i = txt.search(new RegExp(r, "i"));
      if (i !== -1){
        const lines = txt.slice(i).split("\n").slice(1,8).map(s=>s.trim()).filter(Boolean);
        for (const line of lines){
          if (stop.test(line)) continue;
          const key = line.replace(/\./g,'').toUpperCase();
          if (skipSet.has(key)) continue;
          return line;
        }
      }
    }
    return '';
  }
  function buildFieldsFromText(txt){
    const out = {};
    const rfcMatch = txt.match(/\b([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})\b/); if(rfcMatch) out.rfc = rfcMatch[1];
    let nameMatch = txt.match(/Denominación\/Razón\s*Social:\s*([^\n]+)/i)
                  || txt.match(/Denominación\s*\/\s*Razón\s*Social:\s*([^\n]+)/i)
                  || txt.match(/Nombre[, ]*denominación[^\n]*social:?\s*([^\n]+)/i)
                  || txt.match(/Razón\s*social:\s*([^\n]+)/i)
                  || txt.match(/Nombre:\s*([^\n]+)/i);
    if (nameMatch) out.nombre = nameMatch[1].trim();

    let domBlock = '';
    for (const a of [/Datos\s+del\s+domicilio\s+registrado/i, /Domicilio\s+fiscal/i]){
      const i = txt.search(a);
      if (i !== -1){
        const slice = txt.slice(i);
        const end = slice.search(/Actividades\s+Económicas|Regímenes|Régimen\s+Fiscal|Correo|Fecha\s+de|Nombre\s+Comercial/i);
        domBlock = (end>0 ? slice.slice(0,end) : slice);
        break;
      }
    }
    const scope = domBlock || txt;

    const typeWords = ['CALLE','AVENIDA','AV','AV.','BLVD','BOULEVARD','CALZADA','CALZ.','PROLONGACION','PROLONGACIÓN','PROL','PROL.','CARRETERA','AUTOPISTA','ANDADOR','CERRADA','CDA','CDA.','PRIVADA','PRIV','PRIV.','EJE','CIRCUITO','CTO','CTO.','PASAJE','PJE','PJE.','PERIFERICO','PERIFÉRICO','VIADUCTO','PASEO'];
    const tipo = extractByLabel(scope, ['Tipo\\s*de\\s*vialidad','Tipo\\s*de\\s*la\\s*vialidad']);
    const skip = [...typeWords, tipo];
    let nombreVialidad = extractByLabel(scope, ['Nombre\\s*de\\s*(?:la\\s*)?vialidad'], { skip });
    if (!nombreVialidad) nombreVialidad = extractByLabel(scope, ['Vialidad'], { skip });

    const tipoClean = (tipo||'').replace(/\./g,'').trim();
    const nombreClean = (nombreVialidad||'').trim();
    let calleFinal = '';
    if (nombreClean){
      if (tipoClean && nombreClean.toUpperCase().startsWith(tipoClean.toUpperCase()+' ')) calleFinal = nombreClean;
      else if (tipoClean) calleFinal = tipoClean + ' ' + nombreClean;
      else calleFinal = nombreClean;
    } else if (tipoClean){ calleFinal = tipoClean; }
    if (calleFinal) out.calle = calleFinal.replace(/\s{2,}/g,' ').trim();

    const colonia = extractByLabel(scope, ['Nombre\\s*de\\s*la\\s*colonia','Colonia']); if (colonia) out.colonia = colonia;
    const noExt = extractByLabel(scope, ['N[uú]mero\\s*exterior','No\\.?\\s*exterior','N\\.?\\s*Ext\\.?','Num\\.?\\s*Ext\\.?']); if (noExt) out.no_exterior = noExt;
    const noInt = extractByLabel(scope, ['N[uú]mero\\s*interior','No\\.?\\s*interior','N\\.?\\s*Int\\.?','Num\\.?\\s*Int\\.?']); if (noInt) out.no_interior = noInt;

    let cp=''; const cpLabel=/(?:C[óo]digo\s*Postal|C\.?\s*P\.?|CP)/i;
    const cpInBlock = domBlock && domBlock.match(new RegExp(cpLabel.source+"\\s*[:\\-]?\\s*(\\d{5})","i"));
    if (cpInBlock) cp = cpInBlock[1];
    else { const cpAny = txt.match(new RegExp(cpLabel.source+"\\s*[:\\-]?\\s*(\\d{5})","i")); if (cpAny) cp = cpAny[1]; }
    if (cp) out.cp = cp;

    const entidad  = extractByLabel(scope, ['Entidad\\s*Federativa','Estado']); if (entidad) out.entidad = entidad;
    const municipio= extractByLabel(scope, ['Municipio(?:\\s*o\\s*Demarcaci[óo]n\\s*Territorial)?','Alcald[ií]a']); if (municipio) out.municipio = municipio;
    const localidad= extractByLabel(scope, ['Localidad']); if (localidad) out.localidad = localidad;

    const mailMatch = txt.match(/[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,}/i); if (mailMatch) out.email = mailMatch[0];
    return out;
  }

  // Botón Leer y autollenar
  parseBtn?.addEventListener('click', async () => {
    if (!pdfInput || !pdfInput.files || !pdfInput.files[0]){ setStatus('Selecciona un PDF primero','badge bg-danger'); return; }
    setStatus('Leyendo PDF…','badge bg-secondary');
    try {
      const data = buildFieldsFromText(await (async f => {
        const ab = await f.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({ data: ab, useXfa:true }).promise;
        let full=''; for (let p=1; p<=pdf.numPages; p++){ const page = await pdf.getPage(p); const c = await page.getTextContent(); full += '\\n' + c.items.map(it=>it.str).join('\\n'); }
        return normalizeText(full);
      })(pdfInput.files[0]));
      ['nombre','rfc','calle','no_exterior','no_interior','colonia','cp','municipio','localidad','entidad','email'].forEach(k=> setValue(k, data[k]));
      setStatus('Autollenado completado','badge bg-success');
    } catch (err) {
      console.error(err); setStatus('No se pudo leer el PDF','badge bg-danger'); alert('No se pudo leer el PDF. Revisa la consola.');
    }
  });

  // Submit por fetch (evita abrir el JSON)
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    e.stopPropagation();
    form.classList.add('was-validated');
    if (!form.checkValidity()) return;

    const fd  = new FormData(form);
    const btn = form.querySelector('button[type=\"submit\"]');
    btn?.setAttribute('disabled','disabled');

    try {
      const res = await fetch(form.action, { method:'POST', body: fd, headers:{ 'Accept':'application/json' } });
      const ct  = (res.headers.get('content-type')||'').toLowerCase();
      if (!ct.includes('application/json')) {
        const txt = await res.text();
        console.error('Respuesta no-JSON', { status: res.status, ct, preview: txt.slice(0,500) });
        throw new Error(`Respuesta no-JSON (status ${res.status}).`);
      }
      const data = await res.json();
      if (!res.ok || !data.ok) throw new Error(data?.error || `HTTP ${res.status}`);
      Swal.fire('OK!','Cambios guardados con exito', 'success');
      // Redirige si quieres:
      // location.href = 'lista_proveedores.php';
    } catch (err) {
      alert('No se pudo actualizar: ' + err.message);
    } finally {
      btn?.removeAttribute('disabled');
    }
  });
});
</script>



</body>

</html>