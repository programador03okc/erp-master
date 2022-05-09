function openAgenciaTransporte(data) {
    $("#modal-orden_despacho_transportista").modal({
        show: true
    });
    console.log(data);
    $("[name=id_od]").val(data.id_od);
    $("[name=con_id_requerimiento]").val(data.id_requerimiento);
    $("[name=tr_id_transportista]").val(data.id_transportista !== null ? data.id_transportista : '');

    $("[name=tr_razon_social]").val(data.transportista_razon_social !== null ? data.transportista_razon_social : '');
    $("[name=serie]").val(data.serie_tra !== null ? data.serie_tra : '');
    $("[name=numero]").val(data.numero_tra !== null ? data.numero_tra : '');
    $("[name=serie_guia_venta]").val(data.serie_guia !== null ? data.serie_guia : '');
    $("[name=numero_guia_venta]").val(data.numero_guia !== null ? data.numero_guia : '');

    $("[name=fecha_transportista]").val(data.fecha_transportista !== null ? data.fecha_transportista : '');
    $("[name=fecha_despacho_real]").val(data.fecha_despacho_real !== null ? data.fecha_despacho_real : '');
    $("[name=importe_flete]").val(data.importe_flete !== null ? data.importe_flete : '');
    $("[name=codigo_envio]").val(data.codigo_envio !== null ? data.codigo_envio : '');

    if (data.credito) {
        $('[name=credito]').prop('checked', true);
    } else {
        $('[name=credito]').prop('checked', false);
    }
    $("#submit_od_transportista").removeAttr("disabled");
}

$("#form-orden_despacho_transportista").on("submit", function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    let tr = $('[name=tr_id_transportista]').val();

    if (tr == '') {
        Swal.fire({
            title: "Es necesario que seleccione por lo menos un transportista",
            icon: "error",
        });
    } else {
        despachoTransportista(data);
    }
});

function despachoTransportista(data) {
    $('#submit_od_transportista').attr('disabled', 'true');
    $.ajax({
        type: 'POST',
        url: 'despachoTransportista',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response > 0) {
                $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
                $('#modal-orden_despacho_transportista').modal('hide');
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Datos actualizados correctamente.'
                });
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function cerrarDespachoTransportista() {
    $('#modal-orden_despacho_transportista').modal('hide');
}

function ceros_numero(numero) {
    if (numero == "numero") {
        var num = $("[name=numero]").val();
        if (num !== '') {
            $("[name=numero]").val(leftZero(7, num));
        }
    } else if (numero == "serie") {
        var num = $("[name=serie]").val();
        if (num !== '') {
            $("[name=serie]").val(leftZero(4, num));
        }
    } else if (numero == "numero_gv") {
        var num = $("[name=numero_guia_venta]").val();
        if (num !== '') {
            $("[name=numero_guia_venta]").val(leftZero(7, num));
        }
    } else if (numero == "serie_gv") {
        var num = $("[name=serie_guia_venta]").val();
        if (num !== '') {
            $("[name=serie_guia_venta]").val(leftZero(4, num));
        }
    }
}