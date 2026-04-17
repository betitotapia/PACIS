<?php
session_start();
$session_id = session_id();

require_once("../config/db.php");
require_once("../config/conexion.php");

$id_oc = isset($_POST['id_oc']) ? (int)$_POST['id_oc'] : 0;
if($id_oc <= 0){ echo "ID inválido"; exit; }

// Ajusta el nombre real de tu tabla/campo:
$sql = "UPDATE ordenes_compra SET estatus = 'CANCELADA' WHERE id_oc = $id_oc";

if(mysqli_query($con, $sql)){
  echo "OK";
}else{
  echo "Error: ".mysqli_error($con);
}
