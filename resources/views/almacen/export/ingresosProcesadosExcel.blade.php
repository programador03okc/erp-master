<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Ingresos Procesados</h2>
    <label>Del {{date('d-m-Y', strtotime($finicio))}} al {{date('d-m-Y', strtotime($ffin))}}</label>
    <br>
    <br>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>Fecha Ingreso</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Ingreso</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Guía Compra</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Proveedor</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Operación</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Almacén</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Responsable</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Ordenes</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Facturas</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Facturas</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Requerimientos</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td>{{date('d-m-Y H:i', strtotime($d->fecha_emision))}}</td>
                <td>{{$d->codigo}}</td>
                <td>{{$d->serie}} - {{$d->numero}}</td>
                <td>{{$d->razon_social}}</td>
                <td>{{$d->operacion_descripcion}}</td>
                <td>{{$d->almacen_descripcion}}</td>
                <td>{{$d->nombre_corto}}</td>
                <td>{{$d->ordenes_compra}}</td>
                <td>{{implode(', ', $d->comprobantes['codigo'])}}</td>
                <td>{{implode(', ', $d->comprobantes['codigo_concat'])}}</td>
                <td>{{$d->requerimientos}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>