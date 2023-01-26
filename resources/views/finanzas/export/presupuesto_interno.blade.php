<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Presupuesto de interno</h2>
    <br>
    <br>
    <table class="">
        <tbody>
            <tr>
                <td>Código :</td>
                <td>{{$presupuesto_interno->codigo}}</td>
            </tr>
            <tr>
                <td>Grupo :</td>
                <td>{{$presupuesto_interno->grupo}}</td>
            </tr>
            <tr>
                <td>Área :</td>
                <td>{{$presupuesto_interno->area}}</td>
            </tr>
            <tr>
                <td>Moneda :</td>
                <td>{{$presupuesto_interno->moneda.' ('.$presupuesto_interno->simbolo.')'}}</td>
            </tr>
            <tr>
                <td>Descripción :</td>
                <td>{{$presupuesto_interno->descripcion}}</td>
            </tr>
        </tbody>
    </table>
    @if (sizeof($ingresos)>0)
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>PARTIDA</b></th>
                <th style="background-color: #cccccc;" width="30"><b>DESCRIPCION</b></th>
                {{-- <th style="background-color: #cccccc;" width="18"><b>%</b></th> --}}
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>ENE</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>FEB</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>MAR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>ABR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>MAY</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>JUN</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>JUL</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>AGO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>SET</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>OCT</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOV</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>DIC</b></th>

            </tr>
        </thead>
        <tbody>
            @foreach ($ingresos as $item)
            <tr>
                <td>{{$item->partida}}</td>
                <td>{{$item->descripcion}}</td>
                {{-- <td>{{$item->descripcion}}</td> --}}
                <td style="text-align: right;">{{$item->enero}}</td>
                <td style="text-align: right;">{{$item->febrero}}</td>
                <td style="text-align: right;">{{$item->marzo}}</td>
                <td style="text-align: right;">{{$item->abril}}</td>
                <td style="text-align: right;">{{$item->mayo}}</td>
                <td style="text-align: right;">{{$item->junio}}</td>
                <td style="text-align: right;">{{$item->julio}}</td>
                <td style="text-align: right;">{{$item->agosto}}</td>
                <td style="text-align: right;">{{$item->setiembre}}</td>
                <td style="text-align: right;">{{$item->octubre}}</td>
                <td style="text-align: right;">{{$item->noviembre}}</td>
                <td style="text-align: right;">{{$item->diciembre}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
    @if (sizeof($costos)>0)
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>PARTIDA</b></th>
                <th style="background-color: #cccccc;" width="30"><b>DESCRIPCION</b></th>
                <th style="background-color: #cccccc;" width="10"><b>%</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>ENE</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>FEB</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>MAR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>ABR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>MAY</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>JUN</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>JUL</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>AGO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>SET</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>OCT</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOV</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>DIC</b></th>

            </tr>
        </thead>
        <tbody>
            @foreach ($costos as $item)
            <tr>
                <td>{{$item->partida}}</td>
                <td>{{$item->descripcion}}</td>
                <td >{{($item->registro==='1'?'':$item->porcentaje_costo.'%')}} </td>
                <td style="text-align: right;">{{$item->enero}}</td>
                <td style="text-align: right;">{{$item->febrero}}</td>
                <td style="text-align: right;">{{$item->marzo}}</td>
                <td style="text-align: right;">{{$item->abril}}</td>
                <td style="text-align: right;">{{$item->mayo}}</td>
                <td style="text-align: right;">{{$item->junio}}</td>
                <td style="text-align: right;">{{$item->julio}}</td>
                <td style="text-align: right;">{{$item->agosto}}</td>
                <td style="text-align: right;">{{$item->setiembre}}</td>
                <td style="text-align: right;">{{$item->octubre}}</td>
                <td style="text-align: right;">{{$item->noviembre}}</td>
                <td style="text-align: right;">{{$item->diciembre}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
    @if (sizeof($gastos)>0)
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>PARTIDA</b></th>
                <th style="background-color: #cccccc;" width="30"><b>DESCRIPCION</b></th>
                {{-- <th style="background-color: #cccccc;" width="10"><b>%</b></th> --}}
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>ENE</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>FEB</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>MAR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>ABR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>MAY</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>JUN</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>JUL</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>AGO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>SET</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>OCT</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOV</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>DIC</b></th>

            </tr>
        </thead>
        <tbody>
            @foreach ($gastos as $item)
            <tr>
                <td>{{$item->partida}}</td>
                <td>{{$item->descripcion}}</td>
                {{-- <td >{{($item->registro==='1'?'':$item->porcentaje_costo.'%')}} </td> --}}
                <td style="text-align: right;">{{$item->enero}}</td>
                <td style="text-align: right;">{{$item->febrero}}</td>
                <td style="text-align: right;">{{$item->marzo}}</td>
                <td style="text-align: right;">{{$item->abril}}</td>
                <td style="text-align: right;">{{$item->mayo}}</td>
                <td style="text-align: right;">{{$item->junio}}</td>
                <td style="text-align: right;">{{$item->julio}}</td>
                <td style="text-align: right;">{{$item->agosto}}</td>
                <td style="text-align: right;">{{$item->setiembre}}</td>
                <td style="text-align: right;">{{$item->octubre}}</td>
                <td style="text-align: right;">{{$item->noviembre}}</td>
                <td style="text-align: right;">{{$item->diciembre}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif

</body>
</html>
