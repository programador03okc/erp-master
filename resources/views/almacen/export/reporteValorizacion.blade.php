<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table class="table table-border">
        <thead>
            <tr>
                <th style="font-weight: bold; font-size: 24px;" colspan="6" align="center">REPORTE DE STOCK VALORIZADO</th>
            </tr>
            <tr>
                <th style="font-weight: bold;">Al: {{ date('d/m/Y', strtotime($fecha)) }}</th>
                <th style="font-weight: bold;" colspan="4" align="center">{{ $almacen }}</th>
                <th style="font-weight: bold;">TC. {{ $tc }}</th>
            </tr>
            <tr>
                <th style="background-color: #f4f4f4; font-weight: bold;" width="10" rowspan="2">Código</th>
                <th style="background-color: #f4f4f4; font-weight: bold;" width="10" rowspan="2">Código SoftLink</th>
                <th style="background-color: #f4f4f4; font-weight: bold;" width="50" rowspan="2">Producto</th>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;" width="15" rowspan="2">Stock</th>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;" width="15" colspan="2">Valorizacion</th>
            </tr>
            <tr>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;" width="15">Soles</th>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;" width="15">Dolares</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item['codigo'] }}</td>
                    <td>{{ $item['codigo_softlink'] }}</td>
                    <td>{{ $item['producto'] }}</td>
                    <td>{{ $item['stock'] }}</td>
                    <td align="right">{{ $item['valorizacion_sol'] }}</td>
                    <td align="right">{{ $item['valorizacion_dol'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>