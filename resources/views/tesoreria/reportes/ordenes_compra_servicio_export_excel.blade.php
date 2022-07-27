<table>
    <thead>
        <tr>
            <th>Prio.</th>
            <th>Cod. Req.</th>
            <th>Emp.</th>
            <th>Código</th>
            <th>Razon social del proveedor</th>
            <th>Fecha de envío a pago</th>
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
            <td>
                @foreach ($requerimiento->requerimientos as $key => $value)
                    {{$value->codigo}}

                @endforeach
            </td>
            <td>{{ $requerimiento->codigo_empresa }}</td>
            <td>{{ $requerimiento->codigo }}</td>
            <td>{{ $requerimiento->razon_social }}</td>
            <td>{{ date("d-m-Y", strtotime($requerimiento->fecha_solicitud_pago)) }}</td>
            <td>{{ $requerimiento->simbolo }}</td>
            <td>{{ round($requerimiento->monto_total, 2)  }}</td>
            <td>{{
                round(($requerimiento->monto_total - $requerimiento->suma_pagado),2)
            }}</td>
            <td>{{ $requerimiento->estado_doc }}</td>
            <td>{{ $requerimiento->nombre_autorizado!='' ? $requerimiento->nombre_autorizado.' el '.date("d-m-Y", strtotime($requerimiento->fecha_autorizacion)) : '' }}</td>

            <th>{{ date("d-m-Y", strtotime($requerimiento->fecha_pago)) }}</th>
            <th>{{$requerimiento->razon_social_empresa}}</th>
            <th>{{$requerimiento->nro_cuenta}}</th>
            <th>{{$requerimiento->observacion}}</th>
            <th>{{$requerimiento->simbolo}}</th>
            <th>{{ round($requerimiento->total_pago, 2) }}</th>
            <th>{{$requerimiento->nombre_corto}}</th>
            <th>{{ date("d-m-Y h:i", strtotime($requerimiento->fecha_registro)) }}</th>
        </tr>
        @endforeach
    </tbody>
</table>
