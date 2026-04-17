<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  header("location: ../login");
  exit;
}

include("../../config/db.php");
include("../../config/conexion.php");

$id = (int)($_GET['id'] ?? 0);
if($id<=0){ die("Factura inválida"); }

$prods = mysqli_query($con, "SELECT id_producto, referencia, descripcion, precio_producto, id_almacen
  FROM products
  WHERE estatus=1
  ORDER BY descripcion ASC
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
                <div class="text-muted" style="font-size:12px;">Selecciona un producto y captura cantidad / precio.</div>
              </div>
              <a class="btn btn-outline-secondary" href="nueva_factura.php?id=<?php echo (int)$id; ?>">Volver</a>
            </div>

            <hr>

            <div class="table-responsive">
              <table class="table table-sm table-bordered table-hover">
                <thead style="background:#0d6efd;color:#fff;">
                  <tr>
                    <th>Ref</th>
                    <th>Descripción</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end" style="width:360px;">Agregar</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while($p=mysqli_fetch_assoc($prods)): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($p['referencia']); ?></td>
                      <td><?php echo htmlspecialchars($p['descripcion']); ?></td>
                      <td class="text-end">$ <?php echo number_format((float)$p['precio_producto'],2); ?></td>
                      <td>
                        <form method="post" action="../../ajax/agregar_item.php" class="d-flex gap-2 align-items-center justify-content-end">
                          <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
                          <input type="hidden" name="id_producto" value="<?php echo (int)$p['id_producto']; ?>">
                          <input type="hidden" name="id_almacen" value="<?php echo (int)$p['id_almacen']; ?>">
                          <input class="form-control form-control-sm" style="max-width:120px" name="cantidad" value="1" required>
                          <input class="form-control form-control-sm" style="max-width:140px" name="precio" value="<?php echo htmlspecialchars($p['precio_producto']); ?>" required>
                          <button class="btn btn-sm btn-primary">Agregar</button>
                        </form>
                      </td>
                    </tr>
                  <?php endwhile; ?>
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
