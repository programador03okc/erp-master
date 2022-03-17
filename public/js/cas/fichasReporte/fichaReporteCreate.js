
$("#form-fichaReporte").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "¿Está seguro que desea guardar la ficha reporte?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {
        if (result.isConfirmed) {
            guardarFichaReporte();
        }
    });
});

function guardarFichaReporte() {
    $("#submit_guardar_reporte").attr('disabled', true);
    var id = $('[name=id_incidencia_reporte]').val();
    var formData = new FormData($('#form-fichaReporte')[0]);
    var url = '';

    if (id !== '') {
        url = 'actualizarFichaReporte';
    } else {
        url = 'guardarFichaReporte';
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
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

            $("#listaIncidencias").DataTable().ajax.reload(null, false);
            $("#submit_guardar_reporte").attr('disabled', false);
            $('#modal-fichaReporte').modal('hide');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}