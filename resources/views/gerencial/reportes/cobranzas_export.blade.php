
<table>
    <thead>
        <tr>
            <th style="border: 6px solid #000 !important;">Empresa</th>
            <th style="border: 6px solid #000 !important;">Tipo</th>
            <th style="border: 6px solid #000 !important;">RUC Cliente</th>
            <th style="border: 6px solid #000 !important;">Cliente</th>
            <th style="border: 6px solid #000 !important;">OCAM</th>
            <th style="border: 6px solid #000 !important;">Fact.</th>
            <th style="border: 6px solid #000 !important;">UU.EE.</th>
            <th style="border: 6px solid #000 !important;">OC.</th>
            <th style="border: 6px solid #000 !important;">SIAF</th>
            <th style="border: 6px solid #000 !important;">FTE FTO</th>
            <th style="border: 6px solid #000 !important;">Fecha Emisión</th>
            <th style="border: 6px solid #000 !important;">Fecha Recepción</th>
            <th style="border: 6px solid #000 !important;">Moneda</th>
            <th style="border: 6px solid #000 !important;">Importe</th>
            <th style="border: 6px solid #000 !important;">Plazo Crédito </th>
            <th style="border: 6px solid #000 !important;">Días de Retraso</th>
            <th style="border: 6px solid #000 !important;">Estado</th>
            <th style="border: 6px solid #000 !important;">Fase</th>
            <th style="border: 6px solid #000 !important;">Area Responsable</th>
            <th style="border: 6px solid #000 !important;">Vendedor</th>
            <th style="border: 6px solid #000 !important;">Programación de Pago </th>
            <th style="border: 6px solid #000 !important;">Categoría</th>
            <th style="border: 6px solid #000 !important;">Observaciones</th>

            <th style="border: 6px solid #000 !important;">Penalidad</th>
            <th style="border: 6px solid #000 !important;">Penalidad Monto</th>
            <th style="border: 6px solid #000 !important;">Retención</th>
            <th style="border: 6px solid #000 !important;">Retención Monto</th>
            <th style="border: 6px solid #000 !important;">Detracción</th>
            <th style="border: 6px solid #000 !important;">Detracción Monto</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $requerimiento)
        <tr>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->codigo_empresa }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->nombre_sector }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->cliente_ruc }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->cliente }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->ocam }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->factura }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->uu_ee }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->oc_fisica }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->siaf }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->fuente_financ }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->fecha_emision }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->fecha_recepcion }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->moneda }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->importe }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->plazo_credito }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->atraso }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->estado }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->fase }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->area }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->vendedor }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->fecha_pago }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->categoria }}</td>
            <td style="border: 6px solid #000 !important;">{{ '--' }}</td>

            <td style="border: 6px solid #000 !important;">{{ $requerimiento->penalidad }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->penalidad_importe }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->retencion }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->retencion_importe }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->detraccion }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->detraccion_importe }}</td>

        </tr>
        @endforeach
    </tbody>
</table>


