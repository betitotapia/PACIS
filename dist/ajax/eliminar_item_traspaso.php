<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

$id_tmp = (int)($_GET['id_tmp'] ?? 0);
if ($id_tmp <= 0) {
    echo "ID inválido.";
    exit;
}

mysqli_query($con, "DELETE FROM tmp_traspaso WHERE id_tmp = $id_tmp AND session_id = '$session_id'");
echo "OK";
