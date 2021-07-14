let detalle = [];

function itemsRequerimiento(id_requerimiento){
    detalle = [];
    
    $.ajax({
        type: 'GET',
        url: 'itemsRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            response.forEach(element => {
                if(element.id_tipo_item ==1){
                    detalle.push({
                        'id_detalle_requerimiento'  :element.id_detalle_requerimiento,
                        'id_producto'           :element.id_producto,
                        'codigo'                :element.codigo,
                        'part_number'           :(element.id_producto!==null ? element.part_number_prod : element.part_number),
                        'descripcion'           :(element.id_producto!==null ? element.descripcion_prod : element.descripcion),
                        'cantidad'              :element.cantidad,
                        'tiene_transformacion'  :element.tiene_transformacion,
                        'abreviatura'           :(element.abreviatura!==null?element.abreviatura:''),
                    });
                }
            
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
        var pn = element.part_number;
        var dsc = encodeURIComponent(element.descripcion);
        var link_pn = '';
        var link_des = '';

        if (pn !== null) {
            link_pn = `
            <a href="javascript: void(0);" 
                onclick="openAsignarProducto('`+ pn +`', '`+ dsc +`', `+ element.id_detalle_requerimiento +`, 1);">
            `+ pn +`
            </a>`;
        }
        if (dsc !== null) {
            link_des = `
            <a href="javascript: void(0);" 
                onclick="openAsignarProducto('`+ pn +`', '`+ dsc +`', `+ element.id_detalle_requerimiento +`, 2);">
            `+ decodeURIComponent(dsc) +`
            </a>`;
        }
        html+=`<tr>
            <td>${i}</td>
            <td>${element.codigo!==null?element.codigo:''}</td>
            <td>`+ link_pn +(element.tiene_transformacion ? ' <span class="badge badge-secondary">Transformado</span> ' : '')+`</td>
            <td>`+ link_des +`</td>
            <td>${element.cantidad!==null?element.cantidad:''}</td>
            <td>${element.abreviatura!==null?element.abreviatura:''}</td>
            <td>
                <button type="button" style="padding-left:8px;padding-right:7px;" 
                    class="asignar btn btn-info boton" data-toggle="tooltip" 
                    data-placement="bottom" data-partnumber="${element.part_number}" 
                    data-desc="${encodeURIComponent(element.descripcion)}" data-id="${element.id_detalle_requerimiento}"
                    title="Asignar producto" >
                    <i class="fas fa-angle-double-right"></i>
                </button>
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
    openAsignarProducto(partnumber,desc,id, 0);
});

function openAsignarProducto(partnumber,desc,id, type){
    
    $('#part_number').text(partnumber);
    $('#descripcion').text(decodeURIComponent(desc));
    $('[name=id_detalle_requerimiento]').val(id);
    $('[name=part_number]').val(partnumber);
    $('[name=descripcion]').val(decodeURIComponent(desc));
    $('[name=id_tipo_producto]').val('');
    $('[name=id_categoria]').val('');
    $('[name=id_subcategoria]').val('');
    $('[name=id_clasif]').val(5);
    $('[name=id_unidad_medida]').val(1);
    
    listarProductosCatalogo();
    listarProductosSugeridos(partnumber, decodeURIComponent(desc), type);
    
    $('#modal-mapeoAsignarProducto').modal('show');
    $('[href="#seleccionar"]').tab('show');
    $('#submit_mapeoAsignarProducto').removeAttr('disabled');
}

$("#form-mapeoItemsRequerimiento").on("submit", function(e){
    e.preventDefault();

    var rspta = confirm("¿Está seguro que desea guardar los productos mapeados?");

    if (rspta){
        $("#submit_orden_despacho").attr('disabled','true');
        let lista = [];

        detalle.forEach(element => {
            lista.push({
                'id_detalle_requerimiento'  : element.id_detalle_requerimiento,
                'id_producto'               : element.id_producto,
                'part_number'               : (element.id_producto!==null?'':element.part_number),
                'descripcion'               : (element.id_producto!==null?'':element.descripcion),
                'codigo'                    : element.codigo,
                'cantidad'                  : element.cantidad,
                'abreviatura'               : element.abreviatura,
                'id_categoria'              : element.id_categoria,
                'id_clasif'                 : element.id_clasif,
                'id_subcategoria'           : element.id_subcategoria,
                'id_unidad_medida'          : element.id_unidad_medida,
            });
        });

        let data = 'detalle='+JSON.stringify(lista);
        console.log(data);
        $.ajax({
            type: 'POST',
            url: 'guardar_mapeo_productos',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                alert('Productos mapeados con éxito.');
                $('#modal-mapeoItemsRequerimiento').modal('hide');
                // requerimientoPendienteView.renderRequerimientoPendienteListModule(null, null);
                location.reload();
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
});