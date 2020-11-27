let od_seleccionadas = [];
let permiso_temp = null;

function iniciar(permiso){
    $("#tab-reqPendientes section:first form").attr('form', 'formulario');
    listarRequerimientosElaborados();
    actualizaCantidadDespachosTabs();
    intervalFunction();
    permiso_temp = permiso;

    $('ul.nav-tabs li a').on('click',function(){
        $('ul.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.content-tabs section').attr('hidden', true);
        $('.content-tabs section form').removeAttr('type');
        $('.content-tabs section form').removeAttr('form');

        var activeTab = $(this).attr('type');
        var activeForm = "form-"+activeTab.substring(1);
        
        $("#"+activeForm).attr('type', 'register');
        $("#"+activeForm).attr('form', 'formulario');
        changeStateInput(activeForm, true);

        // clearDataTable();
        if (activeForm == "form-elaborados"){
            listarRequerimientosElaborados();
            od_seleccionadas = [];
        }
        else if (activeForm == "form-confirmados"){
            listarRequerimientosConfirmados(permiso);
            od_seleccionadas = [];
        }
        else if (activeForm == "form-pendientes"){
            od_seleccionadas = [];
            listarRequerimientosPendientes(permiso);
            // $('#requerimientosPendientes').DataTable().ajax.reload();
        } 
        else if (activeForm == "form-despachos"){
            listarOrdenesPendientes();
            od_seleccionadas = [];
        }
        else if (activeForm == "form-sinTransporte"){
            listarGruposDespachados(permiso);
            od_seleccionadas = [];
        }
        else if (activeForm == "form-retornoCargo"){
            listarGruposDespachadosPendientesCargo(permiso);
            od_seleccionadas = [];
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    vista_extendida();
}

function intervalFunction() {
    setInterval(actualizaCantidadDespachosTabs, 60000);
}

function actualizaCantidadDespachosTabs(){
    $.ajax({
        type: 'GET',
        url: 'actualizaCantidadDespachosTabs',
        global: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#selaborados').text(response['count_pendientes'] > 0 ? response['count_pendientes'] : '');
            $('#sconfirmados').text(response['count_confirmados'] > 0 ? response['count_confirmados'] : '');
            $('#spendientes').text(response['count_en_proceso'] > 0 ? response['count_en_proceso'] : '');
            $('#sdespachos').text(response['count_por_despachar'] > 0 ? response['count_por_despachar'] : '');
            $('#ssinTransporte').text(response['count_despachados'] > 0 ? response['count_despachados'] : '');
            $('#sretornoCargo').text(response['count_cargo'] > 0 ? response['count_cargo'] : '');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listarRequerimientosElaborados(){
    var vardataTables = funcDatatables();
    $('#requerimientosElaborados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarRequerimientosElaborados',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_requerimiento'},
            {'render': function (data, type, row){
                return (row['orden_am'] !== null ? row['orden_am']+`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                <span class="label label-success">Ver O.E.</span></a>
            <a href="${row['url_oc_fisica']}">
                <span class="label label-warning">Ver O.F.</span></a>` : '');
                }
            },
            {'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad'},
            {'data': 'oportunidad', 'name': 'oportunidades.oportunidad'},
            {'data': 'nombre', 'name': 'entidades.nombre'},
            {'data': 'sede_descripcion_req', 'name': 'sede_req.descripcion'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'fecha_requerimiento'},
            // {'data': 'direccion_entrega'},
            // {'data': 'grupo', 'name': 'adm_grupo.descripcion'},
            {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
            // {'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc'},
            {'render': function (data, type, row){
                return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                }
            },
            {'render': function (data, type, row){
                    return 'Pendiente de que <strong>Compras</strong> genere la OC';
                }
            }
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" >'+
                    '<i class="fas fa-list-ul"></i></button>';
                }, targets: 12
            }
        ],
    });
}

$('#requerimientosElaborados tbody').on("click","button.detalle", function(){
    var data = $('#requerimientosElaborados').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    open_detalle_requerimiento(data);
});

function listarRequerimientosConfirmados(permiso){
    var vardataTables = funcDatatables();
    $('#requerimientosConfirmados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarRequerimientosConfirmados',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_requerimiento'},
            {'render': function (data, type, row){
                return (row['orden_am'] !== null ? row['orden_am']+`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                <span class="label label-success">Ver O.E.</span></a>
            <a href="${row['url_oc_fisica']}">
                <span class="label label-warning">Ver O.F.</span></a>` : '');
                }
            },
            {'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad'},
            {'data': 'oportunidad', 'name': 'oportunidades.oportunidad'},
            {'data': 'nombre', 'name': 'entidades.nombre'},
            {'data': 'sede_descripcion_req', 'name': 'sede_req.descripcion'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'fecha_requerimiento'},
            {'render': function (data, type, row){
                return (row['ubigeo_descripcion'] !== null ? row['ubigeo_descripcion'] : '');
                }
            },
            {'data': 'direccion_entrega'},
            // {'data': 'grupo', 'name': 'adm_grupo.descripcion'},
            {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
            // {'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc'},
            {'render': function (data, type, row){
                return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                }
            },
            {'render': function (data, type, row){
                    if (row['estado'] == 1 && row['confirmacion_pago'] == true){
                        return 'Pendiente de que <strong>Compras</strong> genere la OC';
                    } 
                    else if (row['estado'] == 5){
                        return 'Pendiente de que <strong>Almacén</strong> genere el Ingreso';
                    } 
                    else if (row['estado'] == 15){
                        return 'Pendiente de que <strong>Compras</strong> complete la OC';
                    } 
                    // else if (row['id_tipo_requerimiento'] !== 1 && row['estado'] == 19 && row['id_od'] == null){
                    //     return 'Pendiente de que <strong>Distribución</strong> genere la OD';
                    // }
                    // else if (row['estado'] == 29){
                    //     return 'Pendiente de que <strong>Almacén</strong> realice la salida';
                    // }
                    else {
                        return '';
                    }
                }
            }
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                    return (permiso == '1' ? ((row['estado'] == 19 && row['confirmacion_pago'] == true) ? 
                    (`<button type="button" class="despacho btn btn-success boton" data-toggle="tooltip" 
                        data-placement="bottom" title="Generar Orden de Despacho" >
                        <i class="fas fa-sign-in-alt"></i></button>`) : '') : '')+
                    (`<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                        data-placement="bottom" title="Ver Detalle" >
                        <i class="fas fa-list-ul"></i></button>`);
                }, targets: 14
            }
        ],
    });
}
$('#requerimientosConfirmados tbody').on("click","button.detalle", function(){
    var data = $('#requerimientosConfirmados').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    open_detalle_requerimiento(data);
});

$('#requerimientosConfirmados tbody').on("click","button.despacho", function(){
    var data = $('#requerimientosConfirmados').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    tab_origen = 'confirmados';
    open_despacho_create(data);
});

function listarRequerimientosPendientes(permiso){
    var vardataTables = funcDatatables();
    $('#requerimientosEnProceso').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarRequerimientosPendientes',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_requerimiento'},
            {'render': function (data, type, row){
                return (row['orden_am'] !== null ? row['orden_am']+`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                <span class="label label-success">Ver O.E.</span></a>
                <a href="${row['url_oc_fisica']}">
                <span class="label label-warning">Ver O.F.</span></a>` : '');
                }
            },
            {'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad'},
            {'data': 'oportunidad', 'name': 'oportunidades.oportunidad'},
            {'data': 'nombre', 'name': 'entidades.nombre'},
            {'data': 'sede_descripcion_req', 'name': 'sede_req.descripcion'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'fecha_requerimiento'},
            // {'render': function (data, type, row){
            //     return (row['ubigeo_descripcion'] !== null ? row['ubigeo_descripcion'] : '');
            //     }
            // },
            // {'data': 'direccion_entrega'},
            // {'data': 'grupo', 'name': 'adm_grupo.descripcion'},
            {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
            // {'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc'},
            {'render': function (data, type, row){
                return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                }
            },
            {'render': function (data, type, row){
                return ((row['count_transferencia'] > 0 ? 
                '<button type="button" class="detalle_trans btn btn-success boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle de Transferencias" data-id="'+row['id_requerimiento']+'">'+
                    '<i class="fas fa-exchange-alt"></i></button>' : ''))
                }
            },
            {'render': function (data, type, row){
                return  (row['count_despachos_internos'] > 0 ? ('<span class="label label-danger">'+row['count_despachos_internos']+' </span>') : '')+
                        (row['codigo_od'] !== null ? ('<span class="label label-primary">'+row['codigo_od']+'</span>') : '');
                }
            },
            {'data': 'fecha_despacho', 'name': 'orden_despacho.fecha_despacho'},
            {'data': 'hora_despacho', 'name': 'orden_despacho.hora_despacho'},
            {'render': function (data, type, row){
                    if (row['estado'] == 17){
                        return 'Pendiente de que <strong>Almacén</strong> recepcione la Transferencia';
                    } 
                    else if (row['estado'] == 19 && row['count_transferencia'] > 0 && 
                             row['count_transferencia'] !== row['count_transferencia_recibida']){
                        return 'Pendiente de que <strong>Almacén</strong> envíe la Transferencia';
                    }
                    else if (row['estado'] == 19 && row['id_od'] == null && 
                             row['count_transferencia'] == row['count_transferencia_recibida']){
                        return 'Pendiente de que <strong>Distribución</strong> genere la OD';
                    }
                    else if (row['estado'] == 19 && row['id_od'] !== null){
                        return 'Pendiente de que <strong>Almacén</strong> genere la Salida';
                    }
                    else if (row['id_tipo_requerimiento'] !== 1 && row['estado'] == 19 && row['id_od'] == null){
                        return 'Pendiente de que <strong>Distribución</strong> genere la OD';
                    }
                    else if (row['estado'] == 22){
                        return 'Pendiente de que <strong>Customización</strong> realice la transformación';
                    }
                    else if (row['estado'] == 10){
                        return 'Pendiente de que <strong>Distribución</strong> realice el Despacho Externo';
                    }
                    else if (row['estado'] == 27){
                        return 'Pendiente de que <strong>Almacén</strong> complete los ingresos';
                    }
                    else if (row['estado'] == 28){
                        return 'Pendiente de que <strong>Distribución</strong> genere la Orden de Despacho';
                    }
                    else if (row['estado'] == 29){
                        return 'Pendiente de que <strong>Almacén</strong> genere la Salida';
                    }
                }
            }
        ],
        'order': [[ 10, "asc" ],[ 11, "asc" ]],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                if (permiso == '1') {
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" >'+
                    '<i class="fas fa-list-ul"></i></button>'+
                    ((row['estado'] == 19 && row['id_tipo_requerimiento'] == 2 && row['id_od'] == null && row['confirmacion_pago'] == false) ? 
                        '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                        'data-placement="bottom" data-id="'+row['id_requerimiento']+'" data-cod="'+row['codigo']+'" title="Anular Requerimiento" >'+
                        '<i class="fas fa-trash"></i></button>' : '')+
                    (
                        (
                        //     (row['estado'] == 19 && row['id_tipo_requerimiento'] == 1 && row['sede_requerimiento'] == row['sede_orden'] && row['id_od'] == null) || //compra 
                        // (row['estado'] == 19 && row['id_tipo_requerimiento'] == 1 && row['sede_requerimiento'] !== row['sede_orden'] && row['id_transferencia'] !== null && row['id_od'] == null) || //compra con transferencia
                        (row['estado'] == 19 && row['confirmacion_pago'] == true && /*row['id_od'] == null &&*/ row['count_transferencia'] == 0) || //venta directa
                        (row['estado'] == 10) || (row['estado'] == 22) ||
                        (row['estado'] == 28) || (row['estado'] == 27) ||
                        (row['estado'] == 19 && row['id_tipo_requerimiento'] !== 1) ||
                        (row['estado'] == 19 && row['confirmacion_pago'] == true && /*row['id_od'] == null &&*/ row['count_transferencia'] > 0 && row['count_transferencia'] == row['count_transferencia_recibida'])) ? //venta directa con transferencia
                            ('<button type="button" class="despacho btn btn-success boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Generar Orden de Despacho" >'+
                            '<i class="fas fa-sign-in-alt"></i></button>') : 
                        ( row['id_od'] !== null && row['estado_od'] == 1) ?
                        `<button type="button" class="adjuntar btn btn-warning boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Adjuntar Boleta/Factura" >
                            <i class="fas fa-paperclip"></i></button>
                        <button type="button" class="anular_od btn btn-danger boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Anular Orden Despacho" >
                            <i class="fas fa-trash"></i></button>` : '' )+
                        (row['estado'] == 9 ? 
                        `<button type="button" class="adjuntar btn btn-warning boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Adjuntar Boleta/Factura" >
                            <i class="fas fa-paperclip"></i></button>` : '')
                } else {
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" >'+
                    '<i class="fas fa-list-ul"></i></button>'
                }
                }, targets: 16
            }
        ],
    });
   
}

$('#requerimientosEnProceso tbody').on("click","button.detalle", function(){
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    open_detalle_requerimiento(data);
});

$('#requerimientosEnProceso tbody').on("click","button.detalle_trans", function(){
    var id = $(this).data('id');
    open_detalle_transferencia(id);
});

$('#requerimientosEnProceso tbody').on("click","button.adjuntar", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    $('#modal-despachoAdjuntos').modal({
        show: true
    });
    listarAdjuntos(id);
    $('[name=id_od]').val(id);
    $('[name=codigo_od]').val(cod);
    $('[name=descripcion]').val('');
    $('[name=archivo_adjunto]').val('');
});

$('#requerimientosEnProceso tbody').on("click","button.anular", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    var origen = 'despacho';
    openRequerimientoObs(id, cod, origen);
});

$('#requerimientosEnProceso tbody').on("click","button.despacho", function(){
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    tab_origen = 'enProceso';
    open_despacho_create(data);
});

$('#requerimientosEnProceso tbody').on("click","button.anular_od", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    var msj = confirm('¿Está seguro que desea anular la '+cod+' ?');
    if (msj){
        anularOrdenDespacho(id);
    }
});

function anularOrdenDespacho(id){
    $.ajax({
        type: 'GET',
        url: 'anular_orden_despacho/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                $('#requerimientosEnProceso').DataTable().ajax.reload();
                actualizaCantidadDespachosTabs();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function open_detalle_transferencia(id){
    $('#modal-detalleTransferencia').modal({
        show: true
    });
    $.ajax({
        type: 'GET',
        url: 'listarDetalleTransferencias/'+id,
        dataType: 'JSON',
        success: function(response){
            $('#detalleTransferencias tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listarOrdenesPendientes(){
    var vardataTables = funcDatatables();
    $('#ordenesDespacho').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarOrdenesDespacho',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_od'},
            {'render': function (data, type, row){
                return (row['orden_am'] !== null ? row['orden_am']+`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                <span class="label label-success">Ver O.E.</span></a>
            <a href="${row['url_oc_fisica']}">
                <span class="label label-warning">Ver O.F.</span></a>` : '');
                }
            },
            {'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad'},
            {'data': 'oportunidad', 'name': 'oportunidades.oportunidad'},
            {'data': 'nombre', 'name': 'entidades.nombre'},
            {'data': 'codigo'},
            {'render': 
                function (data, type, row){
                    if (row['razon_social'] !== null){
                        return row['razon_social'];
                    } else if (row['nombre_persona'] !== null){
                        return row['nombre_persona'];
                    }
                }
            },
            // {'data': 'razon_social', 'name': 'adm_contri.razon_social'},
            {'data': 'codigo_req', 'name': 'alm_req.codigo'},
            {'data': 'concepto', 'name': 'alm_req.concepto'},
            // {'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion'},
            // {'data': 'ubigeo_descripcion', 'name': 'ubi_dis.descripcion'},
            // {'data': 'direccion_destino'},
            {'data': 'fecha_despacho'},
            {'data': 'fecha_entrega'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'},
            {'render': 
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            },
            {'render': 
                function (data, type, row){
                    return 'Pendiente de que <strong>Distribución</strong> genere el Despacho';
                }
            },
            {'render': 
                function (data, type, row){
                    return `<button type="button" class="od_detalle btn btn-primary boton" data-toggle="tooltip" 
                                data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}" 
                                data-codigo="${row['codigo_req']}" data-concepto="${row['concepto']}">
                                <i class="fas fa-list-ul"></i></button>
                            <button type="button" class="adjuntar btn btn-warning boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo']}" title="Adjuntar Boleta/Factura" >
                                <i class="fas fa-paperclip"></i></button>`;
                }
            }
        ],
        'drawCallback': function(){
            $('#ordenesDespacho tbody tr td input[type="checkbox"]').iCheck({
               checkboxClass: 'icheckbox_flat-blue'
            });
         },
         'columnDefs': [
            {
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'className': 'dt-body-center',
                // 'checkboxes': {
                //     'selectRow': true
                //  }
                'checkboxes': {
                    'selectRow': true,
                    'selectCallback': function(nodes, selected){
                        $('input[type="checkbox"]', nodes).iCheck('update');
                    },
                    'selectAllCallback': function(nodes, selected, indeterminate){
                        $('input[type="checkbox"]', nodes).iCheck('update');
                    }
                }
            }
         ],
        'select': 'multi',
        'order': [[1, 'asc']]
    });
    
    $('#ordenesDespacho tbody').on("click","button.od_detalle", function(){
        // var data = $('#ordenesDespacho').DataTable().row($(this).parents("tr")).data();
        // console.log('data.id_od'+data.id_od);
        // open_detalle_despacho(data);
        var id = $(this).data('id');
        var cod = $(this).data('codigo');
        var con = $(this).data('concepto');
        var data = {
            'id_requerimiento': id,
            'codigo': cod,
            'concepto': con,
        };
        console.log(data);
        open_detalle_requerimiento(data);
    });

    $('#ordenesDespacho tbody').on("click","button.adjuntar", function(){
        var id = $(this).data('id');
        var cod = $(this).data('cod');
        $('#modal-despachoAdjuntos').modal({
            show: true
        });
        listarAdjuntos(id);
        $('[name=id_od]').val(id);
        $('[name=codigo_od]').val(cod);
    });
    // Handle iCheck change event for checkboxes in table body
    $($('#ordenesDespacho').DataTable().table().container()).on('ifChanged', '.dt-checkboxes', function(event){
        var cell = $('#ordenesDespacho').DataTable().cell($(this).closest('td'));
        cell.checkboxes.select(this.checked);

        var data = $('#ordenesDespacho').DataTable().row($(this).parents("tr")).data();
        console.log(this.checked);

        if (data !== null && data !== undefined){
            if (this.checked){
                od_seleccionadas.push(data);
            }
            else {
                var index = od_seleccionadas.findIndex(function(item, i){
                    return item.id_od == data.id_od;
                });
                if (index !== null){
                    od_seleccionadas.splice(index,1);
                }
            }
        }
    });
}

function listarGruposDespachados(permiso){
    var vardataTables = funcDatatables();
    $('#gruposDespachados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarGruposDespachados',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_od_grupo_detalle'},
            {'render': function (data, type, row){
                return (row['orden_am'] !== null ? row['orden_am']+`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                <span class="label label-success">Ver O.E.</span></a>
            <a href="${row['url_oc_fisica']}">
                <span class="label label-warning">Ver O.F.</span></a>` : '');
                }
            },
            {'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad'},
            {'data': 'oportunidad', 'name': 'oportunidades.oportunidad'},
            {'data': 'nombre', 'name': 'entidades.nombre'},
            // {'data': 'codigo_odg', 'name': 'orden_despacho_grupo.codigo'},
            // {'data': 'codigo_od', 'name': 'orden_despacho.codigo'},
            {'render': 
                function (data, type, row){
                    return ('<label class="lbl-codigo" title="Abrir Despacho" onClick="openDespacho('+row['id_od_grupo']+')">'+row['codigo_od']+'</label>');
                }
            },
            {'data': 'codigo_req', 'name': 'alm_req.codigo'},
            // {'render': 
            //     function (data, type, row){
            //         if (row['cliente_razon_social'] !== null){
            //             return row['cliente_razon_social'];
            //         } else if (row['cliente_persona'] !== null){
            //             return row['cliente_persona'];
            //         }
            //     }
            // },
            {'data': 'concepto', 'name': 'alm_req.concepto'},
            // {'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion'},
            // {'data': 'ubigeo_descripcion', 'name': 'ubi_dis.descripcion'},
            // {'data': 'direccion_destino', 'name': 'orden_despacho.direccion_destino'},
            {'data': 'fecha_despacho', 'name': 'orden_despacho_grupo.fecha_despacho'},
            {'render': 
                function (data, type, row){
                    if (row['proveedor_despacho'] !== null){
                        return (row['proveedor_despacho']);
                    } else {
                        return (row['trabajador_despacho']);
                    }
                }
            },
            {'data': 'mov_entrega', 'name': 'orden_despacho_grupo.mov_entrega'},
            // {'data': 'obs_confirmacion'},
            {'render': 
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            },
            {'render': 
                function (data, type, row){
                    if (row['estado_od'] == 20){
                        return 'Pendiente de que <strong>Distribución</strong> confirme la Entrega';
                    } else {
                        return '';
                    }
                }
            },
            {'render': 
                function (data, type, row){
                    if (permiso == '1') {
                        // '<button type="button" class="god_detalle btn btn-primary boton" data-toggle="tooltip" '+
                        // 'data-placement="bottom" title="Ver Detalle" >'+
                        // '<i class="fas fa-list-ul"></i></button>'+
                        return (
                        `<button type="button" class="adjuntar btn btn-warning boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Adjuntar Boleta/Factura" >
                            <i class="fas fa-paperclip"></i></button>`+
                        ((row['confirmacion'] == false && row['estado_od'] == 20)? 
                        ('<button type="button" class="transportista btn btn-success boton" data-toggle="tooltip" '+
                        'data-placement="bottom" data-id="'+row['id_od_grupo_detalle']+'" data-od="'+row['id_od']+'" data-idreq="'+row['id_requerimiento']+'" data-cod-req="'+row['codigo_req']+'" data-concepto="'+row['concepto']+'" data-mov="'+row['mov_entrega']+'" title="Agregar Datos del Transportista" >'+
                        '<i class="fas fa-shuttle-van"></i></button>'+
                        '<button type="button" class="revertir btn btn-danger boton" data-toggle="tooltip" '+
                        'data-placement="bottom" data-id="'+row['id_od_grupo_detalle']+'" data-od="'+row['id_od']+'" data-idreq="'+row['id_requerimiento']+'" data-cod-req="'+row['codigo_req']+'" data-concepto="'+row['concepto']+'" title="Revertir" >'+
                        '<i class="fas fa-backspace"></i></button>') : ''));
                    } else {
                        return `<button type="button" class="adjuntar btn btn-warning boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Adjuntar Boleta/Factura" >
                            <i class="fas fa-paperclip"></i></button>`;
                    }
                }
            },
        ],
        'columnDefs': [{ 
            'aTargets': [0], 
            // 'searchable': true,
            'sClass': 'invisible'
        }],
    });
}

$('#gruposDespachados tbody').on("click","button.god_detalle", function(){
    var data = $('#gruposDespachados').DataTable().row($(this).parents("tr")).data();
    console.log('data.id_od'+data.id_od_grupo);
    open_grupo_detalle(data);
});

$('#gruposDespachados tbody').on("click","button.adjuntar", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    $('#modal-despachoAdjuntos').modal({
        show: true
    });
    listarAdjuntos(id);
    $('[name=id_od]').val(id);
    $('[name=codigo_od]').val(cod);
});

// $('#gruposDespachados tbody').on("click","button.imprimir", function(){
//     var id_od_grupo = $(this).data('idGrupo');
//     var id = encode5t(id_od_grupo);
//     window.open('imprimir_despacho/'+id);
// });

function openDespacho(id_od_grupo){
    var id = encode5t(id_od_grupo);
    window.open('imprimir_despacho/'+id);
}

$('#gruposDespachados tbody').on("click","button.transportista", function(){
    var id_od_grupo_detalle = $(this).data('id');
    var id_od = $(this).data('od');
    var id_req = $(this).data('idreq');
    var cod_req = $(this).data('codReq');
    var concepto = $(this).data('concepto');
    var mov = $(this).data('mov');

    if (mov !== 'Cliente Recoge en Oficina'){
        $('#modal-orden_despacho_transportista').modal({
            show: true
        });
        $('[name=id_od]').val(id_od);
        $('[name=con_id_requerimiento]').val(id_req);
        $('[name=id_od_grupo_detalle]').val(id_od_grupo_detalle);
        $('[name=agencia]').val('');
        $('[name=serie]').val('');
        $('[name=numero]').val('');
        $('[name=fecha_transportista]').val('');
        $('[name=codigo_envio]').val('');
        $('[name=importe_flete]').val('');
        $('#submit_od_transportista').removeAttr("disabled");
    }
    else {
        var rspta = confirm('¿Está seguro que desea dar Conformidad de Entrega al '+cod_req+' '+concepto+'?');
        if (rspta){
            var data = 'id_od_grupo_detalle='+id_od_grupo_detalle+
                       '&id_od='+id_od+
                       '&id_requerimiento='+id_req;
            despacho_conforme(data);
        }
    }
});

$("#form-orden_despacho_transportista").on("submit", function(e){
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    $('#submit_od_transportista').attr('disabled','true');
    despacho_transportista(data);
    $('#modal-orden_despacho_transportista').modal('hide');
});

$('#gruposDespachados tbody').on("click","button.revertir", function(){
    var id_od_grupo_detalle = $(this).data('id');
    var id_od = $(this).data('od');
    var id_req = $(this).data('idreq');
    var cod_req = $(this).data('codReq');
    var concepto = $(this).data('concepto');

    var data = 'id_od_grupo_detalle='+id_od_grupo_detalle+
                '&id_od='+id_od+
                '&id_requerimiento='+id_req;

    var rspta = confirm('¿Está seguro que desea revertir el '+cod_req+' '+concepto+'?');
    if (rspta){
        despacho_revertir_despacho(data);
    }
    // $('#modal-despacho_obs').modal({
    //     show: true
    // });

    // $('[name=obs_id_od_grupo_detalle]').val(id_od_grupo_detalle);
    // $('[name=obs_id_od]').val(id_od);
    // $('[name=obs_id_requerimiento]').val(id_req);
    // $("#codigo_odg").text(cod_req +' - '+concepto+' - '+"No Entregado");
    // $("#btnDespachoObs").removeAttr("disabled");
});

function despacho_transportista(data){
    $.ajax({
        type: 'POST',
        url: 'despacho_transportista',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                $('#gruposDespachados').DataTable().ajax.reload();
                actualizaCantidadDespachosTabs();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function despacho_revertir_despacho(data){
    $.ajax({
        type: 'POST',
        url: 'despacho_revertir_despacho',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                // $('#modal-despacho_obs').modal('hide');
                $('#gruposDespachados').DataTable().ajax.reload();
                actualizaCantidadDespachosTabs();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function open_grupo_detalle(data){
    $('#modal-grupoDespachoDetalle').modal({
        show: true
    });
    $('#cabeceraGrupo').text(data.codigo);
    $('#detalleGrupoDespacho tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'verDetalleGrupoDespacho/'+data.id_od_grupo,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response.forEach(element => {
                html+='<tr id="'+element.id_od_grupo_detalle+'">'+
                '<td>'+i+'</td>'+
                '<td>'+element.codigo+'</td>'+
                '<td>'+(element.razon_social !== null ? element.razon_social : element.nombre_persona)+'</td>'+
                '<td>'+element.codigo_req+'</td>'+
                '<td>'+element.concepto+'</td>'+
                '<td>'+element.ubigeo_descripcion+'</td>'+
                '<td>'+element.direccion_destino+'</td>'+
                '<td>'+element.fecha_despacho+'</td>'+
                '<td>'+element.fecha_entrega+'</td>'+
                '<td>'+element.nombre_corto+'</td>'+
                '</tr>';
                i++;
            });
            $('#detalleGrupoDespacho tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listarGruposDespachadosPendientesCargo(permiso){
    var vardataTables = funcDatatables();
    $('#pendientesRetornoCargo').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarGruposDespachadosPendientesCargo',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_od_grupo_detalle'},
            {'render': function (data, type, row){
                return (row['orden_am'] !== null ? row['orden_am']+`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                <span class="label label-success">Ver O.E.</span></a>
            <a href="${row['url_oc_fisica']}">
                <span class="label label-warning">Ver O.F.</span></a>` : '');
                }
            },
            {'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad'},
            {'data': 'oportunidad', 'name': 'oportunidades.oportunidad'},
            {'data': 'nombre', 'name': 'entidades.nombre'},
            // {'data': 'codigo_odg', 'name': 'orden_despacho_grupo.codigo'},
            // {'data': 'codigo_od', 'name': 'orden_despacho.codigo'},
            {'render': 
                function (data, type, row){
                    return ('<label class="lbl-codigo" title="Abrir Despacho" onClick="openDespacho('+row['id_od_grupo']+')">'+row['codigo_od']+'</label>');
                }
            },
            {'data': 'codigo_req', 'name': 'alm_req.codigo'},
            // {'render': 
            //     function (data, type, row){
            //         if (row['cliente_razon_social'] !== null){
            //             return row['cliente_razon_social'];
            //         } else if (row['cliente_persona'] !== null){
            //             return row['cliente_persona'];
            //         }
            //     }
            // },
            {'data': 'concepto', 'name': 'alm_req.concepto'},
            // {'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion'},
            // {'data': 'ubigeo_descripcion', 'name': 'ubi_dis.descripcion'},
            // {'data': 'direccion_destino', 'name': 'orden_despacho.direccion_destino'},
            {'data': 'fecha_despacho', 'name': 'orden_despacho_grupo.fecha_despacho'},
            {'render': 
                function (data, type, row){
                    if (row['proveedor_despacho'] !== null){
                        return (row['proveedor_despacho']);
                    } else {
                        return (row['trabajador_despacho']);
                    }
                }
            },
            {'data': 'mov_entrega', 'name': 'orden_despacho_grupo.mov_entrega'},
            // {'data': 'obs_confirmacion'},
            {'render': 
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            },
            {'render': 
                function (data, type, row){
                    if (row['estado_od'] == 20){
                        return 'Pendiente de que <strong>Distribución</strong> confirme la Entrega';
                    } else {
                        return '';
                    }
                }
            },
            {'render': 
                function (data, type, row){
                    if (permiso == '1') {
                        // '<button type="button" class="god_detalle btn btn-primary boton" data-toggle="tooltip" '+
                        // 'data-placement="bottom" title="Ver Detalle" >'+
                        // '<i class="fas fa-list-ul"></i></button>'+
                        return `<button type="button" class="adjuntar btn btn-warning boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Agregar Comentarios" >
                            <i class="fas fa-comment-dots"></i></button>
                            <button type="button" class="conforme btn btn-success boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od_grupo_detalle']}" data-od="${row['id_od']}" data-idreq="${row['id_requerimiento']}" data-cod-req="${row['codigo_req']}" data-concepto="${row['concepto']}" title="Confirmar Entrega" >
                            <i class="fas fa-check"></i></button>
                            <button type="button" class="no_conforme btn btn-danger boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od_grupo_detalle']}" data-od="${row['id_od']}" data-idreq="${row['id_requerimiento']}" data-cod-req="${row['codigo_req']}" data-concepto="${row['concepto']}" title="Revertir" >
                            <i class="fas fa-backspace"></i></button>`;
                    }
                }
            },
        ],
        'columnDefs': [{ 
            'aTargets': [0], 
            // 'searchable': true,
            'sClass': 'invisible'
        }],
    });
}

$('#pendientesRetornoCargo tbody').on("click","button.adjuntar", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    $('#modal-despachoAdjuntos').modal({
        show: true
    });
    listarAdjuntos(id);
    $('[name=id_od]').val(id);
    $('[name=codigo_od]').val(cod);
});

$('#pendientesRetornoCargo tbody').on("click","button.conforme", function(){
    var id_od_grupo_detalle = $(this).data('id');
    var id_od = $(this).data('od');
    var id_req = $(this).data('idreq');
    var cod_req = $(this).data('codReq');
    var concepto = $(this).data('concepto');
    
    var rspta = confirm('¿Está seguro de confirmar la Entrega del '+cod_req+' '+concepto);

    if (rspta){
        var data =  'id_od_grupo_detalle='+id_od_grupo_detalle+
                    '&id_od='+id_od+
                    '&id_requerimiento='+id_req;
        despacho_conforme(data);
    }
});

$('#pendientesRetornoCargo tbody').on("click","button.no_conforme", function(){
    var id_od_grupo_detalle = $(this).data('id');
    var id_od = $(this).data('od');
    var id_req = $(this).data('idreq');
    var cod_req = $(this).data('codReq');
    var concepto = $(this).data('concepto');

    var rspta = confirm('¿Está seguro que desea revertir el '+cod_req+' '+concepto);

    if (rspta){
        var data =  'id_od_grupo_detalle='+id_od_grupo_detalle+
                    '&id_od='+id_od+
                    '&id_requerimiento='+id_req;
        despacho_no_conforme(data);
    }
});

function despacho_conforme(data){
    $.ajax({
        type: 'POST',
        url: 'despacho_conforme',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                $('#pendientesRetornoCargo').DataTable().ajax.reload();
                actualizaCantidadDespachosTabs();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function despacho_no_conforme(data){
    $.ajax({
        type: 'POST',
        url: 'despacho_no_conforme',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                $('#pendientesRetornoCargo').DataTable().ajax.reload();
                actualizaCantidadDespachosTabs();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
