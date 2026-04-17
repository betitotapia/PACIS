<?php
// print_recepcion_html.php
$color_primary = "#F26A21";
$color_dark    = "#1F2937";
$color_muted   = "#6B7280";
$color_border  = "#E5E7EB";
$color_bg      = "#F8FAFC";

// Ajusta estas variables según tu recepcion_pdf.php
$folio         = $folio ?? ($rw_enc['folio'] ?? '');
$fecha_rec     = $fecha_rec ?? (isset($rw_enc['fecha_recepcion']) ? date("d/m/Y H:i", strtotime($rw_enc['fecha_recepcion'])) : '');
$proveedor     = $proveedor ?? ($rw_enc['nombre_proveedor'] ?? $rw_enc['proveedor'] ?? '');
$almacen       = $almacen ?? (($rw_enc['numero_almacen'] ?? '')." - ".($rw_enc['nombre_almacen'] ?? $rw_enc['almacen_desc'] ?? ''));
$usuario       = $usuario ?? ($rw_enc['nombre_usuario'] ?? $rw_enc['usuario'] ?? '');
$observaciones = $observaciones ?? ($rw_enc['observaciones'] ?? '');
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Acuse de recepción</title>

<style>
  :root{
    --primary: <?php echo $color_primary; ?>;
    --dark: <?php echo $color_dark; ?>;
    --muted: <?php echo $color_muted; ?>;
    --border: <?php echo $color_border; ?>;
    --bg: <?php echo $color_bg; ?>;
  }
  *{ box-sizing:border-box; }
  body{
    margin:0;
    padding:20px;
    font-family: Arial, Helvetica, sans-serif;
    color: var(--dark);
    background:#fff;
  }
  .sheet{
    width:100%;
    max-width: 920px;
    margin:0 auto;
  }
  .header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    padding:16px 16px 12px 16px;
    border:1px solid var(--border);
    border-radius:12px;
  }
  .brand{ display:flex; align-items:center; gap:14px; }
  .brand img{ height:56px; width:auto; display:block; }
  .brand .meta .name{ font-weight:700; font-size:15px; }
  .brand .meta .tag{ color:var(--muted); font-size:12px; margin-top:2px; }

  .docbox{ text-align:right; min-width:250px; }
  .doctype{
    font-size:12px; color:var(--muted);
    letter-spacing:.12em; text-transform:uppercase;
  }
  .docno{ font-size:22px; font-weight:900; margin-top:4px; color:var(--primary); }

  .grid{
    display:grid; grid-template-columns: 1.2fr 1fr;
    gap:12px; margin-top:12px;
  }
  .card{
    border:1px solid var(--border);
    border-radius:12px;
    padding:12px 14px;
  }
  .card h3{
    margin:0 0 8px 0;
    font-size:12px;
    letter-spacing:.10em;
    text-transform:uppercase;
    color:var(--muted);
  }
  .kv{
    display:grid;
    grid-template-columns: 130px 1fr;
    row-gap:6px;
    column-gap:10px;
    font-size:12px;
  }
  .k{ color:var(--muted); }
  .v{ color:var(--dark); font-weight:600; }

  .tablewrap{
    margin-top:12px;
    border:1px solid var(--border);
    border-radius:12px;
    overflow:hidden;
  }
  table{ width:100%; border-collapse:collapse; }
  thead th{
    background: var(--dark);
    color:#fff;
    font-size:11px;
    letter-spacing:.08em;
    text-transform:uppercase;
    padding:10px 10px;
    text-align:left;
  }
  tbody td{
    border-top:1px solid var(--border);
    padding:9px 10px;
    font-size:12px;
    vertical-align:top;
  }
  .right{ text-align:right; }
  .totals{
    display:flex;
    justify-content:flex-end;
    padding:10px 12px;
    gap:16px;
    background:#fff;
    border-top:1px solid var(--border);
  }
  .totals .label{
    color:var(--muted);
    font-size:12px;
    font-weight:700;
    letter-spacing:.06em;
    text-transform:uppercase;
  }
  .totals .value{
    font-size:16px;
    font-weight:900;
    color:var(--primary);
  }

  .footer{
    margin-top:12px;
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:12px;
  }
  .sign{
    border:1px solid var(--border);
    border-radius:12px;
    padding:14px;
    height:110px;
    display:flex;
    align-items:flex-end;
    justify-content:center;
    color:var(--muted);
    font-weight:700;
    font-size:12px;
  }
  .line{
    width:100%;
    border-top:1px solid var(--border);
    padding-top:8px;
    text-align:center;
  }

  @media print{ body{ padding:0; } .sheet{ max-width:none; } }
</style>
</head>

<body>
<div id="contenedor-recepcion" class="sheet">

  <div class="header">
    <div class="brand">
      <img src="../img/opacis_logo.png" alt="OPACIS">
      <div class="meta">
       
        <div class="tag">Acuse de recepción</div>
      </div>
    </div>

    <div class="docbox">
      <div class="doctype">Recepción</div>
      <div class="docno">REC <?php echo htmlspecialchars($folio); ?></div>
    </div>
  </div>

  <div class="grid">
    <div class="card">
      <h3>Datos de la recepción</h3>
      <div class="kv">
        <div class="k">Fecha</div><div class="v"><?php echo htmlspecialchars($fecha_rec); ?></div>
        <div class="k">Proveedor</div><div class="v"><?php echo htmlspecialchars($proveedor); ?></div>
        <div class="k">Usuario</div><div class="v"><?php echo htmlspecialchars($usuario); ?></div>
      </div>
    </div>

    <div class="card">
      <h3>Destino</h3>
      <div class="kv">
        <div class="k">Almacén</div><div class="v"><?php echo htmlspecialchars($almacen); ?></div>
        <div class="k">Observ.</div><div class="v"><?php echo $observaciones ? htmlspecialchars($observaciones) : "<span style='color:var(--muted);font-weight:600;'>—</span>"; ?></div>
      </div>
    </div>
  </div>

  <div class="tablewrap">
    <table>
      <thead>
        <tr>
          <th style="width:16%">Referencia</th>
          <th>Descripción</th>
          <th style="width:14%">Lote</th>
          <th style="width:12%">Caducidad</th>
          <th class="right" style="width:10%">Cant.</th>
          <th class="right" style="width:12%">Costo</th>
          <th class="right" style="width:12%">Importe</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $total = 0;
        while($d = mysqli_fetch_assoc($sql_det)){
          $importe = (float)$d['cantidad'] * (float)$d['costo_unitario'];
          $total += $importe;
      ?>
        <tr>
          <td><?php echo htmlspecialchars($d['referencia']); ?></td>
          <td><?php echo htmlspecialchars($d['descripcion']); ?></td>
          <td><?php echo htmlspecialchars($d['lote']); ?></td>
          <td><?php echo htmlspecialchars($d['caducidad']); ?></td>
          <td class="right"><?php echo number_format((float)$d['cantidad'], 2); ?></td>
          <td class="right"><?php echo number_format((float)$d['costo_unitario'], 4); ?></td>
          <td class="right"><?php echo number_format($importe, 2); ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>

    <div class="totals">
      <div class="label">Total</div>
      <div class="value">$ <?php echo number_format($total, 2); ?></div>
    </div>
  </div>

  <div class="footer">
    <div class="sign"><div class="line">Entrega</div></div>
    <div class="sign"><div class="line">Recibe</div></div>
  </div>

</div>
</body>
</html>
