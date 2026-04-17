<?php
error_reporting(0);
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos

	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
    if (isset($_GET['id_almacen'])){
        $id_almacen=intval($_GET['id_almacen']);
		echo "<script>console.log('el id es:".$id_almacen."');</script>";
    } else {
        $id_almacen=0;
    }
	if (isset($_GET['id_producto'])){

		$id_producto=intval($_GET['id_producto']);
		$query=mysqli_query($con, "select * from detalle_factura where id_producto ='".$id_producto."'");
		$count=mysqli_num_rows($query);
		if ($count==0){
			$sql_usuario=mysqli_query($con, "select * from products where id_producto='".$id_producto."'");
			 $rw_producto=mysqli_fetch_array($sql_usuario);
		      $sku = $rw_producto['id_producto'];

			if ($delete=mysqli_query($con,"DELETE FROM products WHERE id_producto ='".$sku."'")) {
							
			?>
			 <script>Swal.fire("OK!", "Producto Eliminado Exitosamente", "success");</script>
			<?php 
		}else {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <strong>Error!</strong> Lo siento algo ha salido mal intenta nuevamente.
			</div>
			<?php
			
		} 
	}else {
			?>
			<!--<div class="alert alert-danger alert-dismissible" role="alert">-->
			  <!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
			  <!--<strong>Error!</strong> No se pudo eliminar éste  producto. Existen cotizaciones vinculadas a éste producto. -->
			  <script>Swal.fire("Error!", "No se pudo eliminar éste  producto. Existen cotizaciones vinculadas a éste producto.", "error");</script>
			<!-- </div> -->
			<?php
		}
		
		
		}
	if($action == 'ajax'){
		$usuario = $_SESSION['user_id'];
		// escaping, additionally removing everything that could be (html/javascript-) code
      
	  	$sql="SELECT * FROM products WHERE id_almacen = $id_almacen ";
			//echo "<script>console.log('admin');</script>";

		$query = mysqli_query($con, $sql);		//loop through fetched data

			echo mysqli_error($con);

	
					$session_id = $_SESSION["user_id"];
					$sql_usuario=mysqli_query($con,"select * from users where user_id ='$session_id'");
					$rj_usuario=mysqli_fetch_array($sql_usuario);
						$admin=$rj_usuario['is_admin'];			
			?>
			<div class="table-responsive">
			  <table class="table  table-striped" id="myTable">
				<tr  class="info">
					<th class="" >SKU</th>
					<th class="">DESCRIPCION</th>
				    <th class=''>LOTE</th>
					<th>CADUCIDAD</th>
					<th>EXISTENCIAS</th>
					<?php
					if ($admin==1 || $admin==3){
							?>
					<th class=''>Acciones</th>
					<?php
					}
					?>

				</tr>

				<?php
				while ($row=mysqli_fetch_array($query)){
						$id_producto=$row['id_producto'];
                        $referencia=$row['referencia'];
                        $descripcion=$row['descripcion'];
                        $lote=$row['lote'];
                        $caducidad=$row['caducidad'];
						$existencias=$row['existencias'];
						$costo=$row['costo'];
						$precio=$row['precio_producto'];
					?>


					<tr>
						<td class=" ">
						<input type="hidden" value="<?php echo $id_producto;?>" id="id_producto_<?php echo $id_producto;?>">
						<input type="hidden" value="<?php echo $referencia;?>" id="referencia_<?php echo $id_producto;?>">	
						<input type="hidden" value="<?php echo $descripcion;?>" id="descripcion_<?php echo $id_producto;?>">
						<input type="hidden" value="<?php echo $lote;?>" id="lote_<?php echo $id_producto;?>">
						<input type="hidden" value="<?php echo $caducidad;?>" id="caducidad_<?php echo $id_producto;?>">
						<input type="hidden" value="<?php echo $existencias;?>" id="existencias_<?php echo $id_producto;?>">
						<?php
						
						

						if ($admin==1 || $admin==3){
							?>
							<input type="hidden" value="<?php echo $costo;?>" id="costo_<?php echo $id_producto;?>">
							<?php
						}
						?>
						
						<input type="hidden" value="<?php echo $precio;?>" id="precio_<?php echo $id_producto;?>">

						<?php echo $referencia; ?></td>
						<td class=" "><?php echo $descripcion; ?></td>
						<td class=" "><?php echo $lote; ?></td>
						<td class=" "><?php echo $caducidad; ?></td>
						<td class=" "><?php echo $existencias; ?></td>
						<?php 

					if ($rj_usuario['is_admin']==1 || $rj_usuario['is_admin']==3){
                          echo "		
					<td class='hidden-xs'><span class='pull-right'>
					<a href='#' class='btn btn-default bg_icons-green btn-scale' title='Editar producto' onclick='obtener_datos(".$id_producto.");' data-bs-toggle='modal' data-bs-target='#modalEditarProducto'><ion-icon name='create-outline' class='icons-white'></ion-icon></a> 
					<a href='#' class='btn btn-default bg_icons-red btn-scale' title='Borrar producto' onclick='eliminar(".$id_producto.");'><ion-icon name='trash-outline' class='icons-white'></ion-icon> </a></span></td>";
						}?>

					<?php
				}
				?>
			  </table>
			  <script>
	 
	
	 var tabla = document.querySelector("#myTable");
	 var dataTable = new DataTable(tabla);
	</script>
			</div>
			<?php
		}

?>