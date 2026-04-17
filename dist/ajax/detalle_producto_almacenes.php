<?php
// dist/ajax/detalle_producto_almacenes.php
// Ajusta las rutas de conexión según tu proyecto
require_once("../config/db.php");
require_once("../config/conexion.php");

if (!isset($_GET['referencia']) || empty($_GET['referencia'])) {
    echo "<div class='alert alert-danger'>Referencia no recibida.</div>";
    exit;
}

$referencia = mysqli_real_escape_string($con, $_GET['referencia']);

// Consulta: cuánto hay por almacén para esa referencia
$sql = "
    SELECT 
        a.numero_almacen,
        a.descripcion AS nombre_almacen,
        SUM(p.existencias) AS total_existencias
    FROM products p
    INNER JOIN almacenes a ON p.id_almacen = a.id_almacen
    WHERE p.referencia = '$referencia'
    GROUP BY a.id_almacen, a.numero_almacen, a.descripcion
    ORDER BY a.numero_almacen
";

$query = mysqli_query($con, $sql);

if (!$query) {
    echo "<div class='alert alert-danger'>Error en la consulta: " . mysqli_error($con) . "</div>";
    exit;
}

if (mysqli_num_rows($query) == 0) {
    echo "<div class='alert alert-warning'>No hay existencias para la referencia <strong>" . htmlspecialchars($referencia) . "</strong>.</div>";
    exit;
}

?>
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th># Almacén</th>
                <th>Descripción almacén</th>
                <th class="text-right">Existencias</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['numero_almacen']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre_almacen']); ?></td>
                <td class="text-right"><?php echo number_format($row['total_existencias']); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
