
<h3>POWER BI -Reporte Resumen </h3>
<table>
    <thead>
        <tr style="text-align: center;">
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Empresa</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="30">Cliente</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="15">Sector</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="25">Tipo de venta</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="25">Tipo de documento</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="15">Número de documento</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Moneda</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Importe original</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Importe Soles</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Fecha emisión</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="15">Fecha recepción</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="15">Plazo crédito</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Fecha de vencimiento</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Días</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Días para cobranza</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Condición</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="20">Rango</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Estado</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="10">Año</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="25">CONTROVERSIA</th>
            <th style="border: 6px solid #000 !important;text-align: center;" width="30">COMENTARIO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $requerimiento)
        <tr>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->empresa }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->cliente }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->nombre_sector }}</td>
            <td style="border: 6px solid #000 !important;" >{{ $requerimiento->ocam }}</td>
            <td style="border: 6px solid #000 !important;">{{ 'Factura' }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->factura }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->moneda }}</td>
            <td style="border: 6px solid #000 !important;">{{ number_format($requerimiento->importe, 2) }}</td>
            <td style="border: 6px solid #000 !important;">{{ ($requerimiento->importe * 3.95) }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->fecha_emision }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->fecha_recepcion }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->plazo_credito }}</td>

            <td style="border: 6px solid #000 !important;">{{ date("d-m-Y",strtotime($requerimiento->fecha_emision."+ ".$requerimiento->plazo_credito." days")) }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->dias }}</td>

            <td style="border: 6px solid #000 !important;">{{ $requerimiento->dias_cobrar }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->condicion }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->rango }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->estado }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->periodo }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->controversia }}</td>
            <td style="border: 6px solid #000 !important;">{{ $requerimiento->comentarios }}</td>

        </tr>
        @endforeach
    </tbody>
</table>


