
function format(table_id, id, row, $boton) {
    if (id !== null) {
        $.ajax({
            type: 'GET',
            url: 'verDetalleRequerimientoDI/' + id,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                var html = '';
                var i = 1;

                if (response.length > 0) {
                    response.forEach(element => {
                        html += '<tr ' + (element.tiene_transformacion ? ' style="background-color: gainsboro;" ' : '') + ' id="' + element.id_detalle_requerimiento + '">' +
                            '<td style="border: none;">' + i + '</td>' +
                            '<td style="border: none;text-align:center">' + (element.producto_codigo !== null ? element.producto_codigo : '(producto no mapeado)') + (element.tiene_transformacion ? ' <span class="badge badge-secondary">Transformado</span> ' : '') + '</td>' +
                            '<td style="border: none;text-align:center">' + (element.part_number !== null ? element.part_number : '') + '</td>' +
                            '<td style="border: none;">' + (element.producto_descripcion !== null ? element.producto_descripcion : (element.descripcion !== null ? element.descripcion : '')) + '</td>' +
                            '<td style="border: none;text-align:center">' + element.cantidad + '</td>' +
                            '<td style="border: none;text-align:center">' + (element.abreviatura !== null ? element.abreviatura : '') + '</td>' +
                            // '<td style="border: none;">'+(element.suma_transferencias!==null?element.suma_transferencias:'')+'</td>'+
                            // '<td style="border: none;">' + (element.almacen_guia_com_descripcion !== null ? element.almacen_guia_com_descripcion : '') + '</td>' +
                            // '<td style="border: none;">' + (element.suma_ingresos !== null ? element.suma_ingresos : '0') + '</td>' +
                            '<td style="border: none;text-align:center">' + (element.almacen_reserva_descripcion !== null ? element.almacen_reserva_descripcion : '') + '</td>' +
                            '<td style="border: none;text-align:center">' + (element.stock_comprometido !== null ? element.stock_comprometido : '0') + '</td>' +
                            '<td style="border: none;text-align:center">' + (element.suma_despachos_internos !== null ? element.suma_despachos_internos : '0') + '</td>' +
                            // '<td style="border: none;">' + (element.suma_despachos_externos !== null ? element.suma_despachos_externos : '0') + '</td>' +
                            '<td style="border: none;"><span class="label label-' + element.bootstrap_color + '">' + element.estado_doc + '</span></td>' +
                            '</tr>';
                        i++;
                    });
                    var tabla = `<table class="table table-sm" style="border: none;" 
                    id="detalle_${table_id}">
                    <thead style="color: black;background-color: #c7cacc;">
                        <tr>
                            <th style="border: none;">#</th>
                            <th style="border: none;">Cod.Prod.</th>
                            <th style="border: none;">PartNumber</th>
                            <th style="border: none;">Descripción</th>
                            <th style="border: none;">Cantidad</th>
                            <th style="border: none;">Unid.</th>
                            <th style="border: none;">Alm.Reserva</th>
                            <th style="border: none;">Reservado</th>
                            <th style="border: none;">Transformación</th>
                            <th style="border: none;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>${html}</tbody>
                    </table>`;
                }
                else {
                    var tabla = `<table class="table table-sm" style="border: none;" 
                    id="detalle_${table_id}">
                    <tbody>
                        <tr><td>No hay registros para mostrar</td></tr>
                    </tbody>
                    </table>`;
                }
                $boton.prop('disabled', false);
                row.child(tabla).show();
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        var tabla = `<table class="table table-sm" style="border: none;" 
            id="detalle_${table_id}">
            <tbody>
                <tr><td>No hay registros para mostrar</td></tr>
            </tbody>
            </table>`;

        $boton.prop('disabled', false);
        row.child(tabla).show();
    }
}