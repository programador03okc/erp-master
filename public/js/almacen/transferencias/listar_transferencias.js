let valor_permiso = null;
let usuario_session = null;

function iniciar(permiso, usuario){
    // clearDataTable();
    $("#tab-transferencias section:first form").attr('form', 'formulario');
    // $('[name=id_almacen_origen]').val(1);
    // $('[name=id_almacen_destino]').val(1);
    valor_permiso = permiso;
    usuario_session = usuario;

    listarTransferenciasPorEnviar();
    console.log(permiso);
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

        // clearDataTable();
        if (activeForm == "form-pendientes"){
            listarTransferenciasPendientes();
        } 
        else if (activeForm == "form-porEnviar"){
            listarTransferenciasPorEnviar();
        }
        else if (activeForm == "form-recibidas"){
            listarTransferenciasRecibidas();
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    vista_extendida();
}

function listarTransferenciasPendientes(){
    var alm_destino = $('[name=id_almacen_destino]').val();
    
    if (alm_destino !== '' && alm_destino !== ''){
        var vardataTables = funcDatatables();
        $('#listaTransferenciasPendientes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            // "scrollX": true,
            'bDestroy':true,
            'ajax' : 'listar_transferencias_pendientes/'+alm_destino,
            // 'ajax': {
            //     url:'listar_transferencias_pendientes/'+alm_origen+'/'+alm_destino,
            //     dataSrc:''
            // },
            'columns': [
                {'data': 'id_transferencia'},
                // {'data': 'codigo_transferencia'},
                {'render':
                    function (data, type, row){
                        return (formatDate(row['fecha_transferencia']));
                    }
                },
                {'data': 'codigo'},
                {'render':
                    function (data, type, row){
                        if (row['id_guia_ven'] !== null){
                            return ('<label class="lbl-codigo" title="Abrir Guía" onClick="abrir_guia_venta('+row['id_guia_ven']+')">'+row['guia_ven']+'</label>');
                        } else {
                            return '';
                        }
                    }
                },
                // {'render':
                //     function (data, type, row){
                //         return ('<label class="lbl-codigo" title="Abrir Guía" onClick="abrir_guia_compra('+row['id_guia_com']+')">'+row['guia_com']+'</label>');
                //     }
                // },
                // {'data': 'guia'},
                // {'data': 'fecha_guia'},
                {'data': 'alm_origen_descripcion'},
                {'data': 'alm_destino_descripcion'},
                {'data': 'nombre_origen'},
                {'data': 'nombre_destino'},
                // {'data': 'nombre_registro'},
                // {'data': 'estado_doc'},
                {'render':
                    function (data, type, row){
                        return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                    }
                },
                {'render':
                    function (data, type, row){
                        if (row['codigo_orden'] !== null){
                            return (row['codigo_orden']);
                        } else {
                            return '';
                        }
                    }
                },
                {'render':
                    function (data, type, row){
                        if (row['codigo_req'] !== null){
                            return (row['codigo_req']);
                        } 
                        else if (row['codigo_req_directo'] !== null){
                            return (row['codigo_req_directo']);
                        }
                        else {
                            return '';
                        }
                    }
                },
                {'render':
                    function (data, type, row){
                        if (row['concepto_req'] !== null){
                            return (row['concepto_req']);
                        }
                        else if (row['concepto_req_directo'] !== null){
                            return (row['concepto_req_directo']);
                        } 
                        else {
                            return '';
                        }
                    }
                },
                {'render':
                    function (data, type, row){
                        if (valor_permiso == '1') {
                            return (row['id_guia_ven'] !== null ? ('<button type="button" class="atender btn btn-success boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Recibir" >'+
                            '<i class="fas fa-share"></i></button>'+
                            '<button type="button" class="salida btn btn-primary boton" data-toggle="tooltip" '+
                            'data-placement="bottom" data-id-salida="'+row['id_salida']+'" title="Ver Salida" >'+
                            '<i class="fas fa-file-alt"></i></button>') : '');
                        } else {
                            return ''
                        }
                    }
                }
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
    }
    // atender("#listaTransferenciasPendientes tbody", $('#listaTransferenciasPendientes').DataTable());
    // verSalida("#listaTransferenciasPendientes tbody", $('#listaTransferenciasPendientes').DataTable());
    // anular("#listaTransferenciasPendientes tbody", $('#listaTransferenciasPendientes').DataTable());
}

$('#listaTransferenciasPendientes tbody').on("click","button.atender", function(){
    var data = $('#listaTransferenciasPendientes').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    if (data !== undefined){
        open_transferencia_detalle(data);
    }
});

$('#listaTransferenciasPendientes tbody').on("click","button.salida", function(){
    var idSalida = $(this).data('idSalida');
    if (idSalida !== ''){
        var id = encode5t(idSalida);
        window.open('imprimir_salida/'+id);
    }
});

function listarTransferenciasPorEnviar(){
    var alm_origen = $('[name=id_almacen_origen]').val();
    var vardataTables = funcDatatables();
    $('#listaTransferenciasPorEnviar').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarTransferenciasPorEnviar/'+alm_origen,
            type: 'POST'
        },
        'columns': [
            {'data': 'id_transferencia'},
            {'data': 'codigo'},
            {'data': 'fecha_registro'},
            {'data': 'alm_origen_descripcion', 'name': 'origen.descripcion'},
            {'data': 'alm_destino_descripcion', 'name': 'destino.descripcion'},
            {'data': 'cod_req', 'name': 'alm_req.codigo'},
            {'data': 'concepto', 'name': 'alm_req.concepto'},
            {'data': 'sede_descripcion', 'name': 'sis_sede.descripcion'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'}
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                    if (valor_permiso == '1') {
                        return `<button type="button" class="guia btn btn-success boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_transferencia']}" data-cod="${row['id_requerimiento']}" title="Generar Guía" >
                            <i class="fas fa-sign-in-alt"></i></button>`
                    // } else {
                    //     return `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                    //     data-placement="bottom" title="Ver Detalle" >
                    //     <i class="fas fa-list-ul"></i></button>`
                    }
                }, targets: 9
            }
        ],
    });
}
   
$('#listaTransferenciasPorEnviar tbody').on("click","button.guia", function(){
    var data = $('#listaTransferenciasPorEnviar').DataTable().row($(this).parents("tr")).data();
    console.log('data'+data);
    // var data = $(this).data('id');
    openGenerarGuia(data);
});

// $('#listaTransferenciasPorEnviar tbody').on("click","button.detalle", function(){
//     var data = $('#listaTransferenciasPorEnviar').DataTable().row($(this).parents("tr")).data();
//     console.log(data);
//     if (data !== undefined){
//         open_transferencia_detalle(data);
//     }
// });

function listarTransferenciasRecibidas(){
    var destino = $('[name=id_almacen_dest_recibida]').val();
    console.log('ori'+destino);
    
    if (destino !== '' && destino !== ''){
        var vardataTables = funcDatatables();
        $('#listaTransferenciasRecibidas').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            // "scrollX": true,
            'bDestroy':true,
            'ajax' : 'listar_transferencias_recibidas/'+destino,
            // 'ajax': {
            //     url:'listar_transferencias_pendientes/'+alm_origen+'/'+alm_destino,
            //     dataSrc:''
            // },
            'columns': [
                {'data': 'id_transferencia'},
                // {'data': 'codigo_transferencia'},
                {'render':
                    function (data, type, row){
                        return (formatDate(row['fecha_transferencia']));
                    }
                },
                {'data': 'codigo'},
                {'render':
                    function (data, type, row){
                        return ('<label class="lbl-codigo" title="Abrir Guía" onClick="abrir_guia_venta('+row['id_guia_ven']+')">'+row['guia_ven']+'</label>');
                    }
                },
                {'render':
                    function (data, type, row){
                        return ('<label class="lbl-codigo" title="Abrir Guía" onClick="abrir_guia_compra('+row['id_guia_com']+')">'+row['guia_com']+'</label>');
                    }
                },
                // {'data': 'fecha_guia'},
                {'data': 'alm_origen_descripcion'},
                {'data': 'alm_destino_descripcion'},
                {'data': 'nombre_origen'},
                {'data': 'nombre_destino'},
                // {'data': 'nombre_registro'},
                {'render':
                    function (data, type, row){
                        return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                    }
                },
                {'render':
                    function (data, type, row){
                        if (row['codigo_orden'] !== null){
                            return (row['codigo_orden']);
                        } else {
                            return '';
                        }
                    }
                },
                {'render':
                    function (data, type, row){
                        if (row['codigo_req'] !== null){
                            return (row['codigo_req']);
                        }
                        else if (row['codigo_req_directo'] !== null){
                            return (row['codigo_req_directo']);
                        } 
                        else {
                            return '';
                        }
                    }
                },
                {'render':
                    function (data, type, row){
                        if (row['concepto_req'] !== null){
                            return (row['concepto_req']);
                        } 
                        else if (row['concepto_req_directo'] !== null){
                            return (row['concepto_req_directo']);
                        } 
                        else {
                            return '';
                        }
                    }
                },
                {'render':
                    function (data, type, row){
                        if (valor_permiso == '1') {
                            return ('<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                                'data-placement="bottom" title="Ver Detalle" >'+
                                '<i class="fas fa-list-ul"></i></button>'+
                            '<button type="button" class="ingreso btn btn-warning boton" data-toggle="tooltip" '+
                                'data-placement="bottom" data-id-ingreso="'+row['id_ingreso']+'" title="Ver Ingreso" >'+
                                '<i class="fas fa-file-alt"></i></button>'+
                            '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                                'data-placement="bottom" data-id="'+row['id_transferencia']+'" data-guia="'+row['id_guia_com']+'" data-ing="'+row['id_ingreso']+'" title="Anular" >'+
                                '<i class="fas fa-trash"></i></button>');
                        } else {
                            return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Detalle" >'+
                            '<i class="fas fa-list-ul"></i></button>'
                        }
                    }
                }
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
    }
}

$('#listaTransferenciasRecibidas tbody').on("click","button.ingreso", function(){
    var idIngreso = $(this).data('idIngreso');
    if (idIngreso !== ''){
        var id = encode5t(idIngreso);
        window.open('imprimir_ingreso/'+id);
    }
});

$('#listaTransferenciasRecibidas tbody').on("click","button.detalle", function(){
    var data = $('#listaTransferenciasRecibidas').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    if (data !== undefined){
        open_transferencia_detalle(data);
    }
});

$('#listaTransferenciasRecibidas tbody').on("click","button.anular", function(){
    var id_transferencia = $(this).data('id');
    var id_mov_alm = $(this).data('ing');
    var id_guia = $(this).data('guia');

    if (id_transferencia !== null && id_mov_alm !== null && id_guia !== null){
        var c = confirm('¿Está seguro que desea anular la transferencia?');
        if (c){
            $('#modal-guia_com_obs').modal({
                show: true
            });

            $('[name=id_mov_alm]').val(id_mov_alm);
            $('[name=id_transferencia]').val(id_transferencia);
            $('[name=id_guia_com]').val(id_guia);
            $('[name=observacion]').val('');

            $("#submitGuiaObs").removeAttr("disabled");
        }
    }
});

$("#form-obs").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    anular_transferencia(data);
});

function anular_transferencia(data){
    $("#submitGuiaObs").attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'anular_transferencia_ingreso',
        data: data,
        dataType: 'JSON',
        success: function(response){
            if (response.length > 0){
                alert(response);
                $('#modal-guia_com_obs').modal('hide');
            } else {
                alert('Ingreso por Transferencia anulado con éxito');
                $('#modal-guia_com_obs').modal('hide');
                $('#listaTransferenciasRecibidas').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function abrir_guia_venta(id_guia_venta){
    // Abrir nuevo tab
    localStorage.setItem("id_guia_ven",id_guia_venta);
    let url ="/logistica/almacen/movimientos/guias-venta/index";
    var win = window.open(url, '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
    // location.assign("/logistica/almacen/movimientos/guias-venta/index");
}

function abrir_guia_compra(id_guia_compra){
    // Abrir nuevo tab
    localStorage.setItem("id_guia_com",id_guia_compra);
    let url ="/logistica/almacen/movimientos/guias-compra/index";
    var win = window.open(url, '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
    // location.assign("guia_compra");
}