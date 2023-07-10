<table>
    <thead>
        <tr>
            <th><b>Cód. Orden</b></th>
            <th><b>Fecha Emisión Orden</b></th>
            <th><b>Cód. Requerimiento</b></th>
            <th><b>Cód. Producto</b></th>
            <th><b>Bien comprado/ servicio contratado</b></th>
            <th><b>Rubro Proveedor</b></th>
            <th><b>Razón Social del Proveedor</b></th>
            <th><b>RUC del Proveedor</b></th>
            <th><b>Domicilio Fiscal/Principal</b></th>
            <th><b>Provincia</b></th>
            <th><b>Fecha de presentación del comprobante de pago.</b></th>
            <th><b>Fecha de cancelación del comprobante de pago</b></th>
            <th><b>Tiempo de cancelación(# días)</b></th>
            <th><b>Cantidad</b></th>
            <th><b>Moneda</b></th>
            <th><b>Precio Soles</b></th>
            <th><b>Precio Dolares</b></th>
            <th><b>Monto Soles inc IGV</b></th>
            <th><b>Monto Dólares inc IGV</b></th>
            <th><b>Tipo de Comprobante de Pago</b></th>
            <th><b>N° Comprobante de Pago</b></th>
            <th><b>Empresa - sede</b></th>
            <th><b>Grupo</b></th>
            <th><b>Proyecto</b></th>
            <th><b>Estado pago</b></th>
            <th><b>Observación</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($comprasLocales as $compras)

        <tr>
            <td>{{ $compras['codigo'] }}</td>
            <td>{{ $compras['fecha_emision'] }}</td>
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
            <td>{{ $compras['cantidad'] }}</td>
            <td>{{ $compras['moneda_orden'] }}</td>
            <td>{{ $compras['total_precio_soles_item'] }}</td>
            <td>{{ $compras['total_precio_dolares_item'] }}</td>
            <td>{{ $compras['total_a_pagar_soles'] }}</td>
            <td>{{ $compras['total_a_pagar_dolares'] }}</td>
            <td>{{ $compras['tipo_doc_com'] }}</td>
            <td>{{ $compras['nro_comprobante'] }}</td>
            <td>{{ $compras['descripcion_sede_empresa'] }}</td>
            <td>{{ $compras['descripcion_grupo'] }}</td>
            <td>{{ $compras['descripcion_proyecto'] }}</td>
            <td>{{ $compras['descripcion_estado_pago'] }}</td>
            <td>{{ $compras['observacion_orden'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
