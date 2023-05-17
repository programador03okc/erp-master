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

        </tr>
    </thead>
    <tbody>
        @foreach ($data as $requerimiento)
        <tr>
            <td>{{ $requerimiento->prioridad }}</td>
            <td>
                @foreach($requerimiento->requerimientos as $value)
                @if ($value->codigo)
                    <p>{{$value->codigo}}</p>
                @endif

                @endforeach

            </td>
            <td>{{ $requerimiento->codigo_empresa }}</td>
            <td>{{ $requerimiento->codigo }}</td>
            <td>{{ $requerimiento->razon_social }}</td>
            <td>{{ date("d-m-Y", strtotime($requerimiento->fecha_solicitud_pago)) }}</td>
            <td>{{ $requerimiento->simbolo }}</td>
            <td>{{ round($requerimiento->monto_total, 2)  }}</td>
            <td>{{ round($requerimiento->saldo,2) }}</td>
            <td>{{ $requerimiento->tiene_pago_en_cuotas }}</td>
            <td>{{ $requerimiento->estado_doc }}</td>
            <th>{{ $requerimiento->nombre_autorizado }}</th>
        </tr>
        @endforeach
    </tbody>
</table>
