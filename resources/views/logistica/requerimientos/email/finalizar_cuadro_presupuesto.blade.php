<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    {!! nl2br($mensaje) !!}

    <!-- {{-- Para ver el cuadro de presupuesto, haga clic <a href="{{route('mgcp.cuadro-costos.detalles',['id' => $oportunidad->id])}}">aquí</a>. --}} -->

    @foreach($listaFinalizados as $lf)
        Para ver el cuadro, haga clic <a href="/mgcp/public/mgcp/cuadro-costos/detalles/{{$lf['id_cuadro_presupuesto']}}">aquí</a>
    @endforeach
    
    <br>
    <br>
    <hr>

 {!! nl2br($piePagina) !!}

</body>

</html>