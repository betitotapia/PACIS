<?php
// Verifica si un RFC ya está registrado en la tabla clientes.
// GET ?rfc=XXXX  →  { "exists": bool, "nombre": string|null }
header('Content-Type: application/json; charset=utf-8');

require_once('../config/db.php');
require_once('../config/conexion.php');

$rfc = strtoupper(trim($_GET['rfc'] ?? ''));
if ($rfc === '') {
    echo json_encode(['exists' => false, 'nombre' => null]);
    exit;
}

// Excluye el cliente actual en edición (opcional, para reutilizar este endpoint en update)
$excludeId = intval($_GET['excluir_id'] ?? 0);

if ($excludeId > 0) {
    $stmt = $con->prepare("SELECT id_cliente, nombre_cliente FROM clientes WHERE rfc = ? AND id_cliente <> ? LIMIT 1");
    $stmt->bind_param('si', $rfc, $excludeId);
} else {
    $stmt = $con->prepare("SELECT id_cliente, nombre_cliente FROM clientes WHERE rfc = ? LIMIT 1");
    $stmt->bind_param('s', $rfc);
}

$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$con->close();

echo json_encode([
    'exists' => (bool)$row,
    'nombre' => $row ? $row['nombre_cliente'] : null
]);
