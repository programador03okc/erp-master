<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Despachos Pendientes</h2>
    <br>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>Cod.Despacho</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cod.Req.</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha despacho</b></th>
                <th style="background-color: #cccccc;" width="18"><b>CDP</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Cliente</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Almacén</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cod.Producto</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Part Number</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Descripción</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cantidad</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Und</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Almacen reserva</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cant.Reservada</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cant.Despachada</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td>{{$d->codigo}}</td>
                <td>{{$d->codigo_req}}</td>
                <td>{{$d->fecha_despacho}}</td>
                <td>{{$d->codigo_oportunidad}}</td>
                <td>{{$d->razon_social}}</td>
                <td>{{$d->almacen_descripcion}}</td>
                <td>{{$d->codigo_producto}}</td>
                <td>{{$d->part_number}}</td>
                <td>{{$d->descripcion}}</td>
                <td>{{$d->cantidad}}</td>
                <td>{{$d->abreviatura}}</td>
                <td>{{$d->almacen_reserva}}</td>
                <td>{{$d->stock_comprometido}}</td>
                <td>{{$d->cantidad_despachada}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>