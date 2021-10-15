<table>
    <thead>
        <tr>
            <th>Revisado</th>
            <th>Fecha Emisión</th>
            <th>Cod.Ing</th>
            <th>Fecha Guía</th>
            <th>Guía</th>
            <th>Fecha Doc</th>
            <th>Serie-Número</th>
            <th>RUC</th>
            <th>Razon Social</th>
            <th>Ordenes</th>
            <th>Mn</th>
            <th>Valor Neto</th>
            <th>IGV</th>
            <th>Total</th>
            <th>Condicion</th>
            <th>Operación</th>
            <th>Responsable</th>
            <th>Almacén</th>
            <th>Fecha Registro</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ingresos as $ingreso)
        <tr>
            <td>{{ $ingreso["revisado"] }}</td>
            <td>{{ $ingreso["fecha_emision"] }}</td>
            <td>{{ $ingreso["codigo"] }}</td>
            <td>{{ $ingreso["fecha_guia"] }}</td>
            <td>{{ $ingreso["guia"] }}</td>
            <td>{{ $ingreso["fecha_doc"] }}</td>
            <td>{{ $ingreso["documentos"] }}</td>
            <td>{{ $ingreso["nro_documento"] }}</td>
            <td>{{ $ingreso["razon_social"] }}</td>
            <td>{{ $ingreso["ordenes"] }}</td>
            <td>{{ $ingreso["simbolo"] }}</td>
            <td>{{ $ingreso["total"] }}</td>
            <td>{{ $ingreso["total_igv"] }}</td>
            <td>{{ $ingreso["total_a_pagar"] }}</td>
            <td>{{ $ingreso["des_condicion"] }}</td>
            <td>{{ $ingreso["des_operacion"] }}</td>
            <td>{{ $ingreso["nombre_trabajador"] }}</td>
            <td>{{ $ingreso["des_almacen"] }}</td>
            <td>{{ $ingreso["fecha_registro"] }}</td>
 
        </tr>
        @endforeach
    </tbody>
</table>