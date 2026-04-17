<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  header("location: ../login");
  exit;
}

include("../../config/db.php");
include("../../config/conexion.php");

$id = (int)($_GET['id'] ?? 0);
$id_remision = (int)($_GET['id_remision'] ?? 0);

if ($id <= 0) {
  // Crear borrador y redirigir
  // OJO: ajusta el nombre del consecutivo si en tu tabla se llama diferente
  $id_vendedor = (int)($_SESSION['user_id'] ?? 0);

  // Si tienes tabla consecutivos, usa tu lógica real:
  // $no = (int)get_row('consecutivos','factura','id_consecutivo',1);

  $no = 0; // si no manejas consecutivo aquí, déjalo 0 o calcula como uses en tu sistema

  if ($id == 0 && $id_remision > 0) {
  $ex = mysqli_query($con, "SELECT id_fact_facturas
                            FROM fact_facturas
                            WHERE id_remision = ".$id_remision."
                            ORDER BY id_fact_facturas DESC
                            LIMIT 1");
  if ($e = mysqli_fetch_assoc($ex)) {
    header("Location: nueva_factura.php?id=".(int)$e['id_fact_facturas']);
    exit;
  }
}
  $sql = "INSERT INTO fact_facturas (id_remision, no_fact_factura, id_cliente, id_vendedor, total_factura, status_factura, validacion, date_created)
          VALUES (".($id_remision > 0 ? $id_remision : "NULL").", $no, 0, $id_vendedor, 0, 0, 0, NOW())";
  mysqli_query($con, $sql);

  $id = (int)mysqli_insert_id($con);

  // Si vienes de remisión y quieres copiar conceptos:
  if ($id_remision > 0) {
    // AJUSTA: esto depende de cómo guardas tu remisión (facturas/detalle_factura)
    mysqli_query($con, "UPDATE fact_facturas ff
      JOIN facturas f ON f.numero_factura=$id_remision
      SET ff.id_cliente = f.id_cliente
      WHERE ff.numero_fact_factura=$id");

    $dq = mysqli_query($con, "SELECT id_producto, cantidad, precio_venta, id_almacen, id_vendedor
      FROM detalle_factura
      WHERE numero_factura=$id_remision");

    while ($d = mysqli_fetch_assoc($dq)) {
      $pid = (int)$d['id_producto'];

      // Inserta en tu tabla de detalle de borrador
      mysqli_query($con, "INSERT INTO detalle_fact_factura
        (numero_fact_factura, id_producto, cantidad, precio_venta, id_almacen, id_vendedor, date_created)
        VALUES
        ($id, $pid, ".(float)$d['cantidad'].", ".(float)$d['precio_venta'].", ".(int)$d['id_almacen'].", ".(int)$d['id_vendedor'].", NOW())");
    }
  }

  header("Location: nueva_factura.php?id=".$id);
  exit;
}

$ff = mysqli_query($con, "SELECT ff.*, c.nombre_cliente, c.rfc
  FROM fact_facturas ff
  LEFT JOIN clientes c ON c.id_cliente=ff.id_cliente
  WHERE ff.id_fact_facturas=$id LIMIT 1");
$fact = mysqli_fetch_assoc($ff);
if(!$fact){ die("Factura inválida"); }

$clientes = mysqli_query($con, "SELECT id_cliente, nombre_cliente, rfc FROM clientes ORDER BY nombre_cliente");

$items = mysqli_query($con, "SELECT df.*, p.referencia, p.descripcion
  FROM detalle_fact_factura df
  LEFT JOIN products p ON p.id_producto=df.id_producto
  WHERE df.numero_fact_factura=$id
  ORDER BY df.id_detalle_fact ASC");

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
                <h4 class="m-0">Factura CFDI (borrador) #<?php echo (int)$id; ?></h4>
                <div class="text-muted">Status: <?php echo (int)($fact['status_factura'] ?? 0); ?>
                  <?php if(!empty($fact['uuid'])): ?>
                    — UUID: <span style="font-size:12px"><?php echo htmlspecialchars($fact['uuid']); ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="index.php">Volver</a>
                <button class="btn btn-success" id="btnTimbrar">Timbrar CFDI</button>
              </div>
            </div>

            <hr>

            <div class="card mb-3">
              <div class="card-header"><strong>Datos del receptor (cliente)</strong></div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Cliente</label>
                    <select class="form-select" id="id_cliente">
                      <option value="0">Selecciona…</option>
                      <?php while($c = mysqli_fetch_assoc($clientes)): ?>
                        <option value="<?php echo (int)$c['id_cliente']; ?>" <?php echo ((int)$c['id_cliente']==(int)($fact['id_cliente']??0))?'selected':''; ?>>
                          <?php echo htmlspecialchars($c['nombre_cliente']." (".$c['rfc'].")"); ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Método de pago</label>
                    <select class="form-select" id="metodo_pago">
                      <option value="PUE" <?php echo (($fact['metodo_pago']??'PUE')==='PUE')?'selected':''; ?>>PUE</option>
                      <option value="PPD" <?php echo (($fact['metodo_pago']??'PUE')==='PPD')?'selected':''; ?>>PPD</option>
                    </select>
                    <div class="form-text">Si es PPD, luego podrás timbrar Complementos de Pago.</div>
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Forma de pago (si PUE)</label>
                    <input class="form-control" id="forma_pago" placeholder="Ej: 03" value="<?php echo htmlspecialchars($fact['forma_pago'] ?? '03'); ?>">
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Uso CFDI</label>
                    <input class="form-control" id="uso_cfdi" placeholder="Ej: G03" value="<?php echo htmlspecialchars($fact['uso_cfdi'] ?? 'G03'); ?>">
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Serie</label>
                    <input class="form-control" id="serie" value="<?php echo htmlspecialchars($fact['serie'] ?? 'A'); ?>">
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Folio</label>
                    <input class="form-control" id="folio" value="<?php echo htmlspecialchars($fact['folio'] ?? ''); ?>">
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Moneda</label>
                    <input class="form-control" id="moneda" value="<?php echo htmlspecialchars($fact['moneda'] ?? 'MXN'); ?>">
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Tipo cambio</label>
                    <input class="form-control" id="tipo_cambio" value="<?php echo htmlspecialchars($fact['tipo_cambio'] ?? '1'); ?>">
                  </div>
                </div>

                <div class="mt-3">
                  <button class="btn btn-primary" id="btnGuardarHeader">Guardar datos</button>
                </div>
              </div>
            </div>

            <div class="card mb-3">
              <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <strong>Conceptos</strong>
                <a class="btn btn-sm btn-outline-primary" href="seleccionar_producto.php?id=<?php echo (int)$id; ?>">Agregar producto</a>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-sm table-bordered m-0">
                    <thead style="background:#0d6efd;color:#fff;">
                      <tr>
                        <th>Referencia</th>
                        <th>Descripción</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Importe</th>
                        <th style="width:60px;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $sum = 0.0;
                        while($it = mysqli_fetch_assoc($items)):
                          $cant = (float)($it['cantidad'] ?? 0);
                          $precio = (float)($it['precio_venta'] ?? 0);
                          $imp = $cant * $precio;
                          $sum += $imp;
                      ?>
                      <tr>
                        <td><?php echo htmlspecialchars($it['referencia'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($it['descripcion'] ?? ''); ?></td>
                        <td class="text-end"><?php echo number_format($cant,2); ?></td>
                        <td class="text-end"><?php echo number_format($precio,2); ?></td>
                        <td class="text-end"><?php echo number_format($imp,2); ?></td>
                        <td class="text-center">
                          <button class="btn btn-sm btn-outline-danger btnDel" data-id="<?php echo (int)$it['id_detalle_fact']; ?>" title="Eliminar">X</button>
                        </td>
                      </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="card-footer">
                <?php
                  $subtotal = (float)($fact['subtotal'] ?? 0);
                  $iva = (float)($fact['iva'] ?? 0);
                  $total = (float)($fact['total'] ?? 0);

                  if($subtotal <= 0){ $subtotal = $sum; }
                  if($iva <= 0){ $iva = round($subtotal * 0.16, 2); }
                  if($total <= 0){ $total = $subtotal + $iva; }
                ?>
                <div class="d-flex justify-content-end">
                  <table class="table table-sm table-bordered" style="max-width:360px;margin:0;">
                    <tr>
                      <th class="text-end" style="width:45%;">Subtotal</th>
                      <td class="text-end">$ <?php echo number_format($subtotal,2); ?></td>
                    </tr>
                    <tr>
                      <th class="text-end">IVA</th>
                      <td class="text-end">$ <?php echo number_format($iva,2); ?></td>
                    </tr>
                    <tr>
                      <th class="text-end">Total</th>
                      <td class="text-end"><strong>$ <?php echo number_format($total,2); ?></strong></td>
                    </tr>
                  </table>
                </div>
                <div class="text-muted mt-2" style="font-size:12px;">
                  Nota: para evitar diferencias, recomiendo calcular y guardar subtotal/iva/total en BD al momento de guardar/timbrar.
                </div>
              </div>
            </div>

            <?php if((int)($fact['status_factura'] ?? 0) === 2 && !empty($fact['uuid']) && ($fact['metodo_pago'] ?? '') === 'PPD'): ?>
              <div class="alert alert-info">
                Esta factura está timbrada como <strong>PPD</strong>. Puedes generar el <strong>Complemento de Pago</strong> desde:
                <a href="pagos.php?id=<?php echo (int)$id; ?>">Pagos / Complementos</a>
              </div>
            <?php endif; ?>

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

document.getElementById('btnGuardarHeader').addEventListener('click', async ()=>{
  const res = await post('../../ajax/guardar_factura_header.php', {
    id: <?php echo (int)$id; ?>,
    id_cliente: document.getElementById('id_cliente').value,
    metodo_pago: document.getElementById('metodo_pago').value,
    forma_pago: document.getElementById('forma_pago').value,
    uso_cfdi: document.getElementById('uso_cfdi').value,
    serie: document.getElementById('serie').value,
    folio: document.getElementById('folio').value,
    moneda: document.getElementById('moneda').value,
    tipo_cambio: document.getElementById('tipo_cambio').value
  });

  alert(res.ok ? 'Guardado' : ('Error: '+(res.error||'')));
});

document.getElementById('btnTimbrar').addEventListener('click', async ()=>{
  if(!confirm('¿Timbrar CFDI ahora?')) return;
  const res = await post('../../ajax/timbrar_factura.php', { id: <?php echo (int)$id; ?> });
  if(res.ok){
    alert('Timbrada. UUID: '+res.uuid);
    location.href = 'nueva_factura.php?id=<?php echo (int)$id; ?>';
  } else {
    alert('Error timbrado: ' + (res.error || ''));
  }
});

document.querySelectorAll('.btnDel').forEach(btn=>{
  btn.addEventListener('click', async ()=>{
    if(!confirm('¿Eliminar concepto?')) return;
    const res = await post('../../ajax/eliminar_item.php', { id_detalle: btn.dataset.id });
    if(res.ok) location.reload();
    else alert('Error: '+(res.error||''));
  });
});
</script>
</body>
</html>
