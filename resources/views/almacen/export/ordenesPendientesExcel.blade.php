<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Ordenes Pendientes</h2>
    <br>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>Código Orden</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td>{{$d->codigo_softlink}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>