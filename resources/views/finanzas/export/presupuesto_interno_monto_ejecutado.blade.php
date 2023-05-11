<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Presupuesto de interno</h2>
    <br>
    <br>
    <table class="">
        <tbody>
            <tr>
                <td>Código :</td>
                <td></td>
            </tr>
            <tr>
                <td>Grupo :</td>
                <td></td>
            </tr>
            <tr>
                <td>Área :</td>
                <td></td>
            </tr>
            <tr>
                <td>Moneda :</td>
                <td></td>
            </tr>
            <tr>
                <td>Descripción :</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="18"><b>FECHA EMISIÓN	</b></th>
                <th style="background-color: #cccccc;" width="30"><b>FECHA APROBACIÓN	</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>REQ</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>ITEM</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>VALOR</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>TIPO PPTO</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>COD PPTO	</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOMBRE PPTO	</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOMBRE PPTO	</b></th>
                <th style="background-color: #cccccc;text-align: center;" width="18"><b>NOMBRE PPTO	</b></th>


            </tr>
        </thead>
        <tbody>
            @foreach ($ingresos as $item)
            <tr>
                <td>{{$item->partida}}</td>
                <td>{{$item->descripcion}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
</body>
</html>
