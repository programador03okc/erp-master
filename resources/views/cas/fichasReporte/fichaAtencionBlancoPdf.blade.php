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
        background-color: #bce8f1;
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
    <table width="100%" style="margin-bottom: 0px">
        <tr>
            <td>
                <img src="{{ $logo_empresa }}" height="50px">
            </td>
        </tr>
    </table>
    {{-- <h4 style="text-align: center;
        padding-top: 5px;
        padding-bottom: 5px;
        border-bottom: 1px solid black;
        border-top: 1px solid black;
        background-color: #acf2bf;
        font-size: 22px;margin:0px; padding:0px;">Ficha Reporte</h4> --}}
    <h4 class="text-center" style="margin:0px; padding:0px;">Ficha de Atención</h4>

    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Datos Generales</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 25%" class="text-right">Cliente:</th>
                <td style="width: 35%">{{$incidencia->razon_social}}</td>
                <th style="width: 25%" class="text-right">Cod. Incidencia:</th>
                <td style="width: 35%">{{$incidencia->codigo}}</td>
            </tr>
            <tr>
                <th style="width: 25%" class="text-right">Sede cliente:</th>
                <td style="width: 35%">{{$incidencia->sede_cliente}}</td>
                <th style="width: 25%" class="text-right">Factura:</th>
                <td style="width: 35%">{{$incidencia->factura}}</td>
            </tr>
            <tr>
                <th style="width: 25%" class="text-right">Cod. Requerimiento:</th>
                <td style="width: 35%">{{$incidencia->codigo_requerimiento}}</td>
                <th style="width: 25%" class="text-right">Fecha reporte:</th>
                <td style="width: 35%">{{$incidencia->fecha_reporte}}</td>
            </tr>
            <tr>
                <th style="width: 25%" class="text-right">Concepto:</th>
                <td style="width: 35%" colspan="3">{{$incidencia->concepto}}</td>
            </tr>
        </thead>
    </table>

    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Datos del contacto</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 25%" class="text-right">Nombre contacto:</th>
                <td style="width: 35%">{{$incidencia->nombre}}</td>
                <th style="width: 25%" class="text-right">Cargo:</th>
                <td style="width: 35%">{{$incidencia->cargo}}</td>
            </tr>
            <tr>
                <th style="width: 25%" class="text-right">Dirección contacto:</th>
                <td style="width: 35%">{{$incidencia->direccion}}</td>
                <th style="width: 25%" class="text-right">Teléfono:</th>
                <td style="width: 35%">{{$incidencia->telefono}}</td>
            </tr>
            <tr>
                <th style="width: 25%" class="text-right">Usuario final:</th>
                <td style="width: 35%">{{$incidencia->usuario_final}}</td>
                <th style="width: 25%" class="text-right">Ubigeo:</th>
                <td style="width: 35%">{{$incidencia->ubigeo_descripcion}}</td>
            </tr>
        </thead>
    </table>

    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Descripción del producto(s)</h4>
    </div>
    
    <table>
        <thead>
            @foreach ($productos as $prod)
            <tr>
                <th style="width: 25%" class="text-right">Tipo Producto:</th>
                <td style="width: 35%">{{$prod->producto}}</td>
                <th style="width: 25%" class="text-right">Marca:</th>
                <td style="width: 35%">{{$prod->marca}}</td>
            </tr>
            <tr>
                <th style="width: 25%" class="text-right">Modelo:</th>
                <td style="width: 35%">{{$prod->modelo}}</td>
                <th style="width: 25%" class="text-right">Serie:</th>
                <td style="width: 35%">{{$prod->serie}}</td>
            </tr>
            @endforeach
        </thead>
    </table>
    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Falla reportada</h4>
    </div>
    <table>
        <thead>
            <tr>
                <td style="width: 100%" colspan="4">{{$incidencia->falla_reportada}}<br></td>
            </tr>
            <tr>
                <td style="width: 30%">Tipo de falla:</td>
                <td style="width: 25%">(  ) Software</td>
                <td style="width: 25%" colspan="2">(  ) Hardware</td>
            </tr>
        </thead>
    </table>
    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Acciones realizadas</h4>
    </div>
    <table>
        <thead>
            <tr>
                <td style="width: 100%; border-bottom: 1px solid rgb(156, 156, 156);color: white;" colspan="4">X</td>
            </tr>
            <tr>
                <td style="width: 100%; border-bottom: 1px solid rgb(156, 156, 156);color: white;" colspan="4">X</td>
            </tr>
            <tr>
                <td style="width: 100%; border-bottom: 1px solid rgb(156, 156, 156);color: white;" colspan="4">X</td>
            </tr>
            <tr>
                <td style="color: white;" colspan="4">X</td>
            </tr>
            <tr>
                <td style="width: 30%">Estado de atención:</td>
                <td style="width: 25%">(  ) Cerrado</td>
                <td style="width: 25%">(  ) Pendiente</td>
                <td style="width: 25%">(  ) Cancelado</td>
            </tr>
            <tr>
                <td style="width: 30%">Equipo operativo:</td>
                <td style="width: 25%" >(  ) SI</td>
                <td style="width: 25%" colspan="2">(  ) NO</td>
            </tr>
        </thead>
    </table>
    <div class="seccion-hoja">
        <h4 style="font-size: 14px;">Comentarios del cierre</h4>
    </div>
    <table>
        <thead>
            <tr>
                <td style="width: 100%; border-bottom: 1px solid rgb(156, 156, 156);color: white;" colspan="4">X</td>
            </tr>
            <tr>
                <td style="width: 100%; border-bottom: 1px solid rgb(156, 156, 156);color: white;" colspan="4">X</td>
            </tr>
            <tr>
                <td style="color: white;" colspan="4">X</td>
            </tr>
            <tr>
                <td style="width: 30%">Hora llegada: __________</td>
                <td style="width: 25%">Hora inicio: __________</td>
                <td style="width: 25%">Hora final: __________</td>
                <td style="width: 25%">Hora salida: __________</td>
            </tr>
        </thead>
    </table>

    <table>
        <thead>
            <tr>
                <th style="height: 80px;" colspan="2" ></th>
            </tr>
            <tr>
                <th style="width: 50%" class="text-center">___________________________________</th>
                <th style="width: 50%" class="text-center">___________________________________</th>
            </tr>
            <tr>
                <th style="width: 50%" class="text-center">Cliente</th>
                <th style="width: 50%" class="text-center">Representante de servicios</th>
            </tr>
        </thead>
    </table>
    <footer style="position:absolute;bottom:0px;right:0px;">
        {{-- <p style="text-align:right;font-size:10px;margin-bottom:0px;">
        {{'Registrado por: ' . $incidencia->usuario->nombre_corto }}
        </p> --}}
        <p style="text-align:right;font-size:10px;margin-bottom:0px;margin-top:0px;">
            {{'Fecha registro: ' . $fecha_registro . ' ' . $hora_registro }}
            </p>
        <p style="text-align:right;font-size:10px;margin-top:0px;">
            <strong>{{config('global.nombreSistema') . ' '  . config('global.version')}}</strong>
        </p>
    </footer>
</body>
</html>