<table>
    <thead>
        <tr>
            <th>Empresa</th>
            <th>Tipo</th>
            <th>RUC Cliente</th>
            <th>Cliente</th>
            <th>OCAM</th>
            <th>Fact.</th>
            <th>UU.EE.</th>
            <th>OC.</th>
            <th>SIAF</th>
            <th>FTE FTO</th>
            <th>Fecha Emisión</th>
            <th>Fecha Recepción</th>
            <th>Moneda</th>
            <th>Importe</th>
            <th>Plazo Crédito </th>
            <th>Días de Retraso</th>
            <th>Estado</th>
            <th>Fase</th>
            <th>Area Responsable</th>
            <th>Vendedor</th>
            <th>Programación de Pago </th>
            <th>Categoría</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $requerimiento)
        <tr>
            <td>{{ $requerimiento->codigo_empresa }}</td>
            <td>{{ $requerimiento->nombre_sector }}</td>
            <td>{{ $requerimiento->cliente_ruc }}</td>
            <td>{{ $requerimiento->cliente }}</td>
            <td>{{ $requerimiento->oc }}</td>
            <td>{{ $requerimiento->factura }}</td>
            <td>{{ $requerimiento->uu_ee }}</td>
            <td>{{ $requerimiento->oc }}</td>
            <td>{{ $requerimiento->siaf }}</td>
            <td>{{ $requerimiento->fuente_financ }}</td>
            <td>{{ $requerimiento->fecha_emision }}</td>
            <td>{{ $requerimiento->fecha_recepcion }}</td>
            <td>{{ $requerimiento->moneda }}</td>
            <td>{{ $requerimiento->importe }}</td>
            <td>{{ $requerimiento->plazo_credito }}</td>
            <td>{{ $requerimiento->atraso }}</td>
            <td>{{ $requerimiento->estado }}</td>
            <td>{{ $requerimiento->fase }}</td>
            <td>{{ $requerimiento->area }}</td>
            <td>{{ '--' }}</td>
            <td>{{ $requerimiento->fecha_pago }}</td>
            <td>{{ $requerimiento->categoria }}</td>
            <td>{{ '--' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


