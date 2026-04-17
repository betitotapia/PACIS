<?php
// Igual que oc_ver.php, solo cambia el JS final
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
  header("location: ../../login.php");
  exit;
}
include("../config/db.php");
include("../config/conexion.php");

$id_oc = isset($_GET['id_oc']) ? (int)$_GET['id_oc'] : 0;
if ($id_oc <= 0) { die("OC inválida"); }

$q = mysqli_query($con, "
  SELECT oc.*,
         p.nombre_provedor AS proveedor,
         a.numero_almacen, a.descripcion AS almacen_desc,
         u.nombre AS usuario
  FROM ordenes_compra oc
  INNER JOIN proveedores p ON oc.id_proveedor = p.id_proveedor
  INNER JOIN almacenes a   ON oc.id_almacen   = a.id_almacen
  INNER JOIN users u       ON oc.id_usuario   = u.user_id
  WHERE oc.id_oc = $id_oc
  LIMIT 1
");
$oc = mysqli_fetch_assoc($q);
if(!$oc){ die("No existe la OC"); }

$det = mysqli_query($con, "SELECT * FROM ordenes_compra_detalle WHERE id_oc = $id_oc");

include(dirname(__FILE__).'/res/print_oc_html.php');
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
window.onload = function () {
  const folio = "<?php echo htmlspecialchars($oc['folio_oc']); ?>";
  const el = document.getElementById('contenedor-oc');

  html2pdf()
    .set({
      margin: 0.5,
      filename: 'OC_' + folio + '.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2, useCORS: true },
      jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    })
    .from(el)
    .save()
    .then(() => { window.close(); });
};
</script>
