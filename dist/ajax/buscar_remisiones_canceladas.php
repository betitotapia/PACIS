<?php include('is_logged.php');

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
		if ($estado_factura==0 ){
			$update_factura="UPDATE facturas SET status_fact=2 WHERE id_factura='".$numero_factura."'";
			$update1=mysqli_query($con,$update_factura);
			echo"<script>console.log('work:se ejecuto codigo hasta aqui ".$estado_factura."');</script>";
				?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">x</span></button>
    <strong>Aviso!</strong> Remisión Cancelada Exitosamente!
</div>
<?php
			}
		
		else{
			?>
<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">x</span></button>
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
		 echo"<script>console.log('work:se ejecuto codigo usuario: ".$usuario."');</script>";
 if ($rj_usuario1['is_admin']==2){
		$status=1;
		
		$sTable2 = "SELECT f.id_factura, f.numero_factura, f.fecha_factura, f.id_cliente, f.id_vendedor, f.total_venta, f.estado_factura,
			f.compra, f.cotizacion, f.doctor, f.paciente, f.material, f.pago, f.d_factura, f.observaciones, f.status_fact,
			c.id_cliente, c.clave, c.nombre_cliente, c.rfc, c.calle, c.num_int, c.num_ext, c.colonia, c.telefono, c.email,
			u.user_id, u.nombre, u.user_name, u.user_email, u.letra, u.is_admin FROM facturas f LEFT JOIN clientes c ON c.id_cliente = f.id_cliente
			INNER JOIN users u ON u.user_id = f.id_vendedor
			WHERE f.id_vendedor = $usuario AND f.status_fact =2  ORDER BY f.id_factura DESC";
		
		}

	 //  echo"<script>console.log('work:".$sWhere2."');</script>";
	   //echo"<script>console.log('work:".$q."');</script>";


		if ($rj_usuario1['is_admin']!=2){
	    	// $sql="SELECT * FROM $sTable   $sWhere  ";
			$sql="SELECT * FROM facturas
					LEFT JOIN clientes ON facturas.id_cliente = clientes.id_cliente
					INNER JOIN users ON facturas.id_vendedor = users.user_id
					WHERE facturas.status_fact = 2  
					ORDER BY facturas.id_factura DESC";
	
		}else if ($rj_usuario1['is_admin']==2){
			$sql= $sTable2 ;
		}
		$query = mysqli_query($con, $sql);		//loop through fetched data
		//if ($numrows<0){
			echo mysqli_error($con);
			?>
<div class="table-responsive">
    <table class="table  table-striped" id="myTable">
        <tr class="info">
            <th class="">Remisión No./Cliente</th>
            <th class="d-none d-md-table-cell ">Fecha</th>
            <th class=' d-none d-md-table-cell '>Total</th>
            <th class="d-none d-md-table-cell ">Vendedor</th>
            <th class="d-none d-md-table-cell ">Estado Remisión</th>
            <th class="d-none d-md-table-cell">Facturar Remisión</th>
            <th class="hidden-xs">Status en Sistema</th>
            <?php
				$session_id = $_SESSION["user_id"];
				$sql_usuario=mysqli_query($con,"select * from users where user_id ='$session_id'");
				$rj_usuario=mysqli_fetch_array($sql_usuario);

				/*if ($rj_usuario['is_admin']==1 || $rj_usuario['is_admin']==4){
					echo "<th class=''>Reposiciones</th>";
				}*/
				?>
            <th>Acciones</th>
            <!--<th>Bloqueo</th> -->
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
						$email = $row['email'];
						$nombre_vendedor=$row['nombre'];
						$estado_factura=$row['estado_factura'];
						/*if ($estado_factura==1){$text_estado="Pagada";$label_class='label-success';}
						else{$text_estado="Pendiente";$label_class='label-warning';}*/

					switch ($estado_factura){

					case 0:
							$factura_status = "SIN FACTURAR";
							$btn_class='bg_icons-red btn-scale';
						break;
					case 1:
							$factura_status = "FACTURADA";
							$btn_class='bg_icons-green btn-scale';
						break;
					case 2:
							$factura_status="REPOSICIÓN";
							$btn_class='bg_icons-blue btn-scale';
						break;
					case 3:
							$factura_status="MUESTRA";
							$btn_class='bg_icons-gray btn-scale';
						break;
					case 4:
						$factura_status="CONSIGNA";
						$btn_class='bg_icons-purple btn-scale';
						break;
					case 5:
						$factura_status="PRESTAMO";
						$btn_class='bg_icons-highpurple btn-scale';
						break;
						}


						if($total_venta=$row['total_venta']== ""){
							$total_venta = 0;
						}
						$status_fact=$row['status_fact'];
							if($status_fact==1){
								$status_fact="ACTIVA";
								$label_class='g-label-success';
							}
							elseif($status_fact==3){
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
            <td class="columnas">
                <ul>
                    <li><?php echo substr($nombre_cliente,0,25);?></li>
                    <li><?php echo $letra_ventas."-".$numero_factura; ?></li>
            </td>
            <!--<td class="hidden-xs columnas"><?php echo $fecha; echo"<br>Hora: $hora";?></td>-->
            <td class="d-none d-md-table-cell columnas"><?php echo $fecha;?></td>
            <td class='d-none d-md-table-cell  text-left columnas'>$<?php echo number_format ($total_venta,2); ?></td>
            <td class="d-none d-md-table-cell "><?php echo $nombre_vendedor; ?></td>
            <td><a href="#" style='color:white'
                    class="btn btn-default <?php echo $btn_class?>"><?php echo $factura_status; ?></a></td>

            <td>
                <?php
						if ($rj_usuario['is_admin']==1 || $rj_usuario['is_admin']==2 ){
							if($status_fact=="ACTIVA"){
						echo		
						"<a href='' onclick='crear_factura(".$id_factura.");' class='btn btn-default bg_icons-highpurple btn-scale' title='Crear factura' ><i class='bi bi-receipt-cutoff'></i></a>";  
						}	else {
						echo"";
						}
					}

						?>
            </td>
            <td><a href='#' class='btn btn-default <?php echo $label_class; ?>' title='STATUS'
                    style="color:white;"><?php echo $status_fact; ?></a></td>


            <?php 	
			/*if ($rj_usuario['is_admin']==1|| $rj_usuario['is_admin']==4){
				echo "
				<a href='reposicion.php?id_factura=".$id_factura."' class='btn btn-default' title='Reposicion' ><i class='glyphicon glyphicon-save-file'></i></a>";
			}*/
?>

            <td class="columnas">
                <?php
			$consulta_bloqueo=mysqli_query($con,"SELECT facturas.id_factura, facturas.bloqueo, facturas.id_vendedor from facturas where id_factura = '$id_factura' " );
			$rj_bloqueo=mysqli_fetch_array($consulta_bloqueo);
			$bloqueo=$rj_bloqueo['bloqueo'];
			$id_ventas=$rj_bloqueo['id_vendedor'];
			
			
if ($rj_usuario['is_admin'] == 1){

	if($status_fact=="ACTIVA"){
		
	  echo "
	<a href='editar_remision.php?id_factura=".$id_factura."' class='btn btn-default bg_icons-green btn-scale' title='Editar remision' ><ion-icon name='create-outline' class='icons-white'></ion-icon></a> 
	<a href='#' class='btn btn-default bg_icons-purple  btn-scale' title='Ver factura' onclick='ver_factura(".$id_factura.",".$numero_factura.",".$id_ventas.");'><ion-icon name='eye-outline' class='icons-white'></ion-icon></a>
	<a href='#' class='btn btn-default bg_icons-gray btn-scale' title='Descargar factura' onclick='imprimir_factura(".$id_factura.",".$numero_factura.",".$id_ventas.");'><ion-icon name='print-outline' class='icons-white'></ion-icon></a>
	<a href='#' class='btn btn-default bg_icons-blue btn-scale' title='Descargar excel' onclick='descargar(".$id_factura.",".$numero_factura.");'><ion-icon name='cloud-download-outline' class='icons-white'></ion-icon></a>
	<a href='#' class='btn btn-default bg_icons-red btn-scale' title='Borrar factura' onclick='eliminar(".$id_factura.");'><ion-icon name='trash-outline' class='icons-white'></ion-icon></a>
";}
else if($status_fact=="CANCELADA"){
	

}

else{
	
	echo "
	<a href='editar_remision_scliente.php?id_factura=".$id_factura."' class='btn btn-default bg_icons-green btn-scale' title='Editar remision' ><ion-icon name='create-outline' class='icons-white'></ion-icon></a> 
	";
}
	}
	else if($rj_usuario['is_admin'] == 2) {
		 // <a href='editar_factura.php?id_factura=".$id_factura."' class='btn btn-default' title='Editar factura' ><i class='glyphicon glyphicon-edit'></i></a>
	echo "
	<a href='editar_remision.php?id_factura=".$id_factura."' class='btn btn-default bg_icons-green btn-scale' title='Editar remision' ><ion-icon name='create-outline' class='icons-white'></ion-icon></a> 
	<a href='#' class='btn btn-default bg_icons-purple  btn-scale' title='Ver factura' onclick='ver_factura(".$id_factura.",".$numero_factura.",".$id_ventas.");'><ion-icon name='eye-outline' class='icons-white'></ion-icon></a>
	<a href='#' class='btn btn-default bg_icons-gray btn-scale' title='Descargar factura' onclick='imprimir_factura(".$id_factura.",".$numero_factura.",".$id_ventas.");'><ion-icon name='print-outline' class='icons-white'></ion-icon></a>
	<a href='#' class='btn btn-default bg_icons-blue btn-scale' title='Descargar excel' onclick='descargar(".$id_factura.",".$numero_factura.");'><ion-icon name='cloud-download-outline' class='icons-white'></ion-icon></a>
	<a href='#' class='btn btn-default bg_icons-red btn-scale' title='Borrar factura' onclick='eliminar(".$id_factura.");'><ion-icon name='trash-outline' class='icons-white'></ion-icon></a>
	";

	}else if($rj_usuario['is_admin']==4){
		echo "
		 
	<a href='#' class='btn btn-default' title='Ver factura' onclick='ver_factura(".$id_factura.",".$numero_factura.");'><i class='glyphicon glyphicon-eye-open'></i></a>
	<a href='#' class='btn btn-default' title='Descargar factura' onclick='imprimir_factura(".$id_factura.",".$numero_factura.");'><i class='glyphicon glyphicon-download'></i></a>
</td>";
	}else {
		echo "
		<a href='#' class='btn btn-default bg_icons-green btn-scale' title='Editar estado' onclick='obtener_datos(".$id_factura.");' data-toggle='modal' data-target='#myModal9'><ion-icon name='create-outline' class='icons-white'></ion-icon></a> 
	<a href='#' class='btn btn-default bg_icons-purple  btn-scale' title='Ver factura' onclick='ver_factura(".$id_factura.",".$numero_factura.");'><ion-icon name='eye-outline' class='icons-white'></ion-icon></a>
	<a href='#' class='btn btn-default bg_icons-gray btn-scale' title='Descargar factura' onclick='imprimir_factura(".$id_factura.",".$numero_factura.");'><ion-icon name='print-outline' class='icons-white'></ion-icon></a>
	<a href='#' class='btn btn-default bg_icons-blue btn-scale' title='Descargar excel' onclick='descargar(".$id_factura.",".$numero_factura.");'><ion-icon name='cloud-download-outline' class='icons-white'></ion-icon></a>
</td>";
	}
	
	?> </td>

            <?php
	// 	if($rj_usuario['is_admin']==1 || $rj_usuario['is_admin']==3 ||$rj_usuario['is_admin']==4){

	
	// 		echo"
	// 		<td>
	// 		<a href='#' ".$estilo."  class='btn btn-default btn-scale' title='Bolquear Remisión' onclick='obtener_bloqueo(".$id_factura.");' data-toggle='modal' data-target='#myModalBloqueoNormal'><ion-icon name='close-circle-outline' class='icons-white'></ion-icon></a> 					
	// 		</td>";

	// }

			?>
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