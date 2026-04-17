<?php
		if (isset($con))
		{
	?>
	<!-- Modal -->
	<div class="modal fade" id="myModalBloqueoNormal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Bloqueo de Remisión</h4>
		  </div>
		  <div class="modal-body">
			<form class="form-horizontal" method="post" id="bloqueo_estado" name="actualizar_estado">
			<div id="resultados_ajax4"></div>
			
			<div class="form-group">
			 <label for="" class="col-sm-3 control-label">Que quieres hacer con la remisión?</label>
		<div class="col-sm-8">
			<select class='form-control input-sm ' id="mod_estado" name="mod_estado">
				<option value="" selected >Selecciona una opción</option>
			<option value="1" >Bloquearla</option>
				<option value="0" >Desbloquearla</option>
				</option>
			</select>
		</div>
        <input type="hidden" id="mod_id2" name="mod_id2" value="">
			  </div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			<button type="submit" class="btn btn-primary" id="bloqueo_remision">Actualizar</button>
		  </div>
		  </form>
		</div>
	  </div>
	</div>
	<?php
		}
	?>