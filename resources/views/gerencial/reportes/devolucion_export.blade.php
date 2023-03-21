<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Lista de Devoluciones de penalidad </h1>
    <br>
    <table>
        <thead>
            <tr style="text-align: center;">
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Empresa</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">RUC Cliente</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="25">Cliente</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="25">OCAM</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Fact.</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="20">OC.</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">SIAF</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Gestión</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Fecha Penalidad</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="30">Pagador</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="10">Moneda</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Importe Penalidad</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Importe Cobrado</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Estado</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Doc Penalidad</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Doc Cobro</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="40">Observaciones</th>
                <th style="border: 6px solid #000 !important; background-color: #cccccc; font-weight: bold; text-align: center;" width="15">Usuario Responsable</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
            <tr>
                <td style="border: 6px solid #000 !important;">{{ $item->empresa }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->cliente_ruc }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->cliente }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->ocam }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->factura }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->oc_fisica }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->siaf }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->gestion }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->fecha_penalidad }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->pagador }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->moneda }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->importe_penalidad }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->importe_devolucion }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->estado }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->doc_penalidad }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->doc_devolucion_penalidad }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->motivo_devolucion }}</td>
                <td style="border: 6px solid #000 !important;">{{ $item->usuario_responsable }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
