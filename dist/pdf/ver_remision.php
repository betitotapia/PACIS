<?php
	error_reporting(E_ALL ^ E_NOTICE);
	session_start();
	if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
        header("location: ../../login.php");
		exit;
    }
	ini_set('display_errors', 1);
	
	/* Connect To Database*/
	include("../config/db.php");
	include("../config/conexion.php");

	//Archivo de funciones PHP
	include("../pages/funciones.php");
	
	//FIn Variables GET
	
	//Variables por GET
	$numero_factura=intval($_GET['numero_factura']);
	$id_vendedor=intval($_GET['id_vendedor']);
    //$id_factura=intval($_GET['id_factura']);
	

	$session_id= session_id();
	$sql_count=mysqli_query($con,"SELECT * FROM detalle_factura WHERE numero_factura = '".$numero_factura."' AND id_vendedor = '".$id_vendedor."'");
	$count=mysqli_num_rows($sql_count);
	if ($count==0)
	{
	echo "<script>alert('No hay productos agregados a la factura')</script>";
	echo "<script>window.close();</script>";
	exit;
	}
    $sql_facturas=mysqli_query($con,"SELECT * FROM facturas WHERE numero_factura = '".$numero_factura."' AND id_vendedor = '".$id_vendedor."'");
    $rw_factura= mysqli_fetch_array($sql_facturas);

	$id_cliente=intval($rw_factura['id_cliente']);
	$fecha=date("d/m/Y",strtotime($rw_factura['fecha_factura']));
	$compra=($rw_factura['compra']);
	$cotizacion=($rw_factura['cotizacion']);
	$doctor=($rw_factura['doctor']);
	$paciente=($rw_factura['paciente']);
	$material=($rw_factura['material']);
	$pago=($rw_factura['pago']);
	$d_factura=($rw_factura['d_factura']);
	$observaciones=($rw_factura['observaciones']);
	$hospital=($rw_factura['hospital']);
	$proveedor=($rw_factura['no_proveedor']);

    $sql_user=mysqli_query($con,"SELECT * FROM users WHERE user_id = '".$id_vendedor."'");
    $rw_usuario=mysqli_fetch_array($sql_user);
    $nombre_vendedor=($rw_usuario['nombre']);
    $letra_ventas=($rw_usuario['letra']);
	
	
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------$condiciones=mysqli_real_escape_string($con,(strip_tags($_REQUEST['condiciones'], ENT_QUOTES)));
	//Fin de variables por GET
	

	$simbolo_moneda="$"; // Simbolo de la moneda, se puede cambiar por el que se necesite
    // GET the HTML
    include(dirname('__FILE__').'/res/print_remision_oc_html.php');
   ?>
   <input type="hidden" id="letra" value="<?php echo $letra_ventas  ?>" >
   <input type="hidden" id="numero_factura" value="<?php echo $numero_factura  ?>" >
<script>
	letra=document.getElementById("letra").value;
	factura=document.getElementById("numero_factura").value;
	//toPdf(letra,factura)
</script>


