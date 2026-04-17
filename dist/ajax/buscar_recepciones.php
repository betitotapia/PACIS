<?php
require_once ("../config/db.php");
require_once ("../config/conexion.php");

$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';

if ($action == 'ajax') {

  // Calcula total desde detalle (si tu recepciones NO tiene campo total)
  $sql = mysqli_query($con, "
    SELECT 
      r.id_recepcion,
      r.folio,
      r.fecha_recepcion,
      r.observaciones,
      r.id_proveedor,
      r.id_almacen,
      r.id_usuario,
      r.estatus,
      p.nombre_provedor,
      a.numero_almacen,
      a.descripcion AS almacen_desc,
      u.nombre AS usuario_nombre,
      IFNULL(SUM(d.cantidad * d.costo_unitario),0) AS total
    FROM recepciones r
    INNER JOIN proveedores p ON r.id_proveedor = p.id_proveedor
    INNER JOIN almacenes a   ON r.id_almacen   = a.id_almacen
    INNER JOIN users u       ON r.id_usuario   = u.user_id
    LEFT JOIN recepciones_detalle d ON r.id_recepcion = d.id_recepcion
    GROUP BY r.id_recepcion
    ORDER BY r.id_recepcion DESC
  ");
?>
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="recepcionesTable">
      <thead>
        <tr>
          <th>Folio</th>
          <th>Fecha</th>
          <th>Proveedor</th>
          <th>Almacén</th>
          <th>Usuario</th>
          <th class="text-right">Total</th>
          <th width="220">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($sql)) { ?>
          <tr>
            <td><?php echo htmlspecialchars($row['folio']); ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($row['fecha_recepcion'])); ?></td>
            <td><?php echo htmlspecialchars($row['nombre_provedor']); ?></td>
            <td><?php echo $row['numero_almacen']." - ".htmlspecialchars($row['almacen_desc']); ?></td>
            <td><?php echo htmlspecialchars($row['usuario_nombre']); ?></td>
            <td class="text-right"><?php echo number_format($row['total'], 2); ?></td>
            <td class="text-center">

              <!-- Ver/Generar acuse (tu html2pdf) -->
              <a class="btn btn-default bg_icons-gray btn-scale"
                 title="Acuse PDF"
                 href="#"
                 onclick="VentanaCentrada('../../pdf/recepcion_pdf.php?id_recepcion=<?php echo (int)$row['id_recepcion']; ?>','Acuse','','800','600','true'); return false;">
                <ion-icon name="download" class="icons-white"></ion-icon>
              </a>

              <!-- (Opcional) botón detalle en pantalla -->
              <a class="btn btn-default bg_icons-purple btn-scale"s
                 title="Ver detalle"
                 href="#"
                  onclick="VentanaCentrada('../../pdf/ver_recepcion_pdf.php?id_recepcion=<?php echo (int)$row['id_recepcion']; ?>','Acuse','','800','600','true'); return false;">
                 <ion-icon name="eye" class="icons-white"></ion-icon>
              </a>
            <!-- Imprimir directo -->
            <a class="btn btn-default bg_icons-purple btn-scale"
              title="Imprimir recepción"
              href="#"
              onclick="VentanaCentrada(
                '../../pdf/print_recepcion.php?id_recepcion=<?php echo (int)$row['id_recepcion']; ?>',
                'Print','','800','600','true'
              ); return false;">
              <ion-icon name="print-outline" class="icons-white"></ion-icon>
            </a>
            </td>
            <td class="text-center">

  <!-- Editar -->
  <?php if ($row['estatus'] !== 'CANCELADA') { ?>
    <a class="btn btn-default bg_icons-purple btn-scale"
       title="Editar recepción"
       href="editar.php?id_recepcion=<?php echo (int)$row['id_recepcion']; ?>">
       <ion-icon name="create" class="icons-white"></ion-icon>
    </a>
  <?php } ?>

  <!-- Acuse PDF -->

  <!-- Cancelar -->
  <?php if ($row['estatus'] !== 'CANCELADA') { ?>
    <a class="btn btn-default bg_icons-gray btn-scale"
       title="Cancelar recepción"
       href="#"
       onclick="cancelar_recepcion(<?php echo (int)$row['id_recepcion']; ?>); return false;">
      
      <ion-icon name="close" class="icons-white"></ion-icon>
    </a>
  <?php } ?>

</td>

          </tr>
        <?php } ?>
      </tbody>
    </table>

    <script>
      var tabla = document.querySelector("#recepcionesTable");
      var dataTable = new DataTable(tabla);
    </script>
  </div>
<?php
}
?>
