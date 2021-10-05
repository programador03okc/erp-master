
let tab_origen = null;

function open_despacho_create(data) {
    $('#modal-orden_despacho_create').modal({
        show: true
    });
    $("#submit_orden_despacho").removeAttr("disabled");
    $('[name=tipo_entrega]').val('MISMA CIUDAD').trigger('change.select2');
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $('[name=tiene_transformacion]').val(data.tiene_transformacion ? 'si' : 'no');
    // $('[name=direccion_destino]').val(data.contacto_direccion !== null ? data.contacto_direccion : (data.entidad_direccion !== null ? data.entidad_direccion : data.direccion_entrega));
    $('[name=direccion_destino]').val(data.direccion_entrega);
    $('[name=ubigeo]').val(data.id_ubigeo_entrega);
    $('[name=name_ubigeo]').val(data.ubigeo_descripcion);
    $('[name=tipo_cliente]').val(data.tipo_cliente);
    $('[name=id_almacen]').val((data.id_almacen !== null && data.id_almacen !== 0) ? data.id_almacen : '');
    $('[name=almacen_descripcion]').val(data.almacen_descripcion !== null ? data.almacen_descripcion : '');
    $('[name=id_sede]').val(data.sede_requerimiento !== null ? data.sede_requerimiento : '');
    // $('[name=telefono_cliente]').val(data.contacto_telefono !== null ? data.contacto_telefono : (data.entidad_telefono !== null ? data.entidad_telefono : data.telefono));
    $('[name=telefono_cliente]').val(data.telefono);
    // $('[name=correo_cliente]').val(data.contacto_email !== null ? data.contacto_email : (data.entidad_email !== null ? data.entidad_email : data.email));
    $('[name=correo_cliente]').val(data.email);
    $('[name=contacto_cliente]').val(data.contacto_persona !== null ? data.contacto_persona :
        (data.entidad_persona !== null ? data.entidad_persona
            : (data.nombre_persona !== null ? data.nombre_persona : data.cliente_razon_social)));
    $('[name=id_cc]').val(data.id_cc);
    $('[name=hora_despacho]').val(hora_actual());
    $('[name=contenido]').val('');

    if (data.tipo_cliente == 1) {
        $('#Boleta').prop('checked', true);
    }
    else if (data.tipo_cliente == 2) {
        $('#Factura').prop('checked', true);
    }

    if (data.id_persona !== null) {
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
    else if (data.id_cliente !== null) {
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
    $("#despachoExterno").show();

    $('#detalleSale tbody').html('');

    $('[name=fecha_despacho]').val(fecha_actual());
    $('[name=fecha_entrega]').val(data.fecha_entrega);

}

$("#form-orden_despacho").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();
    var msj = validaOrdenDespacho();

    if (msj.length > 0) {
        alert(msj);
    }
    else {
        var serial = $(this).serialize();
        var doc = $('input[name=optionsRadios]:checked').val();

        var data = serial + '&documento=' + doc;
        console.log(data);
        guardar_orden_despacho(data);

    }
});

function guardar_orden_despacho(data) {
    $("#submit_orden_despacho").attr('disabled', 'true');

    $.ajax({
        type: 'POST',
        url: 'guardarOrdenDespachoExterno',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                // width: 500,
                msg: 'Orden de Despacho guardada con éxito.'
            });
            $('#modal-orden_despacho_create').modal('hide');

            listarRequerimientosPendientes();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("[name=optionsRadios]").on('change', function () {
    if ($(this).is(':checked')) {
        var tipo = null;
        if ($(this).val() == 'Factura') {
            tipo = 2;
        } else {
            tipo = 1;
        }
    }
});

function validaOrdenDespacho() {
    var tpcli = $('[name=tipo_cliente]').val();
    var clie = $('[name=id_cliente]').val();
    var perso = $('[name=id_persona]').val();
    var ubig = $('[name=ubigeo]').val();
    var dir = $('[name=direccion_destino]').val();
    var telf = $('[name=telefono_cliente]').val();
    var hora = $('[name=hora_despacho]').val();
    var msj = '';


    if (tpcli == 1) {
        if (perso == '') {
            msj += '\n Es necesario que ingrese los datos del Cliente';
        }
    } else if (tpcli == 2) {
        if (clie == '') {
            msj += '\n Es necesario que ingrese los datos del Cliente';
        }
    }
    if (ubig == '') {
        msj += '\n Es necesario que ingrese un Ubigeo Destino';
    }
    if (dir == '') {
        msj += '\n Es necesario que ingrese una Dirección Destino';
    }
    if (telf == '') {
        msj += '\n Es necesario que ingrese un Teléfono';
    }

    if (hora == '') {
        msj += '\n Es necesario que ingrese una Hora';
    }
    return msj;
}
