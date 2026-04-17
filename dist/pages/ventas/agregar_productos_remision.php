
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
?>
<!DOCTYPE html>
<html lang="en">
<?php
include '../header.php';
?>
<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info  bg-body-tertiary">
	<div class="app-wrapper">
		
    <main class="app-main">
	 <?php
include '../navbar.php';
include '../aside_menu.php';
    ?>
<div>
	

			<!-- <a  href='nueva_factura.php' class='btn btn-info'><span class='glyphicon glyphicon-plus' ></span> Nuevo almacén</a> -->
			 <button class='btn btn-success'data-bs-toggle="modal" data-bs-target="#buscar_productos" >Agregar Productos  <i class="bi bi-building-fill-add"></i></button>
			
	</div>
<?php
			include("../modal/buscar_productos.php");
			?>
</body>
</html>