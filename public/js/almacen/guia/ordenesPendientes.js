$(function(){
    $("#tab-ordenes section:first form").attr('form', 'formulario');
    listarOrdenesPendientes();

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
            listarOrdenesPendientes();
        } 
        else if (activeForm == "form-ingresadas"){
            listarOrdenesEntregadas();
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    vista_extendida();
});

function listarOrdenesPendientes(){
    var vardataTables = funcDatatables();
    $('#ordenesPendientes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarOrdenesPendientes',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_orden_compra'},
            {'data': 'codigo'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'fecha'},
            {'data': 'codigo_requerimiento'},
            {'data': 'concepto'},
            {'data': 'nombre_corto'},
            {'data': 'simbolo'},
            {'data': 'monto_subtotal'},
            {'data': 'monto_igv'},
            {'data': 'monto_total'}
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" >'+
                    '<i class="fas fa-list-ul"></i></button>'+
                '<button type="button" class="guia btn btn-info boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Generar Guía" >'+
                    '<i class="fas fa-sign-in-alt"></i></button>';
                }, targets: 12
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

function listarOrdenesEntregadas(){
    var vardataTables = funcDatatables();
    $('#ordenesEntregadas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarOrdenesEntregadas',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_mov_alm'},
            {'data': 'codigo_orden'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'simbolo'},
            {'data': 'monto_subtotal', 'class': 'right'},
            {'data': 'monto_igv', 'class': 'right'},
            {'data': 'monto_total', 'class': 'right'},
            {'data': 'codigo_requerimiento'},
            {'data': 'concepto'},
            {'render': function (data, type, row){
                    return row['serie']+'-'+row['numero'];
                }
            },
            {'data': 'codigo'},
            {'data': 'fecha_emision'},
            {'data': 'nombre_corto'},
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" data-id="'+row.id_orden_compra+'">'+
                    '<i class="fas fa-list-ul"></i></button>'+
                '<button type="button" class="ingreso btn btn-warning boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Ingreso" data-id="'+row.id_mov_alm+'">'+
                    '<i class="fas fa-file-alt"></i></button>'+

                // '<button type="button" class="ver_guias btn btn-warning boton" data-toggle="tooltip" '+
                //     'data-placement="bottom" title="Ver Guías" data-id="'+row.id_orden_compra+'">'+
                //     '<i class="fas fa-file-alt"></i></button>'+
                // '<button type="button" class="anularIngreso btn btn-danger boton" data-toggle="tooltip" '+
                //     'data-placement="bottom" title="Anular Ingreso" data-id="'+row.id_mov_alm+'">'+
                //     '<i class="fas fa-trash"></i></button>'+
                ((row['sede_orden'] !== row['sede_requerimiento'] && row['id_guia_ven'] == null) ? 
                ('<button type="button" class="transferencia btn btn-success boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Generar Transferencia" >'+
                    '<i class="fas fa-exchange-alt"></i></button>') : '');
                }, targets: 14
            }    
        ],
    });
}
$('#ordenesEntregadas tbody').on("click","button.detalle", function(){
    var data = $('#ordenesEntregadas').DataTable().row($(this).parents("tr")).data();
    console.log('data.id_orden_compra'+data.id_orden_compra);
    open_detalle(data);
});
$('#ordenesEntregadas tbody').on("click","button.ingreso", function(){
    var id_mov_alm = $(this).data('id');
    var id = encode5t(id_mov_alm);
    window.open('imprimir_ingreso/'+id);
});
// $('#ordenesEntregadas tbody').on("click","button.ver_guias", function(){
//     var data = $('#ordenesEntregadas').DataTable().row($(this).parents("tr")).data();
//     open_guias(data);
// });
$('#ordenesEntregadas tbody').on("click","button.transferencia", function(){
    var data = $('#ordenesEntregadas').DataTable().row($(this).parents("tr")).data();
    console.log('data.id_orden_compra'+data.id_orden_compra);
    // var data = $(this).data('id');
    openTransferenciaGuia(data);
});

function open_detalle(data){
    $('#modal-ordenDetalle').modal({
        show: true
    });
    $('#cabecera_orden').text(data.codigo_orden+' - '+data.razon_social+' - Total: '+data.simbolo+data.monto_total);
    listar_detalle_orden(data.id_orden_compra, data.simbolo);
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

function open_guias(data){
    $('#modal-guias').modal({
        show: true
    });
    $('#cabecera_orden').text(data.codigo_orden+' - '+data.razon_social+' - Total: '+data.simbolo+data.monto_total);
    listar_guias_orden(data.id_orden_compra);
}

function listar_detalle_orden(id_orden, simbolo){
    console.log('id_orden',id_orden);
    $.ajax({
        type: 'GET',
        url: '/detalleOrden/'+id_orden,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            var dscto = 0;
            var sub_total = 0;
            var total = 0;
            response.forEach(element => {
                dscto = (element.monto_descuento !== null ? element.monto_descuento : 0);
                sub_total = (element.cantidad_cotizada * element.precio_cotizado);
                total += (sub_total - dscto);
                html+='<tr id="'+element.id_detalle_orden+'">'+
                '<td>'+i+'</td>'+
                '<td>'+element.codigo+'</td>'+
                '<td>'+element.descripcion+(element.descripcion_adicional !== null ? ' '+element.descripcion_adicional : '')+'</td>'+
                '<td>'+element.cantidad_cotizada+'</td>'+
                '<td>'+element.unidad_medida+'</td>'+
                '<td>'+element.precio_cotizado+'</td>'+
                '<td class="right">'+formatNumber.decimal(sub_total,'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal(dscto,'',-2)+'</td>'+
                '<td class="right">'+formatNumber.decimal((sub_total - dscto),'',-2)+'</td>'+
                '</tr>';
                i++;
            });
            var html_foot = '<tr><td class="right" colSpan="8">'+simbolo+'</td><td class="right">'+formatNumber.decimal(total,'',-2)+'</td></tr>';
            $('#detalleOrden tbody').html(html);
            $('#detalleOrden tfoot').html(html_foot);
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
                var id = encode5t(id_ingreso);
                window.open('imprimir_ingreso/'+id);                
                // localStorage.setItem("id_guia_com",response);
                // location.assign("guia_compra");
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