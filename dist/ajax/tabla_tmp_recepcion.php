<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

// Obtener productos temporales de la recepción
$sql = mysqli_query($con, "SELECT * FROM tmp_recepcion WHERE session_id = '$session_id'");
?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Referencia</th>
            <th>Descripción</th>
            <th>Lote</th>
            <th>Caducidad</th>
            <th class="text-right">Cantidad</th>
            <th class="text-right">Costo</th>
            <th width="40px">Acciones</th>
        </tr>
    </thead>
   <tbody>
<?php
if (mysqli_num_rows($sql) == 0) {
  echo "<tr><td colspan='8' class='text-center'>No hay productos agregados</td></tr>";
} else {
  while ($row = mysqli_fetch_assoc($sql)) {
?>
<tr>
  <td>
    <input type="text" class="form-control input-sm"
           value="<?php echo htmlspecialchars($row['referencia_tmp']); ?>"
           readonly>
  </td>

  <td>
    <input type="text" class="form-control input-sm"
           id="desc_<?php echo $row['id_tmp']; ?>"
           value="<?php echo htmlspecialchars($row['descripcion_tmp']); ?>">
  </td>

  <td>
    <input type="text" class="form-control input-sm"
           id="lote_<?php echo $row['id_tmp']; ?>"
           value="<?php echo htmlspecialchars($row['lote_tmp']); ?>"
           placeholder="Captura lote">
  </td>

  <td>
    <input type="date" class="form-control input-sm"
           id="cad_<?php echo $row['id_tmp']; ?>"
           value="<?php echo htmlspecialchars($row['caducidad_tmp']); ?>">
  </td>

  <td class="text-right" style="width:110px;">
    <input type="number" min="1" class="form-control input-sm text-right"
           id="cant_<?php echo $row['id_tmp']; ?>"
           value="<?php echo (int)$row['cantidad_tmp']; ?>">
  </td>

  <td class="text-right" style="width:130px;">
    <input type="number" min="0" step="0.01" class="form-control input-sm text-right"
           id="costo_<?php echo $row['id_tmp']; ?>"
           value="<?php echo (float)$row['costo_tmp']; ?>">
  </td>

  <td class="text-center" style="white-space:nowrap;">
    <a href="#"
       class="btn btn-default btn-sm"
       title="Guardar cambios"
       onclick="actualizar_item_recepcion(<?php echo $row['id_tmp']; ?>); return false;">
      <i class="fa fa-save text-success"></i>
    </a>

    <a href="#"
       class="btn btn-default btn-sm"
       title="Eliminar"
       onclick="eliminar_item_recepcion(<?php echo $row['id_tmp']; ?>); return false;">
      <i class="fa fa-trash text-danger"></i>
    </a>
  </td>
</tr>
<?php
  }
}
?>
</tbody>
</table>
