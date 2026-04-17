<?php
session_start();
require_once("../config/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
require_once("../config/conexion.php");




if (isset($_GET['id'])){

	if(isset($_GET['tipo'])){

		$tipo=$_GET['tipo'];


	switch ($tipo){

		case "cantidad":

		$cantidad=$_GET['item'];
		$id=($_GET['id']);
		// Preparar la consulta con parámetros
			$sql_update = mysqli_query($con,"UPDATE detalle_factura SET cantidad = $cantidad WHERE id_detalle = $id");
		
		break;

        case "descripcion":

        $descripcion=$_GET['item'];
        $id=($_GET['id']);
        // Preparar la consulta con parámetros
            $sql_update = mysqli_query($con,"UPDATE detalle_factura SET descripcion = '$descripcion' WHERE id_detalle = $id");
        
        break;

	  }
	
	}
}
		

?>