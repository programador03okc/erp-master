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
                <th style="background-color: #cccccc;text-align:center;" width="15"><b>Mes</b></th>
                <th style="background-color: #cccccc;text-align:center;" width="15"><b>Año</b></th>
                <th style="background-color: #cccccc;text-align:center;" width="15"><b>Fecha</b></th>
                {{-- <th style="background-color: #cccccc;" width="25"><b>Empresa</b></th> --}}
                <th style="background-color: #cccccc;" width="15"><b>Cod.Req.</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Cuenta (Partida)</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Cuenta (Sub Partida)</b></th>
                <th style="background-color: #cccccc;" width="20"><b>Tipo documento</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Número - serie</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Fecha emisión</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Ruc/DNI</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Proveedor o Persona asignada</b></th>
                <th style="background-color: #cccccc;" width="8"><b>Cant.</b></th>
                <th style="background-color: #cccccc;" width="10"><b>Unid.</b></th>
                <th style="background-color: #cccccc;" width="50"><b>Descripción</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Mnd.</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Unit.</b></th>
                <th style="background-color: #cccccc;" width="18"><b>SubTotal</b></th>
                <th style="background-color: #cccccc;" width="18"><b>IGV</b></th>
                <th style="background-color: #cccccc;" width="18"><b>P.Compra</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha pago</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Estado pago</b></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $meses = array(1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            ?>
            @foreach ($req_compras as $d)
            <tr>
                <td>{{$meses[date('n', strtotime($d->fecha_requerimiento) )]}}</td>
                <td style="text-align:center;">{{date("Y", strtotime($d->fecha_requerimiento))}}</td>
                <td style="text-align:center;">{{date("d-m-Y", strtotime($d->fecha_requerimiento))}}</td>
                <td>{{$d->codigo}}</td>
                <td>{{$d->titulo_descripcion}}</td>
                <td>{{$d->partida_descripcion}}</td>
                <td>{{$d->tipo_comprobante}}</td>
                <td>{{$d->nro_comprobante}}</td>
                <td>{{$d->fecha_emision_comprobante}}</td>
                <td>{{($d->nro_documento_proveedor !=null ? $d->nro_documento_proveedor : ($request->nro_documento_persona != null ? $request->nro_documento_persona :''))}}</td>
                <td>{{$d->proveedor_razon_social}}</td>
                <td>{{$d->cantidad}}</td>
                <td>{{$d->abreviatura}}</td>
                <td>{{$d->descripcion_adicional}}</td>
                <td>{{$d->simbolo}}</td>
                <td>{{$d->precio}}</td>
                <td>{{$d->subtotal}}</td>
                <td>{{$d->subtotal*0.18}}</td>
                <td>{{$d->subtotal + ($d->subtotal*0.18)}}</td>
                <td>{{$d->fecha_pago}}</td>
                <td>{{$d->estado_pago}}</td>
            </tr>
            @endforeach
            @foreach ($req_pagos as $d)
            <tr>
                <td>{{$meses[date('n', strtotime($d->fecha_registro) )]}}</td>
                <td style="text-align:center;">{{date("Y", strtotime($d->fecha_registro))}}</td>
                <td style="text-align:center;">{{date("d-m-Y", strtotime($d->fecha_registro))}}</td>
                <td>{{$d->codigo}}</td>
                <td>{{$d->titulo_descripcion}}</td>
                <td>{{$d->partida_descripcion}}</td>
                <td>{{$d->apellido_paterno.' '.$d->apellido_materno.' '.$d->nombres}}</td>
                <td>{{$d->cantidad}}</td>
                <td>{{$d->abreviatura}}</td>
                <td>{{$d->descripcion}}</td>
                <td>{{$d->simbolo}}</td>
                <td>{{$d->precio_unitario}}</td>
                <td>{{$d->subtotal}}</td>
                <td>{{$d->subtotal*0.18}}</td>
                <td>{{$d->subtotal + ($d->subtotal*0.18)}}</td>
                <td>{{$d->fecha_pago}}</td>
                <td>{{$d->estado_pago}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>