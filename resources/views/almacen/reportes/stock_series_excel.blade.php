<table>
    <thead>
        <tr>
            <th>Almacén</th>
            <th>Cód. producto</th>
            <th>Part number</th>
            <th>Serie</th>
            <th>Descripción producto</th>
            <th>Unidad</th>
            <th>Afecto IGV</th>
            <th>Fecha Ingreso</th>
            <th>Fecha Guía Emisión</th>
            <th>Doc. com</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stockSeries as $data)

        <tr>
            <td>{{ $data['almacen'] }}</td>
            <td>{{ $data['codigo_producto'] }}</td>
            <td>{{ $data['part_number'] }}</td>
            <td>{{ $data['serie'] }}</td>
            <td>{{ $data['descripcion'] }}</td>
            <td>{{ $data['unidad_medida'] }}</td>
            <td>{{ $data['afecto_igv'] }}</td>
            <td>{{ $data['fecha_ingreso'] }}</td>
            <td>{{ $data['guia_fecha_emision'] }}</td>
            <td>{{ $data['documento_compra'] }}</td>

        </tr>
        @endforeach
    </tbody>
</table>