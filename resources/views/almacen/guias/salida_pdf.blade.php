<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<style>
    * {
        font-family: "DejaVu Sans";
        font-size: 10px;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    div.seccion-hoja {
        border-bottom: 1px solid black;
        margin-bottom: 5px
    }

    div.seccion-hoja h4 {
        margin-bottom: 1px
    }

    table {
        width: 100%;
    }

    div.producto-transformar {
        background-color: #bce8f1;
        padding-top: 5px;
        padding-bottom: 5px;
        font-weight: bold;
    }

    div.seccion-producto {
        background-color: #ededed;
        padding-top: 4px;
        padding-bottom: 4px;
        font-weight: bold;
    }

    span.rojo {
        color: red;
        font-weight: bold;
    }

    span.verde {
        color: green;
        font-weight: bold;
    }

    h4 {
        font-size: 20px;
    }

    table.bordered {
        border-spacing: 0px;
    }

    table.bordered th {
        border-top: 1px solid #cfcfcf;
        border-right: 1px solid #cfcfcf;
        border-bottom: 1px solid #cfcfcf;
    }

    table.bordered th:nth-child(1) {
        border-left: 1px solid #cfcfcf;
    }

    table.bordered td:nth-child(1) {
        border-left: 1px solid #cfcfcf;
    }

    table.bordered td {
        border-right: 1px solid #cfcfcf;
        border-bottom: 1px solid #cfcfcf;
    }

    h3.titulo {
        text-align: center;
        background-color: #acf2bf;
        padding-top: 5px;
        padding-bottom: 5px;
        font-size: 22px;
    }
</style>

<body>
    <table width="100%" style="margin-bottom: 0px">
        <tr>
            <td>
                <img src="{{ $logo_empresa }}" height="50px">
            </td>
        </tr>
    </table>
    <h4 style="text-align: center;
        padding-top: 5px;
        padding-bottom: 5px;
        border-bottom: 1px solid black;
        font-size: 22px;margin:0px; padding:0px;">Salida de Almacén</h4>
    <h4 class="text-center" style="margin:0px; padding:0px;border-bottom: 1px solid black;background-color: #fce0a5;">
        Guía {{$salida->guia}}</h4>
    <h5 class="text-center" style="margin:0px; padding:0px;">{{$salida->cod_sunat}} - {{$salida->ope_descripcion}}</h5>
    <h5 class="text-center" style="margin:0px; padding:0px;">{{$salida->codigo}}</h5>

    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Datos Generales</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 20%" class="text-right">Almacén:</th>
                <td style="width: 45%">{{$salida->des_almacen}}</td>
                <th style="width: 25%" class="text-right">Fecha de Salida:</th>
                <td style="width: 35%">{{$salida->fecha_emision}}</td>
            </tr>
            <tr>
                <th style="width: 20%" class="text-right">Cliente:</th>
                <td style="width: 45%">{{$salida->ruc_cliente}} - {{$salida->razon_social_cliente}}</td>
                <th style="width: 25%" class="text-right">Fecha Emisión Guía:</th>
                <td style="width: 35%">{{$salida->fecha_guia}}</td>
            </tr>
            @if ($docs!=='')
            <tr>
                <th style="width: 20%" class="text-right">Doc(s) Venta:</th>
                <td style="width: 45%">{{$docs}}</td>
                <th style="width: 25%" class="text-right">Fecha(s) emisión Doc.:</th>
                <td style="width: 35%">{{$docs_fecha}}</td>
            </tr>
            @endif
            @if ($salida->id_transferencia!==null)
            <tr>
                <th style="width: 20%" class="text-right">Transferencia:</th>
                <td style="width: 45%">{{$salida->trans_codigo}}</td>
                <th style="width: 25%" class="text-right">Almacén Destino:</th>
                <td style="width: 35%">{{$salida->trans_almacen_destino}}</td>
            </tr>
            @endif
            @if ($salida->id_transformacion!==null)
            <tr>
                <th style="width: 20%" class="text-right">Transformación:</th>
                <td style="width: 45%">{{$salida->cod_transformacion}}</td>
                <th style="width: 25%" class="text-right">Fecha de proceso:</th>
                <td style="width: 35%">{{$salida->fecha_transformacion}}</td>
            </tr>
            @endif
            <tr>
                <th style="width: 20%" class="text-right">Responsable:</th>
                <td style="width: 45%">{{$salida->nombre_corto}}</td>
            </tr>
        </thead>
    </table>
    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Lista de productos</h4>
    </div>
    
    <table class="bordered">
        <thead>
            <tr>
                <th class="text-center cabecera-producto" style="width: 7%">Código</th>
                <th class="text-center cabecera-producto" style="width: 10%">Part Number</th>
                <th class="text-center cabecera-producto">Descripción del producto</th>
                <th class="text-center cabecera-producto" style="width: 5%">Cant.</th>
                <th class="text-center cabecera-producto" style="width: 5%">Und.</th>
                <th class="text-center cabecera-producto" style="width: 5%">Mnd.</th>
                <th class="text-center cabecera-producto" style="width: 5%">Unit.</th>
                @if($salida->id_operacion == 27)
                <th class="text-center cabecera-producto" style="width: 5%">Unit.$</th>
                @endif
                <th class="text-center cabecera-producto" style="width: 5%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalle as $prod)
            <?php
            
            $det_series = DB::table('almacen.alm_prod_serie')
                ->select('alm_prod_serie.serie')
                ->where([
                    ['alm_prod_serie.id_prod', '=', $prod['id_producto']],
                    ['alm_prod_serie.id_guia_ven_det', '=', $prod['id_guia_ven_det']],
                    ['alm_prod_serie.estado', '!=', 7]
                ])
                ->get();

            $series = '';

            if ($det_series!==null) {
                foreach ($det_series as $s) {
                    if ($s->serie !== null){
                        // if ($series !== '') {
                        //     $series .= ', ' . $s->serie;
                        // } else {
                        //     $series = 'Serie(s): ' . $s->serie;
                        // }
                    }
                }
            }

            ?>
            <tr>
                <td class="text-center">{{$prod['codigo']}}</td>
                <td class="text-center">{{$prod['part_number']}}</td>
                <td>{{$prod['descripcion']}} <br><strong> {{$series}}</strong></td>
                <td class="text-center">{{$prod['cantidad']}}</td>
                <td class="text-center">{{$prod['abreviatura']}}</td>
                <td class="text-right">{{$prod['simbolo']}}</td>
                <td class="text-right">{{round($prod['costo_promedio'],2,PHP_ROUND_HALF_UP)}}</td>
                @if($salida->id_operacion == 27)
                <td class="text-right">{{round($prod['valor_dolar'],2,PHP_ROUND_HALF_UP)}}</td>
                @endif
                <td class="text-right">{{round($prod['valorizacion'],2,PHP_ROUND_HALF_UP)}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>

    <footer style="position:absolute;bottom:0px;right:0px;">
        <p style="text-align:right;font-size:10px;margin-bottom:0px;">
            {{'Registrado por: ' . $salida->nombre_corto }}
        </p>
        <p style="text-align:right;font-size:10px;margin-bottom:0px;margin-top:0px;">
            {{'Fecha registro: ' . $fecha_registro . ' ' . $hora_registro }}
        </p>
        <p style="text-align:right;font-size:10px;margin-top:0px;">
            <strong>{{config('global.nombreSistema') . ' '  . config('global.version')}}</strong>
        </p>
    </footer>
</body>
</html>