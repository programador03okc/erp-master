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
    
});

function listarOrdenesPendientes(){
    var vardataTables = funcDatatables();
    var tabla = $('#ordenesPendientes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listarOrdenesPendientes',
        'columns': [
            {'data': 'id_orden_compra'},
            {'data': 'codigo'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'fecha'},
            {'data': 'condicion'},
            {'data': 'nombre_corto'},
            {'data': 'simbolo'},
            {'data': 'monto_subtotal'},
            {'data': 'monto_igv'},
            {'data': 'monto_total'},
            {'defaultContent': 
            '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver Detalle" >'+
                '<i class="fas fa-list-ul"></i></button>'+
            '<button type="button" class="guia btn btn-info boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Generar Guía" >'+
                '<i class="fas fa-sign-in-alt"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#ordenesPendientes tbody',tabla);
}

function listarOrdenesEntregadas(){
    var vardataTables = funcDatatables();
    var tabla = $('#ordenesEntregadas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listarOrdenesEntregadas',
        'columns': [
            {'data': 'id_orden_compra'},
            {'data': 'codigo'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'fecha'},
            {'data': 'condicion'},
            {'data': 'nombre_corto'},
            {'data': 'simbolo'},
            {'data': 'monto_subtotal'},
            {'data': 'monto_igv'},
            {'data': 'monto_total'},
            {'defaultContent': 
            '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver Detalle" >'+
                '<i class="fas fa-list-ul"></i></button>'+
            '<button type="button" class="ver_guias btn btn-warning boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver Guías" >'+
                '<i class="fas fa-sign-in-alt"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#ordenesEntregadas tbody',tabla);
}

function botones(tbody, tabla){
    $(tbody).on("click","button.detalle", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log('data.id_orden_compra'+data.id_orden_compra);
        open_detalle(data);
    });
    $(tbody).on("click","button.guia", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log('data.id_orden_compra'+data.id_orden_compra);
        open_guia_create(data);
    });
    $(tbody).on("click","button.ver_guias", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log('data.id_orden_compra'+data.id_orden_compra);
        open_guias(data);
    });
}

function open_detalle(data){
    $('#modal-ordenDetalle').modal({
        show: true
    });
    $('#cabecera_orden').text(data.codigo+' - '+data.razon_social+' - Total: '+data.simbolo+data.monto_total);
    listar_detalle_orden(data.id_orden_compra);
}

function open_guia_create(data){
    $('#modal-guia_create').modal({
        show: true
    });
    // $('[name=id_tp_doc_almacen]').val(1).trigger('change.select2');
    $('[name=id_operacion]').val(2).trigger('change.select2');
    $('[name=id_guia_clas]').val(1);
    $('[name=id_orden_compra]').val(data.id_orden_compra);
    $('#serie').text('');
    $('#numero').text('');
}

function open_guias(data){
    $('#modal-guias').modal({
        show: true
    });
    $('#cabecera_orden').text(data.codigo+' - '+data.razon_social+' - Total: '+data.simbolo+data.monto_total);
    listar_guias_orden(data.id_orden_compra);
}

function listar_detalle_orden(id_orden){
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
            response.forEach(element => {
                dscto = (element.monto_descuento !== null ? element.monto_descuento : 0);
                sub_total = (element.cantidad_cotizada * element.precio_cotizado);
                html+='<tr id="'+element.id_detalle_orden+'">'+
                '<td>'+i+'</td>'+
                '<td>'+element.codigo+'</td>'+
                '<td>'+element.descripcion+(element.descripcion_adicional !== null ? ' '+element.descripcion_adicional : '')+'</td>'+
                '<td>'+element.cantidad_cotizada+'</td>'+
                '<td>'+element.unidad_medida+'</td>'+
                '<td>'+element.precio_cotizado+'</td>'+
                '<td>'+sub_total+'</td>'+
                '<td>'+dscto+'</td>'+
                '<td>'+(sub_total - dscto)+'</td>'+
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
        url: '/verGuiasOrden/'+id_orden,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response.forEach(element => {
                html+='<tr id="'+element.id_guia_com_oc+'">'+
                '<td>'+i+'</td>'+
                '<td>'+element.serie+'-'+element.numero+'</td>'+
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

$("#form-guia_create").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    guardar_guia_create(data);
});

function guardar_guia_create(data){
    $.ajax({
        type: 'POST',
        url: 'guardar_guia_com_oc',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('La Guía se generó correctamente.');
                $('#modal-guia_create').modal('hide');
                localStorage.setItem("id_guia_com",response);
                location.assign("guia_compra");
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