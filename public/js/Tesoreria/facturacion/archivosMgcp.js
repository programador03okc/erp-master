function obtenerArchivosMgcp(id, tipo) {
    console.log("id:" + id + "tipo: " + tipo);
    const $modal = $('#modal-archivos_oc_mgcp');

    $modal.modal('show');
    $("#lista_archivos_oc_mgcp").html('');
    $modal.find('div.modal-body').LoadingOverlay("show", {
        imageAutoResize: true,
        imageColor: "#3c8dbc"
    });

    $.ajax({
        type: "POST",
        url: "obtenerArchivosOc",
        data: { id: id, tipo: tipo },
        dataType: "JSON",
    }).done(function (response) {
        console.log(response);
        $("#lista_archivos_oc_mgcp").html(response.archivos);

    }).always(function () {
        $modal.find('div.modal-body').LoadingOverlay("hide", true);

    }).fail(function (jqXHR) {
        Lobibox.notify('error', {
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Hubo un problema. Por favor actualice la página e intente de nuevo.'
        });
        //Cerrar el modal
        $modal.modal('hide');
        console.log('Error devuelto: ' + jqXHR.responseText);
    });
}

function cerrarVentanaArchivos() {
    $("#modal-archivos_oc_mgcp").modal('hide');
}