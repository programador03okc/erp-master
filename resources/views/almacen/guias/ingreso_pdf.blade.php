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
<?php
use Carbon\Carbon;
?>
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
        font-size: 22px;margin:0px; padding:0px;">Ingreso a Almacén</h4>
    <h4 class="text-center" style="margin:0px; padding:0px;border-bottom: 1px solid black;background-color: #acedf2;">
        {{($ingreso->id_guia_com!==null?('Guía '.$ingreso->guia):
        ($ingreso->id_transformacion!==null?$ingreso->cod_transformacion:''))}}</h4>
    <h5 class="text-center" style="margin:0px; padding:0px;">{{$ingreso->cod_sunat}} - {{$ingreso->ope_descripcion}}</h5>
    <h5 class="text-center" style="margin:0px; padding:0px;">{{$ingreso->codigo}}</h5>

    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Datos Generales</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 20%" class="text-right">Almacén:</th>
                <td style="width: 45%">{{$ingreso->des_almacen}}</td>
                <th style="width: 25%" class="text-right">Fecha de Ingreso:</th>
                <td style="width: 35%">{{$ingreso->fecha_emision}}</td>
            </tr>
            <tr>
                <th style="width: 20%" class="text-right">Proveedor:</th>
                <td style="width: 45%">{{$ingreso->id_guia_com!==null ? $ingreso->nro_documento : $ingreso->ruc_empresa}} - {{$ingreso->id_guia_com!==null ? $ingreso->razon_social : $ingreso->empresa_razon_social}}</td>
                <th style="width: 25%" class="text-right">Fecha Emisión Guía:</th>
                <td style="width: 35%">{{$ingreso->fecha_guia}}</td>
            </tr>
            @if ($ocs!=='')
            <tr>
                <th style="width: 20%" class="text-right">Orden(s) Compra:</th>
                <td style="width: 45%">{{$ocs}}</td>
                <th style="width: 25%" class="text-right">Orden(s) SoftLink:</th>
                <td style="width: 35%">{{$softlink}}</td>
            </tr>
            @endif
            @if ($docs!=='')
            <tr>
                <th style="width: 20%" class="text-right">Doc(s) Compra:</th>
                <td style="width: 45%">{{$docs}}</td>
                <th style="width: 25%" class="text-right">Fecha(s) emisión Doc.:</th>
                <td style="width: 35%">{{$docs_fecha}}</td>
            </tr>
            @endif
            @if ($ingreso->id_transferencia!==null)
            <tr>
                <th style="width: 20%" class="text-right">Transferencia:</th>
                <td style="width: 45%">{{$ingreso->trans_codigo}}</td>
                <th style="width: 25%" class="text-right">Almacén Origen:</th>
                <td style="width: 35%">{{$ingreso->trans_almacen_origen}}</td>
            </tr>
            @endif
            @if ($ingreso->id_transformacion!==null)
            <tr>
                <th style="width: 20%" class="text-right">Transformación:</th>
                <td style="width: 45%">{{$ingreso->cod_transformacion}}</td>
                <th style="width: 25%" class="text-right">Fecha de proceso:</th>
                <td style="width: 35%">{{$ingreso->fecha_transformacion}}</td>
            </tr>
            @endif
            <tr>
                <th style="width: 20%" class="text-right">Responsable:</th>
                <td style="width: 45%">{{$ingreso->nombre_corto}}</td>
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
                <th class="text-center cabecera-producto" style="width: 5%">SubTotal</th>
                {{-- <th class="text-center cabecera-producto" style="width: 5%">Adic.</th>
                <th class="text-center cabecera-producto" style="width: 5%">Total</th> --}}
            </tr>
        </thead>
        <tbody>
            @php
            $total=0;
            $moneda='';
            @endphp
            @foreach ($detalle as $prod)
                <?php
                $det_series = [];

                if ($prod->id_guia_com_det !==null){
                    $det_series = DB::table('almacen.alm_prod_serie')
                        ->select('alm_prod_serie.serie')
                        ->where([
                            ['alm_prod_serie.id_prod', '=', $prod->id_producto],
                            ['alm_prod_serie.id_guia_com_det', '=', $prod->id_guia_com_det],
                            ['alm_prod_serie.estado', '!=', 7]
                        ])
                        ->get();
                } 
                else if ($prod->id_sobrante !==null){
                    $det_series = DB::table('almacen.alm_prod_serie')
                        ->select('alm_prod_serie.serie')
                        ->where([
                            ['alm_prod_serie.id_prod', '=', $prod->id_producto],
                            ['alm_prod_serie.id_sobrante', '=', $prod->id_sobrante],
                            ['alm_prod_serie.estado', '!=', 7]
                        ])
                        ->get();
                }
                else if ($prod->id_transformado !==null){
                    $det_series = DB::table('almacen.alm_prod_serie')
                        ->select('alm_prod_serie.serie')
                        ->where([
                            ['alm_prod_serie.id_prod', '=', $prod->id_producto],
                            ['alm_prod_serie.id_transformado', '=', $prod->id_transformado],
                            ['alm_prod_serie.estado', '!=', 7]
                        ])
                        ->get();
                }

                $series_array = [];
                $series = '';

                if ($det_series!==null) {
                    foreach ($det_series as $s) {
                        if ($series !== '') {
                            $series .= ', ' . $s->serie;
                        } else {
                            $series = 'Serie(s): ' . $s->serie;
                        }
                    }
                    // $series = (count($series_array)>1 ? implode(",", $series_array) : '');
                }
                $unitario = ($prod->precio_unitario !== null
                                ? $prod->precio_unitario
                                : ($prod->unitario!==null?$prod->unitario:($prod->unitario_guia!==null?$prod->unitario_guia:($prod->costo_promedio))));
                $valorizacion = ($unitario) * ($prod->cantidad);

                // $unitario = ($prod->cantidad !== null
                //                 ? ($prod->valorizacion / $prod->cantidad)
                //                 : 0);

                // $valorizacion = $prod->valorizacion;

                $total += $valorizacion;
                $moneda = $prod->moneda_doc!==null ? $prod->moneda_doc : 
                    ($prod->moneda_oc!==null?$prod->moneda_oc :
                        ($prod->moneda_dev!==null?$prod->moneda_dev:($prod->id_moneda == 1 ? 'S/' : '$')));

                $adic_valor = DB::table('almacen.guia_com_prorrateo_det')
                    ->where([['id_guia_com_det','=',$prod->id_guia_com_det],
                            ['estado','!=',7]])
                    ->sum('adicional_valor');

                $adic_peso = DB::table('almacen.guia_com_prorrateo_det')
                    ->where([['id_guia_com_det','=',$prod->id_guia_com_det],
                            ['estado','!=',7]])
                    ->sum('adicional_peso');

                $adicional = ($prod->unitario_adicional!==null ? $prod->unitario_adicional : 0) +
                    $adic_valor + $adic_peso;

                ?>
                <tr>
                    <td class="text-center">{{$prod->codigo}}</td>
                    <td class="text-center">{{$prod->part_number}}</td>
                    {{-- <td>{{$prod->descripcion}} <br><strong> {{$series_array!==[]?('Series(s): '+implode(" - ", $series_array)):''}}</strong></td> --}}
                    <td>{{$prod->descripcion}} <br><strong> {{$series}}</strong></td>
                    <td class="text-center">{{$prod->cantidad}}</td>
                    <td class="text-center">{{$prod->abreviatura}}</td>
                    <td class="text-right">{{$moneda}}</td>
                    <td class="text-right">{{round($unitario,2,PHP_ROUND_HALF_UP)}}</td>
                    <td class="text-right">{{round($valorizacion,2,PHP_ROUND_HALF_UP)}}</td>
                    {{-- <td class="text-right">{{round($adicional,2,PHP_ROUND_HALF_UP)}}</td>
                    <td class="text-right">{{round(($valorizacion + $adicional),2,PHP_ROUND_HALF_UP)}}</td> --}}
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right" colspan="7">{{$moneda}}</th>
                <th class="text-right">{{number_format(round($total,2,PHP_ROUND_HALF_UP), 2)}}</th>
            </tr>
        </tfoot>
    </table>
    <br>

    <footer style="position:absolute;bottom:0px;right:0px;">
        <p style="text-align:right;font-size:10px;margin-bottom:0px;">
        {{'Registrado por: ' . $ingreso->nombre_corto }}
        </p>
        <p style="text-align:right;font-size:10px;margin-bottom:0px;margin-top:0px;">
            {{'Fecha registro: ' . $fecha_registro . ' ' . $hora_registro }}
            </p>
        <p style="text-align:right;font-size:10px;margin-bottom:0px;margin-top:0px;">
            {{'Fecha de impresión: ' . (new Carbon())->format('d-m-Y H:i:s') }}
            </p>
        <p style="text-align:right;font-size:10px;margin-top:0px;">
            <strong>{{config('global.nombreSistema') . ' '  . config('global.version')}}</strong>
        </p>
    </footer>
</body>
</html>