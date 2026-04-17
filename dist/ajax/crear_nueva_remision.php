<?php
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');

	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos

    $session_id2 = $_SESSION["user_id"];

    $sql=mysqli_query($con,"SELECT numero_factura FROM facturas WHERE id_vendedor = $session_id2 ORDER BY id_factura DESC LIMIT 1;");
	$rw=mysqli_fetch_array($sql);
	if (empty($rw['numero_factura'])){
		$numero_factura=1;
		$fecha=date("Y-m-d");
		$sql_insert=mysqli_query($con,"INSERT INTO facturas (numero_factura, fecha_factura,id_vendedor,status_fact) VALUES ('1','$fecha', '$session_id2',3)");
		
	}else{
		
		$numero_factura=$rw['numero_factura']+1;	
		$fecha=date("Y-m-d");
		$sql_insert=mysqli_query($con,"INSERT INTO facturas (numero_factura, fecha_factura, id_vendedor,status_fact) VALUES ('$numero_factura', '$fecha', '$session_id2',3)");
	}
		$sql_id=mysqli_query($con,"SELECT LAST_INSERT_ID(id_factura) as ultimo FROM facturas WHERE id_vendedor = $session_id2 order by id_factura desc limit 0,1");
		$rw_id=mysqli_fetch_array($sql_id);
		echo $rw_id['ultimo'];

 die();
?>