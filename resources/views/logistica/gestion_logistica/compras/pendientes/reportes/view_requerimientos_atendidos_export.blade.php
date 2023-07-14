<table>
    <thead>
        <tr>
            <th>Empresa/Sede</th>
            <th>Código</th>
            <th>Fecha creación</th>
            <th>Fecha Limite</th>
            <th>Concepto</th>
            <th>Tipo Req.</th>
            <th>División</th>
            <th>Creado por</th>
            <th>Solicitado por</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requerimientos as $requerimiento)
        <tr>
            <td>{{ $requerimiento["empresa_sede"] }}</td>
            <td>{{$requerimiento["codigo"]}}</td>
            <td>{{$requerimiento["fecha_registro"]}}</td>
            <td>{{$requerimiento["fecha_entrega"]}}</td>
            <td>{{$requerimiento["concepto"]}}</td>
            <td>{{$requerimiento["tipo_req_desc"]}}</td>
            <td>{{$requerimiento["division"]}}</td>
            <td>{{$requerimiento["nombre_usuario"]}}</td>
            <td>{{$requerimiento["nombre_solicitado_por"]}}</td>
            <td>{{$requerimiento["estado_doc"]}}</td>
        </tr>
        @endforeach
    </tbody>
</table>