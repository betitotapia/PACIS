<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    echo json_encode(['ok' => false, 'error' => 'auth']);
    exit;
}

require_once('../config/db.php');
require_once('../config/conexion.php');

// Crea la tabla si aún no existe (primera ejecución)
mysqli_query($con, "CREATE TABLE IF NOT EXISTS notificaciones (
  id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
  tipo            ENUM('remision','factura','recepcion','traspaso') NOT NULL,
  mensaje         VARCHAR(255) NOT NULL,
  id_referencia   INT NOT NULL DEFAULT 0,
  folio           VARCHAR(50) DEFAULT NULL,
  leida           TINYINT(1) NOT NULL DEFAULT 0,
  fecha_creacion  DATETIME NOT NULL DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$action = $_GET['action'] ?? $_POST['action'] ?? 'lista';

if ($action === 'marcar_leida') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        mysqli_query($con, "UPDATE notificaciones SET leida=1 WHERE id_notificacion=$id");
    }
    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'marcar_todas') {
    mysqli_query($con, "UPDATE notificaciones SET leida=1 WHERE leida=0");
    echo json_encode(['ok' => true]);
    exit;
}

// Lista de no leídas (máx. 20, más recientes primero)
$q = mysqli_query($con, "SELECT * FROM notificaciones WHERE leida=0 ORDER BY fecha_creacion DESC LIMIT 20");
$items = [];
while ($r = mysqli_fetch_assoc($q)) {
    $items[] = $r;
}

echo json_encode(['ok' => true, 'total' => count($items), 'items' => $items]);
