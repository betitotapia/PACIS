<?php
require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos

    $nombre_almacen = $_POST['nombre_almacen'];
    $encargado = $_POST['encargado'];
    $numero_almacen = intval($_POST['numero_almacen']);

    $sql = "INSERT INTO almacenes (numero_almacen,descripcion, encargado,status) VALUES ('$numero_almacen','$nombre_almacen', '$encargado',1)";
    if (mysqli_query($con, $sql)) {
        echo "Nuevo almacén creado exitosamente";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }   
	
?>