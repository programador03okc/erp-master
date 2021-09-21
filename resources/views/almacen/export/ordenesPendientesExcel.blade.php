<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Ordenes Pendientes</h2>
    <label>Del {{date('d-m-Y', strtotime($finicio))}} al {{date('d-m-Y', strtotime($ffin))}}</label>
    <br>
    <br>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>Orden SoftLink</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Código Orden</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Proveedor</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha emisión</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Sede Orden</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Creado por</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Estado</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td>{{$d->codigo_softlink}}</td>
                <td>{{$d->codigo}}</td>
                <td>{{$d->razon_social}}</td>
                <td>{{date('d-m-Y H:i', strtotime($d->fecha))}}</td>
                <td>{{$d->sede_descripcion}}</td>
                <td>{{$d->nombre_corto}}</td>
                <td>{{$d->estado_doc}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>