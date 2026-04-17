<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: ./login");
    exit;
}

$active_productos="";
$active_recepciones="active";
$active_ordenes_compra="";
$active_borrador="";
$active_nueva="";
$active_remisiones="";
$active_vehiculos="";
$active_cancel="";
$active_almacenes="";
$active_usuarios="";
$active_terceros="";


require_once("../../config/db.php");
require_once("../../config/conexion.php");
include("../header.php");

?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info  bg-body-tertiary">
<div class="app-wrapper">
<?php
include("../navbar.php");
include("../aside_menu.php");
?>
<main class="app-main">

  <div class="app-content-header">
    <div class="container-fluid">
      <h4><i class="fa fa-inbox"></i> Recepciones</h4>
      <a href="nueva.php" class="btn btn-success pull-right">
        <i class="fa fa-plus"></i> Nueva recepción
      </a>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <div class="card card-primary card-outline">
        <div class="card-body table-responsive">

          <div id="recepciones_ajax">
            <!-- AJAX content will be loaded here -->
          </div>

        </div>
      </div>

    </div>
  </div>

</main>
</div>
<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
<script type="text/javascript" src="../../js/lista_recepciones.js"></script>
<?php include("../footer.php"); ?>
</body>
</html>
