function open_detalle_requerimiento(data){
    $('#modal-requerimientoDetalle').modal({
        show: true
    });
    $('#cabecera_orden').text(data.codigo+' - '+data.concepto);
    var idTabla = 'detalleRequerimiento';
    listar_detalle_requerimiento(data.id_requerimiento, idTabla);
}

function listar_detalle_requerimiento(id_requerimiento, idTabla){
    $.ajax({
        type: 'GET',
        url: 'verDetalleRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            detalle_requerimiento = response;
            console.log(detalle_requerimiento);
            response.forEach(element => {
                html+='<tr id="'+element.id_detalle_requerimiento+'">'+
                '<td>'+(idTabla == 'detalleRequerimiento' ? i : '<input type="checkbox" onChange="changeCheckIngresa(this,'+element.id_detalle_requerimiento+');"/>')+'</td>'+
                '<td>'+(element.producto_codigo !== null ? element.producto_codigo : '')+'</td>'+
                '<td>'+(element.part_number !== null ? element.part_number : '')+'</td>'+
                '<td>'+(element.categoria !== null ? element.categoria : '')+'</td>'+
                '<td>'+(element.subcategoria !== null ? element.subcategoria : '')+'</td>'+
                '<td>'+(element.producto_descripcion !== null ? element.producto_descripcion : element.descripcion_adicional)+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                '<td>'+(element.abreviatura !== null ? element.abreviatura : '')+'</td>'+
                '<td>'+(element.almacen_descripcion !== null ? element.almacen_descripcion : '')+'</td>'+
                // '<td>'+(element.codigo_posicion !== null ? element.codigo_posicion : '')+'</td>'+
                // '<td>'+(element.lugar_entrega !== null ? element.lugar_entrega : element.lugar_despacho_orden)+'</td>'+
                '<td><span class="label label-'+element.bootstrap_color+'">'+element.estado_doc+'</span></td>'+
                // '<td>'+(element.id_almacen !== null ? 
                //     '<button type="button" class="btn btn-info" data-toggle="tooltip" data-placement="bottom" title="Ver Transferencia" onClick="#"><i class="fas fa-file-alt"></i></button>' : '')+
                // '</td>'+
                '</tr>';
                i++;
            });
            console.log(html);
            $('#'+idTabla+' tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
