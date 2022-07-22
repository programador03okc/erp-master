<table>
    <thead>
        <tr>
            <th>Empresa</th>
            <th>Tipo Doc.</th>
            <th>Serie</th>
            <th>Número</th>
            <th>Cód softlink</th>
            <th>RUC</th>
            <th>Proveedor</th>
            <th>Fecha Emisión</th>
            <th>Condición</th>
            <th>Fecha de vencimiento</th>
            <th>Mnd</th>
            <th>Total a pagar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requerimientos as $requerimiento)
        <tr>
            <td>{{ $requerimiento->razon_social_empresa }}</td>
            <td>{{ $requerimiento->tipo_documento }}</td>
            <td>{{ $requerimiento->serie }}</td>
            <td>{{ $requerimiento->numero }}</td>
            <td>{{ $requerimiento->codigo_softlink }}</td>
            <td>{{ $requerimiento->nro_documento }}</td>
            <td>{{ $requerimiento->razon_social }}</td>
            <td>{{ $requerimiento->fecha_emision }}</td>
            <td>{{ $requerimiento->condicion_pago}}</td>
            <td>{{ $requerimiento->fecha_vcmto }}</td>
            <td>{{ $requerimiento->simbolo }}</td>
            <td>{{ $requerimiento->total_a_pagar }}</td>

        </tr>
        @endforeach
    </tbody>
</table>
