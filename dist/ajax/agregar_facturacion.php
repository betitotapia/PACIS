<?php
include('is_logged.php'); //Archivo verifica que el usario que intenta acceder a la URL esta logueado
$session_id = session_id();

require_once("../config/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
require_once("../config/conexion.php"); //Contiene funcion que conecta a la base de datos
$numero_factura = $_POST['numero_factura'];
$id_vendedor = $_POST['id_vendedor'];

if(isset($_POST['id']) &&
    isset($_POST['cantidad']) &&
    isset($_POST['precio_venta']) &&
    isset($_POST['lote']) &&
	isset($_POST['caducidad']) &&
    isset($_POST['referencia']) &&
    isset($_POST['almacen']) &&
    isset($_POST['numero_factura']) &&
    isset($_POST['id_vendedor']) &&
	isset($_POST['descripcion']))

{
	$id = $_POST['id'];
	$cantidad = $_POST['cantidad'];
	$precio_venta = $_POST['precio_venta'];
	$referencia = $_POST['referencia'];
	$almacen = $_POST['almacen'];
	$caducidad = $_POST['caducidad'];
	$lote = $_POST['lote'];
	$descripcion = $_POST['descripcion'];

	
if (!empty($id) and !empty($cantidad) and !empty($precio_venta) and !empty($lote) and !empty($referencia) and !empty($descripcion) and !empty($almacen)) {

	
$sql_check = mysqli_query($con, "SELECT cantidad FROM detalle_factura 
    WHERE id_producto = $id AND numero_factura = '$numero_factura' AND id_vendedor = $id_vendedor");

if (mysqli_num_rows($sql_check) > 0) {


    	$row = mysqli_fetch_assoc($sql_check);
   		$nueva_cantidad = $row['cantidad'] + $cantidad;
		
		$sql_update = mysqli_query($con,"UPDATE detalle_factura SET cantidad = $nueva_cantidad WHERE id_producto = $id AND numero_factura = $numero_factura AND id_vendedor = $id_vendedor");
		$sql_cantidad= mysqli_query($con, "SELECT existencias FROM products WHERE id_producto = '$id'");
		$row_cantidad = mysqli_fetch_array($sql_cantidad);
		$existencias = $row_cantidad['existencias'] - $cantidad;
     	$descuento = mysqli_query($con, "UPDATE products SET existencias = '$existencias' WHERE id_producto = '$id'");
	}else{
		
	$insert_detail = mysqli_query($con, "INSERT INTO detalle_factura VALUES (NULL,'$numero_factura','$id',NULL,'$referencia', '$descripcion', '$lote','$caducidad','$almacen','$cantidad','$precio_venta','$id_vendedor')");
	$sql_cantidad= mysqli_query($con, "SELECT existencias FROM products WHERE id_producto = '$id'");
	$row_cantidad = mysqli_fetch_array($sql_cantidad);
	$existencias = $row_cantidad['existencias'] - $cantidad;
	$descuento = mysqli_query($con, "UPDATE products SET existencias = '$existencias' WHERE id_producto = '$id'");
	}

}
}
// if (isset($_GET['id'])) //codigo elimina un elemento del array
// {
// 	$id_tmp = intval($_GET['id']);
// 	$delete = mysqli_query($con, "DELETE FROM detalle_factura WHERE id_detalle='" . $id_tmp . "'");

// }
//$simbolo_moneda=get_row('perfil','moneda', 'id_perfil', 1);
$simbolo_moneda = "$"; // Simbolo de la moneda, se puede cambiar por el que se necesite
?>
<div class="tabe-responsive">

	<table class="table table-striped" id="myTable">
		<tr class="info">
			<!-- <th class='hidden-xs '>CODIGO</th> -->
			<th class='text-center'>REFERENCIA</th>
			<th class='text-center'>ALMACÉN</th>
			<th class='text-center'>LOTE</th>
			<th class='text-center'>CADUCIDAD</th>
			<th class='text-center'>CANT.</th>
			<th class='text-left'>DESCRIPCION</th>
			<th>EXC IVA</th>
			<th class='text-right'>PRECIO UNIT.</th>
			<th class='text-right'>PRECIO TOTAL</th>
			<th></th>
		</tr>
		<?php
		
		$sumador_total = 0;
		$sumador_iva=0;
		$sql = "SELECT d.id_detalle, d.numero_factura,  d.id_producto, d.cantidad,d.lote,d.caducidad,
		d.precio_venta, d.almacen, d.descripcion as nombre_producto,d.iva,
		d.referencia, d.lote, d.caducidad, p.descripcion, p.exento_iva
		FROM  detalle_factura d
		INNER JOIN products p ON p.id_producto = d.id_producto 
		WHERE d.numero_factura = '" . $numero_factura . "' AND d.id_vendedor ='" . $id_vendedor . "' order by d.id_detalle asc";
		$query = mysqli_query($con, $sql);
		//echo "<script>console.log('query: " . $sql . "');</script>";
		while ($row = mysqli_fetch_array($query)) {
			$id_tmp = $row["id_detalle"];
			$codigo_producto = $row['id_producto'];
			$almacen = $row['almacen'];
			$referencia = $row['referencia'];
			$lote = $row['lote'];
			$cantidad = $row['cantidad'];
			$nombre_producto = $row['nombre_producto'];
			$caducidad = $row['caducidad'];
			$precio_venta = $row['precio_venta'];
			$date = date_create($caducidad);
			$iva=$row['exento_iva'];

			$precio_venta = $row['precio_venta'];
			$precio_venta_f = number_format($precio_venta, 2); //Formateo variables

			$precio_venta_r = str_replace(",", "", $precio_venta_f); //Reemplazo las comas
			$precio_total = $precio_venta_r * $cantidad;
			$precio_total_f = number_format($precio_total, 2); //Precio total formateado
			$precio_total_r = str_replace(",", "", $precio_total_f); //Reemplazo las comas

			$sumador_total += $precio_total_r; //Sumador
			
			if ($iva == 0) {
				$sumador_iva += $precio_total * 0.16;
			}


		?>
			<tr>
				<!-- <td class='hidden-xs text-center'><?php echo $codigo_producto; ?></td> -->
				<td class='text-center'><?php echo $referencia; ?></td>
				<td class='text-center'><?php 
				$sql_almacen = mysqli_query($con, "SELECT * FROM almacenes WHERE id_almacen = '$almacen'");
				$row_almacen = mysqli_fetch_array($sql_almacen);
				$desc_almacen = $row_almacen['descripcion'];
				echo $desc_almacen; ?></td>
				<td class='text-center'><?php echo $lote; ?></td>
				<td class='text-center'><?php echo date_format($date, "d/m/Y"); ?></td>
				<td class='text-center'>
				<input type="text" id="cantidad_<?php echo $id_tmp; ?>" class="form-control" ondblclick="editar_item(<?php echo $id_tmp ?>,'cantidad_<?php echo $id_tmp ?>')" value="<?php echo $cantidad; ?>" readonly onfocusout="guardar_item(<?php echo $id_tmp ?>,1)">	
				</td>
				<td><input type="text" id="descripcion_<?php echo $id_tmp; ?>" class="form-control" ondblclick="editar_item(<?php echo $id_tmp ?>,'descripcion_<?php echo $id_tmp ?>')" value="<?php echo $nombre_producto; ?>" readonly onfocusout="guardar_item(<?php echo $id_tmp ?>,2)" ></td>
				<td class='text-center'> <input class="form-check-input" type="checkbox" value=""  id="exento_<?php echo $id_tmp; ?>" onChange="exc_iva(<?php echo $id_tmp ?>)" <?php if($iva==1){echo "checked";} ?> ></td>
				<td class='text-right'><?php echo $precio_venta_f; ?></td>
				<td class='text-right'><?php echo $precio_total_f; ?></td>
				<td class='text-center'><a href="#" onclick="eliminar('<?php echo $id_tmp ?>','<?php echo $codigo_producto ?>','<?php echo $cantidad ?>',event)"><i class="bi bi-trash3"></i><i class="glyphicon glyphicon-trash"></i></a></td>
			</tr>
		<?php
		}
		
			// if ($iva == 1) {
			// $s_iva=	$precio_venta*0.16;
			// }else{
			// 	$s_iva=0;
			// }
		
		$impuesto = 16;
		$subtotal = number_format($sumador_total, 2, '.', '');
		$total_iva = number_format($sumador_iva, 2, '.', '');
		// $total_iva = (($subtotal * $impuesto) / 100)-$s_iva;
		// $total_iva = number_format($total_iva, 2, '.', '');
		// $total_factura = $subtotal + $total_iva;
			$total_factura = $sumador_total + $sumador_iva;
		?>
		
	<tr>
    <td colspan="8" style="text-align: right; white-space: nowrap;"><strong>SUBTOTAL <?php echo $simbolo_moneda;?></strong></td>
    <td style="text-align: right;"><?php echo number_format($subtotal,2);?></td>
    <td></td>
	<td></td>
	<td></td>
</tr>
<tr>
    <td colspan="8" style="text-align: right; white-space: nowrap;"><strong>IVA (<?php echo $impuesto;?>%) <?php echo $simbolo_moneda;?></strong></td>
    <td style="text-align: right;"><?php echo number_format($total_iva,2);?></td>
    <td></td>
	<td></td>
	<td></td>
</tr>
<tr>
    <td colspan="8" style="text-align: right; white-space: nowrap;"><strong>TOTAL <?php echo $simbolo_moneda;?></strong></td>
    <td style="text-align: right;"><?php echo number_format($total_factura,2);?></td>
    <td></td>
	<td></td>
	<td></td>
</tr>


	</table>
</div>