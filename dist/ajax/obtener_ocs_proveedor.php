<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

$id_proveedor = isset($_GET['id_proveedor']) ? (int)$_GET['id_proveedor'] : 0;
if ($id_proveedor <= 0) { echo json_encode([]); exit; }

$q = mysqli_query($con, "
  SELECT id_oc, folio_oc, id_almacen, estatus, total
  FROM ordenes_compra
  WHERE id_proveedor = $id_proveedor
    AND estatus IN ('ABIERTA','PARCIAL')
  ORDER BY id_oc DESC
  LIMIT 200
");

$data = [];
while($r = mysqli_fetch_assoc($q)){
  $data[] = [
    "id_oc" => (int)$r["id_oc"],
    "folio_oc" => $r["folio_oc"],
    "id_almacen" => (int)$r["id_almacen"],
    "estatus" => $r["estatus"],
    "total" => number_format((float)$r["total"], 2, ".", "")
  ];
}

header("Content-Type: application/json; charset=utf-8");
echo json_encode($data);
