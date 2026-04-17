<?php
session_start();
require_once("../config/db.php");
require_once("../config/conexion.php");

$id_proveedor = isset($_GET['id_proveedor']) ? (int)$_GET['id_proveedor'] : 0;

echo '<option value="">-- Sin orden de compra --</option>';

if ($id_proveedor <= 0) {
    exit;
}

$sql = mysqli_query(
    $con,
    "SELECT id_oc, folio_oc, fecha_oc, estatus, total
     FROM ordenes_compra
     WHERE id_proveedor = $id_proveedor
       AND estatus IN ('ABIERTA','PARCIAL')
     ORDER BY fecha_oc DESC"
);

while ($row = mysqli_fetch_assoc($sql)) {
    $id_oc     = $row['id_oc'];
    $folio_oc  = $row['folio_oc'];
    $fecha_oc  = date("d/m/Y", strtotime($row['fecha_oc']));
    $estatus   = $row['estatus'];
    $total     = number_format($row['total'], 2);

    echo "<option value=\"$id_oc\">$folio_oc - $fecha_oc - $estatus - $total</option>";
}
