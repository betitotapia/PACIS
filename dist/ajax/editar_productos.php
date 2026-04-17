<?php
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/*Inicia validacion del lado del servidor*/
	
		/* Connect To Database*/
		require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
		require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
		// escaping, additionally removing everything that could be (html/javascript-) code
		$id_producto=intval($_POST["id_producto"]);
        $referencia=$_POST["referencia"];
        $descripcion=$_POST["descripcion"];
		$lote=$_POST["lote"];
		$caducidad=$_POST["caducidad"];
		$existencia=intval($_POST["existencia"]);
		$costo=$_POST["costo"];
        $precio=$_POST["precio"];
        
		
		
	    //echo "<script>console.log('work:".$codigo." ".$referencia." ".$nombre." ".$existencia." ".$id_producto."');</script>";
		$sql="UPDATE products SET referencia='".$referencia."', descripcion='".$descripcion."', lote='".$lote."', caducidad='".$caducidad."', costo='".$costo."',  precio_producto='".$precio."' WHERE id_producto='".$id_producto."'";
		$query_update = mysqli_query($con,$sql);
	echo "<script>consoloe.log('work:se ejecuto la actualizacion');</script>";
		if ($query_update) {
			echo "<script>
				Swal.fire({
					icon: 'success',
					title: '¡Bien hecho!',
					text: 'El producto ha sido actualizado satisfactoriamente.',
					confirmButtonColor: '#3085d6'
				});
			</script>";
			

		} else {
			$errorMsg = "Lo siento, algo ha salido mal. Intenta nuevamente. " . mysqli_error($con);
			echo "<script>
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: '".addslashes($errorMsg)."',
					confirmButtonColor: '#d33'
				});
			</script>";
		}


?>