
<?php
//Archivo verifica que el usario que intenta acceder a la URL esta logueado
/* Connect To Database*/
require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos

$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
if($action == 'ajax'){
    // escaping, additionally removing everything that could be (html/javascript-) code
 
   $count_query   = mysqli_query($con, "SELECT count(*) AS numrows FROM products ");
   $row= mysqli_fetch_array($count_query);
   $numrows = $row['numrows'];
   //main query to fetch the data
   $sql="SELECT * from products ";  
   $query = mysqli_query($con, $sql);
   //loop through fetched data
   //var_dump($sql);
    if ($numrows>0){
        
        ?>
        <div class="table-responsive">
          <table class="table table-striped" id="search">
            <tr  class="info">
                <th>Referencia</th>
                <th >Producto</th>
                <th >Almacén</th>
                <th class='text-right size-xl'>Lote</th>
                <th class='text-right size-xl'>Caducidad</th>
                <th class='text-right'>Cantidad</th>
                <th class='text-right'>Existencias</th>
                <th class='text-right'>Precio </th>
                <th class='text-right'>Agregar</th>
            </tr>
            <?php
            while ($row=mysqli_fetch_array($query)){
                    $id_producto=$row['id_producto'];
                    $clave=$row['barcode'];
                    $referencia=$row['referencia'];
                    $descripcion=$row['descripcion'];
                    $precio=$row['precio_producto'];
                    $lote=$row['lote'];
                    $caducidad=$row['caducidad'];
                    $existencias=$row['existencias'];
                    $no_almacen=$row['id_almacen'];
                ?>
                <tr >
                    <td class="column-font-small"><?php echo $referencia; ?></td>
                    <td class="column-font-small">
                        <?php echo $descripcion; ?>
                    <input type="hidden" value="<?php echo $descripcion;?>" id="descripcion_<?php echo $id_producto;?>">
                    </td>
                    <td >  <div class="">
                    <input type="text" class="form-control column-font-small" style="width:100%;" id="almacen_<?php echo $id_producto; ?>" value="<?php echo $no_almacen; ?>" readonly>
                    </div></td>
                    <td class=''>
                    <div class="">
                    <input type="text" class="form-control column-font-small" style="width:100%;" id="lote_<?php echo $id_producto; ?>" value="<?php echo $lote; ?>" readonly>
                    </div></td>
                    <td class='size-xl col-sm-1'>
                    <div class="pull-right">
                    <input type="date" class="form-control column-font-small" style="width:80%;" id="caducidad_<?php echo $id_producto; ?>"  value="<?php echo $caducidad; ?>" readonly >
                    </div></td>
                    <td class='col-xs-1'>
                    <div class="pull-right">
                    <input type="text" class="form-control column-font-small" style="width:60%;" id="cantidad_<?php echo $id_producto; ?>"  value="1" >
                    </div></td>
                    <td class='col-xs-2'>
                    <div class="">
                        <input type="text" class="form-control column-font-small" style="width:50%;" id="existencias_<?php echo $id_producto; ?>" value="<?php echo $existencias; ?>" readonly>
                    </div></td>
                    
                    <td class='col-xs-2'><div class="pull-right">
                    <input type="text" class="form-control" style="text-align:right width:80%;" id="precio_venta_<?php echo $id_producto ;?>" value="<?php echo $precio; ?>">
                    <input type="hidden" class="form-control" style="text-align:right" id="referencia_<?php echo $id_producto; ?>"  value="<?php echo $referencia; ?>" >
                    </div></td>
                    <td class='text-center'><a class='btn bg_icons-orange'href="#" onclick="agregar(<?php echo $id_producto;?>)"><i class="bi bi-plus"></i></a></td>
                    
                </tr>
                <?php
            }
            ?>
            <tr>
                <!-- <td colspan=5><span class="pull-right" style="margin-left:60% !important;">
                <?php
                 //echo paginate($reload, $page, $total_pages, $adjacents);
                ?></span></td> -->
            </tr>
          </table> <script>
	var tabla = document.querySelector("#search");
	var dataTable = new DataTable(tabla);
	</script>

        </div>
        <?php
    }
}
?>
