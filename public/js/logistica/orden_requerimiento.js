var rutaRequerimientosPendientes, 
rutaRequerimientosAtendidos,
rutaRequerimientoOrden,
rutaGuardarOrdenPorRequerimiento,
rutaRevertirOrdenPorRequerimiento
;

function inicializar(
    _rutaRequerimientosPendientes,
    _rutaRequerimientosAtendidos,
    _rutaRequerimientoOrden,
    _rutaGuardarOrdenPorRequerimiento,
    _rutaRevertirOrdenPorRequerimiento
    ) {
    
    rutaRequerimientosPendientes = _rutaRequerimientosPendientes;
    rutaRequerimientosAtendidos = _rutaRequerimientosAtendidos;
    rutaRequerimientoOrden = _rutaRequerimientoOrden;
    rutaGuardarOrdenPorRequerimiento = _rutaGuardarOrdenPorRequerimiento;
    rutaRevertirOrdenPorRequerimiento = _rutaRevertirOrdenPorRequerimiento;

}

function tieneAccion(permisoCrearOrdenPorRequerimiento, permisoRevertirOrden){
    listar_requerimientos_pendientes(permisoCrearOrdenPorRequerimiento);

    $('ul.nav-tabs li a').click(function(){

        var activeTab = $(this).attr('href');
        var activeForm = "form-"+activeTab.substring(1);

        if (activeForm == "form-requerimientosAtendidos"){
            listar_requerimientos_atendidos(permisoRevertirOrden);
        } 
        else if (activeForm == "form-requerimientosPendientes"){
            listar_requerimientos_pendientes(permisoCrearOrdenPorRequerimiento);
        }

    });
}

var detalleRequerimientoSelected = [];


function listar_requerimientos_pendientes(permisoCrearOrdenPorRequerimiento){
    var vardataTables = funcDatatables();
    $('#listaRequerimientosPendientes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'order': [[0, 'desc']],
        'destroy' : true,
        'ajax': rutaRequerimientosPendientes,
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'codigo_sede_empresa'},
            {'data': 'fecha_requerimiento'},
            { render: function (data, type, row) {                
                if(permisoCrearOrdenPorRequerimiento == '1') {
                    return ('<div class="btn-group btn-group-sm" role="group">'+
                    '<button type="button" class="btn btn-primary btn-sm" name="btnOpenModalOrdenRequerimiento" title="Generar Orden" data-id-requerimiento="'+row.id_requerimiento+'"  onclick="openModalOrdenRequerimiento(this);">'+
                        '<i class="far fa-file-alt"></i>'+
                    '</button>'+

                '</div>');
                    }else{
                        return ''
                    }
                },
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function listar_requerimientos_atendidos(permisoRevertirOrden){
    var vardataTables = funcDatatables();
    $('#listaRequerimientosAtendidos').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'order': [[0, 'desc']],
        'destroy' : true,
        'ajax': rutaRequerimientosAtendidos,
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'fecha_requerimiento'},
            {'data': 'codigo_softlink'},
            {'data': 'codigo_sede_empresa'},
            {'data': 'fecha_orden'},
            { render: function (data, type, row) {               
                if (permisoRevertirOrden == '1') {
                    return ('<div class="btn-group btn-group-sm" role="group">'+
                            '<button type="button" class="btn btn-danger btn-sm" name="btnEliminarAtencionOrdenRequerimiento" title="Revertir Atención" data-id-requerimiento="'+row.id_requerimiento+'"  data-codigo-requerimiento="'+row.codigo+'" data-id-orden-compra="'+row.id_orden_compra+'" onclick="eliminarAtencionOrdenRequerimiento(this);">'+
                            '<i class="fas fa-backspace"></i>'+
                            '</button>'+
                            '</div>');
                }else {
                    return '';
                }   
            }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

// function updateTableRequerimientoAtendidos(){    
//     // $('#listaRequerimientosAtendidos').DataTable().ajax.reload();
// }

function eliminarAtencionOrdenRequerimiento(obj){
    let codigo_requerimiento = obj.dataset.codigoRequerimiento;
    let id_requerimiento = obj.dataset.idRequerimiento;
    let id_orden = obj.dataset.idOrdenCompra;
    console.log(id_requerimiento,id_orden);
    var ask = confirm('¿Desea revertir el requerimiento '+codigo_requerimiento+'?');
    if (ask == true){
        $.ajax({
            type: 'PUT',
            url: rutaRevertirOrdenPorRequerimiento+'/'+id_orden+'/'+id_requerimiento,
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
        url: rutaRequerimientoOrden+'/'+id,
        dataType: 'JSON',
        success: function(response){
            detalleRequerimientoSelected=response.det_req;
            listar_detalle_orden_requerimiento(response.det_req);
            console.log(response); 
            document.querySelector("div[id='modal-orden-requerimiento'] span[id='codigo_requeriento_seleccionado']").textContent= ' - Requerimiento: '+ response.requerimiento.codigo;
            document.querySelector("div[id='modal-orden-requerimiento'] input[name='id_requerimiento']").value= response.requerimiento.id_requerimiento;
            // document.querySelector("div[id='modal-orden-requerimiento'] select[name='sede']").value= response.requerimiento.id_sede;
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

function validaOrdenRequerimiento(){
    var codigo_orden = $('[name=codigo_orden]').val();
    var id_proveedor = $('[name=id_proveedor]').val();
    var msj = '';
    if (codigo_orden == ''){
        msj+='\n Es necesario que ingrese un código de orden Softlink';
    }
    if (id_proveedor == ''){
        msj+='\n Es necesario que seleccione un Proveedor';
    }
    return  msj;
}

function guardar_orden_requerimiento(data){
    var msj = validaOrdenRequerimiento();
    if (msj.length > 0){
        alert(msj);
    } else{
        $.ajax({
            type: 'POST',
            url: rutaGuardarOrdenPorRequerimiento,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Orden de registrada con éxito');
                    $('#modal-orden-requerimiento').modal('hide');
                    $('#listaRequerimientosPendientes').DataTable().ajax.reload();
                    // $('#listaRequerimientosAtendidos').DataTable().ajax.reload();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
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