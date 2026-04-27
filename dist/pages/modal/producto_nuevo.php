<div class="modal fade" id="modalNuevoProductoAlmacen" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Agregar producto manual</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_nuevo_producto_almacen">
					<div class="row">
						<div class="col-md-6">
							<label for="np_barcode">Codigo de barras</label>
							<input type="text" class="form-control" id="np_barcode" name="barcode" placeholder="Ej: 7501128112024" required>
						</div>
						<div class="col-md-6">
							<label for="np_referencia">Referencia</label>
							<input type="text" class="form-control" id="np_referencia" name="referencia" required>
						</div>
					</div>

					<div class="row" style="margin-top:10px;">
						<div class="col-md-6">
							<label for="np_alt1">Clave alterna 1</label>
							<input type="text" class="form-control" id="np_alt1" name="cve_alterna_1">
						</div>
						<div class="col-md-6">
							<label for="np_alt2">Clave alterna 2</label>
							<input type="text" class="form-control" id="np_alt2" name="cve_alterna_2">
						</div>
					</div>

					<div class="row" style="margin-top:10px;">
						<div class="col-md-12">
							<label for="np_descripcion">Descripcion</label>
							<input type="text" class="form-control" id="np_descripcion" name="descripcion" required>
						</div>
					</div>

					<div class="row" style="margin-top:10px;">
						<div class="col-md-4">
							<label for="np_lote">Lote</label>
							<input type="text" class="form-control" id="np_lote" name="lote" required>
						</div>
						<div class="col-md-4">
							<label for="np_caducidad">Caducidad</label>
							<input type="date" class="form-control" id="np_caducidad" name="caducidad" required>
						</div>
						<div class="col-md-4">
							<label for="np_existencias">Existencias iniciales</label>
							<input type="number" min="1" class="form-control" id="np_existencias" name="existencias" value="1" required>
						</div>
					</div>

					<div class="row" style="margin-top:10px;">
						<div class="col-md-4">
							<label for="np_costo">Costo</label>
							<input type="number" step="0.01" min="0" class="form-control" id="np_costo" name="costo" value="0" required>
						</div>
						<div class="col-md-4">
							<label for="np_precio">Precio</label>
							<input type="number" step="0.01" min="0" class="form-control" id="np_precio" name="precio_producto" value="0" required>
						</div>
						<div class="col-md-4" style="padding-top:28px;">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="np_exento_iva" name="exento_iva" value="1">
								<label class="form-check-label" for="np_exento_iva">Exento IVA</label>
							</div>
						</div>
					</div>

					<input type="hidden" id="np_id_almacen" name="id_almacen">
				</form>
				<div id="resultado_nuevo_producto_almacen" style="margin-top:10px;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-primary" onclick="guardar_producto_manual_almacen();">Guardar producto</button>
			</div>
		</div>
	</div>
</div>
