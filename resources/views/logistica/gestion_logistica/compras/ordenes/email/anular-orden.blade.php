<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<?php


$codigoRequerimientoList = array();
$responsableRequerimientoList = array();
$codigoOportunidadList = array();
// $responsableOportunidadList = array();
foreach(($orden->requerimientos) as $r) {
    $codigoRequerimientoList[] = $r['codigo'];
    $responsableRequerimientoList[] = $r['nombre_corto'];
}
foreach(($orden->oportunidad) as $o) {
    $codigoOportunidadList[] = $o['codigo_oportunidad'];
    // $responsableOportunidadList[] = $o['responsable'];
}
?>
<body>

    <h3><strong>Anulación de orden de compra</strong></h3>
    <p>{{$nombreUsuarioEnSession}} ha anulado la orden de compra {{$orden->codigo}}</p>
    <br>
    <h4>Sustento de anulación:</h4>
    <p>{{$orden->sustento_anulacion}}</p>
    <br>
    <h4>Información de adicional:</h4>
    
    <li>Requerimiento : {!! implode(",", $codigoRequerimientoList) !!}</li>
    <li>Responsable : {!! implode(",", $responsableRequerimientoList) !!}</li>
    <li>Oportunidad : {!! implode(",", $codigoOportunidadList) !!}</li>
    <li>Empresa - sede : {{$orden->sede->descripcion}}</li>
    <li>Fecha creacion : {{$orden->fecha}}</li>
    <li>Fecha anulación : {{$orden->fecha_anulacion}}</li>
    <br>

    <hr>

    <p> *Este correo es generado de manera automática, por favor no responder.</p>
    <br> Saludos, <br> Módulo de Logística <br> {{config('global.nombreSistema')}}  {{config('global.version')}}</p>

</body>

</html>