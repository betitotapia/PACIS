<?php
require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos

$codigo = $_POST['codigo'];
$lote = $_POST['lote'];
$caducidad = $_POST['caducidad'];
$referencia = $_POST['referencia'] ?? '';
$operacion = $_POST['operacion'] ?? 'sumar';
$cantidad_ajuste = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
$almacen = $_POST['almacen'] ?? '';

$stmt = $con->prepare("SELECT * FROM products WHERE barcode = ? AND lote = ? and almacen = ?");
$stmt->execute([$codigo, $lote, $almacen]);
$producto = $stmt->fetch();

if ($producto) {
    switch ($operacion) {
        case "sumar":
            $pdo->prepare("UPDATE products SET existencias= existencias + 1 WHERE id = ?")->execute([$producto['id']]);
            echo "Producto actualizado (+1).";
            break;
            
        case "restar":
            if ($producto['cantidad'] > 0) {
                $con->prepare("UPDATE products SET existencias = existencias - 1 WHERE id = ?")->execute([$producto['id']]);
                echo "Producto actualizado (-1).";
            } else {
                echo "Sin existencias para restar.";
            }
            break;
            
        case "ajuste":
            $nueva_cantidad = $producto['existencias'] + $cantidad_ajuste;
            if ($nueva_cantidad >= 0) {
                $con->prepare("UPDATE products SET existencias = ? WHERE id = ?")->execute([$nueva_cantidad, $producto['id']]);
                echo "Ajuste realizado. Nueva cantidad: " . $nueva_cantidad;
            } else {
                echo "Error: La cantidad no puede ser negativa.";
            }
            break;
    }
} else {
    // Verificar si el código existe (aunque sea con otro lote)
    $stmtCod = $con->prepare("SELECT referencia FROM products WHERE barcode = ? LIMIT 1");
    $stmtCod->execute([$codigo]);
    $base = $stmtCod->fetch();

    if ($operacion === "restar") {
        echo "Producto no encontrado para restar.";
    } elseif ($base) {
        // Usar referencia existente
        $con->prepare("INSERT INTO products (barcode, lote, caducidad, referencia, existencias) VALUES (?, ?, ?, ?, ?)")
            ->execute([$codigo, $lote, $caducidad, $base['referencia'], ($operacion === 'ajuste') ? $cantidad_ajuste : 1]);
        echo "Nuevo lote agregado con referencia existente.";
    } elseif (!empty($referencia)) {
        // Usar referencia proporcionada
        $con->prepare("INSERT INTO products (barcode, lote, caducidad, referencia, existencias) VALUES (?, ?, ?, ?, ?)")
        ->execute([$codigo, $lote, $caducidad, $referencia, ($operacion === 'ajuste') ? $cantidad_ajuste : 1]);
        echo "Producto insertado.";
    } else {
        echo "NECESITA_REFERENCIA";
    }
}