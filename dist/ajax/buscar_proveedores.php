<?php 
    require_once('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	
	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';


	

	    	// $sql="SELECT * FROM $sTable   $sWhere  ";
	$sql="SELECT * FROM proveedores";
		$query_proveedores = mysqli_query($con, $sql);	
			?>


<div class="table-responsive">
    <table class="table  table-striped" id="myTable">
        <tr class="info">
            <th class="">Nombre </th>
            <th class=' '>RFC</th>
            <th class="">TELEFONO</th>
            <th>EMAIL</th>
            <th>Acciones</th>
        </tr>
            <?php
				
				while ($row=mysqli_fetch_array($query_proveedores)){
						$id_proveedor=$row['id_proveedor'];
                        $nombre=$row['nombre_provedor'];
                       $rfc=$row['rfc'];
                        $telefono=$row['telefono'];
                        $email=$row['email'];
                        $municipio=$row['municipio'];		
						
					?>
        <tr>
            <td>
               
            <?php echo $nombre?></td>
            <td><?php echo $rfc ?></td>
            <td class="columnas"><?php echo $telefono?></td>
            <td class="columnas"><?php echo $email?></td>   
            <td>
             <?php
             	$session_id = $_SESSION["user_id"];
				$sql_usuario=mysqli_query($con,"select * from users where user_id ='$session_id'");
				$rj_usuario=mysqli_fetch_array($sql_usuario);

                if ($rj_usuario['is_admin']==1){
               echo "<a href ='editar_proveedor.php?id_proveedor=". $id_proveedor." ?>' class='btn btn-default bg_icons-green btn-scale'><ion-icon name='create-outline' class='icons-white'></ion-icon></a>";
                
             } elseif($rj_usuario['is_admin']==2){

             }  
            ?>
	        </td>

        <tr>
<?php
		}
	//
?>
    </table>
    <script>
    var tabla = document.querySelector("#myTable");
    var dataTable = new DataTable(tabla);
    </script>
</div>
