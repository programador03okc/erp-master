<table>
    <thead>
        <tr>
            <th>Requerimiento</th>
            <th>Orden servicio</th>
            <th>Cod. Softlink</th>
            <th>Empresa - Sede</th>
            <th>Estado</th>
            <th>Fecha generación OS</th>
            <th>Fecha entrega Servicio</th>
            <th>Observacion</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ordenes as $orden)
        <tr>
            <td>{{ $orden["requerimientos"] }}</td>
            <td>{{ $orden["codigo"] }}</td>
            <td>{{ $orden["codigo_softlink"] }}</td>
            <td>{{ $orden["sede"] }}</td>
            <td>{{ $orden["estado"] }}</td>
            <td>{{ $orden["fecha"] }}</td>
            <td>{{ $orden["fecha_entrega"] }}</td>
            <td>{{ $orden["observacion"] }}</td>

        </tr>
        @endforeach
    </tbody>
</table>