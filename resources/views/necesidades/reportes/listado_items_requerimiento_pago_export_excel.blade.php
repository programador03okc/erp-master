<table>
    <thead>
        <tr>
            <th>Prioridad</th>
            <th>Requerimiento</th>
            <th>CDP</th>
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
            <th>Moneda</th>
            <th>Monto Total</th>
            <th>Observación</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
                <tr>
                    <td>{{ $item['prioridad'] }}</td>
                    <td>{{ $item['codigo'] }}</td>
                    <td>{{ $item['codigo_oportunidad'] }}</td>
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
                    <td>{{ $item['simbolo_moneda'] }}</td>
                    <td>{{ $item['monto_total'] }}</td>
                    <td>{{ $item['comentario'] }}</td>
                </tr>
        @endforeach
    </tbody>
</table>