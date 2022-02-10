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
 

<body>
 
@if($idTipoDocumento == 1)
    <h4 style="text-align: center;
        background-color: #acf2bf;
        padding-top: 5px;
        padding-bottom: 5px;
        border-bottom: 1px solid black;
        border-top: 1px solid black;
        font-size: 22px;margin:0px; padding:0px;">{{is_null($requerimiento->codigo) ? '' : $requerimiento->codigo}} - {{is_null($accion) ? '' : $accion}}
    </h4>
@elseif($idTipoDocumento ==11)
<h4 style="text-align: center;
        background-color: #acf2bf;
        padding-top: 5px;
        padding-bottom: 5px;
        border-bottom: 1px solid black;
        border-top: 1px solid black;
        font-size: 22px;margin:0px; padding:0px;">{{is_null($requerimiento->codigo) ? '' : $requerimiento->codigo}} - {{is_null($accion) ? '' : $accion}}
    </h4>
@endif

    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Información adicional</h4>
    </div>
    @if($idTipoDocumento == 1)
    <table>
        <thead>
            <tr>
                <th style="width: 15%" class="text-right">Concepto:</th>
                <td style="width: 35%">{{is_null($requerimiento->concepto) ? '' : $requerimiento->concepto}}</td>
                <th style="width: 15%" class="text-right">Tipo de requerimiento:</th>
                <td style="width: 35%">{{is_null($requerimiento->tipo->descripcion) ? '' : $requerimiento->tipo->descripcion}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">División:</th>
                <td style="width: 35%">{{is_null($requerimiento->division->descripcion) ? '' : $requerimiento->division->descripcion}}</td>
                <th style="width: 15%" class="text-right">Fecha limite de entrega:</th>
                <td style="width: 35%">{{is_null($requerimiento->fecha_entrega) ? '' : $requerimiento->fecha_entrega}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Monto Total:</th>
                <td style="width: 35%">{{is_null($requerimiento->moneda->simbolo) ? '' : $requerimiento->moneda->simbolo}} {{is_null($montoTotal)?'': number_format($montoTotal, 2)}}</td>
                <th style="width: 15%" class="text-right">Creado por:</th>
                <td style="width: 35%">{{is_null($nombreCompletoUsuarioPropietarioDelDocumento) ? '' : $nombreCompletoUsuarioPropietarioDelDocumento}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Estado {{is_null($accion)? '' : ucfirst(strtolower($accion))}} por:</th>
                <td style="width: 35%">{{is_null($nombreCompletoUsuarioRevisaAprueba) ? '' : $nombreCompletoUsuarioRevisaAprueba}}</td>
                <th style="width: 15%" class="text-right">Sustento:</th>
                <td style="width: 35%">{{is_null($sustento) ? '' : $sustento}}</td>
            </tr>
        </thead>
    </table>
    @elseif($idTipoDocumento ==11)
    <table>
        <thead>
            <tr>
                <th style="width: 15%" class="text-right">Concepto:</th>
                <td style="width: 35%">{{is_null($requerimiento->concepto) ? '' : $requerimiento->concepto}}</td>
                <th style="width: 15%" class="text-right">Tipo de requerimiento de pago:</th>
                <td style="width: 35%">{{is_null($requerimiento->tipoRequerimientoPago->descripcion) ? '' : $requerimiento->tipoRequerimientoPago->descripcion}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">División:</th>
                <td style="width: 35%">{{is_null($requerimiento->division->descripcion) ? '' : $requerimiento->division->descripcion}}</td>
                <th style="width: 15%" class="text-right">Comentario:</th>
                <td style="width: 35%">{{is_null($requerimiento->comentario) ? '' : $requerimiento->comentario}}</td>
            </tr>
            <tr>
                <th style="width: 15%" class="text-right">Monto Total:</th>
                <td style="width: 35%">{{is_null($requerimiento->moneda->simbolo) ? '' : $requerimiento->moneda->simbolo}} {{is_null($montoTotal)?'': number_format($montoTotal, 2)}}</td>
                <th style="width: 15%" class="text-right">Creado por:</th>
                <td style="width: 35%">{{is_null($nombreCompletoUsuarioPropietarioDelDocumento) ? '' : $nombreCompletoUsuarioPropietarioDelDocumento}}</td>
            </tr>
 
            <tr>
                <th style="width: 15%" class="text-right">Estado {{is_null($accion)? '' : ucfirst(strtolower($accion))}} por:</th>
                <td style="width: 35%">{{is_null($nombreCompletoUsuarioRevisaAprueba) ? '' : $nombreCompletoUsuarioRevisaAprueba}}</td>
                <th style="width: 15%" class="text-right">Sustento:</th>
                <td style="width: 35%">{{is_null($sustento) ? '' : $sustento}}</td>
            </tr>
        </thead>
    </table>
    @endif
    <br>
    <hr>
<footer>
    {!! $piePagina !!}

</footer>

</body>

</html>