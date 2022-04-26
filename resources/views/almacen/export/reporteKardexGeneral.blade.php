<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="15">Código</th>
                <th style="background-color: #cccccc;" width="12">Part Number</th>
                <th style="background-color: #cccccc;">Categoría</th>
                <th style="background-color: #cccccc;">SubCategoría</th>
                <th style="background-color: #cccccc;" width="40">Descripción</th>
                <th style="background-color: #cccccc;" width="12">Fecha</th>
                <th style="background-color: #cccccc;" width="20">Almacén</th>
                <th style="background-color: #cccccc;">Und</th>
                <th style="background-color: #cccccc;">Ing.</th>
                <th style="background-color: #cccccc;">Sal.</th>
                <th style="background-color: #cccccc;">Saldo</th>
                <th style="background-color: #cccccc;" width="12">Ing.</th>
                <th style="background-color: #cccccc;" width="12">Sal.</th>
                <th style="background-color: #cccccc;" width="12">Valoriz.</th>
                <th style="background-color: #cccccc;" width="15">Cod.Mov.</th>
                <th style="background-color: #cccccc;">Op</th>
                <th style="background-color: #cccccc;" width="15">Movimiento</th>
                <th style="background-color: #cccccc;" width="12">Guía </th>
                <th style="background-color: #cccccc;" width="12">Transf.</th>
                <th style="background-color: #cccccc;" width="12">O.C.</th>
                <th style="background-color: #cccccc;" width="12">Fact.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item['codigo'] }}</td>
                    <td>{{ $item['prod_part_number'] }}</td>
                    <td>{{ $item['categoria'] }}</td>
                    <td>{{ $item['subcategoria'] }}</td>
                    <td>{{ $item['prod_descripcion'] }}</td>
                    <td>{{ $item['fecha_emision'] }}</td>
                    <td>{{ $item['almacen_descripcion'] }}</td>
                    <td>{{ $item['abreviatura'] }}</td>
                    <td style="background-color: #ffffb0;">{{ ($item['tipo'] == 1 || $item['tipo'] == 0) ? $item['cantidad'] : 0 }}</td>
                    <td style="background-color: #ffffb0;">{{ ($item['tipo'] == 2) ? $item['cantidad'] : 0 }}</td>
                    <td style="background-color: #ffffb0;">{{ $item['saldo'] }}</td>
                    <td style="background-color: #fcd8e4;">{{ ($item['tipo'] == 1 || $item['tipo'] == 0) ? $item['valorizacion'] : 0 }}</td>
                    <td style="background-color: #fcd8e4;">{{ ($item['tipo'] == 2) ? $item['valorizacion'] : 0 }}</td>
                    <td style="background-color: #fcd8e4;">{{ $item['saldo_valor'] }}</td>
                    <td>{{ $item['codigo'] }}</td>
                    <td>{{ $item['cod_sunat_com'] }}</td>
                    <td>{{ ($item['tipo'] == 1) ? $item['tp_com_descripcion'] : $item['tp_ven_descripcion'] }}</td>
                    <td>
                        @php
                        $cod_op = '';
                        if ($item['cod_transformacion'] !== null) {
                            $cod_op = $item['cod_transformacion'];
                        } else if ($item['cod_transferencia'] !== null) {
                            $cod_op = $item['cod_transferencia'];
                        }
                        @endphp
                        {{ $cod_op }}
                    </td>
                    <td></td>
                    <td>{{ $item['docs'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>