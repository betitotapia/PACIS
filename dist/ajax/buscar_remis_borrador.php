<?php
error_reporting(0);
	//include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	
	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
	if (isset($_GET['id'])){
		$numero_factura=intval($_GET['id']);
		
		/*$del1="delete from facturas where numero_factura='".$numero_factura."'";
		$del2="delete from detalle_factura where numero_factura='".$numero_factura."'";*/
		$sql_1=mysqli_query($con,"select * from facturas where id_factura='".$numero_factura."'");
		$rj_1=mysqli_fetch_array($sql_1);
		$estado_factura=$rj_1['estado_factura'];
		if ($estado_factura==1){
			$update_factura="UPDATE facturas SET status_fact=0 WHERE id_factura='".$numero_factura."'";
			$update1=mysqli_query($con,$update_factura);
			//echo"<script>console.log('work:se ejecuto codigo hasta aqui ".$estado_factura."');</script>";
				?>
				<div class="alert alert-success alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
				  <strong>Aviso!</strong> Remisión Cancelada Exitosamente!
				</div>
				<?php
			}
		
		else{
			?>
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
				  <strong>Error!</strong> Esta remisión ya esta facturada, no se puede cancelar!
				</div>
				<?php
			
		}

	}
	if($action == 'ajax'){
		$usuario = $_SESSION['user_id'];
		// escaping, additionally removing everything that could be (html/javascript-) code
         $q = mysqli_real_escape_string($con,(strip_tags($_REQUEST['q'], ENT_QUOTES)));
		
		 $sql_usuario1=mysqli_query($con,"select * from users where user_id ='$usuario'");
		 $rj_usuario1=mysqli_fetch_array($sql_usuario1);
		 $rj_usuario1['is_admin'];
		 //echo"<script>console.log('work:se ejecuto codigo hasta aqui ".$rj_usuario1['is_admin']."');</script>";

if ($rj_usuario1['is_admin']!=2){

		$sTable = "facturas, clientes, users";
		 $sWhere = "";
		 $sWhere.=" WHERE facturas.id_cliente=clientes.id_cliente and facturas.id_vendedor=users.user_id and status_fact=3";
		$sWhere.=" order by facturas.id_factura desc";
			
		//echo"<script>console.log('work:se ejecuto codigo de busqueda  ".$_SESSION['user_id']."');</script>";
	}else if ($rj_usuario1['is_admin']==2){
		$status=1;
		$sWhere2 = "";
		$aColumns = array('user_name','numero_factura');//Columnas de busqueda
		$sTable2 = "f.id_factura,f.numero_factura,f.fecha_factura,f.id_cliente,f.id_vendedor,f.total_venta,f.estado_factura,f.compra,f.cotizacion,f.doctor,f.paciente,f.material,
		f.pago,f.d_factura,f.observaciones,f.status_fact,
		c.id_cliente,c.clave,c.nombre_cliente,c.rfc,c.calle,c.numint,c.numext,c.colonia,c.telefono,c.emailpred,
		u.user_id,u.nombre,u.user_name,u.user_email,u.letra,u.is_admin 
		FROM facturas f INNER JOIN clientes c  ON c.id_cliente = f.id_cliente 
		INNER JOIN users u on u.user_id = f.id_vendedor 
		";
		
		$sWhere2.=" WHERE f.id_vendedor=$usuario  and status_fact=3";
		if ( $_GET['q'] != "" )
			{
				$sWhere2 = "WHERE ( " ;
				for ( $i=0 ; $i<count($aColumns) ; $i++ )
				{
					$sWhere2 .= $aColumns[$i]." LIKE '%".$q."%' OR ";
				}
				$sWhere2 = substr_replace( $sWhere2, "", -3 );
				$sWhere2 .= ')';
			}            
			
	   $sWhere2.=" order by f.id_factura desc";
	  	
	   //echo"<script>console.log('work:se ejecuto codigo de usuario');</script>";
		}
	 //  echo"<script>console.log('work:".$sWhere2."');</script>";
	   //echo"<script>console.log('work:".$q."');</script>";

		include 'pagination.php'; //include pagination file
		

		if ($rj_usuario1['is_admin']!=2){
	    	$sql="SELECT * FROM $sTable   $sWhere  ";
			// echo"<script>console.log('work:se ejecuto codigo de usuario ".$sTable."');</script>";
	
		}else if ($rj_usuario1['is_admin']==2){
			$sql="SELECT $sTable2  $sWhere2 ";
		}
		$query = mysqli_query($con, $sql);	
		//loop through fetched data
		//if ($numrows<0){
			?>
			<div class="table-responsive">
			  <table class="table  table-striped" id="myTable">
				<tr  class="info">
				<th class="" >Remisión No./Cliente</th>
				<th class="d-none d-md-table-cell ">Fecha</th>
				<th class="d-none d-md-table-cell ">Vendedor</th>
				<th class="d-none d-md-table-cell ">Estado Remisión</th>
				<th class="hidden-xs">Status en Sistema</th>
				<?php
				$session_id = $_SESSION["user_id"];
				$sql_usuario=mysqli_query($con,"select * from users where user_id ='$session_id'");
				$rj_usuario=mysqli_fetch_array($sql_usuario);


				/*if ($rj_usuario['is_admin']==1 || $rj_usuario['is_admin']==4){
					echo "<th class=''>Reposiciones</th>";
				}*/
				?>
				<th >Acciones</th>
				</tr>
				<?php
				
				while ($row=mysqli_fetch_array($query)){
						$id_factura=$row['id_factura'];
						$numero_factura=$row['numero_factura'];
					    $letra_ventas = $row['letra'];
						$fecha=date("d/m/Y", strtotime($row['fecha_factura']));
						$hora=date("H:i", strtotime($row['fecha_factura']));
						$nombre_cliente=$row['nombre_cliente'];
						$telefono = $row['telefono'];
						$email = $row['emailpred'];
						$nombre_vendedor=$row['nombre'];
						$estado_factura=$row['estado_factura'];

						echo"<script>console.log('work:se ejecuto codigo de usuario ".$id_factura."');</script>";
						/*if ($estado_factura==1){$text_estado="Pagada";$label_class='label-success';}
						else{$text_estado="Pendiente";$label_class='label-warning';}*/

					switch ($estado_factura){

					case 0:
							$factura_status = "<span class='label label-info'>SIN FACTURAR</span>";
						break;
					case 1:
							$factura_status = "<span class='label label-success'>FACTURADA</span>";
						break;
					case 2:
							$factura_status="<span class='label label-warning'>REPOSICIÓN</span>";
						break;
					case 3:
							$factura_status="<span class='label label-default'>MUESTRA</span>";
						break;
					case 4:
						$factura_status="<span class='label label-primary'>CONSIGNA</span>";
						break;
					case 5:
						$factura_status="<span class='label label-default'>PRESTAMO</span>";
						break;
						}


						$total_venta=$row['total_venta'];
						$status_fact=$row['status_fact'];
							if($status_fact==1){
								$status_fact="ACTIVA";
								$label_class='g-label-success';

							}else if($status_fact==3){
								$status_fact="BORRADOR";
								$label_class='bg_icons-orange';
							}
							
							else{
								$status_fact="CANCELADA";
								$label_class='bg_icons-red';
							} 	
							
					?>

					<input type="hidden" value="<?php echo $estado_factura;?>" id="estado<?php echo $id_factura;?>">
					
					<tr>
						<td class="columnas"><ul><li><?php echo substr($nombre_cliente,0,25);?></li><li><?php echo $letra_ventas."-".$numero_factura; ?></li></td>
						<!--<td class="hidden-xs columnas"><?php echo $fecha; echo"<br>Hora: $hora";?></td>-->
						<td class="d-none d-md-table-cell columnas"><?php echo $fecha;?></td>
						<td class="d-none d-md-table-cell "><?php echo $nombre_vendedor; ?></td>
						<td class="d-none d-md-table-cell  columnas"><?php echo $factura_status; ?></td>
						<td ><a href='#' class='btn btn-default <?php echo $label_class; ?>' title='STATUS' ><?php echo $status_fact; ?></a></td>
						<td class="text-right">
							<!-- <a href="#" class='btn btn-default bg_icons-blue' title='VER REMISIÓN' onclick="imprimir_remision('<?php echo $numero_factura;?>','<?php echo $letra_ventas;?>')"><i class="bi bi-eye"></i></a> -->
							<a href="editar_remision?n_remi=<?php echo $numero_factura ?>" class='btn btn-default bg_icons-green' title='EDITAR REMISIÓN' ><i class="bi bi-pencil-square"></i></a>
							<a href="#" class='btn btn-default bg_icons-red' title='CANCELAR REMISIÓN' onclick="eliminar('<?php echo $id_factura;?>')"><i class="bi bi-trash3"></i></a>
						</td>
						
								
					<?php
				}
				?>
				<tr>
					<td colspan=7><span class="pagination pull-right"><?php
					// echo paginate($reload, $page, $total_pages, $adjacents);
					?></span></td>
				</tr>
			  </table>
			  <script>
	var tabla = document.querySelector("#myTable");
	var dataTable = new DataTable(tabla);
	</script>
			</div>
			<?php
		}
	//}
?>