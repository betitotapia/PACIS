<?php
// traspaso_html.php
$color_primary = "#F26A21";
$color_dark    = "#1F2937";
$color_muted   = "#6B7280";
$color_border  = "#E5E7EB";

$folio         = $folio         ?? '';
$fecha_trasp   = $fecha_trasp   ?? '';
$usuario       = $usuario       ?? '';
$almacen_orig  = $almacen_orig  ?? '';
$almacen_dest  = $almacen_dest  ?? '';
$observaciones = $observaciones ?? '';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Traspaso entre Almacenes</title>
<style>
  :root {
    --primary: <?php echo $color_primary; ?>;
    --dark:    <?php echo $color_dark; ?>;
    --muted:   <?php echo $color_muted; ?>;
    --border:  <?php echo $color_border; ?>;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    padding: 20px;
    font-family: Arial, Helvetica, sans-serif;
    color: var(--dark);
    background: #fff;
  }
  .sheet { width: 100%; max-width: 920px; margin: 0 auto; }

  .header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px;
    border: 1px solid var(--border);
    border-radius: 12px;
  }
  .brand { display: flex; align-items: center; gap: 14px; }
  .brand img { height: 56px; width: auto; display: block; }
  .brand .tag { color: var(--muted); font-size: 12px; margin-top: 2px; }

  .docbox { text-align: right; min-width: 250px; }
  .doctype { font-size: 12px; color: var(--muted); letter-spacing: .12em; text-transform: uppercase; }
  .docno { font-size: 22px; font-weight: 900; margin-top: 4px; color: var(--primary); }

  .grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 12px; margin-top: 12px; }
  .card { border: 1px solid var(--border); border-radius: 12px; padding: 12px 14px; }
  .card h3 { margin: 0 0 8px 0; font-size: 12px; letter-spacing: .10em; text-transform: uppercase; color: var(--muted); }
  .kv { display: grid; grid-template-columns: 120px 1fr; row-gap: 6px; column-gap: 10px; font-size: 12px; }
  .k { color: var(--muted); }
  .v { color: var(--dark); font-weight: 600; }

  .tablewrap { margin-top: 12px; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
  table { width: 100%; border-collapse: collapse; }
  thead th {
    background: var(--dark);
    color: #fff;
    font-size: 11px;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: 10px;
    text-align: left;
  }
  tbody td { border-top: 1px solid var(--border); padding: 9px 10px; font-size: 12px; vertical-align: top; }
  .right { text-align: right; }

  .totals {
    display: flex;
    justify-content: flex-end;
    padding: 10px 12px;
    gap: 16px;
    border-top: 1px solid var(--border);
  }
  .totals .label { color: var(--muted); font-size: 12px; font-weight: 700; text-transform: uppercase; }
  .totals .value { font-size: 16px; font-weight: 900; color: var(--primary); }

  .footer { margin-top: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .sign {
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px;
    height: 110px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    color: var(--muted);
    font-weight: 700;
    font-size: 12px;
  }
  .line { width: 100%; border-top: 1px solid var(--border); padding-top: 8px; text-align: center; }

  @media print { body { padding: 0; } .sheet { max-width: none; } }
</style>
</head>
<body>
<div class="sheet">

  <div class="header">
    <div class="brand">
      <img src="../img/opacis_logo.png" alt="PACIS">
      <div class="tag">Traspaso entre Almacenes</div>
    </div>
    <div class="docbox">
      <div class="doctype">Traspaso</div>
      <div class="docno">TRA <?php echo htmlspecialchars($folio); ?></div>
    </div>
  </div>

  <div class="grid">
    <div class="card">
      <h3>Datos del traspaso</h3>
      <div class="kv">
        <div class="k">Folio</div>     <div class="v"><?php echo htmlspecialchars($folio); ?></div>
        <div class="k">Fecha</div>     <div class="v"><?php echo htmlspecialchars($fecha_trasp); ?></div>
        <div class="k">Usuario</div>   <div class="v"><?php echo htmlspecialchars($usuario); ?></div>
        <div class="k">Observ.</div>   <div class="v"><?php echo $observaciones ? htmlspecialchars($observaciones) : "<span style='color:var(--muted);font-weight:600;'>—</span>"; ?></div>
      </div>
    </div>
    <div class="card">
      <h3>Movimiento</h3>
      <div class="kv">
        <div class="k">Origen</div>  <div class="v"><?php echo htmlspecialchars($almacen_orig); ?></div>
        <div class="k">Destino</div> <div class="v"><?php echo htmlspecialchars($almacen_dest); ?></div>
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
          <th style="width:11%">Caducidad</th>
          <th class="right" style="width:10%">Cantidad</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $total_piezas = 0;
        while ($d = mysqli_fetch_assoc($sql_det)) {
            $total_piezas += (float)$d['cantidad'];
      ?>
        <tr>
          <td><?php echo htmlspecialchars($d['referencia']); ?></td>
          <td><?php echo htmlspecialchars($d['descripcion']); ?></td>
          <td><?php echo htmlspecialchars($d['lote']); ?></td>
          <td><?php echo htmlspecialchars($d['caducidad'] ?? ''); ?></td>
          <td class="right"><?php echo number_format((float)$d['cantidad'], 2); ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>

    <div class="totals">
      <div class="label">Total piezas</div>
      <div class="value"><?php echo number_format($total_piezas, 2); ?></div>
    </div>
  </div>

  <div class="footer">
    <div class="sign"><div class="line">Entrega (Origen)</div></div>
    <div class="sign"><div class="line">Recibe (Destino)</div></div>
  </div>

</div>
</body>
</html>
