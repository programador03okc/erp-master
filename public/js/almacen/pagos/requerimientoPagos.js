class RequerimientoPago
{
    constructor(permisoConfirmarDenegarPago)
    {
        this.permisoConfirmarDenegarPago=permisoConfirmarDenegarPago;
        this.listarRequerimientosPendientes();
        this.listarRequerimientosConfirmados();
    }

    listarRequerimientosPendientes() {
        const permisoConfirmarDenegarPago=this.permisoConfirmarDenegarPago;
        var vardataTables = funcDatatables();
        $('#requerimientosPendientes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'destroy' : true,
            'serverSide' : true,
            'ajax': {
                url: 'listarRequerimientosPendientesPagos',
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
                {'render': function (data, type, row){
                    return row['simbolo']+' '+row['monto'];
                    }
                },
                {'render': function (data, type, row){
                    return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                    }
                }
            ],
            'columnDefs': [
                {'aTargets': [0], 'sClass': 'invisible'},
                {'render': function (data, type, row){
                    //const accion = permisoConfirmarDenegarPago;
                    //console.log(permisoConfirmarDenegarPago == '1');
                    if (permisoConfirmarDenegarPago !== '1') {
                        return `<button style="padding-left:2px;" type="button" class="detalle btn btn-primary boton" data-toggle="tooltip"
                        data-placement="bottom" title="Ver Detalle">
                        <i class="fas fa-list-ul"></i></button>`;
                    } else {
                        return `
                        <div>
                        <button type="button" style="padding-left:8px;padding-right:7px;" class="adjunto btn btn-warning boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_requerimiento']}" title="Ver Adjuntos" >
                            <i class="fas fa-file-download"></i></button>
                        <button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                            data-placement="bottom" title="Ver Detalle" >
                            <i class="fas fa-list-ul"></i></button>
                            </div>
                            <div>
                        <button type="button" class="conforme btn btn-success boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_requerimiento']}" data-cod="${row['codigo']}" data-concepto="${row['concepto']}" title="Confirmar Entrega" >
                            <i class="fas fa-check"></i></button>
                        <button type="button" class="no_conforme btn btn-danger boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_requerimiento']}" data-cod="${row['codigo']}" data-concepto="${row['concepto']}" title="Descartar Requerimiento" >
                            <i class="fas fa-ban"></i></button>
                            </div>
                            `;
                    }
                    }, targets: 10
                }
            ],
        });
    }

    listarRequerimientosConfirmados(){
        var vardataTables = funcDatatables();
        $('#requerimientosConfirmados').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'destroy' : true,
            'serverSide' : true,
            'ajax': {
                url: 'listarRequerimientosConfirmadosPagos',
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
                    return (row['confirmacion_pago'] ? 'PAGO CONFIRMADO' : 'PAGO NO CONFIRMADO');
                    }
                },
                {'data': 'obs_confirmacion'}
            ],
            'columnDefs': [
                {'aTargets': [0], 'sClass': 'invisible'},
                {'render': function (data, type, row){
                    return (row['estado'] !== 7 ? ('<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver Detalle" >'+
                        '<i class="fas fa-list-ul"></i></button>') : '')
                    }, targets: 11
                }
            ],
        });
    }
}

function iniciar(permisoConfirmarDenegarPago) {

    //$("#tab-reqPendientes section:first form").attr('form', 'formulario');
    //Se llama al iniciar
    listarRequerimientosPendientes(permisoConfirmarDenegarPago);

    $('ul.nav-tabs li a').click(function(){
        /*$('ul.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.content-tabs section').attr('hidden', true);
        $('.content-tabs section form').removeAttr('type');
        $('.content-tabs section form').removeAttr('form');*/

        var activeTab = $(this).attr('type');
        var activeForm = "form-"+activeTab.substring(1);
        
        $("#"+activeForm).attr('type', 'register');
        $("#"+activeForm).attr('form', 'formulario');
        changeStateInput(activeForm, true);

        clearDataTable();
        if (activeForm == "form-pendientes"){
            //Si ya se llamo al iniciar, no deberia verificarse si cumple algun requisito porque ya se llamo
            //Si quieres que se actualice la data, ya vimos que la funcion es ... $('#tabla').DataTable().ajax.reload();
            //Lo que hace esa funcion es volver a solicitar data del servidor, no hace falta volver a iniciar toda la estructura
            //del datatable, no hace falta volver a llamar esa funcion
            //parece que te confunde aun la forma de trabajar, los conceptos, necesitas un cursito rapido
            listarRequerimientosPendientes(permisoConfirmarDenegarPago);
            // $('#requerimientosPendientes').DataTable().ajax.reload();
        } 
        else if (activeForm == "form-confirmados"){
            listarRequerimientosConfirmados();
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    vista_extendida();
}



$('#requerimientosPendientes tbody').on("click","button.adjunto", function(){
    var id_requerimiento = $(this).data('id');
    $('#modal-requerimientoAdjuntos').modal({
        show: true
    });
    verRequerimientoAdjuntos(id_requerimiento);
});

$('#requerimientosPendientes tbody').on("click","button.detalle", function(){
    var data = $('#requerimientosPendientes').DataTable().row($(this).parents("tr")).data();
    console.log(data.id_requerimiento);
    open_detalle_requerimiento(data);
});

$('#requerimientosPendientes tbody').on("click","button.conforme", function(){
    var id_requerimiento = $(this).data('id');
    var codigo = $(this).data('cod');
    var concepto = $(this).data('concepto');

    $('#modal-requerimiento_obs').modal({
        show: true
    });

    $('[name=obs_id_requerimiento]').val(id_requerimiento);
    $('[name=boton_origen]').val('confirmado');
    $('[name=obs_motivo]').val('');
    $("#cabecera_req").text(codigo +' - '+concepto+' - '+"Pago Confirmado");
    $("#btnRequerimientoObs").removeAttr("disabled");
});

$('#requerimientosPendientes tbody').on("click","button.no_conforme", function(){
    var id_requerimiento = $(this).data('id');
    var codigo = $(this).data('cod');
    var concepto = $(this).data('concepto');

    $('#modal-requerimiento_obs').modal({
        show: true
    });

    $('[name=obs_id_requerimiento]').val(id_requerimiento);
    $('[name=boton_origen]').val('no_confirmado');
    $('[name=obs_motivo]').val('');
    $("#cabecera_req").text(codigo +' - '+concepto+' - '+"Pago No Confirmado");
    $("#btnRequerimientoObs").removeAttr("disabled");
});

$("#form-requerimiento_obs").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    confirmacion_pago(data);
});

function verRequerimientoAdjuntos(id_requerimiento){
    $.ajax({
        type: 'GET',
        url: 'verRequerimientoAdjuntos/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#listaAdjuntosRequerimiento tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

function confirmacion_pago(data){
    var origen = $('[name=boton_origen]').val();
    $('#btnRequerimientoObs').attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'pago_'+origen,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                $('#modal-requerimiento_obs').modal('hide');
                $('#requerimientosPendientes').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
   
$('#requerimientosConfirmados tbody').on("click","button.detalle", function(){
    var data = $('#requerimientosConfirmados').DataTable().row($(this).parents("tr")).data();
    console.log(data.id_requerimiento);
    open_detalle_requerimiento(data);
});
