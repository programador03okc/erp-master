<table>
    <thead>
        <tr>
            <th style="background-color: #cccccc;text-align:center;">Prio.</th>
            <th style="background-color: #cccccc;text-align:center;">Emp.</th>
            <th style="background-color: #cccccc;text-align:center;">Código</th>
            <th style="background-color: #cccccc;text-align:center;" width="80">Concepto</th>
            <th style="background-color: #cccccc;text-align:center;">Elaborado por</th>
            <th style="background-color: #cccccc;text-align:center;">Fecha Emisión</th>
            <th style="background-color: #cccccc;text-align:center;">Mnd</th>
            <th style="background-color: #cccccc;text-align:center;">Total</th>
            <th style="background-color: #cccccc;text-align:center;">Saldo</th>
            <th style="background-color: #cccccc;text-align:center;">Estado</th>
            <th style="background-color: #cccccc;text-align:center;" width="30">Autorizado por</th>

            <th style="background-color: #cccccc;text-align:center;" width="30">N° de Documento</th>
            <th style="background-color: #cccccc;text-align:center;" width="60">Destinatario</th>
            <th style="background-color: #cccccc;text-align:center;" width="20">Tipo de cuenta</th>
            <th style="background-color: #cccccc;text-align:center;" width="40">Banco</th>
            <th style="background-color: #cccccc;text-align:center;" width="30">Cuenta bancaria</th>
            <th style="background-color: #cccccc;text-align:center;" width="30">Cuenta CCI</th>

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
            <td>{{ $requerimiento->fecha_registro!=null ? date("d-m-Y", strtotime($requerimiento->fecha_registro)) : '' }}</td>
            <td>{{ $requerimiento->simbolo }}</td>
            <td>{{ round($requerimiento->monto_total, 2)  }}</td>
            <td>{{ round(($requerimiento->saldo),2) }}</td>
            <td>{{ $requerimiento->estado_doc }}</td>
            <td>{{ $requerimiento->nombre_autorizado }}</td>

            @if($requerimiento->id_tipo_destinatario ==2)
                <td>{{ $requerimiento->nro_documento }}</td>
                <td>{{ $requerimiento->razon_social }}</td>
                <td>{{ $requerimiento->tipo_cuenta }}</td>
                <td>{{ $requerimiento->banco_contribuyente }}</td>
                <td>{{ $requerimiento->nro_cuenta }}</td>
                <td>{{ $requerimiento->nro_cuenta_interbancaria }}</td>
            @elseif($requerimiento->id_tipo_destinatario ==1)
                <td>{{ $requerimiento->nro_documento_persona }}</td>
                <td>{{ $requerimiento->nombre_completo_persona }}</td>
                <td>{{ $requerimiento->tipo_cuenta_persona }}</td>
                <td>{{ $requerimiento->banco_persona }}</td>
                <td>{{ $requerimiento->nro_cuenta_persona }}</td>
                <td>{{ $requerimiento->nro_cci_persona }}</td>
            @endif


        </tr>
        @endforeach
    </tbody>
</table>
