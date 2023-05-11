<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <style>
        .text-center-booton{
            vertical-align: text-bottom ;
            text-align: center;
        }
    </style>
    <h2>Reporte de Presupuesto de Interno </h2>
    <br>
    <br>
    <h6>Monto ejecutado</h6>
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
            @foreach ($data as $item)
                @foreach ($item->detalle as $key_detalle => $item_detalle)
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


            @endforeach
        </tbody>
    </table>
</body>
</html>
