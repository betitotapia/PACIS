<?php include('is_logged.php');

	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	
	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';

	    	// $sql="SELECT * FROM $sTable   $sWhere  ";
	$sql="SELECT * FROM clientes";
		$query_usuarios = mysqli_query($con, $sql);	


                     $session_id = $_SESSION["user_id"];
					$sql_usuario=mysqli_query($con,"select * from users where user_id ='$session_id'");
					$rj_usuario=mysqli_fetch_array($sql_usuario);
                    $is_admin=$rj_usuario['is_admin'];
			?>
<div class="table-responsive">
    <table class="table  table-striped" id="myTable">
        <tr class="info">
            <th class="">Nombre/Razon Social</th>
            <th class=' '>RFC</th>
            <th class="">Telefono</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>
            <?php
				
				while ($row=mysqli_fetch_array($query_usuarios)){
						$id_cliente=$row['id_cliente'];
                        $nombre=$row['nombre_cliente'];
                       $rfc=$row['rfc'];
                        $telefono=$row['telefono'];
                        $email=$row['email'];		
						

                        
					?>
                  
        <tr>
              
          
            <td>
                 
            <?php echo $nombre?></td>
            <td><?php echo $rfc ?></td>
            <td class="columnas"><?php echo $telefono?></td>
            <td class="columnas"><?php echo $email?></td>   
            <td>
             <?php
             	

                if ($is_admin == 1){
               echo "<a href ='editar_cliente.php?id_cliente=$id_cliente' class='btn btn-default bg_icons-green btn-scale' ><ion-icon name='create-outline' class='icons-white'></ion-icon></a>";
             
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
