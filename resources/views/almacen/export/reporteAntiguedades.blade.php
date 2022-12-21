<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
</head>

<body>
    <h1>Reporte de Antiguedades</h1>
    <table class="table table-border">
        <thead>
            <tr><td></td></tr>
            <tr>
                <td>Tipo de cambio</td>
                <td>:</td>
                <td>{{$tipo_cambio}}</td>
                <td colspan="12"></td>
            </tr>
            <tr><td></td></tr>
            <tr>
                <th style="background-color: #cccccc;" width="15"><b>Código</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Código Softlink</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Part Number</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Categoría</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Descripción</b></th>
                <th style="background-color: #cccccc;" width="8"><b>Moneda</b></th>
                <th style="background-color: #cccccc;" width="8"><b>Unidad</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Almacén</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Serie</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Fecha 1er ingreso</b></th>
                <th style="background-color: #cccccc;" width="5"><b>Mnd.</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Precio Unit.</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Doc. Ingreso</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Unit.Soles</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Unit.Dolares</b></th>
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
                <td>{{ $item['abreviatura'] }}</td>
                <td>{{ $item['almacen_descripcion'] }}</td>
                <td>{{ strval($item['serie']) }}</td>
                <td>{{ $item['fecha_ingreso_soft'] }}</td>
                <td>{{ $item['moneda_soft']==1?'S/':($item['moneda_soft']==2?'$':'') }}</td>
                <td>{{ number_format(round($item['precio_unitario_soft'],2,PHP_ROUND_HALF_UP), 2) }}</td>
                <td>{{ $item['doc_ingreso_soft'] }}</td>
                <td>{{ number_format(round($item['unitario_soles'],2,PHP_ROUND_HALF_UP), 2) }}</td>
                <td>{{ number_format(round($item['unitario_dolares'],2,PHP_ROUND_HALF_UP), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>