<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: ../login");
    exit;
}

require_once("../../config/db.php");
require_once("../../config/conexion.php");
include("../header.php");
?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse sidebar-dark-info bg-body-tertiary">
<div class="app-wrapper">
<?php
include("../navbar.php");
include("../aside_menu.php");
?>
<main class="app-main">

  <div class="app-content-header">
    <div class="container-fluid">
      <h4><i class="bi bi-arrow-left-right"></i> Traspasos entre Almacenes</h4>
      <a href="nueva.php" class="btn btn-success pull-right">
        <i class="fa fa-plus"></i> Nuevo Traspaso
      </a>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <div class="card card-primary card-outline">
        <div class="card-body table-responsive">
          <div id="traspasos_ajax">
            <!-- AJAX content loaded here -->
          </div>
        </div>
      </div>

    </div>
  </div>

</main>
</div>

<script type="text/javascript" src="../../js/VentanaCentrada.js"></script>
<script type="text/javascript" src="../../js/lista_traspasos.js"></script>
<?php include("../footer.php"); ?>
</body>
</html>
