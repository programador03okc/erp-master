var ordenSelected=[];

$(function(){
    $('#listaOrdenesCompra tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaOrdenesCompra').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_orden_com').text(id);
    });
});

function orden_compraModal(){
    $('#modal-orden_compra').modal({
        show: true
    });
    // clearDataTable();
    ordenSelected=[];

    let formName = document.getElementsByClassName('page-main')[0].getAttribute('type');
    
    if (formName =='doc_compra'){
        var id_proveedor = $('[name=id_proveedor]').val();
        if (id_proveedor !== null && id_proveedor !== '' && id_proveedor !== 0){
            listarOrdenesProveedor(id_proveedor);
        } else {
            alert('No ha ingresado un proveedor!');
        } 

    }
}

function listarOrdenesProveedor(id_proveedor){
    var vardataTables = funcDatatables();
    $('#listaOrdenesCompra').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': '/listar_ordenes_sin_comprobante/'+id_proveedor,
        'columns': [
            {'data': 'id_orden_compra'},
            {'data': 'razon_social'},
            {'render':
                function (data, type, row){
                    return (row.codigo);
                }
            },
            {'render':
                function (data, type, row){
                    return (formatDate(row.fecha));
                }
            },
            {'data': 'des_estado'},
            {'data': 'id_proveedor'}
        ],
        'columnDefs': [{ 'aTargets': [0,5], 'sClass': 'invisible'}],
    });
}


function selectOrdenCompra(){
    var myId = $('.modal-footer #id_orden_com').text();
    var id_doc_com = $('.modal-footer #id_doc_com').text();
    var page = $('.page-main').attr('type');

    if (page == "doc_compra"){
        if (myId !== null && myId !== ''){
            open_modal_detalle_orden(myId);
        }
    } 
    $('#modal-orden_compra').modal('hide');
}


function open_modal_detalle_orden(id_orden){
    $('#modal-detalle_orden').modal({
        show: true
    });
    var id_doc_com = $('[name=id_doc_com]').val();
    if (id_orden !== null){
        $.ajax({
            type: 'GET',
            url: '/get_orden/'+id_orden,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if (response.detalle_orden.length > 0){
                    listar_detalle_orden(response.detalle_orden);
                    ordenSelected = response;
                }else{
                    alert('la orden seleccionada no tiene items');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('Debe seleccionar una Orden');
    }
}

function listar_detalle_orden(data){
    var vardataTables = funcDatatables();
    $('#listaDetalleOrden').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'scrollX': true,
        'data': data,
        'columns': [
            {'data': 'id_detalle_requerimiento'},
            {'render':
            function (data, type, row){
                let checkbox =
                '<input type="checkbox" name="checkIdDetalleOrden" data-id-detalle-orden="' +
                row.id_detalle_requerimiento +
                '" checked />'
            return checkbox
            }
            },
            {'data': 'codigo_item'},
            {'data': 'descripcion_producto'},
            {'data': 'cantidad_cotizada'},
            {'data': 'unidad_medida_cotizado'},
            {'data': 'precio_cotizado'},
            {'data': 'subtotal'},
            {'data': 'plazo_entrega'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}


function selectDetalleOrden(){
    // moment(myfecha).format("DD/MM/YYYY")
    var headOrden ={
        'id_orden_compra':ordenSelected.header_orden.id_orden_compra,
        'codigo_orden':ordenSelected.header_orden.codigo,
        'fecha_emision':(ordenSelected.header_orden.fecha_orden).slice(0,10),
        'id_proveedor':ordenSelected.header_proveedor.id_proveedor,
        'razon_social_proveedor':ordenSelected.header_proveedor.razon_social_proveedor,
        'tipo_documento':ordenSelected.header_orden.tipo_documento
    };
    var IdDetalleOrdenListEnabled=[];
    let checkLength = document.querySelectorAll("input[name='checkIdDetalleOrden']").length;
    for (let index = 0; index <checkLength; index++) {
        if(document.querySelectorAll("input[name='checkIdDetalleOrden']")[index].checked == true){
            IdDetalleOrdenListEnabled.push(parseInt(document.querySelectorAll("input[name='checkIdDetalleOrden']")[index].dataset.idDetalleOrden));
        }
        
    }

    var detalleOrden= [];
    ordenSelected.detalle_orden.forEach(element => {
        if(IdDetalleOrdenListEnabled.includes(element.id_detalle_requerimiento)==true){
            detalleOrden.push(element);
        }
    });

    console.log(IdDetalleOrdenListEnabled);
    console.log(headOrden);
    console.log(detalleOrden);
    let data = {'header':headOrden,'detalle_orden':detalleOrden};
    guardar_doc_com_det(data);
}

function guardar_doc_com_det(data){
    var id_doc_com = $('[name=id_doc_com]').val();
        $.ajax({
        type: 'POST',
        url: '/guardar_doc_com_det_orden/'+id_doc_com,
        dataType: 'JSON',
        data: data,
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Items registrados con éxito');
                listar_doc_com_orden(id_doc_com);
            //     actualiza_totales();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

function listar_doc_com_orden(id_doc_com){
    if(id_doc_com >0){
        $.ajax({
            type: 'GET',
            url: '/listar_doc_com_orden/'+id_doc_com,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                // if (response > 0){
                //     listar_doc_com_orden(id_doc_com);
                // }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
// function listar_orden(id_orden){
//     var id_doc_com = $('[name=id_doc_com]').val();
//     if (id_orden !== null){
//         var rspta = confirm('¿Esta seguro que desea agregar los items de ésta Orden?');
//         if (rspta){
//             $.ajax({
//                 type: 'GET',
//                 url: '/guardar_doc_items_orden/'+id_orden+'/'+id_doc_com,
//                 dataType: 'JSON',
//                 success: function(response){
//                     console.log(response);
//                     // if (response > 0){
//                     //     alert('Items registrados con éxito');
//                     //     listar_doc_items(id_doc_com);
//                     //     listar_doc_guias(id_doc_com);
//                     //     actualiza_totales();
//                     // }
//                 }
//             }).fail( function( jqXHR, textStatus, errorThrown ){
//                 console.log(jqXHR);
//                 console.log(textStatus);
//                 console.log(errorThrown);
//             });
//         }
//     } else {
//         alert('Debe seleccionar una Orden');
//     }
// }

function agregar_orden(data){

}