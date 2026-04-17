<?php
// dist/pdf/res/print_remision_oc_html.php
// Plantilla "estilo OC" para Remisión (misma estética: sheet + cards + tabla)

// Helpers seguros
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Traer datos del vendedor y cliente (si aplica)
$nombre_vendedor = $nombre_vendedor ?? '';
if(empty($nombre_vendedor) && !empty($id_vendedor)){
  $qv = mysqli_query($con, "SELECT nombre, letra FROM users WHERE user_id=".(int)$id_vendedor." LIMIT 1");
  if($rv = mysqli_fetch_assoc($qv)){
    $nombre_vendedor = $rv['nombre'] ?? '';
    $letra_ventas = $letra_ventas ?? ($rv['letra'] ?? '');
  }
}

// Cliente (opcional)
$cliente_nombre = $cliente_nombre ?? '';
$cliente_doc    = $cliente_doc ?? '';
$cliente_tel    = $cliente_tel ?? '';
if(!empty($id_cliente)){
  $qc = mysqli_query($con, "SELECT * FROM clientes WHERE id_cliente=".(int)$id_cliente." LIMIT 1");
  if($rc = mysqli_fetch_assoc($qc)){
    $cliente_nombre = $rc['nombre_cliente'] ?? '';
    $cliente_doc    = $rc['documento'] ?? '';
    $cliente_tel    = $rc['telefono_cliente'] ?? '';
  }
}

// Folio
$folio = "P".($letra_ventas ?? '')."-".(int)$numero_factura;

// Detalle
$det = mysqli_query($con, "
  SELECT
    f.id_detalle, f.numero_factura, f.id_producto, f.cantidad, f.precio_venta, f.iva,
    p.referencia, p.descripcion, p.lote, p.caducidad
  FROM detalle_factura f
  INNER JOIN products p ON f.id_producto = p.id_producto
  WHERE f.numero_factura = '".mysqli_real_escape_string($con, (string)$numero_factura)."'
    AND f.id_vendedor = '".mysqli_real_escape_string($con, (string)$id_vendedor)."'
  ORDER BY f.id_detalle ASC
");

// Totales
$sumador_total = 0;
$total_iva_acum = 0;

?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Remisión <?php echo h($folio); ?></title>

<style>
:root{
  --primary:#cb2a0a;
  --bg:#f3f5f7;
  --text:#111827;
  --muted:#6b7280;
  --border:#e5e7eb;
}
*{ box-sizing:border-box; }
body{
  margin:0;
  padding:18px;
  background:var(--bg);
  color:var(--text);
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial, "Apple Color Emoji","Segoe UI Emoji";
}
.sheet{
  max-width: 980px;
  margin:0 auto;
  background:#fff;
  border:1px solid var(--border);
  border-radius:16px;
  overflow:hidden;
}
.header{
  display:flex;
  justify-content:space-between;
  gap:12px;
  padding:16px 18px;
  border-bottom:1px solid var(--border);
}
.brand{
  display:flex;
  gap:12px;
  align-items:center;
}
.brand img{
  height:40px;
  width:auto;
}
.tag{
  display:inline-block;
  font-size:12px;
  font-weight:800;
  letter-spacing:.08em;
  text-transform:uppercase;
  color:var(--muted);
}
.docbox{
  text-align:right;
}
.doctype{
  font-weight:900;
  font-size:14px;
  color:var(--muted);
  text-transform:uppercase;
  letter-spacing:.08em;
}
.docno{
  font-weight:1000;
  font-size:22px;
  color:#F26A21;
  margin-top:2px;
}
.status{
  display:inline-block;
  margin-top:6px;
  padding:6px 10px;
  border-radius:999px;
  font-size:12px;
  font-weight:800;
  background:#fff3ef;
  color:var(--primary);
  border:1px solid #ffd3c6;
}
.grid{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap:12px;
  padding:14px 18px 10px;
}
.card{
  border:1px solid var(--border);
  border-radius:14px;
  padding:12px 12px;
}
.card h3{
  margin:0 0 10px 0;
  font-size:12px;
  color:var(--muted);
  letter-spacing:.08em;
  text-transform:uppercase;
}
.kv{
  display:grid;
  grid-template-columns: 110px 1fr;
  gap:8px 10px;
  font-size:13px;
}
.k{ color:var(--muted); font-weight:700; }
.v{ color:var(--text); font-weight:700; }
.v.muted{ color:var(--muted); font-weight:700; }
.tablewrap{
  padding:0 18px 12px;
}
table{
  width:100%;
  border-collapse:collapse;
  border:1px solid var(--border);
  border-radius:14px;
  overflow:hidden;
}
thead th{
  text-align:left;
  font-size:12px;
  color:#ffffff;
  letter-spacing:.06em;
  text-transform:uppercase;
  padding:10px 10px;
  border-bottom:1px solid var(--border);
  background:#1F2937; /* azul tipo OC */
}
tbody td{
  padding:9px 10px;
  border-bottom:1px solid var(--border);
  font-size:13px;
  vertical-align:top;
}
tbody tr:last-child td{ border-bottom:none; }
.right{ text-align:right; }
.totals{
  display:flex;
  justify-content:flex-end;
  padding:10px 18px 16px;
  border-top:1px solid var(--border);
}

.totalsBox{
  width: 280px;
  border:1px solid var(--border);
  border-radius:14px;
  padding:12px 12px;
  background:#fafafa;
}

.trow{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:10px;
  padding:6px 0;
  border-bottom:1px dashed var(--border);
}

.trow:last-child{ border-bottom:none; }

.tlabel{
  color:#F26A21;
  font-size:12px;
  font-weight:800;
  letter-spacing:.06em;
  text-transform:uppercase;
}

.tvalue{
  font-size:14px;
  font-weight:900;
  color:var(--text);
}

.trow.total .tlabel{ color:#F26A21; }
.trow.total .tvalue{
  font-size:18px;
  font-weight:1000;
  color:#F26A21;
}
.footer{
  padding:0 18px 18px;
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap:12px;
}
.sign{
  border:1px solid var(--border);
  border-radius:12px;
  height:105px;
  display:flex;
  align-items:flex-end;
  justify-content:center;
  padding:12px;
  color:var(--muted);
  font-weight:800;
  font-size:12px;
}
.line{ width:100%; border-top:1px solid var(--border); padding-top:8px; text-align:center; }

@media print{
  body{ padding:0; background:#fff; }
  .sheet{ max-width:none; border:none; border-radius:0; }
}
</style>
</head>

<body>
<div id="contenedor-remision" class="sheet">

  <div class="header">
    <div class="brand">
      <img src="../assets/img/<?php echo h(get_row('perfil','logo_url','id_perfil',1)); ?>" alt="Logo">
      <div>
        <div class="tag">Remisión / Salida</div>
      </div>
    </div>

    <div class="docbox">
      <div class="doctype">Remisión</div>
      <div class="docno"><?php echo h($folio); ?></div>
      <div class="status"><?php echo h("Generada"); ?></div>
    </div>
  </div>

  <div class="grid">
    <div class="card">
      <h3>Datos generales</h3>
      <div class="kv">
        <div class="k">Fecha</div><div class="v"><?php echo h($fecha ?? ''); ?></div>
        <div class="k">Vendedor</div><div class="v"><?php echo h($nombre_vendedor ?: '—'); ?></div>
        <div class="k">Compra</div><div class="v"><?php echo $compra ? h($compra) : "<span class='v muted'>—</span>"; ?></div>
        <div class="k">Cotización</div><div class="v"><?php echo $cotizacion ? h($cotizacion) : "<span class='v muted'>—</span>"; ?></div>
      </div>
    </div>

    <div class="card">
      <h3>Paciente / Hospital</h3>
      <div class="kv">
        <div class="k">Hospital</div><div class="v"><?php echo $hospital ? h($hospital) : "<span class='v muted'>—</span>"; ?></div>
        <div class="k">Doctor</div><div class="v"><?php echo $doctor ? h($doctor) : "<span class='v muted'>—</span>"; ?></div>
        <div class="k">Paciente</div><div class="v"><?php echo $paciente ? h($paciente) : "<span class='v muted'>—</span>"; ?></div>
        <div class="k">Pago</div><div class="v"><?php echo $pago ? h($pago) : "<span class='v muted'>—</span>"; ?></div>
      </div>
    </div>

    <div class="card">
      <h3>Cliente</h3>
      <div class="kv">
        <div class="k">Nombre</div><div class="v"><?php echo $cliente_nombre ? h($cliente_nombre) : "<span class='v muted'>—</span>"; ?></div>
        <div class="k">Doc</div><div class="v"><?php echo $cliente_doc ? h($cliente_doc) : "<span class='v muted'>—</span>"; ?></div>
        <div class="k">Tel</div><div class="v"><?php echo $cliente_tel ? h($cliente_tel) : "<span class='v muted'>—</span>"; ?></div>
      </div>
    </div>

    <div class="card">
      <h3>Observaciones</h3>
      <div class="kv" style="grid-template-columns: 110px 1fr;">
        <div class="k">Nota</div>
        <div class="v"><?php echo $observaciones ? h($observaciones) : "<span class='v muted'>—</span>"; ?></div>
      </div>
    </div>
  </div>

  <div class="tablewrap">
    <table>
      <thead>
        <tr>
          <th style="width:16%">Referencia</th>
          <th>Descripción</th>
          <th style="width:12%">Lote</th>
          <th style="width:12%">Caducidad</th>
          <th class="right" style="width:10%">Cant.</th>
          <th class="right" style="width:12%">Precio</th>
          <th class="right" style="width:12%">Importe</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($det)): ?>
          <?php
            $cantidad = (float)$row['cantidad'];
            $precio   = (float)$row['precio_venta'];
            $importe  = $cantidad * $precio;
            $sumador_total += $importe;

            // IVA: en tu detalle, iva=1 parece significar “NO” en la plantilla vieja (lo invierte).
            // Aquí lo tratamos como: si iva==1 => sin IVA (0), si iva!=1 => con IVA 16.
            $aplica_iva = ((int)$row['iva'] == 1) ? 0 : 1;
            if($aplica_iva){
              $total_iva_acum += ($importe * 0.16);
            }
          ?>
          <tr>
            <td><?php echo h($row['referencia']); ?><br>021354</td>
            <td><?php echo h($row['descripcion']); ?></td>
            <td><?php echo h($row['lote']); ?></td>
            <td><?php echo h($row['caducidad']); ?></td>
            <td class="right"><?php echo number_format($cantidad, 2); ?></td>
            <td class="right"><?php echo number_format($precio, 2); ?></td>
            <td class="right"><?php echo number_format($importe, 2); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <?php
    $subtotal = $sumador_total;
    $total = $subtotal + $total_iva_acum;
  ?>
 <div class="totals">
  <div class="totalsBox">
    <div class="trow">
      <div class="tlabel">Subtotal</div>
      <div class="tvalue">$ <?php echo number_format($subtotal, 2); ?></div>
    </div>

    <div class="trow">
      <div class="tlabel">IVA</div>
      <div class="tvalue">$ <?php echo number_format($total_iva_acum, 2); ?></div>
    </div>

    <div class="trow total">
      <div class="tlabel">Total</div>
      <div class="tvalue">$ <?php echo number_format($total, 2); ?></div>
    </div>
  </div>
</div>

  <div class="footer">
    <div class="sign"><div class="line">Entrega</div></div>
    <div class="sign"><div class="line">Recibe</div></div>
  </div>

</div>
</body>
</html>