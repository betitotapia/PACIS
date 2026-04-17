<?php
// Conexión a la base de datos
require_once("../config/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
require_once("../config/conexion.php"); //Contiene funcion que conecta a la base de datos


$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["id"]);
$iva = intval($data["iva"]);

$sql = "UPDATE detalle_factura SET iva = $iva WHERE id_detalle = $id";

if ($con->query($sql) === TRUE) {
    echo "IVA actualizado a $iva correctamente.";
} else {
    echo "Error al actualizar: " . $con->error;
}

$con->close();

?>
