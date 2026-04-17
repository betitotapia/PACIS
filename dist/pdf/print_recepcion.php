<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
ini_set('display_errors', 1);

/* Conexión */
include("../config/db.php");
include("../config/conexion.php");

/* Datos por GET */
$id_recepcion = isset($_GET['id_recepcion']) ? intval($_GET['id_recepcion']) : 0;
if ($id_recepcion <= 0) {
    echo "<script>alert('Recepción no válida'); window.close();</script>";
    exit;
}

/* Encabezado de la recepción */
$sql_enc = mysqli_query($con,"
    SELECT r.*,
           u.nombre          AS nombre_usuario,
           p.nombre_provedor AS nombre_proveedor,
           a.numero_almacen,
           a.descripcion     AS nombre_almacen
    FROM recepciones r
    INNER JOIN users u       ON r.id_usuario   = u.user_id
    INNER JOIN proveedores p ON r.id_proveedor = p.id_proveedor
    INNER JOIN almacenes a   ON r.id_almacen   = a.id_almacen
    WHERE r.id_recepcion = '$id_recepcion'
    LIMIT 1
");

$rw_enc = mysqli_fetch_assoc($sql_enc);
if (!$rw_enc) {
    echo "<script>alert('No se encontró la recepción'); window.close();</script>";
    exit;
}

$folio           = $rw_enc['folio'];
$fecha_rec       = date("d/m/Y H:i", strtotime($rw_enc['fecha_recepcion']));
$usuario         = $rw_enc['nombre_usuario'];
$proveedor       = $rw_enc['nombre_proveedor'];
$almacen         = $rw_enc['numero_almacen'].' - '.$rw_enc['nombre_almacen'];
$observaciones   = $rw_enc['observaciones'];

/* Detalle de la recepción */
$sql_det = mysqli_query($con,"
    SELECT *
    FROM recepciones_detalle
    WHERE id_recepcion = '$id_recepcion'
");
?>
<?php
// Plantilla HTML que usará html2pdf (similar a print_remision_html.php)
include(dirname(__FILE__).'/res/print_recepcion_html.php');
?>

<input type="hidden" id="folio_recepcion" value="<?php echo $folio; ?>">

<script>
window.onload = function () {
  // abrir diálogo de impresión del navegador
  window.print();

  // opcional: cerrar ventana después de imprimir
  window.onafterprint = function () {
    window.close();
  };
};
</script>
