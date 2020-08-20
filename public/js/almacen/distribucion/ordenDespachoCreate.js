let detalle_requerimiento = [];
let detalle_ingresa = [];
let tab_origen = null;

function open_despacho_create(data){
    $('#modal-orden_despacho_create').modal({
        show: true
    });
    console.log('open_despacho_create');
    console.log(data);
    $("#submit_orden_despacho").removeAttr("disabled");
    $('[name=tipo_entrega]').val('MISMA CIUDAD').trigger('change.select2');
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    // $('[name=id_sede]').val(data.id_sede);
    $('[name=direccion_destino]').val(data.direccion_entrega);
    $('[name=ubigeo]').val(data.id_ubigeo_entrega);
    $('[name=name_ubigeo]').val(data.ubigeo_descripcion);
    $('[name=tipo_cliente]').val(data.tipo_cliente);
    $('[name=id_almacen]').val((data.id_almacen !== null && data.id_almacen !== 0) ? data.id_almacen : '');
    $('[name=almacen_descripcion]').val(data.almacen_descripcion !== null ? data.almacen_descripcion : '');
    $('[name=id_sede]').val(data.sede_requerimiento !== null ? data.sede_requerimiento : '');
    $('[name=telefono_cliente]').val(data.telefono);
    $('[name=correo_cliente]').val(data.email);

    // $('#'+data.documento+'').prop('checked', true);
    if (data.tipo_cliente == 1){
        $('#Boleta').prop('checked', true);
    } 
    else if (data.tipo_cliente == 2){
        $('#Factura').prop('checked', true);
    }

    if (data.id_persona !== null){
        $('[name=id_persona]').val(data.id_persona);
        $('[name=dni_persona]').val(data.dni_persona);
        $('[name=nombre_persona]').val(data.nombre_persona);
        $('[name=dni_persona]').show();
        $('[name=nombre_persona]').show();

        $('[name=id_cliente]').val('');
        $('[name=cliente_ruc]').val('');
        $('[name=cliente_razon_social]').val('');
        $('[name=cliente_ruc]').hide();
        $('[name=cliente_razon_social]').hide();
    }
    else if (data.id_cliente !== null){
        $('[name=id_cliente]').val(data.id_cliente);
        $('[name=cliente_ruc]').val(data.cliente_ruc);
        $('[name=cliente_razon_social]').val(data.cliente_razon_social);
        $('[name=cliente_ruc]').show();
        $('[name=cliente_razon_social]').show();

        $('[name=id_persona]').val('');
        $('[name=dni_persona]').val('');
        $('[name=nombre_persona]').val('');
        $('[name=dni_persona]').hide();
        $('[name=nombre_persona]').hide();
    }
    $("#detalleItemsReq").hide();

    console.log(data.id_tipo_requerimiento);
    // if (data.id_tipo_requerimiento == 2){
        var idTabla = 'detalleRequerimientoOD';
        console.log(idTabla);
        listar_detalle_requerimiento(data.id_requerimiento, idTabla);
    // } 
    // else if (data.id_tipo_requerimiento == 1){
    //     listar_detalle_ingreso(data.id_requerimiento);
    // }

    $('[name=fecha_despacho]').val(fecha_actual());
    $('[name=fecha_entrega]').val(fecha_actual());
    $('[name=aplica_cambios]').prop('checked', false);
    $('[name=aplica_cambios_valor]').val('no');

    detalle_requerimiento = [];
    detalle_ingresa = [];
}

function openCliente(){
    var tipoCliente = $('[name=tipo_cliente]').val();
    if (tipoCliente == 1){
        modalPersona();
    } else {
        clienteModal();
    }
}

function changeTipoCliente(e){
    limpiarCampos(e.target.value);
}

function limpiarCampos(tipo){
    if (tipo == 1){
        $('[name=id_cliente]').val('');
        $('[name=cliente_ruc]').val('');
        $('[name=cliente_razon_social]').val('');
        $('[name=cliente_ruc]').hide();
        $('[name=cliente_razon_social]').hide();

        $('[name=id_persona]').val('');
        $('[name=dni_persona]').val('');
        $('[name=nombre_persona]').val('');
        $('[name=dni_persona]').show();
        $('[name=nombre_persona]').show();
    }
    else if (tipo == 2){
        $('[name=id_cliente]').val('');
        $('[name=cliente_ruc]').val('');
        $('[name=cliente_razon_social]').val('');
        $('[name=cliente_ruc]').show();
        $('[name=cliente_razon_social]').show();

        $('[name=id_persona]').val('');
        $('[name=dni_persona]').val('');
        $('[name=nombre_persona]').val('');
        $('[name=dni_persona]').hide();
        $('[name=nombre_persona]').hide();
    }
}

function listar_detalle_ingreso(id_requerimiento){
    $.ajax({
        type: 'GET',
        url: 'verDetalleIngreso/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            detalle_requerimiento = response;
            console.log(detalle_requerimiento);
            response.forEach(element => {
                html+='<tr id="'+element.id_mov_alm_det+'">'+
                '<td><input type="checkbox" onChange="changeCheckIngresa(this,'+element.id_mov_alm_det+');"/></td>'+
                '<td>'+(element.codigo_producto !== null ? element.codigo_producto : '')+'</td>'+
                '<td>'+(element.part_number !== null ? element.part_number : '')+'</td>'+
                '<td>'+(element.categoria !== null ? element.categoria : '')+'</td>'+
                '<td>'+(element.subcategoria !== null ? element.subcategoria : '')+'</td>'+
                '<td>'+(element.producto_descripcion !== null ? element.producto_descripcion : '')+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                '<td>'+(element.unidad_producto !== null ? element.unidad_producto : '')+'</td>'+
                // '<td>'+(element.almacen_descripcion !== null ? element.almacen_descripcion : '')+'</td>'+
                // '<td>'+(element.codigo_posicion !== null ? element.codigo_posicion : '')+'</td>'+
                // '<td>'+(element.lugar_entrega !== null ? element.lugar_entrega : element.lugar_despacho_orden)+'</td>'+
                // '<td><span class="label label-'+element.bootstrap_color+'">'+element.estado_doc+'</span></td>'+
                // '<td>'+(element.id_almacen !== null ? 
                //     '<button type="button" class="btn btn-info" data-toggle="tooltip" data-placement="bottom" title="Ver Transferencia" onClick="#"><i class="fas fa-file-alt"></i></button>' : '')+
                // '</td>'+
                '</tr>';
                i++;
            });
            $('#detalleRequerimientoOD tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#form-orden_despacho").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var msj = validaOrdenDespacho();

    if (msj.length > 0){
        alert(msj);
    } 
    else {
        var serial = $(this).serialize();
        var doc = $('input[name=optionsRadios]:checked').val();
        var data = serial+'&documento='+doc+
                          '&detalle_ingresa='+JSON.stringify(detalle_ingresa)+
                          '&detalle_requerimiento='+JSON.stringify(detalle_requerimiento);
        console.log(data);
        guardar_orden_despacho(data);
    }
});

function guardar_orden_despacho(data){
    // var sede = $('[name=id_sede]').val();
    // var req = $('[name=id_requerimiento]').val();
    // var alm = $('[name=id_almacen]').val();
    // var clie = $('[name=id_cliente]').val();
    // var perso = $('[name=id_persona]').val();
    // var ubig = $('[name=ubigeo]').val();
    // var dir = $('[name=direccion_destino]').val();
    // var fdes = $('[name=fecha_despacho]').val();
    // var hdes = $('[name=hora_despacho]').val();
    // var fent = $('[name=fecha_entrega]').val();
    // var camb = $('[name=aplica_cambios_valor]').val();
    // var tipo = $('[name=tipo_entrega]').val();
    // var tpcli = $('[name=tipo_cliente]').val();
    // var telf = $('[name=telefono_cliente]').val();
    // var sale = $('[name=sale]').val();

    // var mail = $('[name=correo_cliente]').val();
    // var dni = $('[name=dni_persona]').val();
    // var name = $('[name=nombre_persona]').val();
    // var ruc = $('[name=cliente_ruc]').val();
    // var raz = $('[name=cliente_razon_social]').val();

    // var doc = $('input[name=optionsRadios]:checked').val();

    // var data =  'id_sede='+sede+
    //             '&id_requerimiento='+req+
    //             '&id_cliente='+clie+
    //             '&id_persona='+perso+
    //             '&id_almacen='+alm+
    //             '&ubigeo='+ubig+
    //             '&direccion_destino='+dir+
    //             '&fecha_despacho='+fdes+
    //             '&hora_despacho='+hdes+
    //             '&fecha_entrega='+fent+
    //             '&documento='+doc+
    //             '&aplica_cambios_valor='+camb+
    //             '&tipo_entrega='+tipo+
    //             '&tipo_cliente='+tpcli+
    //             '&telefono='+telf+***
    //             '&sale='+sale+
    //             '&correo_cliente='+mail+
    //             '&dni_persona='+dni+
    //             '&nombre_persona='+name+
    //             '&ruc='+ruc+***
    //             '&razon_social='+raz+***
    //             '&detalle_ingresa='+JSON.stringify(detalle_ingresa)+
    //             '&detalle_requerimiento='+JSON.stringify(detalle_requerimiento);

    console.log(data);
    // var msj = validaOrdenDespacho();

    // if (msj.length > 0){
    //     alert(msj);
    // } 
    // else {
        $("#submit_orden_despacho").attr('disabled','true');
        $.ajax({
            type: 'POST',
            url: 'guardar_orden_despacho',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                alert(response);
                $('#modal-orden_despacho_create').modal('hide');
                
                if (tab_origen == 'confirmados'){
                    $('#requerimientosConfirmados').DataTable().ajax.reload();
                } 
                else if (tab_origen == 'enProceso'){
                    $('#requerimientosPendientes').DataTable().ajax.reload();
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    // }
}

$("[name=aplica_cambios]").on( 'change', function() {
    if( $(this).is(':checked') ) {
        $("#detalleItemsReq").show();
        $("[name=aplica_cambios_valor]").val('si');

        $("[name=seleccionar_todos]").prop('checked', true);
        detalle_ingresa = detalle_requerimiento;
        $("#detalleRequerimientoOD tbody tr").each(function(){
            $(this).find("td input[type=checkbox]").prop('checked', true);
        });
    } else {
        $("#detalleItemsReq").hide();
        $("[name=aplica_cambios_valor]").val('no');

        $("[name=seleccionar_todos]").prop('checked', false);
        detalle_ingresa = [];
        $("#detalleRequerimientoOD tbody tr").each(function(){
            $(this).find("td input[type=checkbox]").prop('checked', false);
        });
    }
});

$("[name=optionsRadios]").on( 'change', function() {
    if( $(this).is(':checked') ) {
        var tipo = null;
        if ($(this).val() == 'Factura'){
            tipo = 2;
        } else {
            tipo = 1;
        }
        $('[name=tipo_cliente]').val(tipo);
        limpiarCampos(tipo);
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

function changeCheckIngresa(checkbox, id_mov_alm_det){
    console.log(checkbox.checked+' id_mov_alm_det'+id_mov_alm_det);
    if (checkbox.checked) {
        var nuevo = detalle_requerimiento.find(element => element.id_mov_alm_det == id_mov_alm_det);
        detalle_ingresa.push(nuevo);
    } else {
        var index = detalle_ingresa.findIndex(function(item, i){
            return item.id_mov_alm_det == id_mov_alm_det;
        });
        detalle_ingresa.splice(index,1);
    }
    console.log(detalle_ingresa);
}

function validaOrdenDespacho(){
    var tpcli = $('[name=tipo_cliente]').val();
    var clie = $('[name=id_cliente]').val();
    var perso = $('[name=id_persona]').val();
    var ubig = $('[name=ubigeo]').val();
    var dir = $('[name=direccion_destino]').val();
    var telf = $('[name=telefono_cliente]').val();
    var mail = $('[name=correo_cliente]').val();
    var hora = $('[name=hora_despacho]').val();
    var msj = '';

    if (tpcli == 1){
        if (perso == ''){
            msj+='\n Es necesario que ingrese los datos del Cliente';
        }
    } else if (tpcli == 2){
        if (clie == ''){
            msj+='\n Es necesario que ingrese los datos del Cliente';
        }
    }
    if (ubig == ''){
        msj+='\n Es necesario que ingrese un Ubigeo Destino';
    }
    if (dir == ''){
        msj+='\n Es necesario que ingrese una Dirección Destino';
    }
    if (telf == ''){
        msj+='\n Es necesario que ingrese un Teléfono';
    }
    if (mail == ''){
        msj+='\n Es necesario que ingrese un Email';
    }
    if (hora == ''){
        msj+='\n Es necesario que ingrese una Hora';
    }
    return msj;
}