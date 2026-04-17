<!-- Modal -->
<div class="modal fade " id="editarusuarios" data-bs-backdrop="static"  data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel"
    aria-hidden="true">    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Nuevo Usuario</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="post" id="mod_editar_usuario" name="editar_usuario">
                    <div id="resultados_ajax"></div>
                    <div class="form-group">
                        <label for="firstname" class="col-sm-4 control-label">Nombre </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mod_firstname" name="firstname2"
                                placeholder="Nombre completo" required>
                                <input type="hidden" class="form-control"id="mod_id" name="mod_id">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="user_name" class="col-sm-4 control-label">Usuario</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mod_user_name" name="user_name2"
                                placeholder="Usuario" pattern="[a-zA-Z0-9]{2,64}"
                                title="Nombre de usuario ( sólo letras y números, 2-64 caracteres)" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="user_email" class="col-sm-4 control-label">Email</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" id="mod_user_email" name="user_email2"
                                placeholder="Correo electrónico" required>
                        </div>
                    </div>
                   
                  
                    <div class="form-group">
                        <label for="" class="col-sm-4 control-label">Tipo de Usuario</label>
                        <div class="col-sm-8">
                            <select class='form-control input-sm ' id="mod_is_admin" name="admin2">
                                <option value="" selected >Selecciona una opcion</option>
                                <option value="1">Administrador</option>
                                <option value="2">Vendedor</option>
                                <option value="3">Facturación</option>
                                <option value="4">Almacén</option>
                                <option value="99">Consulta</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="actualizar_datos"
                onclick='actualizar_usuarios()'>Guardar</button>
            </div>
        </div>
        </div>
    </div>
</div>