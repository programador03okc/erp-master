<table>
    <thead>
        <tr>
            <th>Guía</th>
            <th>Fecha Guía</th>
            <th>Sede Guía</th>
            <th>Entidad Cliente</th>
            <th>Responsable</th>
            <th>Cod. Trans.</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($requerimientos as $requerimiento)
        <tr>
            <td>{{ $requerimiento->serie.'-'.$requerimiento->numero }}</td>
            <td>{{ date("d-m-Y", strtotime($requerimiento->fecha_emision)) }}</td>
            <td>{{ $requerimiento->sede_descripcion }}</td>
            <td>{{ $requerimiento->razon_social }}</td>
            <td>{{ $requerimiento->nombre_corto_trans }}</td>
            <td>{{ $requerimiento->codigo_trans }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
