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

            <th>N° de Documento</th>
            <th>Destinatario</th>
            <th>Tipo de cuenta</th>
            <th>Banco</th>
            <th>Cuenta bancaria</th>
            <th>Cuenta CCI</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($data as $requerimiento)
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
            <td>{{ round(($requerimiento->saldo),2) }}</td>
            <td>{{ $requerimiento->estado_doc }}</td>
            <td>{{ $requerimiento->nombre_autorizado }}</td>

            <td>{{ $requerimiento->nro_documento }}</td>
            <td>{{ $requerimiento->razon_social }}</td>
            <td>{{ $requerimiento->tipo_cuenta }}</td>
            <td>{{ $requerimiento->banco_contribuyente }}</td>
            <td>{{ $requerimiento->nro_cuenta }}</td>
            <td>{{ $requerimiento->nro_cuenta_interbancaria }}</td>



        </tr>
        @endforeach
    </tbody>
</table>
