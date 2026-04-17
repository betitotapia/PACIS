<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
	$active_productos="";
	$active_recepciones="";
	$active_lista_productos="";
	$active_borrador="";
	$active_nueva="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
	$active_almacenes="active";
	$active_usuarios="";
	$active_terceros="";
	$active_provedores='';

require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos

$id_almacen = isset($_GET['id_almacen']) ? intval($_GET['id_almacen']) : 0;
$sql_almacen = mysqli_query($con, "select * from almacenes where id_almacen = $id_almacen");
$rw_almacen = mysqli_fetch_array($sql_almacen);
$no_almacen=$rw_almacen['numero_almacen'];
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
			<h4><i class='glyphicon glyphicon-search' onkeyup='load(1);'></i>Almacen <?php echo $no_almacen ?></h4>
			<div><i class='glyphicon glyphicon-user' ></i>
			<?php //echo $rw_usuario['nombre']; ?></i></div>
		</div>
			<div class="panel-body">
			
		
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
				
			<input type="hidden" id="id_almacen" value="<?php echo $id_almacen; ?>">	
				
			</form>
				<div class='outer_div'></div><!-- Carga los datos ajax -->
			</div>
		</div>	
		
	</div>
	
	<?php
include("../footer.php");
include("../modal/editar_productos.php");
	?>
	<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="../../js/detalle_almacen.js"> </script>
	
	
</body>
</html>
<script>
	   function eliminar (id)
{
    var q= $("#q").val();
//if (confirm("Realmente deseas eliminar el producto")){	
if (Swal.fire({
    title: 'Eliminar producto',
    text: "Realmente deseas eliminar el producto",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar'
    }).then((result) => {
    if (result.isConfirmed) {   
        $.ajax({
            type: "GET",
            url: "../../ajax/buscar_almacenes_detalle.php",
            data: "id_producto="+id,"q":q,
            beforeSend: function(objeto){
                $("#resultados").html("Mensaje: Cargando...");
              },
            success: function(datos){
                $("#resultados").html(datos);
                load(1);
            }
                }); //
            }
    })) ;                        
         }

 function obtener_datos(id){
	 
	$id_producto=$("#id_producto_"+id).val();
	$referencia=$("#referencia_"+id).val();
	$descripcion=$("#descripcion_"+id).val();
	$lote=$("#lote_"+id).val();
	$caducidad=$("#caducidad_"+id).val();
	$existencias=$("#existencias_"+id).val();
	$costo=$("#costo_"+id).val();
	$precio=$("#precio_"+id).val();
	
	$("#modal_id_producto").val($id_producto);
	$("#modalReferencia").val($referencia);
	$("#modalDescripcion").val($descripcion);
	$("#modalLote").val($lote);
	$("#modalCaducidad").val($caducidad);
	$("#modalExistencias").val($existencias);
	$("#modalCosto").val($costo);
	$('#modalPrecio').val($precio);
 }

 function editar_producto(event){
	 var parametros=$("#editar_producto_formulario").serialize();
		
	$.ajax({
		type: "POST",
		url: "../../ajax/editar_productos.php",
		data: parametros,
		beforeSend: function(objeto){
			$("#resultados_ajax_producto").html("Mensaje: Cargando...");
		},
		success: function(datos){
			$("#resultados_ajax_producto").html(datos);
			load(1);
			$("#modalEditarProducto").modal('hide');
			$(".modal-backdrop").remove();

			$("#modalEditarProducto")[0].reset();


		}
	});

}
 





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