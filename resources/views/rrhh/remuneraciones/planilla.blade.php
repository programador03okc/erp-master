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
		<div class="col-md-2">
			{{-- <button class="btn btn-flat btn-primary" onclick="procesar();">Procesar Planilla</button> --}}
            <button class="btn btn-flat btn-block btn-danger" onclick="generar();">Generar Boleta</button>
		</div>
		<div class="col-md-2">
            <button class="btn btn-flat btn-block btn-warning" onclick="processBoleta();">Generar Boleta Individual</button>
		</div>
		<div class="col-md-2">
			<button class="btn btn-flat btn-block btn-success" onclick="reportePlanilla();">Reporte Planilla</button>
		</div>
		<div class="col-md-4">
			<div class="input-group">
				<select class="form-control" name="nameGrupo" id="nameGrupo">
					<option value="" selected disabled>Elija una opción..</option>
					<option value="ADMINISTRACION">Administración</option>
					<option value="COMERCIAL">Comercial</option>
					<option value="GERENCIA">Gerencia</option>
					<option value="PROYECTOS">Proyetos</option>
				</select>
				<span class="input-group-btn">
				  <button type="button" class="btn btn-primary btn-flat" onclick="reportePlanillaGrupal();">Reporte Planilla Grupal</button>
				</span>
			</div>
		</div>
		<div class="col-md-2">
			<button class="btn btn-flat btn-block btn-info" onclick="reporteGastos();">Reporte de Gastos</button>
		</div>
	</div>
	<br><br>
	
	<div class="row">
		<div class="col-md-4">
			<fieldset style="padding: 10px; background-color: #fff; border: 1px solid #ddd;">
				<h2>Adicional SPCC</h2>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<button class="btn btn-flat btn-block btn-danger" onclick="generarSPCC();">Generar Boleta</button>
					</div>
					<div class="col-md-6">
						<button class="btn btn-flat btn-block btn-success" onclick="reportePlanillaSPCC();">Reporte Planilla</button>
					</div>
				</div>
			</fieldset>
		</div>

		<div class="col-md-3">
			<fieldset style="padding: 10px; background-color: #fff; border: 1px solid #ddd;">
				<h2>Enviar Correos</h2>
				<hr>
				<div class="row">
					<div class="col-md-12">
						<button class="btn btn-flat btn-block btn-primary" onclick="enviarBoleta();">Enviar Boletas por Correo</button>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</div>

<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-correos">
    <div class="modal-dialog" style="width: 55%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Resultado del Envío</h3>
            </div>
            <div class="modal-body">
				<div class="row">
					<div class="col-md-6 oculto" id="ul-si" style="border-right: 1px solid #ddd;"><ul></ul></div>
					<div class="col-md-6 oculto" id="ul-no"><ul></ul></div>
				</div>
			</div>
        </div>
    </div>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/remuneraciones/planilla.js')}}"></script>
@include('layout.fin_html')