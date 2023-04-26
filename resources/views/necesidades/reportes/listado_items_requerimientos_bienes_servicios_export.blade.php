<table>
    <thead>
        <tr>
            <th>Prioridad</th>
            <th>Requerimiento</th>
            <th>CDP</th>
            <th>Partida</th>
            <th>Cod.sub Partida</th>
            <th>Des.sub Partida</th>
            <th>Cod.Centro Costo</th>
            <th>Des.Centro Costo</th>
            <th>Motivo</th>
            <th>Concepto</th>
            <th>Item</th>
            <th>Fecha Registro</th>
            <th>Tipo Requerimiento</th>
            <th>Empresa</th>
            <th>Sede</th>
            <th>Grupo</th>
            <th>División</th>
            <th>Proyecto</th>
            <th>Cantidad</th>
            <th>Precio Unitario (Sin IGV)</th>
            <th>Subtotal</th>
            <th>Moneda</th>
            <th>Observación</th>
            <th>Estado Requerimiento</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
                <tr>
                    <td>{{ $item['prioridad'] }}</td>
                    <td>{{ $item['codigo'] }}</td>
                    <td>{{ $item['codigo_oportunidad'] }}</td>
                    <td>{{ strtoupper($item['descripcion_partida_padre']) }}</td>
                    <td>{{ $item['partida'] }}</td>
                    <td>{{ $item['descripcion_partida'] }}</td>
                    <td>{{ $item['centro_costo'] }}</td>
                    <td>{{ $item['descripcion_centro_costo'] }}</td>
                    <td>{{ $item['motivo'] }}</td>
                    <td>{{ $item['concepto'] }}</td>
                    <td>{{ $item['descripcion'] }}</td>
                    <td>{{ $item['fecha_registro'] }}</td>
                    <td>{{ $item['tipo_requerimiento'] }}</td>
                    <td>{{ $item['empresa_razon_social'] }}</td>
                    <td>{{ $item['sede'] }}</td>
                    <td>{{ $item['grupo'] }}</td>
                    <td>{{ $item['division'] }}</td>
                    <td>{{ $item['descripcion_proyecto'] }}</td>
                    <td>{{ $item['cantidad'] }}</td>
                    <td>{{ $item['precio_unitario'] }}</td>
                    <td>{{ $item['subtotal'] }}</td>
                    <td>{{ $item['simbolo_moneda'] }}</td>
                    <td>{{ $item['observacion'] }}</td>
                    <td>{{ $item['estado_requerimiento'] }}</td>
                </tr>
        @endforeach
    </tbody>
</table>