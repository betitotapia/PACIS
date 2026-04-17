<?php
session_start();

$active_vehiculos="active";
$active_nueva="";
$active_remisiones="";
$active_cancel="";
	
$user_id=$_SESSION["user_id"];
require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos

?>

<!DOCTYPE html>
<html lang="en">
<?php
include '../header.php';
?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
    <?php
		include '../navbar.php';
		include '../aside_menu.php';
    ?>
   <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
		<div class="container-fluid">
		    <div class="btn-group pull-right">
				 <?php
		$session_id2 = $_SESSION["user_id"];
		$sql_usuario1=mysqli_query($con,"SELECT * FROM users WHERE user_id ='$session_id2'");
        $rj_usuario1=mysqli_fetch_array($sql_usuario1);
		echo "<script>console.log('work evaluando:".$session_id2."');</script>";	
		?>		 
			<div>
				
			 <?php 
			//  if ($rj_usuario1['is_admin'] != 3 && $rj_usuario1['is_admin'] != 4){
			// echo	
			// 	"<a  href='nueva_factura.php' class='btn btn-info'><span class='glyphicon glyphicon-plus' ></span> Nueva Remisión</a>";
			// }
			?> 
	</div>
				<h4  style='display:inline-block'>Buscar vehiculos</h4>
			</div>
			<div style="width:20%;">
				<!-- <a  href='nueva_factura.php' class='btn btn-info'  style='display:inline-block;margin-left:0%;'>Nuevo Vehículo</a> -->
				<button type="button" class="btn btn-info botones_cel" data-toggle="modal" data-target="#myModal">
						 <span class="glyphicon glyphicon-search"></span> Nuevo Vehículo </button>
			</div>
		</div>	
			<div class="panel-body">
			<?php
			include "../modal/registro_vehiculo.php"
			?>
		
				<form class="form-horizontal" role="form" id="datos_cotizacion" >
				
						<div class="form-group row">
							<!--<label for="q" class="col-md-2 control-label">Cliente o # de Remisión</label>-->
							<div class="col-md-5">
								<input type="hidden" class="form-control" id="q" placeholder="Nombre del cliente o # de Remisión" onkeyup='load(1);'>
							</div>
							
							<div class='col-md-3'>
								<!--<button type='button' class='btn btn-default' onclick='load(1);'>-->
									<!--<span class='glyphicon glyphicon-search' ></span> Buscar</button>-->
								<span id='loader'></span>
							</div>
							
						</div> 
				
				
				
			</form>
				<div id="resultados"></div><!-- Carga los datos ajax -->
				<div class='outer_div'></div><!-- Carga los datos ajax -->
			</div>
		</div>	
		
	</div>
	
	<?php
//nclude("footer.php");
	?>
	


</body>
</html>
<script type="text/javascript" src="../../js/vehiculos.js"></script>
	<script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
      crossorigin="anonymous"
    ></script> 
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="../../js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
<script>

const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });


		 
	</script>