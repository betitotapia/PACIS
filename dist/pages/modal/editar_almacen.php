<div class="modal fade bs-example-modal-sm" id="edit_almacen" tabindex="1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Editar Almacen</h4>
            </div>
            <div class="modal-body">
				<div id='resultados_ajax_almacen'></div>
                <form id="editar_almacen_nuevo">
                   
                    <div class="form-group">
                        <label for="numero_almacen" class="col-form-label">Número del almacén</label>
                        <input type="text" class="form-control" id="mod_id_numero_almacen" name="numero_almacen">
						<input type="hidden" class="form-control" id="mod_id_almacen" name="id_almacen">
                    </div>
                    <div class="form-group">
                        <label for="nombre_almacen" class="col-form-label">Nombre del almacén</label>
                        <input type="text" class="form-control" id="mod_nombre_almacen" name="nombre_almacen">
                    </div>
                    <div class="form-group">
                        <label for="encargado" class="col-form-label">Encargado del almacén</label>
                        <input type="text" class="form-control" id="mod_id_encargado" name="encargado">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" id="actualizar_datos_almacen" onclick='editar_datos()'>
                    Guardar</button>
            </div>
        </div>
    </div>
</div>