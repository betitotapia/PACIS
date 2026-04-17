<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: ../login");
    exit;
}
$active_productos="";
    $active_lista_productos="";
	$active_borrador="";
	$active_nueva="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
	$active_almacenes="";
	$active_usuarios="";
	$active_terceros="";
	$active_provedores='';
	$active_recepciones="";
    $active_ordenes_compra="active";

require_once("../../config/db.php");
require_once("../../config/conexion.php");


?>
<?php include("../header.php"); ?>  <!-- o como manejes tu layout -->
<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info  bg-body-tertiary">
<div class="app-wrapper">
<?php
include '../navbar.php';
include("../aside_menu.php");
?>
<main class="app-main">

<div class="app-content-header">
  <div class="container-fluid">
    <h4><i class="fa fa-plus-circle"></i> Nueva Orden de Compra</h4>
  </div>
</div>

<div class="app-content">
<div class="container-fluid">

<div class="card card-primary card-outline">
<div class="card-body">

<form id="form_oc">
<div class="row">

  <div class="col-md-3">
    <label>Fecha</label>
    <input type="date" name="fecha_oc" class="form-control" value="<?php echo date('Y-m-d'); ?>">
  </div>

  <div class="col-md-4">
    <label>Proveedor</label>
    <select name="id_proveedor" class="form-control" required>
      <option value="">Seleccione</option>
      <?php
      $q = mysqli_query($con, "SELECT * FROM proveedores ORDER BY nombre_provedor");
      while($r = mysqli_fetch_assoc($q)){
        echo "<option value='{$r['id_proveedor']}'>{$r['nombre_provedor']}</option>";
      }
      ?>
    </select>
  </div>

  <div class="col-md-3">
    <label>Almacén</label>
    <select name="id_almacen" class="form-control" required>
      <option value="">Seleccione</option>
      <?php
      $q = mysqli_query($con, "SELECT * FROM almacenes ORDER BY numero_almacen");
      while($r = mysqli_fetch_assoc($q)){
        echo "<option value='{$r['id_almacen']}'>{$r['numero_almacen']} - {$r['descripcion']}</option>";
      }
      ?>
    </select>
  </div>

</div>

<div class="row mt-2">
  <div class="col-md-12">
    <label>Observaciones</label>
    <textarea name="observaciones" class="form-control"></textarea>
  </div>
</div>
</form>

<hr>

<!-- Partidas -->
<div class="row">
    
  <div class="col-md-3">
    <label for="referencia">Referencia</label>
    <input type="text" id="ref_tmp_oc" class="form-control" placeholder="Referencia">
  </div>
  <div class="col-md-4">
    <label for="descripcion">Descripción</label>
    <input type="text" id="desc_tmp_oc" class="form-control" placeholder="Descripción">
  </div>
  <div class="col-md-2">
    <label for="cantidad">Cantidad</label>
    <input type="number" id="cant_tmp_oc" class="form-control" value="1" placeholder="Cantidad">
  </div>
  <div class="col-md-2">
    <label for="costo">Costo</label>
    <input type="number" id="costo_tmp_oc" class="form-control" value="0" placeholder="Costo">
  </div>
  <div class="col-md-1">
    <button class="btn btn-success" onclick="agregar_item_oc();">
      <i class="fa fa-plus">Agregar</i>
    </button>
  </div>
</div>

<div class="row mt-3">
  <div class="col-md-12" id="resultado_tmp_oc"></div>
</div>

<div class="row mt-3">
  <div class="col-md-12 text-right">
    <button class="btn btn-primary" onclick="guardar_oc();">
      <i class="fa fa-save"></i> Guardar OC
    </button>
    <a href="index.php" class="btn btn-default">Cancelar</a>
  </div>
</div>

</div>
</div>

</div>
</div>

</main>
</div>


</body>

<script src="../../js/orden_compra.js"></script>
 <script type="text/javascript" src="../../js/VentanaCentrada.js"></script> 
<?php include("../footer.php"); ?>
</html>
