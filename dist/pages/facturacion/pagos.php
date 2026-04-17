<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  header("location: ../login");
  exit;
}

include("../../config/db.php");
include("../../config/conexion.php");

$id = (int)($_GET['id'] ?? 0);

$ff = mysqli_query($con, "SELECT ff.*, c.nombre_cliente
  FROM fact_facturas ff
  LEFT JOIN clientes c ON c.id_cliente=ff.id_cliente
  WHERE ff.id_fact_facturas=$id LIMIT 1");
$fact = mysqli_fetch_assoc($ff);

if(!$fact){ die("Factura inválida"); }
if((int)$fact['status_factura'] !== 2 || empty($fact['uuid'])){ die("La factura debe estar timbrada para crear complemento de pago."); }

$pagos = mysqli_query($con, "SELECT * FROM fact_pagos WHERE id_fact_facturas=$id ORDER BY id_pago DESC");

include("../header.php");
?>
<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info bg-body-tertiary">
<div class="app-wrapper">
  <?php include("../navbar.php"); ?>
  <?php include("../aside_menu.php"); ?>

  <main class="app-main">
    <div class="app-content">
      <div class="container-fluid">

        <div class="card card-primary card-outline">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
              <div>
                <h4 class="m-0">Pagos (Complemento) — Factura #<?php echo (int)$id; ?></h4>
                <div class="text-muted" style="font-size:12px;">
                  <?php echo htmlspecialchars($fact['nombre_cliente']??''); ?> — UUID:
                  <span style="font-size:12px"><?php echo htmlspecialchars($fact['uuid']); ?></span>
                </div>
              </div>
              <a class="btn btn-outline-secondary" href="nueva_factura.php?id=<?php echo (int)$id; ?>">Volver</a>
            </div>

            <hr>

            <div class="card mb-3">
              <div class="card-header"><strong>Registrar pago</strong></div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-3">
                    <label class="form-label">Fecha pago</label>
                    <input class="form-control" id="fecha" value="<?php echo date('Y-m-d\TH:i'); ?>" type="datetime-local">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Monto</label>
                    <input class="form-control" id="monto" value="" placeholder="0.00">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Forma de pago</label>
                    <input class="form-control" id="forma_pago" value="03" placeholder="Ej: 03">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Moneda</label>
                    <input class="form-control" id="moneda" value="MXN">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Tipo cambio</label>
                    <input class="form-control" id="tipo_cambio" value="1">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">No. Operación (opcional)</label>
                    <input class="form-control" id="num_operacion" value="">
                  </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                  <button class="btn btn-primary" id="btnGuardarPago">Guardar pago</button>
                  <button class="btn btn-success" id="btnTimbrarPago">Timbrar complemento</button>
                </div>

                <div class="text-muted mt-2" style="font-size:12px;">
                  Tip: registra el pago (monto/fecha) y luego timbra el complemento. Puedes timbrar tantos complementos como pagos existan.
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><strong>Pagos registrados</strong></div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-sm table-bordered m-0">
                    <thead style="background:#0d6efd;color:#fff;">
                      <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th class="text-end">Monto</th>
                        <th>Forma</th>
                        <th>UUID complemento</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while($p=mysqli_fetch_assoc($pagos)): ?>
                        <tr>
                          <td><?php echo (int)$p['id_pago']; ?></td>
                          <td><?php echo htmlspecialchars($p['fecha_pago'] ?? ''); ?></td>
                          <td class="text-end"><?php echo number_format((float)($p['monto'] ?? 0),2); ?></td>
                          <td><?php echo htmlspecialchars($p['forma_pago'] ?? ''); ?></td>
                          <td style="font-size:12px;"><?php echo htmlspecialchars($p['uuid_pago'] ?? ''); ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </main>
</div>

<?php include("../footer.php"); ?>

<script>
async function post(url, data){
  const r = await fetch(url, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams(data)
  });
  return await r.json();
}

document.getElementById('btnGuardarPago').addEventListener('click', async ()=>{
  const res = await post('../../ajax/guardar_pago.php', {
    id_fact: <?php echo (int)$id; ?>,
    fecha: document.getElementById('fecha').value,
    monto: document.getElementById('monto').value,
    forma_pago: document.getElementById('forma_pago').value,
    moneda: document.getElementById('moneda').value,
    tipo_cambio: document.getElementById('tipo_cambio').value,
    num_operacion: document.getElementById('num_operacion').value
  });

  if(res.ok){
    alert('Pago guardado');
    location.reload();
  } else {
    alert('Error: ' + (res.error||''));
  }
});

document.getElementById('btnTimbrarPago').addEventListener('click', async ()=>{
  if(!confirm('¿Timbrar el complemento para el último pago guardado (sin UUID)?')) return;
  const res = await post('../../ajax/timbrar_pago.php', { id_fact: <?php echo (int)$id; ?> });
  if(res.ok){
    alert('Complemento timbrado. UUID: ' + (res.uuid||''));
    location.reload();
  } else {
    alert('Error timbrado: ' + (res.error||''));
  }
});
</script>
</body>
</html>
