<?php
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos

$id_cliente=intval($_POST['cliente_id']);
	$id_vendedor=intval($_POST['vendedor']);
	$letra_ventas=($_POST['letra_ventas']);
	$hospital=($_POST['hospital_f']);
    $proveedor=($_POST['proveedor_f']);
	$compra=($_POST['compra_f']);
	$cotizacion=($_POST['cotizacion_f']);
	$doctor=($_POST['doctor_f']);
	$paciente=($_POST['paciente_f']);
	$material=($_POST['material_f']);
	$pago=($_POST['pago_f']);
	$d_factura=($_POST['d_factura_f']);
	$observaciones=($_POST['observaciones_f']);
    $date_create=date("Y-m-d H:i:s");
    $numero_factura=($_POST['numero_factura']);

	$sql = mysqli_query($con, "UPDATE facturas SET 
    
    id_cliente = '$id_cliente',
    id_vendedor = '$id_vendedor',
    estado_factura = 0,
    compra = '$compra',
    cotizacion = '$cotizacion',
    doctor = '$doctor',
    paciente = '$paciente',
    material = '$material',
    pago = '$pago',
    d_factura = '$d_factura',
    observaciones = '$observaciones',
    status_fact = 3,
    bloqueo = 0,
    validacion = 0,
    date_create = '$date_create',
    hospital = '$hospital',
    proveedor = '$proveedor'
    WHERE numero_factura = '$numero_factura' AND id_vendedor = '$id_vendedor'");

   
?>