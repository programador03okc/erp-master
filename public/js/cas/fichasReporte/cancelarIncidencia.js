
$("#form-cancelarIncidencia").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "¿Está seguro que desea cancelar la incidencia?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cerrar",
        confirmButtonText: "Sí, Cancelar"
    }).then(result => {

        if (result.isConfirmed) {
            var data = $(this).serialize();
            console.log(data);
            cancelarIncidencia(data);
            $("#listaIncidencias").DataTable().ajax.reload(null, false);
        }
    });
});

function cancelarIncidencia(data) {
    $("#submit_guardar_cancelacion").attr('disabled', true);
    $.ajax({
        type: 'POST',
        url: 'cancelarIncidencia',
        data: data,
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

            $("#submit_guardar_cancelacion").attr('disabled', false);
            $('#modal-cancelarIncidencia').modal('hide');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}