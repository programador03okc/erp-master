
var rutaRequerimientosPendientes, 
rutaOrdenesEnProceso,
// rutaGuardarOrdenPorRequerimiento,
rutaRevertirOrdenPorRequerimiento,
rutaActualizarEstadoOrdenPorRequerimiento,
rutaActualizarEstadoDetalleOrdenPorRequerimiento,
rutaActualizarEstadoDetalleRequerimiento,
rutaSedeByEmpresa,
rutaDocumentosVinculadosOrden,
rutaTieneItemsParaCompra
;

var listCheckReq=[];
var detalleRequerimientoSelected = [];
var selectRequirementsToLink='';
var linksToReqObjArray=[];
var payload_orden=[];
var tempDetalleItemsParaCompraCC=[];
var detalleItemsParaCompraCCSelected ={};
var data_item_para_compra =[];

function inicializar(
    _rutaRequerimientosPendientes,
    _rutaOrdenesEnProceso,
    // _rutaGuardarOrdenPorRequerimiento,
    _rutaRevertirOrdenPorRequerimiento,
    _rutaActualizarEstadoOrdenPorRequerimiento,
    _rutaActualizarEstadoDetalleOrdenPorRequerimiento,
    _rutaActualizarEstadoDetalleRequerimiento,
    _rutaSedeByEmpresa,
    _rutaDocumentosVinculadosOrden,
    _rutaTieneItemsParaCompra
    ) {
    
    rutaRequerimientosPendientes = _rutaRequerimientosPendientes;
    rutaOrdenesEnProceso = _rutaOrdenesEnProceso;
    // rutaGuardarOrdenPorRequerimiento = _rutaGuardarOrdenPorRequerimiento;
    rutaRevertirOrdenPorRequerimiento = _rutaRevertirOrdenPorRequerimiento;
    rutaActualizarEstadoOrdenPorRequerimiento = _rutaActualizarEstadoOrdenPorRequerimiento;
    rutaActualizarEstadoDetalleOrdenPorRequerimiento = _rutaActualizarEstadoDetalleOrdenPorRequerimiento;
    rutaActualizarEstadoDetalleRequerimiento = _rutaActualizarEstadoDetalleRequerimiento;
    rutaSedeByEmpresa = _rutaSedeByEmpresa;
    rutaDocumentosVinculadosOrden = _rutaDocumentosVinculadosOrden;
    rutaTieneItemsParaCompra = _rutaTieneItemsParaCompra;

}
function tieneItemsParaCompra(requerimientoList) {
// console.log(requerimientoList);
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: 'POST',
            data:{'requerimientoList':requerimientoList},
            url:  rutaTieneItemsParaCompra,
            dataType: 'JSON',
            success(response) {
                resolve(response) // Resolve promise and go to then() 
            },
            error: function (err) {
                reject(err) // Reject the promise and go to catch()
            }
        });
    });
}

function openModalOrdenRequerimiento(){
    $('#modal-orden-requerimiento').modal({
        show: true,
        backdrop: 'true'
    });

    obtenerRequerimiento(reqTrueList);
}
function openModalItemsParaCompra(){
    $('#modal-agregar-items-para-compra').modal({
        show: true,
        backdrop: 'static'
    });

    obtenerListaItemsCuadroCostosPorIdRequerimiento(reqTrueList); //modal_items_para_compra.js

}

function openModalCrearOrdenCompra() {

    if (listCheckReq.length > 0) {
        listCheckReq.forEach(element => {
            if (element.stateCheck == true) {
                reqTrueList.push(element.id_req)
            }
        });


    tieneItemsParaCompra(reqTrueList).then(function (tieneItems) {
        // console.log(tieneItems);
        if(tieneItems == true){
            openModalOrdenRequerimiento();
        }else{
            openModalItemsParaCompra();
        }

    }).catch(function (err) {
        // Run this when promise was rejected via reject()
        console.log(err)
    })
        // obtenerRequerimiento(reqTrueList);
        // cleanFormModalOrdenRequerimiento();
        // console.log(reqTrueList);
    } else {
        alert("No existe Requerimiento seleccionado");
    }
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




function listarItems() {
    // console.log('listaItems');
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
        "initComplete": function(settings, json) {
            if(tempDetalleItemCCSelect.hasOwnProperty('descripcion')){
                if(tempDetalleItemCCSelect.descripcion.length >0){
                    $('#text-info-item-vinculado').attr('title',tempDetalleItemCCSelect.descripcion);
                    $('#text-info-item-vinculado').removeAttr('hidden');
                    $('#example_filter input').val(tempDetalleItemCCSelect.descripcion);
                    this.api().search(tempDetalleItemCCSelect.descripcion).draw();
                }
            }
          } 
    });

 

    let tablelistaitem = document.getElementById(
        'listaItems_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    
    let listaItems_filter = document.getElementById(
        'listaItems_filter'
    )
    listaItems_filter.querySelector("input[type='search']").style.width='100%';
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
    // console.log(data);
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
                    return ('<div class="btn-group btn-group-xs" role="group">'+
                    '<button type="button" class="btn btn-primary btn-xs" name="btnOpenModalVerDetalleRequeriento" title="Ver Detalle Requerimiento" data-id-requerimiento="'+row.id_requerimiento+'"  onclick="openModalVerDetalleRequerimiento(this);">'+
                        '<i class="far fa-eye fa-sm"></i>'+
                    '</button>'+
                    '<button type="button" class="btn btn-warning btn-xs" name="btnOpenModalDesbloquarRequeriento" title="Desbloquear Requerimiento" data-id-requerimiento="'+row.id_requerimiento+'"  onclick="openModalDesbloquearRequerimiento(this);">'+
                        '<i class="fas fa-unlock-alt fa-sm"></i>'+
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
                return `${row.concepto}`;
                }
            },
            { render: function (data, type, row) {     
                return `${row.razon_social_cliente}`;
                }
            },
            { render: function (data, type, row) {     
                return `${row.razon_social}`;
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
                let estadoDetalleOrdenHabilitadasActualizar=[17,20,26,30,31];
                if(estadoDetalleOrdenHabilitadasActualizar.includes(row.id_detalle_orden_estado) ==true){
                    return `<span class="label label-${row.detalle_orden_estado_bootstrap_color}" onClick="editarEstadoItemOrden(this);" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.detalle_orden_estado}</span>`;
                }else{
                    return `<span class="label label-${row.detalle_orden_estado_bootstrap_color}" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: default;">${row.detalle_orden_estado}</span>`;
                }

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
            {'26':'EN TRANSITO'}
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
    let estadoOrdenHabilitadasActualizar=[17,20,26,30,31];

    if(estadoOrdenHabilitadasActualizar.includes(data.id_estado)==true){
        document.querySelector("p[id='inputEstado']").innerHTML = `<span class="label label-${data.bootstrap_color}" id="estado_orden" onClick="editarEstadoOrden(this);" data-id-estado-orden-compra="${data.id_estado}" data-id-orden-compra="${data.id_orden_compra}" data-codigo-orden-compra="${data.codigo_softlink}" style="cursor: pointer;" title="Cambiar Estado de Orden">${data.estado_doc}</span>`
    }else{
        document.querySelector("p[id='inputEstado']").innerHTML = `<span class="label label-${data.bootstrap_color}" id="estado_orden" data-id-estado-orden-compra="${data.id_estado}" data-id-orden-compra="${data.id_orden_compra}" data-codigo-orden-compra="${data.codigo_softlink}" style="cursor: default;">${data.estado_doc}</span>`
    }
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
                    let estadoDetalleOrdenHabilitadasActualizar=[17,20,26,30,31];

                    if(estadoDetalleOrdenHabilitadasActualizar.includes(row.id_estado_detalle_orden)==true){
                        return `<span class="label label-${row.bootstrap_color_estado_detalle_orden}" onClick="editarEstadoItemOrden(this);" data-id-estado-detalle-orden-compra="${row.id_estado_detalle_orden}" data-id-orden-compra="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_item}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.estado_detalle_orden}</span>`;
                    }else{
                        return `<span class="label label-${row.bootstrap_color_estado_detalle_orden}" data-id-estado-detalle-orden-compra="${row.id_estado_detalle_orden}" data-id-orden-compra="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_item}" style="cursor: default;" >${row.estado_detalle_orden}</span>`;
                    }
                }
            }
        ],

    })

    let tablelistaitem = document.getElementById('tablaItemOrdenCompra_wrapper');
    tablelistaitem.childNodes[0].childNodes[0].hidden = true;
}


function eliminarAtencionOrdenRequerimiento(obj){
    let codigo_requerimiento = obj.dataset.codigoRequerimiento;
    // let id_requerimiento = obj.dataset.idRequerimiento;
    let id_orden = obj.dataset.idOrdenCompra;
    // console.log(id_requerimiento,id_orden);
    var ask = confirm('¿Desea revertir el requerimiento '+codigo_requerimiento+'?');
    if (ask == true){
        $.ajax({
            type: 'PUT',
            url: rutaRevertirOrdenPorRequerimiento+'/'+id_orden,
            beforeSend: function(){
            },
            success: function(response){
                // console.log(response);                
                if (response.status == 200) {
                    alert(response.mensaje);
                    $('#listaComprasEnProceso').DataTable().ajax.reload();
                    $('#listaRequerimientosPendientes').DataTable().ajax.reload();
                }else {
                    console.log(response);
                    alert(response.mensaje);
                    
                }
            }
        });
        return false;
    }else{
        return false;
    }
    
}


 

function cleanFormModalOrdenRequerimiento(){
    document.querySelector("form[id='form-orden-requerimiento'] input[name='codigo_orden']").value = '';
    document.querySelector("form[id='form-orden-requerimiento'] input[name='razon_social']").value = '';
    document.querySelector("form[id='form-orden-requerimiento'] input[name='id_proveedor']").value = '';
    document.querySelector("form[id='form-orden-requerimiento'] input[name='id_contrib']").value = '';
    $('#listaDetalleOrden').DataTable().clear();

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
        payload_orden =get_header_orden_requerimiento();
        payload_orden.detalle= detalleRequerimientoSelected;
    sendDataToSaveOrden(payload_orden);

    }else{
        alert('hubo un error al intentar vincular el item con el requerimiento');
        linksToReqObjArray=[];
    }

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





 

function agregarNuevoItem(){
    $('#modal-catalogo-items').modal({
        show: true,
        backdrop: 'true',
        keyboard: true

    });
    listarItems();

}





function fill_input_detalle_requerimiento(item){
    $('[name=id_tipo_item]').val(1);
    $('[name=id_item]').val(item.id_item);
    $('[name=id_producto]').val(item.id_producto);
    $('[name=id_servicio]').val(item.id_servicio);
    $('[name=id_equipo]').val(item.id_equipo);
    $('[name=id_detalle_requerimiento]').val(item.id_detalle_requerimiento);
    $('[name=codigo_item]').val(item.codigo_item);
    $('[name=part_number]').val(item.part_number);
    $('[name=descripcion_item]').val(item.descripcion_adicional);
    $('[name=unidad_medida_item]').val(item.id_unidad_medida);
    $('[name=cantidad_item]').val(item.cantidad);
    $('[name=precio_ref_item]').val(item.precio_referencial);
    $('[name=estado]').val(item.estado);
}


function get_data_detalle_requerimiento(){

    let id_cc_am_filas = null;
    let id_cc_venta_filas=null;
    // if( tempDetalleItemCCSelect.hasOwnProperty('id_cc_am_filas')){
    //     id_cc_am_filas = tempDetalleItemCCSelect.id_cc_am_filas;
    // }else if(tempDetalleItemCCSelect.hasOwnProperty('id_cc_venta_filas')){
    //     id_cc_venta_filas = tempDetalleItemCCSelect.id_cc_venta_filas;
    // }
 
    var id_item = $('[name=id_item]').val();
    var id_tipo_item = $('[name=id_tipo_item]').val();
    var id_producto = $('[name=id_producto]').val();
    var id_servicio = $('[name=id_servicio]').val();
    var id_equipo = $('[name=id_equipo]').val();
    var id_detalle_requerimiento = $('[name=id_detalle_requerimiento]').val();
    var cod_item = $('[name=codigo_item]').val();
    var part_number = $('[name=part_number]').val();
    var des_item = $('[name=descripcion_item]').val();
    // var id_unidad_medida = $('[name=unidad_medida_item]').val() !=="" ?$('[name=unidad_medida_item]').val():0;
    var id_unidad_medida = $('[name=unidad_medida_item]').val();
    // var und = document.getElementsByName("unidad_medida_item")[0];
    // var und_text = und.options[und.selectedIndex].text;   
    var und_text = $('[name=unidad_medida_item]').find('option:selected').text();
    var cantidad = $('[name=cantidad_item]').val();
    var precio_referencial = $('[name=precio_ref_item]').val();
    var id_tipo_moneda = $('[name=tipo_moneda]').val();
    var tipo_moneda = $('[name=tipo_moneda] option:selected').text()
    var categoria = $('[name=categoria]').val();
    var subcategoria = $('[name=subcategoria]').val();
    var fecha_entrega = $('[name=fecha_entrega_item]').val();
    var lugar_entrega = $('[name=lugar_entrega_item]').val();
    var id_partida = $('[name=id_partida]').val();
    var cod_partida = $('[name=cod_partida]').val();
    var des_partida = $('[name=des_partida]').val();
    var id_almacen_reserva = $('[name=id_almacen_reserva]').val();
    var almacen_descripcion = $('[name=almacen_descripcion]').val();
    if($('[name=estado]').val() === ""){
        var estado = 1;
    }else{
        var estado = $('[name=estado]').val();
        
    }
    
    // let tiene_transformacion = document.querySelector("form[id='form-requerimiento'] input[name='tiene_transformacion']").value;


    let item = {
        'id_item':parseInt(id_item),
        'id_tipo_item':parseInt(id_tipo_item),
        'id_producto':parseInt(id_producto),
        'id_servicio':parseInt(id_servicio),
        'id_equipo':parseInt(id_equipo),
        'id_detalle_requerimiento':parseInt(id_detalle_requerimiento),
        'cod_item':cod_item,
        'part_number':part_number,
        'des_item':des_item,
        'id_unidad_medida':parseInt(id_unidad_medida),
        'unidad':und_text,
        'cantidad':parseFloat(cantidad),
        'precio_referencial':parseFloat(precio_referencial)?parseFloat(precio_referencial):null,
        'id_tipo_moneda':id_tipo_moneda,
        'tipo_moneda':tipo_moneda,
        'categoria':categoria,
        'subcategoria':subcategoria,
        'fecha_entrega':fecha_entrega,
        'lugar_entrega':lugar_entrega,
        'id_partida':parseInt(id_partida),
        'cod_partida':cod_partida,
        'des_partida':des_partida,
        'estado':parseInt(estado),
        'id_almacen_reserva':parseInt(id_almacen_reserva),
        'almacen_descripcion':almacen_descripcion,
        'id_cc_am_filas':id_cc_am_filas,
        'id_cc_venta_filas': id_cc_venta_filas
        // 'tiene_transformacion':tiene_transformacion
        };
        return item;
}

function agregarItem(){

    let item = get_data_detalle_requerimiento();
    // console.log(item);
    if(item.cod_item ==="" || item.cod_item ===null || item.cod_item ===undefined ){
        alert("Campo vacío - Debe selecione un item o escriba uno");
        return null;
    }else{
        data_item_para_compra.push(item);
    }

    $('#modal-detalle-requerimiento').modal('hide');

    console.log(data_item_para_compra);
    llenar_tabla_items_para_compra(data_item_para_compra);

}

function limpiarTabla(idElement){
    // console.log("limpiando tabla....");
    var table = document.getElementById(idElement);
    for(var i = table.rows.length - 1; i > 0; i--)
    {
        table.deleteRow(i);
    }
    return null;
}

function llenar_tabla_items_para_compra(data_item_para_compra){
    limpiarTabla('ListaItemsParaComprar');
    htmls ='<tr></tr>';
    $('#ListaItemsParaComprar tbody').html(htmls);
    var table = document.getElementById("ListaItemsParaComprar"); 
    
    for(var a=0;a < data_item_para_compra.length;a++){
        if(data_item_para_compra[a].estado !=7){
            
            var row = table.insertRow(-1);
    
            row.insertCell(0).innerHTML = data_item_para_compra[a].cod_item?data_item_para_compra[a].cod_item:'0';
            row.insertCell(1).innerHTML = data_item_para_compra[a].part_number?data_item_para_compra[a].part_number:'-';
            row.insertCell(2).innerHTML = data_item_para_compra[a].des_item?data_item_para_compra[a].des_item:'-';
            row.insertCell(3).innerHTML = data_item_para_compra[a].unidad?data_item_para_compra[a].unidad:'-';
            row.insertCell(4).innerHTML = data_item_para_compra[a].cantidad?data_item_para_compra[a].cantidad:'0';
            row.insertCell(5).innerHTML = data_item_para_compra[a].tipo_moneda?data_item_para_compra[a].tipo_moneda:'';
            row.insertCell(6).innerHTML = data_item_para_compra[a].precio_referencial?data_item_para_compra[a].precio_referencial:'0';

            var tdBtnAction = row.insertCell(7);
            tdBtnAction.setAttribute('width','auto');
            tdBtnAction.innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
            '<button class="btn btn-primary btn-sm"  name="btnEditarItem" data-toggle="tooltip" title="Editar" data-id-item="'+data_item_para_compra[a].id_item+'" onClick="detalleRequerimientoModal(event, '+a+');"><i class="fas fa-edit"></i></button>'+
            '<button class="btn btn-danger btn-sm"   name="btnEliminarItem" data-toggle="tooltip" title="Eliminar" data-id-item="'+data_item_para_compra[a].id_item+'" onclick="eliminarItemDetalleRequerimiento(event, '+a+');" ><i class="fas fa-trash-alt"></i></button>'+
            '</div>';

        }
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

function llenarTablaDocumentosVinculados(data){
    var vardataTables = funcDatatables();
    $('#tablaDocumentosVinculados').dataTable({
        'info':     false,
        'searching': false,
        'paging':   false,
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'data':data,
        'columns': [
            {'render':
                function (data, type, row){
                    return `<a href="${row.orden_fisica}" target="_blank"><span class="label label-warning">Orden Física</span></a> 
                    <a href="${row.orden_electronica}" target="_blank"><span class="label label-info">Orden Electrónica</span></a>`;
                }
            }
        ]
    });
    let tableDocumentosVinculados = document.getElementById(
        'tablaDocumentosVinculados_wrapper'
    )
    tableDocumentosVinculados.childNodes[0].childNodes[0].hidden = true;
}

function listarDocumentosVinculados(id){
    $.ajax({
        type: 'GET',
        url: rutaDocumentosVinculadosOrden+'/'+id,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.status==200){
                llenarTablaDocumentosVinculados(response.data);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function documentosVinculados(obj){
    $('#modal-documentos-vinculados').modal({
        show: true,
        backdrop: 'static'
    });

    let id_orden_compra = obj.dataset.idOrdenCompra;
    listarDocumentosVinculados(id_orden_compra);
}