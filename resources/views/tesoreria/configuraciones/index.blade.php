@extends('tesoreria.layout.tesoreria')


@php($pagina = ['titulo' => 'Tesoreria > Planilla de Pagos', 'tiene_menu' => true, 'slug' => 'planillapagos'])

@section('cuerpo_seccion')
	<legend class="mylegend">
		<h2>Configuraciones</h2>
		<ol class="breadcrumb">
			<li>Tesoreria</li>
			<li>Configuraciones</li>
		</ol>
	</legend>
	<div class="row">
		<div class="col-md-12">


		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped" id="listaPlanillaPagos">
				<thead>
				<tr>
					<th></th>
					<th></th>
					<th>Fecha</th>
					<th>Cta Origen</th>
					<th>Asignado a:</th>
					<th>Cta Destino</th>
					<th>Importe</th>
					<th>Estado</th>

				</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
@stop


@section('scripts_seccion')
	<script type="text/javascript">


	</script>

@stop



