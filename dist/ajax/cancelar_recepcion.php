<?php
session_start();
require_once("../config/db.php");
require_once("../config/conexion.php");

$id_usuario = (int)$_SESSION['user_id'];
$id_recepcion = isset($_POST['id_recepcion']) ? (int)$_POST['id_recepcion'] : 0;
if ($id_recepcion <= 0) { echo "Recepción inválida"; exit; }

// 1) Traer recepción y validar estado
$qrec = mysqli_query($con, "SELECT estatus, stock_revertido FROM recepciones WHERE id_recepcion=$id_recepcion LIMIT 1");
$rec = mysqli_fetch_assoc($qrec);
if(!$rec){ echo "No existe la recepción"; exit; }

if($rec['estatus'] === 'CANCELADA'){
  echo "La recepción ya está cancelada.";
  exit;
}
if((int)$rec['stock_revertido'] === 1){
  echo "El stock de esta recepción ya fue revertido.";
  exit;
}

// 2) Traer detalle (lo que se recibió)
$qdet = mysqli_query($con, "
  SELECT referencia, lote, id_almacen, cantidad
  FROM recepciones_detalle
  WHERE id_recepcion=$id_recepcion
");
if(!$qdet){ echo "Error detalle: ".mysqli_error($con); exit; }

// 3) Revertir stock con transacción
mysqli_begin_transaction($con);

try {
  // Marcamos cancelada
  $up = "UPDATE recepciones
         SET estatus='CANCELADA',
             cancelada_at=NOW(),
             cancelada_por=$id_usuario
         WHERE id_recepcion=$id_recepcion";
  if(!mysqli_query($con,$up)) throw new Exception(mysqli_error($con));

  // Por cada renglón, restamos existencias en products
  while($d = mysqli_fetch_assoc($qdet)){
    $referencia = mysqli_real_escape_string($con, $d['referencia']);
    $lote       = mysqli_real_escape_string($con, $d['lote']);
    $id_almacen = (int)$d['id_almacen'];
    $cantidad   = (float)$d['cantidad'];

    // Buscar el producto exacto por referencia + lote + almacén
    $qp = mysqli_query($con, "
      SELECT id_producto, existencias
      FROM products
      WHERE referencia='$referencia'
        AND lote='$lote'
        AND id_almacen=$id_almacen
      LIMIT 1
    ");
    $p = mysqli_fetch_assoc($qp);

    if(!$p){
      // Si no existe el producto, algo está desalineado; para ser seguros, abortamos
      throw new Exception("No se encontró producto para revertir: $referencia / $lote / almacén $id_almacen");
    }

    // Validación para no irnos negativo (puedes cambiar esto si quieres permitirlo)
    if(((float)$p['existencias'] - $cantidad) < 0){
      throw new Exception("No hay existencias suficientes para revertir ($referencia / $lote). Exist: ".$p['existencias']." a revertir: ".$cantidad);
    }

    $id_producto = (int)$p['id_producto'];

    $upd_stock = "UPDATE products
                 SET existencias = existencias - $cantidad,
                     ultima_modificacion = NOW()
                 WHERE id_producto=$id_producto";
    if(!mysqli_query($con,$upd_stock)) throw new Exception(mysqli_error($con));
  }

  // Marcar que ya se revirtió inventario (anti doble cancelación)
  $flag = "UPDATE recepciones SET stock_revertido=1 WHERE id_recepcion=$id_recepcion";
  if(!mysqli_query($con,$flag)) throw new Exception(mysqli_error($con));

  mysqli_commit($con);
  echo "OK";
} catch (Exception $e) {
  mysqli_rollback($con);
  echo "Error: ".$e->getMessage();
}
