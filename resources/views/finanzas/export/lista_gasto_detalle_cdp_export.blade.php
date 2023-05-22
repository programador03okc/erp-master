<table>
    <thead>
        <tr>
            <th style="background-color:#787878;">Oportunidad</th>
            <th style="background-color:#787878;">Desc. Oportunidad</th>
            <th style="background-color:#787878;">Tipo Negocio</th>
            <th style="background-color:#787878;">Moneda Oportunidad</th>
            <th style="background-color:#787878;">Importe Oportunidad</th>
            <th style="background-color:#787878;">Fecha Oportunidad</th>
            <th style="background-color:#787878;">Estado Oportunidad</th>
            <th style="background-color:#787878;">Part-number</th>
            <th style="background-color:#787878;" width="90">Descripción</th>
            <th style="background-color:#787878;">P.V.U. O/C (sinIGV) S/ </th>
            <th style="background-color:#787878;">Flete O/C (sinIGV) S/ </th>
            <th style="background-color:#787878;">Cant.</th>
            <th style="background-color:#787878;">Garant. Meses</th>
            <th style="background-color:#787878;">Origen Costo</th>
            <th style="background-color:#787878;" width="80">Proveedor Seleccionado</th>
            <th style="background-color:#787878;">Moneda Costo Unit.(SinIGV)</th>
            <th style="background-color:#787878;">Costo Unit.(SinIGV)</th>
            <th style="background-color:#787878;">Plazo Prov.</th>
            <th style="background-color:#787878;">Flete S/ (SinIGV)</th>
            <th style="background-color:#787878;">Fondo Proveedor</th>
            <th style="background-color:#787878;">Moneda costo compra</th>
            <th style="background-color:#787878;">Costo de Compra</th>
            <th style="background-color:#787878;">Costo de compra en soles</th>
            <th style="background-color:#787878;">Total flete proveedor</th>
            <th style="background-color:#787878;">Costo compra + flete</th>
            <th style="background-color:#787878;">Creado por</th>
            <th style="background-color:#787878;">Fecha creación</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
                <tr>
                    <td>{{ $item['codigo_oportunidad'] }}</td>
                    <td>{{ $item['oportunidad'] }}</td>
                    <td>{{ $item['tipo_negocio'] }}</td>
                    <td>{{ $item['moneda_oportunidad'] }}</td>
                    <td>{{ $item['importe_oportunidad'] }}</td>
                    <td>{{ $item['fecha_registro_oportunidad'] }}</td>
                    <td>{{ $item['estado_oportunidad'] }}</td>
                    <td>{{ $item['part_no'] }}</td>
                    <td>{{ $item['descripcion'] }}</td>
                    <td>{{ $item['pvu_oc'] }}</td>
                    <td>{{ $item['flete_oc'] }}</td>
                    <td>{{ $item['cantidad'] }}</td>
                    <td>{{ $item['garantia'] }}</td>
                    <td>{{ $item['origen_costo'] }}</td>
                    <td>{{ $item['razon_social_proveedor'] }}</td>
                    <td>{{ $item['moneda_costo_unitario_proveedor'] }}</td>
                    <td>{{ $item['costo_unitario_proveedor'] }}</td>
                    <td>{{ $item['plazo_proveedor'] }}</td>
                    <td>{{ $item['flete_proveedor'] }}</td>
                    <td>{{ $item['fondo_proveedor'] }}</td>
                    <td>{{ $item['moneda_costo_compra'] }}</td>
                    <td>{{ $item['importe_costo_compra'] }}</td>
                    <td>{{ $item['importe_costo_compra_soles'] }}</td>
                    <td>{{ $item['total_flete_proveedor'] }}</td>
                    <td>{{ $item['costo_compra_mas_flete_proveedor'] }}</td>
                    <td>{{ $item['nombre_autor'] }}</td>
                    <td>{{ $item['created_at'] }}</td>
 

                </tr>
        @endforeach
    </tbody>
</table>