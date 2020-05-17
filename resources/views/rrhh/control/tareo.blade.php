@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="tareo">
    <legend><h2>Tareo Diario</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <button class="btn btn-flat btn-primary" onclick="OpenModal();">Cargar Horarios</button>
            <button class="btn btn-flat btn-success" onclick="Diario();">Control Diario</button>
			<button class="btn btn-flat btn-danger" onclick="modalReporte();">Reporte General</button>
			<input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
		</div>
	</div>
	<div class="row" id="inputDiario" hidden="true">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-3">
					<h5>Empresa</h5>
					<select id="id_empresa" class="form-control input-sm" onChange="cambiarEmpresa(this.value);">
						<option value="0" selected disabled>Elija una opcion</option>
						@foreach ($empre as $empre)
							<option value="{{$empre->id_empresa}}">{{$empre->razon_social}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<h5>Sede</h5>
					<select id="id_sede" class="form-control activation">
						<option value="0" selected disabled>Elija una opción</option>
					</select>
				</div>
				<div class="col-md-3">
					<h5>Tipo Planilla</h5>
					<select id="tipo_planilla" class="form-control input-sm">
						<option value="0" selected disabled>Elija una opcion</option>
						@foreach ($plani as $plani)
							<option value="{{$plani->id_tipo_planilla}}">{{$plani->descripcion}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<h5>Fecha</h5>
					<div class="flexDiv">
						<input type="date" id="fecha" class="form-control input-sm">
						<input type="hidden" name="dia_sem">
						<button class="btn btn-flat btn-primary btn-sm btn-flat" onclick="ProcesarDiario();">Procesar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<table class="table table-condensed table-bordered" id="tablaDiario" hidden="true">
		<caption style="text-align: right;"></caption>
		<thead>
			<tr>
				<th colspan="6">Personal</th>
				<th>Entrada</th>
				<th>Salida Alm</th>
				<th>Entrada Alm</th>
				<th>Salida</th>
				<th width="90">Tard. Ingreso</th>
				<th width="90">Tard. Almuerzo</th>
				<th width="90">Total Tardanza</th>
				<th width="90">Permisos</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

<!-- Modal Archivo CSV -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-csv">
	<div class="modal-dialog" style="width: 400px;">
		<div class="modal-content">
			<form class="formPage" id="formPage" form="csv" type="register">
				<div class="modal-header" style="display: flex; justify-content: space-between;">
					<h3 class="modal-title">Cargar CSV</h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<h5>Cargar Archivo:</h5>
							<input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
							<input type="file" name="archivo" id="archivo" class="filestyle" data-buttonName="btn-primary" data-buttonText="Seleccionar CSV" data-size="sm">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-flat btn-success">Guardar <span class="fas fa-save"></span></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Reporte -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-reporte">
	<div class="modal-dialog" style="width: 250px;">
		<div class="modal-content">
			<div class="modal-header" style="display: flex; justify-content: space-between;">
				<h3 class="modal-title">Generar Reporte</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<h5>Fecha Inicio:</h5>
						<input type="date" name="from" id="from" class="form-control input-sm">
					</div>
					<div class="col-md-12">
						<h5>Fecha Fin:</h5>
						<input type="date" name="to" id="to" class="form-control input-sm">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-flat btn-success" onclick="downloadExcel();"> Generar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Permisos -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-permi-asist">
	<div class="modal-dialog" style="width: 400px;">
		<div class="modal-content">
			<div class="modal-header" style="display: flex; justify-content: space-between;">
				<h3 class="modal-title">Historial de Permisos</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body"><div class="row" id="permi-tareo"></div></div>
		</div>
	</div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/control/tareo.js')}}"></script>
@include('layout.fin_html')