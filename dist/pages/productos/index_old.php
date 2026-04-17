<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
  $active_productos="active";
  $active_lista_productos="";
  $active_borrador="";
  $active_cancel="";
	$active_nueva="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
  $active_almacenes="";
	
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos

?>
<!DOCTYPE html>
<html lang="es">
<?php
include '../header.php';
?>
<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info  bg-body-tertiary">
<div class="app-wrapper">
<?php
include '../navbar.php';
include '../aside_menu.php';
    ?>
    <main class="app-main" >
        <!--begin::App Content Header-->
        <div class="app-content-header">
		<div class="container-fluid">
   
    <div class="d-flex gap-3 mt-3">
      <input type="radio" class="btn-check" name="operacion" id="sumar" value="sumar" autocomplete="off" checked>
      <label class="btn btn-success btn-lg" for="sumar"><i class="bi bi-plus-circle"></i> SUMAR</label>

      <input type="radio" class="btn-check" name="operacion" id="restar" value="restar" autocomplete="off">
      <label class="btn btn-danger btn-lg" for="restar"><i class="bi bi-dash-circle"></i> RESTAR</label>

      <input type="radio" class="btn-check" name="operacion" id="ajuste" value="ajuste" autocomplete="off">
  <label class="btn btn-warning btn-lg" for="ajuste"><i class="bi bi-gear"></i>AJUSTE</label>
</div>
    </div>
    <br>
	<div class="input-group mb-3">
	<select class="form-control" id="id_almacen"style="width:100%;">
                        <option selected="selected" value="">Almacen</option>
                    <?php $sql_almacen="SELECT * FROM almacenes"; $query= mysqli_query($con, $sql_almacen);
                    while($row_alm=mysqli_fetch_array($query))
                            {
                            ?>
                            <option value='<?php echo $clave=$row_alm['id_almacen'];?>'><?php echo $row_alm['id_almacen']."--".$desc_almacen=$row_alm['descripcion'];?></option>
                            <?php
                            }
                    ?>		
                            </select>
	</div>
    
    <div class="input-group mb-3">
	
<br>
      <span class="input-group-text" id="basic-addon1"><i class="bi bi-upc-scan"></i></span>
      <input type="text" class="form-control" id="codigo" placeholder="ESCANEA EL CÓDIGO DE BARRAS" autofocus aria-label="Código de barras">
      </div>
      <br>
      <div id="mensaje" class="alert mt-2" role="alert" style="display: none;"></div>
    
    
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Productos en Inventario</h3>
      </div>
      <div class="card-body p-0">
	  <div id="resultados"></div><!-- Carga los datos ajax -->
	  <div class='outer_div'></div><!-- Carga los datos ajax -->



      </div>
    </div>
  </div>
  </main>
</div>
  <?php
   include '../footer.php'; ?>
  <script src="../../js/script_add_product.js"></script>
</body>
</html>