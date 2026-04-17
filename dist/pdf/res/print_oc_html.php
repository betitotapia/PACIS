<?php
// print_oc_html.php
// Espera: $oc (array) y $det (mysqli result)
$color_primary = "#F26A21"; // Naranja OPACIS
$color_dark    = "#1F2937"; // Gris oscuro
$color_muted   = "#6B7280"; // Gris medio
$color_border  = "#E5E7EB"; // Gris claro
$color_bg      = "#F8FAFC"; // Fondo suave

$folio   = $oc['folio_oc'] ?? '';
$fecha   = isset($oc['fecha_oc']) ? date("d/m/Y H:i", strtotime($oc['fecha_oc'])) : '';
$prov    = $oc['proveedor'] ?? '';
$almacen = ($oc['numero_almacen'] ?? '')." - ".($oc['almacen_desc'] ?? '');
$usuario = $oc['usuario'] ?? '';
$estatus = $oc['estatus'] ?? '';
$obs     = $oc['observaciones'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Orden de compra</title>

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
  .brand{
    display:flex;
    align-items:center;
    gap:14px;
  }
  .brand img{
    height:56px;
    width:auto;
    display:block;
  }
  .brand .meta{
    line-height:1.2;
  }
  .brand .meta .name{
    font-weight:700;
    font-size:15px;
    letter-spacing:.2px;
  }
  .brand .meta .tag{
    color:var(--muted);
    font-size:12px;
    margin-top:2px;
  }
  .docbox{
    text-align:right;
    min-width: 250px;
  }
  .doctype{
    font-size:12px;
    color:var(--muted);
    letter-spacing:.12em;
    text-transform:uppercase;
  }
  .docno{
    font-size:22px;
    font-weight:800;
    margin-top:4px;
    color:var(--primary);
  }
  .status{
    display:inline-block;
    margin-top:8px;
    padding:6px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    border:1px solid var(--border);
    color:var(--dark);
    background: var(--bg);
  }

  .grid{
    display:grid;
    grid-template-columns: 1.2fr 1fr;
    gap:12px;
    margin-top:12px;
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
  table{
    width:100%;
    border-collapse:collapse;
  }
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
  .muted{ color:var(--muted); font-weight:600; }
  .totals{
    display:flex;
    justify-content:flex-end;
    padding:10px 12px;
    gap:16px;
    background: #fff;
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

  .printnote{
    margin-top:10px;
    color:var(--muted);
    font-size:11px;
    text-align:center;
  }

  @media print{
    body{ padding:0; }
    .sheet{ max-width: none; }
  }
</style>
</head>

<body>
<div id="contenedor-oc" class="sheet">

  <div class="header">
    <div class="brand">
      <img src="../img/opacis_logo.png" alt="OPACIS">
      <div class="meta">
        
        <div class="tag">Orden de compra / Abastecimiento</div>
      </div>
    </div>

    <div class="docbox">
      <div class="doctype">Orden de compra</div>
      <div class="docno">OC <?php echo htmlspecialchars($folio); ?></div>
      <div class="status"><?php echo htmlspecialchars($estatus); ?></div>
    </div>
  </div>

  <div class="grid">
    <div class="card">
      <h3>Datos del proveedor</h3>
      <div class="kv">
        <div class="k">Proveedor</div><div class="v"><?php echo htmlspecialchars($prov); ?></div>
        <div class="k">Fecha</div><div class="v"><?php echo htmlspecialchars($fecha); ?></div>
        <div class="k">Solicita</div><div class="v"><?php echo htmlspecialchars($usuario); ?></div>
      </div>
    </div>

    <div class="card">
      <h3>Destino</h3>
      <div class="kv">
        <div class="k">Almacén</div><div class="v"><?php echo htmlspecialchars($almacen); ?></div>
        <div class="k">Referencia</div><div class="v muted">Según detalle</div>
        <div class="k">Observ.</div><div class="v"><?php echo $obs ? htmlspecialchars($obs) : "<span class='muted'>—</span>"; ?></div>
      </div>
    </div>
  </div>

  <div class="tablewrap">
    <table>
      <thead>
        <tr>
          <th style="width:16%">Referencia</th>
          <th>Descripción</th>
          <th class="right" style="width:10%">Cant. Sol.</th>
          <th class="right" style="width:10%">Cant. Rec.</th>
          <th class="right" style="width:12%">Costo</th>
          <th class="right" style="width:12%">Importe</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $total = 0;
          while($d = mysqli_fetch_assoc($det)){
            $total += (float)$d['importe'];
        ?>
        <tr>
          <td><?php echo htmlspecialchars($d['referencia']); ?></td>
          <td><?php echo htmlspecialchars($d['descripcion']); ?></td>
          <td class="right"><?php echo number_format((float)$d['cantidad_solicitada'], 2); ?></td>
          <td class="right"><?php echo number_format((float)$d['cantidad_recibida'], 2); ?></td>
          <td class="right"><?php echo number_format((float)$d['costo_unitario'], 4); ?></td>
          <td class="right"><?php echo number_format((float)$d['importe'], 2); ?></td>
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
    <div class="sign"><div class="line">Solicita</div></div>
    <div class="sign"><div class="line">Autoriza</div></div>
  </div>

  <div class="printnote">
    Documento interno generado por el sistema PACIS 
</div>
</body>
</html>
