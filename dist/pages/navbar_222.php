<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">MiApp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
      aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Enlace</a>
        </li>
      </ul>

      <ul class="navbar-nav mb-2 mb-lg-0">
        <!-- Mensajes -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle position-relative" href="#" id="messagesDropdown" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-chat-left-text"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              3
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="messagesDropdown">
            <li><a class="dropdown-item" href="#">Mensaje 2</a></li>
            <li><a class="dropdown-item" href="#">Mensaje 3</a></li>
          </ul>
        </li>

        <!-- Notificaciones -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationsDropdown" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
              5
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
            <li><a class="dropdown-item" href="#">Notificación 1</a></li>
            <li><a class="dropdown-item" href="#">Notificación 2</a></li>
            <li><a class="dropdown-item" href="#">Notificación 3</a></li>
          </ul>
        </li>

        <!-- Usuario -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <img src="user.jpg" alt="Avatar" width="32" height="32" class="rounded-circle me-2">
            <span class="d-none d-lg-inline text-white">Usuario</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="#">Perfil</a></li>
            <li><a class="dropdown-item" href="#">Configuración</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Cerrar sesión</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
