<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  header("location: ../login");
  exit;
}

$active_recepciones="active";
include("../../config/db.php");
include("../../config/conexion.php");

$id_oc_get   = isset($_GET['id_oc']) ? (int)$_GET['id_oc'] : 0;
$id_prov_get = isset($_GET['id_proveedor']) ? (int)$_GET['id_proveedor'] : 0;
?>
<?php include("../header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info bg-body-tertiary">
<div class="app-wrapper">
<?php include '../navbar.php'; include '../aside_menu.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Recepción de productos</h1>
  </section>

  <section class="content">
    <form id="form_recepcion" method="post">

      <div class="row">
        <div class="col-md-3">
          <label>Fecha</label>
          <input type="date" class="form-control" name="fecha_recepcion"
                 value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="col-md-3">
          <label>Proveedor</label>
          <select name="id_proveedor" id="id_proveedor" class="form-control" required>
            <option value="">Seleccione</option>
            <?php
              $q_prov = mysqli_query($con, "SELECT id_proveedor, nombre_provedor FROM proveedores ORDER BY nombre_provedor");
              while($rw = mysqli_fetch_assoc($q_prov)){
                $sel = ($id_prov_get > 0 && (int)$rw['id_proveedor'] === $id_prov_get) ? "selected" : "";
                echo '<option value="'.$rw['id_proveedor'].'" '.$sel.'>'.htmlspecialchars($rw['nombre_provedor']).'</option>';
              }
            ?>
          </select>
        </div>

        <div class="col-md-3">
          <label>Orden de compra</label>
          <select name="id_oc" id="id_oc" class="form-control">
            <option value="">(Opcional) Seleccione OC</option>
          </select>
          <small class="text-muted">OCs ABIERTA / PARCIAL del proveedor.</small>
        </div>

        <div class="col-md-3">
          <label>Almacén</label>
          <select name="id_almacen" id="id_almacen_hdr" class="form-control" required>
            <option value="">Seleccione</option>
            <?php
              $q_alma = mysqli_query($con, "SELECT id_almacen, numero_almacen, descripcion FROM almacenes ORDER BY numero_almacen");
              while($rw = mysqli_fetch_assoc($q_alma)){
                echo '<option value="'.$rw['id_almacen'].'">'.$rw['numero_almacen'].' - '.htmlspecialchars($rw['descripcion']).'</option>';
              }
            ?>
          </select>
        </div>
      </div>

      <div class="row" style="margin-top:10px;">
        <div class="col-md-12">
          <label>Observaciones</label>
          <textarea name="observaciones" class="form-control" rows="2"></textarea>
        </div>
      </div>

      <hr>

      <!-- =======================
           AGREGAR POR CÓDIGO DE BARRAS (GS1)
           ======================= -->
      <div class="row">
        <div class="col-md-6">
          <label>Código de barras (GS1)</label>
          <input type="text" id="codigo_gs1" class="form-control" placeholder="Escanea aquí y presiona Agregar">
        </div>
        <div class="col-md-2" style="margin-top:25px;">
          <button type="button" class="btn btn-success" onclick="agregar_por_codigo_barras();">
            <i class="fa fa-barcode"></i> Agregar
          </button>
        </div>
        <div class="col-md-4">
          <small class="text-muted">
            Si hay OC seleccionada: completa lote/caducidad del renglón pendiente.<br>
            Si no hay OC: agrega renglón nuevo (o pide referencia si falta).
          </small>
        </div>
      </div>

      <hr>

      <!-- Agregado manual (se conserva) -->
      <div class="row">
        <div class="col-md-2">
          <label>Referencia</label>
          <input type="text" id="ref_tmp" class="form-control">
        </div>
        <div class="col-md-4">
          <label>Descripción</label>
          <input type="text" id="desc_tmp" class="form-control">
        </div>
        <div class="col-md-2">
          <label>Lote</label>
          <input type="text" id="lote_tmp" class="form-control">
        </div>
        <div class="col-md-2">
          <label>Caducidad</label>
          <input type="date" id="cad_tmp" class="form-control">
        </div>
        <div class="col-md-1">
          <label>Cant.</label>
          <input type="number" id="cant_tmp" class="form-control" min="1" value="1">
        </div>
        <div class="col-md-1" style="margin-top:25px;">
          <button type="button" class="btn btn-primary" onclick="agregar_item_recepcion();">
            <i class="fa fa-plus">Agregar</i>
          </button>
        </div>
      </div>

      <div class="row" style="margin-top:10px;">
        <div class="col-md-3">
          <label>Almacén destino (renglón)</label>
          <select id="almacen_tmp" class="form-control">
            <option value="">Seleccione</option>
            <?php
              $q_alma2 = mysqli_query($con, "SELECT id_almacen, numero_almacen, descripcion FROM almacenes ORDER BY numero_almacen");
              while($rw = mysqli_fetch_assoc($q_alma2)){
                echo '<option value="'.$rw['id_almacen'].'">'.$rw['numero_almacen'].' - '.htmlspecialchars($rw['descripcion']).'</option>';
              }
            ?>
          </select>
        </div>
      </div>

      <hr>

      <div id="resultado_recepcion">
        <?php include("../../ajax/tabla_tmp_recepcion.php"); ?>
      </div>

      <div class="row" style="margin-top:20px;">
        <div class="col-md-12 text-right">
          <button type="button" class="btn btn-success" onclick="guardar_recepcion();">
            <i class="fa fa-save"></i> Guardar recepción
          </button>
        </div>
      </div>

    </form>
  </section>
</div>

</div>

<!-- =======================
     MODAL NUEVO PRODUCTO (cuando GS1 no trae referencia o no existe)
     ======================= -->
<div class="modal fade" id="modalNuevoProducto" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-plus"></i> Nuevo Producto</h4>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="np_codigo_gs1">
        <div class="row">
          <div class="col-md-4">
            <label>Referencia</label>
            <input type="text" id="np_referencia" class="form-control">
          </div>
          <div class="col-md-8">
            <label>Descripción</label>
            <input type="text" id="np_descripcion" class="form-control">
          </div>
        </div>

        <div class="row" style="margin-top:10px;">
          <div class="col-md-3">
            <label>Lote (detectado)</label>
            <input type="text" id="np_lote" class="form-control" readonly>
          </div>
          <div class="col-md-3">
            <label>Caducidad (detectada)</label>
            <input type="date" id="np_caducidad" class="form-control" readonly>
          </div>
          <div class="col-md-3">
            <label>Cantidad</label>
            <input type="number" id="np_cantidad" class="form-control" min="1" value="1">
          </div>
          <div class="col-md-3">
            <label>Costo</label>
            <input type="number" id="np_costo" class="form-control" min="0" step="0.01" value="0">
          </div>
        </div>
        <div class="row" style="margin-top:10px;">
  <div class="col-md-6">
    <label>
      <input type="checkbox" id="np_exento" value="1"> Exento IVA
    </label>
  </div>
</div>

        <div class="row" style="margin-top:10px;">
          <div class="col-md-6">
            <label>Almacén destino</label>
            <select id="np_id_almacen" class="form-control">
              <option value="">Seleccione</option>
              <?php
                $q_alma3 = mysqli_query($con, "SELECT id_almacen, numero_almacen, descripcion FROM almacenes ORDER BY numero_almacen");
                while($rw = mysqli_fetch_assoc($q_alma3)){
                  echo '<option value="'.$rw['id_almacen'].'">'.$rw['numero_almacen'].' - '.htmlspecialchars($rw['descripcion']).'</option>';
                }
              ?>
            </select>
          </div>
          <div class="col-md-6">
            <small class="text-muted">

          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardar_nuevo_producto_desde_gs1();">
          Guardar y agregar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  window.__OC_GET = <?php echo (int)$id_oc_get; ?>;
</script>

<script src="../../js/recepcion.js"></script>
<script src="../../js/recepcion_codigos.js"></script>
<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
<?php include("../footer.php"); ?>
</body>
</html>
