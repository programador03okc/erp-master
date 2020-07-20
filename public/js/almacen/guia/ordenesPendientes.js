function iniciar(permiso){
    $("#tab-ordenes section:first form").attr('form', 'formulario');
    listarOrdenesPendientes(permiso);

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
            listarOrdenesPendientes(permiso);
        } 
        else if (activeForm == "form-ingresadas"){
            listarOrdenesEntregadas(permiso);
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    vista_extendida();
}

function listarOrdenesPendientes(permiso){
    var vardataTables = funcDatatables();
    $('#ordenesPendientes').DataTable({
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
            {'data': 'codigo'},
            {'data': 'nro_documento', 'name': 'adm_contri.nro_documento'},
            {'data': 'razon_social', 'name': 'adm_contri.razon_social'},
            {'data': 'codigo_softlink', 'name': 'log_ord_compra.codigo_softlink'},
            {'data': 'fecha'},
            {'data': 'codigo_requerimiento', 'name': 'alm_req.codigo'},
            {'data': 'concepto', 'name': 'alm_req.concepto'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'},
            // {'data': 'simbolo'},
            // {'data': 'monto_subtotal'},
            // {'data': 'monto_igv'},
            // {'data': 'monto_total'}
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                if (permiso == '1') {
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver Detalle" >'+
                        '<i class="fas fa-list-ul"></i></button>'+
                    '<button type="button" class="guia btn btn-info boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Generar Guía" >'+
                        '<i class="fas fa-sign-in-alt"></i></button>';
                } else {
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver Detalle" >'+
                        '<i class="fas fa-list-ul"></i></button>'
                }
                }, targets: 9
            }
        ],
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

function listarOrdenesEntregadas(permiso){
    var vardataTables = funcDatatables();
    $('#ordenesEntregadas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarOrdenesEntregadas',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_mov_alm'},
            {'data': 'codigo_orden', 'name': 'log_ord_compra.codigo'},
            {'data': 'sede_orden_descripcion', 'name': 'sede_oc.descripcion'},
            {'data': 'nro_documento', 'name': 'adm_contri.nro_documento'},
            {'data': 'razon_social', 'name': 'adm_contri.razon_social'},
            {'data': 'codigo_softlink', 'name': 'log_ord_compra.codigo_softlink'},
            // {'data': 'monto_subtotal', 'class': 'right'},
            // {'data': 'monto_igv', 'class': 'right'},
            // {'data': 'monto_total', 'class': 'right'},
            {'data': 'codigo_requerimiento', 'name': 'alm_req.codigo'},
            {'data': 'sede_requerimiento_descripcion', 'name': 'sede_req.descripcion'},
            {'data': 'concepto', 'name': 'alm_req.concepto'},
            {'render': function (data, type, row){
                    return row['serie']+'-'+row['numero'];
                }
            },
            {'data': 'codigo'},
            {'data': 'fecha_emision'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'},
            {'data': 'codigo_trans', 'name': 'trans.codigo'},
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': 
                function (data, type, row){
                    if (permiso == '1') {
                        return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_orden_compra']+'">'+
                            '<i class="fas fa-list-ul"></i></button>'+
                        '<button type="button" class="ingreso btn btn-warning boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Ingreso" data-id="'+row['id_mov_alm']+'">'+
                            '<i class="fas fa-file-alt"></i></button>'+
                        // '<button type="button" class="ver_guias btn btn-warning boton" data-toggle="tooltip" '+
                        //     'data-placement="bottom" title="Ver Guías" data-id="'+row.id_orden_compra+'">'+
                        //     '<i class="fas fa-file-alt"></i></button>'+
                        (row['codigo_trans'] == null ? '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Anular Ingreso" data-id="'+row['id_mov_alm']+'" data-guia="'+row['id_guia_com']+'" data-oc="'+row['id_orden_compra']+'">'+
                        '<i class="fas fa-trash"></i></button>' : '')+
                        (
                        ((row['id_tipo_requerimiento'] == 1 && (row['sede_orden'] !== row['sede_requerimiento'] && row['codigo_trans'] == null)) ||
                         (row['id_tipo_requerimiento'] == 3 && (row['sede_orden'] !== row['sede_requerimiento'] && row['codigo_trans'] == null))) ? 
                            ('<button type="button" class="transferencia btn btn-success boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Generar Transferencia" >'+
                            '<i class="fas fa-exchange-alt"></i></button>') : 
                            ((row['codigo_trans'] !== null && row['estado_trans'] == 17) ?
                            '<button type="button" class="anular_sal btn btn-danger boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Anular Salida" data-id="'+row['id_salida_trans']+'" data-guia="'+row['id_guia_ven_trans']+'" data-trans="'+row['id_transferencia']+'">'+
                            '<i class="fas fa-trash"></i></button>' : ''));
                    } else {
                        return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_orden_compra']+'">'+
                            '<i class="fas fa-list-ul"></i></button>'+
                        '<button type="button" class="ingreso btn btn-warning boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Ver Ingreso" data-id="'+row['id_mov_alm']+'">'+
                            '<i class="fas fa-file-alt"></i></button>'
                    }
                }, targets: 14
            }    
        ],
    });
}

$('#ordenesEntregadas tbody').on("click","button.detalle", function(){
    var data = $('#ordenesEntregadas').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    open_detalle(data);
});

$('#ordenesEntregadas tbody').on("click","button.ingreso", function(){
    var id_mov_alm = $(this).data('id');
    var id = encode5t(id_mov_alm);
    window.open('imprimir_ingreso/'+id);
});

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

$('#ordenesEntregadas tbody').on("click","button.transferencia", function(){
    var data = $('#ordenesEntregadas').DataTable().row($(this).parents("tr")).data();
    console.log('data.id_orden_compra'+data.id_orden_compra);
    // var data = $(this).data('id');
    openTransferenciaGuia(data);
});

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

$("#form-guia_ven_obs").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    anular_transferencia_salida(data);
});

function anular_transferencia_salida(data){
    $("#submitGuiaVenObs").attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'anular_transferencia_salida',
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
                $('#ordenesEntregadas').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function open_detalle(data){
    $('#modal-ordenDetalle').modal({
        show: true
    });
    $('#cabecera_orden').text(data.codigo_orden+' - '+data.razon_social);
    listar_detalle_orden(data.id_orden_compra);
}

function open_guia_create(data){
    $('#modal-guia_create').modal({
        show: true
    });
    $("#submit_guia").removeAttr("disabled");
    $('[name=id_operacion]').val(2).trigger('change.select2');
    $('[name=id_guia_clas]').val(1);
    $('[name=id_orden_compra]').val(data.id_orden_compra);
    $('#serie').text('');
    $('#numero').text('');
    cargar_almacenes(data.id_sede, 'id_almacen');
}

// function cargar_almacenes(sede){
//     if (sede !== ''){
//         $.ajax({
//             type: 'GET',
//             url: 'cargar_almacenes/'+sede,
//             dataType: 'JSON',
//             success: function(response){
//                 console.log(response);
//                 var option = '';
//                 for (var i=0; i<response.length; i++){
//                     if (response.length == 1){
//                         option+='<option data-id-sede="'+response[i].id_sede+'" data-id-empresa="'+response[i].id_empresa+'" value="'+response[i].id_almacen+'" selected>'+response[i].codigo+' - '+response[i].descripcion+'</option>';

//                     } else {
//                         option+='<option data-id-sede="'+response[i].id_sede+'" data-id-empresa="'+response[i].id_empresa+'" value="'+response[i].id_almacen+'">'+response[i].codigo+' - '+response[i].descripcion+'</option>';

//                     }
//                 }
//                 $('[name=id_almacen]').html(option);
//             }
//         }).fail( function( jqXHR, textStatus, errorThrown ){
//             console.log(jqXHR);
//             console.log(textStatus);
//             console.log(errorThrown);
//         });
//     }
// }

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
            var dscto = 0;
            var sub_total = 0;
            var total = 0;
            response.forEach(element => {
                // dscto = (element.monto_descuento !== null ? element.monto_descuento : 0);
                // sub_total = (element.cantidad_cotizada * element.precio_cotizado);
                // total += (sub_total - dscto);
                html+='<tr id="'+element.id_detalle_orden+'">'+
                '<td>'+i+'</td>'+
                '<td>'+element.codigo+'</td>'+
                '<td>'+element.part_number+'</td>'+
                '<td>'+element.categoria+'</td>'+
                '<td>'+element.subcategoria+'</td>'+
                '<td>'+element.descripcion+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                '<td>'+element.abreviatura+'</td>'+
                // '<td>'+element.precio_cotizado+'</td>'+
                // '<td class="right">'+formatNumber.decimal(sub_total,'',-2)+'</td>'+
                // '<td class="right">'+formatNumber.decimal(dscto,'',-2)+'</td>'+
                // '<td class="right">'+formatNumber.decimal((sub_total - dscto),'',-2)+'</td>'+
                '</tr>';
                i++;
            });
            // var html_foot = '<tr><td class="right" colSpan="8">'+simbolo+'</td><td class="right">'+formatNumber.decimal(total,'',-2)+'</td></tr>';
            $('#detalleOrden tbody').html(html);
            // $('#detalleOrden tfoot').html(html_foot);
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
        url: '/verGuiasOrden/'+id_orden,
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

$("#form-guia_create").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    guardar_guia_create(data);
});

function guardar_guia_create(data){
    $("#submit_guia").attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'guardar_guia_com_oc',
        data: data,
        dataType: 'JSON',
        success: function(id_ingreso){
            console.log(id_ingreso);
            if (id_ingreso > 0){
                alert('Ingreso Almacén generado con éxito');
                $('#modal-guia_create').modal('hide');
                $('#ordenesPendientes').DataTable().ajax.reload();
                // var id = encode5t(id_ingreso);
                // window.open('imprimir_ingreso/'+id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
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