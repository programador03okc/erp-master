function iniciar(permiso){
    $("#tab-ordenes section:first form").attr('form', 'formulario');
    listarDespachosPendientes(permiso);

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
        // "scrollX": true,
        'ajax': {
            url: 'listarOrdenesDespachoPendientes',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_od'},
            {'render': 
                function (data, type, row){
                    if (row['aplica_cambios']){
                        return '<span class="label label-danger">Despacho Interno</span>';
                    } else {
                        return '<span class="label label-primary">Despacho Externo</span>';
                    }
                }
            },
            {'data': 'fecha_entrega'},
            {'data': 'hora_despacho'},
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
            {'data': 'fecha_despacho'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'}
        ],
        'order': [[ 2, "desc" ],[ 3, "desc" ]],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                if (permiso == '1') {
                    return `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                        data-placement="bottom" title="Ver Detalle" >
                        <i class="fas fa-list-ul"></i></button>`+
                    (row['estado'] == 1 ? 
                    (`<button type="button" class="guia btn btn-warning boton" data-toggle="tooltip" 
                        data-placement="bottom" title="Generar Guía" >
                        <i class="fas fa-sign-in-alt"></i></button>
                    <button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" 
                        data-placement="bottom" title="Anular Orden de Despacho" data-id="${row['id_od']}">
                        <i class="fas fa-trash"></i></button>`) : '');
                } else {
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" >'+
                    '<i class="fas fa-list-ul"></i></button>'
                }
                }, targets: 11
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

$('#despachosPendientes tbody').on("click","button.anular", function(){
    var id = $(this).data('id');
    var msj = confirm('¿Está seguro que desea anular la Orden de Despacho ?');
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
                $('#despachosPendientes').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listarDespachosEntregados(permiso){
    var vardataTables = funcDatatables();
    $('#despachosEntregados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarSalidasDespacho',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_mov_alm'},
            {'data': 'fecha_emision'},
            {'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion'},
            {'data': 'codigo_od', 'name': 'orden_despacho.codigo'},
            {'data': 'codigo'},
            {'render': function (data, type, row){
                    return row['serie']+'-'+row['numero'];
                }
            },
            {'data': 'codigo_requerimiento', 'name': 'alm_req.codigo'},
            {'render': 
                function (data, type, row){
                    if (row['razon_social'] !== null){
                        return row['razon_social'];
                    } else if (row['nombre_persona'] !== null){
                        return row['nombre_persona'];
                    }
                }
            },
            {'data': 'concepto', 'name': 'alm_req.concepto'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'}
        ],
        'order': [[ 1, "desc" ]],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                    if (permiso == '1') {
                        return `<button type="button" class="salida btn btn-warning boton" data-toggle="tooltip" 
                            data-placement="bottom" title="Ver Salida" data-id="${row['id_mov_alm']}">
                            <i class="fas fa-file-alt"></i></button>

                            <button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" 
                                data-placement="bottom" title="Anular Salida" data-id="${row['id_mov_alm']}" data-guia="${row['id_guia_ven']}"
                                data-od="${row['id_od']}"><i class="fas fa-trash"></i></button>
                                
                            <button type="button" class="cambio btn btn-success boton" data-toggle="tooltip" 
                                data-placement="bottom" title="Cambiar Serie-Número" data-id="${row['id_mov_alm']}" data-guia="${row['id_guia_ven']}"
                                data-od="${row['id_od']}"><i class="fas fa-sync-alt"></i></button>`;
                    } else {
                        return '<button type="button" class="salida btn btn-warning boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Salida" data-id="'+row['id_mov_alm']+'">'+
                            '<i class="fas fa-file-alt"></i></button>'
                    }
                }, targets: 10
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

$('#despachosEntregados tbody').on("click","button.cambio", function(){
    var id_mov_alm = $(this).data('id');
    var id_guia = $(this).data('guia');
    var id_od = $(this).data('od');

    $('#modal-guia_ven_cambio').modal({
        show: true
    });

    $('[name=id_salida]').val(id_mov_alm);
    $('[name=id_guia_ven]').val(id_guia);
    $('[name=id_od]').val(id_od);

    $("#submit_guia_ven_cambio").removeAttr("disabled");
});