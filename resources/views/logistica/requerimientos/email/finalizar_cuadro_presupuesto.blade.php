<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <h3><strong>Cuadro de presupuesto finalizado</strong></h3>
    <p>{{$nombreUsuarioEnSession}} ha finalizado el cuadro de presupuesto {!! implode(",",$codigoOportunidad) !!}</p>
    <br>
    <h4>Información de oportunidad:</h4>

    @foreach($payload as $data)
    <li>Oportunidad : {{$data['cuadro_presupuesto']->oportunidad->oportunidad}}</li>
    <li>Responsable : {{$data['cuadro_presupuesto']->oportunidad->responsable->name}}</li>
    <li>Fecha Limite : {{$data['cuadro_presupuesto']->oportunidad->fecha_limite}}</li>
    <li>Cliente : {{$data['cuadro_presupuesto']->oportunidad->entidad->nombre}}</li>
    <li>Tipo de negocio : {{$data['cuadro_presupuesto']->oportunidad->tipoNegocio->tipo}}</li>
    <br>
    @endforeach

    <hr>

    <p> *Este correo es generado de manera automática, por favor no responder.</p>
    <br> Saludos, <br> Módulo de Logística <br> {{config('global.nombreSistema')}}  {{config('global.version')}}</p>

</body>

</html>