<?php
session_start();
require_once("../../config/db.php");
require_once("../../config/conexion.php");

$id = (int)$_POST['id_recepcion'];
$fecha = mysqli_real_escape_string($con, str_replace('T',' ', $_POST['fecha_recepcion']));
$id_proveedor = (int)$_POST['id_proveedor'];

$ref  = $_POST['referencia'];
$desc = $_POST['descripcion'];
$lote = $_POST['lote'];
$cant = $_POST['cantidad'];
$costo= $_POST['costo'];

mysqli_begin_transaction($con);

try {
  mysqli_query($con,"UPDATE recepciones
                     SET fecha_recepcion='$fecha',
                         id_proveedor=$id_proveedor,
                         estatus='EDITADA'
                     WHERE id_recepcion=$id");

  mysqli_query($con,"DELETE FROM recepciones_detalle WHERE id_recepcion=$id");

  for($i=0;$i<count($ref);$i++){
    if(trim($ref[$i])=='') continue;

    $importe = $cant[$i]*$costo[$i];
    mysqli_query($con,"
      INSERT INTO recepciones_detalle
      (id_recepcion, referencia, descripcion, lote, cantidad, costo_unitario)
      VALUES
      ($id,'{$ref[$i]}','{$desc[$i]}','{$lote[$i]}',{$cant[$i]},{$costo[$i]})
    ");
  }

  mysqli_commit($con);
  echo "OK";
} catch(Exception $e){
  mysqli_rollback($con);
  echo "Error";
}
