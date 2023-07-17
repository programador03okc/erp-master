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

    div.seccion {
        border-bottom: 1px solid black;
        margin-bottom: 5px
    }

    div.seccion h4 {
        margin-bottom: 1px
    }

    table {
        width: 100%;
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

    footer {
        position: relative;
    }

    .pie-pagina {
        position: absolute;
        bottom: 0px;
        right: 0px;
        text-align: right;
    }
</style>
<?php
if($requerimientoPago->id_tipo_destinatario ==1){
    $tipoDocumentoIdentidad= $requerimientoPago->persona !=null ? $requerimientoPago->persona['tipoDocumentoIdentidad']['descripcion']:'';
    $nroDocumento= $requerimientoPago->persona !=null ?$requerimientoPago->persona['nro_documento']:'';
    $tipoDestinatario= $requerimientoPago->tipoDestinatario !=null ?$requerimientoPago->tipoDestinatario['descripcion']:'';
    $nombre=$requerimientoPago->persona !=null ?($requerimientoPago->persona['nombres'].' '.$requerimientoPago->persona['apellido_paterno'].' '.$requerimientoPago->persona['apellido_materno']):'';
    $banco=$requerimientoPago->cuentaPersona !=null  && $requerimientoPago->cuentaPersona['banco'] !=null ?$requerimientoPago->cuentaPersona['banco']['contribuyente']['razon_social']:'';
    $tipoCuenta=$requerimientoPago->cuentaPersona !=null  && $requerimientoPago->cuentaPersona['tipoCuenta'] !=null ?$requerimientoPago->cuentaPersona['tipoCuenta']['descripcion']:'';
    $moneda=$requerimientoPago->cuentaPersona !=null  && $requerimientoPago->cuentaPersona['moneda'] !=null ?$requerimientoPago->cuentaPersona['moneda']['descripcion']:'';
    $numeroCuenta=$requerimientoPago->cuentaPersona !=null ?$requerimientoPago->cuentaPersona['nro_cuenta']:'';
    $numeroCci=$requerimientoPago->cuentaPersona !=null ?$requerimientoPago->cuentaPersona['nro_cci']:'';
}elseif($requerimientoPago->id_tipo_destinatario ==2){
    $tipoDocumentoIdentidad= $requerimientoPago->contribuyente !=null ? $requerimientoPago->contribuyente['tipoDocumentoIdentidad']['descripcion']:'';
    $nroDocumento= $requerimientoPago->contribuyente !=null ?$requerimientoPago->contribuyente['nro_documento']:'';
    $tipoDestinatario= $requerimientoPago->tipoDestinatario !=null ?$requerimientoPago->tipoDestinatario['descripcion']:'';
    $nombre=$requerimientoPago->contribuyente !=null ? $requerimientoPago->contribuyente['razon_social']:'';
    $banco=$requerimientoPago->cuentaContribuyente !=null  && $requerimientoPago->cuentaContribuyente['banco'] !=null ?$requerimientoPago->cuentaContribuyente['banco']['contribuyente']['razon_social']:'';
    $tipoCuenta=$requerimientoPago->cuentaContribuyente !=null  && $requerimientoPago->cuentaContribuyente['tipoCuenta'] !=null ?$requerimientoPago->cuentaContribuyente['tipoCuenta']['descripcion']:'';
    $moneda=$requerimientoPago->cuentaContribuyente !=null  && $requerimientoPago->cuentaContribuyente['moneda'] !=null ?$requerimientoPago->cuentaContribuyente['moneda']['descripcion']:'';
    $numeroCuenta=$requerimientoPago->cuentaContribuyente !=null ?$requerimientoPago->cuentaContribuyente['nro_cuenta']:'';
    $numeroCci=$requerimientoPago->cuentaContribuyente !=null ?$requerimientoPago->cuentaContribuyente['nro_cuenta_interbancaria']:'';
}
?>

<body>
    <table width="100%" style="margin-bottom: 0px">
        <tr>
            <td>
                <img src=".{{ $requerimientoPago->empresa['logo_empresa']??'' }}" height="50px">
            </td>
        </tr>
    </table>
    <h4 style="text-align: center;
        background-color: #acf2bf;
        padding-top: 5px;
        padding-bottom: 5px;
        border-bottom: 1px solid black;
        border-top: 1px solid black;
        font-size: 22px;margin:0px; padding:0px;">Requerimiento de Pago</h4>
    <h4 class="text-center" style="margin:0px; padding:0px;"> {{ $requerimientoPago->codigo != null ?$requerimientoPago->codigo:'' }}</h4>

    <div class="seccion">
        <h4 style="font-size: 14px;">Datos Generales</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%" class="text-right">Tipo req.:</th>
                <td style="width: 35%">{{ $requerimientoPago->tipoRequerimientoPago!=null ? $requerimientoPago->tipoRequerimientoPago['descripcion']:'' }}</td>
                <th style="width: 15%" class="text-right">Concepto:</th>
                <td style="width: 35%">{{$requerimientoPago->concepto!=null ? $requerimientoPago->concepto: ''}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Empresa/Sede:</th>
                <td style="width: 35%">{{$requerimientoPago->sede!=null ? $requerimientoPago->sede['descripcion']:''}}</td>
                <th style="width: 15%" class="text-right">Prioridad:</th>
                <td style="width: 35%">{{$requerimientoPago->prioridad !=null ? $requerimientoPago->prioridad['descripcion']:''}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Grupo/División:</th>
                <td style="width: 35%">{{$requerimientoPago->grupo!=null ? $requerimientoPago->grupo['descripcion']:''}} / {{$requerimientoPago->grupo!=null ? $requerimientoPago->grupo['descripcion']:'' }}</td>
                <th style="width: 15%" class="text-right">Periodo:</th>
                <td style="width: 35%">{{$requerimientoPago->periodo!=null ? $requerimientoPago->periodo['descripcion']:''}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Comentario:</th>
                <td style="width: 35%">{{$requerimientoPago->comentario!=null ? $requerimientoPago->comentario:'' }}</td>
                <th style="width: 15%" class="text-right">Fecha registro:</th>
                <td style="width: 35%">{{$requerimientoPago->fecha_registro!=null ? $requerimientoPago->fecha_registro:''}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Presupuesto:</th>
                <td style="width: 35%">{{$requerimientoPago->presupuestoInterno != null  ? ($requerimientoPago->presupuestoInterno['codigo'].' - '.$requerimientoPago->presupuestoInterno['descripcion']):'' }}</td>
                <th style="width: 15%" class="text-right">Solicitado por:</th>
                <td style="width: 35%">{{$requerimientoPago->nombre_trabajador!=null ? $requerimientoPago->nombre_trabajador:'' }}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Tipo Impuesto:</th>
                <td style="width: 35%">{{$requerimientoPago->tipo_impuesto ==1  ? 'Detracción' : ($requerimientoPago->tipo_impuesto ==2?'Renta':'No aplica') }}</td>
            </tr>
        </thead>
    </table>

    <div class="seccion">
        <h4 style="font-size: 14px;">Datos del destinatario de pago</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%" class="text-right">Tipo:</th>
                <td style="width: 35%">{{ $tipoDestinatario ?? '' }}</td>
                <th style="width: 15%" class="text-right">Banco:</th>
                <td style="width: 35%">{{ $banco ?? '' }}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Destinatario:</th>
                <td style="width: 35%">{{ $nombre ?? '' }}</td>
                <th style="width: 15%" class="text-right">Cuenta Bancaria:</th>
                <td style="width: 35%">{{ $numeroCuenta != ''? $numeroCuenta: '' }} {{$numeroCci !='' ? ' CCI:'.($numeroCci) : ''}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">{{ $tipoDocumentoIdentidad ??'' }}:</th>
                <td style="width: 35%">{{ $nroDocumento ?? '' }}</td>
                <th style="width: 15%" class="text-right">Tipo Cuenta:</th>
                <td style="width: 35%">{{ $tipoCuenta ?? '' }}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Moneda:</th>
                <td style="width: 35%">{{ $moneda ?? '' }}</td>
            </tr>
          

        </thead>
    </table>

    <div class="seccion">
        <h4 style="font-size: 14px;">Detalle de requerimiento de pago</h4>
    </div>
    <table class="bordered">
        <thead>
            <tr>
                <th class="text-center cabecera-producto">Partida</th>
                <th class="text-center cabecera-producto">Centro costo</th>
                <th class="text-center cabecera-producto">Descripción de item</th>
                <th class="text-center cabecera-producto" style="width: 10%">Cant.</th>
                <th class="text-center cabecera-producto" style="width: 10%">Precio Unit.</th>
                <th class="text-center cabecera-producto" style="width: 10%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach(($requerimientoPago->detalle) as $item)

            <tr>
                <td class="text-center">{{ $requerimientoPago->id_presupuesto_interno > 0? $item->presupuestoInternoDetalle->partida :($item->partida != null ? $item->partida['codigo'] : '') }}</td>
                <td class="text-center">{{ $item->centroCosto != null ? $item->centroCosto['codigo'] : '' }}</td>
                <td class="text-left">{{ $item->descripcion != null ? $item->descripcion : '' }}</td>
                <td class="text-center" style="width: 10%">{{ $item->cantidad != null ? $item->cantidad : '' }} {{($item->unidadMedida != null ? $item->unidadMedida['abreviatura'] : '') }}</td>
                <td class="text-right" style="width: 10%">{{ $requerimientoPago->moneda !=null ? $requerimientoPago->moneda['simbolo']:'' }} {{ $item->precio_unitario != null ? number_format($item->precio_unitario, 2): '' }}</td>
                <td class="text-right" style="width: 10%">{{ $requerimientoPago->moneda !=null ? $requerimientoPago->moneda['simbolo']:'' }} {{ $item->subtotal != null ? number_format($item->subtotal, 2) : '' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><span name="simboloMoneda">{{$requerimientoPago->moneda !=null ? $requerimientoPago->moneda['simbolo']:''}}</span>{{number_format($requerimientoPago->monto_total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    <footer>
        <p style="font-size:9px; " class="pie-pagina">Generado por: {{$requerimientoPago->creadoPor!=null ? $requerimientoPago->creadoPor['nombre_corto']: '' }}
            <br>
            Fecha registro: {{$requerimientoPago->fecha_registro !=null ? $requerimientoPago->fecha_registro:'' }}
            <br>
            Versión del sistema: {{config('global.nombreSistema')}} {{config('global.version')}}
        </p>

    </footer>
</body>

</html>