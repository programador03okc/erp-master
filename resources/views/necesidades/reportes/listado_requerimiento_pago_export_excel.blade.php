<table>
    <thead>
        <tr>
            <th>Prioridad</th>
            <th>Código</th>
            <th>Concepto</th>
            <th>Tipo Req.</th>
            <th>Fecha Registro</th>

            <th>Fecha de Aprobación</th>
            <th>Empresa</th>
            <th>Grupo</th>
            <th>División</th>
            <th>Proyecto/presupuesto</th>

            <th>Moneda</th>
            <th>Monto Total</th>
            <th>Elaborado por</th>
            <th>Aprobado por</th>
            <th>Estado</th>
            <th>Importe Pagado</th>

            <th>PARTIDA PRSUPUESTAL</th>
            <th>C.Costo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($requerimientos as $requerimiento)
            {{-- @foreach ($requerimientosDetalle as $item) --}}
                <tr>
                    <td>{{ $requerimiento["priori"] }}</td>
                    <td>{{ $requerimiento["codigo"] }}</td>
                    <td>{{ $requerimiento["concepto"] }}</td>
                    <td>{{ $requerimiento["tipo_requerimiento"] }}</td>
                    <td>{{ $requerimiento["fecha_registro"] }}</td>

                    <td>{{ $requerimiento["fecha_autorizacion"] }}</td>
                    <td>{{ $requerimiento["razon_social"] }}</td>
                    <td>{{ $requerimiento["grupo"] }}</td>
                    <td>{{ $requerimiento["division"] }}</td>
                    <td>{{ $requerimiento["descripcion_proyecto"] }}</td>



                    <td>{{ $requerimiento["simbolo_moneda"] }}</td>
                    <td>{{ $requerimiento["monto_total"]    }}</td>
                    <td>{{ $requerimiento["nombre_usuario"] }}</td>
                    <td>{{ $requerimiento["ultimo_aprobador"] }}</td>
                    <td>{{ $requerimiento["estado_doc"]     }}</td>
                    <td>{{ $requerimiento["pago_total"]     }}</td>
                    <td>{{ $requerimiento["partida"] }}</td>
                    <td>{{ $requerimiento["c_costo"]}}</td>
                    {{-- @if ($item->id_requerimiento_pago == $requerimiento['id_requerimiento_pago'])
                        <td>{{ $item->id_requerimiento_pago }}</td>
                        <td>{{ $item->id_requerimiento_pago}}</td>
                    @else
                        <td>{{ ' ' }}</td>
                        <td>{{ ' ' }}</td>
                    @endif --}}

                </tr>
            {{-- @endforeach --}}
        @endforeach
    </tbody>
</table>
