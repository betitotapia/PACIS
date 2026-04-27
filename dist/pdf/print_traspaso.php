<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
ini_set('display_errors', 0);

include("../config/db.php");
include("../config/conexion.php");

$id_traspaso = isset($_GET['id_traspaso']) ? (int)$_GET['id_traspaso'] : 0;
if ($id_traspaso <= 0) {
    echo "<script>alert('Traspaso no válido'); window.close();</script>";
    exit;
}

$sql_enc = mysqli_query($con, "
    SELECT t.*,
           u.nombre          AS nombre_usuario,
           ao.numero_almacen AS num_origen,
           ao.descripcion    AS desc_origen,
           ad.numero_almacen AS num_destino,
           ad.descripcion    AS desc_destino
    FROM traspasos t
    INNER JOIN users u      ON t.id_usuario         = u.user_id
    INNER JOIN almacenes ao ON t.id_almacen_origen   = ao.id_almacen
    INNER JOIN almacenes ad ON t.id_almacen_destino  = ad.id_almacen
    WHERE t.id_traspaso = $id_traspaso
    LIMIT 1
");

$rw_enc = mysqli_fetch_assoc($sql_enc);
if (!$rw_enc) {
    echo "<script>alert('Traspaso no encontrado'); window.close();</script>";
    exit;
}

$folio        = $rw_enc['folio'];
$fecha_trasp  = date("d/m/Y H:i", strtotime($rw_enc['fecha_traspaso']));
$usuario      = $rw_enc['nombre_usuario'];
$almacen_orig = $rw_enc['num_origen'] . " - " . $rw_enc['desc_origen'];
$almacen_dest = $rw_enc['num_destino'] . " - " . $rw_enc['desc_destino'];
$observaciones = $rw_enc['observaciones'];

$sql_det = mysqli_query($con, "
    SELECT * FROM traspasos_detalle WHERE id_traspaso = $id_traspaso ORDER BY id_det_traspaso
");

include(dirname(__FILE__) . '/res/traspaso_html.php');
?>

<script>
window.onload = function () {
    window.print();
    window.onafterprint = function () { window.close(); };
};
</script>
