<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Cuadro de Gastos</h2>
    <br>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="25"><b>Empresa</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha pago</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Cod.Req.</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Titulo</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Partida</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Descripción</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cant.</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Unid.</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Unit.</b></th>
                <th style="background-color: #cccccc;" width="18"><b>SubTotal</b></th>
                <th style="background-color: #cccccc;" width="18"><b>IGV</b></th>
                <th style="background-color: #cccccc;" width="18"><b>P.Compra</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($req_compras as $d)
            <tr>
                <td>{{$d->razon_social}}</td>
                <td>{{$d->fecha_pago}}</td>
                <td>{{$d->codigo}}</td>
                <td>{{$d->titulo_descripcion}}</td>
                <td>{{$d->partida_descripcion}}</td>
                <td>{{$d->descripcion_adicional}}</td>
                <td>{{$d->cantidad}}</td>
                <td>{{$d->abreviatura}}</td>
                <td>{{$d->precio}}</td>
                <td>{{$d->subtotal}}</td>
                <td>{{$d->subtotal*0.18}}</td>
                <td>{{$d->subtotal}}</td>
            </tr>
            @endforeach
            @foreach ($req_pagos as $d)
            <tr>
                <td>{{$d->razon_social}}</td>
                <td>{{$d->fecha_pago}}</td>
                <td>{{$d->codigo}}</td>
                <td>{{$d->titulo_descripcion}}</td>
                <td>{{$d->partida_descripcion}}</td>
                <td>{{$d->descripcion}}</td>
                <td>{{$d->cantidad}}</td>
                <td>{{$d->abreviatura}}</td>
                <td>{{$d->precio_unitario}}</td>
                <td>{{$d->subtotal}}</td>
                <td>{{$d->subtotal*0.18}}</td>
                <td>{{$d->subtotal}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>