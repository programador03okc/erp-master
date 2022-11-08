<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table class="table table-border">
        <thead>
            <tr>
                <th style="font-weight: bold; font-size: 24px;" colspan="8" align="center">REPORTE DE STOCK VALORIZADO</th>
            </tr>
            <tr>
                <th style="font-weight: bold;">Al: {{ date('d/m/Y', strtotime($fecha)) }}</th>
                <th style="font-weight: bold;" colspan="6" align="center">{{ $almacen }}</th>
                <th style="font-weight: bold;">TC. {{ $tc }}</th>
            </tr>
            <tr>
                <th style="background-color: #f4f4f4; font-weight: bold;" width="10" rowspan="2">Código</th>
                <th style="background-color: #f4f4f4; font-weight: bold;" width="10" rowspan="2">Código SoftLink</th>
                <th style="background-color: #f4f4f4; font-weight: bold;" width="10" rowspan="2">Categoría</th>
                <th style="background-color: #f4f4f4; font-weight: bold;" width="10" rowspan="2">Part Number</th>
                <th style="background-color: #f4f4f4; font-weight: bold;" width="50" rowspan="2">Producto</th>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;" width="15" rowspan="2">Stock</th>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;" width="15" colspan="2">Costo Promedio</th>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;" width="15" colspan="2">Valorizacion</th>
            </tr>
            <tr>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;">Moneda</th>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;">Costo U.</th>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;" width="15">Soles</th>
                <th align="center" style="background-color: #f4f4f4; font-weight: bold;" width="15">Dolares</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_soles=0;
                $total_dolares=0;
            @endphp
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item['codigo'] }}</td>
                    <td>{{ $item['cod_softlink'] }}</td>
                    <td>{{ $item['categoria'] }}</td>
                    <td>{{ $item['part_number'] }}</td>
                    <td>{{ $item['producto'] }}</td>
                    <td>{{ $item['stock'] }}</td>
                    <td align="right">{{ $item['simbolo'] }}</td>
                    <td align="right">{{ $item['costo_promedio'] }}</td>
                    <td align="right">{{ $item['simbolo']!=='$' ? $item['valorizacion'] : '' }}</td>
                    <td align="right">{{ $item['simbolo']==='$' ? $item['valorizacion'] : '' }}</td>
                </tr>
                @php
                    $item['simbolo']!=='$' ? ($total_soles = floatval($total_soles) + floatval($item['valorizacion']))  : '';
                    $item['simbolo']==='$' ? ($total_dolares = floatval($total_dolares) + floatval($item['valorizacion']))  : '';
                @endphp
            @endforeach
            <tr>
                {{-- <td rowspan="2"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"></td> --}}
                <td align="right"  colspan="8">TOTAL =</td>
                <td align="right">{{ $total_soles }}</td>
                <td align="right">{{ $total_dolares }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
