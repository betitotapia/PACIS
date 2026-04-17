
			<!-- Modal -->
<div class="modal fade bs-example-modal-sm"   id="modalRegistroLote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm " role="document">
	<div class="modal-content"  >
	  <div class="modal-header">
		<h4 class="modal-title" id="myModalLabel">Nuevo Producto</h4>
	  </div>
	  <div class="modal-body" >

        <div class="form-group">
            <label for="referencia" class="col-form-label">Referencia</label>
            <input type="text" class="form-control" id="regReferencia" >
          </div>
          <div class="form-group">
            <label for="descripcion" class="col-form-label">Descripción</label>
            <input type="text" class="form-control" id="regDescripcion" >
          </div>
          <div class="form-group">
            <label for="lote" class="col-form-label">Lote</label>
            <input type="text" class="form-control" id="regLote" >
          </div>
          <div class="form-group">
            <label for="caducidad" class="col-form-label">Caducidad</label>
            <input type="date" class="form-control" id="regCad" >
          </div>
            <div class="form-group">
            <label for="Costo" class="col-form-label">Costo</label>
            <input type="number" class="form-control" id="regCosto" step="0.01" >
          </div>
          <div class="form-group">
            <label for="Precio" class="col-form-label">Precio</label>
            <input type="number" class="form-control" id="regPrecio" step="0.01" >
          </div>
				
		   <div class="modal-footer">
				<button type="button" class="btn btn-secondary" id='btnCancelarRegistroLote' data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarRegistroLote">Guardar</button>
			</div>
		  </div>
		</div>
	 </div>
   </div>
</div>  