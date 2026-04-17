<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
	$active_usuarios="";
	$active_productos="";
	$active_lista_productos="";
	$active_borrador="";
	$active_nueva="";
	$active_remisiones="";
	$active_vehiculos="";
	$active_cancel="";
	$active_almacenes="";
    $active_terceros="active";
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
		$sql_usuario1=mysqli_query($con,"SELECT * FROM clientes ");
        $rj_usuario1=mysqli_fetch_array($sql_usuario1);
		echo "<script>console.log('work evaluando:".$session_id2."');</script>";	
		?>
                        <div>

                            
                        </div>

                    </div>
                    <h4><i class='glyphicon glyphicon-search' onkeyup='load(1);'></i>Clientes</h4>
                    <div><i class='glyphicon glyphicon-user'></i>
                        <?php //echo $rw_usuario['nombre']; ?></i></div>
                </div>
                <div class="panel-body">
                    <?php
			?>
                    <div class="boton_new">
                        <!-- <button class="btn btn-lg btn-success "href="nueva_remision.php" >Nueva remisión</button> -->
                        <button class='btn btn-success' onclick='redirect_to_nuevo_cliente()'>Nuevo
                            cliente<i class="bi bi-building-fill-add"></i></button>

                    </div>


                    <div class="form-group row">
                        <!--<label for="q" class="col-md-2 control-label">Cliente o # de Remisión</label>-->
                        <div class="col-md-5">

                        </div>
                        <div class="outer-div">

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
	?>
    <script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
    <script type="text/javascript" src="../../js/clientes.js"> </script>
    <script >function redirect_to_nuevo_cliente(){

    window.location.href = "nuevo_cliente.php";
}
</script>

</html>
<script>

</script>