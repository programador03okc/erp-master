
function nuevoProveedor() {
    $("#modal-proveedor").modal({
        show: true
    });
    $('.limpiar').val('');
}

$("#form-proveedor").on("submit", function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);

    let ruc = ($('[name=nuevo_nro_documento]').val()).trim();
    let nom = ($('[name=nuevo_razon_social]').val()).trim();
    let txt = '';

    if (ruc == '' || nom == '') {
        txt += (ruc == '' ? 'ruc ' : '');
        txt += (nom == '' ? (txt == '' ? 'razon social ' : ', razon social ') : '');

        Swal.fire({
            title: "Es necesario que ingrese por lo menos " + txt,
            icon: "error",
        });
    } else {
        guardarProveedor(data);
    }
});

function guardarProveedor(data) {
    const $button = $("#submit_nuevo_proveedor");
    $button.prop('disabled', true);
    $button.html('Guardando...');
    $.ajax({
        type: 'POST',
        url: 'guardarProveedor',
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
            $('#modal-proveedor').modal('hide');
            // listarTablaProveedores();
            $('#ListaProveedores').DataTable().ajax.reload(null, false);
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

function cerrarProveedor() {
    $('#modal-proveedor').modal('hide');
}