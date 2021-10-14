<table>
    <thead>
        <tr>
            <th>Cuadro presupuestp</th>
            <th>Proveedor</th>
            <th>Orden compra</th>
            <th>Fecha creación</th>
            <th>Empresa - Sede</th>
            <th>Monto (inc. IGV)</th>
            <th>Estado</th>
            <th>ETA</th>
            <th>Transformaciones</th>
            <th>Cantidad de equipos</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transitoOrdenes as $orden)
        <tr>
            <td>{{ $orden["codigo_oportunidad"] }}</td>
            <td>{{ $orden["razon_social_proveedor"] }}</td>
            <td>{{ $orden["codigo"] }}</td>
            <td>{{ $orden["fecha"] }}</td>
            <td>{{ $orden["sede"] }}</td>
            <td>{{ $orden["moneda"] }}</td>
            <td>{{ $orden["monto"] }}</td>
            <td>{{ $orden["estado"] }}</td>
            <td>{{ $orden["tiene_transformacion"] }}</td>
            <td>{{ $orden["cantidad_equipos"] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>