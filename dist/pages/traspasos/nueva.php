<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: ../login");
    exit;
}

require_once("../../config/db.php");
require_once("../../config/conexion.php");

// Datos del usuario actual
$id_usuario_actual = (int)$_SESSION['user_id'];
$q_usr = mysqli_query($con, "SELECT nombre FROM users WHERE user_id = $id_usuario_actual LIMIT 1");
$nombre_usuario = '';
if ($q_usr && $rw_usr = mysqli_fetch_assoc($q_usr)) {
    $nombre_usuario = $rw_usr['nombre'];
}

// Limpiar tmp de sesiones anteriores (solo las más viejas de 2 horas para no afectar sesiones paralelas)
// Se limpia automáticamente al guardar el traspaso.

include("../header.php");
?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info bg-body-tertiary">
<div class="app-wrapper">
<?php include '../navbar.php'; include '../aside_menu.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1><i class="bi bi-arrow-left-right"></i> Nuevo Traspaso entre Almacenes</h1>
  </section>

  <section class="content">
    <form id="form_traspaso" method="post">

      <div class="row">
        <!-- Fecha -->
        <div class="col-md-2">
          <label>Fecha</label>
          <input type="date" class="form-control" name="fecha_traspaso"
                 id="fecha_traspaso" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <!-- Almacén Origen -->
        <div class="col-md-3">
          <label>Almacén Origen</label>
          <select name="id_almacen_origen" id="id_almacen_origen" class="form-control" required>
            <option value="">Seleccione</option>
            <?php
              $q_alma = mysqli_query($con, "SELECT id_almacen, numero_almacen, descripcion FROM almacenes ORDER BY numero_almacen");
              while ($rw = mysqli_fetch_assoc($q_alma)) {
                echo '<option value="' . $rw['id_almacen'] . '">'
                    . $rw['numero_almacen'] . ' - ' . htmlspecialchars($rw['descripcion'])
                    . '</option>';
              }
            ?>
          </select>
        </div>

        <!-- Almacén Destino -->
        <div class="col-md-3">
          <label>Almacén Destino</label>
          <select name="id_almacen_destino" id="id_almacen_destino" class="form-control" required>
            <option value="">Seleccione</option>
            <?php
              $q_alma2 = mysqli_query($con, "SELECT id_almacen, numero_almacen, descripcion FROM almacenes ORDER BY numero_almacen");
              while ($rw = mysqli_fetch_assoc($q_alma2)) {
                echo '<option value="' . $rw['id_almacen'] . '">'
                    . $rw['numero_almacen'] . ' - ' . htmlspecialchars($rw['descripcion'])
                    . '</option>';
              }
            ?>
          </select>
        </div>

        <!-- Usuario (solo lectura) -->
        <div class="col-md-2">
          <label>Usuario</label>
          <input type="text" class="form-control" value="<?php echo htmlspecialchars($nombre_usuario); ?>" readonly>
        </div>
      </div>

      <div class="row" style="margin-top:10px;">
        <div class="col-md-12">
          <label>Observaciones</label>
          <textarea name="observaciones" class="form-control" rows="2" placeholder="(Opcional)"></textarea>
        </div>
      </div>

      <hr>

      <!-- ── BUSCAR PRODUCTOS EN ALMACÉN ORIGEN ── -->
      <div class="row align-items-end">
        <div class="col-md-4">
          <label>Buscar producto por referencia</label>
          <input type="text" id="ref_buscar" class="form-control"
                 placeholder="Clave / referencia del producto" autocomplete="off">
        </div>
        <div class="col-md-2" style="padding-top:24px;">
          <button type="button" class="btn btn-info" onclick="buscar_producto_almacen();">
            <i class="fa fa-search"></i> Buscar
          </button>
        </div>
        <div class="col-md-6" style="padding-top:28px;">
          <small class="text-muted">
            Muestra los lotes disponibles en el almacén origen. Coloca la cantidad y presiona <strong>Agregar</strong> en cada lote deseado.
          </small>
        </div>
      </div>

      <!-- Resultados de búsqueda -->
      <div id="panel_busqueda" style="display:none; margin-top:12px;">
        <div class="card card-outline card-info">
          <div class="card-header py-2">
            <h6 class="card-title mb-0"><i class="fa fa-list"></i> Lotes disponibles en almacén origen</h6>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-bordered table-sm mb-0">
                <thead class="table-dark">
                  <tr>
                    <th>Referencia</th>
                    <th>Descripción</th>
                    <th>Lote</th>
                    <th>Caducidad</th>
                    <th class="text-right">Existencia</th>
                    <th style="width:110px;">Cantidad</th>
                    <th style="width:90px;"></th>
                  </tr>
                </thead>
                <tbody id="tbody_busqueda">
                  <tr><td colspan="7" class="text-center text-muted">Ingresa una referencia y presiona Buscar.</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <hr>

      <!-- Tabla temporal -->
      <div id="resultado_traspaso">
        <?php include("../../ajax/tabla_tmp_traspaso.php"); ?>
      </div>

      <div class="row" style="margin-top:20px;">
        <div class="col-md-12 text-right">
          <a href="index.php" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Cancelar
          </a>
          &nbsp;
          <button type="button" class="btn btn-success" onclick="guardar_traspaso();">
            <i class="fa fa-exchange"></i> Realizar Traspaso
          </button>
        </div>
      </div>

    </form>
  </section>
</div>

</div>

<script src="../../js/traspaso.js"></script>
<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
<?php include("../footer.php"); ?>
</body>
</html>
