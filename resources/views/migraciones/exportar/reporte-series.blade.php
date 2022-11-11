<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table class="table table-border">
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="15"><b>Almacen</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Código SoftLink</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Descripción del producto</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Fecha</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Periodo</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Documento</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Cantidad</b></th>
                <th style="background-color: #cccccc;" width="70"><b>Series</b></th>
            </tr>
        </thead>
        <tbody>
        @foreach($lista as $item)
            <tr>
                <td>{{ $item['almacen'] }}</td>
                <td>{{ $item['codigo'] }}</td>
                <td>{{ $item['producto'] }}</td>
                <td>{{ $item['fecha'] }}</td>
                <td>{{ $item['periodo'] }}</td>
                <td>{{ $item['documento'] }}</td>
                <td>{{ $item['total'] }}</td>
                <td>{{ $item['series'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>