
$("#form-cierreIncidencia").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "¿Está seguro que desea cerrar la incidencia?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Cerrar"
    }).then(result => {

        if (result.isConfirmed) {
            var data = $(this).serialize();
            console.log(data);
            cerrarIncidencia(data);
            $("#listaIncidencias").DataTable().ajax.reload(null, false);
        }
    });
});

function cerrarIncidencia(data) {
    $("#submit_guardar_cierre").attr('disabled', true);
    $.ajax({
        type: 'POST',
        url: 'cerrarIncidencia',
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

            $("#submit_guardar_cierre").attr('disabled', false);
            $('#modal-cierreIncidencia').modal('hide');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}