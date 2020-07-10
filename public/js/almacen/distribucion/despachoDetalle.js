function open_detalle_despacho(data){
    $('#modal-despachoDetalle').modal({
        show: true
    });
    $('#cabecera').text(data.codigo+' - '+data.concepto);
    verDetalleDespacho(data.id_od);
}

function verDetalleDespacho(id_od){
    $.ajax({
        type: 'GET',
        url: 'verDetalleDespacho/'+id_od,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            // detalle_requerimiento = response;
            response.forEach(element => {
                html+='<tr id="'+element.id_od_detalle+'">'+
                '<td>'+i+'</td>'+
                '<td>'+(element.codigo !== null ? element.codigo : '')+'</td>'+
                '<td>'+(element.part_number !== null ? element.part_number : '')+'</td>'+
                '<td>'+(element.categoria !== null ? element.categoria : '')+'</td>'+
                '<td>'+(element.subcategoria !== null ? element.subcategoria : '')+'</td>'+
                '<td>'+(element.descripcion !== null ? element.descripcion : '')+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                '<td>'+(element.abreviatura !== null ? element.abreviatura : '')+'</td>'+
                '<td>'+(element.posicion !== null ? element.posicion : '')+'</td>'+
                '<td>'+element.descripcion_producto+'</td>'+
                '</tr>';
                i++;
            });
            $('#detalleDespacho tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
