
function agregarCliente() {
    $("#modal-cliente").modal({
        show: true
    });
    $('.limpiar').val('');
}

$("#form-cliente").on("submit", function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);

    let ruc = ($('[name=nro_documento]').val()).trim();
    let nom = ($('[name=razon_social]').val()).trim();
    let txt = '';

    if (ruc == '' || nom == '') {
        txt += (ruc == '' ? 'ruc ' : '');
        txt += (nom == '' ? (txt == '' ? 'razon social ' : ', razon social ') : '');

        Swal.fire({
            title: "Es necesario que ingrese por lo menos " + txt,
            icon: "error",
        });
    } else {
        guardarCliente(data);
    }
});

function guardarCliente(data) {
    // $('#submit_nuevo_transportista').prop('disabled', 'true');
    const $button = $("#submit_nuevo_cliente");
    $button.prop('disabled', true);
    $button.html('Guardando...');
    $.ajax({
        type: 'POST',
        url: 'guardarCliente',
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
            $('#modal-cliente').modal('hide');
            listarClientes();
        } else {
            console.log('Error devuelto: ' + response.error);
        }

    }).always(function () {
        $button.prop('disabled', false);
        $button.html('Guardar');
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

function cerrarCliente() {
    $('#modal-cliente').modal('hide');
}