let oc_seleccionadas = [];
let oc_det_seleccionadas = [];
let acceso = null;

function iniciar(permiso){
    $("#tab-ordenes section:first form").attr('form', 'formulario');
    acceso = permiso;
    listarOrdenesPendientes();
    oc_seleccionadas = [];

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
            listarOrdenesPendientes();
        } 
        else if (activeForm == "form-transformaciones"){
            listarTransformaciones();
        }
        else if (activeForm == "form-ingresadas"){
            listarOrdenesEntregadas();
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    vista_extendida();
}

var table;

function listarOrdenesPendientes(){
    var vardataTables = funcDatatables();
    table = $('#ordenesPendientes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarOrdenesPendientes',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_orden_compra'},
            {'data': 'codigo_softlink'},
            {'data': 'codigo'},
            {'render': function (data, type, row){
                var dias_restantes = restarFechas(fecha_actual(), sumaFecha(row['plazo_entrega'], row['fecha']));
                // var dias_restantes = 3;
                var porc = dias_restantes * 100 / parseFloat(row['plazo_entrega']);
                var color = (porc > 50 ? 'success' : ((porc <= 50 && porc > 20) ? 'warning' : 'danger'));
                return `<div class="progress-group">
                            <span class="progress-text">Nro días Restantes</span>
                            <span class="float-right"><b> ${dias_restantes}</b> / ${row['plazo_entrega']}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-${color}" style="width: ${porc}%"></div>
                            </div>
                        </div>`;
                }
            },
            {'data': 'sede_descripcion', 'name': 'sis_sede.descripcion'},
            {'data': 'razon_social', 'name': 'adm_contri.razon_social'},
            // {'data': 'codigo_softlink', 'name': 'log_ord_compra.codigo_softlink'},
            {'data': 'fecha'},
            // {'data': 'codigo_requerimiento', 'name': 'alm_req.codigo'},
            // {'data': 'concepto', 'name': 'alm_req.concepto'},
            // {'data': 'fecha_entrega', 'name': 'alm_req.fecha_entrega'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'},
            {'render': function (data, type, row){
                return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                }
            },
            // {'render': 
            //     function (data, type, row){
            //         if (acceso == '1') {
            //             return '<button type="button" class="ver-detalle btn btn-primary boton" data-toggle="tooltip" '+
            //             'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_orden_compra']+'">'+
            //             '<i class="fas fa-chevron-down"></i></button>'+
            //             // '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
            //             //     'data-placement="bottom" title="Ver Detalle" >'+
            //             //     '<i class="fas fa-list-ul"></i></button>'+
            //             '<button type="button" class="guia btn btn-info boton" data-toggle="tooltip" '+
            //                 'data-placement="bottom" title="Generar Guía" >'+
            //                 '<i class="fas fa-sign-in-alt"></i></button>';
            //         } else {
            //             return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
            //                 'data-placement="bottom" title="Ver Detalle" >'+
            //                 '<i class="fas fa-list-ul"></i></button>';
            //         }
            //     }
            // }
        ],
        'drawCallback': function(){
            $('#ordenesPendientes tbody tr td input[type="checkbox"]').iCheck({
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
            },
            {'render': 
                function (data, type, row){
                    if (acceso == '1') {
                        return `<button type="button" class="ver-detalle btn btn-primary boton" data-toggle="tooltip" 
                            data-placement="bottom" title="Ver Detalle" data-id="${row['id_orden_compra']}">
                            <i class="fas fa-chevron-down"></i></button>
                        <button type="button" class="guia btn btn-info boton" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Guía" >
                            <i class="fas fa-sign-in-alt"></i></button>`;
                    } else {
                        return '<button type="button" class="ver-detalle btn btn-primary boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_orden_compra']+'">'+
                        '<i class="fas fa-chevron-down"></i></button>';
                    }
                }, targets: 9
            }
         ],
        'select': 'multi',
        'order': [[1, 'asc']]
    });
    
    $($('#ordenesPendientes').DataTable().table().container()).on('ifChanged', '.dt-checkboxes', function(event){
        var cell = $('#ordenesPendientes').DataTable().cell($(this).closest('td'));
        cell.checkboxes.select(this.checked);
    
        var data = $('#ordenesPendientes').DataTable().row($(this).parents("tr")).data();
        console.log(this.checked);
    
        if (data !== null && data !== undefined){
            if (this.checked){
                oc_seleccionadas.push(data);
            }
            else {
                var index = oc_seleccionadas.findIndex(function(item, i){
                    return item.id_orden_compra == data.id_orden_compra;
                });
                if (index !== null){
                    oc_seleccionadas.splice(index,1);
                }
            }
        }
    });
}

// botones('#ordenesPendientes tbody',$('#ordenesPendientes').DataTable());
$('#ordenesPendientes tbody').on("click","button.detalle", function(){
    var data = $('#ordenesPendientes').DataTable().row($(this).parents("tr")).data();
    console.log('data.id_orden_compra'+data.id_orden_compra);
    // var data = $(this).data('id');
    open_detalle(data);
});

$('#ordenesPendientes tbody').on("click","button.guia", function(){
    var data = $('#ordenesPendientes').DataTable().row($(this).parents("tr")).data();
    console.log('data.id_orden_compra'+data.id_orden_compra);
    open_guia_create(data);
});

function listarTransformaciones(){
    var vardataTables = funcDatatables();
    $('#listaTransformaciones').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarTransformacionesProcesadas',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_transformacion'},
            {'data': 'orden_am', 'name': 'oc_propias.orden_am'},
            {'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad'},
            {'data': 'oportunidad', 'name': 'oportunidades.oportunidad'},
            {'data': 'nombre', 'name': 'entidades.nombre'},
            {'data': 'codigo'},
            {'data': 'fecha_transformacion', 'name': 'transformacion.fecha_transformacion'},
            {'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion'},
            {'data': 'nombre_responsable', 'name': 'sis_usua.nombre_corto'},
            {'data': 'cod_od', 'name': 'orden_despacho.codigo'},
            {'data': 'cod_req', 'name': 'alm_req.codigo'},
            {'render': function(data, type, row){
                    return (row['serie'] !== null ? (row['serie']+'-'+row['numero']) : '');
                }
            },
            {'data': 'observacion', 'name': 'transformacion.observacion'},
            {'render': 
                function (data, type, row){
                    if (acceso == '1') {
                        return '<button type="button" class="guia btn btn-info boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ingresar Guía" >'+
                            '<i class="fas fa-sign-in-alt"></i></button>'+
                            '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Detalle" >'+
                            '<i class="fas fa-list-ul"></i></button>';
                    } else {
                        return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Detalle" >'+
                            '<i class="fas fa-list-ul"></i></button>';
                    }
                }
            }
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'}
         ],
        'order': [[1, 'asc']]
    });
}

$('#listaTransformaciones tbody').on("click","button.guia", function(){
    var data = $('#listaTransformaciones').DataTable().row($(this).parents("tr")).data();
    open_transformacion_guia_create(data);
});

function listarOrdenesEntregadas(){
    var vardataTables = funcDatatables();
    $('#ordenesEntregadas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarOrdenesEntregadas',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_mov_alm'},
            // {'data': 'sede_guia_descripcion', 'name': 'sede_guia.descripcion'},
            {'render': function (data, type, row){
                    return row['serie']+'-'+row['numero'];
                }
            },
            {'data': 'nro_documento', 'name': 'adm_contri.nro_documento'},
            {'data': 'razon_social', 'name': 'adm_contri.razon_social'},
            {'render': function (data, type, row){
                    return (row['codigo'] !== null ? 
                    ('<label class="lbl-codigo" title="Abrir Ingreso" onClick="abrir_ingreso('+row['id_mov_alm']+')">'+row['codigo']+'</label>')
                    : '');
                }
            },
            {'data': 'operacion_descripcion', 'name': 'tp_ope.descripcion'},
            {'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion'},
            {'data': 'fecha_emision'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'}
        ],
        "order": [[ 0, "desc" ]],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': 
                function (data, type, row){
                    if (acceso == '1') {
                        return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_guia_com']+'" data-cod="'+row['codigo']+'">'+
                            '<i class="fas fa-list-ul"></i></button>'+
                        // '<button type="button" class="ingreso btn btn-warning boton" data-toggle="tooltip" '+
                        //     'data-placement="bottom" title="Ver Ingreso" data-id="'+row['id_mov_alm']+'">'+
                        //     '<i class="fas fa-file-alt"></i></button>'+
                        '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Anular Ingreso" data-id="'+row['id_mov_alm']+'" data-guia="'+row['id_guia_com']+'" data-oc="'+row['id_orden_compra']+'">'+
                        '<i class="fas fa-trash"></i></button>'+
                        `<button type="button" class="cambio btn btn-warning boton" data-toggle="tooltip" 
                            data-placement="bottom" title="Cambiar Serie-Número" data-id="${row['id_mov_alm']}" 
                            data-guia="${row['id_guia_com']}"><i class="fas fa-sync-alt"></i></button>
                            
                        <button type="button" class="transferencia btn btn-success boton" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Transferencia" data-guia="${row['id_guia_com']}">
                            <i class="fas fa-exchange-alt"></i></button>`+

                        (row['id_operacion'] == 2 ? 
                        `<button type="button" class="${row['count_facturas']>0?'ver_doc':'doc'} btn btn-${row['count_facturas']>0?'info':'default'} boton" data-toggle="tooltip" 
                            data-placement="bottom" title="Generar Factura" data-guia="${row['id_guia_com']}">
                            <i class="fas fa-file-medical"></i></button>`:'')
                        ;
                    } else {
                        return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_mov_alm']+'" data-cod="'+row['codigo']+'">'+
                            '<i class="fas fa-list-ul"></i></button>'+
                        '<button type="button" class="ingreso btn btn-warning boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Ingreso" data-id="'+row['id_mov_alm']+'">'+
                            '<i class="fas fa-file-alt"></i></button>'
                    }
                }, targets: 9
            }    
        ],
    });
}

$('#ordenesEntregadas tbody').on("click","button.transferencia", function(){
    var id_guia_com = $(this).data('guia');
    // console.log(data);
    ver_transferencia(id_guia_com);
});

$('#ordenesEntregadas tbody').on("click","button.detalle", function(){
    var id_guia_com = $(this).data('id');
    var codigo = $(this).data('cod');
    // console.log(data);
    open_detalle_movimiento(id_guia_com, codigo);
});

function abrir_ingreso(id_mov_alm){
    var id = encode5t(id_mov_alm);
    window.open('imprimir_ingreso/'+id);
}

$('#ordenesEntregadas tbody').on("click","button.anular", function(){
    var id_mov_alm = $(this).data('id');
    var id_guia = $(this).data('guia');
    var id_oc = $(this).data('oc');

    $('#modal-guia_com_obs').modal({
        show: true
    });

    $('[name=id_mov_alm]').val(id_mov_alm);
    $('[name=id_guia_com]').val(id_guia);
    $('[name=id_oc]').val(id_oc);
    $('[name=observacion]').val('');

    $("#submitGuiaObs").removeAttr("disabled");
});

$('#ordenesEntregadas tbody').on("click","button.cambio", function(){
    var id_mov_alm = $(this).data('id');
    var id_guia = $(this).data('guia');

    $('#modal-guia_com_cambio').modal({
        show: true
    });

    $('[name=id_ingreso]').val(id_mov_alm);
    $('[name=id_guia_com]').val(id_guia);
    $('[name=serie_nuevo]').val('');
    $('[name=numero_nuevo]').val('');

    $("#submit_guia_com_cambio").removeAttr("disabled");
});

// $('#ordenesEntregadas tbody').on("click","button.transferencia", function(){
//     var id_guia = $(this).data('id');
//     generar_transferencia(id_guia);
// });

$('#ordenesEntregadas tbody').on("click","button.anular_sal", function(){
    var id_mov_alm = $(this).data('id');
    var id_guia = $(this).data('guia');
    var id_trans = $(this).data('trans');

    $('#modal-guia_ven_obs').modal({
        show: true
    });

    $('[name=id_salida]').val(id_mov_alm);
    $('[name=id_guia_ven]').val(id_guia);
    $('[name=id_trans]').val(id_trans);
    $('[name=observacion_guia_ven]').val('');

    $("#submitGuiaVenObs").removeAttr("disabled");
});

$('#ordenesEntregadas tbody').on("click","button.doc", function(){
    var id_guia = $(this).data('guia');
    open_doc_create(id_guia);
});

$('#ordenesEntregadas tbody').on("click","button.ver_doc", function(){
    var id_guia = $(this).data('guia');
    documentosVer(id_guia);
});

// $("#form-guia_ven_obs").on("submit", function(e){
//     console.log('submit');
//     e.preventDefault();
//     var data = $(this).serialize();
//     console.log(data);
//     anular_transferencia_salida(data);
// });

// function anular_transferencia_salida(data){
//     $("#submitGuiaVenObs").attr('disabled','true');
//     $.ajax({
//         type: 'POST',
//         url: 'anular_transferencia_salida',
//         data: data,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             if (response.length > 0){
//                 alert(response);
//                 $('#modal-guia_ven_obs').modal('hide');
//             } else {
//                 alert('Salida Almacén anulada con éxito');
//                 $('#modal-guia_ven_obs').modal('hide');
//                 $('#ordenesEntregadas').DataTable().ajax.reload();
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function open_detalle(data){
    $('#modal-ordenDetalle').modal({
        show: true
    });
    $('#cabecera_orden').text(data.codigo_orden+' - '+data.razon_social);
    listar_detalle_orden(data.id_orden_compra);
}

function cargar_almacenes(sede){
    if (sede !== ''){
        $.ajax({
            type: 'GET',
            url: 'cargar_almacenes/'+sede,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                var option = '';
                for (var i=0; i<response.length; i++){
                    if (response.length == 1){
                        option+='<option data-id-sede="'+response[i].id_sede+'" data-id-empresa="'+response[i].id_empresa+'" value="'+response[i].id_almacen+'" selected>'+response[i].codigo+' - '+response[i].descripcion+'</option>';

                    } else {
                        option+='<option data-id-sede="'+response[i].id_sede+'" data-id-empresa="'+response[i].id_empresa+'" value="'+response[i].id_almacen+'">'+response[i].codigo+' - '+response[i].descripcion+'</option>';

                    }
                }
                $('[name=id_almacen]').html(option);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function open_guias(data){
    $('#modal-guias').modal({
        show: true
    });
    $('#cabecera_orden').text(data.codigo_orden+' - '+data.razon_social+' - Total: '+data.simbolo+data.monto_total);
    listar_guias_orden(data.id_orden_compra);
}

function listar_detalle_orden(id_orden){
    console.log('id_orden',id_orden);
    $.ajax({
        type: 'GET',
        url: 'detalleOrden/'+id_orden,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response.forEach(element => {
                html+='<tr id="'+element.id_detalle_orden+'">'+
                '<td>'+i+'</td>'+
                '<td>'+element.codigo+'</td>'+
                '<td>'+element.part_number+'</td>'+
                '<td>'+element.categoria+'</td>'+
                '<td>'+element.subcategoria+'</td>'+
                '<td>'+element.descripcion+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                '<td>'+element.abreviatura+'</td>'+
                '<td><span class="label label-'+element.bootstrap_color+'">'+element.estado_doc+'</span></td>'+
                '</tr>';
                i++;
            });
            $('#detalleOrden tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_guias_orden(id_orden){
    $.ajax({
        type: 'GET',
        url: 'verGuiasOrden/'+id_orden,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response.forEach(element => {
                html+='<tr id="'+element.id_guia_com_oc+'">'+
                '<td>'+i+'</td>'+
                '<td><label class="lbl-codigo" title="Abrir Guía" onClick="abrir_guia_compra('+element.id_guia_com+')">'+element.serie+'-'+element.numero+'</label></td>'+
                '<td>'+element.fecha_emision+'</td>'+
                '<td>'+element.almacen+'</td>'+
                '<td>'+element.operacion+'</td>'+
                '<td>'+element.nombre_responsable+'</td>'+
                '<td>'+element.nombre_registrado_por+'</td>'+
                '<td><span class="label label-'+element.bootstrap_color+'">'+element.estado_doc+'</span></td>'+
                '</tr>';
                i++;
            });
            $('#guiasOrden tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function abrir_guia_compra(id_guia_compra){
    console.log('abrir_guia_compra()');
    localStorage.setItem("id_guia_com",id_guia_compra);
    location.assign("guia_compra");
}

function ceros_numero(numero){
    if (numero == 'numero'){
        var num = $('[name=numero]').val();
        $('[name=numero]').val(leftZero(7,num));
    }
    else if(numero == 'serie'){
        var num = $('[name=serie]').val();
        $('[name=serie]').val(leftZero(4,num));
    }
}


$("#form-obs").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    anular_ingreso(data);
});

function anular_ingreso(data){
    $("#submitGuiaObs").attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'anular_ingreso',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.length > 0){
                alert(response);
                $('#modal-guia_com_obs').modal('hide');
            } else {
                alert('Ingreso Almacén anulado con éxito');
                $('#modal-guia_com_obs').modal('hide');
                $('#ordenesEntregadas').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function generar_transferencia(id_guia){
//     $.ajax({
//         type: 'GET',
//         url: 'transferencia/'+id_guia,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             alert(response);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }