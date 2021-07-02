let detalle = [];

function itemsRequerimiento(id_requerimiento){
    console.log('id_requerimiento'+id_requerimiento);
    
    $.ajax({
        type: 'GET',
        url: 'itemsRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            let id = null;
            response.forEach(element => {
                detalle.push({
                    'id_detalle_requerimiento'  :element.id_detalle_requerimiento,
                    'id_producto'               :element.id_producto,
                    'codigo'                    :element.codigo,
                    'part_number'               :element.part_number,
                    'descripcion'               :element.descripcion,
                    'cantidad'                  :element.cantidad,
                    'abreviatura'               :element.abreviatura,
                });
            
            });
            mostrar_detalle();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_detalle(){
    var html = '';
    var i = 1;

    detalle.forEach(element => {
        html+=`<tr>
            <td>${i}</td>
            <td>${element.codigo!==null?element.codigo:''}</td>
            <td>${element.part_number!==null?element.part_number:''}</td>
            <td>${element.descripcion!==null?element.descripcion:''}</td>
            <td>${element.cantidad!==null?element.cantidad:''}</td>
            <td>${element.abreviatura!==null?element.abreviatura:''}</td>
            <td>
                <button type="button" style="padding-left:8px;padding-right:7px;" 
                    class="asignar btn btn-info boton" data-toggle="tooltip" 
                    data-placement="bottom" data-partnumber="${element.part_number}" 
                    data-desc="${element.descripcion}" title="Asignar producto" >
                    Asignar</button>
            </td>
        </tr>`;
        i++;
    });

    $('#detalleItemsRequerimiento tbody').html(html);
}

$('#detalleItemsRequerimiento tbody').on("click","button.asignar", function(){
    var partnumber = $(this).data('partnumber');
    var desc = $(this).data('desc');
    
    $('#modal-mapeoAsignarProducto').modal({
        show: true
    });
    $('#part_number').text(partnumber);
    $('#descripcion').text(desc);
    listarProductosCatalogo();
    listarProductosSugeridos(partnumber);
    
    $('#submit_mapeoAsignarProducto').removeAttr('disabled');
});