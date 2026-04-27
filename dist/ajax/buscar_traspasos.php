<?php
require_once("../config/db.php");
require_once("../config/conexion.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == 'ajax') {
    $sql = mysqli_query($con, "
        SELECT
            t.id_traspaso,
            t.folio,
            t.fecha_traspaso,
            t.observaciones,
            t.estatus,
            u.nombre          AS usuario_nombre,
            ao.numero_almacen AS num_origen,
            ao.descripcion    AS desc_origen,
            ad.numero_almacen AS num_destino,
            ad.descripcion    AS desc_destino,
            IFNULL(SUM(d.cantidad), 0) AS total_piezas
        FROM traspasos t
        INNER JOIN users u       ON t.id_usuario         = u.user_id
        INNER JOIN almacenes ao  ON t.id_almacen_origen   = ao.id_almacen
        INNER JOIN almacenes ad  ON t.id_almacen_destino  = ad.id_almacen
        LEFT  JOIN traspasos_detalle d ON t.id_traspaso = d.id_traspaso
        GROUP BY t.id_traspaso
        ORDER BY t.id_traspaso DESC
    ");
?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="traspasosTable">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Usuario</th>
                    <th class="text-right">Piezas</th>
                    <th>Estatus</th>
                    <th width="120">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($sql)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['folio']); ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($row['fecha_traspaso'])); ?></td>
                    <td><?php echo $row['num_origen'] . " - " . htmlspecialchars($row['desc_origen']); ?></td>
                    <td><?php echo $row['num_destino'] . " - " . htmlspecialchars($row['desc_destino']); ?></td>
                    <td><?php echo htmlspecialchars($row['usuario_nombre']); ?></td>
                    <td class="text-right"><?php echo number_format((float)$row['total_piezas'], 2); ?></td>
                    <td>
                        <span class="badge bg-<?php echo ($row['estatus'] === 'CANCELADO') ? 'danger' : 'success'; ?>">
                            <?php echo htmlspecialchars($row['estatus']); ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <a class="btn btn-default bg_icons-purple btn-scale"
                           title="Imprimir traspaso"
                           href="#"
                           onclick="VentanaCentrada('../../pdf/print_traspaso.php?id_traspaso=<?php echo (int)$row['id_traspaso']; ?>','Traspaso_<?php echo (int)$row['folio']; ?>','','860','650','true'); return false;">
                            <ion-icon name="print-outline" class="icons-white"></ion-icon>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <script>
            var tablaT = document.querySelector("#traspasosTable");
            if (tablaT) new DataTable(tablaT);
        </script>
    </div>
<?php
}
?>
