<!DOCTYPE html>
<html>
<head>
	<title>Movimientos Caja Chica</title>
	<style type="text/css">
		<?php
		include (public_path().'/template/bootstrap/css/bootstrap.css')
		?>

		/**
                Set the margins of the page to 0, so the footer and the header
                can be of the full height and width !
             **/
		@page {
			margin: 0cm 0cm;
		}

		/** Define now the real margins of every page in the PDF **/
		body {
			margin-top: 5.5cm;
			margin-left: 2cm;
			margin-right: 2cm;
			margin-bottom: 2cm;
		}

		/** Define the header rules **/
		header {
			position: fixed;
			top: 2mm;
			left: 2mm;
			right: 2mm;
			height: 5cm;

			border-bottom: solid 1px;
		}

		/** Define the footer rules **/
		footer {
			position: fixed;
			bottom: 0cm;
			left: 1cm;
			right: 1cm;
			height: 2cm;
			border-top: solid 1px;
		}

		hr {
			page-break-after: always;
			border: 0;
			margin: 0;
			padding: 0;
		}

		.bordeado{
			border: solid 1px;
			height: 20px;
			pargin: 1px;
		}
		.dataImpreso{
			font-style: italic;
		}
		.usuariosD{
			font-style: italic;
			font-size: 9px;
			vertical-align: bottom;
		}



	</style>
</head>
<body>
<script type="text/php">
    if (isset($pdf)) {
        $x = $pdf->get_width() - 94;
        $y = 32;
        $text = "{PAGE_NUM} de {PAGE_COUNT}";
        $font = null;
        $size = 8;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>

<header>
	<div class="row" style="font-size: 11px;">
		<div class="col-sm-9">
			<img height="80px" src="{{ $logo_empresa }}" />
		</div>
		<div class="col-sm-3">
			Fecha: {{ today()->format('d/m/Y') }}<br>
			Hora: {{ now()->hour }}:{{ now()->minute }}<br>
			Pag:
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<h4 class="text-center">Movimientos de Caja Chica</h4>
			<h3 class="text-center">{{ $cajachica->descripcion }}</h3>
		</div>
	</div>
	<div class="row" style="font-size: 11px;">
		<div class="col-sm-12 text-center">
			<p>Del {{ $fecha_ini }} al {{ $fecha_fin }}</p>
		</div>
	</div>



</header>

<footer>
	<p class="text-center">Informe ERP - OKC generado por {{ Auth::user()->trabajador->postulante->persona->nombre_completo }}</p>
</footer>

<main>
	<h4>Ingresos</h4>
	<table class="table table-bordered" align="center">
		<thead style="font-size: 12px">
		<tr>
			<th>Fecha</th>
			<th>Detalle</th>
			<th>Documentos</th>
			<th>Monto</th>
		</tr>
		</thead>
		<tbody style="font-size: 11px">
		@foreach($ingresos as $mov)
			<tr>
				<td>{{ $mov->fecha_j_s }}</td>
				<td>{{ $mov->observaciones }}</td>
				<td>
					@php($dataPago = json_decode($mov->data_pago))
					@if ($dataPago)

						<ul>
							@foreach($dataPago as $dataPago)
								<li>{{ $dataPago->num_docu }} ({{ ($dataPago->monto??'') }})</li>
							@endforeach
						</ul>
					@endif
				</td>
				<td class="text-right">{{ $mov->importe }}</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot style="font-size: 12px">>
		<tr>
			<th colspan="3">Total</th>
			<th class="text-right"><span class="pull-left">{{ $cajachica->moneda->simbolo }} </span>{{ sprintf('%0.2f',$ingresos->sum('importe')) }}</th>
		</tr>
		</tfoot>
	</table>
	<br>
	<h4>Egresos</h4>
	<table class="table table-bordered" align="center">
		<thead style="font-size: 12px">
		<tr>
			<th>Fecha</th>
			<th>Detalle</th>
			<th>Documentos</th>
			<th>Monto</th>
		</tr>
		</thead>
		<tbody style="font-size: 11px">
		@foreach($egresos as $mov)
			<tr>
				<td>{{ $mov->fecha_j_s }}</td>
				<td>{{ $mov->observaciones }}</td>
				<td>
					@php($dataPago = json_decode($mov->data_pago))
					@if ($dataPago)

						<ul>
							@foreach($dataPago as $dataPago)
								<li>{{ $dataPago->num_docu }} ({{ ($dataPago->monto??'') }})</li>
							@endforeach
						</ul>
					@endif
				</td>
				<td class="text-right">{{ $mov->importe }}</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot style="font-size: 12px">>
		<tr>
			<th colspan="3">Total</th>
			<th class="text-right"><span class="pull-left">{{ $cajachica->moneda->simbolo }} </span>{{ sprintf('%0.2f',$egresos->sum('importe')) }}</th>
		</tr>
		</tfoot>
	</table>


	<br>
	<h4>Resumen</h4>
	<table class="table table-bordered" style="width: 50%">
		<tbody>
		<tr>
			<th>Total Ingresos</th>
			<td class="text-right"><span class="pull-left">{{ $cajachica->moneda->simbolo }} </span>{{ sprintf('%0.2f',$ingresos->sum('importe')) }}</td>
		</tr>
		<tr>
			<th>Total Egresos</th>
			<td class="text-right"><span class="pull-left">{{ $cajachica->moneda->simbolo }} </span>{{ sprintf('%0.2f',$egresos->sum('importe')) }}</td>
		</tr>
		<tr>
			<th>Saldo Caja</th>
			<td class="text-right"><span class="pull-left">{{ $cajachica->moneda->simbolo }} </span>{{ sprintf('%0.2f', $cajachica->saldo) }}</td>
		</tr>
		</tbody>
	</table>
	<small><em>Responsable: {{ $cajachica->responsable->trabajador->postulante->persona->nombre_completo }}</em></small>

</main>
</body>
</html>
