<!-- Modal -->
<div class="modal fade " id="usuarios" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Nuevo Usuario</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="post" id="guardar_usuario" name="guardar_usuario">
                    <div id="resultados_ajax"></div>
                    <div class="form-group">
                        <label for="firstname" class="col-sm-4 control-label">Nombre </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="firstname" name="firstname"
                                placeholder="Nombre completo" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="user_name" class="col-sm-4 control-label">Usuario</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="user_name" name="user_name"
                                placeholder="Usuario" pattern="[a-zA-Z0-9]{2,64}"
                                title="Nombre de usuario ( sólo letras y números, 2-64 caracteres)" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="user_email" class="col-sm-4 control-label">Email</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" id="user_email" name="user_email"
                                placeholder="Correo electrónico" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="user_letra" class="col-sm-4 control-label">Letra para remisiones</label>
                        <div class="col-sm-8">
                            <input type="letra" class="form-control" id="letra" name="letra"
                                placeholder="Letra para usar en remisiones" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="user_password_new" class="col-sm-4 control-label">Contraseña</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="user_password_new" name="user_password_new"
                                placeholder="Contraseña" pattern=".{6,}" title="Contraseña ( min . 6 caracteres)"
                                required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="user_password_repeat" class="col-sm-4 control-label">Repite contraseña</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="user_password_repeat"
                                name="user_password_repeat" placeholder="Repite contraseña" pattern=".{6,}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-4 control-label">Tipo de Usuario</label>
                        <div class="col-sm-8">
                            <select class='form-control input-sm ' id="is_admin" name="is_admin">
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
            <button type="button" class="btn btn-primary" id="guardar_datos" onclick='guardar_usuarios()'>Guardar</button>
        </div>
    </div>
</div>
</div>