<?php
error_reporting(0);
	//include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	
	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
	
	if($action == 'ajax'){
		echo "<script>console.log('estamos dentro!!!')</script>";
        $sql="SELECT * FROM autos   ";
		$query = mysqli_query($con, $sql);		//loop through fetched data
		//if ($numrows<0)

			?>
			<div class="table-responsive">
            <div class="card-body table-responsive p-0">
            <table class="table table-striped align-middle" id="myTable">
				<tr  class="info">
				<th class="" >MARCA</th>
				<th class="">MODELO</th>
				<th class=''>PLACA</th>
				<th class="">KILOMETRAJE</th>
				<th class="">RESPONSABLE<th>
				<th >Acciones</th>
				</tr>
				<?php
				
				while ($row=mysqli_fetch_array($query)){
						$id_auto=$row['id_auto'];
						$marca=$row['marca'];
					    $modelo= $row['modelo'];
						//$fecha=date("d/m/Y", strtotime($row['fecha_factura']));
						//$hora=date("H:i", strtotime($row['fecha_factura']));
						$placa=$row['placa'];
					    $kilometraje=$row['kilometraje_inicial'];
                        $user_id=$row['id_vendedor'];
                        ?>

						<tr>
						<td class=""><?php echo $marca;?></td>
						<td class=''><?php echo $modelo; ?></td>	
						<td class=""><?php echo $placa?></td>
						<td><?php echo number_format($kilometraje,0,'.',',') ?> km</td>
						<td class=""><?php 
                        $sql_responsable="SELECT user_id, nombre FROM users where user_id=$user_id";
                        $query_responsable=mysqli_query($con, $sql_responsable);
                        $rj_responsable=mysqli_fetch_array($query_responsable);
                        $encargado=$rj_responsable['nombre'];

                        echo $encargado; ?></td>
					<td><a href='#' class='btn btn-default' title='Borrar factura' onclick='eliminar(".$id_factura.");'><i class='glyphicon glyphicon-remove'></i> </a>
					</td>
					
	
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
    }?>