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
            <form id="clienteForm" class="needs-validation" novalidate enctype="multipart/form-data">
                    <div class="row g-3">
                           <div class="col-md-6">
          <div class="form-group">
            <label for="nombre">Nombre o razón social</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
            <div class="invalid-feedback">Escribe el nombre o razón social.</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="rfc">RFC</label>
            <input type="text" class="form-control" id="rfc" name="rfc" style="text-transform:uppercase" required>
            <div class="invalid-feedback">Indica el RFC.</div>
          </div>
        </div>

        <!-- Domicilio desglosado (sin campo de resumen) -->
        <div class="col-md-6">
          <div class="form-group">
            <label for="calle">Calle (Nombre de la vialidad)</label>
            <input type="text" class="form-control" id="calle" name="calle" placeholder="Ej. AVENIDA REFORMA" />
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="no_exterior">Número exterior</label>
            <input type="text" class="form-control" id="no_exterior" name="no_exterior" placeholder="Ej. 123B o S/N">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="no_interior">Número interior</label>
            <input type="text" class="form-control" id="no_interior" name="no_interior" placeholder="Ej. 4 o A-2">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="colonia">Colonia</label>
            <input type="text" class="form-control" id="colonia" name="colonia" placeholder="Ej. Centro" />
          </div>
        </div>

        <!-- CP / Municipio / Localidad / Entidad Federativa -->
        <div class="col-md-3">
          <div class="form-group">
            <label for="cp">Código Postal</label>
            <input type="text" pattern="\d{5}" maxlength="5" class="form-control" id="cp" name="cp" placeholder="00000">
            <div class="form-text">5 dígitos</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="municipio">Municipio / Alcaldía</label>
            <input type="text" class="form-control" id="municipio" name="municipio" placeholder="Ej. Puebla">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="localidad">Localidad</label>
            <input type="text" class="form-control" id="localidad" name="localidad" placeholder="Ej. Puebla">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="entidad">Entidad federativa</label>
            <input type="text" class="form-control" id="entidad" name="entidad" placeholder="Ej. Puebla">
          </div>
        </div>

        <!-- Teléfono de contacto (manual) -->
        <div class="col-md-6">
          <div class="form-group">
            <label for="telefono">Teléfono de contacto</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej. 55 1234 5678">
          </div>
        </div>

        <!-- Otros datos -->
        <div class="col-md-6">
          <div class="form-group">
            <label for="uso">Uso del CFDI</label>
            <select class="form-control" id="uso" name="uso">
              <option value="">Selecciona…</option>
              <option value="G01">G01 - Adquisición de mercancías</option>
              <option value="G03">G03 - Gastos en general</option>
              <option value="D01">D01 - Honorarios médicos</option>
              <option value="I01">I01 - Construcciones</option>
              <option value="CP01">CP01 - Pagos</option>
              <option value="S01">S01 - Sin efectos fiscales</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="formaPago">Forma de pago</label>
            <select class="form-control" id="formaPago" name="formaPago">
              <option value="">Selecciona…</option>
              <option>01 - Efectivo</option>
              <option>03 - Transferencia electrónica</option>
              <option>04 - Tarjeta de crédito</option>
              <option>28 - Tarjeta de débito</option>
              <option>99 - Por definir</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="metodoPago">Método de pago</label>
            <select class="form-control" id="metodoPago" name="metodoPago">
              <option value="">Selecciona…</option>
              <option>PUE - Pago en una sola exhibición</option>
              <option>PPD - Pago en parcialidades o diferido</option>
            </select>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="form-group">
            <label>Constancia de Situación Fiscal (PDF)</label>
            <div class="input-group">
             <input id="pdfInput" name="csf_pdf" type="file" class="form-control" accept="application/pdf">
              <button class="btn btn-outline-primary" type="button" id="parseBtn">Leer y autollenar</button>
            </div>
            <small id="fileName" class="form-text text-muted">Ningún archivo seleccionado</small>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer text-end d-flex gap-2 justify-content-end">
      <button type="reset" class="btn btn-secondary">Limpiar</button>
      <button type="submit" class="btn btn-primary">Guardar</button>
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
          document.addEventListener('DOMContentLoaded',()=>{
  const el = id => document.getElementById(id);
  const status = el('parseStatus');
  const pdfInput = el('pdfInput');
  const parseBtn = el('parseBtn');
  const fileName = el('fileName');

  const setStatus = (text, cls) => { if(status){ status.textContent = text; status.className = cls; } };
  const setFileName = (text) => { if(fileName){ fileName.textContent = text; } };
  const setValue = (id, val) => { const x = el(id); if(x && val){ x.value = val; } };

  pdfInput?.addEventListener('change', ()=>{
    setFileName(pdfInput.files[0] ? pdfInput.files[0].name : 'Ningún archivo seleccionado');
  });

  function normalizeText(s){
    const NBSP = String.fromCharCode(160);
    return s.replace(/\r/g,'\n').replace(new RegExp(NBSP,'g'),' ')
            .replace(/[\t ]+/g,' ').replace(/\n{2,}/g,'\n').trim();
  }

  // Lee "valor" por etiqueta (misma línea o las siguientes). Permite filtrar palabras a omitir.
  function extractByLabel(txt, patterns, opts={}){
    const stop = /^(Número|Núm\.|No\.|Tipo|Nombre\s*(?:de\s*la)?\s*vialidad|Colonia|C\.?P\.?|Código|Localidad|Municipio|Entidad|Régimen|Correo|Fecha|Denominación|Razón|RFC)\b/i;
    const skipSet = new Set((opts.skip||[]).map(s=>String(s).replace(/\./g,'').trim().toUpperCase()));
    for(const r of patterns){
      // 1) Mismo renglón
      const same = txt.match(new RegExp(r + "\\s*:?\\s*([^\\n,]+)", 'i'));
      if(same){
        const cand = same[1].trim();
        if(!skipSet.has(cand.replace(/\./g,'').toUpperCase())) return cand;
      }
      // 2) Siguientes renglones (ventana)
      const i = txt.search(new RegExp(r, 'i'));
      if(i !== -1){
        const lines = txt.slice(i).split('\n').slice(1,8).map(s=>s.trim()).filter(Boolean);
        for(const line of lines){
          if(stop.test(line)) continue; // salta otras etiquetas
          const key = line.replace(/\./g,'').toUpperCase();
          if(skipSet.has(key)) continue; // salta palabras a omitir
          return line; // primera candidata válida
        }
      }
    }
    return '';
  }

  function extractFields(txt){
    const out = {};
    // RFC
    const rfcMatch = txt.match(/\b([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})\b/);
    if(rfcMatch) out.rfc = rfcMatch[1];

    // Nombre o Razón Social
    let nameMatch =
      txt.match(/Denominación\/Razón\s*Social:\s*([^\n]+)/i) ||
      txt.match(/Denominación\s*\/\s*Razón\s*Social:\s*([^\n]+)/i) ||
      txt.match(/Nombre[, ]*denominación[^\n]*social:?\s*([^\n]+)/i) ||
      txt.match(/Razón\s*social:\s*([^\n]+)/i) ||
      txt.match(/Nombre:\s*([^\n]+)/i);
    if(nameMatch) out.nombre = nameMatch[1].trim();

    // Localizar bloque de domicilio
    let domBlock = '';
    for(const a of [/Datos\s+del\s+domicilio\s+registrado/i, /Domicilio\s+fiscal/i]){
      const i = txt.search(a);
      if(i !== -1){
        const slice = txt.slice(i);
        const end = slice.search(/Actividades\s+Económicas|Regímenes|Régimen\s+Fiscal|Correo|Fecha\s+de|Nombre\s+Comercial/i);
        domBlock = (end>0? slice.slice(0,end): slice);
        break;
      }
    }

    const scope = domBlock || txt;

    // ---- Calle: Tipo + Nombre de la vialidad con DEDUP ----
    const typeWords = ['CALLE','AVENIDA','AV','AV.','BLVD','BOULEVARD','CALZADA','CALZ.','PROLONGACION','PROLONGACIÓN','PROL','PROL.','CARRETERA','AUTOPISTA','ANDADOR','CERRADA','CDA','CDA.','PRIVADA','PRIV','PRIV.','EJE','CIRCUITO','CTO','CTO.','PASAJE','PJE','PJE.','PERIFERICO','PERIFÉRICO','VIADUCTO','PASEO'];
    const tipo = extractByLabel(scope, ['Tipo\\s*de\\s*vialidad','Tipo\\s*de\\s*la\\s*vialidad']);
    const skip = [...typeWords, tipo];
    // Busca nombre (evitando devolver el tipo)
    let nombreVialidad = extractByLabel(scope, ['Nombre\\s*de\\s*(?:la\\s*)?vialidad'], { skip });
    if(!nombreVialidad){
      // fallback cuando solo hay "Vialidad" y la primera línea es el tipo
      nombreVialidad = extractByLabel(scope, ['Vialidad'], { skip });
    }

    // Construye calle final evitando duplicaciones tipo "CALLE CALLE"
    const tipoClean = (tipo||'').replace(/\./g,'').trim();
    const nombreClean = (nombreVialidad||'').trim();
    let calleFinal = '';
    if(nombreClean){
      if(tipoClean && nombreClean.toUpperCase().startsWith(tipoClean.toUpperCase()+' ')){
        calleFinal = nombreClean;
      } else if(tipoClean) {
        calleFinal = tipoClean + ' ' + nombreClean;
      } else {
        calleFinal = nombreClean;
      }
    } else if(tipoClean) {
      // último recurso, pero normalmente no deberíamos quedarnos solo con "CALLE"
      calleFinal = tipoClean;
    }
    if(calleFinal) out.calle = calleFinal.replace(/\s{2,}/g,' ').trim();

    // Colonia
    const colonia = extractByLabel(scope, ['Nombre\\s*de\\s*la\\s*colonia','Colonia']);
    if(colonia) out.colonia = colonia;

    // No. exterior / interior
    const noExt = extractByLabel(scope, ['N[uú]mero\\s*exterior','No\\.?\\s*exterior','N\\.?\\s*Ext\\.?','Num\\.?\\s*Ext\\.?']);
    if(noExt) out.no_exterior = noExt;
    const noInt = extractByLabel(scope, ['N[uú]mero\\s*interior','No\\.?\\s*interior','N\\.?\\s*Int\\.?','Num\\.?\\s*Int\\.?']);
    if(noInt) out.no_interior = noInt;

    // Código Postal
    let cp = '';
    const cpLabel = /(?:C[óo]digo\s*Postal|C\.?\s*P\.?|CP)/i;
    const cpInBlock = domBlock && domBlock.match(new RegExp(cpLabel.source + "\\s*[:\\-]?\\s*(\\d{5})", 'i'));
    if(cpInBlock) cp = cpInBlock[1];
    if(!cp){
      const cpAny = txt.match(new RegExp(cpLabel.source + "\\s*[:\\-]?\\s*(\\d{5})", 'i'));
      if(cpAny) cp = cpAny[1];
    }
    if(cp) out.cp = cp;

    // Entidad / Municipio / Localidad
    const entidad = extractByLabel(scope, ['Entidad\\s*Federativa','Estado']);
    if(entidad) out.entidad = entidad;
    const municipio = extractByLabel(scope, ['Municipio(?:\\s*o\\s*Demarcaci[óo]n\\s*Territorial)?','Alcald[ií]a']);
    if(municipio) out.municipio = municipio;
    const localidad = extractByLabel(scope, ['Localidad']);
    if(localidad) out.localidad = localidad;

    // Email (si aparece)
    const mailMatch = txt.match(/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i);
    if(mailMatch) out.email = mailMatch[0];

    // DEBUG opcional en consola
    console.log('DEBUG calle', { tipo, nombreVialidad, calleFinal });

    return out;
  }

  async function readPdfText(file){
    const data = await file.arrayBuffer();
    const pdf = await pdfjsLib.getDocument({
      data,
      useXfa: true,
      cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
      cMapPacked: true,
      standardFontDataUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/standard_fonts/'
    }).promise;

    let full = '';
    for(let p=1; p<=pdf.numPages; p++){
      try{
        const page = await pdf.getPage(p);
        const content = await page.getTextContent();
        const strings = content.items.map(it=>it.str).join('\n');
        full += '\n' + strings;
      }catch(e){ console.warn('Fallo leyendo página', p, e); }
    }
    return normalizeText(full);
  }

  parseBtn?.addEventListener('click', async ()=>{
    if(!pdfInput || !pdfInput.files || !pdfInput.files[0]){
      setStatus('Selecciona un PDF primero', 'badge bg-danger');
      return;
    }
    setStatus('Leyendo PDF…', 'badge bg-secondary');
    try{
      const text = await readPdfText(pdfInput.files[0]);
      const data = extractFields(text);

      setValue('nombre', data.nombre);
      setValue('rfc', data.rfc ? data.rfc.toUpperCase() : '');

      setValue('calle', data.calle);
      setValue('no_exterior', data.no_exterior);
      setValue('no_interior', data.no_interior);
      setValue('colonia', data.colonia);

      setValue('cp', data.cp);
      setValue('municipio', data.municipio);
      setValue('localidad', data.localidad);
      setValue('entidad', data.entidad);

      setValue('email', data.email);

      setStatus('Autollenado completado', 'badge bg-success');
      console.log({textoExtraido: text, campos: data});
    }catch(err){
      console.error('Error leyendo PDF:', err);
      setStatus('No se pudo leer el PDF', 'badge bg-danger');
      alert('Ocurrió un error leyendo el PDF con PDF.js (posible XFA/fonts). Revisa la consola para detalles.');
    }
  });

  // Validación y envío (placeholder)
 const form = document.getElementById('clienteForm');
form?.addEventListener('submit', async (e)=>{
  e.preventDefault();
  e.stopPropagation();
  form.classList.add('was-validated');
  if(!form.checkValidity()) return;

  const el = id => document.getElementById(id);
  const fd = new FormData(form);
  // Normaliza/asegura nombres tal como espera insert_cliente.php
  fd.set('nombre', (el('nombre')?.value || '').trim());
  fd.set('rfc', (el('rfc')?.value || '').trim().toUpperCase());
  fd.set('calle', (el('calle')?.value || '').trim());
  fd.set('no_exterior', (el('no_exterior')?.value || '').trim());
  fd.set('no_interior', (el('no_interior')?.value || '').trim());
  fd.set('colonia', (el('colonia')?.value || '').trim());
  fd.set('cp', (el('cp')?.value || '').trim());
  fd.set('municipio', (el('municipio')?.value || '').trim());
  fd.set('localidad', (el('localidad')?.value || '').trim());
  fd.set('entidad', (el('entidad')?.value || '').trim());
  fd.set('telefono', (el('telefono')?.value || '').trim());
  fd.set('uso', el('uso')?.value || '');
  fd.set('email', (el('email')?.value || '').trim());
  fd.set('formaPago', el('formaPago')?.value || '');
  fd.set('metodoPago', el('metodoPago')?.value || '');

  // Si el input tiene name="csf_pdf", FormData(form) ya lo incluye.
  const btn = form.querySelector('button[type="submit"]');
  btn?.setAttribute('disabled','disabled');
  try{
    const res = await fetch('../../ajax/insert_cliente.php', { method:'POST', body: fd });
    const json = await res.json();
    if(!res.ok || !json.ok) throw new Error(json.error || 'Error desconocido');
    alert('Cliente guardado con ID: ' + json.id);
    form.reset();
    document.getElementById('fileName').textContent = 'Ningún archivo seleccionado';
  }catch(err){
    alert('No se pudo guardar: ' + err.message);
  }finally{
    btn?.removeAttribute('disabled');
  }
});


});
</script>


</body>

</html>