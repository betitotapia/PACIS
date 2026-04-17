<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$session_id = session_id();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

$sql = mysqli_query($con, "SELECT * FROM tmp_oc WHERE session_id = '$session_id' ORDER BY id_tmp_oc DESC");

$total = 0;
?>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Referencia</th>
      <th>Descripción</th>
      <th class="text-right">Cantidad</th>
      <th class="text-right">Costo</th>
      <th class="text-right">Importe</th>
      <th width="60">Acción</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$sql || mysqli_num_rows($sql) == 0) { ?>
      <tr><td colspan="6" class="text-center">Sin partidas</td></tr>
    <?php } else {
      while($r = mysqli_fetch_assoc($sql)) {
        $importe = (float)$r['cantidad_tmp'] * (float)$r['costo_tmp'];
        $total += $importe;
    ?>
      <tr>
        <td><?php echo htmlspecialchars($r['referencia_tmp']); ?></td>
        <td><?php echo htmlspecialchars($r['descripcion_tmp']); ?></td>
        <td class="text-right"><?php echo number_format((float)$r['cantidad_tmp'], 2); ?></td>
        <td class="text-right"><?php echo number_format((float)$r['costo_tmp'], 4); ?></td>
        <td class="text-right"><?php echo number_format($importe, 2); ?></td>
        <td class="text-center">
          <button class="btn btn-danger btn-sm" onclick="eliminar_item_oc(<?php echo (int)$r['id_tmp_oc']; ?>)">
            <i class="fa fa-trash">Eliminar</i>
          </button>
        </td>
      </tr>
    <?php } } ?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="4" class="text-right">Total</th>
      <th class="text-right"><?php echo number_format($total, 2); ?></th>
      <th></th>
    </tr>
  </tfoot>
</table>
