<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    {!! nl2br($mensaje) !!}
    <hr>
    <br>
    {{-- Para ver el cuadro de presupuesto, haga clic <a href="{{route('mgcp.cuadro-costos.detalles',['id' => $oportunidad->id])}}">aquí</a>. --}}
</body>

</html>