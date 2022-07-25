<table>
    <thead>
        <tr>
            <th>Guía</th>
            <th>Fecha Guía</th>
            <th>Sede Guía</th>
            <th>Entidad Cliente</th>
            <th>Responsable</th>
            <th>Cod. Trans.</th>

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
            <td>{{ $requerimiento->serie.'-'.$requerimiento->numero }}</td>
            <td>{{ date("d-m-Y", strtotime($requerimiento->fecha_emision)) }}</td>
            <td>{{ $requerimiento->sede_descripcion }}</td>
            <td>{{ $requerimiento->razon_social }}</td>
            <td>{{ $requerimiento->nombre_corto_trans }}</td>
            <td>{{ $requerimiento->codigo_trans }}</td>

            @foreach ($requerimientoDetaller as $item)
                @if ($requerimiento->id_guia_ven == $item->id_guia_ven)
                    <th>{{ $item->id_requerimiento }}</th>
                    <th>{{ $item->serie_numero }}</th>
                    <th>{{ $item->empresa_razon_social }}</th>
                    <th>{{ date("d-m-Y", strtotime($item->fecha_emision)) }}</th>
                    <th>{{ $item->razon_social }}</th>
                    <th>{{ $item->simbolo }}</th>
                    <th>{{ round($item->total_a_pagar, 2) }}</th>
                    <th>{{ $item->nombre_corto }}</th>
                    <th>{{ $item->condicion.' '.$item->credito_dias.' días' }}</th>
                @endif
            @endforeach
        </tr>

        @endforeach
    </tbody>
</table>
