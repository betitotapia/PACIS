<?php
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos

if (isset($_GET['export']) && $_GET['export'] == 1) {
    $sql = "SELECT * from products GROUP BY referencia"; // ajusta según tus columnas
    $result = mysqli_query($con, $sql);

    $productos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($productos);
    exit;
}
?>
