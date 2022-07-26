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
            <td>{{ $requerimiento->serie }}</td>
            <td>{{ date("d-m-Y", strtotime($requerimiento->fecha_emision)) }}</td>
            <td>{{ $requerimiento->sede_descripcion }}</td>
            <td>{{ $requerimiento->razon_social }}</td>
            <td>{{ $requerimiento->nombre_corto_trans }}</td>
            <td>{{ $requerimiento->codigo_trans }}</td>

            {{-- @foreach ($requerimientoDetaller as $item)
                @if ($requerimiento->id_guia_ven == $item->id_guia_ven) --}}
                    <th>{{ $requerimiento->id_requerimiento }}</th>
                    <th>{{ $requerimiento->serie_numero }}</th>
                    <th>{{ $requerimiento->empresa_razon_social }}</th>
                    <th>{{ date("d-m-Y", strtotime($requerimiento->fecha_emision)) }}</th>
                    <th>{{ $requerimiento->razon_social }}</th>
                    <th>{{ $requerimiento->simbolo }}</th>
                    <th>{{ round($requerimiento->total_a_pagar, 2) }}</th>
                    <th>{{ $requerimiento->nombre_corto }}</th>
                    <th>{{ $requerimiento->condicion }}</th>
                {{-- @endif
            @endforeach --}}
        </tr>

        @endforeach
    </tbody>
</table>
