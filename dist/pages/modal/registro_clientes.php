<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro de Cliente – Estilo oscuro + Autollenado PDF</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0b1220;      /* fondo general */
      --panel:#0e1626;   /* barras oscuras (labels) */
      --ink:#f6efe8;     /* texto claro */
      --muted:#9fb0c6;   /* texto secundario */
      --brand:#5eead4;   /* acentos */
      --error:#ef4444;   /* errores */
      --ok:#22c55e;      /* éxito */
      --ring: 0 0 0 3px rgba(94,234,212,.25);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{margin:0;background:linear-gradient(180deg,#0a0f1a,#0d1424 20%,#0a1222);color:var(--ink);font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif}

    .wrap{max-width:980px;margin:40px auto;padding:24px}
    .card{background:rgba(255,255,255,.02);backdrop-filter:saturate(120%) blur(6px);border:1px solid rgba(255,255,255,.06);border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.35)}
    .card-header{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid rgba(255,255,255,.08)}
    .title{font-size:20px;font-weight:700;letter-spacing:.2px}
    .sub{color:var(--muted);font-size:13px}

    .grid{display:grid;grid-template-columns:260px 1fr;align-items:stretch}
    @media (max-width:760px){.grid{grid-template-columns:1fr}.label{border-radius:10px 10px 0 0}.field{border-radius:0 0 10px 10px}}

    .row{display:contents}

    .label{background:var(--panel);padding:16px 18px;border-bottom:1px solid rgba(255,255,255,.06);font-weight:700}
    .field{padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;gap:12px;align-items:center}

    input[type="text"], input[type="email"], select, textarea{
      width:100%;padding:12px 14px;border-radius:10px;background:rgba(255,255,255,.06);border:1px solid transparent;color:var(--ink);font-size:14px;outline:none;transition:.2s;
    }
    input::placeholder, textarea::placeholder{color:#bfd0e680}
    input:focus, select:focus, textarea:focus{border-color:#38bdf8;box-shadow:var(--ring)}

    .hint{font-size:12px;color:var(--muted)}

    .uploader{display:flex;gap:12px;align-items:center}
    .uploader input[type=file]{display:none}
    .btn{appearance:none;border:0;border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer;transition:transform .04s ease, box-shadow .2s}
    .btn:active{transform:translateY(1px)}
    .btn-primary{background:linear-gradient(180deg,#24c8b8,#10b3a3);color:#052a2a;box-shadow:0 6px 20px rgba(16,179,163,.35)}
    .btn-ghost{background:rgba(255,255,255,.06);color:var(--ink);border:1px solid rgba(255,255,255,.12)}

    .pill{display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(255,255,255,.12);border-radius:999px;padding:6px 10px;font-size:12px;color:var(--muted)}
    .status-ok{color:var(--ok)}
    .status-bad{color:var(--error)}

    .footer{display:flex;gap:10px;justify-content:flex-end;padding:16px;border-top:1px solid rgba(255,255,255,.08)}

    .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);border:0}
  </style>
  <!-- PDF.js (para extraer texto del PDF en el navegador) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" integrity="sha512-3m2v0UQx6tDqGqytUee+L5pkkNf3wV9nGoiF2vQbX3JEDJjc9D1D4E+oD7M2QWZrP5aO1dQF9xY/2G2n+VnfyA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js'</script>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="title">Alta de cliente</div>
          <div class="sub">Estilo visual inspirado en tu captura. Puedes subir la <strong>Constancia de Situación Fiscal (PDF)</strong> para autocompletar.</div>
        </div>
        <div class="pill" id="parseStatus">Listo para analizar PDF</div>
      </div>

      <form id="clienteForm" autocomplete="on">
        <div class="grid">
          <div class="label">Nombre o razón social</div>
          <div class="field"><input id="nombre" name="nombre" type="text" placeholder="Ej. DISTRIBUIDORA EJEMPLO, S.A. DE C.V."></div>

          <div class="label">RFC</div>
          <div class="field"><input id="rfc" name="rfc" type="text" placeholder="AAA010101AAA" style="text-transform:uppercase"></div>

          <div class="label">Domicilio fiscal</div>
          <div class="field"><textarea id="domicilio" name="domicilio" rows="3" placeholder="Calle, número, colonia, CP, municipio, entidad"></textarea></div>

          <div class="label">Uso del CFDI</div>
          <div class="field">
            <select id="uso" name="uso">
              <option value="">Selecciona…</option>
              <option value="G01">G01 - Adquisición de mercancías</option>
              <option value="G03">G03 - Gastos en general</option>
              <option value="D01">D01 - Honorarios médicos</option>
              <option value="I01">I01 - Construcciones</option>
              <option value="CP01">CP01 - Pagos</option>
              <option value="S01">S01 - Sin efectos fiscales</option>
            </select>
            <span class="hint">La constancia normalmente <em>no</em> trae este dato; queda a elección del cliente.</span>
          </div>

          <div class="label">Correo electrónico</div>
          <div class="field"><input id="email" name="email" type="email" placeholder="cliente@correo.com"></div>

          <div class="label">Forma de pago</div>
          <div class="field">
            <select id="formaPago" name="formaPago">
              <option value="">Selecciona…</option>
              <option>01 - Efectivo</option>
              <option>03 - Transferencia electrónica</option>
              <option>04 - Tarjeta de crédito</option>
              <option>28 - Tarjeta de débito</option>
              <option>99 - Por definir</option>
            </select>
          </div>

          <div class="label">Método de pago</div>
          <div class="field">
            <select id="metodoPago" name="metodoPago">
              <option value="">Selecciona…</option>
              <option>PUE - Pago en una sola exhibición</option>
              <option>PPD - Pago en parcialidades o diferido</option>
            </select>
          </div>

          <div class="label">Subir constancia (PDF)</div>
          <div class="field">
            <div class="uploader">
              <label class="btn btn-ghost" for="pdfInput">Elegir archivo</label>
              <input id="pdfInput" type="file" accept="application/pdf" />
              <button class="btn btn-primary" type="button" id="parseBtn">Leer y autollenar</button>
              <span id="fileName" class="hint">Ningún archivo seleccionado</span>
            </div>
          </div>
        </div>

        <div class="footer">
          <button class="btn btn-ghost" type="reset">Limpiar</button>
          <button class="btn btn-primary" type="submit">Guardar datos</button>
        </div>
      </form>
    </div>
  </div>

<script>
  const el = (id)=>document.getElementById(id);
  const status = el('parseStatus');
  const pdfInput = el('pdfInput');
  const parseBtn = el('parseBtn');
  const fileName = el('fileName');

  pdfInput.addEventListener('change', ()=>{
    fileName.textContent = pdfInput.files[0] ? pdfInput.files[0].name : 'Ningún archivo seleccionado';
  });

  // Normaliza espacios y acentos para facilitar regex
  function normalizeText(s){
    return s.replace(/\r/g,'\n').replace(/\u00A0/g,' ').replace(/[\t ]+/g,' ').replace(/\n+/g,'\n');
  }

  // Intenta extraer bloques por etiquetas conocidas de la constancia
  function extractFields(txt){
    const out = {};

    // RFC
    const rfcRegex = /\b([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})\b/;
    const rfcMatch = txt.match(rfcRegex);
    if(rfcMatch) out.rfc = rfcMatch[1];

    // Nombre / razón social (varias variantes de etiqueta)
    let nameMatch = txt.match(/Nombre(?:,\s*denominación\s*o\s*razón\s*social)?:\s*([^\n]+)\n/i)
                 || txt.match(/Denominación\s*o\s*razón\s*social:\s*([^\n]+)\n/i)
                 || txt.match(/Nombre:\s*([^\n]+)\n/i);
    if(nameMatch) out.nombre = nameMatch[1].trim();

    // Domicilio fiscal: tomamos el bloque entre la etiqueta y la siguiente sección frecuente
    const domStart = txt.search(/Domicilio\s*fiscal/i);
    if(domStart !== -1){
      const slice = txt.slice(domStart);
      const endIdx = slice.search(/Régimen\s*fiscal|Correo\s*electrónico|Correo\s*electronico|Fecha\s*de\s*inicio/i);
      const block = (endIdx>0? slice.slice(0,endIdx): slice).split('\n').slice(1,10).join(' ').trim();
      if(block) out.domicilio = block.replace(/\s{2,}/g,' ');
    }

    // Correo electrónico (si lo trae)
    const mailMatch = txt.match(/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i);
    if(mailMatch) out.email = mailMatch[0];

    return out;
  }

  async function readPdfText(file){
    const data = await file.arrayBuffer();
    const pdf = await pdfjsLib.getDocument({data}).promise;
    let full = '';
    for(let p=1; p<=Math.min(pdf.numPages, 4); p++){ // primeras 4 páginas suelen bastar
      const page = await pdf.getPage(p);
      const content = await page.getTextContent();
      const strings = content.items.map(it=>it.str).join('\n');
      full += '\n' + strings;
    }
    return normalizeText(full);
  }

  parseBtn.addEventListener('click', async ()=>{
    if(!pdfInput.files[0]){
      status.textContent = 'Selecciona un PDF primero';
      status.className = 'pill status-bad';
      return;
    }
    status.textContent = 'Leyendo PDF…';
    status.className = 'pill';
    try{
      const text = await readPdfText(pdfInput.files[0]);
      const data = extractFields(text);

      if(data.nombre) el('nombre').value = data.nombre;
      if(data.rfc) el('rfc').value = data.rfc.toUpperCase();
      if(data.domicilio) el('domicilio').value = data.domicilio;
      if(data.email) el('email').value = data.email;

      status.textContent = 'Autollenado completado';
      status.className = 'pill status-ok';
    }catch(err){
      console.error(err);
      status.textContent = 'No se pudo leer el PDF';
      status.className = 'pill status-bad';
      alert('Ocurrió un problema leyendo el PDF. Verifica que sea una Constancia de Situación Fiscal legible por texto (no una imagen escaneada).');
    }
  });

  // Validación sencilla en envío
  document.getElementById('clienteForm').addEventListener('submit', (e)=>{
    e.preventDefault();
    const payload = {
      nombre: el('nombre').value.trim(),
      rfc: el('rfc').value.trim().toUpperCase(),
      domicilio: el('domicilio').value.trim(),
      uso: el('uso').value,
      email: el('email').value.trim(),
      formaPago: el('formaPago').value,
      metodoPago: el('metodoPago').value
    };
    console.log('Datos del cliente:', payload);
    alert('Datos capturados en consola. Integra aquí tu envío (fetch/POST) al backend que prefieras.');
  });
</script>
</body>
</html>
