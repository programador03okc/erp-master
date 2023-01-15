function openFechaProgramada(id, od) {
    $('#modal-despacho_fecha_programada').modal('show');
    $('[name=fecha_despacho]').val(fecha_actual());
    $('[name=fecha_documento]').val(fecha_actual());
    $('[name=req_id_requerimiento]').val(id);
    $('[name=req_id_od]').val(od);
}

function generarDespachoInterno() {
    document.querySelectorAll("div[id='modal-despacho_fecha_programada'] button[id='btnDespachoObs']")[1].setAttribute("disabled", true);
    var id = $('[name=req_id_requerimiento]').val();
    var fec = $('[name=fecha_despacho]').val();
    var fdoc = $('[name=fecha_documento]').val();
    var com = $('[name=comentario]').val();
    console.log(com);
    $.ajax({
        type: 'POST',
        url: 'generarDespachoInterno',
        data: {
            'id_requerimiento': id,
            'fecha_despacho': fec,
            'fecha_documento': fdoc,
            'comentario': com,
        },
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            document.querySelectorAll("div[id='modal-despacho_fecha_programada'] button[id='btnDespachoObs']")[1].removeAttribute("disabled");

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
        document.querySelectorAll("div[id='modal-despacho_fecha_programada'] button[id='btnDespachoObs']")[1].removeAttribute("disabled");
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularDespachoInterno() {
    var id = $('[name=req_id_od]').val();
    var req = $('[name=req_id_requerimiento]').val();
    document.querySelectorAll("div[id='modal-despacho_fecha_programada'] button[id='btnDespachoObs']")[0].setAttribute("disabled", true);

    $.ajax({
        type: 'POST',
        url: 'anularDespachoInterno',
        data: {
            'id_od': id,
            'id_requerimiento': req,
        },
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            document.querySelectorAll("div[id='modal-despacho_fecha_programada'] button[id='btnDespachoObs']")[0].removeAttribute("disabled");

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
        document.querySelectorAll("div[id='modal-despacho_fecha_programada'] button[id='btnDespachoObs']")[0].removeAttribute("disabled");
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
