<table>
    <thead>
        <tr>
            <th>Revisado</th>
            <th>Fecha Emisión</th>
            <th>Cod.Sal</th>
            <th>Fecha Guía</th>
            <th>Guía</th>
            <th>Fecha Doc</th>
            <th>Tp</th>
            <th>Serie-Número</th>
            <th>RUC</th>
            <th>Razon Social</th>
            <th>Mn</th>
            <th>Valor Neto</th>
            <th>IGV</th>
            <th>Total</th>
            <th>Condicion</th>
            <th>Días</th>
            <th>Operación</th>
            <th>Fecha Vencimento</th>
            <th>Responsable</th>
            <th>Tipo Cambio</th>
            <th>Almacén</th>
            <th>Fecha Registro</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($salidas as $salida)
        <tr>
            <td>{{ $salida["revisado"] }}</td>
            <td>{{ $salida["fecha_emision"] }}</td>
            <td>{{ $salida["codigo"] }}</td>
            <td>{{ $salida["guia_fecha_emision"] }}</td>
            <td>{{ $salida["guia"] }}</td>
            <td>{{ $salida["fecha_documento_venta"] }}</td>
            <td>{{ $salida["tipo_documento_venta"] }}</td>
            <td>{{ $salida["documento_venta"] }}</td>
            <td>{{ $salida["cliente_nro_documento"] }}</td>
            <td>{{ $salida["cliente_razon_social"] }}</td>
            <td>{{ $salida["moneda"] }}</td>
            <td>{{ $salida["total"] }}</td>
            <td>{{ $salida["total_igv"] }}</td>
            <td>{{ $salida["total_a_pagar"] }}</td>
            <td>{{ $salida["saldo"] }}</td>
            <td>{{ $salida["condicion"] }}</td>
            <td>{{ $salida["dias"] }}</td>
            <td>{{ $salida["operacion"] }}</td>
            <td>{{ $salida["fecha_vencimiento"] }}</td>
            <td>{{ $salida["responsable"] }}</td>
            <td>{{ $salida["tipo_cambio"] }}</td>
            <td>{{ $salida["almacen"] }}</td>
            <td>{{ $salida["fecha_registro"] }}</td>
 
 
        </tr>
        @endforeach
    </tbody>
</table>