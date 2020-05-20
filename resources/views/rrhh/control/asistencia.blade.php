@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="asistencia">
    <legend><h2>Asistencia Final</h2></legend>
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-5">
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
						<div class="col-md-4">
							<h5>Tipo Planilla</h5>
							<select id="tipo_planilla" class="form-control input-sm">
								<option value="0" selected disabled>Elija una opcion</option>
								@foreach ($plani as $plani)
									<option value="{{$plani->id_tipo_planilla}}">{{$plani->descripcion}}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="row">
						<div class="col-md-12">
							<h5>Fecha Inicio / Fecha Fin</h5>
							<div class="flexDiv">
								<input type="date" id="fecha1" class="form-control input-sm">
								<input type="date" id="fecha2" class="form-control input-sm">
								<button class="btn btn-primary btn-sm btn-flat" onclick="ProcesarDiario();">Procesar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="reporte-visual"></div>
	<div class="row">
		<div class="col-md-12 text-right">
			<button class="btn btn-success btn-sm btn-flat" onclick="generarAsist();">Grabar Asistencia</button>
		</div>
	</div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/control/asistencia.js')}}"></script>
@include('layout.fin_html')