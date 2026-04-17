<?php
session_start();
$session_id = session_id();

$session_id = session_id();


require_once("../config/db.php");
require_once("../config/conexion.php");

$referencia  = isset($_POST['referencia']) ? trim($_POST['referencia']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$cantidad    = isset($_POST['cantidad']) ? (float)$_POST['cantidad'] : 0;
$costo       = isset($_POST['costo']) ? (float)$_POST['costo'] : 0;

if ($referencia === '') { echo "Captura la referencia."; exit; }
if ($descripcion === '') { echo "Captura la descripción."; exit; }
if ($cantidad <= 0) { echo "Cantidad inválida."; exit; }

$referencia  = mysqli_real_escape_string($con, $referencia);
$descripcion = mysqli_real_escape_string($con, $descripcion);

mysqli_query($con, "
  INSERT INTO tmp_oc
  (referencia_tmp, descripcion_tmp, cantidad_tmp, costo_tmp, id_producto_tmp, session_id)
  VALUES
  ('$referencia', '$descripcion', $cantidad, $costo, NULL, '$session_id')
");

echo "OK";
