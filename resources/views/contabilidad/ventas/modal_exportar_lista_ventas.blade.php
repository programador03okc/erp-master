
<div class="modal fade" tabindex="-1" role="dialog" id="modal-exportar-lista-ventas">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Exportar Lista Venta</h3>
            </div>
            <div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-4">
								<div class="form-check">
									<label class="text-muted" for="checkEmpresa">Rango de Fechas</label>
								</div>
							</div>
							<div class="col-md-4">
								<input type="date" class="form-control input-sm" name="fil_rango_ini" id="fil_rango_ini">
							</div>
							<div class="col-md-4">
								<input type="date" class="form-control input-sm" name="fil_rango_fin" id="fil_rango_fin">
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4">
									<div class="form-check">
										<label class="text-muted" for="checkTipoDocumento">Tipo Documento</label>
									</div>
								</div>
								<div class="col-md-8">
									<select class="form-control input-sm" name="fil_tipo_documento" id="fil_tipo_documento">
										<option value="0">Todos</option>
										<option value="1">Factura</option>
										<option value="2">Boleta</option>
										<option value="3">Nota Credito</option>
									</select>
								</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4">
								<div class="form-check">
									<label class="text-muted" for="checkEmpresa">Empresa</label>
								</div>
							</div>
							<div class="col-md-8">
								<select class="form-control input-sm" name="id_empresa" id="id_empresa">
									<option value="1">OK COMPUTER E.I.R.L.</option><option value="3">SMART VALUE SOLUTIONS S.R.L.</option><option value="4">RICHARD DORADO BACA</option><option value="5">JONATHAN DEZA RUGEL</option><option value="6">PROYECTEC E.I.R.L</option><option value="7">PROTECNOLOGIA E.I.R.L.</option>								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
            <div class="modal-footer">
				<button type="button" class="btn btn-sm btn-primary" onclick="exportarListaVentas();"> Procesar </button>
			</div>
        </div>
    </div>
</div>