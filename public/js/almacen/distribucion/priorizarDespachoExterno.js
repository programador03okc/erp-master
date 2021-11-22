
function priorizar() {
    $('.limpiar').val('');
    var valida = 0;

    despachos_seleccionados.forEach(element => {
        console.log(element);
        if (element == null) {
            valida++;
        }
    });

    if (valida > 0) {
        Lobibox.notify("error", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Hay ' + valida + ' requerimientos que no tienen Despacho Externo.'
        });
    }
    else {
        $('#modal-priorizarDespachoExterno').modal("show");
    }
}

$("#form-priorizarDespachoExterno").on("submit", function (e) {
    // let fdesp = $('[name=fecha_despacho]').val();
    // let ffact = $('[name=fecha_facturacion]').val();
    // let comen = $('[name=comentario]').val();

    // Swal.fire({
    //     title: "¿Está seguro que desea priorizar con la fecha: " + formatDate(fecha) + "?",
    //     icon: "warning",
    //     showCancelButton: true,
    //     confirmButtonColor: "#00a65a", //"#3085d6",
    //     cancelButtonColor: "#d33",
    //     cancelButtonText: "Cancelar",
    //     confirmButtonText: "Sí, Guardar"
    // }).then(result => {

    //     if (result.isConfirmed) {
    e.preventDefault();
    var data = $(this).serialize() +
        '&despachos_externos=' + JSON.stringify(despachos_seleccionados);
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'priorizar',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            if (response == 'ok') {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Despachos Externos priorizados correctamente.'
                });
                $('#modal-priorizarDespachoExterno').modal("hide");
                $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
            } else {
                Lobibox.notify("error", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Ha ocurrido un error interno. Inténtelo nuevamente.'
                });
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    //     }
    // });
});