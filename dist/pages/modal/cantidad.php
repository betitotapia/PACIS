<div class="modal fade" id="modalCantidad" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cantidad para Ajuste</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <label for="modalCantidadInput" class="form-label">
          Ingresa la cantidad con signo (ej. <strong>+5</strong> para sumar, <strong>-3</strong> para restar)
        </label>
        <input id="modalCantidadInput" type="text" class="form-control" placeholder="+5 o -3">
        <div id="modalCantidadError" class="text-danger mt-2" style="display:none;"></div>
      </div>
      <div class="modal-footer">
        <button id="btnCancelarCantidad" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button id="btnGuardarCantidad" type="button" class="btn btn-primary">Aceptar</button>
      </div>
    </div>
  </div>
</div>