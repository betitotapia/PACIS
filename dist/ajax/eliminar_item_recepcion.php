<?php
session_start();
$session_id = session_id();

require_once("../config/db.php");
require_once("../config/conexion.php");

$id_tmp = isset($_REQUEST['id_tmp']) ? (int)$_REQUEST['id_tmp'] : 0;

if ($id_tmp <= 0) {
    echo "ID inválido.";
    exit;
}

// Borramos solo el renglón del usuario actual
$sql = "DELETE FROM tmp_recepcion WHERE id_tmp = $id_tmp AND session_id = '$session_id'";

if (mysqli_query($con, $sql)) {
    echo "OK";
} else {
    echo "Error al eliminar: " . mysqli_error($con);
}
