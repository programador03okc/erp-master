
var rutaRequerimientosPendientes, 
rutaOrdenesEnProceso,
rutaDetalleRequerimientoOrden,
rutaGuardarOrdenPorRequerimiento,
rutaRevertirOrdenPorRequerimiento,
rutaActualizarEstadoOrdenPorRequerimiento,
rutaActualizarEstadoDetalleOrdenPorRequerimiento,
rutaActualizarEstadoDetalleRequerimiento,
rutaSedeByEmpresa
;

var listCheckReq=[];
var detalleRequerimientoSelected = [];
var selectRequirementsToLink='';
var linksToReqObjArray=[];
var payload_orden=[];

function inicializar(
    _rutaRequerimientosPendientes,
    _rutaOrdenesEnProceso,
    _rutaDetalleRequerimientoOrden,
    _rutaGuardarOrdenPorRequerimiento,
    _rutaRevertirOrdenPorRequerimiento,
    _rutaActualizarEstadoOrdenPorRequerimiento,
    _rutaActualizarEstadoDetalleOrdenPorRequerimiento,
    _rutaActualizarEstadoDetalleRequerimiento,
    _rutaSedeByEmpresa
    ) {
    
    rutaRequerimientosPendientes = _rutaRequerimientosPendientes;
    rutaOrdenesEnProceso = _rutaOrdenesEnProceso;
    rutaDetalleRequerimientoOrden = _rutaDetalleRequerimientoOrden;
    rutaGuardarOrdenPorRequerimiento = _rutaGuardarOrdenPorRequerimiento;
    rutaRevertirOrdenPorRequerimiento = _rutaRevertirOrdenPorRequerimiento;
    rutaActualizarEstadoOrdenPorRequerimiento = _rutaActualizarEstadoOrdenPorRequerimiento;
    rutaActualizarEstadoDetalleOrdenPorRequerimiento = _rutaActualizarEstadoDetalleOrdenPorRequerimiento;
    rutaActualizarEstadoDetalleRequerimiento = _rutaActualizarEstadoDetalleRequerimiento;
    rutaSedeByEmpresa = _rutaSedeByEmpresa;

}

$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaItems tbody').on('click', 'tr', function(){
        // if ($(this).hasClass('eventClick')){
        //     $(this).removeClass('eventClick');
        // } else {
        //     $('#listaItems').dataTable().$('tr.eventClick').removeClass('eventClick');
        //     $(this).addClass('eventClick');
        // }
        var idItem = $(this)[0].children[0].innerHTML;
        var idProd = $(this)[0].children[1].innerHTML;
        var idServ = $(this)[0].children[2].innerHTML;
        var idEqui = $(this)[0].children[3].innerHTML;
        var codigo = $(this)[0].children[4].innerHTML;
        var partNum = $(this)[0].children[5].innerHTML;
        var categoria = $(this)[0].children[6].innerHTML;
        var subcategoria = $(this)[0].children[7].innerHTML;
        var descri = $(this)[0].children[8].innerHTML;
        var unidad = $(this)[0].children[9].innerHTML;
        var id_unidad = $(this)[0].children[10].innerHTML;
        $('.modal-footer #id_item').text(idItem);
        $('.modal-footer #codigo').text(codigo);
        $('.modal-footer #part_number').text(partNum);
        $('.modal-footer #descripcion').text(descri);
        $('.modal-footer #id_producto').text(idProd);
        $('.modal-footer #id_servicio').text(idServ);
        $('.modal-footer #id_equipo').text(idEqui);
        $('.modal-footer #unidad_medida').text(unidad);
        $('.modal-footer #id_unidad_medida').text(id_unidad);
        $('.modal-footer #categoria').text(categoria);
        $('.modal-footer #subcategoria').text(subcategoria);
    });
});
$( "#check-guarda_en_requerimiento input[type='checkbox']" ).on( "click", function(){
    if ($(this).prop('checked')){
         $(this).attr('value', 1);
    } else {
         $(this).attr('value', 0);
    }
}); 
function statusBtnGenerarOrden(){
    let countStateCheckTrue=0;

    listCheckReq.map(value => {
        if (value.stateCheck == true) {
            countStateCheckTrue += 1;
        }
    })

    
    if (countStateCheckTrue > 0) {
        document
            .getElementById('btnCrearOrdenCompra')
            .removeAttribute('disabled')
    } else {
        document
            .getElementById('btnCrearOrdenCompra')
            .setAttribute('disabled', true)
    }
}

function agregarListCheckReq(id,stateCheck){
    let newCheckReq = {
        id_req: id,
        stateCheck: stateCheck,
    };
    listCheckReq.push(newCheckReq);
    statusBtnGenerarOrden();
}

function evalSelectedCheckReq(id,stateCheck){
    let arrIdReq=[];
    let newCheckReq = {
        id_req: id,
        stateCheck: stateCheck,
    };

    listCheckReq.map(value => {
            arrIdReq.push(value.id_req);
    });

    if (arrIdReq.includes(newCheckReq.id_req) == true) {
        // actualiza
        listCheckReq.map(value => {
            if (value.id_req == newCheckReq.id_req) {
                value.stateCheck = newCheckReq.stateCheck
                // console.log(newCheckReq.stateCheck);
            }
        });
    } else {
        listCheckReq.push(newCheckReq)
    }

    statusBtnGenerarOrden();

}

$('#listaRequerimientosPendientes tbody').on('click', 'tr', function () {
    if ($(this).hasClass('eventClick')) {
        $(this).removeClass('eventClick')
    } else {
        $('#listaRequerimientosPendientes')
            .dataTable()
            .$('tr.eventClick')
            .removeClass('eventClick')
        $(this).addClass('eventClick')
    }
    let id = $(this)[0].childNodes[1].childNodes[0].dataset.idRequerimiento
    let stateCheck = $(this)[0].childNodes[1].childNodes[0].checked



    if (listCheckReq.length == 0) {
        agregarListCheckReq(id,stateCheck);
    }else{
        evalSelectedCheckReq(id,stateCheck);
    }
    // console.log(listCheckReq);

})

function openModalCrearOrdenCompra(){

let reqTrueList=[];
if(listCheckReq.length >0){
    listCheckReq.forEach(element => {
        if(element.stateCheck == true){
            reqTrueList.push(element.id_req)
        }
        
    });
    
    $('#modal-orden-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });
    obtenerRequerimiento(reqTrueList);
    cleanFormModalOrdenRequerimiento();
    // console.log(reqTrueList);
}else{
    alert("No existe Requerimiento seleccionado");
}
}

function tieneAccion(permisoCrearOrdenPorRequerimiento, permisoRevertirOrden){
   let id_empresa= document.querySelector("select[id='id_empresa_select_req']").value;
    listar_requerimientos_pendientes(permisoCrearOrdenPorRequerimiento,id_empresa,null);

    $('ul.nav-tabs li a').click(function(){

        var activeTab = $(this).attr('href');
        var activeForm = "form-"+activeTab.substring(1);

        if (activeForm == "form-comprasEnProceso"){
            listar_ordenes_en_proceso(permisoRevertirOrden);
        } 
        else if (activeForm == "form-requerimientosPendientes"){
            listar_requerimientos_pendientes(permisoCrearOrdenPorRequerimiento,id_empresa,null);
        }

    });
}

function llenarTablaListaItemsRequerimiento(data){
    console.log(data);
    var vardataTables = funcDatatables();
    $('#listaItemsRequerimiento').dataTable({
        'scrollY':        '50vh',
        'info':     false,
        'searching': false,
        'paging':   false,
        'scrollCollapse': true,
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        "scrollX": true,
        'data':data,
        'columns': [
            {'render':
                function (data, type, row,meta){
                    return meta.row +1
                }
            },
            {'data': 'codigo_item'},
            {'data': 'part_number'},
            {'data': 'categoria'},
            {'data': 'subcategoria'},
            {'data': 'descripcion'},
            {'data': 'unidad_medida'},
            {'data': 'cantidad'},
            {'data': 'precio_referencial'},
            {'data': 'estado_doc'},
            {'data': 'observacion'},
            {'render':
            function (data, type, row, meta){
                let action ='';
                    action = `
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-warning btn-sm" title="Atendido por Almacén" name="btnAtendidoPorAlmacen" data-id_requerimiento="${(row.id_requerimiento?row.id_requerimiento:0)}" data-id_detalle_requerimiento="${(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)}"  onclick="AtendidoPorAlmacen(this);">
                        <i class="fas fa-pallet"></i>
                        </button>
                    </div>
                    `;
                return action;
            }
        }
        ],
        'order': [
            [0, 'asc']
        ]
    });
    let tablelistaitem = document.getElementById(
        'listaItemsRequerimiento_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

function AtendidoPorAlmacen(obj){
    let id_requerimiento = obj.dataset.id_requerimiento;
    let id_detalle_requerimiento = obj.dataset.id_detalle_requerimiento;
    console.log(id_requerimiento);
    console.log(id_detalle_requerimiento);

    $.ajax({
        type: 'PUT',
        url: rutaActualizarEstadoDetalleRequerimiento+'/'+id_detalle_requerimiento+'/'+5,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.status == 200){
                alert('El estado del item fue actualizado');
                fillTablaListaItemsRequerimiento(id_requerimiento);
            }else{
                alert('Hubo un problema al intentar Actualizado');
                
            }

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function openModalVerDetalleRequerimiento(obj){
    let id_requerimiento = obj.dataset.idRequerimiento;
    // console.log(id_requerimiento);

    $('#modal-lista-items-requerimiento').modal({
        show: true,
        backdrop: 'true'
    });
    fillTablaListaItemsRequerimiento(id_requerimiento)
}

function fillTablaListaItemsRequerimiento(id_requerimiento){
    $.ajax({
        type: 'GET',
        url:  `/logistica/gestion-logistica/requerimiento/elaboracion/mostrar-requerimiento/${id_requerimiento}/0`,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            llenarTablaListaItemsRequerimiento(response.det_req)
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_requerimientos_pendientes(permisoCrearOrdenPorRequerimiento,id_empresa= null,id_sede =null){
    var vardataTables = funcDatatables();
    $('#listaRequerimientosPendientes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'order': [[0, 'desc']],
        'destroy' : true,
        'ajax': rutaRequerimientosPendientes+'/'+id_empresa+'/'+id_sede,
        'columns': [
            {'data': 'id_requerimiento'},
            { render: function (data, type, row) { 
                return `<input type="checkbox" data-id-requerimiento="${row.id_requerimiento}" />`;
                }
            },
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'tipo_req_desc'},
            {'data': 'tipo_cliente_desc'},
            { render: function (data, type, row) { 
                let entidad = '';
                if(row.id_cliente > 0){
                    entidad = `${row.cliente_razon_social} RUC: ${row.cliente_ruc}`;
                }else if(row.id_persona >0){
                    entidad = `${row.nombre_persona}`;
                }   
                return entidad;
                }
            },
            {'data': 'empresa_sede'},
            {'data': 'usuario'},
            {'data': 'estado_doc'},
            {'data': 'fecha_requerimiento'},
            { render: function (data, type, row) { 
                // if(permisoCrearOrdenPorRequerimiento == '1') {
                    return ('<div class="btn-group btn-group-sm" role="group">'+
                    '<button type="button" class="btn btn-primary btn-sm" name="btnOpenModalVerDetalleRequeriento" title="Ver Detalle Requerimiento" data-id-requerimiento="'+row.id_requerimiento+'"  onclick="openModalVerDetalleRequerimiento(this);">'+
                        '<i class="far fa-eye"></i>'+
                    '</button>'+

                '</div>');
                    // }else{
                    //     return ''
                    // }
                },
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function listar_ordenes_en_proceso(permisoRevertirOrden){
    var vardataTables = funcDatatables();
    $('#listaComprasEnProceso').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'order': [[7, 'desc']],
        'destroy' : true,
        'ajax': rutaOrdenesEnProceso,
        'columns': [
            { render: function (data, type, row, meta) {     
                return meta.row +1;
            }
            },
            { render: function (data, type, row) {     
                return `<span class="label label-default" onClick="verOrdenModal(this);" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}"  data-codigo-requerimiento="${row.codigo_requerimiento}" data-id-requerimiento="${row.orden_id_requerimiento}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: pointer;" title="Ver Orden">${row.orden_codigo_softlink}</span>`;

            }
            },
            { render: function (data, type, row) {     
                return `${row.nro_documento} - ${row.razon_social}`;
                }
            },
            { render: function (data, type, row) {     
                return `${row.subcategoria}`;
                }
            },
            { render: function (data, type, row) {     
                return `${row.categoria}`;
                }
            },
            { render: function (data, type, row) {     
                return `${row.part_number}`;
                }
            },
            { render: function (data, type, row) {     
                return `${row.alm_prod_descripcion}`;
                }
            },
            { render: function (data, type, row) {     
                return `${row.orden_fecha}`;
                }
            },
            { render: function (data, type, row) {     
                return `${row.orden_plazo_entrega}`;
                }
            },
            { render: function (data, type, row) {     
               var estimatedTimeOfArrive= moment(row.orden_fecha).add(row.orden_plazo_entrega, 'days').format('YYYY-MM-DD hh:mm:ss');
                return `${estimatedTimeOfArrive}`;
                }
            },
            {'data': 'empresa_sede'},
            { render: function (data, type, row) {     
                return `<span class="label label-${row.detalle_orden_estado_bootstrap_color}" onClick="editarEstadoItemOrden(this);" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.detalle_orden_estado}</span>`;

                }
            },
            { render: function (data, type, row) {     
                return row.observacion?row.observacion:"Ninguna";
                }
            },
            { render: function (data, type, row) {               
                if (permisoRevertirOrden == '1') {
                    return ('<div class="btn-group btn-group-sm" role="group">'+
                            '<button type="button" class="btn btn-danger btn-xs" name="btnEliminarAtencionOrdenRequerimiento" title="Revertir Atención" data-id-requerimiento="'+row.orden_id_requerimiento+'"  data-codigo-requerimiento="'+row.codigo_requerimiento+'" data-id-orden-compra="'+row.orden_id_orden_compra+'" onclick="eliminarAtencionOrdenRequerimiento(this);">'+
                            '<i class="fas fa-backspace fa-xs"></i>'+
                            '</button>'+
                            '<button type="button" class="btn btn-primary btn-xs" name="btnDocumentosVinculados" title="Ver Documento Vinculados" data-id-requerimiento="'+row.orden_id_requerimiento+'"  data-codigo-requerimiento="'+row.codigo_requerimiento+'" data-id-orden-compra="'+row.orden_id_orden_compra+'" onclick="documentosVinculados(this);">'+
                            '<i class="fas fa-folder fa-xs"></i>'+
                            '</button>'+
                            '</div>');
                }else {
                    return '';
                }   
            }
            }
        ],
 
        // 'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

// function updateTableRequerimientoAtendidos(){    
//     // $('#listaComprasEnProceso').DataTable().ajax.reload();
// }
function fillEstados(id_estado_actual){
    // console.log(id_estado_actual);
    let html ='';
    let estados = [
            {'17':'ENVIADO'},
            {'30':'CONFIRMADA'},
            {'31':'FACTURADA'},
            {'20':'DESPACHADO'},
            {'22':'EN TRANSITO'}
    ];


    if(id_estado_actual > 0 ){
    estados.forEach(element => {
            if(id_estado_actual == (Object.keys(element)[0])){
                html+=`<option value="${Object.keys(element)[0]}" selected>${Object.values(element)[0]}</option>`;
            }else{
                html+=`<option value="${Object.keys(element)[0]}">${Object.values(element)[0]}</option>`;
            }
            
        });
        document.querySelector("select[name='estado_orden']").innerHTML = html;
        document.querySelector("select[name='estado_detalle_orden']").innerHTML = html;
    }else{
        estados.forEach(element => {
            html+=`<option value="${Object.keys(element)[0]}">${Object.values(element)[0]}</option>`;
            
        });
        document.querySelector("select[name='estado_orden']").innerHTML = html;
        document.querySelector("select[name='estado_detalle_orden']").innerHTML = html;
    }


}

function editarEstadoOrden(obj){
    let id_orden = obj.dataset.idOrdenCompra;
    let id_estado_actual = obj.dataset.idEstadoOrdenCompra;
    let codigo = obj.dataset.codigoOrdenCompra;

    $('#modal-editar-estado-orden').modal({
        show: true,
        backdrop: 'true'
    });

    document.querySelector("div[id='modal-editar-estado-orden'] input[name='id_orden_compra'").value = id_orden;
    document.querySelector("div[id='modal-editar-estado-orden'] span[name='codigo_orden_compra'").textContent = codigo;

    fillEstados(id_estado_actual);

}

function editarEstadoItemOrden(obj){
    let id_orden_compra = obj.dataset.idOrdenCompra;
    let id_detalle_orden = obj.dataset.idDetalleOrdenCompra;
    let id_estado_actual = obj.dataset.idEstadoDetalleOrdenCompra;
    let codigo_item = obj.dataset.codigoItem;

    $('#modal-editar-estado-detalle-orden').modal({
        show: true,
        backdrop: 'true'
    });

    document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_orden_compra'").value = id_orden_compra;
    document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_detalle_orden_compra'").value = id_detalle_orden;
    document.querySelector("div[id='modal-editar-estado-detalle-orden'] span[name='codigo_item_orden_compra'").textContent = codigo_item;

    fillEstados(id_estado_actual);

}

function updateEstadoOrdenCompra(){
    let id_orden_compra = document.querySelector("div[id='modal-editar-estado-orden'] input[name='id_orden_compra'").value;
    let id_estado_orden_selected = document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'").value;
    let estado_orden_selected = document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'")[document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'").selectedIndex].textContent;
    
    $.ajax({
        type: 'POST',
        url: rutaActualizarEstadoOrdenPorRequerimiento,
        data:{'id_orden_compra':id_orden_compra, 'id_estado_orden_selected':id_estado_orden_selected},
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            listar_ordenes_en_proceso(1);
            if(response ==1){
                alert('El estado fue Actualizado');
                document.querySelector("span[id='estado_orden']").textContent = estado_orden_selected;
                $('#modal-editar-estado-orden').modal('hide');
            }else{
                alert('Hubo un problema al intentar Actualizado');
                
            }

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function updateEstadoDetalleOrdenCompra(){
    let id_orden_compra = document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_orden_compra'").value;
    let id_detalle_orden_compra = document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_detalle_orden_compra'").value;
    let id_estado_detalle_orden_selected = document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'").value;
    let estado_detalle_orden_selected = document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'")[document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'").selectedIndex].textContent;
    
    console.log(id_detalle_orden_compra);
    console.log(id_estado_detalle_orden_selected);
    $.ajax({
        type: 'POST',
        url: rutaActualizarEstadoDetalleOrdenPorRequerimiento,
        data:{'id_detalle_orden_compra':id_detalle_orden_compra, 'id_estado_detalle_orden_selected':id_estado_detalle_orden_selected},
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            listar_ordenes_en_proceso(1);
            if(response ==1){
                alert('El estado del item fue actualizado');
                ver_orden(id_orden_compra);
                $('#modal-editar-estado-detalle-orden').modal('hide');
            }else{
                alert('Hubo un problema al intentar Actualizado');
                
            }

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function verOrdenModal(obj){
    // let id_requerimiento = obj.dataset.idRequerimiento;
    let codigo = obj.dataset.codigoOrdenCompra;
    let id_orden = obj.dataset.idOrdenCompra;
    let id_estado_actual = obj.dataset.idEstadoOrdenCompra;
    // console.log(id_orden);

    $('#modal-ver-orden').modal({
        show: true,
        backdrop: 'true'
    });

    ver_orden(id_orden)
}

function llenarCabeceraOrde(data){
    // console.log(data);
    document.querySelector("span[id='inputCodigo']").textContent = data.codigo_softlink;
    document.querySelector("p[id='inputProveedor']").textContent = data.razon_social+' RUC: '+data.nro_documento;
    document.querySelector("p[id='inputFecha']").textContent = data.fecha;
    document.querySelector("p[id='inputMoneda']").textContent = data.simbolo;
    document.querySelector("p[id='inputCondicion']").textContent = data.condicion+' '+data.plazo_dias+' días';
    document.querySelector("p[id='inputPlazoEntrega']").textContent = data.plazo_entrega;
    document.querySelector("p[id='inputEstado']").innerHTML = `<span class="label label-${data.bootstrap_color}" id="estado_orden" onClick="editarEstadoOrden(this);" data-id-estado-orden-compra="${data.id_estado}" data-id-orden-compra="${data.id_orden_compra}" data-codigo-orden-compra="${data.codigo_softlink}" style="cursor: pointer;" title="Cambiar Estado de Orden">${data.estado_doc}</span>`
}

function ver_orden(id_orden){

    $.ajax({
        type: 'GET',
        url: 'ver-orden/'+id_orden,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if (response.status ==200){
                llenarCabeceraOrde(response.data.orden);
                llenarTablaItemsOrden(response.data.detalle_orden);
            }else{
                alert("sin data");
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });


}

function llenarTablaItemsOrden(data){
    var vardataTables = funcDatatables();
    $('#tablaItemOrdenCompra').dataTable({
        bDestroy: true,
        order: [[0, 'asc']],
        info:     true,
        iDisplayLength:2,
        paging:   true,
        searching: false,
        language: vardataTables[0],
        processing: true,
        bDestroy: true,
        data:data ,
        columns: [
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
 
            { data: 'codigo_item' },
            { data: 'part_number' },
            { data: 'categoria' },
            { data: 'subcategoria' },
            { data: 'descripcion' },
            { data: 'unidad_medida' },
            { data: 'cantidad' },
            { data: 'precio_referencial' },
            { data: 'subtotal' },
            {'render':
                function (data, type, row, meta){
                    return `<span class="label label-${row.bootstrap_color_estado_detalle_orden}" onClick="editarEstadoItemOrden(this);" data-id-estado-detalle-orden-compra="${row.id_estado_detalle_orden}" data-id-orden-compra="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_item}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.estado_detalle_orden}</span>`;

                }
            }
        ],

    })

    let tablelistaitem = document.getElementById('tablaItemOrdenCompra_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}


function eliminarAtencionOrdenRequerimiento(obj){
    let codigo_requerimiento = obj.dataset.codigoRequerimiento;
    let id_requerimiento = obj.dataset.idRequerimiento;
    let id_orden = obj.dataset.idOrdenCompra;
    // console.log(id_requerimiento,id_orden);
    var ask = confirm('¿Desea revertir el requerimiento '+codigo_requerimiento+'?');
    if (ask == true){
        $.ajax({
            type: 'PUT',
            url: rutaRevertirOrdenPorRequerimiento+'/'+id_orden+'/'+id_requerimiento,
            beforeSend: function(){
            },
            success: function(response){
                // console.log(response);                
                if (response.status == 200) {
                    alert('Se revertió la orden y restablecio el estado del requerimiento');
                    $('#listaComprasEnProceso').DataTable().ajax.reload();
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


// function openModalOrdenRequerimiento(obj){
//     // console.log(obj.dataset.idRequerimiento);   
 
//     $('#modal-orden-requerimiento').modal({
//         show: true,
//         backdrop: 'static'
//     });

//     cleanFormModalOrdenRequerimiento();
//     obtenerRequerimiento(obj.dataset.idRequerimiento);
// }

function cleanFormModalOrdenRequerimiento(){
    document.querySelector("form[id='form-orden-requerimiento'] input[name='codigo_orden']").value = '';
    document.querySelector("form[id='form-orden-requerimiento'] input[name='razon_social']").value = '';
    document.querySelector("form[id='form-orden-requerimiento'] input[name='id_proveedor']").value = '';
    document.querySelector("form[id='form-orden-requerimiento'] input[name='id_contrib']").value = '';
    $('#listaDetalleOrden').DataTable().clear();

}

function obtenerRequerimiento(reqTrueList){
// console.log(reqTrueList);

    $.ajax({
        type: 'POST',
        url: rutaDetalleRequerimientoOrden,
        data:{'requerimientoList':reqTrueList},
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            detalleRequerimientoSelected=response.det_req;
            listar_detalle_orden_requerimiento(response.det_req);
            // console.log(response.det_req); 
            // document.querySelector("div[id='modal-orden-requerimiento'] span[id='codigo_requeriento_seleccionado']").textContent= ' - Requerimiento: '+ response.requerimiento.codigo;
            // document.querySelector("div[id='modal-orden-requerimiento'] input[name='id_requerimiento']").value= response.requerimiento.id_requerimiento;
            // document.querySelector("div[id='modal-orden-requerimiento'] select[name='sede']").value= response.requerimiento.id_sede;
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function agregarItemsWithChecked(){
    var itemsChecked =[]; 
    let tableListaDetalleOrden = document.getElementById('listaDetalleOrden');
    let tableChildren = tableListaDetalleOrden.children[1].children;
    let sizeTableChildren = tableChildren.length;
    for(i=0;i<sizeTableChildren;i++){
        if(tableChildren[i].cells.length >0){
            if(tableChildren[i].cells[0].children[0].checked == true){
                itemsChecked.push( {
                'id_detalle_requerimiento':tableChildren[i].cells[0].children[0].dataset.idDetalleRequerimiento
            });
            }
        };
    }

    return itemsChecked;
}
function hasNullCantidadAComprar(){
    let sizeInputCantidadAComprar = document.querySelectorAll("input[name='cantidad_a_comprar']").length;
    let cantidadVacios=0;
    let hasNull=false;
    for (let index = 0; index < sizeInputCantidadAComprar; index++) {
        let inputCantidadAComprar = document.querySelectorAll("input[name='cantidad_a_comprar']")[index];
        if(inputCantidadAComprar.value == '0' || inputCantidadAComprar.value == '' || inputCantidadAComprar == null){
            cantidadVacios+=1;
        }
        // console.log(inputCantidadAComprar);
    }

    if(cantidadVacios >0){
        hasNull=true;
    }

    return hasNull;
}

function hasCheckedGuardarEnRequerimiento(){
    let hasCheck = document.querySelector("input[name='guardarEnRequerimiento']").checked;
    return hasCheck;
}

function countRequirementsInObj(){
    // console.log(listCheckReq);
    let idRequerimientoList=[];
    let size=0;
    listCheckReq.forEach(element => {
        if(element.stateCheck ==true){
            idRequerimientoList.push(element.id_req);
        } 
    });
    let idRequerimientoListUnique = Array.from(new Set(idRequerimientoList));
    // console.log(idRequerimientoList);
    // console.log(idRequerimientoListUnique);
    size = idRequerimientoListUnique.length;
    return size;
}
function fillListaItemsRequerimientosVinculados(){
    // console.log(listCheckReq);
    let data=detalleRequerimientoSelected.filter(item => item.id > 0)
    console.log(data);
    var vardataTables1 = funcDatatables();
    $('#listaItemsRequerimientosVinculados').dataTable({
 
        'paging':true,
        'info': true,
        'iDisplayLength': 10,
        'language' : vardataTables1[0],
        'processing': true,
        'searching':false,
        "bDestroy": true,
        'data':data,
        'columns': [
            {'render':
                function (data, type, row, meta){
                    return row.id;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.codigo_item;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.part_number;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.descripcion;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.unidad_medida;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.cantidad_a_comprar;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return row.precio_referencial;
                }
            },
            {'render':
                function (data, type, row, meta){
                    return selectRequirementsToLink;
                }
            }
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'}
        ],
        'order': [
            [0, 'asc']
        ]

    });

    let tablelistaitem = document.getElementById('listaItemsRequerimientosVinculados_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;

}
function getDetalleYRequerimientoOrden(idReqList){

    return new Promise(function(resolve, reject) {
    $.ajax({
        type: 'POST',
        data:{'requerimientoList':idReqList},
        url:rutaDetalleRequerimientoOrden,
        dataType: 'JSON',
        success(response) {
            resolve(response) // Resolve promise and go to then() 
        },
        error: function(err) {
        reject(err) // Reject the promise and go to catch()
        }
        });
    });
}

function margeObjArrayToDetalleReqSelected(){
    let countChanges=0;
    detalleRequerimientoSelected.forEach(drs => {
        linksToReqObjArray.forEach(ltr => {
            if(drs.id==ltr.id_nuevo_item){
                countChanges+=1;
                drs.id_requerimiento= ltr.id_requerimiento;
            }
        });
    });
    console.log(detalleRequerimientoSelected);
    if(countChanges>0){
        alert(`se vinculo con exito ${countChanges} item(s)`);
        $('#modal-vincular-item-requerimiento').modal('hide');
        linksToReqObjArray=[];
        payload_orden = '&detalle_requerimiento='+JSON.stringify(detalleRequerimientoSelected);
        sendDataToSaveOrden(payload_orden);

    }else{
        alert('hubo un error al intentar vincular el item con el requerimiento');
        linksToReqObjArray=[];
    }

}

function makeLinktoReq(obj){

    let id_requerimiento = obj.value;
    let id_nuevo_item = obj.parentNode.parentNode.children[0].textContent;
    let linksToReqObj={
        'id_requerimiento':id_requerimiento,
        'id_nuevo_item':id_nuevo_item
    }
    linksToReqObjArray.push(linksToReqObj);
    // console.log(detalleRequerimientoSelected);
    // console.log(linksToReqObjArray);
}

function buildSelectOfRequirements(data){
    let html=`<select class="form-control" name="requerimientos" onChange="makeLinktoReq(this);">
                <option value="0">selecciona un requerimiento</option>`;
    data.forEach(element => {
        html +=`
            <option value="${element.id_requerimiento}">${element.codigo}</option>`;
    });
    html+='</select>';
    selectRequirementsToLink = html;
}

function fillListaRequerimientosVinculados(){

    let ReqListOnlyTrue=listCheckReq.filter(field => field.stateCheck == true);
    let idReqList = ReqListOnlyTrue.map(function (element, index, array) {
        return element.id_req; 
    });
    // console.log(idReqList);
    getDetalleYRequerimientoOrden(idReqList).then(function(data) {
        // Run this when your request was successful
        console.log(data)
        if(data.requerimiento.length >0){
            llenarTablaListaRequerimientosVinculados(data.requerimiento);
            buildSelectOfRequirements(data.requerimiento);
            fillListaItemsRequerimientosVinculados();
        }

    }).catch(function(err) {
        // Run this when promise was rejected via reject()
        console.log(err)
    })

}

function llenarTablaListaRequerimientosVinculados(data){
    var vardataTables1 = funcDatatables();
    $('#listaRequerimientosVinculados').DataTable({
        'paging':false,
        'info': false,
        'language' : vardataTables1[0],
        'processing': true,
        'searching':false,
        "bDestroy": true,
        "data" :  data,
        'columns': [
            {'render':
                function (data, type, row, meta){
                    return meta.row +1;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.codigo;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.concepto;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.tipo_requerimiento;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.nombre_persona?row.nombre_persona:row.cliente_razon_social;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.razon_social_empresa;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.codigo_sede_empresa;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.usuario;
                }
            },
            {'render':
                function (data, type, row, meta){
                    
                    return row.fecha_requerimiento;
                }
            }
 
        ],
        'order': [
            [0, 'asc']
        ]

    });

    let tablelistaitem = document.getElementById('listaRequerimientosVinculados_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}

$("#form-orden-requerimiento").on("submit", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    var detalle_requerimiento = [];
    payload_orden = data;
    // console.log(detalleRequerimientoSelected);
    let hasCheck = hasCheckedGuardarEnRequerimiento();
    if(hasCheck == true){
        let coutReqInObj =countRequirementsInObj();
        if(coutReqInObj == 1){
            // console.log(listCheckReq);
            // console.log(detalleRequerimientoSelected);
            // vincultar item con req unico
            let id_req = listCheckReq[0].id_req;
            detalleRequerimientoSelected.forEach(drs => {
                if(drs.id>0){
                    drs.id_requerimiento= id_req;
                }
            });

            payload_orden += '&detalle_requerimiento='+JSON.stringify(detalleRequerimientoSelected);
            sendDataToSaveOrden(payload_orden);

        }else if(coutReqInObj >1){
            // console.log('open modal to select item/req');
            $('#modal-vincular-item-requerimiento').modal({
                show: true,
                backdrop: 'static'
            });
            fillListaRequerimientosVinculados();

            
        }else{ //no existen nuevos item argregados, guardar nromal (no habra que guardar en req)
            payload_orden += '&detalle_requerimiento='+JSON.stringify(detalleRequerimientoSelected);
            sendDataToSaveOrden(payload_orden);
    
        }
    }else{ // sin guardar en req
        payload_orden += '&detalle_requerimiento='+JSON.stringify(detalleRequerimientoSelected);
        sendDataToSaveOrden(payload_orden);
    }

    // let itemsChecked=[];
    // itemsChecked = agregarItemsWithChecked();
    // console.log('detalleRequerimientoSelected');
    // console.log(detalleRequerimientoSelected);
    // console.log('itemsChecked');
    // console.log(itemsChecked);
    // if(itemsChecked.length > 0){
    //     detalleRequerimientoSelected.forEach(elementDetReq => {
    //         itemsChecked.forEach(elementChecked => {
    //             if(elementDetReq.id_detalle_requerimiento == elementChecked.id_detalle_requerimiento){
    //                 detalle_requerimiento.push(elementDetReq);
    //             }
    //         })
    //     });
    // }else{
    //     detalle_requerimiento= detalleRequerimientoSelected;
    // }

    // var payload = data+'&detalle_requerimiento='+JSON.stringify(detalleRequerimientoSelected);
  
    // console.log(detalleRequerimientoSelected);




});

function sendDataToSaveOrden(payload){
    console.log(payload);
    let hasNull = hasNullCantidadAComprar();

    if(hasNull ==true){
        var ask = confirm('Tiene seleccionado producto(s) con cantidad a comprar igual a cero/vacio. ¿desea continuar?');
        if (ask == true){
        guardar_orden_requerimiento(payload);
    payload_orden =[];
    }else{
            return false;
        } 
    }else{
        guardar_orden_requerimiento(payload);
    }
}



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
                // console.log(response);
                if (response > 0){
                    alert('Orden de registrada con éxito');
                    $('#modal-orden-requerimiento').modal('hide');
                    $('#listaRequerimientosPendientes').DataTable().ajax.reload();
                    // $('#listaComprasEnProceso').DataTable().ajax.reload();
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
    // console.log(data);
    var vardataTables = funcDatatables();
    $('#listaDetalleOrden').DataTable({
        bDestroy: true,
        order: [[0, 'asc']],
        info:     false,
        scrollY: '30vh',
        scrollCollapse: true,
        paging:   false,
        searching: false,
        language: vardataTables[0],
        processing: true,
        bDestroy: true,
        data:data,
        columns: [
            {'render':
                function (data, type, row, meta){
                    if(row.estado == 7){
                        return '<input type="checkbox" checked data-id-detalle-requerimiento="' + row.id_detalle_requerimiento + '"  disabled />';
                    }else{
                        return '<input type="checkbox" checked data-id-detalle-requerimiento="' + row.id_detalle_requerimiento + '"  />';

                    }
                }, 'name':'checkbox'
            },
            {'render':
                function (data, type, row, meta){
                    return row.codigo_requerimiento;
                }, 'name':'codigo_requerimiento'
            },
            {'render':
                function (data, type, row, meta){
                    return row.codigo_item;
                }, 'name':'codigo_item'
            },
            {'render':
                function (data, type, row, meta){
                    return row.descripcion_adicional;
                }, 'name':'descripcion_adicional'
            },
            {'render':
                function (data, type, row, meta){
                    return row.unidad_medida;
                }, 'name':'unidad_medida'
            },            
            {'render':
                function (data, type, row, meta){
                    if(row.estado ==7){
                        return '<input type="text" class="form-control" name="cantidad" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+row.cantidad+'" onChange="updateInputCantidad(event);" style="width: 70px;" disabled/>';
                    }else{
                        return '<input type="text" class="form-control" name="cantidad" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+row.cantidad+'" onChange="updateInputCantidad(event);" style="width: 70px;"/>';
                    }
                }, 'name':'cantidad'
            },
            {'render':
                function (data, type, row, meta){
                    if(row.estado ==7){
                        return '<input type="text" class="form-control" name="precio" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_referencial?row.precio_referencial:"")+'" onChange="updateInputPrecio(event);" style="width:70px;" disabled/>';
                    }else{
                        return '<input type="text" class="form-control" name="precio" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="'+(row.precio_referencial?row.precio_referencial:"")+'" onChange="updateInputPrecio(event);" style="width:70px;"/>';
                    }
                } , 'name':'precio'
            },
            {'render':
                function (data, type, row, meta){
                    if(row.estado == 7){
                        return '<input type="text" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="updateInputStockComprometido(event);" onfocusin ="updateInputStockComprometido(event);" style="width: 70px;" disabled />';
                    }else{
                        return '<input type="text" class="form-control" name="stock_comprometido" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'" value="0" onkeyup ="updateInputStockComprometido(event);" onfocusin ="updateInputStockComprometido(event);" style="width: 70px;"/>';
                    }
                }, 'name':'stock_comprometido'
            },
            {'render':
                function (data, type, row, meta){
                    if(row.estado == 7){
                        return '<input type="text" class="form-control" name="cantidad_a_comprar" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="updateInputCantidadAComprar(event);" value="'+row.cantidad_a_comprar+'" style="width:70px;" disabled />';
                    }else{
                        return '<input type="text" class="form-control" name="cantidad_a_comprar" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"   onchange="updateInputCantidadAComprar(event);" value="'+row.cantidad_a_comprar+'" style="width:70px;"/>';
                    }
                } , 'name':'cantidad_a_comprar'
            },
            {'render':
                function (data, type, row, meta){
                    return '<div name="subtotal" data-row="'+(meta.row+1)+'" data-id_requerimiento="'+(row.id_requerimiento?row.id_requerimiento:0)+'" data-id_detalle_requerimiento="'+(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)+'"></div>';
                } , 'name':'subtotal'
            },
            {'render':
                function (data, type, row, meta){
                    let action ='';
                    if(row.estado ==7){
                        action = `
                        <div class="btn-group btn-group-sm" role="group" style="cursor: default;">
                            <i class="fas fa-sticky-note fa-2x" style="color:orange" title="${(row.observacion?row.observacion:'Sin Observación')}" ></i>
                        </div>
                        `;
                    }else{
                        action = `
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-danger btn-sm" name="btnOpenModalEliminarItemOrden" title="Eliminar Item" data-row="${(meta.row+1)}" data-id_requerimiento="${(row.id_requerimiento?row.id_requerimiento:0)}" data-id_detalle_requerimiento="${(row.id_detalle_requerimiento?row.id_detalle_requerimiento:0)}"  onclick="openModalEliminarItemOrden(this);">
                            <i class="fas fa-trash fa-sm"></i>
                            </button>
                        </div>
                        `;
                    }
                    return action;
                }
            }
        ],
        rowCallback: function( row, data ) {
            if ( data.estado == '7' )
            { 
                $('td', row).css({'background-color': 'mistyrose', 'color': 'indianred'});
            }
        },
        columnDefs: [
            { width: '10px', targets: 0 },
            { width: '20px', targets: 1 },
            { width: '20px', targets: 2 },
            { width: '40px', targets: 3 },
            { width: '50px', targets: 4 },
            { width: '20px', targets: 5 },
            { width: '20px', targets: 6 },
            { width: '20px', targets: 7 , sClass: 'invisible'},
            { width: '20px', targets: 8 },
            { width: '20px', targets: 9 },
            { width: '30px', targets: 10, sClass:'text-center' }
        ],
    
        order: [[1, "asc"]]


    });

    let tablelistaitem = document.getElementById('listaDetalleOrden_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;


}

function eliminarItemSinMotivo(obj){
    // let codigoItemSelected=obj.parentNode.parentNode.parentNode.childNodes[2].textContent;
    // let descripcionItemSelected=obj.parentNode.parentNode.parentNode.childNodes[3].textContent;
    let rowNumber = obj.dataset.row;
    // let idRequerimientoSelected = obj.dataset.id_requerimiento;
    let idDetalleRequerimiento = obj.dataset.id_detalle_requerimiento
    eliminarItemDeObj(rowNumber,idDetalleRequerimiento);
    afectarEstadoEliminadoFilaTablaListaDetalleOrden(rowNumber,'Error de Ingreso');


}

function openModalEliminarItemOrden(obj){
    let codigoItemSelected=obj.parentNode.parentNode.parentNode.childNodes[2].textContent;
    let descripcionItemSelected=obj.parentNode.parentNode.parentNode.childNodes[3].textContent;
    let rowNumber = obj.dataset.row;
    let idRequerimientoSelected = obj.dataset.id_requerimiento;
    let idDetalleRequerimiento = obj.dataset.id_detalle_requerimiento

    if(idDetalleRequerimiento > 0){
        $('#modal-confirmar-eliminar-item').modal({
            show: true,
            backdrop: 'true',
            keyboard: true
    
        });
        document.querySelector("span[id='codigo_descripcion_item']").textContent= `${codigoItemSelected} ${descripcionItemSelected}`;
        document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='codigo_item']").textContent= `${codigoItemSelected}`;
        document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='descripcion_item']").textContent= `${descripcionItemSelected}`;
        document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='row']").textContent= `${rowNumber}`;
        document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='id_requerimiento']").textContent= `${idRequerimientoSelected}`;
        document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='id_detalle_requerimiento']").textContent= `${idDetalleRequerimiento}`;
    
    }else{
        eliminarItemSinMotivo(obj);
    }
    console.log(detalleRequerimientoSelected);


}

function updateEstadoDetalleRequerimiento(id_detalle_requerimiento,estado){

    return new Promise(function(resolve, reject) {
    $.ajax({
        type: 'PUT',
        url:rutaActualizarEstadoDetalleRequerimiento+'/'+id_detalle_requerimiento+'/'+estado,
        dataType: 'JSON',
        success(response) {
            resolve(response) // Resolve promise and go to then() 
        },
        error: function(err) {
        reject(err) // Reject the promise and go to catch()
        }
        });
    });
}


function eliminarItemOrden(){
    let codigoItemSelected= document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='codigo_item']").textContent;
    let descripcionItemSelected= document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='descripcion_item']").textContent;
    let rowSelected =document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='row']").textContent;
    let idRequerimientoSelected = document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='id_requerimiento']").textContent;
    let idDetalleRequerimientoSelected = document.querySelector("div[id='modal-confirmar-eliminar-item'] div[class='modal-footer'] label[id='id_detalle_requerimiento']").textContent;
    let motivo = document.querySelector("div[id='modal-confirmar-eliminar-item'] textarea[name='motivo']").value;

    var ask = confirm('Esta seguro que quiere anular el item '+codigoItemSelected+' '+descripcionItemSelected+' del requerimiento ?');
    if (ask == true){

        if(idDetalleRequerimientoSelected > 0){
        // 
            updateEstadoDetalleRequerimiento(idDetalleRequerimientoSelected,7).then(function(data) {
                // Run this when your request was successful
                console.log(data)
                if(data.status ==200){
                    eliminarItemDeObj(rowSelected,idDetalleRequerimientoSelected);
                    afectarEstadoEliminadoFilaTablaListaDetalleOrden(rowSelected,motivo);
                    alert('se anulo el item en el requerimiento');
                }
        
            }).catch(function(err) {
                // Run this when promise was rejected via reject()
                console.log(err)
            })
        // 
        }else{
            eliminarItemDeObj(rowSelected,idDetalleRequerimientoSelected);
            afectarEstadoEliminadoFilaTablaListaDetalleOrden(rowSelected,motivo);
        }

        
    }else{
        return false;
    }
}

function eliminarItemDeObj(rowSelected,idDetalleRequerimientoSelected){

    if(idDetalleRequerimientoSelected >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_detalle_requerimiento == idDetalleRequerimientoSelected){
            detalleRequerimientoSelected.splice( index, 1 );
            }
        });
    }else if(rowSelected >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id == rowSelected){
                detalleRequerimientoSelected.splice( index, 1 );
            }
        });
    }
    console.log(detalleRequerimientoSelected);

}

function afectarEstadoEliminadoFilaTablaListaDetalleOrden(rowSelected,motivo){
    let sizeTableListaDetalleOrden = document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children.length;
    for (let index = 0; index < sizeTableListaDetalleOrden; index++) {
        let row = document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[10].children[0].children[0].dataset.row;
        if(row ==rowSelected){
            $('#modal-confirmar-eliminar-item').modal('hide');
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[0].children[0].disabled = true;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[5].children[0].disabled = true;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[6].children[0].disabled = true;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[7].children[0].disabled = true;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[8].children[0].disabled = true;

            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[10].children[0].children[0].disabled = true;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[10].children[0].children[0].remove();
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].cells[10].children[0].innerHTML=`
            <div class="btn-group btn-group-sm" role="group" style="cursor: default;">
                <i class="fas fa-sticky-note fa-2x" style="color:orange" title="${(motivo?motivo:'Sin Observación')}" ></i>
            </div>
            `;
            document.querySelector("table[id='listaDetalleOrden']").tBodies[0].children[index].setAttribute("style","background:mistyrose; color:indianred;");
        }
        
    }
}

function updateInputCantidad(event){
    // console.log(detalleRequerimientoSelected);
    let nuevoValor =event.target.value;
    let rowNumber= event.target.dataset.row;
    let idRequerimientoSelected= event.target.dataset.id_requerimiento;
    let idDetalleRequerimientoSelected = event.target.dataset.id_detalle_requerimiento;
 
    updateInObjCantidad(rowNumber,idRequerimientoSelected,idDetalleRequerimientoSelected,nuevoValor);
    calcTotalDetalleRequerimiento(idDetalleRequerimientoSelected,rowNumber);

    // console.log(detalleRequerimientoSelected);
}
function updateInputCantidadAComprar(event){
    let nuevoValor =event.target.value;
    let idRequerimientoSelected= event.target.dataset.id_requerimiento;
    let idDetalleRequerimientoSelected = event.target.dataset.id_detalle_requerimiento;
    let rowNumberSelected = event.target.dataset.row;
    let sizeInputCantidad = document.querySelectorAll("input[name='cantidad']").length;
    let cantidad =0;
    for (let index = 0; index < sizeInputCantidad; index++) {
        let row = document.querySelectorAll("input[name='cantidad']")[index].dataset.row;
        if(row == rowNumberSelected){
            cantidad = document.querySelectorAll("input[name='cantidad']")[index].value;
            if(parseFloat(nuevoValor) <= parseFloat(cantidad)){                
                // actualizar datadetreq cantidad
                updateInObjCantidadAComprar(rowNumberSelected,idRequerimientoSelected,idDetalleRequerimientoSelected,nuevoValor);
                calcTotalDetalleRequerimiento(idDetalleRequerimientoSelected,rowNumberSelected);

                console.log(detalleRequerimientoSelected);
                // 
            }
            
            if(parseFloat(nuevoValor) > parseFloat(cantidad)){
                console.log(nuevoValor);
                console.log(cantidad);
                alert("La cantidad a comprar no puede ser mayor a la cantidad `solicitada");
                document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value= cantidad;

            }
        }
    }
}
// function updateInputStockComprometido(event){
//     // console.log(detalleRequerimientoSelected);
//     let nuevoValor =event.target.value;
//     let rowNumber = event.target.dataset.row;
//     let idRequerimientoSelected= event.target.dataset.id_requerimiento;
//     let idDetalleRequerimientoSelected = event.target.dataset.id_detalle_requerimiento;


//     let sizeInputCantidadAComprar = document.querySelectorAll("input[name='cantidad_a_comprar']").length;
//     for (let index = 0; index < sizeInputCantidadAComprar; index++) {
//         let idDetReq = document.querySelectorAll("input[name='cantidad_a_comprar']")[index].dataset.id_detalle_requerimiento;
//         if(idDetReq == idDetalleRequerimientoSelected){
//             let cantidad = document.querySelectorAll("input[name='cantidad']")[index].value;
//             let stock_comprometido = document.querySelectorAll("input[name='stock_comprometido']")[index].value;
//             // console.log(cantidad);
//             // console.log(stock_comprometido);
//             if(stock_comprometido == cantidad ){
//                 updateInObjStockComprometido(rowNumber,idRequerimientoSelected,idDetalleRequerimientoSelected,nuevoValor);
//                 document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value= 0;
//                 updateInObjCantidadAComprar(rowNumber,idRequerimientoSelected,idDetalleRequerimientoSelected, 0);
//             }
//             if(stock_comprometido < cantidad ){
//                 updateInObjStockComprometido(rowNumber,idRequerimientoSelected,idDetalleRequerimientoSelected,nuevoValor);
//                 // document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value= parseInt(cantidad - stock_comprometido);
//                 updateInObjCantidadAComprar(rowNumber,idRequerimientoSelected,idDetalleRequerimientoSelected, parseInt(cantidad - stock_comprometido));

//             }
//             if(stock_comprometido > cantidad){
//                 updateInObjStockComprometido(rowNumber,idRequerimientoSelected,idDetalleRequerimientoSelected, cantidad);
//                 alert("El Stock Comprometido no puede ser mayor a la cantidad");
//                 document.querySelectorAll("input[name='stock_comprometido']")[index].value= cantidad;

//                 document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value= 0;
//                 updateInObjCantidadAComprar(rowNumber,idRequerimientoSelected,idDetalleRequerimientoSelected,0);

//             }
//         }
//     }

    

//     calcTotalDetalleRequerimiento(idDetalleRequerimientoSelected,rowNumber);

//     // console.log(detalleRequerimientoSelected);
// }


function updateInputPrecio(event){
    // console.log(detalleRequerimientoSelected);
    let nuevoValor =event.target.value;
    let idRequerimientoSelected= event.target.dataset.id_requerimiento;
    let idDetalleRequerimientoSelected = event.target.dataset.id_detalle_requerimiento;
    let rowNumber = event.target.dataset.row;
    updateInObjPrecioReferencial(rowNumber,idRequerimientoSelected,idDetalleRequerimientoSelected,nuevoValor);

    calcTotalDetalleRequerimiento(idDetalleRequerimientoSelected,rowNumber);

    // console.log(detalleRequerimientoSelected);
}

function calcTotalDetalleRequerimiento(idDetalleRequerimientoSelected,rowNumberSelected){
    let sizeInputTotal = document.querySelectorAll("div[name='subtotal']").length;
    for (let index = 0; index < sizeInputTotal; index++) {
        let rowNumber = document.querySelectorAll("div[name='subtotal']")[index].dataset.row;
        let idReq = document.querySelectorAll("div[name='subtotal']")[index].dataset.id_requerimiento;
        if(rowNumber == rowNumberSelected){
            let precio = document.querySelectorAll("input[name='precio']")[index].value?document.querySelectorAll("input[name='precio']")[index].value:0;
            let cantidad =( document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value)>0?document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value:document.querySelectorAll("input[name='cantidad']")[index].value;
            let subtotal = (parseFloat(precio) * parseFloat(cantidad)).toFixed(2);
            document.querySelectorAll("div[name='subtotal']")[index].textContent=subtotal;
            updateInObjSubtotal(rowNumberSelected,idReq,idDetalleRequerimientoSelected,subtotal);
        }
    }
    let total =0;
    let simbolo_moneda_selected = document.querySelector("select[name='id_moneda']")[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda;
    for (let index = 0; index < sizeInputTotal; index++) {
        let num = document.querySelectorAll("div[name='subtotal']")[index].textContent?document.querySelectorAll("div[name='subtotal']")[index].textContent:0;
        total += parseFloat(num);
    }
    // console.log(total);
    document.querySelector("var[name='total']").textContent= (simbolo_moneda_selected) + (total);


    
}


function updateInObjCantidad(rowNumber,idReq,idDetReq,valor){
    if(idReq >0 && idDetReq >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_requerimiento == idReq){
                if(element.id_detalle_requerimiento == idDetReq){
                detalleRequerimientoSelected[index].cantidad = valor;
                }
            }
        });
    }
    if(idReq ==0 && idDetReq ==0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id == rowNumber){
                detalleRequerimientoSelected[index].cantidad = valor;
                
            }
        });
    }

}

function updateInObjPrecioReferencial(rowNumber,idReq,idDetReq,valor){
    if(idReq >0 && idDetReq >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_requerimiento == idReq){
                if(element.id_detalle_requerimiento == idDetReq){
                detalleRequerimientoSelected[index].precio_referencial = valor;
                }
            }
        });
    }
    if(idReq ==0 && idDetReq ==0){
        detalleRequerimientoSelected.forEach((element,index) => {
            console.log(element.id);
            console.log(rowNumber);
            if(element.id == rowNumber){
                detalleRequerimientoSelected[index].precio_referencial = valor;
                
            }
        });
    }

}

function updateInObjCantidadAComprar(rowNumber, idReq,idDetReq,valor){
    if(idReq >0 && idDetReq >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_requerimiento == idReq){
                if(element.id_detalle_requerimiento == idDetReq){
                detalleRequerimientoSelected[index].cantidad_a_comprar = valor;
                }
            }
        });
    }

    if(idReq ==0 && idDetReq ==0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id == rowNumber){
                detalleRequerimientoSelected[index].cantidad_a_comprar = valor;
                
            }
        });
    }
    console.log(detalleRequerimientoSelected);
}

function updateInObjStockComprometido(rowNumber,idReq,idDetReq,valor){
    if(idReq >0 && idDetReq >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_requerimiento == idReq){
                if(element.id_detalle_requerimiento == idDetReq){
                detalleRequerimientoSelected[index].stock_comprometido = valor;
                }
            }
        });
    }
    if(idReq ==0 && idDetReq ==0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id == rowNumber){
                detalleRequerimientoSelected[index].stock_comprometido = valor;
                
            }
        });
    }
}

function updateInObjSubtotal(rowNumber,idReq,idDetReq,valor){
    if(idReq >0 && idDetReq >0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id_requerimiento == idReq){
                if(element.id_detalle_requerimiento == idDetReq){
                detalleRequerimientoSelected[index].subtotal = valor;
                }
            }
        });
    }
    if(idReq ==0 && idDetReq ==0){
        detalleRequerimientoSelected.forEach((element,index) => {
            if(element.id == rowNumber){
                detalleRequerimientoSelected[index].subtotal = valor;
                
            }
        });
    }
}

 

function agregarNuevoItem(){
    $('#modal-catalogo-items').modal({
        show: true,
        backdrop: 'true',
        keyboard: true

    });
    listarItems();

}


function listarItems() {
    var vardataTables = funcDatatables();
    $('#listaItems').dataTable({
        // 'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        // "dom": '<"toolbar">frtip',

        'scrollY': '30vh',
        'scrollCollapse': true,
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        "scrollX": true,
        'ajax': '/logistica/mostrar_items',
        'columns': [
            {'data': 'id_item'},
            {'data': 'id_producto'},
            {'data': 'id_servicio'},
            {'data': 'id_equipo'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'categoria'},
            {'data': 'subcategoria'},
            {'data': 'descripcion'},
            {'data': 'unidad_medida_descripcion'},
            {'data': 'id_unidad_medida'},
            {'render':
                function (data, type, row){
                    if(row.id_unidad_medida == 1){
                        return ('<button class="btn btn-sm btn-info" onClick="verSaldoProducto('+row.id_producto+ ');">Stock</button>');
                    }else{ 
                        return '';
                    }

                }
            }
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},
            { 'aTargets': [1], 'sClass': 'invisible'},
            { 'aTargets': [2], 'sClass': 'invisible'},
            { 'aTargets': [3], 'sClass': 'invisible'},
            { 'aTargets': [10], 'sClass': 'invisible'}
                    ],
        'order': [
            [8, 'asc']
        ],
        initComplete: function( settings, json ) {
            // console.log('data cargada');
        }
        
    });
    let tablelistaitem = document.getElementById(
        'listaItems_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;

}

function selectItem(){
 
    var id_item = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_item']").textContent;
    var codigo_item = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='codigo']").textContent;
    var part_number = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='part_number']").textContent;
    var id_producto = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent;
    var id_servicio = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_servicio']").textContent;
    var id_equipo = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_equipo']").textContent;
    var descripcion_producto = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='descripcion']").textContent;
    var unidad_medida_item = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='unidad_medida']").textContent;
    var id_unidad_medida_item = document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_unidad_medida']").textContent;
    var newItem={};

    if(id_item.length >0){
        // console.log(detalleRequerimientoSelected);
   
        newItem= {
            'id': 0,
            'cantidad': 1,
            'cantidad_a_comprar': 1,
            'codigo_item': codigo_item,
            'codigo_requerimiento': null,
            'descripcion': descripcion_producto,
            'descripcion_adicional': descripcion_producto,
            'estado': 1,
            'fecha_entrega': null,
            'fecha_registro': new Date().toISOString().slice(0, 10),
            'id_detalle_requerimiento': 0,
            'id_item': id_item,
            'id_producto': id_producto,
            'id_requerimiento': 0,
            'id_tipo_item': 1,
            'id_unidad_medida': id_unidad_medida_item,
            'lugar_entrega': null,
            'obs': null,
            'part_number': part_number,
            'precio_referencial': 0,
            'stock_comprometido': 0,
            'subtotal': 0,
            'unidad_medida': unidad_medida_item
        };
    
        detalleRequerimientoSelected.push(newItem)
        agregarItemATablaListaDetalleOrden(newItem);
        document.querySelector("div[id='check-guarda_en_requerimiento']").setAttribute("style",'display:inline-block');
        $('#modal-catalogo-items').modal('hide');

    }else{
        alert('hubo un error, no existe un id_item');
    }
}


function agregarItemATablaListaDetalleOrden(newItem){
    // console.log(newItem);
    var table = $('#listaDetalleOrden').DataTable();
    
    var i= table.row.add({
        "checkbox":"",
        "codigo_requerimiento":"SIN CODIGO",
        "codigo_item":newItem.codigo_item,
        "descripcion_adicional": newItem.descripcion,
        "unidad_medida": newItem.unidad_medida,
        "cantidad": 1,
        "precio":"",
        "stock_comprometido":"",
        "cantidad_a_comprar":"",
        "subtotal":""

    }).draw();

    //actualizar id con number row a los nuevos item agregados
    let rowNumber = table.rows(i).nodes()[0].childNodes[5].children[0].dataset.row; 
    detalleRequerimientoSelected.forEach((element,index) => {
        if(element.id == 0){
            detalleRequerimientoSelected[index].id = parseInt(rowNumber);
            
        }
    });
    // 
    // console.log(table.rows(i).nodes()[0]);
    table.rows(i).nodes()[0].setAttribute("class",'text-success')
    table.rows(i).nodes()[0].setAttribute("style",'background:lightcyan')
    alert('Item '+newItem.codigo_item+' Agregado')
    
    console.log(detalleRequerimientoSelected);
    // table.rows(i).nodes()[0].childNodes[5].children[0].dataset.id_requerimiento=0;
    // table.rows(i).nodes()[0].childNodes[5].children[0].dataset.id_detalle_requerimiento=0;
}

function handleChangeFilterReqByEmpresa(e) {
    let id_empresa =e.target.value;
    getDataSelectSede(id_empresa);
    listar_requerimientos_pendientes(null,id_empresa,null);
}

function getDataSelectSede(id_empresa = null){
    if(id_empresa >0){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: rutaSedeByEmpresa+ '/' + id_empresa,
            dataType: 'JSON',
            success: function(response){
                llenarSelectSede(response);
            }
        });
    }
    return false;
}

function llenarSelectSede(array){

    let selectElement = document.querySelector("select[id='id_sede_select_req']");
    // console.log(tabId);
    // console.log(selector);
    // console.log(selectElement);
    
    if(selectElement.options.length>0){
        var i, L = selectElement.options.length - 1;
        for(i = L; i >= 0; i--) {
            selectElement.remove(i);
        }
    }

    array.forEach(element => {
        let option = document.createElement("option");
        option.text = element.descripcion;
        option.value = element.id_sede;
        selectElement.add(option);
    });

    // console.log(selectElement.value);
    let id_empresa = document.querySelector("select[id='id_empresa_select_req']").value;
    let id_sede= selectElement.value;
     listar_requerimientos_pendientes(null,id_empresa,id_sede);


}

function handleChangeIncluirSede(event){
    
    let selectEmpresa = document.querySelector("select[id='id_empresa_select_req']");
    let id_empresa = selectEmpresa.value;

    if(event.target.checked == true){
        document.querySelector("select[id='id_sede_select_req']").removeAttribute('disabled');
        getDataSelectSede(id_empresa);

    }else{
        document.querySelector("select[id='id_sede_select_req']").setAttribute('disabled',true);
        let selectElement = document.querySelector("select[id='id_sede_select_req']");
        var i, L = selectElement.options.length - 1;
        for(i = L; i >= 0; i--) {
            selectElement.remove(i);
        }
        listar_requerimientos_pendientes(null,id_empresa,null);

    }

}

function handleChangeFilterReqBySede(e){
    let id_sede =e.target.value;
    let id_empresa = document.querySelector("select[id='id_empresa_select_req']");
    listar_requerimientos_pendientes(null,id_empresa,id_sede);

    
}

function handlechangeCondicion(event){
    let condicion= document.querySelector("select[name='id_condicion']")
    let text_condicion = condicion.options[condicion.selectedIndex].text;
    if(text_condicion == 'Contado cash'){
        document.getElementsByName('plazo_dias')[0].value = null;
        document.getElementsByName('plazo_dias')[0].setAttribute('disabled','true');
    }else if(text_condicion =='Crédito'){
        document.getElementsByName('plazo_dias')[0].removeAttribute('disabled');

    }

}