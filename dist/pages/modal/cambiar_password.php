<!-- Modal -->
<div class="modal fade " id="updatepassword" data-bs-backdrop="static"  data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel"
    aria-hidden="true">    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Nuevo Usuario</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="post" id="editar_password" name="editar_password">
			<div id="resultados_ajax3"></div>
			  <div class="form-group">
				<label for="user_password_new3" class="col-sm-4 control-label">Nueva contraseña</label>
				<div class="col-sm-8">
				  <input type="password" class="form-control" id="user_password_new3" name="user_password_new3" placeholder="Nueva contraseña" pattern=".{6,}" title="Contraseña ( min . 6 caracteres)" required>
					<input type="hidden" id="user_id_mod" name="user_id_mod">
				</div>
			  </div>
			  <div class="form-group">
				<label for="user_password_repeat3" class="col-sm-4 control-label">Repite contraseña</label>
				<div class="col-sm-8">
				  <input type="password" class="form-control" id="user_password_repeat3" name="user_password_repeat3" placeholder="Repite contraseña" pattern=".{6,}" required>
				</div>
			  </div>
		  </form>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="actualizar_datos" onclick='actualizar_password()'>Guardar</button>
            </div>
        </div>
        </div>
    </div>
</div>