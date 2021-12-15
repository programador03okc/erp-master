function openFechaProgramada(id) {
    $('#modal-despacho_fecha_programada').modal('show');
    $('[name=fecha_despacho]').val(fecha_actual());
    $('[name=req_id_requerimiento]').val(id);
}

function generarDespachoInterno() {
    var id = $('[name=req_id_requerimiento]').val();
    var fec = $('[name=fecha_despacho]').val();
    $.ajax({
        type: 'POST',
        url: 'generarDespachoInterno',
        data: {
            'id_requerimiento': id,
            'fecha_despacho': fec,
        },
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
            if (response.tipo == 'success') {
                $('#modal-despacho_fecha_programada').modal('hide');
                $('#requerimientosEnProceso').DataTable().ajax.reload(null, false);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}