
<h3>Lista de Cobranzas </h3>
<table>
    <thead>
        <tr style="text-align: center;">
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Empresa</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Tipo</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="15">RUC Cliente</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="25">Cliente</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="25">OCAM</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="15">Fact.</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">UU.EE.</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">OC.</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">SIAF</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">FTE FTO</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="15">Fecha Emisión</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="15">Fecha Recepción</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Moneda</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Importe</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Plazo Crédito </th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Días de Retraso</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Estado</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Fase</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="25">Area Responsable</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="25">Vendedor</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="30">Programación de Pago </th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="35">Categoría</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="30">Observaciones</th>

            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Penalidad</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Penalidad Monto</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Retención</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Retención Monto</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Detracción</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Detracción Monto</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $requerimiento)
        <tr>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->codigo_empresa }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->nombre_sector }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->cliente_ruc }}</td>
            <td style="border: 6px solid #000 !important;" ><p>{{ $requerimiento->cliente }}</p></td>
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
            <td style="border: 6px solid #000 !important;"><p>{{ $requerimiento->observacion }}</p></td>

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


