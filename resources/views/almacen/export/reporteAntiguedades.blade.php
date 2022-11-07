<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
</head>

<body>
    <table class="table table-border">
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="15"><b>Código</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Código Softlink</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Part Number</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Categoría</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Descripción</b></th>
                <th style="background-color: #cccccc;" width="8"><b>Moneda</b></th>
                {{-- <th style="background-color: #cccccc;" width="15"><b>Valorizacion</b></th> --}}
                <th style="background-color: #cccccc;" width="15"><b>Costo Promedio</b></th>
                <th style="background-color: #cccccc;" width="8"><b>Unidad</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Stock Actual</b></th>
                {{-- <th style="background-color: #cccccc;" width="12"><b>Reserva</b></th> --}}
                {{-- <th style="background-color: #cccccc;" width="12"><b>Disponible</b></th> --}}
                <th style="background-color: #cccccc;" width="30"><b>Almacén</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Serie</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Fecha 1er ingreso</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Precio Unit.</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Doc. Ingreso</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach($saldos as $item)
            <tr>
                <td>{{ $item['codigo'] }}</td>
                <td>{{ $item['cod_softlink'] }}</td>
                <td>{{ $item['part_number'] }}</td>
                <td>{{ $item['categoria'] }}</td>
                <td>{{ $item['producto'] }}</td>
                <td>{{ $item['simbolo'] }}</td>
                <td>{{ $item['costo_promedio'] }}</td>
                <td>{{ $item['abreviatura'] }}</td>
                <td>{{ $item['stock'] }}</td>
                <td>{{ $item['almacen_descripcion'] }}</td>
                <td>{{ $item['serie'] }}</td>
                <td>{{ $item['fecha_ingreso_soft'] }}</td>
                <td>{{ $item['precio_unitario_soft'] }}</td>
                <td>{{ $item['doc_ingreso_soft'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>