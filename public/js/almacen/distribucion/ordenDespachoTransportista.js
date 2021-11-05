function openAgenciaTransporte(data) {
    $("#modal-orden_despacho_transportista").modal({
        show: true
    });
    console.log(data);
    $("[name=id_od]").val(data.id_od);
    $("[name=con_id_requerimiento]").val(data.id_req);
    $("[name=tr_id_transportista]").val(data.id_transportista !== null ? data.id_transportista : '');
    $("[name=tr_razon_social]").val(data.transportista_razon_social !== null ? data.transportista_razon_social : '');
    $("[name=serie]").val(data.serie_tra !== null ? data.serie_tra : '');
    $("[name=numero]").val(data.numero_tra !== null ? data.numero_tra : '');
    // $('[name=fecha_transportista]').val('');
    $("[name=codigo_envio]").val(data.codigo_envio !== null ? data.codigo_envio : '');
    $("[name=importe_flete]").val(data.importe_flete !== null ? data.importe_flete : '');
    $("#submit_od_transportista").removeAttr("disabled");
}

$("#form-orden_despacho_transportista").on("submit", function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    $('#submit_od_transportista').attr('disabled', 'true');
    despacho_transportista(data);
    $('#modal-orden_despacho_transportista').modal('hide');
});

function despacho_transportista(data) {
    $.ajax({
        type: 'POST',
        url: 'despacho_transportista',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response > 0) {
                $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
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
function ceros_numero(numero) {
    if (numero == "numero") {
        var num = $("[name=numero]").val();
        $("[name=numero]").val(leftZero(7, num));
    } else if (numero == "serie") {
        var num = $("[name=serie]").val();
        $("[name=serie]").val(leftZero(4, num));
    }
}