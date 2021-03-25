
<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtros-ventas">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Filtros</h3>
            </div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-4">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="checkEmpresa">
									<label class="text-muted" for="checkEmpresa">Empresa</label>
								</div>
							</div>
							<div class="col-md-8">
								<select class="form-control input-sm" name="fil_empresa" id="fil_empresa">
									<option value="1">OK COMPUTER E.I.R.L.</option><option value="3">SMART VALUE SOLUTIONS S.R.L.</option><option value="4">RICHARD DORADO BACA</option><option value="5">JONATHAN DEZA RUGEL</option><option value="6">PROYECTEC E.I.R.L</option><option value="7">PROTECNOLOGIA E.I.R.L.</option>								</select>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="checkEmi">
									<label class="text-muted" for="checkEmi">Fecha Emisión</label>
								</div>
							</div>
							<div class="col-md-4">
								<input type="date" class="form-control input-sm" name="fil_emision_ini" id="fil_emision_ini">
							</div>
							<div class="col-md-4">
								<input type="date" class="form-control input-sm" name="fil_emision_fin" id="fil_emision_fin">
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="checkPer" checked="" disabled="">
									<label class="text-muted" for="checkPer">Periodo</label>
								</div>
							</div>
							<div class="col-md-4">
 								<select class="form-control input-sm" name="fil_periodo" id="fil_periodo">
									<option value="1">2019</option>
									<option value="2">2020</option>
									<option value="3" selected="">2021</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
            <div class="modal-footer">
				<button type="button" class="btn btn-sm btn-primary" onclick="filterVentas();"> Procesar </button>
			</div>
        </div>
    </div>
</div>