<?php
error_reporting(0);
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos

	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
	if (isset($_GET['id'])){

		$sql_almacen="SELECT COUNT(*) as total FROM products where id_almacen = '".$_GET['id']."'";
		if ($query_almacen=mysqli_query($con,$sql_almacen)){
			$row_almacen=mysqli_fetch_array($query_almacen);
			if ($row_almacen['total']>0){
				?>
<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <strong>Error!</strong> No se puede eliminar el almacen ya que contiene productos
</div>
<?php
			}else{
		$clave=intval($_GET['id']);
		$del1="delete from almacenes where id_almacen='".$clave."'";

		if ($delete1=mysqli_query($con,$del1) and $delete2=mysqli_query($con,$del2)){
			?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <strong>Aviso!</strong> Almacen Eliminado Exitosamente
</div>
<?php
		}else {
			?>
<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <strong>Error!</strong> No se puedo eliminar los datos
</div>
<?php

			}
		}
	}
}
	if($action == 'ajax'){
		$usuario = $_SESSION['user_id'];
		// escaping, additionally removing everything that could be (html/javascript-) code
         $q = mysqli_real_escape_string($con,(strip_tags($_REQUEST['q'], ENT_QUOTES)));

	  	$sql="SELECT * FROM almacenes ";
			//echo "<script>console.log('admin');</script>";

		$query = mysqli_query($con, $sql);		//loop through fetched data

			echo mysqli_error($con);
			?>
<div class="table-responsive">
    <table class="table  table-striped" id="myTable">
        <tr class="info">
            <th class="">NO. Almacen</th>
            <th class="">Nombre Almacén</th>
            <th class=''>Encargado</th>
            <th class=''>Acciones</th>

        </tr>

        <?php
				while ($row=mysqli_fetch_array($query)){
						$id_almacen=$row['id_almacen'];
						$clave=$row['numero_almacen'];
						$descripcion=$row['descripcion'];
					    $encargado = $row['encargado'];

					?>


         <tr> 
			
            <td class=" ">
			<input type="hidden" id="id_almacen_<?php echo $id_almacen; ?>"  value="<?php echo $id_almacen; ?>">
			<input type="hidden" id="clave_<?php echo $id_almacen; ?>"  value="<?php echo $clave; ?>">
			<input type="hidden" id="descripcion_<?php echo $id_almacen; ?>"  value="<?php echo $descripcion; ?>">
			<input type="hidden" id="encargado_<?php echo $id_almacen; ?>"  value="<?php echo $encargado; ?>">	

			<?php echo $clave; ?></td>
            <td class=" "><a href="detalle_almacen.php?id_almacen=<?php echo $id_almacen; ?>" class="" ><?php echo $descripcion; ?></a>
            </td>
            <td class=" "><?php echo $encargado; ?></td>

            <td class="columnas">
			
                <button class='btn bg_icons-highpurple' data-bs-toggle="modal" data-bs-target="#edit_almacen" onclick="obtener_datos_almacen('<?php echo $id_almacen; ?>')"><i
                        class="bi bi-pencil-square"></i>
                </button>
				<a href='#' class='btn btn-default bg_icons-red btn-scale' title='Borrar almacén' onclick='eliminar_almacen(<?php echo $id_almacen; ?>);'><ion-icon name='trash-outline' class='icons-white'></ion-icon> </a>
            </td>
        </tr>
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