<aside class="app-sidebar bg-body-secondary shadow " data-bs-theme="dark" style="background-color:rgb(2, 81, 134) !important; color:#ffffff !important;">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="../index.html" class="brand-link">
            <!--begin::Brand Image-->
            <img
              src="../../../dist/assets/img/AdminLTELogo.png"
              alt="SUMED"
              class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">SUMED V.2.0</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="menu"
              data-accordion="false"
            >
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>
                    Dashboard
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
              </li>
              <?php 
                  if ($active_remisiones=='active'||$active_nueva=='active'||$active_cancel=='active'){
                    $open="menu-open";
                  }
                  else{
                    $open="";

                  }
                  ?>
              <li class="nav-item <?php echo $open; ?>">
                <a href="#" class="nav-link">
                <i class="bi bi-cart-plus"></i>
                  <p>
                   VENTAS
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="../ventas/index.php" class="nav-link <?php echo $active_remisiones; ?>">
                  <?php 
                  if ($active_remisiones=='active'){
                    $circle="bi-record-circle";
                  }
                  else{
                    $circle="bi-circle";

                  }
                  ?>
                      <i class="nav-icon bi <?php echo $circle; ?>"></i>
                      <p>Remisiones</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="../ventas/nueva_remision.php" class="nav-link  <?php echo $active_nueva; ?>">
                    <?php 
                  if ($active_nueva=='active'){
                    $circle="bi-record-circle";
                  }
                  else{
                    $circle="bi-circle";

                  }
                  ?>
                      <i class="nav-icon <?php echo $circle; ?>"></i>
                      <p>Nueva Remisión</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="../ventas/cancel.php" class="nav-link  <?php echo $active_cancel; ?>">
                    <?php 
                  if ($active_cancel=='active'){
                    $circle="bi-record-circle";
                  }
                  else{
                    $circle="bi-circle";

                  }
                  ?>
                      <i class="nav-icon <?php echo $circle; ?>"></i>
                      <p>Canceladas</p>
                    </a>
                  </li>
                </ul>
              </li>
              <?php 
                  if ($active_vehiculos=='active'){
                    $open="menu-open";
                  }
                  else{
                    $open="";;

                  }
                  ?>
                <!--logistica--->

                <li class="nav-item  <?php echo $open; ?>">
                <a href="#" class="nav-link ">
                  <i class="nav-icon bi bi-clipboard-fill"></i>
                  <p>
                    LOGISTICA
                    <span class="nav-badge badge text-bg-secondary me-3">6</span>
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="../logistica/index.php" class="nav-link <?php echo $active_productos; ?>">
                    <?php 
                  if ($active_productos=='active'){
                    $circle="bi-record-circle";
                  }
                  else{
                    $circle="bi-circle";

                  }
                  ?>
                      <i class="nav-icon <?php echo $circle; ?>"></i>
                      <p>Vehículos </p>
                    </a>
                  </li>
                 
                  <li class="nav-item">
                    <a href="../layout/layout-rtl.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Layout RTL</p>
                    </a>
                  </li>
                </ul>
              </li> 

                <!--end-logistica-->


              <li class="nav-item  <?php echo $open; ?>">
                <a href="#" class="nav-link ">
                  <i class="nav-icon bi bi-clipboard-fill"></i>
                  <p>
                    PRODUCTOS
                    <span class="nav-badge badge text-bg-secondary me-3">6</span>
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="../productos/index.php" class="nav-link <?php echo $active_productos; ?>">
                    <?php 
                  if ($active_productos=='active'){
                    $circle="bi-record-circle";
                  }
                  else{
                    $circle="bi-circle";

                  }
                  ?>
                      <i class="nav-icon <?php echo $circle; ?>"></i>
                      <p>Lista</p>
                    </a>
                  </li>
                 
                  <li class="nav-item">
                    <a href="../layout/layout-rtl.html" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Layout RTL</p>
                    </a>
                  </li>
                </ul>
              </li>
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>