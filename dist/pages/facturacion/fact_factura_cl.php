<?php
include('../../ajax/is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
 

$id_factura= $_GET['id_factura'];
 $numero_factura= $_GET['numero_factura'];
 $session_id= session_id();
	/* Connect To Database*/
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	//Archivo de funciones PHP
	//include("./funciones.php");
$sql_fact=mysqli_query($con, "select * from facturas where id_factura='$id_factura'");
while ($rw=mysqli_fetch_array($sql_fact)){
	$id_vendedor=$rw['id_vendedor'];
}

$sql_last = mysqli_query($con, "SELECT id_fact_facturas AS last FROM fact_facturas WHERE id_vendedor = $id_vendedor ORDER BY id_fact_facturas DESC LIMIT 1");

if ($sql_last && mysqli_num_rows($sql_last) > 0) {
    $rw = mysqli_fetch_array($sql_last);
    $no_fact_factura = $rw['last'] + 1;
} else {
    $no_fact_factura = 1;
}


$sql_fact="INSERT INTO fact_facturas (id_remision,no_fact_factura) VALUES ($id_factura,$no_fact_factura) ";
mysqli_query($con,$sql_fact);	



$item=1;
	$sumador_total=0;
	$sql=mysqli_query($con, "SELECT * FROM products, facturas, detalle_factura 
	WHERE facturas.numero_factura=detalle_factura.numero_factura AND  facturas.id_factura='$id_factura' 
	AND products.id_producto=detalle_factura.id_producto 
	AND facturas.id_vendedor='$id_vendedor' AND detalle_factura.id_vendedor='$id_vendedor'  
	ORDER BY detalle_factura.precio_venta DESC ");
	while ($row=mysqli_fetch_array($sql))
	{
	$numero_factura=$row["numero_factura"];
	$id_producto=$row["id_producto"];
	$cantidad=$row['cantidad'];
	$precio_venta=$row['precio_venta'];
	$almacen=$row['almacen'];
	$no_item=$item++;
	$date_created=date("Y-m-d");

	$insert_tmp=mysqli_query($con, "INSERT INTO detalle_fact_factura (nuemero_factura,id_producto,cantidad,precio_venta,id_almacen,id_vendedor,date_created) VALUES ('$numero_factura','$id_producto','$cantidad','$precio_venta','$almacen','$id_vendedor','$date_created')");
    }
    //  header("Location: ../nueva_factura_cl.php?id_factura=".$id_factura);
	//  die();
	 //echo "<script>console.log('".$id_factura." ".$n."')</script>";
	 include "fact_nueva_factura_cl.php";
?>