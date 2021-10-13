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
        background-color: #acf2bf;
        padding-top: 5px;
        padding-bottom: 5px;
        font-size: 22px;margin:0px; padding:0px;">Transferencia entre Almacenes</h4>
    <h4 class="text-center" style="margin:0px; padding:0px;">{{$transferencia->codigo}}</h4>

    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Datos Generales</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 25%" class="text-right">Almacén Origen:</th>
                <td style="width: 35%">{{$transferencia->almacen_origen}}</td>
                <th style="width: 25%" class="text-right">Almacén Destino:</th>
                <td style="width: 35%">{{$transferencia->almacen_destino}}</td>
            </tr>
            <tr>
                <th style="width: 25%" class="text-right">Responsable Origen:</th>
                <td style="width: 35%">{{$transferencia->responsable_origen}}</td>
                <th style="width: 25%" class="text-right">Responsable Destino:</th>
                <td style="width: 35%">{{$transferencia->responsable_destino}}</td>
            </tr>
            <tr>
                <th style="width: 25%" class="text-right">Guía Compra:</th>
                <td style="width: 35%">{{$transferencia->guia_com}}</td>
                <th style="width: 25%" class="text-right">Guía Venta:</th>
                <td style="width: 35%">{{$transferencia->guia_ven}}</td>
            </tr>
            <tr>
                <th style="width: 25%" class="text-right">Fecha de Transferencia:</th>
                <td style="width: 35%">{{$transferencia->fecha_transferencia}}</td>
                <th style="width: 25%" class="text-right">Requerimiento:</th>
                <td style="width: 35%">{{$transferencia->codigo_req}}</td>
            </tr>
            {{-- <tr>
                <th style="width: 15%" class="text-right">Fecha de Transferencia:</th>
                <td style="width: 35%">{{is_null($ordenCompra) ? '(Sin O/C)' : $ordenCompra->nro_orden}}</td>
                <th style="width: 15%" class="text-right">Fecha límite:</th>
                <td style="width: 35%">{{is_null($ordenCompra) ? '(Sin O/C)' : $ordenCompra->fecha_entrega_format}}</td>
            </tr> --}}
        </thead>
    </table>
    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Lista de productos</h4>
    </div>
    
    
    
    <table class="bordered">
        <thead>
            <tr>
                <th class="text-center cabecera-producto" style="width: 7%">Código</th>
                <th class="text-center cabecera-producto" style="width: 15%">Part Number</th>
                <th class="text-center cabecera-producto">Descripción del producto</th>
                <th class="text-center cabecera-producto" style="width: 5%">Cant.</th>
                <th class="text-center cabecera-producto" style="width: 5%">Unid.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalle as $prod)
            <tr>
                <td class="text-center">{{$prod->codigo}}</td>
                <td class="text-center">{{$prod->part_number}}</td>
                <td>{{$prod->descripcion}}</td>
                <td class="text-center">{{$prod->cantidad}}</td>
                <td class="text-center">{{$prod->abreviatura}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>

    <footer style="position:absolute;bottom:0px;right:0px;">
        <p style="text-align:right;font-size:10px;margin-bottom:0px;">
        {{'Registrado por: ' . $transferencia->registrado_por }}
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