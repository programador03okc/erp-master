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

                <th>{{ $requerimiento->id_requerimiento }}</th>
                <th>{{ $requerimiento->serie_numero }}</th>
                <th>{{ $requerimiento->empresa_razon_social }}</th>
                <th>{{ date("d-m-Y", strtotime($requerimiento->fecha_emision)) }}</th>
                <th>{{ $requerimiento->razon_social }}</th>
                <th>{{ $requerimiento->simbolo }}</th>
                <th>{{ round($requerimiento->total_a_pagar, 2) }}</th>
                <th>{{ $requerimiento->nombre_corto }}</th>
                <th>{{ $requerimiento->condicion  }}</th>
        </tr>
        @endforeach
    </tbody>
</table>
