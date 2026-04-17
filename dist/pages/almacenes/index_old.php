<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
	$active_almacenes="active";
	$active_borrador="";
	$active_cancel="";
    $active_productos="";
	$active_lista_productos="";
	$active_nueva="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
    $sctive_nuevo_producto='';
	$active_usuarios="";
	$active_terceros="";
	$active_provedores='';
	
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

			<!-- <a  href='nueva_factura.php' class='btn btn-info'><span class='glyphicon glyphicon-plus' ></span> Nuevo almacén</a> -->
			 <button class='btn btn-success'data-bs-toggle="modal" data-bs-target="#almacen" >Nuevo Almacén  <i class="bi bi-building-fill-add"></i></button>
			
	</div>
				
			</div>
			<h4><i class='glyphicon glyphicon-search' onkeyup='load(1);'></i>Almacenes</h4>
			<div><i class='glyphicon glyphicon-user' ></i>
			<?php //echo $rw_usuario['nombre']; ?></i></div>
		</div>
			<div class="panel-body">
			<?php
			
			include("../modal/registro_almacen.php");
			?>
		
				<form class="form-horizontal" role="form" id="datos_cotizacion" >
				
						<div class="form-group row">
						
							<div class="col-md-5">
								
							</div>
							
							
							<div class="spinner-border text-primary"  id="spinner" role="status" >
                      <span class="visually-hidden">Loading...</span>
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
include("../modal/editar_almacen.php");
	?>
	<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="../../js/almacenes.js"> </script>

	
	
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

function guardar_almacen(){
	var numero_almacen = $("#id_numero_almacen").val();
	var nombre_almacen = $("#nombre_almacen").val();
	var encargado = $("#id_encargado").val();
	const modalElement = document.getElementById('almacen');
  const modalInstance = bootstrap.Modal.getInstance(modalElement);
	if (nombre_almacen == "" || encargado == "") {
		alert("Por favor complete todos los campos");
		return false;
	}
	$.ajax({
		type: "POST",
		url: "../../ajax/almacenes_ajax.php?action=guardar_almacen",
		data: "nombre_almacen=" + nombre_almacen + "&encargado=" + encargado + "&numero_almacen=" + numero_almacen,
		beforeSend: function(objeto) {
			$("#resultados").html("Mensaje: Cargando...");
		},
		success: function(datos) {
			//$("#resultados").html(datos);
			Swal.fire({
				title: "Almacén creado exitosamente",
				text: "OK!",
				icon: "success"
				});

  if (modalInstance) {
    modalInstance.hide();
  }

window.location.reload()
		}


	});
}

function obtener_datos_almacen(id){

        	var id_almacen = $("#id_almacen_"+id).val();
			var clave = $("#clave_"+id).val();
			var descripcion = $("#descripcion_"+id).val();
            var encargado = $("#encargado_"+id).val();
			$("#mod_id_alamacen").val(id);
			$("#mod_id_numero_almacen").val(clave);
			$("#mod_nombre_almacen").val(descripcion);
            $("#mod_id_encargado").val(encargado);

}

function editar_datos(){

	var parametros=$(editar_almacen_nuevo).serialize();
	$.ajax({
		type: "POST",
		url: "../../ajax/editar_almacen.php",
		data: parametros,
		beforeSend: function(objeto){
			$("#resultados_ajax_almacen").html("Mensaje: Cargando...");
		},
		success: function(datos){
			$("#resultados_ajax_almacen").html(datos);
			load(1);
		}
	});
	event.preventDefault();

}
		 
	</script>