<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
	$active_productos="";
    $active_lista_productos="active";
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
			<h4><i class='glyphicon glyphicon-search' onkeyup='load(1);'></i>Lista de Productos por Referencia</h4>
			<div><i class='glyphicon glyphicon-user' ></i>
			<?php //echo $rw_usuario['nombre']; ?></i></div>
			<button class="btn btn-success" id="exportExcel">Exportar a Excel</button>
		</div>
			<div class="panel-body">
			<?php
			include("../modal/estado_factura.php");
			include("../modal/bloqueo_remision_normal.php");
			?>
		<div  class="boton_new">
			<!-- <button class="btn btn-lg btn-success "href="nueva_remision.php" >Nueva remisión</button> -->
		</div>
				<form class="form-horizontal" role="form" id="datos_cotizacion" >
				
						<div class="form-group row">
							<!--<label for="q" class="col-md-2 control-label">Cliente o # de Remisión</label>-->
							<div class="col-md-5">
								<input type="hidden" class="form-control" id="q" placeholder="Nombre del cliente o # de Remisión" onkeyup='load(1);'>
							</div>
							<div class="outer-div">

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
	?>
	<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="../../js/productos.js"> </script>
	

	<!-- Modal detalle por almacén -->
<div class="modal fade" id="modalDetalleAlmacenes" tabindex="-1" role="dialog" aria-labelledby="detalleAlmacenesLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="detalleAlmacenesLabel">
          Existencias por almacén - <span id="detalleReferencia"></span>
        </h4>
      </div>
      <div class="modal-body" id="contenidoDetalleAlmacenes">
        <p>Cargando información...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
	
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


$( "#editar_estado" ).submit(function( event ) {
			$('#actualizar_estado').attr("disabled", true);
			
		   var parametros = $(this).serialize();
			   $.ajax({
					  type: "POST",
					  url: "../../ajax/editar_estado_factura.php",
					  data: parametros,
					   beforeSend: function(objeto){
						  $("#resultados_ajax3").html("Mensaje: Cargando...");
						},
					  success: function(datos){
					  $("#resultados_ajax3").html(datos);
					  $('#actualizar_estado').attr("disabled", false);
					  load(1);
					}
			  });
			  event.preventDefault();
			})

			$( "#bloqueo_estado" ).submit(function( event ) {
			$('#bloqueo_remision').attr("disabled", true);
			
		   var parametros = $(this).serialize();
			   $.ajax({
					  type: "POST",
					  url: "ajax/bloqueo_remision_normal.php",
					  data: parametros,
					   beforeSend: function(objeto){
						  $("#resultados_ajax4").html("Mensaje: Cargando...");
						},
					  success: function(datos){
					  $("#resultados_ajax4").html(datos);
					  $('#bloqueo_remision').attr("disabled", false);
					  load(1);
					}
			  });
			  event.preventDefault();
			})

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

		 function nueva_remision(){
			$.ajax({
				type: "GET",
				url: "../../ajax/crear_nueva_remision.php",
				data: "",
				success: function(data) {
					// Aquí puedes manejar la respuesta del servidor si es necesario
					console.log("Nueva remisión creada "+ data);
					window.location.href = "nueva_remision.php?n_remi="+data; // Redirige a la página de nueva remisión
				},
				error: function(xhr, status, error) {
					console.error("Error al crear nueva remisión:", error);
				}
			});

		 }

// 		  document.getElementById("exportExcel").addEventListener("click", function() {
//     // Selecciona la tabla que quieres exportar
//     var tabla = document.querySelector("table");

//     // Convierte la tabla a un workbook
//     var wb = XLSX.utils.table_to_book(tabla, {sheet:"Datos"});

//     // Descarga el archivo Excel
//     XLSX.writeFile(wb, "datos_tabla.xlsx");
// });


document.getElementById("exportExcel").addEventListener("click", function () {
  fetch("../../ajax/excel_productos.php?export=1")
    .then(response => response.json())
    .then(data => {
      const ws = XLSX.utils.json_to_sheet(data);
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, "Productos");
      XLSX.writeFile(wb, "productos_completos.xlsx");
    })
    .catch(error => console.error("Error al exportar:", error));
});

function verDetalleAlmacenes(referencia) {
    // Poner la referencia en el título del modal
    document.getElementById('detalleReferencia').textContent = referencia;
    // Mensaje de carga
    document.getElementById('contenidoDetalleAlmacenes').innerHTML = '<p>Cargando información...</p>';

    // Mostrar el modal
    $('#modalDetalleAlmacenes').modal('show');

    // Llamada AJAX para traer el desglose por almacén
    $.ajax({
        url: '../../ajax/detalle_producto_almacenes.php',
        method: 'GET',
        data: { referencia: referencia },
        success: function (data) {
            $('#contenidoDetalleAlmacenes').html(data);
        },
        error: function (xhr, status, error) {
            $('#contenidoDetalleAlmacenes').html(
                '<div class="alert alert-danger">Ocurrió un error al cargar el detalle: ' + error + '</div>'
            );
        }
    });
}
		 
	</script>