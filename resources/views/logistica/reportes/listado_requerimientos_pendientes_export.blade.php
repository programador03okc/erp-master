<table>
    <thead>
        <tr>
            <th>Empresa</th>
            <th>Sede</th>
            <th>Grupo</th>
            <th>Codigo Req.</th>
            <th>Concepto</th>
            <th>Fecha Creación</th>
            <th>Fecha límite</th>
            <th>Tipo req.</th>
            <th>División</th>
            <th>Solicitado por</th>
            <th>Creado por</th>
            <th>Observación</th>
            <th>Estado</th>


        </tr>
    </thead>
    <tbody>
        @foreach ($requerimientos as $requerimiento)

        <tr>
            <td>{{ $requerimiento->codigo_empresa }}</td>
            <td>{{ $requerimiento->codigo_sede }}</td>
            <td>{{ $requerimiento->descripcion_grupo }}</td>
            <td>{{ $requerimiento->codigo }}</td>
            <td>{{ str_replace("'", "", str_replace("", "", $requerimiento->concepto)) }}</td>
            <td>{{ date("d-m-Y", strtotime($requerimiento->fecha_registro)) }}</td>
            <td>{{ date("d-m-Y", strtotime($requerimiento->fecha_entrega)) }}</td>
            <td>{{ $requerimiento->tipo_req_desc }}</td>
            <td>{{ $requerimiento->descripcion_division }}</td>
            <td>{{ ucwords(strtolower($requerimiento->nombre_solicitado_por)) }}</td>
            <td>{{ ucwords(strtolower($requerimiento->nombre_usuario)) }}</td>
            <td>{{ $requerimiento->observacion }}</td>
            <td>{{ $requerimiento->estado_doc }}</td>
        </tr>

        @endforeach
    </tbody>
</table>