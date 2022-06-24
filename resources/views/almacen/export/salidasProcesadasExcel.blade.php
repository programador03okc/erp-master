<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Salidas Procesadas</h2>
    <br>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="15"><b>Fecha Salida</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Salida</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Orden Despacho</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Guía Venta</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Cliente</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Operación</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Almacén</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Responsable</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Facturas</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Facturas</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Requerimientos</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Estado</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td>{{date('d-m-Y', strtotime($d->fecha_emision))}}</td>
                <td>{{$d->codigo}}</td>
                <td>{{$d->codigo_od}}</td>
                <td>{{$d->serie}} - {{$d->numero}}</td>
                <td>{{$d->razon_social}}</td>
                <td>{{$d->operacion}}</td>
                <td>{{$d->almacen_descripcion}}</td>
                <td>{{$d->nombre_corto}}</td>
                <td>{{$d->comprobantes_venta}}</td>
                <td>{{$d->comprobantes_venta_concat}}</td>
                <td>{{$d->codigo_requerimiento}}</td>
                <td>{{$d->estado==7?"Anulado":"Activo"}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>