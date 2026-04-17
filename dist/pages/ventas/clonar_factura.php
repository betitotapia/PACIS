<?php

include('./ajax/is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
 $id_factura= $_GET['id_factura'];
 $numero_factura= $_GET['numero_factura'];
 $session_id= session_id();
	/* Connect To Database*/
	require_once ("./config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("./config/conexion.php");//Contiene funcion que conecta a la base de datos
	//Archivo de funciones PHP
	include("./funciones.php");


$sql_borrar=mysqli_query($con,"DELETE FROM tmp WHERE session_id = '".$session_id."'");
echo "<script>console.log('esto se borro ')</script>";
//sleep(2);
$sql_fact=mysqli_query($con, "select * from facturas where id_factura='$id_factura'");
while ($rw=mysqli_fetch_array($sql_fact)){
	$id_vendedor=$rw['id_vendedor'];
}

$item=1;
	$sumador_total=0;
	$sql=mysqli_query($con, "select * from products, facturas, detalle_factura where facturas.numero_factura=detalle_factura.numero_factura and  facturas.id_factura='$id_factura' and inve01.id_producto=detalle_factura.id_producto and facturas.id_vendedor='$id_vendedor' and detalle_factura.id_vendedor='$id_vendedor'  ORDER BY detalle_factura.precio_venta DESC ");
	while ($row=mysqli_fetch_array($sql))
	{
	$id_producto=$row["id_producto"];
	$codigo_producto=$row['clave'];
	$referencia=$row['referencia'];
	$almacen=$row['almacen'];
	$cantidad=$row['cantidad'];
	$lote=$row['lote'];
	$caducidad=$row['caducidad'];
	$nombre_producto=$row['descripcion'];
	$no_item=$item++;
	$precio_venta=$row['precio_venta'];

	$insert_tmp=mysqli_query($con, "INSERT INTO tmp (id_producto,lote_tmp,caducidad_tmp,referencia_tmp,id_almacen_tmp,cantidad_tmp,precio_tmp,session_id) VALUES ('$id_producto','$lote','$caducidad','$referencia','$almacen','$cantidad','$precio_venta','$session_id')");

    }
    //  header("Location: ../nueva_factura_cl.php?id_factura=".$id_factura);
	//  die();
	 //echo "<script>console.log('".$id_factura." ".$n."')</script>";
	 include(dirname('__FILE__').'/nueva_factura_cl.php');
?>