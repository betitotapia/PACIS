<?php
if (isset($_GET['term'])){
include "../../config/db.php";
include "../../config/conexion.php";
$return_arr = array();
/* If connection to database, run sql statement. */
if ($con)
{
	
	$fetch = mysqli_query($con,"SELECT * FROM clientes WHERE nombre_cliente like '%" . mysqli_real_escape_string($con,($_GET['term'])) . "%' LIMIT 0 ,50"); 
	
	/* Retrieve and store in array the results of the query.*/
 while ($row = mysqli_fetch_array($fetch)) {
		$id_cliente=$row['id_cliente'];
		$row_array['value'] = $row['nombre_cliente'];
		$row_array['id_cliente']=$id_cliente;
		$row_array['nombre_cliente']=$row['nombre_cliente'];
		$row_array['rfc_cliente']=$row['rfc'];
		if($row['telefono']== NULL){
			$vacio="No hay Teléfono";
			$row_array['telefono_cliente']= $vacio;
		}else{
		$row_array['telefono_cliente']=$row['telefono'];
		}
		if($row['email']== NULL){
			$vacio="No hay correo";
			$row_array['emailpred']= $vacio;
		}else{
		$row_array['emailpred']=$row['email'];
		}
		$row_array['calle_cliente']=$row['calle'];
		$row_array['numext_cliente']=$row['num_ext'];
		$row_array['numint_cliente']=$row['num_int'];
		$row_array['colonia_cliente']=$row['colonia'];
		array_push($return_arr,$row_array);
		

    }
	
}

/* Free connection resources. */
mysqli_close($con);

/* Toss back results as json encoded array. */
echo json_encode($return_arr);

}
?>