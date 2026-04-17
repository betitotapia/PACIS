<?php
session_start();
$session_id = session_id();

require_once("../config/db.php");
require_once("../config/conexion.php");

$id_tmp      = isset($_POST['id_tmp']) ? (int)$_POST['id_tmp'] : 0;
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$lote        = isset($_POST['lote']) ? trim($_POST['lote']) : '';
$caducidad   = isset($_POST['caducidad']) ? $_POST['caducidad'] : '';
$cantidad    = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
$costo       = isset($_POST['costo']) ? (float)$_POST['costo'] : 0;

if($id_tmp <= 0){ echo "ID inválido"; exit; }
if($cantidad <= 0){ echo "Cantidad inválida"; exit; }
if($lote === ''){ echo "Captura el lote"; exit; }
if($caducidad === ''){ echo "Captura la caducidad"; exit; }

$descripcion = mysqli_real_escape_string($con, $descripcion);
$lote        = mysqli_real_escape_string($con, $lote);
$caducidad   = mysqli_real_escape_string($con, $caducidad);

$sql = "UPDATE tmp_recepcion
        SET descripcion_tmp='$descripcion',
            lote_tmp='$lote',
            caducidad_tmp='$caducidad',
            cantidad_tmp=$cantidad,
            costo_tmp=$costo
        WHERE id_tmp=$id_tmp
          AND session_id='$session_id'";

echo mysqli_query($con, $sql) ? "OK" : ("Error: ".mysqli_error($con));
