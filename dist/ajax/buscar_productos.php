<?php
	//include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	$_SESSION["user_id"]=1;
	//Archivo de funciones PHP
	//include("../funciones.php");
	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
	if (isset($_GET['id'])){
		$id_producto=intval($_GET['id']);
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
		// escaping, additionally removing everything that could be (html/javascript-) code
		
         $q = mysqli_real_escape_string($con,(strip_tags($_REQUEST['q'], ENT_QUOTES)));
		 
		
		
		$sql="SELECT * from products GROUP BY referencia ";
		//$sql="SELECT $sTable.id_producto, $sTable.clave as SKU, $sTable.clave_alterna as referencia, $sTable.descripcion as producto, ltpd01.CVE_ART, ltpd01.LOTE, ltpd01.CVE_ALM, ltpd01.CANTIDAD, almacenes01.clave as n_almacen, almacenes01.descripcion as nombre_almacen FROM $sTable INNER JOIN ltpd01 ON $sTable.clave = ltpd01.CVE_ART INNER JOIN almacenes01 ON almacenes01.clave = ltpd01.CVE_ALM $sWhere LIMIT $offset, $per_page";
		$query = mysqli_query($con, $sql);
		//loop through fetched data

		$session_id = $_SESSION["user_id"];
					$sql_usuario=mysqli_query($con,"select is_admin from users where user_id ='$session_id'");
					$rj_usuario=mysqli_fetch_array($sql_usuario);
					$is_admin=$rj_usuario['is_admin'];
					echo "<script>console.log('work:se ejecuto codigo usuario: ".$session_id."');</script>"
		
			?>
			<div class="table-responsive">
			  <table class="table  table-striped" id="producTable" >
				<tr  class="info">
					<th class="hidden-xs">Referencia</th>
					<th class="hidden-xs">Producto</th>
					
					<th>Existencias</th>
					<th>Precio</th>
					<!-- <th class='hidden-xs text-right'>Acciones</th> -->
					
				</tr>
				<?php

				while ($row=mysqli_fetch_array($query)){
						$id_producto=$row['id_producto'];
						$clave=$row['barcode'];
						$referencia=$row['referencia'];
						$descripcion=$row['descripcion'];
						$existencias=$row['existencias'];
						$precio_producto=$row['precio_producto'];
						// $lote=$row['lote'];
						// $caducidad=$row['caducidad'];
						$costo=$row['costo'];
						
						$sql="SELECT SUM(existencias) as total FROM products WHERE referencia = '$referencia'";
						$query_existencias=mysqli_query($con,$sql);
						$rw_existencias=mysqli_fetch_array($query_existencias);
						$existencias=$rw_existencias['total'];
					?>
                    
					<input type="hidden" value="<?php echo $clave;?>" id="clave<?php echo $id_producto;?>">
					<input type="hidden" value="<?php echo $referencia;?>" id="referencia<?php echo $id_producto;?>">
					<input type="hidden" value="<?php echo $descripcion;?>" id="descripcion<?php echo $id_producto;?>">
					<input type="hidden" value="<?php echo $existencias;?>" id="existencias<?php echo $id_producto;?>">
					
					<!--<input type="hidden" value="<?php echo number_format($precio_producto,2,'.','');?>" id="precio_producto<?php echo $id_producto;?>">-->
					<tr>
						<td  class="columnas"><a href="#" onclick="verDetalleAlmacenes('<?php echo htmlspecialchars($row['referencia'], ENT_QUOTES, 'UTF-8'); ?>'); return false;">
								<?php echo htmlspecialchars($row['referencia']); ?>
							</a></td>
						<td  class="columnas "><?php echo $descripcion; ?></td>
						
						
						<td><?php echo $existencias; ?></td>
						<td>$<span class='pull-right'><?php echo number_format($precio_producto,2);?></span></td>
				<?php 

					?>
						
					</tr>
					<?php
				}
				?>
				<tr>
					<td colspan=6><span class="pull-right">
					<?php
					// echo paginate($reload, $page, $total_pages, $adjacents);
					?></span></td>
				</tr>
			  </table>
              <script>
	var tabla = document.querySelector("#producTable");
	var dataTable = new DataTable(tabla);
	</script>
			</div>
			<?php
		
	}
?>