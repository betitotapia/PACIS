<?php
require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos

$codigo = $_GET['codigo'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE barcode = ?");
$stmt->execute([$codigo]);
echo $stmt->rowCount() > 0 ? "existe" : "nuevo";
