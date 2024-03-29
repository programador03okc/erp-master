<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Lista de Cobranzas </h1>
    <br>
    <table>
        <thead>
            <tr style="text-align: center;">
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Empresa</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Tipo</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">RUC Cliente</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="25">Cliente</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="25">CDP</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="25">OCAM</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Fact.</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">UU.EE.</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="20">OC.</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">SIAF</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">FTE FTO</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Fecha Emisión</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Fecha Recepción</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Periodo</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Moneda</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Importe</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="9">Plazo Crédito </th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="25">Estado</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="18">Trámite</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="18">Fase</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="18">Area Responsable</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="18">Vendedor</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Categoría</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="30">Observaciones</th>

                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="20">Penalidad</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="20">Penalidad Monto</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="20">Retención</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="20">Retención Monto</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="20">Detracción</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="20">Detracción Monto</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="20">Fecha Pago (próx)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
            <tr>
                <td style="border: 6px solid #000 !important;">{{ $item->empresa }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->sector }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->cliente_ruc }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->cliente }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->cdp }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->ocam }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->factura }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->uu_ee }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->oc_fisica }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->siaf }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->fuente_financ }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->fecha_emision }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->fecha_recepcion }}</td>
                <td style="border: 6px solid #000 !important;" align="center">{{ $item->periodo }}</td>
                <td style="border: 6px solid #000 !important;" align="center">{{ $item->moneda }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->importe }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->plazo_credito }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->estado_cobranza }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->tipo_tramite }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->fase }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->area }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->usuario_responsable }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->categoria }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->observacion }}</td>

                <td style="border: 6px solid #000 !important;" align="center">{{ ($item->tiene_penalidad) ? 'SI' : 'NO' }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->monto_penalidad }}</td>
                <td style="border: 6px solid #000 !important;" align="center">{{ ($item->tiene_retencion) ? 'SI' : 'NO' }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->monto_retencion }}</td>
                <td style="border: 6px solid #000 !important;" align="center">{{ ($item->tiene_detraccion) ? 'SI' : 'NO' }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->monto_detraccion }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->programacion_pago }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
