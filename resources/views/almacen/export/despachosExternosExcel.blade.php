<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Reporte de Despachos Externos</h2>
    <label>Mostrar: 
        @if($select_mostrar == "0")
            Todos
        @endif
        @if($select_mostrar == "1")
            Priorizados
        @endif
        @if($select_mostrar == "2")
            "Los de hoy"
        @endif
    </label>
    <br>
    <br>
    <table>
        <thead>
            <tr>
                <th style="background-color: #cccccc;" width="20"><b>Nro. O/C</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Requerimiento</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Empresa</b></th>
                <th style="background-color: #cccccc;" width="40"><b>Entidad/Cliente</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha publicación</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha despacho</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha entrega</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Fecha entregada</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Transformación</b></th>
                <th style="background-color: #cccccc;" width="30"><b>Empresa de transporte</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Guía de empresa</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Guía transportista</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Flete real S/</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Cancelación</b></th>
                <th style="background-color: #cccccc;" width="12"><b>Extra</b></th>
                <th style="background-color: #cccccc;" width="18"><b>Estado de transporte</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Entrega a tiempo</b></th>
                <th style="background-color: #cccccc;" width="15"><b>Observaciones</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td>{{$d->nro_orden!==null?$d->nro_orden:''}}</td>
                <td>{{$d->codigo}}</td>
                <td>{{$d->sede_descripcion_req}}</td>
                <td>{{$d->cliente_razon_social}}</td>
                <td>{{$d->fecha_publicacion!==null ? date('d-m-Y', strtotime($d->fecha_publicacion)):''}}</td>
                <td>{{$d->fecha_despacho!==null ? date('d-m-Y', strtotime($d->fecha_despacho)):''}}</td>
                <td>{{$d->fecha_entrega!==null ? date('d-m-Y', strtotime($d->fecha_entrega)):''}}</td>
                <td>{{$d->fecha_entregada!==null ? date('d-m-Y', strtotime($d->fecha_entregada)):''}}</td>
                <td>{{$d->tiene_transformacion ? 'SI':'NO'}}</td>
                <td>{{$d->transportista_razon_social!==null ? $d->transportista_razon_social:''}}</td>
                <td>
                    @if($d->serie!==null && $d->numero!==null) 
                        {{$d->serie}}-{{$d->numero}}
                    @endif
                </td>
                <td>
                    @if($d->serie_tra!==null && $d->numero_tra!==null) 
                        {{$d->serie_tra}}-{{$d->numero_tra}}
                    @endif
                </td>
                <td>{{$d->importe_flete!==null ? $d->importe_flete :''}}</td>
                <td></td>
                <td>{{$d->gasto_extra!==null ? $d->gasto_extra :''}}</td>
                <td>{{$d->estado_envio!==null ? $d->estado_envio :''}}</td>
                <td>{{$d->plazo_excedido!==null ? ($d->plazo_excedido ? 'PLAZO EXCEDIDO':'ENTREGA A TIEMPO') : ''}}</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>