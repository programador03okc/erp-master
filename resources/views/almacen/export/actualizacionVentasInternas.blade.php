<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Actualización de Ventas Internas</h2>
    <br>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="25"><b>Codigo Ingreso</b></th>
                <th style="background-color: #cccccc;" width="25"><b>Almacén</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Cod.Producto</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cantidad</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Valorización Anterior</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Valorización Nueva</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Unitario Anterior</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Unitario Nuevo</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Moneda Producto</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Moneda Documento</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha Emisión</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td>{{$d['codigo']}}</td>
                <td>{{$d['almacen_descripcion']}}</td>
                <td>{{$d['codigo_producto']}}</td>
                <td>{{$d['cantidad']}}</td>
                <td>{{$d['valorizacion']}}</td>
                <td>{{$d['nueva_valorizacion']}}</td>
                <td>{{$d['unitario_anterior']}}</td>
                <td>{{$d['unitario_nuevo']}}</td>
                <td>{{$d['id_moneda_producto']}}</td>
                <td>{{$d['id_moneda_doc']}}</td>
                <td>{{$d['fecha_emision']}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>