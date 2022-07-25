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

            <th>Fecha Pago</th>
            <th>Empresa</th>
            <th>Cuenta origen</th>
            <th>Motivo</th>
            <th>Mnd</th>
            <th>Total Pago</th>
            <th>Registrado por</th>
            <th>Fecha Registro</th>

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

            @foreach ($requerimientosDetalle as $item)
                @if ($item->id_requerimiento_pago == $requerimiento->id_requerimiento_pago)
                    <th>{{ date("d-m-Y", strtotime($item->fecha_pago)) }}</th>
                    <th>{{$item->razon_social_empresa}}</th>
                    <th>{{$item->nro_cuenta}}</th>
                    <th>{{$item->observacion}}</th>
                    <th>{{$item->simbolo}}</th>
                    <th>{{$item->total_pago}}</th>
                    <th>{{$item->nombre_corto}}</th>
                    <th>{{ date("d-m-Y h:i", strtotime($item->fecha_registro)) }}</th>
                @endif

            @endforeach

        </tr>
        @endforeach
    </tbody>
</table>
