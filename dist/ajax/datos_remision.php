<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    exit;
}

require_once("../config/db.php");
require_once("../config/conexion.php");

$id_factura    = intval($_POST['id_factura'] ?? 0);
$id_cliente    = intval($_POST['cliente_id'] ?? 0);
$id_vendedor   = intval($_POST['vendedor'] ?? 0);
$hospital      = mysqli_real_escape_string($con, $_POST['hospital_f'] ?? '');
$no_proveedor  = mysqli_real_escape_string($con, $_POST['proveedor_f'] ?? '');
$compra        = mysqli_real_escape_string($con, $_POST['compra_f'] ?? '');
$cotizacion    = mysqli_real_escape_string($con, $_POST['cotizacion_f'] ?? '');
$doctor        = mysqli_real_escape_string($con, $_POST['doctor_f'] ?? '');
$paciente      = mysqli_real_escape_string($con, $_POST['paciente_f'] ?? '');
$material      = mysqli_real_escape_string($con, $_POST['material_f'] ?? '');
$pago          = mysqli_real_escape_string($con, $_POST['pago_f'] ?? '');
$d_factura     = mysqli_real_escape_string($con, $_POST['d_factura_f'] ?? '');
$observaciones = mysqli_real_escape_string($con, $_POST['observaciones_f'] ?? '');
$date_create   = date("Y-m-d H:i:s");

if ($id_factura <= 0) {
    echo "Error: id_factura requerido";
    exit;
}

$sql = mysqli_query($con, "UPDATE facturas SET
    id_cliente    = '$id_cliente',
    id_vendedor   = '$id_vendedor',
    estado_factura = 0,
    compra        = '$compra',
    cotizacion    = '$cotizacion',
    doctor        = '$doctor',
    paciente      = '$paciente',
    material      = '$material',
    pago          = '$pago',
    d_factura     = '$d_factura',
    observaciones = '$observaciones',
    status_fact   = 3,
    bloqueo       = 0,
    validacion    = 0,
    date_create   = '$date_create',
    hospital      = '$hospital',
    no_proveedor  = '$no_proveedor'
    WHERE id_factura = $id_factura");

if (!$sql) {
    echo "Error SQL: " . mysqli_error($con);
}
