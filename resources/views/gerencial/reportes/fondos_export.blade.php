<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Lista de Fondos y Auspicios </h1>
    <br>
    <table>
        <thead>
            <tr style="text-align: center;">
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Fecha</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Tipo</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="25">Negocio</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="25">Entidad</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">CLAIM</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Periodo</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Moneda</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Importe</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Forma de Pago</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Fecha inicio</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Fecha vencimiento</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Estado</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="20">Nro Documento</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="30">Pagador</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="40">Observaciones</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Usuario Responsable</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
            <tr>
                <td style="border: 6px solid #000 !important;">{{ $item->fecha_solicitud }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->tipo_gestion }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->tipo_negocio }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->razon_social }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->claim }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->periodo }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->moneda }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->importe }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->forma_pago }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->fecha_inicio }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->fecha_vencimiento }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->estado }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->nro_documento }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->pagador }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->detalles }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->usuario_responsable }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
