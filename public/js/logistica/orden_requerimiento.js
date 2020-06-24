var detalleRequerimientoSelected = [];

$(function(){
    listar_requerimientos_pendientes();
    listar_requerimientos_atendidos();
});


 

function listar_requerimientos_pendientes(){
    var vardataTables = funcDatatables();
    $('#listaRequerimientosPendientes').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'order': [[0, 'desc']],
        'destroy' : true,
        'ajax': '/listar_requerimientos_pendientes',
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'codigo_sede_empresa'},
            {'data': 'fecha_requerimiento'},
            { render: function (data, type, row) {                
                    let btn =
                    '<div class="btn-group btn-group-sm" role="group">'+
                        '<button type="button" class="btn btn-primary btn-sm" name="btnOpenModalOrdenRequerimiento" title="Generar Orden" data-id-requerimiento="'+row.id_requerimiento+'"  onclick="openModalOrdenRequerimiento(this);">'+
                            '<i class="far fa-file-alt"></i>'+
                        '</button>'+

                    '</div>';
                    return (btn);
                },
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function listar_requerimientos_atendidos(){
    var vardataTables = funcDatatables();
    $('#listaRequerimientosAtendidos').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'order': [[0, 'desc']],
        'destroy' : true,
        'ajax': '/listar_requerimientos_atendidos',
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'fecha_requerimiento'},
            {'data': 'codigo_softlink'},
            {'data': 'codigo_sede_empresa'},
            {'data': 'fecha_orden'},
            { render: function (data, type, row) {               
                    let btn =
                    '<div class="btn-group btn-group-sm" role="group">'+
                        '<button type="button" class="btn btn-danger btn-sm" name="btnEliminarAtencionOrdenRequerimiento" title="Revertir Atención" data-id-requerimiento="'+row.id_requerimiento+'"  data-codigo-requerimiento="'+row.codigo+'" data-id-orden-compra="'+row.id_orden_compra+'" onclick="eliminarAtencionOrdenRequerimiento(this);">'+
                            '<i class="fas fa-backspace"></i>'+
                        '</button>'+

                    '</div>';
                    return (btn);
                },
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function eliminarAtencionOrdenRequerimiento(obj){
    let codigo_requerimiento = obj.dataset.codigoRequerimiento;
    let id_requerimiento = obj.dataset.idRequerimiento;
    let id_orden = obj.dataset.idOrdenCompra;
    console.log(id_requerimiento,id_orden);
    var ask = confirm('¿Desea revertir el requerimiento '+codigo_requerimiento+'?');
    if (ask == true){
        $.ajax({
            type: 'PUT',
            url: '/revertir_orden_requerimiento/'+id_orden+'/'+id_requerimiento,
            beforeSend: function(){
            },
            success: function(response){
                console.log(response);                
                if (response.status == 200) {
                    alert('Se revertió la orden y restablecio el estado del requerimiento');
                    $('#listaRequerimientosAtendidos').DataTable().ajax.reload();
                    $('#listaRequerimientosPendientes').DataTable().ajax.reload();
                }else {
                    alert('hubo un problema, No se puedo revertir el restablecio el estado del requerimiento y anular la orden');
                    console.log(response);
                    
                }
            }
        });
        return false;
    }else{
        return false;
    }
    
}

function openModalOrdenRequerimiento(obj){
    // console.log(obj.dataset.idRequerimiento);    
    $('#modal-orden-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });
    obtenerRequerimiento(obj.dataset.idRequerimiento);
}

function obtenerRequerimiento(id){
    $.ajax({
        type: 'GET',
        url: '/get_requerimiento_orden/'+id,
        dataType: 'JSON',
        success: function(response){
            detalleRequerimientoSelected=response.det_req;
            listar_detalle_orden_requerimiento(response.det_req);
            console.log(response); 
            document.querySelector("div[id='modal-orden-requerimiento'] span[id='codigo_requeriento_seleccionado']").textContent= ' - Requerimiento: '+ response.requerimiento.codigo;
            document.querySelector("div[id='modal-orden-requerimiento'] input[name='id_requerimiento']").value= response.requerimiento.id_requerimiento;
            document.querySelector("div[id='modal-orden-requerimiento'] select[name='sede']").value= response.requerimiento.id_sede;
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}


$("#form-orden-requerimiento").on("submit", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    var payload = data+'&detalle_requerimiento='+JSON.stringify(detalleRequerimientoSelected);
    guardar_orden_requerimiento(payload);
});

function guardar_orden_requerimiento(data){
    console.log(data);
    $.ajax({
        type: 'POST',
        url: '/guardar_orden_por_requerimiento',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Orden de registrada con éxito');
                $('#modal-orden-requerimiento').modal('hide');
                $('#listaRequerimientosPendientes').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}

function listar_detalle_orden_requerimiento(data){
    var vardataTables = funcDatatables();
    $('#listaDetalleOrden').dataTable({
        bDestroy: true,
        order: [[1, 'desc']],
        info:     true,
        iDisplayLength:2,
        paging:   true,
        searching: false,
        language: vardataTables[0],
        processing: true,
        bDestroy: true,
        data:data,
        columns: [
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            { data: 'codigo_item' },
            {'render':
                function (data, type, row, meta){
                    return row.descripcion_adicional;
                }
            },
            { data: 'unidad_medida' },
            { data: 'cantidad' }
        ],

    })

    let tablelistaitem = document.getElementById('listaDetalleOrden_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;


}