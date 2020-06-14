let detalle_requerimiento = [];
let detalle_ingresa = [];

function open_despacho_create(data){
    $('#modal-orden_despacho_create').modal({
        show: true
    });
    console.log('open_despacho_create');
    console.log(data);
    $("#submit_orden_despacho").removeAttr("disabled");
    $('[name=tipo_entrega]').val('MISMA CIUDAD').trigger('change.select2');
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $('[name=id_sede]').val(data.id_sede);
    $('[name=direccion_destino]').val(data.direccion_entrega);
    $('[name=ubigeo]').val(data.id_ubigeo_entrega);
    $('[name=name_ubigeo]').val(data.ubigeo_descripcion);
    $("#detalleItemsReq").hide();

    var idTabla = 'detalleRequerimientoOD';
    listar_detalle_requerimiento(data.id_requerimiento, idTabla);

    $('[name=id_cliente]').val('');
    $('[name=cliente_razon_social]').val('');
    // $('[name=ubigeo]').val('');
    // $('[name=name_ubigeo]').val('');
    // $('[name=direccion_destino]').val('');
    $('[name=fecha_despacho]').val(fecha_actual());
    $('[name=fecha_entrega]').val(fecha_actual());
    $('[name=aplica_cambios]').prop('checked', false);
    $('[name=aplica_cambios_valor]').val('no');

    detalle_requerimiento = [];
    detalle_ingresa = [];
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
            detalle_requerimiento = response;
            response.forEach(element => {
                html+='<tr id="'+element.id_detalle_requerimiento+'">'+
                '<td>'+(idTabla == 'detalleRequerimiento' ? i : '<input type="checkbox" onChange="changeCheckIngresa(this,'+element.id_detalle_requerimiento+');"/>')+'</td>'+
                '<td>'+(element.codigo_item !== null ? element.codigo_item : '')+'</td>'+
                '<td>'+(element.descripcion_item !== null ? element.descripcion_item : '')+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                '<td>'+(element.unidad_medida_item !== null ? element.unidad_medida_item : element.unidad_medida)+'</td>'+
                '<td>'+(element.almacen_descripcion !== null ? element.almacen_descripcion : '')+'</td>'+
                // '<td>'+(element.codigo_posicion !== null ? element.codigo_posicion : '')+'</td>'+
                // '<td>'+(element.lugar_entrega !== null ? element.lugar_entrega : element.lugar_despacho_orden)+'</td>'+
                '<td><span class="label label-'+element.bootstrap_color+'">'+element.estado_doc+'</span></td>'+
                // '<td>'+(element.id_almacen !== null ? 
                //     '<button type="button" class="btn btn-info" data-toggle="tooltip" data-placement="bottom" title="Ver Transferencia" onClick="#"><i class="fas fa-file-alt"></i></button>' : '')+
                // '</td>'+
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

function guardar_orden_despacho(){
    var sede = $('[name=id_sede]').val();
    var req = $('[name=id_requerimiento]').val();
    var clie = $('[name=id_cliente]').val();
    var ubig = $('[name=ubigeo]').val();
    var dir = $('[name=direccion_destino]').val();
    var fdes = $('[name=fecha_despacho]').val();
    var fent = $('[name=fecha_entrega]').val();
    var camb = $('[name=aplica_cambios_valor]').val();
    var tipo = $('[name=tipo_entrega]').val();
    var sale = $('[name=sale]').val();

    var data =  'id_sede='+sede+
                '&id_requerimiento='+req+
                '&id_cliente='+clie+
                '&ubigeo='+ubig+
                '&direccion_destino='+dir+
                '&fecha_despacho='+fdes+
                '&fecha_entrega='+fent+
                '&aplica_cambios_valor='+camb+
                '&tipo_entrega='+tipo+
                '&sale='+sale+
                '&detalle_ingresa='+JSON.stringify(detalle_ingresa)+
                '&detalle_requerimiento='+JSON.stringify(detalle_requerimiento);

    $("#submit_orden_despacho").attr('disabled','true');
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_orden_despacho',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('La Orden de Despacho se generó correctamente.'+response);
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

$("[name=aplica_cambios]").on( 'change', function() {
    if( $(this).is(':checked') ) {
        $("#detalleItemsReq").show();
        $("[name=aplica_cambios_valor]").val('si');
    } else {
        $("#detalleItemsReq").hide();
        $("[name=aplica_cambios_valor]").val('no');
    }
});

$("[name=seleccionar_todos]").on( 'change', function() {
    if( $(this).is(':checked') ) {
        detalle_ingresa = detalle_requerimiento;
        $("#detalleRequerimientoOD tbody tr").each(function(){
            $(this).find("td input[type=checkbox]").prop('checked', true);
        });
    } else {
        detalle_ingresa = [];
        $("#detalleRequerimientoOD tbody tr").each(function(){
            $(this).find("td input[type=checkbox]").prop('checked', false);
        });
    }
});

function changeCheckIngresa(checkbox, id_detalle_requerimiento){
    console.log(checkbox.checked+' id_detalle_requerimiento'+id_detalle_requerimiento);
    if (checkbox.checked) {
        var nuevo = detalle_requerimiento.find(element => element.id_detalle_requerimiento == id_detalle_requerimiento);
        detalle_ingresa.push(nuevo);
    } else {
        var index = detalle_ingresa.findIndex(function(item, i){
            return item.id_detalle_requerimiento == id_detalle_requerimiento;
        });
        detalle_ingresa.splice(index,1);
    }
    console.log(detalle_ingresa);
}