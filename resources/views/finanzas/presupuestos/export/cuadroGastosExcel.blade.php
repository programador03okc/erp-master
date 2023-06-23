<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Cuadro de Gastos</h2>
    <br>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;text-align:center;" colspan="23"><b>Requerimientos (logísticos y Pago)</b></th>
                <th style="background-color: #dac2bd;text-align:center;" colspan="11"><b>Orden</b></th>
            </tr>
            <tr>
                <th style="background-color: #cccccc;text-align:center;" width="15"><b>Tipo Req.</b></th>
                <th style="background-color: #cccccc;text-align:center;" width="15"><b>Mes Req.</b></th>
                <th style="background-color: #cccccc;text-align:center;" width="15"><b>Año Req.</b></th>
                <th style="background-color: #cccccc;text-align:center;" width="15"><b>Fecha Req.</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Cod.Req.</b></th>
                <th style="background-color: #cccccc;" width="25"><b>Cuenta (Partida)</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Cuenta (Sub Partida)</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Tipo documento</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Número - serie</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Fecha emisión</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Ruc/DNI</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Proveedor o Persona asignada</b></th>
                <th style="background-color: #cccccc;" width="15"><b>OC/OS</b></th>
                <th style="background-color: #cccccc;" width="8"><b>Cant.</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Unid.</b></th>
                <th style="background-color: #cccccc;" width="50"><b>Descripción</b></th>
                <th style="background-color: #cccccc;" width="8"><b>Mnd.</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Unit.</b></th>
                <th style="background-color: #cccccc;" width="18"><b>SubTotal</b></th>
                <th style="background-color: #cccccc;" width="18"><b>IGV</b></th>
                <th style="background-color: #cccccc;" width="18"><b>P.Compra</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Tipo cambio</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Estado pago</b></th>

                <th style="background-color: #dac2bd; text-align:center;" width="15"><b>Mes ord.</b></th>
                <th style="background-color: #dac2bd; text-align:center;" width="15"><b>Año ord.</b></th>
                <th style="background-color: #dac2bd; text-align:center;" width="15"><b>Fecha ord.</b></th>
                <th style="background-color: #dac2bd; text-align:center;" width="18"><b>Cod. Ord.</b></th>
                <th style="background-color: #dac2bd;" width="18"><b>Cantidad Ord.</b></th>
                <th style="background-color: #dac2bd; text-align:center;" width="18"><b>Unidad Ord.</b></th>
                <th style="background-color: #dac2bd; text-align:center;"  width="18"><b>Moneda Ord.</b></th>
                <th style="background-color: #dac2bd;" width="18"><b>Precio Ord.</b></th>
                <th style="background-color: #dac2bd;" width="18"><b>V. Compra Ord.</b></th>
                <th style="background-color: #dac2bd;" width="18"><b>IGV Ord.</b></th>
                <th style="background-color: #dac2bd;" width="18"><b>P. Compra Ord.</b></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $meses = array(1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            ?>
            @foreach ($req_compras as $d)
            <tr>
                <td>Logístico</td>
                <td>{{$meses[date('n', strtotime($d->fecha_requerimiento) )]}}</td>
                <td style="text-align:center;">{{date("Y", strtotime($d->fecha_requerimiento))}}</td>
                <td style="text-align:center;">{{date("d-m-Y", strtotime($d->fecha_requerimiento))}}</td>
                <td>{{$d->codigo}}</td>
                <td>{{$d->titulo_descripcion}}</td>
                <td>{{$d->partida_descripcion}}</td>
                <td>{{$d->tipo_comprobante !=null ? $d->tipo_comprobante : ''}}</td>
                <td>{{$d->serie_numero !=null ? $d->serie_numero : ''}}</td>
                <td>{{$d->fecha_emision_comprobante !=null ? $d->fecha_emision_comprobante : ''}}</td>
                <td>{{($d->nro_documento_proveedor !=null ? $d->nro_documento_proveedor : '')}}</td>
                <td>{{$d->proveedor_razon_social !=null ? $d->proveedor_razon_social : ''}}</td>
                <td>{{$d->codigo_orden!==null?$d->codigo_orden:''}}</td>
                <td>{{$d->cantidad}}</td>
                <td>{{$d->abreviatura}}</td>
                <td>{{$d->descripcion}}</td>
                <td>{{$d->simbolo_moneda_requerimiento!==null?$d->simbolo_moneda_requerimiento:''}}</td>
                <td>{{($d->precio_requerimiento!==null?$d->precio_requerimiento:$d->precio_requerimiento)}}</td>
                <td>{{($d->cantidad * ($d->precio_requerimiento!==null?$d->precio_requerimiento:0))}}</td>
                <td>{{($d->cantidad * ($d->precio_requerimiento!==null?$d->precio_requerimiento:0)) * 0.18}}</td>
                <td>{{($d->cantidad * ($d->precio_requerimiento!==null?$d->precio_requerimiento:0)) + (($d->cantidad * ($d->precio_requerimiento!==null?$d->precio_requerimiento:$d->precio_requerimiento))*0.18)}}</td>
                <td>{{$d->tipo_cambio}}</td>
                <td>{{$d->estado_pago}}</td>

                <td>{{$meses[date('n', strtotime($d->fecha_orden) )]}}</td>
                <td>{{date('Y', strtotime($d->fecha_orden) )}}</td>
                <td style="text-align:center;">{{date("d-m-Y", strtotime($d->fecha_orden))}}</td>
                <td>{{$d->codigo_orden}}</td>
                <td>{{$d->cantidad_orden}}</td>
                <td>{{$d->unidad_orden}}</td>
                <td>{{$d->simbolo_moneda_orden}}</td>
                <td>{{$d->precio_orden}}</td>
                <td>{{$d->subtotal_orden}}</td>
                <td>{{($d->cantidad * ($d->precio_orden!==null?$d->precio_orden:0)) * 0.18}}</td>
                <td>{{($d->cantidad * ($d->precio_orden!==null?$d->precio_orden:0)) + (($d->cantidad_orden * ($d->precio_requerimiento!==null?$d->precio_requerimiento:$d->precio_requerimiento))*0.18)}}</td>

            </tr>
            @endforeach
            @foreach ($req_pagos as $d)
            <tr>
                <td>Pago</td>
                <td>{{$meses[date('n', strtotime($d->fecha_registro) )]}}</td>
                <td style="text-align:center;">{{date("Y", strtotime($d->fecha_registro))}}</td>
                <td style="text-align:center;">{{date("d-m-Y", strtotime($d->fecha_registro))}}</td>
                <td>{{$d->codigo}}</td>
                <td>{{$d->titulo_descripcion}}</td>
                <td>{{$d->partida_descripcion}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{$d->apellido_paterno.' '.$d->apellido_materno.' '.$d->nombres}}</td>
                <td></td>
                <td>{{$d->cantidad}}</td>
                <td>{{$d->abreviatura}}</td>
                <td>{{$d->descripcion}}</td>
                <td>{{$d->simbolo_moneda_requerimiento}}</td>
                <td>{{$d->precio_unitario}}</td>
                <td>{{$d->subtotal}}</td>
                <td>0</td>
                <td>{{$d->subtotal}}</td>
                <td>{{$d->tipo_cambio}}</td>
                <td>{{$d->estado_pago}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>