<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
</head>

<body>
    <table>
        <thead>
            <tr>
                <th></th>
            </tr>
            <tr>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td colspan="15"></td>
                <td colspan="4" style="text-align: left;">{{$guia->serie??''}}-{{{$guia->numero??''}}}</td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="3" style="text-align: left;">{{$guia->fecha_emision??''}}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="6" style="text-align: left;"></td>
                <td colspan="5"></td>
                <td colspan="8" rowspan="2" style="text-align: left; word-wrap:break-word;vertical-align:top;">CA LAS CASTAÑITAS N° 0000000000127, 00SANISIDRO,0000LIMA0000000000000</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td colspan="10" rowspan="2" style="text-align: left; word-wrap:break-word;vertical-align:top;">00MUNICIPALIDAD PROV000. CAR LOS F. FITZCARRALD000000000000000000</td>
                <td colspan="3"></td>
                <td colspan="8" style="text-align: left;"></td>
            </tr>
            <tr>
            <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="8" rowspan="2" style="text-align: left; word-wrap:break-word;">0000000000JR. FITZCARRALD Nº 50400- SAN 0LUIS, FITZCARRALD</td>
                <td colspan="3"></td>
                <td colspan="5" style="text-align: left;"></td>
                <td colspan="5" style="text-align: left;">INGRESAR MARCA VEHICU</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="4" style="text-align: left;"></td>
                <td colspan="8"></td>
                <td colspan="4" style="text-align: left;">PLACA TRA</td>
 
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="3" style="text-align: left;">20519865476</td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="3" style="text-align: left;">74064499</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="3" style="text-align: left;">LICENCIA</td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>

        @foreach ($detalle as $d)
            <tr>
                <td colspan="3" style="text-align: left;">{{$d['codigo']}}</td>
                <td colspan="3" style="text-align: left;">{{$d['cantidad']}}</td>
                <td colspan="3" style="text-align: left;">{{strtoupper($d['abreviatura'])??''}}</td>
                <td colspan="14" style="text-align: left;">CATEGORÍA:</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="14" style="text-align: left;">MARCA: {{strtoupper($d['marca'])??''}}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="14" style="text-align: left;">NUMERO DE PARTE: {{$d['part_number']}}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="14" style="text-align: left;">{{strtoupper($d['descripcion'])??''}}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="14" style="text-align: left;"></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="14" style="text-align: left;">S/N:</td>
            </tr>

            @foreach($d['series'] as $key => $s)

                @if ($key % 3 == 0)
                    <tr>
                    <td colspan="3" style="text-align: left;"></td>
                    <td colspan="3" style="text-align: left;"></td>
                    <td colspan="3" style="text-align: left;"></td>
                @endif

                <td colspan="4" style="text-align: left;">{{$s->serie}}</td>
                <td></td>
                @if (($key + 1) % 3 == 0)
                    </tr>
                @endif




            @endforeach

            <tr>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="4" style="text-align: left;"></td>
            </tr>

        @endforeach

 

 
            <tr>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="4" style="text-align: left;">OC: 0000 </td>
                <td></td>
                <td colspan="4" style="text-align: left;"></td>
                <td></td>
                <td colspan="4" style="text-align: left;"></td>
            </tr>
            <tr>
            <td></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="3" style="text-align: left;"></td>
                <td colspan="12" style="text-align: left;">DENOMINACIÓN TRANSPORTISTA, APELLIDOS Y NOMBRES</td>
                <td></td>
                <td colspan="4" style="text-align: left;">RUC TRANSPORTISTA</td>
            </tr>
        </tbody>
    </table>
</body>

</html>