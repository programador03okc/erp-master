$('#submit_orden_despacho_enviar').on('click', function (params) {


    Swal.fire({
        title: "¿Está seguro que desea enviar la Orden de Despacho?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Enviar"
    }).then(result => {
        if (result.isConfirmed) {
            guardar_orden_despacho();
        }
    });
});

function guardar_orden_despacho() {
    $("#submit_orden_despacho_enviar").prop('disabled', 'true');
    let formData = new FormData(document.getElementById("form-orden_despacho_enviar"));
    console.log(formData);
    $.ajax({
        type: 'POST',
        url: 'guardarOrdenDespachoExterno',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                // title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                // width: 500,
                msg: response.mensaje
            });

            if (response.tipo == 'success') {
                $('#modal-orden_despacho_enviar').modal('hide');
                $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
            } else {
                console.log('Error devuelto: ' + response.error);
            }

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
            // title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            // width: 500,
            msg: 'Hubo un problema. Por favor actualice la página e intente de nuevo.'
        });
        console.log('Error devuelto: ' + jqXHR.responseText);
    });
}