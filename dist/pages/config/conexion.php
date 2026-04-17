<?php
	
    
    $con=@mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if(!$con){
        die("imposible conectarse: ".mysqli_error($con));
    }
    if (@mysqli_connect_errno()) {
        die("Conexión falló: ".mysqli_connect_errno()." : ". mysqli_connect_error());
    }

$host = '192.185.131.136';
$db   = 'andres58_pacis';
$user = 'andres58_sistemas';
$pass = 'Pacis#.2025';
$charset = 'utf8mb4';

// Configuración de rutas

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Registrar error en un archivo log
    error_log("Error de conexión: " . $e->getMessage(), 3, ROOT_PATH . '/error.log');
    die("Error de conexión. Por favor intente más tarde.");
}

    // $con2=@mysqli_connect(DB_HOST2, DB_USER2, DB_PASS2, DB_NAME2);
    // if(!$con2){
    //     die("imposible conectarse: ".mysqli_error($con2));
    // }
    // if (@mysqli_connect_errno()) {
    //     die("Conexión falló: ".mysqli_connect_errno()." : ". mysqli_connect_error());
    // }

?>
