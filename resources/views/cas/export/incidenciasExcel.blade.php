<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Incidencias</h2>
    <br>
    <br>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>Código</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Estado</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Empresa</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cliente</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Concepto</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Factura</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Contacto</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha reporte</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Responsable</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Falla reportada</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td>{{$d->codigo}}</td>
                <td>{{$d->estado_doc}}</td>
                <td>{{$d->empresa_razon_social}}</td>
                <td>{{$d->razon_social}}</td>
                <td>{{$d->concepto}}</td>
                <td>{{$d->factura}}</td>
                <td>{{$d->nombre}}</td>
                <td>{{date('d-m-Y', strtotime($d->fecha_reporte))}}</td>
                <td>{{$d->nombre_corto}}</td>
                <td>{{$d->falla_reportada}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>