<?php
		if (isset($con))
		{
	?>
	<!-- Modal -->
	<div class="modal fade" id="myModal9" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Editar cliente</h4>
		  </div>
		  <div class="modal-body">
			<form class="form-horizontal" method="post" id="editar_estado" name="actualizar_estado">
			<div id="resultados_ajax3"></div>
			
			<div class="form-group">
			 <label for="" class="col-sm-3 control-label">Estado de la factura?</label>
		<div class="col-sm-8">
			<select class='form-control input-sm ' id="mod_estado" name="mod_estado">
			<option value="0" >Facturada</option>
				<option value="1" >Sin Facturar</option>
				<option value="2" >Reposición</option>
				<option value="3" >Muestra</option>
				<option value="4" >Consigna</option>
				<option value="5" >Prestamo</option>
				</option>
			</select>
		</div>
        <input type="hidden" id="mod_id" name="mod_id" value="">
			  </div>
				  
				
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			<button type="submit" class="btn btn-primary" id="actualizar_estado">Actualizar Estado</button>
		  </div>
		  </form>
		</div>
	  </div>
	</div>
	<?php
		}
	?>