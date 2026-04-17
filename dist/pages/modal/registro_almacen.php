	<?php
	error_reporting(E_ALL ^ E_NOTICE);
		if (isset($con))
		{
	?>	
			<!-- Modal -->
			<div class="modal fade bs-example-modal-sm"   id="almacen" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-sm " role="document">
				<div class="modal-content"  >
				  <div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Buscar productos</h4>
				  </div>
				  <div class="modal-body" >
				 <form>
        <?php
				$sql_almacen=mysqli_query($con,"SELECT * FROM almacenes  ORDER BY numero_almacen DESC ");
        		$almacen=mysqli_fetch_array($sql_almacen);
				$clave_id=$almacen['numero_almacen']+1; 

        ?>
        <div class="form-group">
            <label for="numero_almacen" class="col-form-label">Número del almacén</label>
            <input type="text" class="form-control" id="id_numero_almacen" name="numero_almacen" placeholder="Numero de almacen: <?php echo $clave_id; ?>" value="<?php echo $clave_id; ?>" readonly>
          </div>
          <div class="form-group">
            <label for="nombre_almacen" class="col-form-label">Nombre del almacén</label>
            <input type="text" class="form-control" id="nombre_almacen" name="nombre_almacen">
          </div>
            <div class="form-group">
            <label for="encargado" class="col-form-label">Encargado del almacén</label>
            <input type="text" class="form-control" id="id_encargado" name="encargado">
          </div>
        </form>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardar_almacen()">Guardar</button>


					
				  </div>
				</div>
			  </div>
			</div>
	<?php
		}
	?>