let od_seleccionadas = [];

$(function(){
    $("#tab-reqPendientes section:first form").attr('form', 'formulario');
    listarRequerimientosPendientes();

    $('ul.nav-tabs li a').click(function(){
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

        clearDataTable();
        if (activeForm == "form-pendientes"){
            listarRequerimientosPendientes();
        } 
        else if (activeForm == "form-despachos"){
            listarOrdenesPendientes();
        }
        else if (activeForm == "form-despachados"){
            listarGruposDespachados();
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    vista_extendida();
});

function listarRequerimientosPendientes(){
    var vardataTables = funcDatatables();
    $('#requerimientosPendientes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarRequerimientosPendientes',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'tipo_req'},
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
                return (row['codigo_orden'] !== null ? row['codigo_orden'] : '')
                }
            },
            {'render': function (data, type, row){
                return (row['sede_descripcion_orden'] !== null ? row['sede_descripcion_orden'] : '')
                }
            },            
            {'render': function (data, type, row){
                return (row['serie'] !== null ? row['serie']+'-'+row['numero'] : '')
                }
            },
            {'render': function (data, type, row){
                return (row['codigo_transferencia'] !== null ? row['codigo_transferencia'] : '')
                }
            },
            {'render': function (data, type, row){
                return (row['codigo_od'] !== null ? row['codigo_od'] : '')
                }
            }
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver Detalle" >'+
                '<i class="fas fa-list-ul"></i></button>'+
                ((row['estado'] == 19 && row['id_tipo_requerimiento'] == 2 && row['id_od'] == null) ? 
                    '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                    'data-placement="bottom" data-id="'+row['id_requerimiento']+'" data-cod="'+row['codigo']+'" title="Anular Requerimiento" >'+
                    '<i class="fas fa-trash"></i></button>' : '')+
                (
                    ((row['estado'] == 19 && row['id_tipo_requerimiento'] == 1 && row['sede_requerimiento'] == row['sede_orden'] && row['id_od'] == null) ||
                     (row['estado'] == 19 && row['id_tipo_requerimiento'] == 1 && row['sede_requerimiento'] !== row['sede_orden'] && row['id_transferencia'] !== null && row['id_od'] == null) || //compra con transferencia
                     (row['estado'] == 19 && row['id_tipo_requerimiento'] == 2 && row['confirmacion_pago'] == true && row['id_od'] == null)) ? //venta directa
                    ('<button type="button" class="despacho btn btn-success boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Generar Orden de Despacho" >'+
                    '<i class="fas fa-sign-in-alt"></i></button>') : '')
                }, targets: 14
            }
        ],
    });
   
}

$('#requerimientosPendientes tbody').on("click","button.detalle", function(){
    var data = $('#requerimientosPendientes').DataTable().row($(this).parents("tr")).data();
    console.log(data.id_requerimiento);
    open_detalle_requerimiento(data);
});

$('#requerimientosPendientes tbody').on("click","button.anular", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    var origen = 'despacho';
    openRequerimientoObs(id, cod, origen);
});

$('#requerimientosPendientes tbody').on("click","button.despacho", function(){
    var data = $('#requerimientosPendientes').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    open_despacho_create(data);
});

function listarOrdenesPendientes(){
    var vardataTables = funcDatatables();
    $('#ordenesDespacho').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarOrdenesDespacho',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_od'},
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
            {'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion'},
            {'data': 'ubigeo_descripcion', 'name': 'ubi_dis.descripcion'},
            {'data': 'direccion_destino'},
            {'data': 'fecha_despacho'},
            {'data': 'fecha_entrega'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'},
            {'render': 
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            },
            {'defaultContent': 
            '<button type="button" class="od_detalle btn btn-primary boton" data-toggle="tooltip" '+
            'data-placement="bottom" title="Ver Detalle" >'+
            '<i class="fas fa-list-ul"></i></button>'}
            // {'data': 'id_sede'}
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
        var data = $('#ordenesDespacho').DataTable().row($(this).parents("tr")).data();
        console.log('data.id_od'+data.id_od);
        open_detalle_despacho(data);
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

function listarGruposDespachados(){
    var vardataTables = funcDatatables();
    $('#gruposDespachados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarGruposDespachados',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_od_grupo_detalle'},
            {'data': 'codigo_odg', 'name': 'orden_despacho_grupo.codigo'},
            {'data': 'codigo_od', 'name': 'orden_despacho.codigo'},
            {'data': 'codigo_req', 'name': 'alm_req.codigo'},
            {'render': 
                function (data, type, row){
                    if (row['cliente_razon_social'] !== null){
                        return row['cliente_razon_social'];
                    } else if (row['cliente_persona'] !== null){
                        return row['cliente_persona'];
                    }
                }
            },
            {'data': 'concepto', 'name': 'alm_req.concepto'},
            {'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion'},
            {'data': 'ubigeo_descripcion', 'name': 'ubi_dis.descripcion'},
            {'data': 'direccion_destino', 'name': 'orden_despacho.direccion_destino'},
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
            // {'data': 'observaciones'},
            {'data': 'obs_confirmacion'},
            {'render': 
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            },
            {'render': 
                function (data, type, row){
                    return ('<button type="button" class="god_detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" >'+
                    '<i class="fas fa-list-ul"></i></button>'+
                    '<button type="button" class="imprimir btn btn-info boton" data-toggle="tooltip" '+
                    'data-placement="bottom" data-id-grupo="'+row['id_od_grupo']+'" title="Ver Despacho" >'+
                    '<i class="fas fa-file-alt"></i></button>'+
                    ((row['confirmacion'] == false && row['estado_od'] == 20)? 
                    ('<button type="button" class="conforme btn btn-success boton" data-toggle="tooltip" '+
                    'data-placement="bottom" data-id="'+row['id_od_grupo_detalle']+'" data-od="'+row['id_od']+'" title="Confirmar Entrega" >'+
                    '<i class="fas fa-check"></i></button>'+
                    '<button type="button" class="no_conforme btn btn-danger boton" data-toggle="tooltip" '+
                    'data-placement="bottom" data-id="'+row['id_od_grupo_detalle']+'" data-od="'+row['id_od']+'" data-cod-od="'+row['codigo_od']+'" data-concepto="'+row['concepto']+'" title="No Entregado" >'+
                    '<i class="fas fa-ban"></i></button>') : ''));
                }
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

$('#gruposDespachados tbody').on("click","button.god_detalle", function(){
    var data = $('#gruposDespachados').DataTable().row($(this).parents("tr")).data();
    console.log('data.id_od'+data.id_od_grupo);
    open_grupo_detalle(data);
});

$('#gruposDespachados tbody').on("click","button.imprimir", function(){
    var id_od_grupo = $(this).data('idGrupo');
    var id = encode5t(id_od_grupo);
    window.open('imprimir_despacho/'+id);
});

$('#gruposDespachados tbody').on("click","button.conforme", function(){
    var id_od_grupo_detalle = $(this).data('id');
    var id_od = $(this).data('od');
    var data = 'id_od_grupo_detalle='+id_od_grupo_detalle+
               '&id_od='+id_od;
    despacho_conforme(data);
});

$('#gruposDespachados tbody').on("click","button.no_conforme", function(){
    var id_od_grupo_detalle = $(this).data('id');
    var id_od = $(this).data('od');
    var cod_od = $(this).data('codOd');
    var concepto = $(this).data('concepto');

    $('#modal-despacho_obs').modal({
        show: true
    });

    $('[name=obs_id_od_grupo_detalle]').val(id_od_grupo_detalle);
    $('[name=obs_id_od]').val(id_od);
    $("#codigo_odg").text(cod_od +' - '+concepto+' - '+"No Entregado");
    $("#btnDespachoObs").removeAttr("disabled");
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
                $('#gruposDespachados').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function despacho_no_conforme(){
    var idg = $('[name=obs_id_od_grupo_detalle]').val();
    var ido = $('[name=obs_id_od]').val();
    var obs = $('[name=obs_confirmacion]').val();
    $('#btnDespachoObs').attr('disabled','true');

    var data = 'id_od_grupo_detalle='+idg+
               '&id_od='+ido+
               '&obs_confirmacion='+obs;
    $.ajax({
        type: 'POST',
        url: 'despacho_no_conforme',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                $('#modal-despacho_obs').modal('hide');
                $('#gruposDespachados').DataTable().ajax.reload();
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
        url: '/verDetalleGrupoDespacho/'+data.id_od_grupo,
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