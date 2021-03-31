<div class="modal fade" tabindex="-1" role="dialog" id="modal-almacen-reserva">
	<div class="modal-dialog" style="width: 50%;">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Seleccionar Almacén para Reserva</h3>
			</div>
			<div class="modal-body">
					<div class="row">
						<div class="col-md-9">
							<div class="form-group">
								<label>Almacén</label>
								<select id="almacen_reserva" class="form-control">
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="cantidad_reserva">Cantidad Reserva</label>
									<input type="number" class="form-control" id="cantidad_reserva" min="1" readOnly>
							</div>
						</div>
					</div>
			</div>
			<div class="modal-footer">
                <label style="display: none;" id="indice"></label>
                <button type="button" class="btn btn-sm btn-success" onClick="agregarReservaAlmacen()">Aceptar</button>
            </div>
		</div>
	</div>
</div>


