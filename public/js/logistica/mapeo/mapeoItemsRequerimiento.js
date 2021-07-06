let detalle = [];

function itemsRequerimiento(id_requerimiento){
    console.log('id_requerimiento'+id_requerimiento);
    detalle = [];
    $.ajax({
        type: 'GET',
        url: 'itemsRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            
            response.forEach(element => {
                detalle.push({
                    'id_detalle_requerimiento'  :element.id_detalle_requerimiento,
                    'id_producto'        :element.id_producto,
                    'codigo'             :element.codigo,
                    'part_number'        :(element.id_producto!==null ? element.part_number_prod : element.part_number),
                    'descripcion'        :(element.id_producto!==null ? element.descripcion_prod : element.descripcion),
                    'cantidad'           :element.cantidad,
                    'abreviatura'        :(element.abreviatura!==null?element.abreviatura:''),
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
                    data-desc="${element.descripcion}" data-id="${element.id_detalle_requerimiento}"
                    title="Asignar producto" >
                    <i class="fas fa-angle-double-right"></i></button>
            </td>
        </tr>`;
        i++;
    });

    $('#detalleItemsRequerimiento tbody').html(html);
}

$('#detalleItemsRequerimiento tbody').on("click","button.asignar", function(){
    var partnumber = $(this).data('partnumber');
    var desc = $(this).data('desc');
    var id = $(this).data('id');
    
    $('#modal-mapeoAsignarProducto').modal({
        show: true
    });
    $('#part_number').text(partnumber);
    $('#descripcion').text(desc);
    $('[name=id_detalle_requerimiento]').val(id);
    $('[name=part_number]').val(partnumber);
    $('[name=descripcion]').val(desc);
    $('[name=id_tipo_producto]').val('');
    $('[name=id_categoria]').val('');
    $('[name=id_subcategoria]').val('');
    $('[name=id_clasif]').val(5);
    $('[name=id_unidad_medida]').val(1);

    listarProductosCatalogo();
    listarProductosSugeridos(partnumber);
    
    $('#submit_mapeoAsignarProducto').removeAttr('disabled');
});

$("#form-mapeoItemsRequerimiento").on("submit", function(e){
    e.preventDefault();

    var rspta = confirm("¿Está seguro que desea guardar los productos mapeados?");

    if (rspta){
        $("#submit_orden_despacho").attr('disabled','true');
        let data = 'detalle='+JSON.stringify(detalle);

        $.ajax({
            type: 'POST',
            url: 'guardar_mapeo_productos',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                alert('Productos mapeados con éxito.');
                
                $('#modal-mapeoItemsRequerimiento').modal('hide');
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
});