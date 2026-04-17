<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

$id_oc = isset($_POST['id_oc']) ? (int)$_POST['id_oc'] : 0;
$id_almacen_post = isset($_POST['id_almacen']) ? (int)$_POST['id_almacen'] : 0;

if ($id_oc <= 0) { echo "OC no válida"; exit; }

// Tomar almacén de la OC si no mandan uno
$q_oc = mysqli_query($con, "SELECT id_almacen FROM ordenes_compra WHERE id_oc = $id_oc LIMIT 1");
if (!$q_oc || mysqli_num_rows($q_oc) == 0) { echo "OC no encontrada"; exit; }
$rw_oc = mysqli_fetch_assoc($q_oc);
$id_almacen_oc = (int)$rw_oc["id_almacen"];

$id_almacen_dest = ($id_almacen_post > 0) ? $id_almacen_post : $id_almacen_oc;

// Limpiar tmp de este usuario
mysqli_query($con, "DELETE FROM tmp_recepcion WHERE session_id = '$session_id'");

// Cargar partidas pendientes
$q = mysqli_query($con, "
  SELECT id_det_oc, referencia, descripcion,
         cantidad_solicitada, cantidad_recibida, costo_unitario
  FROM ordenes_compra_detalle
  WHERE id_oc = $id_oc
");

while($r = mysqli_fetch_assoc($q)){
  $pendiente = (float)$r["cantidad_solicitada"] - (float)$r["cantidad_recibida"];
  if ($pendiente <= 0) continue;

  $id_det_oc = (int)$r["id_det_oc"];
  $ref = mysqli_real_escape_string($con, $r["referencia"]);
  $desc = mysqli_real_escape_string($con, $r["descripcion"]);
  $costo = (float)$r["costo_unitario"];

  mysqli_query($con, "
    INSERT INTO tmp_recepcion
    (id_oc, id_det_oc, referencia_tmp, descripcion_tmp, lote_tmp, caducidad_tmp,
     cantidad_tmp, costo_tmp, id_almacen_tmp, session_id)
    VALUES
    ($id_oc, $id_det_oc, '$ref', '$desc', '', '0000-00-00',
     $pendiente, $costo, $id_almacen_dest, '$session_id')
  ");
}

echo "OK";
