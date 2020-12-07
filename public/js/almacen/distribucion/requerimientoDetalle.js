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
                html+='<tr '+(element.tiene_transformacion ? ' style="background-color: gainsboro;" ' : '')+' id="'+element.id_detalle_requerimiento+'">'+
                '<td>'+(idTabla == 'detalleRequerimiento' ? i : '<input type="checkbox" onChange="changeCheckIngresa(this,'+element.id_detalle_requerimiento+');"/>')+'</td>'+
                '<td>'+(element.producto_codigo !== null ? element.producto_codigo : '')+(element.tiene_transformacion ? ' <span class="badge badge-secondary">Transformado</span> ' : '')+'</td>'+
                '<td>'+(element.part_number !== null ? element.part_number : '')+'</td>'+
                '<td>'+(element.producto_descripcion !== null ? element.producto_descripcion : element.descripcion_adicional)+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                // '<td>'+(element.suma_transferencias!==null?element.suma_transferencias:'')+'</td>'+
                '<td>'+(element.suma_ingresos!==null?element.suma_ingresos:'')+'</td>'+
                '<td>'+(element.suma_despachos_internos!==null?element.suma_despachos_internos:'')+'</td>'+
                '<td>'+(element.abreviatura !== null ? element.abreviatura : '')+'</td>'+
                '<td><span class="label label-'+element.bootstrap_color+'">'+element.estado_doc+'</span></td>'+
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
