<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <h5>DATOS DE CONTACTO</h5>
    <ul>
        <li>Cliente/Entidad : {{$contacto->razon_social}}</li>
        <li>Nombre : {{$contacto->nombre}}</li>
        @if($contacto->cargo!==null)
        <li>Cargo: {{$contacto->cargo}}</li>
        @endif
        @if($contacto->telefono!==null)
        <li>Teléfono: {{$contacto->telefono}}</li>
        @endif
        @if($contacto->horario!==null)
        <li>Horario de atención: {{$contacto->horario}}</li>
        @endif
    </ul>
    Saludos,
    <br>
    {!! nl2br($usuario) !!}
    {{-- <hr>
    <br> --}}
    {{-- Para ver el cuadro de presupuesto, haga clic <a href="{{route('mgcp.cuadro-costos.detalles',['id' => $oportunidad->id])}}">aquí</a>. --}}
</body>

</html>