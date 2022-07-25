<table>
    <thead>
        <tr>
            <th>Fecha de Facturisación Solicitada</th>
            <th>Obs Facturación</th>
            <th>Código</th>
            <th>Concepto</th>
            <th>Sede Req</th>
            <th>Entidad/Clinte</th>
            <th>Responsable</th>
            <th>OCAM</th>
            <th>C.P.</th>

            <th>N° Requerimiento</th>
            <th>Documento</th>
            <th>Empresa</th>
            <th>Fecha Emisión</th>
            <th>Cliente</th>
            <th>Mnd</th>
            <th>Total a pagar</th>
            <th>Registrado por</th>
            <th>Condición Pago</th>



        </tr>
    </thead>
    <tbody>
        @foreach ($requerimientos as $requerimiento)
        <tr>
            <td>{{ date("d-m-Y", strtotime($requerimiento->fecha_facturacion)) }}</td>
            <td>{{ $requerimiento->obs_facturacion }}</td>
            <td>{{ $requerimiento->codigo }}</td>
            <td>{{ $requerimiento->concepto }}</td>
            <td>{{ $requerimiento->sede_descripcion }}</td>
            <td>{{ $requerimiento->razon_social }}</td>
            <td>{{ $requerimiento->nombre_corto }}</td>
            <td>{{ $requerimiento->nro_orden }}</td>
            <td>{{ $requerimiento->codigo_oportunidad }}</td>

            @foreach ($requerimientosDetalle as $item)
            @if ($requerimiento->id_requerimiento == $item->id_requerimiento)
                <th>{{ $item->id_requerimiento }}</th>
                <th>{{ $item->serie_numero }}</th>
                <th>{{ $item->empresa_razon_social }}</th>
                <th>{{ date("d-m-Y", strtotime($item->fecha_emision)) }}</th>
                <th>{{ $item->razon_social }}</th>
                <th>{{ $item->simbolo }}</th>
                <th>{{ round($item->total_a_pagar, 2) }}</th>
                <th>{{ $item->nombre_corto }}</th>
                <th>{{ $item->condicion.''.($item->condicion !== null ? ' '.$item->credito_dias.'días' : '' )  }}</th>
            @endif

            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
