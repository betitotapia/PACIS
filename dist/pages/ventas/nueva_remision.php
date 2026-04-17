<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
	$active_nueva="active";
	$active_productos="";
	$active_borrador="";
	$active_lista_productos="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
	$active_almacenes='';
	$active_usuarios="";
	 $active_terceros="";
	 $active_provedores='';
	$active_recepciones="";
	
require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
$usuario = $_SESSION['user_id'];
$sql_usuario=mysqli_query($con,"SELECT * FROM users WHERE user_id = $usuario");
$rw_usuario=mysqli_fetch_array($sql_usuario);
$letra=$rw_usuario['letra']; // Default to 0 if not set


$nueva_remision = $_GET['n_remi'] ?? null;

$remision ="P".$letra."-".$nueva_remision;


if (isset($_GET['id']))//codigo elimina un elemento del array
{
$id_tmp=intval($_GET['id']);

$producto = intval($_GET['producto']);
$delete=mysqli_query($con, "DELETE FROM detalle_factura WHERE id_detalle='".$id_tmp."'");
$sql_descuento=mysqli_query($con, "SELECT * FROM products WHERE id_producto='".$producto."'");
$row_descuento=mysqli_fetch_array($sql_descuento);
$existencias=$row_descuento['existencias']+1;
$update_existencias=mysqli_query($con, "UPDATE products SET existencias='".$existencias."' WHERE id_producto='".$producto."'");
if ($delete && $update_existencias){

echo"<script>console.log('se borro el id: ".$id_tmp."');</script>";

}
}
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
		
		 <div class="card card-info card-outline mb-4">
		 <div class="card-header"><div class="card-title">NUEVA REMISION <b><?php echo $remision ?></b></div></div>
		 
		<form onkeydown="return event.key != 'Enter';"  class="form-horizontal needs-validation"  id="datos_remision"  >
			<div class="card-body">
                      <!--begin::Row-->
            <div class="row g-3">
                        <!--begin::Col-->
            <div class="col-12">
				<div class="form-group row">
				  <label for="nombre" class="col-md-1 control-label">Cliente</label>
				  <div class="col-md-2">
					  <input type="text" class="form-control input-sm" name="nombre" id="nombre_cliente" placeholder="Selecciona un cliente" required>
					  <input id="id_cliente" type='hidden' name='cliente_id' value="" >	
				  </div>
				  <label for="colonia" class="col-md-1 control-label">Colonia</label>
				  <div class="col-md-2">
					  <input type="text" class="form-control input-sm" name="colonia" id="colonia_cliente" placeholder="Colonia" readonly>
				  </div>
				  <label for="calle" class="col-md-1 control-label">Calle</label>
				  <div class="col-md-2">
					  <input type="text" class="form-control input-sm" name="calle" id="calle_cliente" placeholder="Calle" readonly >
				  </div>
				  <label for="numext" class="col-md-1 control-label">No.EXT</label>
				  <div class="col-md-2">
					  <input type="text" class="form-control input-sm" name="numext" id="numext_cliente" placeholder="No. EXT" readonly>
				  </div>
				  
				</div>
				<br>
				  <div class="form-group row">
				  <label for="rfc" class="col-sm-1 control-label">RFC</label>
							<div class="col-md-2">
								<input type="text" name="rfc" class="form-control input-sm" id="rfc_cliente" placeholder="RFC" readonly>
							</div>
				  <label for="telefono_cliente" class="col-md-1 control-label">Teléfono</label>
							<div class="col-md-2">
								<input type="text" name="telefono_cliente" class="form-control input-sm" id="telefono_cliente" placeholder="Teléfono" readonly>
							</div>
					<label for="mail" class="col-md-1 control-label">Email</label>
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" name="mail" id="mail" placeholder="Email" readonly>
							</div>
							<label for="tel2" class="col-md-1 control-label">Fecha</label>
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" name="fecha_f" id="fecha" value="<?php echo date("d/m/Y");?>" readonly>
							</div>
				 </div>
				 <br>
					<div class="form-group row">
						<label for="compra" class="col-md-1 control-label">Orden de compra</label>
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" name="compra_f" id="compra" placeholder="Orden de compra:">
							</div>
							<label for="cotizacion" class="col-md-1 control-label">Cotización no.</label>
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" name="cotizacion_f" id="cotizacion" placeholder="Cotización" >
							</div>

							<label for="doctor" class="col-md-1 control-label">Doctor</label>
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" name="doctor_f" id="doctor" placeholder="Nombre del Doctor" >
							</div>

							<label for="paciente" class="col-md-1 control-label">Paciente</label>
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" name="paciente_f" id="paciente" placeholder="Paciente" >
							</div>
					</div>
	<br>
					<div class="form-group row">
							
							
								
								<label for="material" class="col-md-1 control-label">Material de:</label>
									<div class="col-sm-2">
										<select class='form-control input-sm ' id="material" name="material_f">
										<option selected value="" >Tipo de material</option>
											<option value="Consignación" >Consignación</option>
											<option value="Donación" >Donación</option>
											<option value="Venta" >Venta</option>
											<option value="Reposición de consigna" >Reposición de consigna</option>
											<option value="Prestamo" >Prestamo</option>

											</option>
										</select>
									</div>
									<label for="pago" class="col-sm-1 control-label">Condiciones de pago</label>
									<div class="col-md-2">
										<select class='form-control input-sm ' id="pago" name="pago_f">
										<option selected value="">Tipo de pago</option>
											<option value="Efectivo" >Efectivo</option>
											<option value="Transferencia" >Transferencia</option>
											<option value="Crédito" >Crédito</option>
											</option>
										</select>
									</div>
									
									<label for="" class="col-sm-1 control-label">Factura</label>
									<div class="col-md-2">
										<select class='form-control input-sm' id="d_factura" name="d_factura_f">
											<option value="SI" >SI</option>
											<option value="PUBLICO EN GENERAL" >PUBLICO EN GENERAL</option>
											</option>
										</select>
										</div>
									</div>
<br>
							<div class="form-group row">
							<label for="vendedor" class="col-md-1 control-label">Vendedor</label>
							<div class="col-md-2">	
									<?php
									$sql_vendedor2=mysqli_query($con,"SELECT * FROM users WHERE user_id = $usuario order by nombre");
									$rw = mysqli_fetch_array($sql_vendedor2);	
									$id_vendedor=$rw["user_id"];
									$nombre_vendedor=$rw["nombre"];
									$letra = $rw['letra'];
									?>
									<input type="hidden" class="form-control input-sm" name="vendedor" id="id_vendedor" name="vendedor_id" value="<?php echo $id_vendedor?>" >
									<input type="text" class="form-control input-sm" name="id_vendedor" id="" value="<?php echo $nombre_vendedor?>" readonly>
							</div>
							<label for="letra" class="col-md-1 control-label">LETRA</label>
								<div class="col-md-2" id="letras">
								<input type='text' class='form-control input-sm' name='letra_ventas' id='letra_ventas' readonly value="<?php echo $letra?>" >
								</div>

								<label for="letra" class="col-md-1 control-label">Observaciones</label>
								<div class="col-md-3" id="letras">
								<textarea name="observaciones_f" id="observaciones" rows="3" cols="50"></textarea>
								<input type="hidden" name="numero_factura" id="numero_factura" value="<?php echo $nueva_remision?>" >
								</div>
							</div>
						</div>
				</div>
						<div class="col-md-12">
							 <button type="button" class='class=" btn btn-block btn-LG bg_icons-highpurple botones_cel" >' data-bs-toggle="modal" data-bs-target="#buscar_productos" onclick='load(1);'>Agregar Productos <i class="bi bi-cart-plus-fill"></i></button>
							<button type="submit" class=" btn btn-block btn-LG g-label-success botones_cel" > Guardar <i class="bi bi-floppy2"></i>	</button>
                            <button type="button" class="btn btn-block btn-LG bg_icons-orange botones_cel" onclick="cerrar_remision()">Finalizar Remision <i class="bi bi-x-circle"></i></button>				
								

						</div>	
				</div>
			</div>
			</form>	
		
		<!---------------------modal------------------->
		
		<!--------------------- end modal------------------->

			</div>
			</div>
		</div>	
	<div id="resultados" class='' style="margin-top:10px"></div>
     		
		</div>	
	</div>
	
	 <script type="text/javascript" src="../../js/VentanaCentrada.js"></script> 
	<script type="text/javascript" src="../../js/nueva_remision.js"> </script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<?php
	include("../modal/buscar_productos.php");
	include("../footer.php");
	?>
</body>
</html>
<script>
$( "#datos_remision" ).submit(function( event ) {
		 
	var parametros = $(this).serialize();
  var id_cliente = $("#id_cliente").val().trim();

  if (id_cliente === "") {
    Swal.fire({
      icon: 'warning',
      title: '¡Atención!',
      text: 'Debes seleccionar un cliente.',
      confirmButtonText: 'Aceptar'
    });

    return; // Detiene el envío si no hay cliente
  }
 $.ajax({
					type: "POST",
					url: "../../ajax/datos_remision.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#resultados_ajax").html("Mensaje: Cargando...");
					  },
					success: function(datos){
						Swal.fire({
				title: "Remisión guardada exitosamente",
				text: "OK!",
				icon: "success"
				});
				
				  }
			});
		  event.preventDefault();
		})
	
function cerrar_remision() {

    var id_cliente = $("#id_cliente").val().trim();
    var numero_factura = $("#numero_factura").val().trim();
    var letra_ventas = $("#letra_ventas").val().trim();
    var observaciones = $("#observaciones").val().trim();
    var fecha = $("#fecha").val().trim();
    var compra = $("#compra").val().trim();
    var cotizacion = $("#cotizacion").val().trim();
    var doctor = $("#doctor").val().trim();
    var paciente = $("#paciente").val().trim();
    var material = $("#material").val().trim();
    var pago = $("#pago").val().trim();
    var d_factura = $("#d_factura").val().trim();
    var id_vendedor = $("#id_vendedor").val().trim();
    var letra_ventas = $("#letra_ventas").val().trim();

    VentanaCentrada('../../pdf/print_remision.php?id_cliente=' + id_cliente + '&numero_factura=' + numero_factura + '&letra_ventas=' + letra_ventas + '&fecha=' + fecha + '&compra=' + compra + '&cotizacion=' + cotizacion + '&doctor=' + doctor + '&paciente=' + paciente + '&material=' + material + '&pago=' + pago + '&d_factura=' + d_factura + '&id_vendedor=' + id_vendedor+ '&observaciones=' + observaciones);
    

}
    


$(function() {
						$("#nombre_cliente").autocomplete({
							source: "../../ajax/autocomplete/clientes.php",
							minLength: 2,
							select: function(event, ui) {
								$('#id_cliente').val(ui.item.id_cliente);
								$('#nombre_cliente').val(ui.item.nombre_cliente);
								$('#rfc_cliente').val(ui.item.rfc_cliente);
								$('#calle_cliente').val(ui.item.calle_cliente);
								$('#telefono_cliente').val(ui.item.telefono_cliente);
								$('#mail').val(ui.item.emailpred);
								$('#numext_cliente').val(ui.item.numext_cliente);
								$('#colonia_cliente').val(ui.item.colonia_cliente);
							 }
						});
						 
						
					});
					
	$("#nombre_cliente").on( "keydown", function( event ) {
						if (event.keyCode== $.ui.keyCode.LEFT || event.keyCode== $.ui.keyCode.RIGHT || event.keyCode== $.ui.keyCode.UP || event.keyCode== $.ui.keyCode.DOWN || event.keyCode== $.ui.keyCode.DELETE || event.keyCode== $.ui.keyCode.BACKSPACE )
						{
							$("#id_cliente" ).val("");
							$("#rfc_cliente" ).val("");
							$("#telefono_cliente" ).val("");
							$('#calle_cliente').val("");		
							$('#numext_cliente').val("");
							$('#colonia_cliente').val("");
							$('#mail').val("");
						}
						if (event.keyCode==$.ui.keyCode.DELETE){
							$("#nombre_cliente" ).val("");
							$("#id_cliente" ).val("");
							$("#rfc_cliente" ).val("");
							$("#telefono_cliente" ).val("");
							$("#mail" ).val("");
							$('#calle_cliente').val("");
							$('#numext_cliente').val("");
							$('#colonia_cliente').val("");
						}
			});	


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

  window.addEventListener("beforeunload", function (e) {
    const cliente = document.getElementById("nombre_cliente");

    if (cliente && cliente.value.trim() === "") {
      const mensaje = "¡Atención! Debes llenar el campo 'cliente' antes de salir.";
      e.preventDefault();
      e.returnValue = mensaje;
      return mensaje;
    }
  });

  

	</script>