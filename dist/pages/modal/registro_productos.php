	<?php

	?>	
			<!-- Modal -->
			<div class="modal fade bs-example-modal-sm"   id="modalNuevoProducto" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-sm " role="document">
				<div class="modal-content"  >
				  <div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Nuevo Producto</h4>
				  </div>
				  <div class="modal-body" >

                  <div class="form-group">
            <label for="referencia" class="col-form-label">Referencia</label>
            <input type="text" class="form-control" id="modalReferencia" >
          </div>
          <div class="form-group">
            <label for="descripcion" class="col-form-label">Descripción</label>
            <input type="text" class="form-control" id="modalDescripcion" >
          </div>
            <div class="form-group">
            <label for="Costo" class="col-form-label">Costo</label>
            <input type="number" class="form-control" id="modalCosto" step="0.01" >
          </div>
          <div class="form-group">
            <label for="Precio" class="col-form-label">Precio</label>
            <input type="number" class="form-control" id="modalPrecio" step="0.01" >
          </div>
          <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" id="modalExentoIva">
          <label class="form-check-label" for="modalExentoIva">
            Producto exento de IVA
          </label>
        </div>
				
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarProducto">Guardar</button>


					
				  </div>
				</div>
			  </div>
			</div>
	