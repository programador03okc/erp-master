let listaContactos = [];

function open_despacho_create(data) {

    $('#modal-orden_despacho_contacto').modal({
        show: true
    });
    $("#submit_orden_despacho").removeAttr("disabled");
    $('#codigo_req').text(data.codigo_oportunidad + ' - ' + data.codigo);
    $('.limpiar').text('');

    console.log(data);

    $('[name=id_requerimiento]').val(data.id_requerimiento ?? 0);
    $('[name=id_contribuyente]').val(data.id_contribuyente ?? 0);
    $('[name=id_entidad]').val(data.id_entidad ?? '0');
    $('[name=id_contacto_od]').val(data.id_contacto ?? '');

}

$('#modal-orden_despacho_contacto').on('shown.bs.modal', function (e) {
    verDatosContacto($('[name=id_requerimiento]').val(), $('[name=id_entidad]').val());
})

function verDatosContacto(id_requerimiento, id_entidad) {
    $('#listaContactos tbody').html('');
    const $modal = $('#modal-orden_despacho_contacto');

    $modal.find('div.modal-body').LoadingOverlay("show", {
        imageAutoResize: true,
        imageColor: "#3c8dbc"
    });

    let data = 'id_requerimiento=' + id_requerimiento + '&id_entidad=' + id_entidad;

    $.ajax({
        type: 'POST',
        url: 'verDatosContacto',
        data: data,
        dataType: 'JSON',
    }).done(function (response) {
        console.log(response);

        if (response['entidad'] !== null) {
            $modal.find('.ruc').text(response['entidad'].ruc ?? '');
            $('.nombre').text(response['entidad'].nombre ?? '');
            $('.direccion').text(response['entidad'].direccion ?? '');
            $('.ubigeo').text(response['entidad'].ubigeo ?? '');
            $('.responsable').text(response['entidad'].responsable ?? '');
            $('.cargo').text(response['entidad'].cargo ?? '');
            $('.telefono').text(response['entidad'].telefono ?? '');
            $('.correo').text(response['entidad'].correo ?? '');
        }
        listaContactos = response['lista'];
        mostrarContactos(response['id_contacto']);

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

function listarContactos(id_contribuyente, id_contacto) {
    $('#fieldsetListaContactos').LoadingOverlay("show", {
        imageAutoResize: true,
        imageColor: "#3c8dbc"
    });

    $.ajax({
        type: 'GET',
        url: 'listarContactos/' + id_contribuyente,
        dataType: 'JSON',
    }).done(function (response) {
        console.log('listarContactos');
        console.log(response);
        listaContactos = response;
        mostrarContactos(id_contacto);

    }).always(function () {
        $('#fieldsetListaContactos').LoadingOverlay("hide", true);

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

function mostrarContactos(id_contacto) {
    $('#listaContactos tbody').html('');
    var html = '';
    console.log('id_contacto' + id_contacto)
    listaContactos.forEach(element => {
        html += `<tr>
            <td>${parseInt(element.id_datos_contacto) == parseInt(id_contacto)
                ? '<i class="fas fa-check green" style="font-size: 15px;"></i>'
                : ''}</td>
            <td>${element.nombre}</td>
            <td>${element.telefono}</td>
            <td>${element.cargo}</td>
            <td>${element.email}</td>
            <td>${element.direccion}</td>
            <td>${element.horario}</td>
            <td>
                <div style="display:flex;">
                    <button type="button" class="seleccionar btn btn-success btn-flat btn-xs boton" 
                        data-toggle="tooltip" data-placement="bottom" data-id="${element.id_datos_contacto}" title="Seleccionar contacto">
                        <i class="fas fa-check"></i></button>
                    <button type="button" class="editar btn btn-primary btn-flat btn-xs boton" 
                        data-toggle="tooltip" data-placement="bottom" data-id="${element.id_datos_contacto}" title="Editar contacto">
                        <i class="fas fa-pencil-alt"></i></button>
                    <button type="button" class="anular btn btn-danger btn-flat btn-xs boton" 
                        data-toggle="tooltip" data-placement="bottom" data-id="${element.id_datos_contacto}" title="Anular contacto">
                        <i class="fas fa-trash"></i></button>
                </div>
            </td>
            </tr>`;
    });
    $('#listaContactos tbody').html(html);
}

function cerrarContacto() {
    $('#modal-orden_despacho_contacto').modal('hide');
}

function enviarDatosContacto() {
    let id_requerimiento = $('[name=id_requerimiento]').val();
    let data = 'id_requerimiento=' + id_requerimiento;

    const $button = $("#btn_enviar_correo");
    $button.prop('disabled', 'true');
    $button.html('Enviando...');

    $.ajax({
        type: 'POST',
        url: 'enviarDatosContacto',
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
            $('#modal-orden_despacho_contacto').modal('hide');
            // $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
        } else {
            console.log('Error devuelto: ' + response.error);
        }
    }).always(function () {
        $button.prop('disabled', false);
        $button.html('Enviar correo');
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