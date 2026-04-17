<?php

require_once ("../config/db.php");
require_once ("../config/conexion.php");

$sql_productos="SELECT * FROM products  order by ultima_modificacion DESC";
$query = mysqli_query($con, $sql_productos);

if (!$query){
echo json_encode(["error"=>mysqli_error($con)]);
exit;
}
$productos=[];

while ($row = mysqli_fetch_assoc($query)) {
    $productos[] = $row;
}


echo json_encode($productos);
?>
