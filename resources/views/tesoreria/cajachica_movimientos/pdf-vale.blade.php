<!DOCTYPE html>
<html>
<head>
	<title>Hi</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

	<style type="text/css">
		html {
			margin: 0;
			padding: 0;
		}

		html,
		body {
			font-family: "Times New Roman", serif;
			margin: 1mm;
			padding: 2mm;
			border: solid 1px;
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
<table border="0" cellpadding="0" cellspacing="0" dir="ltr" style="width: 100%; font-size: 11px;">
	<tbody>
	<tr>
		<td colspan="4" style="text-align: center; font-weight: bold;">VALE DE CAJA CHICA</td>
		<td colspan="3">N&deg;: {{ substr_replace($vale->codigo, '-', 3, 0) }}</td>
	</tr>
	<tr>
		<td colspan="7" style="height: 5px;"></td>
	</tr>
	<tr>
		<td colspan="3" width="65%" style="text-align: center;">
			<img height="60px" src="{{ $logo_empresa }}" />
		</td>
		<td colspan="4" width="35%">
			<table border="0" cellpadding="0" cellspacing="0" style="width: 100%; text-align: center;">
				<tr style="font-weight: bold;">
					<td style="width: 25%"></td>
					<td class="bordeado" style="width: 25%">D&iacute;a</td>
					<td class="bordeado" style="width: 25%">Mes</td>
					<td class="bordeado" style="width: 25%">A&ntilde;o</td>
				</tr>
				<tr class=" dataImpreso">
					<td></td>
					<td class="bordeado">{{ today()->day }}</td>
					<td class="bordeado">{{ today()->month }}</td>
					<td class="bordeado">{{ today()->year }}</td>
				</tr>
				<tr>
					<td colspan="4" style="height: 5px;"></td>
				</tr>
				<tr>
					<td style="font-weight: bold;">Por S/.</td>
					<td colspan="3" class="bordeado dataImpreso">{{ $vale->cajachica_movimiento->importe }}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="7" style="height: 5px;"></td>
	</tr>
	<tr>
		<td>Entregado a:</td>
		<td colspan="6" class="dataImpreso">{{ $vale->receptor->trabajador->postulante->persona->nombre_completo }}</td>
	</tr>
	<tr>
		<td colspan="7" style="height: 5px;"></td>
	</tr>
	<tr>
		<td>Por la suma de:</td>
		<td colspan="6" class="dataImpreso">{{ $monto_letras }}</td>
	</tr>
	<tr>
		<td colspan="7" style="height: 5px;"></td>
	</tr>
	<tr>
		<td colspan="7">
			<table border="0" cellpadding="0" cellspacing="0" style="width: 100%; text-align: center; font-weight: bold; font-size: 10px;">
				<tr>
					<td class="bordeado" style="width: 47%;">ASIGNADO POR:</td>
					<td style="width: 6%;">&nbsp;</td>
					<td class="bordeado" style="width: 47%;">RECIB&Iacute; CONFORME:</td>
				</tr>
				<tr>
					<td class="bordeado usuariosD" rowspan="4">&nbsp;{{ $vale->emisor->trabajador->postulante->persona->nombre_completo }}</td>
					<td>&nbsp;</td>
					<td class="bordeado usuariosD" rowspan="4">&nbsp;{{ $vale->receptor->trabajador->postulante->persona->nombre_completo }}</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="7" style="height: 5px;"></td>
	</tr>
	<tr>
		<td>Observaciones:</td>
		<td colspan="6" class="dataImpreso">{{ $vale->cajachica_movimiento->observaciones }}</td>
	</tr>
	</tbody>
</table>
</body>
</html>
