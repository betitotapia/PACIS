<?php
		
	?>
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Agregar nuevo vehículo</h4>
		  </div>
		  <div class="modal-body">
			<form class="form-horizontal" method="post" id="guardar_usuario" name="guardar_usuario">
			<div id="resultados_ajax"></div>
			  <div class="form-group">
				<label for="firstname" class="col-sm-3 control-label">Marca</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="marca" name="marca" placeholder="Marca" required>
				</div>
			  </div>
			 
			  <div class="form-group">
				<label for="user_name" class="col-sm-3 control-label">Modelo</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Modelo" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="user_email" class="col-sm-3 control-label">Placa</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="placa" name="placa" placeholder="Correo electrónico" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="user_letra" class="col-sm-3 control-label">Kilometraje Inicial</label>
				<div class="col-sm-8">
				  <input type="letra" class="form-control" id="km_inicial" name="km_inicial" placeholder="Kilometraje al registrar el vehículo" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="user" class="col-sm-3 control-label">Responsable</label>
				<div class="col-sm-8">
			<select class='form-control input-sm ' id="responsable" name="responsable">
				<option value="1" >Administrador</option>
				<option value="2" >Vendedor</option>
				<option value="3" >Facturación</option>
				<option value="4" >Almacén</option>
			</select>
		</div>
			  </div>
		
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			<button type="submit" class="btn btn-primary" id="guardar_datos">Guardar datos</button>
		  </div>
		  </form>
		</div>
	  </div>
	</div>
	<?php
	
	?>