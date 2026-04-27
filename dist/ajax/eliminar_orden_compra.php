<?php
session_start();
$session_id = session_id();

require_once("../config/db.php");
require_once("../config/conexion.php");

$id_oc = isset($_POST['id_oc']) ? (int)$_POST['id_oc'] : 0;
if($id_oc <= 0){ echo "ID inválido"; exit; }

// Verifica que la OC exista y no esté cerrada
$q_est = mysqli_query($con, "SELECT estatus FROM ordenes_compra WHERE id_oc = $id_oc LIMIT 1");
$r_est = mysqli_fetch_assoc($q_est);
if (!$r_est) { echo "Orden de compra no encontrada."; exit; }
if ($r_est['estatus'] === 'CERRADA') {
    echo "No se puede cancelar una orden de compra ya cerrada.";
    exit;
}
if ($r_est['estatus'] === 'CANCELADA') {
    echo "La orden de compra ya está cancelada.";
    exit;
}

// Verifica que no tenga productos ya recibidos
$q_rec = mysqli_query($con, "SELECT COALESCE(SUM(cantidad_recibida),0) AS total FROM ordenes_compra_detalle WHERE id_oc = $id_oc");
$r_rec = mysqli_fetch_assoc($q_rec);
if ((float)$r_rec['total'] > 0) {
    echo "No se puede cancelar: la orden tiene productos ya recibidos. Cancela primero las recepciones asociadas.";
    exit;
}

$sql = "UPDATE ordenes_compra SET estatus = 'CANCELADA' WHERE id_oc = $id_oc";
if(mysqli_query($con, $sql)){
    echo "OK";
}else{
    echo "Error al cancelar la orden.";
}
