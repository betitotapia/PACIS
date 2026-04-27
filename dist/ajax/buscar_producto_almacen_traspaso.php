<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    echo "<tr><td colspan='6'>Sesión inválida.</td></tr>";
    exit;
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/conexion.php";

$referencia        = trim($_POST['referencia'] ?? '');
$id_almacen_origen = (int)($_POST['id_almacen_origen'] ?? 0);

if ($referencia === '') {
    echo "<tr><td colspan='6' class='text-center text-muted'>Ingresa una referencia para buscar.</td></tr>";
    exit;
}
if ($id_almacen_origen <= 0) {
    echo "<tr><td colspan='6' class='text-center text-warning'>Selecciona el almacén de origen primero.</td></tr>";
    exit;
}

$ref_esc = mysqli_real_escape_string($con, $referencia);

$q = mysqli_query($con, "
    SELECT id_producto, referencia, descripcion, lote, caducidad, existencias
    FROM products
    WHERE (referencia = '$ref_esc' OR cve_alterna_1 = '$ref_esc' OR cve_alterna_2 = '$ref_esc')
      AND id_almacen = $id_almacen_origen
      AND estatus = 1
      AND existencias > 0
    ORDER BY lote
");

if (mysqli_num_rows($q) === 0) {
    echo "<tr><td colspan='6' class='text-center text-danger'>No se encontraron lotes disponibles para esa referencia en el almacén seleccionado.</td></tr>";
    exit;
}

while ($row = mysqli_fetch_assoc($q)) {
    $id  = (int)$row['id_producto'];
    $ref = htmlspecialchars($row['referencia']);
    $desc = htmlspecialchars($row['descripcion']);
    $lote = htmlspecialchars($row['lote']);
    $cad  = htmlspecialchars($row['caducidad'] ?? '');
    $exist = number_format((float)$row['existencias'], 2);
    $ref_js  = addslashes($row['referencia']);
    $lote_js = addslashes($row['lote']);
    echo "
    <tr>
        <td>$ref</td>
        <td>$desc</td>
        <td>$lote</td>
        <td>$cad</td>
        <td class='text-right'><strong>$exist</strong></td>
        <td style='width:110px;'>
            <input type='number' id='cant_lote_$id' class='form-control form-control-sm text-right'
                   min='0.001' step='0.001' value='1'
                   style='width:100%;'>
        </td>
        <td style='width:90px;'>
            <button type='button' class='btn btn-success btn-sm'
                    onclick=\"agregar_lote_traspaso('$ref_js','$lote_js',$id);\">
                <i class='bi bi-plus-lg'></i> Agregar
            </button>
        </td>
    </tr>";
}
