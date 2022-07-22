<table>
    <thead>
        <tr>
            <th>Fecha de Facturisación Solicitada</th>
            <th>Obs Facturación</th>
            <th>Código</th>
            <th>Concepto</th>
            <th>Sede Req</th>
            <th>Entidad/Clinte</th>
            <th>Responsable</th>
            <th>OCAM</th>
            <th>C.P.</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($requerimientos as $requerimiento)
        <tr>
            <td>{{ date("d-m-Y", strtotime($requerimiento->fecha_facturacion)) }}</td>
            <td>{{ $requerimiento->obs_facturacion }}</td>
            <td>{{ $requerimiento->codigo }}</td>
            <td>{{ $requerimiento->concepto }}</td>
            <td>{{ $requerimiento->sede_descripcion }}</td>
            <td>{{ $requerimiento->razon_social }}</td>
            <td>{{ $requerimiento->nombre_corto }}</td>
            <td>{{ $requerimiento->nro_orden }}</td>
            <td>{{ $requerimiento->codigo_oportunidad }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
