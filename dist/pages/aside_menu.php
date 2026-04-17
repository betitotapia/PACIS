<?php
/**
 * aside_menu.php (AUTO-ACTIVE)
 * - Ya NO necesitas declarar $active_* en cada página.
 * - Marca "active" y "menu-open" automático por URL.
 * - Evita warnings de variables indefinidas.
 */

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/** =========================
 * Helpers de navegación
 * ========================= */
$__uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$__uriPath = strtolower($__uriPath);

function nav_has($needle) {
  global $__uriPath;
  $needle = strtolower($needle);
  return (strpos($__uriPath, $needle) !== false);
}

function nav_active($needleOrArray) {
  if (is_array($needleOrArray)) {
    foreach ($needleOrArray as $n) {
      if (nav_has($n)) return 'active';
    }
    return '';
  }
  return nav_has($needleOrArray) ? 'active' : '';
}

function nav_open($needles) {
  return nav_active($needles) ? 'menu-open' : '';
}

function nav_circle($activeClass) {
  return ($activeClass === 'active') ? 'bi-record-circle' : 'bi-circle';
}

/** =========================
 * Nivel usuario (solo 1 query)
 * ========================= */
$nivel = 0;
if (isset($_SESSION["user_id"]) && isset($con)) {
  $session_id = (int)$_SESSION["user_id"];
  $sql_usuario = @mysqli_query($con, "SELECT is_admin FROM users WHERE user_id = $session_id LIMIT 1");
  if ($sql_usuario && $rj_usuario = mysqli_fetch_assoc($sql_usuario)) {
    $nivel = (int)$rj_usuario['is_admin'];
  }
}

/** =========================
 * Activos automáticos por módulo
 * Ajusta needles si tu ruta cambia.
 * ========================= */

// VENTAS
$active_remisiones = nav_active(['/ventas/']);      // ../ventas/index.php, ../ventas/nueva.php, etc
$active_nueva      = nav_active(['/nueva_remision', '/ventas/nueva']); // por si existe
$active_cancel     = nav_active(['/ventas/cancel']); // ../ventas/cancel.php
$open_ventas       = nav_open(['/ventas/', '/nueva_remision']);

// ORDENES DE COMPRA
$active_ordenes_compra = nav_active(['/ordenes_compra/']);
$open_oc              = nav_open(['/ordenes_compra/']);

// PRODUCTOS / RECEPCIONES
$active_productos       = nav_active(['/productos/']);     // si aún lo usas
$active_lista_productos = nav_active(['/productos/lista.php', '/productos/lista']);
$open_productos         = nav_open(['/productos/', '/recepciones/']);

//recepciones
$active_recepciones        = nav_active(['/recepciones/']); 
$active_lista_recepciones  = nav_active(['/recepciones/index.php', '/recepciones/index']);
$active_nueva_recepcion    = nav_active(['/recepciones/nueva.php', '/recepciones/nueva']);
$open_recepciones          = nav_open(['/recepciones/']);

// ALMACENES / TERCEROS / USUARIOS
$active_almacenes = nav_active(['/almacenes/']);
$open_almacenes   = nav_open(['/almacenes/']);

$active_terceros   = nav_active(['/terceros/']);
$active_provedores = nav_active(['/terceros/proveedores.php', '/terceros/proveedores']);
$open_terceros     = nav_open(['/terceros/']);

$active_usuarios = nav_active(['/usuarios/']);
$open_usuarios   = nav_open(['/usuarios/']);

?>

<aside class="app-sidebar bg-body-secondary shadow " data-bs-theme="dark" style="background-color:rgb(220, 75, 16) !important; color:#ffffff !important;">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <a href="../index.html" class="brand-link">
      <img
        src="../../../dist/assets/img/pacis_logo.png"
        alt="PACIS"
        class="brand-image opacity-75 shadow"
      />
      <span class="brand-text fw-light">PACIS V.1.0</span>
    </a>
  </div>
  <!--end::Sidebar Brand-->

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>
              Dashboard
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
        </li>

        <!-- ================= VENTAS ================= -->
        <li class="nav-item <?php echo $open_ventas; ?>">
          <a href="#" class="nav-link">
            <i class="bi bi-cart-check-fill"></i>
            <p>
              VENTAS
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <?php $circle = nav_circle($active_remisiones); ?>
              <a href="../ventas/index.php" class="nav-link <?php echo $active_remisiones; ?>">
                <i class="nav-icon bi <?php echo $circle; ?>"></i>
                <p>Remisiones</p>
              </a>
            </li>

            <?php if ($nivel == 1) { ?>
              <li class="nav-item">
                <?php $circle = nav_circle($active_cancel); ?>
                <a href="../ventas/cancel.php" class="nav-link <?php echo $active_cancel; ?>">
                  <i class="nav-icon bi <?php echo $circle; ?>"></i>
                  <p>Canceladas</p>
                </a>
              </li>
            <?php } ?>
          </ul>
        </li>
        <!-- ============== FIN VENTAS ============== -->

        <!-- ============== ORDENES DE COMPRA ============== -->
        <li class="nav-item <?php echo $open_oc; ?>">
          <a href="#" class="nav-link">
            <i class="bi bi-cart-check-fill"></i>
            <p>
              ORDENES DE COMPRA
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <?php $circle = nav_circle($active_ordenes_compra); ?>
              <a href="../ordenes_compra/index.php" class="nav-link <?php echo $active_ordenes_compra; ?>">
                <i class="nav-icon bi <?php echo $circle; ?>"></i>
                <p>Órdenes de Compra</p>
              </a>
            </li>
          </ul>
        </li>
        <!-- ============ FIN ORDENES DE COMPRA ============ -->
        <!-- =========================RECEPCIONE========================= -->      
               <li class="nav-item <?php echo $open_recepciones; ?>">
          <a href="#" class="nav-link ">
            <i class="bi bi-box-seam-fill"></i>
            <p>
              RECEPCIONES
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">

            <?php if ($nivel == 1) { ?>
              <li class="nav-item">
                <?php $circle = nav_circle($active_lista_recepciones); ?>
                <a href="../recepciones/index.php" class="nav-link <?php echo $active_lista_recepciones; ?>">
                  <i class="nav-icon bi <?php echo $circle; ?>"></i>
                  <p>Lista</p>
                </a>
              </li>
            <?php } ?>

            <li class="nav-item">
              <?php $circle = nav_circle($active_nueva_recepcion); ?>
              <a href="../recepciones/nueva.php" class="nav-link <?php echo $active_nueva_recepcion; ?>">
                <i class="nav-icon bi <?php echo $circle; ?>"></i>
                <p>Nueva Recepción</p>
              </a>
            </li>

          </ul>
        </li>

        <!-- =========================FIN RECEPCIONES========================= -->

        <!--==================== FACTURACION ================= -->
        <li class="nav-item">
          <a href="../facturacion/index.php" class="nav-link <?php echo nav_active(['/facturacion/']); ?>">
            <i class="bi bi-receipt-cutoff"></i>
            <p>
              FACTURACIÓN
            </p>
          </a>
        </li>
        <!--==================== FIN FACTURACION ================= -->
        <!-- ================= PRODUCTOS ================= -->
        <li class="nav-item <?php echo $open_productos; ?>">
          <a href="#" class="nav-link ">
            <i class="bi bi-box-seam-fill"></i>
            <p>
              PRODUCTOS
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">


            <li class="nav-item">
              <?php $circle = nav_circle($active_lista_productos); ?>
              <a href="../productos/lista.php" class="nav-link <?php echo $active_lista_productos; ?>">
                <i class="nav-icon bi <?php echo $circle; ?>"></i>
                <p>Lista de productos</p>
              </a>
            </li>

          </ul>
        </li>
        <!-- ============== FIN PRODUCTOS ============== -->

        <!-- ================= ALMACENES ================= -->
        <li class="nav-item <?php echo $open_almacenes; ?>">
          <a href="#" class="nav-link ">
            <i class="nav-icon bi bi-dropbox"></i>
            <p>
              ALMACENES
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <?php $circle = nav_circle($active_almacenes); ?>
              <a href="../almacenes/index.php" class="nav-link <?php echo $active_almacenes; ?>">
                <i class="nav-icon bi <?php echo $circle; ?>"></i>
                <p>Lista</p>
              </a>
            </li>
          </ul>
        </li>
        <!-- ============== FIN ALMACENES ============== -->

        <!-- ================= TERCEROS ================= -->
        <li class="nav-item <?php echo $open_terceros; ?>">
          <a href="#" class="nav-link ">
            <i class="bi bi-people-fill"></i>
            <p>
              TERCEROS
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <?php $circle = nav_circle($active_terceros); ?>
              <a href="../terceros/index.php" class="nav-link <?php echo $active_terceros; ?>">
                <i class="nav-icon bi <?php echo $circle; ?>"></i>
                <p>Clientes</p>
              </a>
            </li>

            <?php if ($nivel == 1) { ?>
              <li class="nav-item">
                <?php $circle = nav_circle($active_provedores); ?>
                <a href="../terceros/proveedores.php" class="nav-link <?php echo $active_provedores; ?>">
                  <i class="nav-icon bi <?php echo $circle; ?>"></i>
                  <p>Proveedores</p>
                </a>
              </li>
            <?php } ?>

          </ul>
        </li>
        <!-- ============== FIN TERCEROS ============== -->

        <!-- ================= USUARIOS ================= -->
        <li class="nav-item <?php echo $open_usuarios; ?>">
          <a href="#" class="nav-link ">
            <i class="bi bi-person-fill"></i>
            <p>
              USUARIOS
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <?php $circle = nav_circle($active_usuarios); ?>
              <a href="../usuarios/index.php" class="nav-link <?php echo $active_usuarios; ?>">
                <i class="nav-icon bi <?php echo $circle; ?>"></i>
                <p>Lista</p>
              </a>
            </li>
          </ul>
        </li>
        <!-- ============== FIN USUARIOS ============== -->

      </ul>
    </nav>
  </div>
</aside>
