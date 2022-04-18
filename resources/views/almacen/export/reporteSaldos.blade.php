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
                <th style="background-color: #cccccc;" width="40"><b>Descripción</b></th>
                <th style="background-color: #cccccc;" width="8"><b>Moneda</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Valorizacion</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Costo Promedio</b></th>
                <th style="background-color: #cccccc;" width="8"><b>Unidad</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Stock Actual</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Reserva</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Disponible</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Almacén</b></th>
            </tr>
        </thead>
        <tbody>
        @foreach($saldos as $item)
            <tr>
                <td>{{ $item['codigo'] }}</td>
                <td>{{ $item['cod_softlink'] }}</td>
                <td>{{ $item['part_number'] }}</td>
                <td>{{ $item['producto'] }}</td>
                <td>{{ $item['simbolo'] }}</td>
                <td>{{ $item['valorizacion'] }}</td>
                <td>{{ $item['costo_promedio'] }}</td>
                <td>{{ $item['abreviatura'] }}</td>
                <td>{{ $item['stock'] }}</td>
                <td>{{ $item['reserva'] }}</td>
                <td>{{ $item['disponible'] }}</td>
                <td>{{ $item['almacen_descripcion'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
