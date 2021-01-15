
function format ( table_id, id, row ) {
    $.ajax({
        type: 'GET',
        url: 'verDetalleRequerimiento/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            
            if (response.length > 0){
                response.forEach(element => {
                    html+='<tr '+(element.tiene_transformacion ? ' style="background-color: gainsboro;" ' : '')+' id="'+element.id_detalle_requerimiento+'">'+
                    '<td style="border: none;">'+i+'</td>'+
                    '<td style="border: none;">'+(element.producto_codigo !== null ? element.producto_codigo : '')+(element.tiene_transformacion ? ' <span class="badge badge-secondary">Transformado</span> ' : '')+'</td>'+
                    '<td style="border: none;">'+(element.part_number !== null ? element.part_number : '')+'</td>'+
                    '<td style="border: none;">'+(element.producto_descripcion !== null ? element.producto_descripcion : element.descripcion_adicional)+'</td>'+
                    '<td style="border: none;">'+element.cantidad+'</td>'+
                    // '<td style="border: none;">'+(element.suma_transferencias!==null?element.suma_transferencias:'')+'</td>'+
                    '<td style="border: none;">'+(element.suma_ingresos!==null?element.suma_ingresos:'0')+'</td>'+
                    '<td style="border: none;">'+(element.stock_comprometido!==null?element.stock_comprometido:'0')+'</td>'+
                    '<td style="border: none;">'+(element.suma_despachos_internos!==null?element.suma_despachos_internos:'0')+'</td>'+
                    '<td style="border: none;">'+(element.suma_despachos_externos!==null?element.suma_despachos_externos:'0')+'</td>'+
                    '<td style="border: none;">'+(element.abreviatura !== null ? element.abreviatura : '')+'</td>'+
                    '<td style="border: none;"><span class="label label-'+element.bootstrap_color+'">'+element.estado_doc+'</span></td>'+
                    '</tr>';
                    i++;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Código</th>
                        <th style="border: none;">PartNumber</th>
                        <th style="border: none;">Descripción</th>
                        <th style="border: none;">Cantidad</th>
                        <th style="border: none;">Ingresado</th>
                        <th style="border: none;">Stock Alm.</th>
                        <th style="border: none;">Transformación</th>
                        <th style="border: none;">Despachado</th>
                        <th style="border: none;">Unid.</th>
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
            console.log(tabla);
            row.child( tabla ).show();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}