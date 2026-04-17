<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
    $active_productos="active";
	$active_nueva="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
	
require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos

?>
<!DOCTYPE html>
<html lang="en">
<?php
include '../header.php';
?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info  bg-body-tertiary">
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
				
			</div>
			<h4><i class='glyphicon glyphicon-search' onkeyup='load(1);'></i>Productos</h4>
			<div><i class='glyphicon glyphicon-user' ></i>
			<?php //echo $rw_usuario['nombre']; ?></i></div>
		</div>
			<div class="panel-body">
			<?php
			include("../modal/estado_factura.php");
			include("../modal/bloqueo_remision_normal.php");
			?>
		
				<form class="form-horizontal" role="form" id="datos_cotizacion" >
				
						<div class="form-group row">
							<!--<label for="q" class="col-md-2 control-label">Cliente o # de Remisión</label>-->
							<div class="col-md-5">
								<input type="hidden" class="form-control" id="q" placeholder="Nombre del cliente o # de Remisión" onkeyup='load(1);'>
							</div>
							
							
							<div class="spinner-border text-primary"  id="spinner" role="status" >
                      <span class="visually-haidden">Loading...</span>
                   			 </div>
							
						</div> 
				
			</form>
				<div id="resultados"></div><!-- Carga los datos ajax -->
				<div class='outer_div'></div><!-- Carga los datos ajax -->
			</div>
		</div>	
		
	</div>
	
	<?php
include("../footer.php");
	?>
	<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="../../js/productos.js"> </script>
	
	
</body>
</html>
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



function obtener_datos(id){
			console.log(id);
			 var clave = $("#estado"+id).val();
			console.log(clave); 
			 $("#mod_id").val(id);
			 $("#mod_estado").val(clave);
			
		 }
		 function obtener_bloqueo(id){
			console.log('ID'+id);
			 var clave = $("#estado"+id).val();
			console.log(clave); 
			 $("#mod_id2").val(id);
			 $("#mod_estado").val(clave);
			
		 }
		 
	</script>s