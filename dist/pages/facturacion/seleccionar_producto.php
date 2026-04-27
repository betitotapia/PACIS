<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  header("location: ../login");
  exit;
}

include("../../config/db.php");
include("../../config/conexion.php");

$id = (int)($_GET['id'] ?? 0);
$q = trim((string)($_GET['q'] ?? ''));
if($id<=0){ die("Factura inválida"); }

$qSql = mysqli_real_escape_string($con, $q);
$whereSearch = '';
if ($qSql !== '') {
  $whereSearch = " AND (referencia LIKE '%$qSql%' OR cve_alterna_1 LIKE '%$qSql%' OR cve_alterna_2 LIKE '%$qSql%' OR descripcion LIKE '%$qSql%')";
}

$prods = mysqli_query($con, "SELECT id_producto, referencia, descripcion, precio_producto, id_almacen, lote, caducidad, existencias
  FROM products
  WHERE estatus=1 AND existencias > 0$whereSearch
  ORDER BY referencia ASC, lote ASC
  LIMIT 2000");

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
                <h4 class="m-0">Agregar producto a Factura #<?php echo (int)$id; ?></h4>
                <div class="text-muted" style="font-size:12px;">Busca por referencia y elige el lote disponible para facturar.</div>
              </div>
              <a class="btn btn-outline-secondary" href="nueva_factura.php?id=<?php echo (int)$id; ?>">Volver</a>
            </div>

            <hr>

            <form method="get" class="row g-2 mb-3">
              <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
              <div class="col-md-8">
                <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Buscar por referencia o descripción">
              </div>
              <div class="col-md-4 d-grid d-md-flex gap-2">
                <button class="btn btn-primary" type="submit">Buscar</button>
                <?php if ($q !== ''): ?>
                  <a class="btn btn-outline-secondary" href="seleccionar_producto.php?id=<?php echo (int)$id; ?>">Limpiar</a>
                <?php endif; ?>
              </div>
            </form>

            <div class="table-responsive">
              <table class="table table-sm table-bordered table-hover">
                <thead style="background:#0d6efd;color:#fff;">
                  <tr>
                    <th>Ref</th>
                    <th>Lote</th>
                    <th>Caducidad</th>
                    <th>Descripción</th>
                    <th class="text-end">Exist.</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end" style="width:360px;">Agregar</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $found = false; while($p=mysqli_fetch_assoc($prods)): $found = true; ?>
                    <?php
                      $caducidad = !empty($p['caducidad']) ? date('d/m/Y', strtotime($p['caducidad'])) : '—';
                    ?>
                    <tr>
                      <td><?php echo htmlspecialchars($p['referencia']); ?></td>
                      <td><?php echo htmlspecialchars($p['lote'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($caducidad); ?></td>
                      <td><?php echo htmlspecialchars($p['descripcion']); ?></td>
                      <td class="text-end"><?php echo number_format((float)($p['existencias'] ?? 0), 2); ?></td>
                      <td class="text-end">$ <?php echo number_format((float)$p['precio_producto'],2); ?></td>
                      <td>
                        <form method="post" action="../../ajax/agregar_item.php" class="d-flex gap-2 align-items-center justify-content-end">
                          <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
                          <input type="hidden" name="id_producto" value="<?php echo (int)$p['id_producto']; ?>">
                          <input type="hidden" name="id_almacen" value="<?php echo (int)$p['id_almacen']; ?>">
                          <input type="hidden" name="lote" value="<?php echo htmlspecialchars($p['lote'] ?? ''); ?>">
                          <input type="hidden" name="caducidad" value="<?php echo htmlspecialchars($p['caducidad'] ?? ''); ?>">
                          <input type="hidden" name="referencia" value="<?php echo htmlspecialchars($p['referencia'] ?? ''); ?>">
                          <input class="form-control form-control-sm" style="max-width:120px" name="cantidad" value="1" required>
                          <input class="form-control form-control-sm" style="max-width:140px" name="precio" value="<?php echo htmlspecialchars($p['precio_producto']); ?>" required>
                          <button class="btn btn-sm btn-primary">Agregar</button>
                        </form>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                  <?php if (!$found): ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted py-4">No se encontraron productos con ese filtro.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
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
