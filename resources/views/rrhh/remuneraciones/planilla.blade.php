@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="planilla">
    <legend><h2>Pago de Planilla</h2></legend>
    <div class="row" id="planex">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-5">
					<div class="row">
						<div class="col-md-6">
							<h5>Empresa</h5>
							<select id="id_empresa" class="form-control input-sm">
								<option value="0" selected disabled>Elija una opcion</option>
								@foreach ($emp as $emp)
									<option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-6">
							<h5>Tipo Planilla</h5>
							<select id="id_tipo_planilla" class="form-control input-sm">
								<option value="0" selected disabled>Elija una opcion</option>
								@foreach ($plani as $plani)
									<option value="{{$plani->id_tipo_planilla}}">{{$plani->descripcion}}</option>
								@endforeach
							</select>
						</div>
						
					</div>
				</div>
				<div class="col-md-7">
					<div class="row">
						<div class="col-md-3">
							<h5>Mes</h5>
							<select id="mes" class="form-control input-sm">
								<option value="0" selected disabled>Elija una opcion</option>
								<option value="1">ENERO</option>
								<option value="2">FEBRERO</option>
								<option value="3">MARZO</option>
								<option value="4">ABRIL</option>
								<option value="5">MAYO</option>
								<option value="6">JUNIO</option>
								<option value="7">JULIO</option>
								<option value="8">AGOSTO</option>
								<option value="9">SETIEMBRE</option>
								<option value="10">OCTUBRE</option>
								<option value="11">NOVIEMBRE</option>
								<option value="12">DICIEMBRE</option>
							</select>
						</div>
						<div class="col-md-3">
							<h5>Periodo</h5>
							<select id="periodo" class="form-control input-sm">
								<option value="0" selected disabled>Elija una opcion</option>
								@foreach ($peri as $peri)
									<option value="{{$peri->id_periodo}}">{{$peri->descripcion}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-5">
							<h5>Trabajador</h5>
							<select class="form-control input-sm js-example-basic-single" name="id_trabajador">
								<option value="0" selected disabled>Elija una opción</option>
								@foreach ($trab as $trab)
									<option value="{{$trab->id_trabajador}}">{{$trab->apellido_paterno}} {{$trab->apellido_materno}} {{$trab->nombres}}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-8">
            {{-- <button class="btn btn-flat btn-primary" onclick="procesar();">Procesar Planilla</button> --}}
            <button class="btn btn-flat btn-success" onclick="reportePlanilla();">Reporte Planilla</button>
            <button class="btn btn-flat btn-danger" onclick="generar();">Generar Boleta</button>
            <button class="btn btn-flat btn-warning" onclick="processBoleta();">Generar Boleta Individual</button>
		</div>
	</div>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/remuneraciones/planilla.js')}}"></script>
@include('layout.fin_html')