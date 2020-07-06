function iniciar(permiso){
    $("#tab-ordenes section:first form").attr('form', 'formulario');
    listarDespachosPendientes(permiso);

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
            listarDespachosPendientes(permiso);
        } 
        else if (activeForm == "form-salidas"){
            listarDespachosEntregados(permiso);
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    vista_extendida();
}

function listarDespachosPendientes(permiso){
    var vardataTables = funcDatatables();
    $('#despachosPendientes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarOrdenesDespachoPendientes',
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
            }
            // {'defaultContent': 
            // }
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                if (permiso == '1') {
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" >'+
                    '<i class="fas fa-list-ul"></i></button>'+
                    (row['estado'] == 1 ? 
                    ('<button type="button" class="guia btn btn-warning boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Generar Guía" >'+
                        '<i class="fas fa-sign-in-alt"></i></button>') : '');
                } else {
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" >'+
                    '<i class="fas fa-list-ul"></i></button>'
                }
                }, targets: 12
            }
        ],
    });
}

$('#despachosPendientes tbody').on("click","button.detalle", function(){
    var data = $('#despachosPendientes').DataTable().row($(this).parents("tr")).data();
    console.log('data.id_od'+data.id_od);
    // var data = $(this).data('id');
    open_detalle_despacho(data);
});

$('#despachosPendientes tbody').on("click","button.guia", function(){
    var data = $('#despachosPendientes').DataTable().row($(this).parents("tr")).data();
    console.log('data.id_od'+data.id_od);
    open_guia_create(data);
});

function listarDespachosEntregados(permiso){
    var vardataTables = funcDatatables();
    $('#despachosEntregados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarSalidasDespacho',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_mov_alm'},
            {'data': 'codigo_od', 'name': 'orden_despacho.codigo'},
            {'render': 
                function (data, type, row){
                    if (row['razon_social'] !== null){
                        return row['razon_social'];
                    } else if (row['nombre_persona'] !== null){
                        return row['nombre_persona'];
                    }
                }
            },
            {'data': 'codigo_requerimiento', 'name': 'alm_req.codigo'},
            {'data': 'concepto', 'name': 'alm_req.concepto'},
            {'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion'},
            {'render': function (data, type, row){
                    return row['serie']+'-'+row['numero'];
                }
            },
            {'data': 'fecha_emision'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'}
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                    if (permiso == '1') {
                        return '<button type="button" class="salida btn btn-warning boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Salida" data-id="'+row['id_mov_alm']+'">'+
                            '<i class="fas fa-file-alt"></i></button>'+
                            '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Anular Salida" data-id="'+row['id_mov_alm']+'" data-guia="'+row['id_guia_ven']+'" data-od="'+row['id_od']+'">'+
                            '<i class="fas fa-trash"></i></button>';
                    } else {
                        return '<button type="button" class="salida btn btn-warning boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Salida" data-id="'+row['id_mov_alm']+'">'+
                            '<i class="fas fa-file-alt"></i></button>'
                    }
                }, targets: 9
                // '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                //     'data-placement="bottom" title="Ver Detalle" data-id="'+row.id_mov_alm+'">'+
                //     '<i class="fas fa-list-ul"></i></button>'+
            }
        ],
    });
}

$('#despachosEntregados tbody').on("click","button.salida", function(){
    var id_mov_alm = $(this).data('id');
    var id = encode5t(id_mov_alm);
    window.open('imprimir_salida/'+id);
});

$('#despachosEntregados tbody').on("click","button.anular", function(){
    var id_mov_alm = $(this).data('id');
    var id_guia = $(this).data('guia');
    var id_od = $(this).data('od');

    $('#modal-guia_ven_obs').modal({
        show: true
    });

    $('[name=id_salida]').val(id_mov_alm);
    $('[name=id_guia_ven]').val(id_guia);
    $('[name=id_od]').val(id_od);
    $('[name=observacion_guia_ven]').val('');

    $("#submitGuiaVenObs").removeAttr("disabled");
});

$("#form-guia_ven_obs").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    anular_salida(data);
});

function anular_salida(data){
    $("#submitGuiaVenObs").attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'anular_salida',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.length > 0){
                alert(response);
                $('#modal-guia_ven_obs').modal('hide');
            } else {
                alert('Salida Almacén anulada con éxito');
                $('#modal-guia_ven_obs').modal('hide');
                $('#despachosEntregados').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}