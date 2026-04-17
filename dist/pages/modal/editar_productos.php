
<div class="modal fade bs-example-modal-sm" id="modalEditarProducto" tabindex="1" role="dialog"
    aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Nuevo Producto</h4>
            </div>
            <div class="modal-body">
                <div id='resultados_ajax_producto'></div>
                  <form id="editar_producto_formulario">

                        <div class="form-group">
                            <label for="referencia" class="col-form-label">Referencia</label>
                            <input type="text" class="form-control" id="modalReferencia" name="referencia">
                            <input type="hidden" class="form-control" id="modal_id_producto" name="id_producto">
                        </div>
                        <div class="form-group">
                            <label for="descripcion" class="col-form-label">Descripción</label>
                            <input type="text" class="form-control" id="modalDescripcion" name="descripcion">
                        </div>
                        <div class="form-group">
                            <label for="lote" class="col-form-label">Lote</label>
                            <input type="text" class="form-control" id="modalLote" name="lote">
                        </div>
                        <div class="form-group">
                            <label for="caducidad" class="col-form-label">Caducidad</label>
                            <input type="date" class="form-control" id="modalCaducidad" name="caducidad">
                        </div>
                        <div class="form-group">
                            <label for="Costo" class="col-form-label">Costo</label>
                            <input type="text" class="form-control" id="modalCosto" name="costo" >
                        </div>
                      <div class=" form-group">
                            <label for="Precio" class="col-form-label">Precio</label>
                            <input type="text" class="form-control" id="modalPrecio" name="precio" >
                      </div>
				          </form>

            </div>
				  <div class=" modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="btnUpdateProducto" onclick='editar_producto(event)'>Guardar</button>
                    </div>
            </div>
        </div>
    </div>