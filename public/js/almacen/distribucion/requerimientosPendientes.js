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
        else if (activeForm == "form-despachados"){
            // listarOrdenesEntregadas();
        }
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    
});

function listarRequerimientosPendientes(){
    var vardataTables = funcDatatables();
    var tabla = $('#requerimientosPendientes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listarRequerimientosPendientes',
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'fecha_requerimiento'},
            {'data': 'observacion'},
            {'data': 'grupo'},
            {'data': 'nombre_corto'},
            {'defaultContent': 
            '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver Detalle" >'+
                '<i class="fas fa-list-ul"></i></button>'+
            '<button type="button" class="despacho btn btn-info boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Generar Orden de Despacho" >'+
                '<i class="fas fa-sign-in-alt"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#requerimientosPendientes tbody',tabla);
}

function listarRequerimientosDespachados(){
    var vardataTables = funcDatatables();
    var tabla = $('#requerimientosDespachados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listarRequerimientosDespachados',
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'fecha_requerimiento'},
            {'data': 'observacion'},
            {'data': 'grupo'},
            {'data': 'nombre_corto'},
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
    // botones('#requerimientosDespachados tbody',tabla);
}

function botones(tbody, tabla){
    $(tbody).on("click","button.detalle", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log('data.id_requerimiento'+data.id_requerimiento);
        open_detalle(data);
    });
    $(tbody).on("click","button.despacho", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log('data.id_requerimiento'+data.id_requerimiento);
        open_despacho_create(data);
    });
    // $(tbody).on("click","button.ver_guias", function(){
    //     var data = tabla.row($(this).parents("tr")).data();
    //     console.log('data.id_requerimiento'+data.id_requerimiento);
    //     open_guias(data);
    // });
}

function open_detalle(data){
    $('#modal-requerimientoDetalle').modal({
        show: true
    });
    $('#cabecera_orden').text(data.codigo+' - '+data.concepto);
    var idTabla = 'detalleRequerimiento';
    listar_detalle_requerimiento(data.id_requerimiento, idTabla);
}

function open_despacho_create(data){
    $('#modal-orden_despacho_create').modal({
        show: true
    });
    $("#submit_orden_despacho").removeAttr("disabled");
    $('[name=tipo_entrega]').val('MISMA CIUDAD').trigger('change.select2');
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $('#ubigeo_destino').text('');
    $('#direccion_destino').text('');
    $("#detalleItemsReq").hide();
    var idTabla = 'detalleRequerimientoOD';
    listar_detalle_requerimiento(data.id_requerimiento, idTabla);
}

function listar_detalle_requerimiento(id_requerimiento, idTabla){
    $.ajax({
        type: 'GET',
        url: '/verDetalleRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response.forEach(element => {
                html+='<tr id="'+element.id_detalle_requerimiento+'">'+
                '<td>'+(idTabla == 'detalleRequerimiento' ? i : '<input type="checkbox"/>')+'</td>'+
                '<td>'+(element.codigo_item !== null ? element.codigo_item : '')+'</td>'+
                '<td>'+(element.descripcion_item !== null ? element.descripcion_item : '')+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                '<td>'+(element.unidad_medida_item !== null ? element.unidad_medida_item : '')+'</td>'+
                '<td>'+(element.lugar_entrega !== null ? element.lugar_entrega : '')+'</td>'+
                '<td><span class="label label-'+element.bootstrap_color+'">'+element.estado_doc+'</span></td>'+
                '<td>'+(element.id_almacen !== null ? '<button type="button" class="btn btn-info" data-toggle="tooltip" data-placement="bottom" title="Ver Transferencia" onClick="#"><i class="fas fa-file-alt"></i></button></td>' : '')+
                '</tr>';
                i++;
            });
            $('#'+idTabla+' tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#form-orden_despacho_create").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    guardar_orden_despacho_create(data);
});

function guardar_orden_despacho_create(data){
    $("#submit_orden_despacho").attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'guardar_orden_despacho',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('La Orden de Despacho se generó correctamente.');
                $('#modal-orden_despacho_create').modal('hide');
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
/*
$('[name=aplica_cambios]').on('ifChecked ifUnchecked', function(event){
    console.log(event.type.replace('if','').toLowerCase());
    if (event.type.replace('if','').toLowerCase()=='checked'){
        $(this).val('1');//true
        $("#detalleItemsReq").show();
    } else if (event.type.replace('if','').toLowerCase()=='unchecked'){
        $(this).val('0');//false
        $("#detalleItemsReq").hide();
    }
});*/
$("[name=aplica_cambios]").on( 'change', function() {
    if( $(this).is(':checked') ) {
        // Hacer algo si el checkbox ha sido seleccionado
        // alert("El checkbox con valor " + $(this).val() + " ha sido seleccionado");
        $("#detalleItemsReq").show();
    } else {
        // Hacer algo si el checkbox ha sido deseleccionado
        // alert("El checkbox con valor " + $(this).val() + " ha sido deseleccionado");
        $("#detalleItemsReq").hide();
    }
});