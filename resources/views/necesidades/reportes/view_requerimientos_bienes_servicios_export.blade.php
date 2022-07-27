<table>
    <thead>
        <tr>
            <th>Prioridad</th>
            <th>Requerimiento</th>
            <th>Concepto</th>
            <th>Fecha Registro</th>
            <th>Fecha Entrega</th>
            <th>Tipo Req.</th>
            <th>Empresa</th>
            <th>Grupo</th>
            <th>División</th>
            <th>Proyecto/presupuesto</th>
            <th>Moneda</th>
            <th>Monto Total</th>
            <th>Observacion</th>
            <th>Creado por</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requerimientos as $requerimiento)
        <tr>
            <td>{{ $requerimiento["priori"] }}</td>
            <td>{{ $requerimiento["codigo"] }}</td>
            <td>{{ $requerimiento["concepto"] }}</td>
            <td>{{ $requerimiento["fecha_registro"] }}</td>
            <td>{{ $requerimiento["fecha_entrega"] }}</td>
            <td>{{ $requerimiento["tipo_requerimiento"] }}</td>
            <td>{{ $requerimiento["razon_social"] }}</td>
            <td>{{ $requerimiento["grupo"] }}</td>
            <td>{{ $requerimiento["division"] }}</td>
            <td>{{ $requerimiento["descripcion_proyecto"] }}</td>
            <td>{{$requerimiento["simbolo_moneda"]}}</td>
            <td>{{ $requerimiento["monto_total"] }}</td>
            <td>{{ $requerimiento["observacion"] }}</td>
            <td>{{ $requerimiento["nombre_usuario"] }}</td>
            <td>{{ $requerimiento["estado_doc"] }}</td>

        </tr>
        @endforeach
    </tbody>
</table>
