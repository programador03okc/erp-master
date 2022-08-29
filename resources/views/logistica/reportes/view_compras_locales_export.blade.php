<table>
    <thead>
        <tr>
        <th>Cód. Orden</th>
        <th>Cód. Requerimiento</th>
        <th>Cód. Producto</th>
        <th>Bien comprado/ servicio contratado</th>
        <th>Rubro Proveedor</th>
        <th>Razón Social del Proveedor</th>
        <th>RUC del Proveedor</th>
        <th>Domicilio Fiscal/Principal</th>
        <th>Provincia</th>
        <th>Fecha de presentación del comprobante de pago.</th>
        <th>Fecha de cancelación del comprobante de pago</th>
        <th>Tiempo de cancelación(# días)</th>
        <th>Moneda</th>
        <th>Monto Soles inc IGV</th>
        <th>Monto Dólares inc IGV</th>
        <th>Tipo de Comprobante de Pago</th>
        <th>N° Comprobante de Pago</th>
        <th>Empresa - sede</th>
        <th>Grupo</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($comprasLocales as $compras)

        <tr>
            <td>{{ $compras['codigo'] }}</td>
            <td>{{ $compras['codigo_requerimiento'] }}</td>
            <td>{{ $compras['codigo_producto'] }}</td>
            <td>{{ $compras['descripcion'] }}</td>
            <td>{{ $compras['rubro_contribuyente'] }}</td>
            <td>{{ $compras['razon_social_contribuyente'] }}</td>
            <td>{{ $compras['nro_documento_contribuyente'] }}</td>
            <td>{{ $compras['direccion_contribuyente'] }}</td>
            <td>{{ $compras['ubigeo_contribuyente'] }}</td>
            <td>{{ $compras['fecha_emision_comprobante_contribuyente'] }}</td>
            <td>{{ $compras['fecha_pago'] }}</td>
            <td>{{ $compras['tiempo_cancelacion'] }}</td>
            <td>{{ $compras['moneda_doc_com'] }}</td>
            <td>{{ $compras['total_a_pagar_soles'] }}</td>
            <td>{{ $compras['total_a_pagar_dolares'] }}</td>
            <td>{{ $compras['tipo_doc_com'] }}</td>
            <td>{{ $compras['nro_doc_com'] }}</td>
            <td>{{ $compras['descripcion_sede_empresa'] }}</td>
            <td>{{ $compras['descripcion_grupo'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
