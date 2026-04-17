<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
	$active_usuarios="active";
	$active_productos="";
	$active_lista_productos="";
	$active_borrador="";
	$active_nueva="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
	$active_almacenes="";
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
                    <h4><i class='glyphicon glyphicon-search' onkeyup='load(1);'></i>Usuarios</h4>
                    <div><i class='glyphicon glyphicon-user'></i>
                        <?php //echo $rw_usuario['nombre']; ?></i></div>
                </div>
                <div class="panel-body">
                    <?php
			?>
                    <div class="boton_new">
                        <!-- <button class="btn btn-lg btn-success "href="nueva_remision.php" >Nueva remisión</button> -->
                        <button class='btn btn-success' data-bs-toggle="modal" data-bs-target="#usuarios">Nuevo
                            Usuario<i class="bi bi-building-fill-add"></i></button>

                    </div>


                    <div class="form-group row">
                        <!--<label for="q" class="col-md-2 control-label">Cliente o # de Remisión</label>-->
                        <div class="col-md-5">

                        </div>
                        <div class="outer-div">

                        </div>

                        <div class="spinner-border text-primary" id="spinner" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>

                    </div>
                    <div id="resultados"></div><!-- Carga los datos ajax -->
                    <div class='outer_div'></div><!-- Carga los datos ajax -->
                </div>
            </div>

    </div>

    <?php
include("../footer.php");
include("../modal/registro_usuarios.php");
include("../modal/editar_usuario.php");
include("../modal/cambiar_password.php");
	?>
    <script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
    <script type="text/javascript" src="../../js/usuarios.js"> </script>

</body>

</html>
<script>
function guardar_usuarios(){
                        var parametros = $("#guardar_usuario").serialize(); // Serializa los datos del formulario

                        $.ajax({
                            type: "POST",
                            url: "../../ajax/nuevo_usuario.php", // Cambia esto si tu archivo está en otra ruta
                            data: parametros,
                            beforeSend: function() {
                                $("#resultados_ajax").html("Mensaje: Cargando...");
                            },
                            success: function(datos) {
                                $("#resultados_ajax").html(datos);
                                $('#guardar_datos').attr("disabled", false); // Reactiva el botón
                                // Puedes limpiar el formulario o recargar datos si es necesario
                                // $("#guardar_usuario")[0].reset();
                                // load(1); // Si tienes una función para recargar datos
                            },
                            error: function() {
                                $("#resultados_ajax").html("Error al enviar los datos.");
                                $('#guardar_datos').attr("disabled", false);
                            }
                        });
					}
function actualizar_usuarios(){
                        var parametros = $("#mod_editar_usuario").serialize(); // Serializa los datos del formulario

                        $.ajax({
                            type: "POST",
                            url: "../../ajax/editar_usuario.php", // Cambia esto si tu archivo está en otra ruta
                            data: parametros,
                            beforeSend: function() {
                                $("#resultados_ajax").html("Mensaje: Cargando...");
                            },
                            success: function(datos) {
                                $("#resultados_ajax").html(datos);
                                $('#actualizar_datos').attr("disabled", false); // Reactiva el botón
                                // Puedes limpiar el formulario o recargar datos si es necesario
                                // $("#guardar_usuario")[0].reset();
                                // load(1); // Si tienes una función para recargar datos
                            },
                            error: function() {
                                $("#resultados_ajax").html("Error al enviar los datos.");
                                $('#actualizar_datos').attr("disabled", false);
                            }
                        });
					}

function get_user_id(id){
		$("#user_id_mod").val(id);
	}


function obtener_datos(id){

    
        	var nombres = $("#nombres_"+id).val();
			var usuario = $("#usuario_"+id).val();
			var email = $("#email_"+id).val();
           // var letra = $("#letra_"+id).val();
            var administrador = $("#administrador_"+id).val();
            
            console.log(administrador);
			$("#mod_id").val(id);
			$("#mod_firstname").val(nombres);
			$("#mod_user_name").val(usuario);
			$("#mod_user_email").val(email);
           // $("#mod_letra").val(letra);
            $("#mod_is_admin").val(administrador);

}

const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };

                        document.addEventListener('DOMContentLoaded', function() {
                            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
                            if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !==
                                'undefined') {
                                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                                    scrollbars: {
                                        theme: Default.scrollbarTheme,
                                        autoHide: Default.scrollbarAutoHide,
                                        clickScroll: Default.scrollbarClickScroll,
                                    },
                                });
                            }
                        });

    $( "#mod_editar_usuario" ).submit(function( event ) {
  $('#actualizar_datos').attr("disabled", true);
  
 var parametros = $(this).serialize();
	 $.ajax({
			type: "POST",
			url: "ajax/editar_usuario.php",
			data: parametros,
			 beforeSend: function(objeto){
				$("#resultados_ajax2").html("Mensaje: Cargando...");
			  },
			success: function(datos){
			$("#resultados_ajax2").html(datos);
			$('#actualizar_datos2').attr("disabled", false);
			load(1);
		  }
	});
  event.preventDefault();
})        

function actualizar_password(){

 var parametros = $(editar_password).serialize();
	 $.ajax({
			type: "POST",
			url: "../../ajax/editar_password.php",
			data: parametros,
			 beforeSend: function(objeto){
				$("#resultados_ajax3").html("Mensaje: Cargando...");
			  },
			success: function(datos){
			$("#resultados_ajax3").html(datos);
			$('#actualizar_datos3').attr("disabled", false);
			load();
		  }
	});
}
</script>