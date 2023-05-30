<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Presupuesto de Interno </h2>
    <br>
    <br>
    <h6>Ordenes</h6>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>FECHA EMISIÓN	</b></th>
                <th style="background-color: #cccccc;" width="30"><b>FECHA APROBACIÓN	</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>REQ</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>ITEM</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>VALOR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>TIPO PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>COD PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOMBRE PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>PARTIDA</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>SUBPARTIDA</b></th>


            </tr>
        </thead>
        <tbody>
            @php
                $total_1 = 0;
            @endphp
            @foreach ($data as $item)

            @if ($item->cuadro===1)

                @foreach ($item->detalle as $key_detalle => $item_detalle)
                    @php
                        $total_1 = $total_1 + $item_detalle->subtotal;
                    @endphp
                    <tr>
                        <td style="vertical-align: baseline;text-align: center;">{{$item->cabecera->fecha_registro}}</td>
                        <td style="vertical-align: text-bottom;text-align: center;">{{$item->fecha_registro}}</td>
                        <td style="vertical-align: text-bottom;text-align: center;">{{$item->cabecera->codigo}}</td>
                        <td style="vertical-align: text-bottom;text-align: center;">{{$item_detalle->descripcion}}</td>
                        <td style="vertical-align: text-bottom;text-align: center;">S/.{{$item_detalle->subtotal}}</td>
                        <td style="vertical-align: text-bottom;text-align: center;">{{$item->tipo}}</td>
                        <td style="vertical-align: text-bottom;text-align: center;">{{$item->codigo_ppt}}</td>
                        <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item->codigo_nombre}}</p></td>
                        <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item->partida_padre_descripcion}}</p></td>
                        <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item->partida_descripcion}}</p></td>
                    </tr>

                @endforeach

            @endif
            @endforeach
            <tr>
                <td style="vertical-align: baseline;text-align: center;"></td>
                <td style="vertical-align: text-bottom;text-align: center;"></td>
                <td style="vertical-align: baseline;text-align: center;"></td>
                <td style="vertical-align: text-bottom;text-align: center;">Total : </td>
                <td style="vertical-align: text-bottom;text-align: center;" >S/.{{$total_1}}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
    <h6>Requerimiento de pago</h6>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>FECHA EMISIÓN	</b></th>
                <th style="background-color: #cccccc;" width="30"><b>FECHA APROBACIÓN	</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>REQ</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>ITEM</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>VALOR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>TIPO PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>COD PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOMBRE PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>PARTIDA</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>SUBPARTIDA</b></th>


            </tr>
        </thead>
        <tbody>
            @php
                $total_2 = 0;
            @endphp
            @foreach ($data as $item)

            @if ($item->cuadro===2)

                @foreach ($item->detalle as $key_detalle => $item_detalle)
                    @php
                        $total_2 = $total_2 + $item_detalle->subtotal;
                    @endphp
                <tr>
                    <td style="vertical-align: baseline;text-align: center;">{{ date("d/m/Y H:i:s", strtotime($item->cabecera->fecha_registro))  }}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{($item->cabecera->fecha_autorizacion? date("d/m/Y H:i:s", strtotime($item->cabecera->fecha_autorizacion)):'-')}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{$item->cabecera->codigo}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{$item_detalle->descripcion}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">S/.{{$item_detalle->subtotal}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{$item->tipo}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{$item->codigo_ppt}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item->codigo_nombre}}</p></td>
                    <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item->partida_padre_descripcion}}</p></td>
                    <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item->partida_descripcion}}</p></td>
                </tr>

                @endforeach

            @endif
            @endforeach
            <tr>
                <td style="vertical-align: baseline;text-align: center;"></td>
                <td style="vertical-align: text-bottom;text-align: center;"></td>
                <td style="vertical-align: baseline;text-align: center;"></td>
                <td style="vertical-align: text-bottom;text-align: center;">Total : </td>
                <td style="vertical-align: text-bottom;text-align: center;" >S/.{{$total_2}}</td>
            </tr>
        </tbody>
    </table>

    <br>
    <br>
    <h6>Total del Presupuesto Interno</h6>
    <table>
        {{-- <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>FECHA EMISIÓN	</b></th>
                <th style="background-color: #cccccc;" width="30"><b>FECHA APROBACIÓN	</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>REQ</b></th>


            </tr>
        </thead> --}}
        <tbody>

            <tr>
                {{-- <td style="vertical-align: baseline;text-align: center;"></td> --}}
                <td style="vertical-align: text-bottom;text-align: center;">Total : </td>
                <td style="vertical-align: text-bottom;text-align: center;" >S/.{{($total_1 + $total_2)}}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
