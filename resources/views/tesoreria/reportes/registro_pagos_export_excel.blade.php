<table>
    <thead>
        <tr>
            <th>Prio.</th>
            <th>Emp.</th>
            <th>Código</th>
            <th>Concepto</th>
            <th>Elaborado por</th>
            <th>Destinatrio</th>
            <th>Fecha Emisión</th>
            <th>Mnd</th>
            <th>Total</th>
            <th>Saldo</th>
            <th>Estado</th>
            <th>Autorizado por</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($requerimientos as $requerimiento)
        <tr>
            <td>{{ $requerimiento->prioridad }}</td>
            <td>{{ $requerimiento->codigo_empresa }}</td>
            <td>{{ $requerimiento->codigo }}</td>
            <td>{{ $requerimiento->concepto }}</td>
            <td>{{ $requerimiento->nombre_corto }}</td>
            <td>{{ $requerimiento->persona }}</td>
            <td>{{ $requerimiento->fecha_registro!=null ? date("d-m-Y", strtotime($requerimiento->fecha_registro)) : '' }}</td>
            <td>{{ $requerimiento->simbolo }}</td>
            <td>{{ round($requerimiento->monto_total, 2)  }}</td>
            <td>{{
                round(($requerimiento->monto_total - $requerimiento->suma_pagado),2)
            }}</td>
            <td>{{ $requerimiento->estado_doc }}</td>
            <td>{{ $requerimiento->nombre_autorizado!='' ? $requerimiento->nombre_autorizado.' el '.date("d-m-Y", strtotime($requerimiento->fecha_autorizacion)) : '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>