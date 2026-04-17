<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../login");
	exit;
	}
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
$barcode=$_POST['codigo'];
$lote = $_POST['lote'];
$caducidad = $_POST['caducidad'];
$referencia = $_POST['referencia'] ?? '';
$operacion = $_POST['operacion'] ?? 'sumar';
$descripcion = $_POST['descripcion'] ?? '';
$costo = isset($_POST['costo']) ? (float)$_POST['costo'] : 0;
$precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0;
$cantidad_ajuste = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
$id_usuario=$_SESSION['user_id'];
$datos = $pdo->query("SELECT * FROM users WHERE user_id = ".$id_usuario."")->fetch(PDO::FETCH_ASSOC);
$almacen = $_POST['almacen'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM products WHERE barcode = ? AND lote = ? AND id_almacen = ?");;
$stmt->execute([$barcode, $lote, $almacen]);
$producto = $stmt->fetch();
//echo "<script>console.log('".$almacen."')</script>";
if ($producto) {
    $id_producto = $producto['id_producto'];
    switch ($operacion) {
        case "sumar":
            $pdo->prepare("UPDATE products SET existencias = existencias + 1, ultima_modificacion = NOW() WHERE id_producto = ?")->execute([$producto['id_producto']]);
            $pdo->prepare("INSERT INTO movimientos (id_producto, cantidad, tipo_movimiento, id_usuario, date_created) VALUES (?, ?, ?, ?, NOW())")
            ->execute([$id_producto, 1,1,$id_usuario ]);
            echo "Producto actualizado (+1).";
            break;
            
        case "restar":
            if ($producto['existencias'] > 0) {
                $pdo->prepare("UPDATE products SET existencias = existencias - 1, ultima_modificacion = NOW() WHERE id_producto = ?")->execute([$producto['id_producto']]);
                $pdo->prepare("INSERT INTO movimientos (id_producto, cantidad, tipo_movimiento, id_usuario, date_created) VALUES (?, ?, ?, ?, NOW())")
            ->execute([$id_producto, 1,2,$id_usuario]);
                echo "Producto actualizado (-1).";
            } else {
                echo "Sin existencias para restar.";
            }
            break;
          

        case "ajuste":
    $nueva_cantidad = $producto['existencias'] + $cantidad_ajuste;
    if ($nueva_cantidad >= 0) {
        if($cantidad_ajuste > 0){ // Cambiado de $nueva_cantidad a $cantidad_ajuste
           $tipo_movimiento=3; // Ajuste de entrada
        }
        else{
            $tipo_movimiento=4; // Ajuste de salida
        }
        $pdo->prepare("UPDATE products SET existencias = ?, ultima_modificacion = NOW() WHERE id_producto = ?")->execute([$nueva_cantidad, $producto['id_producto']]);
        $pdo->prepare("INSERT INTO movimientos (id_producto, cantidad, tipo_movimiento, id_usuario, date_created) VALUES (?, ?, ?, ?, NOW())")
        ->execute([$id_producto, abs($cantidad_ajuste), $tipo_movimiento, $id_usuario]); // Usamos abs() para guardar la cantidad positiva
        echo "Ajuste realizado. Nueva cantidad: " . $nueva_cantidad;
    }else{
                echo "Error: La cantidad no puede ser negativa.";
            }
            break;
        }
}else {
    // Verificar si el código existe (aunque sea con otro lote)
    //$stmtCod = $pdo->prepare("SELECT * FROM products WHERE barcode = ? LIMIT 1");
    $stmtCod = $pdo->prepare("SELECT * FROM products WHERE LEFT(barcode, 16) = LEFT(?, 16) LIMIT 1");
    $stmtCod->execute([$barcode]);
    $base = $stmtCod->fetch();
    $id_usuario=$_SESSION['user_id'];

    if ($operacion === "restar") {
        echo "Producto no encontrado para restar.";
    } elseif ($base) {
        
        // Usar referencia existente 
        $pdo->prepare("INSERT INTO products (barcode,referencia, descripcion, existencias, lote, caducidad, costo, precio_producto, id_almacen,estatus,ultima_modificacion) VALUES (?, ?, ?, ?, ?,?,?,?,?,1,NOW())")
            ->execute([$barcode,$base['referencia'], $base['descripcion'],  ($operacion === 'ajuste') ? $cantidad_ajuste : 1,$lote, $caducidad, $base['costo'], $base['precio_producto'],  $almacen]);
            $id_insertado = $pdo->lastInsertId(); 

        $pdo->prepare("INSERT INTO movimientos (id_producto, cantidad, tipo_movimiento, id_usuario, date_created) VALUES (?, ?, ?, ?, NOW())")
        ->execute([$id_insertado, 1,1,$id_usuario ]);
        echo "Nuevo lote agregado con referencia existente.";

    } elseif (!empty($referencia)) {

        if ($operacion === 'ajuste') {

             $pdo->prepare("INSERT INTO products (barcode,referencia, descripcion, existencias, lote, caducidad, costo, precio_producto, id_almacen,estatus,ultima_modificacion) VALUES (?, ?, ?, ?, ?, ?,?,?,?,1,NOW())")
        ->execute([$barcode, $referencia, $descripcion, $cantidad_ajuste, $lote, $caducidad, $costo, $precio, $almacen]);
        $id_insertado = $pdo->lastInsertId(); 

        $pdo->prepare("INSERT INTO movimientos (id_producto, cantidad, tipo_movimiento, id_usuario, date_created) VALUES (?, ?, ?, ?, NOW())")
        ->execute([$id_insertado, $cantidad_ajuste,3,$id_usuario ]);
        echo "Producto insertado.";

        }else{       // Usar referencia proporcionada
        $pdo->prepare("INSERT INTO products (barcode,referencia, descripcion, existencias, lote, caducidad, costo, precio_producto, id_almacen,estatus,ultima_modificacion) VALUES (?, ?, ?, ?,?, ?, ?,?,?,1,NOW())")
        ->execute([$barcode,$referencia,$descripcion,1, $lote, $caducidad,$costo, $precio, $almacen]);
        $id_insertado = $pdo->lastInsertId(); 

        $pdo->prepare("INSERT INTO movimientos (id_producto, cantidad, tipo_movimiento, id_usuario, date_created) VALUES (?, ?, ?, ?, NOW())")
        ->execute([$id_insertado, 1,1,$id_usuario ]);
        echo "Producto insertado.";
        }

    } else {
        echo "NECESITA_REFERENCIA";
    }
}