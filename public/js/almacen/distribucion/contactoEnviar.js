
function abrirVistaPreviaCorreo() {
    $('#modal-contacto_enviar').modal({
        show: true
    });

    let id_contacto = $('[name=id_contacto_od]').val();
    let contacto = listaContactos.find(element => element.id_datos_contacto == id_contacto);
    console.log(contacto);

    let cliente = $('#modal-orden_despacho_contacto').find('.nombre').html();
    let correo_licencia = ($('[name=correo_licencia]').val()).trim();

    if (contacto !== null) {
        var msj = "DATOS DE CONTACTO \n" +
            "\n• Cliente/Entidad: " + cliente +
            "\n• Nombre: " + contacto.nombre +
            "\n• Teléfono: " + contacto.telefono +
            ((contacto.cargo !== null && contacto.cargo !== '') ? "\n• Cargo: " + contacto.cargo : '') +
            ((contacto.horario !== null && contacto.horario !== '') ? "\n• Horario de atención: " + contacto.horario : '') +
            ((correo_licencia !== null && correo_licencia !== '') ? "\n\nENVIAR LICENCIAS AL\n• Correo: " + correo_licencia : '') +
            "\n\nSaludos, \n" + usuarioSesion;
        $('[name=mensaje_contacto]').val(msj);
    }
}

function enviarDatosContacto() {
    let id_requerimiento = $('[name=id_requerimiento]').val();
    let mensaje = $('[name=mensaje_contacto]').val();
    let correo_licencia = ($('[name=correo_licencia]').val()).trim();

    let data = 'id_requerimiento=' + id_requerimiento +
        '&mensaje=' + mensaje +
        '&correo_licencia=' + correo_licencia;

    const $button = $("#btn_enviar_correo_contacto");
    $button.prop('disabled', 'true');
    $button.html('Enviando...');

    $.ajax({
        type: 'POST',
        url: 'enviarDatosContacto',
        data: data,
        dataType: 'JSON',
    }).done(function (response) {
        console.log(response);
        Lobibox.notify(response.tipo, {
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: response.mensaje
        });
        if (response.tipo == 'success') {
            $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
            $('#modal-contacto_enviar').modal('hide');
            $('#modal-orden_despacho_contacto').modal('hide');
        } else {
            console.log('Error devuelto: ' + response.error);
        }
    }).always(function () {
        $button.prop('disabled', false);
        $button.html('Enviar correo');
    }).fail(function (jqXHR) {
        Lobibox.notify('error', {
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Hubo un problema. Por favor actualice la página e intente de nuevo.'
        });
        console.log('Error devuelto: ' + jqXHR.responseText);
    });
}