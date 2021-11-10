function openOrdenDespachoEnviar(id, fecha, cdp) {
    $('#modal-orden_despacho_enviar').modal({
        show: true
    });

    $('[name=id_requerimiento]').val(id);
    $('#codigo_cdp').text(cdp);

    var msj = "Por favor hacer seguimiento a este pedido. Vence: " + fecha +
        "\nFECHA DE DESPACHO: \n" +
        "\nFavor de generar documentación: " +
        "\n• FACTURA " +
        "\n• GUIA" +
        "\n• CERTIFICADO DE GARANTIA " +
        "\n• CCI" +
        "\n\nSaludos, \n" + usuarioSesion;
    $('[name=mensaje]').val(msj);
}

$('#submit_orden_despacho_enviar').on('click', function (params) {

    // Swal.fire({
    //     title: "¿Está seguro que desea enviar la Orden de Despacho?",
    //     icon: "warning",
    //     showCancelButton: true,
    //     confirmButtonColor: "#00a65a", //"#3085d6",
    //     cancelButtonColor: "#d33",
    //     cancelButtonText: "Cancelar",
    //     confirmButtonText: "Sí, Enviar"
    // }).then(result => {
    //     if (result.isConfirmed) {
    guardar_orden_despacho();
    //     }
    // });
});

function guardar_orden_despacho() {
    const $submit = $("#submit_orden_despacho_enviar");
    $submit.prop('disabled', 'true');
    $submit.html('Enviando...');
    let formData = new FormData(document.getElementById("form-orden_despacho_enviar"));

    $.ajax({
        type: 'POST',
        url: 'guardarOrdenDespachoExterno',
        data: formData,
        processData: false,
        contentType: false,
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
            $('#modal-orden_despacho_enviar').modal('hide');
            $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
        } else {
            console.log('Error devuelto: ' + response.error);
        }
    }).always(function () {
        $submit.prop('disabled', false);
        $submit.html('Enviar');
    }).fail(function () {
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