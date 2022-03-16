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
                <th style="background-color: #cccccc;" width="18"><b>Quien reporto</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Contacto</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cargo / Area</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Teléfono</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Dirección</b></th>
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
                <td>{{$d->cliente}}</td>
                <td>{{$d->nro_orden}}</td>
                <td>{{$d->factura}}</td>
                <td>{{$d->usuario_final}}</td>
                <td>{{$d->nombre_contacto}}</td>
                <td>{{$d->cargo_contacto}}</td>
                <td>{{$d->telefono_contacto}}</td>
                <td>{{$d->direccion_contacto}}</td>
                <td>{{date('d-m-Y', strtotime($d->fecha_reporte))}}</td>
                <td>{{$d->nombre_corto}}</td>
                <td>{{$d->falla_reportada}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>