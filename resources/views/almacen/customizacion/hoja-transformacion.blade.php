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
        background-color: #bcf9fd;
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

use App\Models\mgcp\CuadroCosto\CcAmFila;
use App\Models\almacen\DetalleRequerimiento;
use App\Models\mgcp\CuadroCosto\CcFilaMovimientoTransformacion;
use App\Models\mgcp\CuadroCosto\CuadroCosto;

$cuadroCosto = $oportunidad->cuadroCosto;
$filasCuadro = CcAmFila::join('almacen.alm_det_req', function ($join) {
                $join->on('alm_det_req.id_cc_am_filas', '=', 'cc_am_filas.id');
                $join->where('alm_det_req.tiene_transformacion', '=', false);
            })
->join('almacen.alm_prod','alm_prod.id_producto','=','alm_det_req.id_producto')
->join('almacen.alm_subcat','alm_subcat.id_subcategoria','=','alm_prod.id_subcategoria')
->select('cc_am_filas.*','alm_prod.codigo as codigo_agile','alm_prod.cod_softlink',
'alm_prod.part_number','alm_prod.descripcion as producto_descripcion_agile',
'alm_subcat.descripcion as marca_agile')
->where('cc_am_filas.id_cc_am', $cuadroCosto->id)
->orderBy('cc_am_filas.id', 'asc')->distinct()->get();

$ordenCompra = $oportunidad->ordenCompraPropia;
?>

<body>
    <table width="100%" style="margin-bottom: 0px">
        <tr>
            <td>
                <img src="{{ $logo_empresa??'' }}" height="50px">
            </td>
        </tr>
    </table>
    <h4 style="text-align: center;
        background-color: #acf2bf;
        padding-top: 5px;
        padding-bottom: 5px;
        border-bottom: 1px solid black;
        border-top: 1px solid black;
        font-size: 22px;margin:0px; padding:0px;">{{is_null($codigo) ? 'Orden de Servicio' : 'Orden de Transformación'}}</h4>
    <h4 class="text-center" style="margin:0px; padding:0px;">{{is_null($codigo) ? '' : $codigo}}</h4>

    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Detalles del cuadro</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%" class="text-right">Código CDP:</th>
                <td style="width: 35%">{{$oportunidad->codigo_oportunidad}}</td>
                <th style="width: 15%" class="text-right">Empresa:</th>
                <td style="width: 35%">{{is_null($ordenCompra) ? '(Sin O/C)' : $ordenCompra->empresa->empresa}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Cliente:</th>
                <td style="width: 35%">{{is_null($ordenCompra) ? '(Sin O/C)' : $ordenCompra->entidad->nombre}}</td>
                <th style="width: 15%" class="text-right">Lugar entrega:</th>
                <td style="width: 35%">{{is_null($ordenCompra) ? '(Sin O/C)' : $ordenCompra->lugar_entrega}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">O/C:</th>
                <td style="width: 35%">{{is_null($ordenCompra) ? '(Sin O/C)' : $ordenCompra->nro_orden}}</td>
                <th style="width: 15%" class="text-right">Fecha límite:</th>
                <td style="width: 35%">{{is_null($ordenCompra) ? '(Sin O/C)' : $ordenCompra->fecha_entrega_format}}</td>
            </tr>
        </thead>
    </table>
    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Lista de productos</h4>
    </div>
    @php
    $contador=1;
    @endphp

    @foreach ($filasCuadro as $fila)
    @if ($fila->es_ingreso_transformacion)
    @continue
    @endif
    <div class="producto-transformar">Producto {{ $contador++ }} ({{$fila->tieneTransformacion() ? 'con' : 'sin'}} transformación):</div>
    <div class="seccion-producto">- Producto base</div>
    <table class="bordered">
        <thead>
            <tr>
                <th class="text-center cabecera-producto" style="width: 7%">Cant.</th>
                <th class="text-center cabecera-producto" style="width: 8%">Cod. Agile</th>
                <th class="text-center cabecera-producto" style="width: 8%">Cod. SoftLink</th>
                <th class="text-center cabecera-producto" style="width: 15%">Nro. parte</th>
                <th class="text-center cabecera-producto" style="width: 15%">Marca</th>
                <th class="text-center cabecera-producto">Descripción del producto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{$fila->cantidad}}</td>
                <td class="text-center">{{$fila->codigo_agile}}</td>
                <td class="text-center">{{$fila->cod_softlink}}</td>
                <td class="text-center">{{$fila->part_number}}</td>
                <td class="text-center">{{$fila->marca_agile}}</td>
                <td>{{$fila->producto_descripcion_agile}}</td>
            </tr>
        </tbody>
    </table>
    @if ($fila->tieneTransformacion())
    <div class="seccion-producto">- Producto transformado</div>
    <table class="bordered">
        <thead>
            <tr>
                <th class="text-center cabecera-producto" style="width: 7%">Cant.</th>
                <th class="text-center cabecera-producto" style="width: 15%">Nro. parte</th>
                <th class="text-center cabecera-producto" style="width: 15%">Marca</th>
                <th class="text-center cabecera-producto">Descripción del producto</th>
                <th class="text-center cabecera-producto" style="width: 20%">Comentario</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{$fila->cantidad}}</td>
                <td class="text-center">{{$fila->part_no_producto_transformado}}</td>
                <td class="text-center">{{$fila->marca_producto_transformado}}</td>
                <td>{{$fila->descripcion_producto_transformado}}</td>
                <td>{{$fila->comentario_producto_transformado}}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">
                    Etiquetado: <span class="{{$fila->etiquetado_producto_transformado ? 'verde' : 'rojo'}}">{{$fila->etiquetado_producto_transformado ? 'Sí' : 'No'}}</span>,
                    BIOS: <span class="{{$fila->bios_producto_transformado ? 'verde' : 'rojo'}}">{{$fila->bios_producto_transformado ? 'Sí' : 'No'}}</span>,
                    Office preinstalado: <span class="{{$fila->office_preinstalado_producto_transformado ? 'verde' : 'rojo'}}">{{$fila->office_preinstalado_producto_transformado ? 'Sí' : 'No'}}</span>,
                    Office activado: <span class="{{$fila->office_activado_producto_transformado ? 'verde' : 'rojo'}}">{{$fila->office_activado_producto_transformado ? 'Sí' : 'No'}}</span>
                </td>
            </tr>
        </tfoot>
    </table>
    <div class="seccion-producto">- Ingresos y salidas</div>
    <table class="bordered" style="margin-bottom: 15px">
        <thead>
            <tr>
                <th class="text-center cabecera-producto" style="width: 12%">Cod. Agile</th>
                <th class="text-center cabecera-producto" style="width: 12%">Cod. SoftLink</th>
                <th class="text-center cabecera-producto" style="width: 33%">Ingresa</th>
                <th class="text-center cabecera-producto" style="width: 33%">Sale</th>
                <th class="text-center cabecera-producto" style="width: 34%">Comentario</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $movimientos = CcFilaMovimientoTransformacion::join('almacen.alm_det_req','alm_det_req.id_cc_am_filas','=','cc_fila_movimientos_transformacion.id_fila_ingresa')
->join('almacen.alm_prod','alm_prod.id_producto','=','alm_det_req.id_producto')
->select('cc_fila_movimientos_transformacion.*','alm_prod.codigo as codigo_agile','alm_prod.cod_softlink',
'alm_prod.part_number','alm_prod.descripcion as producto_descripcion_agile')
            ->where('cc_fila_movimientos_transformacion.id_fila_base', $fila->id)
            ->orderBy('cc_fila_movimientos_transformacion.id', 'asc')->get();
            ?>
            @if ($movimientos->count()==0)
            <tr>
                <td class="text-center" colspan="3">Sin datos de ingresos y salidas</td>
            </tr>
            @endif
            @foreach ($movimientos as $movimiento)
            <tr>
                <td>{{$movimiento->id_fila_ingresa!==null ? $movimiento->codigo_agile : ''}}</td>
                <td>{{$movimiento->id_fila_ingresa!==null ? $movimiento->cod_softlink : ''}}</td>
                <td>{{$movimiento->id_fila_ingresa!==null ? $movimiento->producto_descripcion_agile : ''}}</td>
                <td>{{$movimiento->sale}}</td>
                <td>{{$movimiento->comentario}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @if ($fila->tieneComentarios())
    <div class="seccion-producto">- Comentarios</div>
    <table class="bordered">
        <thead>
            <tr>
                <th class="text-center cabecera-producto" style="width: 25%">Usuario</th>
                <th class="text-center cabecera-producto" style="width: 15%">Fecha</th>
                <th class="text-center cabecera-producto" style="width: 60%">Comentario</th>
            </tr>
        </thead>
        <tbody>
            @php
            $comentarios=$fila->comentarios;
            @endphp
            @foreach ($comentarios as $comentario)
            <tr>
                <td>{{$comentario->usuario->name}}</td>
                <td class="text-center">{{$comentario->fecha}}</td>
                <td>{{$comentario->comentario}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    <br>
    @endforeach

</body>

</html>