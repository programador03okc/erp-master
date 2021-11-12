function enviarOrdenSoftlink() {
    let id_orden = $('[name=id_orden]').val();

    $.ajax({
        type: 'GET',
        url: 'migrarOrdenCompra/' + id_orden,
        // data: data,
        dataType: 'JSON',
    }).done(function (response) {
        console.log(response);
        console.log('response');
        console.log(response.ocSoftlink.cabecera.cod_docu);
        $('[name=codigo_orden]').val(response.ocSoftlink.cabecera.cod_docu + ' ' + response.ocSoftlink.cabecera.num_docu);
        Lobibox.notify(response.tipo, {
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: response.mensaje
        });

    }).always(function () {
        // $modal.find('div.modal-body').LoadingOverlay("hide", true);

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