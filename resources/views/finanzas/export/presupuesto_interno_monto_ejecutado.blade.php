<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Presupuesto de Interno </h2>
    <br>
    <br>
    <h6>Requerimiento de pago</h6>
    @if (sizeof($data['requerimiento'])>0)
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
                    <th style="background-color: #cccccc;text-align: center;" width="18"><b>DESCRIPCIÓN</b></th>


                </tr>
            </thead>
            <tbody>
                @php
                    $total_1 = 0;
                @endphp


                    @foreach ($data['requerimiento'] as $key_detalle => $item_detalle)
                        @php
                            $total_1 = $total_1 + $item_detalle->importe_historial;
                        @endphp
                        <tr>
                            <td style="vertical-align: baseline;text-align: center;">{{ date("d/m/Y H:i:s", strtotime($item_detalle->fecha_registro_req))  }}</td>
                            <td style="vertical-align: text-bottom;text-align: center;">{{ date("d/m/Y H:i:s", strtotime($item_detalle->fecha_registro))}}</td>
                            <td style="vertical-align: text-bottom;text-align: center;">{{$item_detalle->codigo_req}}</td>
                            <td style="">{{$item_detalle->descripcion}}</td>
                            <td style="vertical-align: text-bottom;text-align: center;">S/.{{$item_detalle->importe_historial}}</td>
                            <td style="vertical-align: text-bottom;text-align: center;">{{$item_detalle->tipo}}</td>

                            <td style="vertical-align: text-bottom;text-align: center;">{{ $item_detalle->presupuesto_codigo}}</td>
                            <td style="">{{ $item_detalle->presupuesto_descripcion}}</td>
                            <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item_detalle->codigo_partida}}</p></td>
                            <td style="vertical-align: text-bottom;text-align: center;"><p>{{$item_detalle->codigo_descripcion}}</p></td>
                        </tr>

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
    @endif

    <br>
    <br>
    @if (sizeof($data['orden'])>0)
   <h6>Ordenes</h6>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>FECHA EMISIÓN	</b></th>
                <th style="background-color: #cccccc;" width="18"><b>FECHA AUTORIZACIÓN	</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>C. ORDEN</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>C. REQUERIMIENTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>DESCRIPCIÓN</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>VALOR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>TIPO PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>COD PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOMBRE PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>PARTIDA</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>DESCRIPCIÓN</b></th>


            </tr>
        </thead>
        <tbody>
            @php
                $total_2 = 0;
            @endphp
            @foreach ($data['orden'] as $item)
                    @php
                        $total_2 = $total_2 + $item->importe_historial;
                    @endphp
                <tr>
                    <td style="vertical-align: baseline;text-align: center;">{{ date("d/m/Y H:i:s", strtotime($item->fecha_registro))  }}</td>
                    <td style="vertical-align: baseline;text-align: center;">{{ date("d/m/Y H:i:s", strtotime($item->fecha_autorizacion))  }}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{$item->codigo_orden}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{$item->codigo_req}}</td>
                    <td style="">{{$item->descripcion_adicional}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">S/.{{$item->importe_historial}}</td>
                    <td style="vertical-align: text-bottom;text-align: center;">{{$item->tipo}}</td>

                    <td style="">{{ $item_detalle->presupuesto_codigo}}</td>
                    <td style="">{{ $item_detalle->presupuesto_descripcion}}</td>
                    <td style=""><p>{{$item_detalle->codigo_partida}}</p></td>
                    <td style=""><p>{{$item_detalle->codigo_descripcion}}</p></td>
                </tr>
            @endforeach
            <tr>
                <td style="vertical-align: baseline;text-align: center;"></td>
                <td style="vertical-align: text-bottom;text-align: center;"></td>
                <td style="vertical-align: text-bottom;text-align: center;"></td>
                <td style="vertical-align: baseline;text-align: center;"></td>
                <td style="vertical-align: text-bottom;text-align: center;">Total : </td>
                <td style="vertical-align: text-bottom;text-align: center;" >S/.{{$total_2}}</td>
            </tr>
        </tbody>
    </table>
    @endif


    <br>
    <br>
    <h6>Total del Presupuesto Interno</h6>
    <table>
        <tbody>

            <tr>
                <td style="vertical-align: text-bottom;text-align: center;">Total : </td>
                <td style="vertical-align: text-bottom;text-align: center;" >S/.{{($total_1 + $total_2)}}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
