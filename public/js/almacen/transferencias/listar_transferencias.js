$(function(){
    // clearDataTable();
    $("#tab-transferencias section:first form").attr('form', 'formulario');
    $('[name=id_almacen_ori]').val(1);
    listarTransferenciasPendientes();

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
            listarTransferenciasPendientes();
        } 
        else if (activeForm == "form-recibidas"){
            listarTransferenciasRecibidas();
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    vista_extendida();
});

function listarTransferenciasPendientes(){
    var alm_origen = $('[name=id_almacen_ori]').val();
    // var alm_destino = $('[name=id_almacen_des]').val();
    console.log('ori'+alm_origen);
    
    if (alm_origen !== '' && alm_origen !== ''){
        var vardataTables = funcDatatables();
        $('#listaTransferenciasPendientes').DataTable({
            'destroy':true,
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'ajax' : 'listar_transferencias_pendientes/'+alm_origen,
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
                // {'data': 'guia'},
                {'data': 'fecha_guia'},
                {'data': 'alm_origen_descripcion'},
                {'data': 'alm_destino_descripcion'},
                {'data': 'nombre_origen'},
                {'data': 'nombre_destino'},
                {'data': 'nombre_registro'},
                // {'data': 'estado_doc'},
                {'render':
                    function (data, type, row){
                        return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                    }
                },
                {'render':
                    function (data, type, row){
                        return ('<button type="button" class="atender btn btn-success boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Atender" >'+
                        '<i class="fas fa-share"></i></button>'+
                    '<button type="button" class="salida btn btn-primary boton" data-toggle="tooltip" '+
                        'data-placement="bottom" data-id-salida="'+row['id_salida']+'" title="Ver Salida" >'+
                        '<i class="fas fa-file-alt"></i></button>'+
                    '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Anular" >'+
                        '<i class="fas fa-trash"></i></button>');
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

$('#listaTransferenciasPendientes tbody').on("click","button.anular", function(){
    var data = $('#listaTransferenciasPendientes').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    if (data !== undefined){
        var c = confirm('¿Está seguro que desea anular la transferencia?');
        if (c){
            anular(data);
        }
    }
});

function anular(data){
    if (data.guia_com == '-'){
        $.ajax({
            type: 'GET',
            url: 'anular_transferencia/'+data.id_transferencia,
            dataType: 'JSON',
            success: function(response){
                if (response > 0){
                    alert('Transferencia anulada con éxito');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('No se puede anular por que ya tiene Ingreso a Almacén.');
    }
}

function listarTransferenciasRecibidas(){
    var origen = $('[name=id_almacen_ori_recibida]').val();
    console.log('ori'+origen);
    
    if (origen !== '' && origen !== ''){
        var vardataTables = funcDatatables();
        $('#listaTransferenciasRecibidas').DataTable({
            'destroy':true,
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'ajax' : 'listar_transferencias_recibidas/'+origen,
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
                {'data': 'fecha_guia'},
                {'data': 'alm_origen_descripcion'},
                {'data': 'alm_destino_descripcion'},
                {'data': 'nombre_origen'},
                {'data': 'nombre_destino'},
                {'data': 'nombre_registro'},
                {'render':
                    function (data, type, row){
                        return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                    }
                },
                {'render':
                    function (data, type, row){
                        return ('<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver Detalle" >'+
                        '<i class="fas fa-list-ul"></i></button>'+
                    '<button type="button" class="ingreso btn btn-warning boton" data-toggle="tooltip" '+
                        'data-placement="bottom" data-id-ingreso="'+row['id_ingreso']+'" title="Ver Ingreso" >'+
                        '<i class="fas fa-file-alt"></i></button>');
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

function abrir_guia_venta(id_guia_venta){
    console.log('abrir_guia_venta()');
    localStorage.setItem("id_guia_ven",id_guia_venta);
    location.assign("guia_venta");
}

function abrir_guia_compra(id_guia_compra){
    console.log('abrir_guia_compra()');
    localStorage.setItem("id_guia_com",id_guia_compra);
    location.assign("guia_compra");
}