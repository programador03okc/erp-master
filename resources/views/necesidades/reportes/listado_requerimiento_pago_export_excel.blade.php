<table>
    <thead>
        <tr>
            <th>Prioridad</th>
            <th>Código</th>
            <th>Concepto</th>

            {{-- <th>Fecha Entrega</th> --}}
            <th>Tipo Req.</th>
            <th>Fecha Registro</th>
            <th>Fecha de Aprobación</th>
            <th>Empresa</th>
            <th>Grupo</th>
            <th>División</th>
            <th>Proyecto/presupuesto</th>
            <th>PARTIDA PRSUPUESTAL</th>
            <th>C.Costo</th>
            <th>Moneda</th>
            <th>Monto Total</th>
            {{-- <th>Observacion</th> --}}
            <th>Creado por</th>
            <th>Estado</th>
            <th>Importe</th>
            <th>Pagado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requerimientos as $requerimiento)
        <tr>
            <td>{{ $requerimiento["priori"] }}</td>
            <td>{{ $requerimiento["codigo"] }}</td>
            <td>{{ $requerimiento["concepto"] }}</td>

            {{-- <td>{{ $requerimiento["fecha_entrega"] }}</td> --}}
            <td>{{ $requerimiento["tipo_requerimiento"] }}</td>
            <td>{{ $requerimiento["fecha_registro"] }}</td>
            <td>{{ ' ' }}</td>
            <td>{{ $requerimiento["razon_social"] }}</td>
            <td>{{ $requerimiento["grupo"] }}</td>
            <td>{{ $requerimiento["division"] }}</td>
            <td>{{ $requerimiento["descripcion_proyecto"] }}</td>
            <td>{{ ' ' }}</td>
            <td>{{ ' ' }}</td>
            <td>{{$requerimiento["simbolo_moneda"]}}</td>
            <td>{{ $requerimiento["monto_total"] }}</td>
            {{-- <td>{{ $requerimiento["observacion"] }}</td> --}}
            <td>{{ $requerimiento["nombre_usuario"] }}</td>
            <td>{{ $requerimiento["estado_doc"] }}</td>
            <td>{{ ' ' }}</td>
            <td>{{ ' ' }}</td>

        </tr>
        @endforeach
    </tbody>
</table>
