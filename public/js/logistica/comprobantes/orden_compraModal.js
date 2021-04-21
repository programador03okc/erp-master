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
        'ajax': 'listar_ordenes_sin_comprobante/'+id_proveedor,
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
            {'data': 'part_number'},
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
            // console.log(response);
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
                // console.log(response);
                if (response.status == 200){
                    listar_ordenes(id_doc_com,response.ordenes);
                    // console.log(response.doc_com_doc_com_det[0].doc_com_det);
                    
                    listar_detalle(id_doc_com,response.doc_com_doc_com_det[0].doc_com_det);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function listar_ordenes(id_doc_com,data){
    var vardataTables = funcDatatables();
    $('#ordenes').DataTable({
        'dom': 'rt',
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        // 'scrollX': true,
        'data': data,
        'columns': [
            {'data': 'id_orden_compra'},
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return (formatDate(row.fecha));
                }
            },
            {'render':
                function (data, type, row){
                    return (row.razon_social+' RUC:'+row.nro_documento);
                }
            },
            {'data': 'tipo_documento'},
            {'render':
            function (data, type, row){
                let icon =
                '<i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="" onclick="anular_orden('+id_doc_com+', '+row.id_orden_compra+');" data-original-title="Anular Orden"></i>';
                return icon;            
            }
        }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function anular_orden(id_doc_com,id_orden_compra){
    var anula = confirm("¿Esta seguro que desea anular ésta OC?\nSe quitará también la relación de sus Items");
    if (anula){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: '/anular_orden_doc_com/'+id_doc_com+'/'+id_orden_compra,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if (response > 0){
                    alert('Orden anulada con éxito');
                    // $("#doc-"+id_doc_com_guia).remove();
                    listar_doc_com_orden(id_doc_com);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function listar_detalle(id_doc_com,data){
    document.querySelector("table[id='listaDetalle']").firstElementChild.children[0].children[0].textContent = "Código Orden";
    var vardataTables = funcDatatables();
    $('#listaDetalle').DataTable({
        'dom': 'rt',
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        // 'scrollX': true,
        'data': data,
        'columns': [
            {'data': 'codigo_orden'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
            function (data, type, row){
                let cantidad = '<input type="number" class="input-data right" name="cantidad"  onChange="calcula_total('+row.id_doc_det+')" value="'+row.cantidad+'" disabled="true"/>';
                return cantidad;            
            }
        },
            {'data': 'abreviatura'},
            {'render':
                function (data, type, row){
                    let precio_unitario = '<input type="number" class="input-data right" name="precio_unitario"  onChange="calcula_total('+row.id_doc_det+')" value="'+row.precio_unitario+'" disabled="true"/>';
                    return precio_unitario;            
                }
            },
            {'render':
                function (data, type, row){
                    let porcen_dscto = '<input type="number" class="input-data right" name="porcen_dscto"  onChange="calcula_dscto('+row.id_doc_det+')" value="'+row.porcen_dscto+'" disabled="true"/>';
                    return porcen_dscto;            
                }
            },
            {'render':
                function (data, type, row){
                    let total_dscto = '<input type="number" class="input-data right" name="total_dscto"  onChange="calcula_total('+row.id_doc_det+')" value="'+row.total_dscto+'" disabled="true"/>';
                    return total_dscto;            
                }
            },
            {'render':
                function (data, type, row){
                    let precio_total = '<input type="number" class="input-data right" name="precio_total" value="'+row.precio_total+'" disabled="true"/>';
                    return precio_total;            
                }
            },
            {'render':
                function (data, type, row){
                    let icon ='<div style="display:flex;"><i class="fas fa-pen-square icon-tabla blue boton" data-toggle="tooltip" data-placement="bottom" title="Editar Item" onClick="editar_detalle('+row.id_doc_det+');"></i>'+
                    '<i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" title="Anular Item" onClick="anular_detalle('+row.id_doc_det+');"></i></div>';
                    return icon;            
                }
            }
        ],
        // 'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
