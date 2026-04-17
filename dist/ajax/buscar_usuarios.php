<?php include('is_logged.php');

	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	
	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';


	

	    	// $sql="SELECT * FROM $sTable   $sWhere  ";
	$sql="SELECT * FROM users";
		$query_usuarios = mysqli_query($con, $sql);	
			?>
<div class="table-responsive">
    <table class="table  table-striped" id="myTable">
        <tr class="info">
            <th class="">ID</th>
            <th class="">Nombre</th>
            <th class=' '>Usuario</th>
            <th class="">Email</th>
            <th>Letra asignada</th>
            <th>Acciones</th>
        </tr>
            <?php
				
				while ($row=mysqli_fetch_array($query_usuarios)){
						$id_usuario=$row['user_id'];
                        $nombre=$row['nombre'];
                        $username=$row['user_name'];
                        $email=$row['user_email'];
                        $letra=$row['letra'];		
                        $administrador=$row['is_admin'];			
						
					?>
                  
        <tr>
              
            <td>
                   <?php echo $id_usuario; ?>
                    <input type="hidden" id="nombres_<?php echo $id_usuario;?>" value="<?php echo $row['nombre'];?>" >
					<input type="hidden" id="usuario_<?php echo $id_usuario;?>" value="<?php echo $username;?>" >
					<input type="hidden" id="email_<?php echo $id_usuario;?>"  value="<?php echo $email;?>" >
                       
         </td>
            <td>
            <input type="hidden" id="administrador_<?php echo $id_usuario;?>" value="<?php echo $administrador;?>" >     
            <?php echo $nombre?></td>
            <td><?php echo $username ?></td>
            <td class="columnas"><?php echo $email?></td>   
            <td class="columnas">"<?php echo $letra?>"</td>
            <td>
             <?php
             	$session_id = $_SESSION["user_id"];
				$sql_usuario=mysqli_query($con,"select * from users where user_id ='$session_id'");
				$rj_usuario=mysqli_fetch_array($sql_usuario);

                if ($rj_usuario['is_admin']==1){
               echo "<a href ='' class='btn btn-default bg_icons-green btn-scale' data-bs-toggle='modal' data-bs-target='#editarusuarios' onclick='obtener_datos($id_usuario)'><ion-icon name='create-outline' class='icons-white'></ion-icon></a>
                <a href='' class='btn btn-default bg_icons-orange btn-scale' title='Cambiar contraseña' onclick='get_user_id($id_usuario)' data-bs-toggle='modal' data-bs-target='#updatepassword'><i class='bi bi-key-fill'></i></a>";
 
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
