<table>
    <thead>
        <tr>
            <th>Cuadro costos</th>
            <th>Orden compra</th>
            <th>Empresa - Sede</th>
            <th>Estado</th>
            <th>Fecha vencimiento CC</th>
            <th>Estado aprobación CC</th>
            <th>Fecha aprobación CC</th>
            <th>Días de atención CC</th>
            <th>Condición</th>
            <th>Fecha generación OC</th>
            <th>Días de entrega</th>
            <th>Condición 2</th>
            <th>Fecha entrega</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ordenes as $orden)
        <tr>
            <td>{{ $orden["codigo_oportunidad"] }}</td>
            <td>{{ $orden["codigo"] }}</td>
            <td>{{ $orden["sede"] }}</td>
            <td>{{ $orden["cuadro_costo_fecha_limite"] }}</td>
            <td>{{ $orden["cuadro_costo_estado_aprobacion_cuadro"] }}</td>
            <td>{{ $orden["cuadro_costo_estado_fecha_estado"] }}</td>
            <td>{{ $orden["dias_restantes_atencion_cc"] }}</td>
            <td>{{ $orden["condicion1"] }}</td>
            <td>{{ $orden["fecha"] }}</td>
            <td>{{ $orden["dias_entrega"] }}</td>
            <td>{{ $orden["condicion2"] }}</td>
            <td>{{ $orden["fecha_entrega"] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>