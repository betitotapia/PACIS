<?php
	//include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	$_SESSION["user_id"]=1;
	//Archivo de funciones PHP
	//include("../funciones.php");
	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
	
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
			  <table class="table table-bordered table-striped" id="orderTable" >
            <thead>
              <tr>
                <th>Folio</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Almacén</th>
                <th>Estatus</th>
                <th class="text-right">Total</th>
                <th width="180"></th>
                <th width="100">Acciones</th>
              </tr>
            </thead>
            <tbody>
            <?php
            $sql = mysqli_query($con, "
              SELECT oc.*, p.nombre_provedor,
                     a.numero_almacen, a.descripcion AS almacen_desc
              FROM ordenes_compra oc
              INNER JOIN proveedores p ON oc.id_proveedor = p.id_proveedor
              INNER JOIN almacenes a ON oc.id_almacen = a.id_almacen
              ORDER BY oc.id_oc DESC
            ");

            while ($row = mysqli_fetch_assoc($sql)) {
            ?>
              <tr>
                <td><?php echo htmlspecialchars($row['folio_oc']); ?></td>
                <td><?php echo date("d/m/Y", strtotime($row['fecha_oc'])); ?></td>
                <td><?php echo htmlspecialchars($row['nombre_provedor']); ?></td>
                <td><?php echo $row['numero_almacen']." - ".$row['almacen_desc']; ?></td>
                <td>
                  <span class="badge bg-<?php
                    echo ($row['estatus']=='CERRADA')?'success':(($row['estatus']=='PARCIAL')?'warning':'primary');
                  ?>">
                    <?php echo $row['estatus']; ?>
                  </span>
                </td>
                <td class="text-right"><?php echo number_format($row['total'],2); ?></td>
                <td>
                  <a class="btn btn-info btn-sm"
                     href="../recepciones/nueva.php?id_oc=<?php echo $row['id_oc']; ?>&id_proveedor=<?php echo $row['id_proveedor']; ?>">
                    <i class="fa fa-truck"></i> Recibir
                  </a>
                  <a class="btn btn-default bg_icons-purple btn-scale"
                      title="Ver"
                      href="#"
                      onclick="VentanaCentrada('../../pdf/oc_ver.php?id_oc=<?php echo (int)$row['id_oc']; ?>',
                                                'OC_VER','','900','650','true'); return false;">
                      <ion-icon name="eye-outline" class="icons-white"></ion-icon>
                    </a>

                    <!-- DESCARGAR PDF -->
                    <a class="btn btn-default bg_icons-gray btn-scale"
                      title="Descargar PDF"
                      href="#"
                      onclick="VentanaCentrada('../../pdf/oc_pdf.php?id_oc=<?php echo (int)$row['id_oc']; ?>',
                                                'OC_PDF','','900','650','true'); return false;">
                      <ion-icon name="download-outline" class="icons-white"></ion-icon>
                    </a>

                    <!-- IMPRIMIR -->
                    <a class="btn btn-default bg_icons-gray btn-scale"
                      title="Imprimir"
                      href="#"
                      onclick="VentanaCentrada('../../pdf/oc_print.php?id_oc=<?php echo (int)$row['id_oc']; ?>',
                                                'OC_PRINT','','900','650','true'); return false;">
                      <ion-icon name="print-outline" class="icons-white"></ion-icon>
                    </a>
                </td>
                <td class="columnas">
                    <a href="../ordenes_compra/editar.php?id_oc=<?php echo $row['id_oc']; ?>"
                        class="btn btn-default bg_icons-purple btn-scale"
                        title="Editar">
                       <ion-icon name="create" class="icons-white"></ion-icon>
                    </a>

                    <a href="#"
                        onclick="eliminar_orden_compra(<?php echo $row['id_oc']; ?>); return false;"
                        class="btn btn-default bg_icons-gray btn-scale"
                        title="Eliminar">
                              <ion-icon name="close" class="icons-white"></ion-icon>
                    </a>
                    </td>

              </tr>
            <?php } ?>
            </tbody>
          </table>
              <script>
	var tabla = document.querySelector("#orderTable");
	var dataTable = new DataTable(tabla);
	</script>
			</div>
			<?php
		
	}
?>