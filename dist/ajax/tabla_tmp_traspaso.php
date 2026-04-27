<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

$sql = mysqli_query($con, "SELECT * FROM tmp_traspaso WHERE session_id = '$session_id'");
?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Referencia</th>
            <th>Descripción</th>
            <th>Lote</th>
            <th>Caducidad</th>
            <th class="text-right">Cantidad</th>
            <th width="90px">Acciones</th>
        </tr>
    </thead>
    <tbody>
<?php
if (mysqli_num_rows($sql) == 0) {
    echo "<tr><td colspan='6' class='text-center text-muted'>No hay productos agregados</td></tr>";
} else {
    while ($row = mysqli_fetch_assoc($sql)) {
?>
<tr>
    <td>
        <input type="text" class="form-control input-sm"
               value="<?php echo htmlspecialchars($row['referencia_tmp']); ?>" readonly>
    </td>
    <td>
        <input type="text" class="form-control input-sm"
               id="tdesc_<?php echo $row['id_tmp']; ?>"
               value="<?php echo htmlspecialchars($row['descripcion_tmp']); ?>" readonly>
    </td>
    <td>
        <input type="text" class="form-control input-sm"
               id="tlote_<?php echo $row['id_tmp']; ?>"
               value="<?php echo htmlspecialchars($row['lote_tmp']); ?>" readonly>
    </td>
    <td>
        <input type="text" class="form-control input-sm"
               value="<?php echo htmlspecialchars($row['caducidad_tmp'] ?? ''); ?>" readonly>
    </td>
    <td class="text-right" style="width:120px;">
        <input type="number" min="0.001" step="0.001" class="form-control input-sm text-right"
               id="tcant_<?php echo $row['id_tmp']; ?>"
               value="<?php echo (float)$row['cantidad_tmp']; ?>">
    </td>
    <td class="text-center" style="white-space:nowrap;">
        <a href="#" class="btn btn-sm btn-outline-success" title="Actualizar cantidad"
           onclick="actualizar_item_traspaso(<?php echo $row['id_tmp']; ?>); return false;">
            <i class="bi bi-floppy2"></i>
        </a>
        <a href="#" class="btn btn-sm btn-outline-danger" title="Eliminar"
           onclick="eliminar_item_traspaso(<?php echo $row['id_tmp']; ?>); return false;">
            <i class="bi bi-trash"></i>
        </a>
    </td>
</tr>
<?php } } ?>
    </tbody>
</table>
