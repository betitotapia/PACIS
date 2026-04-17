<?php
// dist/pages/facturacion/index.php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  header("location: ../login");
  exit;
}

include("../../config/db.php");
include("../../config/conexion.php");

// Listado (últimas 300)
$q = mysqli_query($con, "SELECT ff.*, c.nombre_cliente, c.rfc
  FROM fact_facturas ff
  LEFT JOIN clientes c ON c.id_cliente = ff.id_cliente
  ORDER BY ff.id_fact_facturas DESC
  LIMIT 300
");

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

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                <h4 class="m-0">Facturación CFDI</h4>
                <div class="text-muted" style="font-size:12px;">Borradores, timbradas y canceladas.</div>
              </div>
              <a class="btn btn-primary" href="nueva_factura.php">Nueva factura</a>
            </div>

            <hr>

            <div class="table-responsive">
              <table class="table table-sm table-bordered table-hover align-middle">
                <thead style="background:#0d6efd;color:#fff;">
                  <tr>
                    <th style="width:70px;">ID</th>
                    <th style="width:140px;">Serie/Folio</th>
                    <th>Cliente</th>
                    <th style="width:140px;" class="text-end">Total</th>
                    <th style="width:110px;">Método</th>
                    <th style="width:120px;">Estatus</th>
                    <th style="width:280px;">UUID</th>
                    <th style="width:160px;" class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                <?php while($r = mysqli_fetch_assoc($q)): ?>
                  <?php
                    $id = (int)$r['id_fact_facturas'];
                    $serie = trim((string)($r['serie'] ?? ''));
                    $folio = trim((string)($r['folio'] ?? ''));
                    $metodo = trim((string)($r['metodo_pago'] ?? ''));
                    $uuid = (string)($r['uuid'] ?? '');
                    $status = (int)($r['status_factura'] ?? 0);

                    // total preferente: total (nuevo) o total_factura (viejo)
                    $total = $r['total_factura'];
                    if($total === null || $total === '' || (float)$total == 0){
                      $total = $r['total_factura'] ?? 0;
                    }

                    // Etiqueta estatus
                    $badge = 'secondary';
                    $label = 'Borrador';
                    if($status === 1){ $badge='info'; $label='Validada'; }
                    if($status === 2){ $badge='success'; $label='Timbrada'; }
                    if($status === 3){ $badge='danger'; $label='Error'; }
                    if($status === 4){ $badge='dark'; $label='Cancelada'; }
                  ?>
                  <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo htmlspecialchars(trim($serie.' '.$folio)); ?></td>
                    <td>
                      <div class="fw-semibold"><?php echo htmlspecialchars($r['nombre_cliente'] ?? ''); ?></div>
                      <div class="text-muted" style="font-size:12px;"><?php echo htmlspecialchars($r['rfc'] ?? ''); ?></div>
                    </td>
                    <td class="text-end">$ <?php echo number_format((float)$total, 2); ?></td>
                    <td><?php echo htmlspecialchars($metodo ?: '—'); ?></td>
                    <td><span class="badge bg-<?php echo $badge; ?>"><?php echo $label; ?></span></td>
                    <td style="font-size:12px;"><?php echo htmlspecialchars($uuid); ?></td>
                    <td class="text-center">
                      <a class="btn btn-sm btn-outline-primary" href="nueva_factura.php?id=<?php echo $id; ?>">Abrir</a>
                      <?php if($status === 2 && $metodo === 'PPD'): ?>
                        <a class="btn btn-sm btn-outline-success" href="pagos.php?id=<?php echo $id; ?>">Pagos</a>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
                </tbody>
              </table>
            </div>

            <div class="text-muted mt-2" style="font-size:12px;">
              Estatus: 0=Borrador, 1=Validada, 2=Timbrada, 3=Error, 4=Cancelada.
            </div>

          </div>
        </div>

      </div>
    </div>
  </main>
</div>

<?php include("../footer.php"); ?>
</body>
</html>
