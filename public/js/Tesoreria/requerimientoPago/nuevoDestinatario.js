function modalNuevoDestinatario() {

    let idTipoDestinatario = document.querySelector("div[id='modal-requerimiento-pago'] select[name='id_tipo_destinatario']").value;
    if (idTipoDestinatario == 1) { // tipo persona
        $("#modal-nueva-persona").modal({
            show: true
        });
        $('.limpiar').val('');

    } else if (idTipoDestinatario == 2) {  // tipo contribuyente
        $("#modal-nuevo-contribuyente").modal({
            show: true
        });
        $('.limpiar').val('');

    }else{
        Swal.fire(
            'Nuevo destinatario',
            'Primero debe seleccionar un tipo de destinatario',
            'info'
        );
    }
}

// ###=========== inicio contribuyente ==========###

$("#form-nuevo-contribuyente").on("submit", function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);

    let doc = ($('div[id="modal-nuevo-contribuyente"] [name=nuevo_nro_documento]').val()).trim();
    let nom = ($('div[id="modal-nuevo-contribuyente"] [name=nuevo_razon_social]').val()).trim();
    let txt = '';

    if (doc == '' || nom == '') {
        txt += (doc == '' ? 'nro documento ' : '');
        txt += (nom == '' ? (txt == '' ? 'razon social ' : ', razon social ') : '');

        Swal.fire({
            title: "Es necesario que ingrese por lo menos " + txt,
            icon: "error",
        });
    } else {
        guardarContribuyente(data);
    }
});

function guardarContribuyente(data) {
    const $button = $("#submit_nuevo_contribuyente");
    $button.prop('disabled', true);
    $button.html('Guardando...');
    $.ajax({
        type: 'POST',
        url: 'guardar-contribuyente',
        data: data,
        dataType: 'JSON',
    }).done(function (response) {
        console.log(response);
        Lobibox.notify(response.tipo_estado, {
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: response.mensaje
        });
        if (response.tipo_estado == 'success') {
            $('#modal-nuevo-contribuyente').modal('hide');
            if (response.id_contribuyente > 0) {
                document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_contribuyente']").value = response.id_contribuyente;
                document.querySelector("div[id='modal-requerimiento-pago'] input[name='tipo_documento_identidad']").value = document.querySelector("div[id='modal-nuevo-contribuyente'] select[name='id_doc_identidad']").options[document.querySelector("div[id='modal-nuevo-contribuyente'] select[name='id_doc_identidad']").selectedIndex].textContent;
                document.querySelector("div[id='modal-requerimiento-pago'] input[name='nro_documento']").value = document.querySelector("div[id='modal-nuevo-contribuyente'] input[name='nuevo_nro_documento']").value;
                document.querySelector("div[id='modal-requerimiento-pago'] input[name='nombre_destinatario']").value = document.querySelector("div[id='modal-nuevo-contribuyente'] input[name='nuevo_razon_social']").value;
            } else {
                Lobibox.notify('error', {
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Hubo un problema. no se encontró un id contribuyente valido'
                });
            }

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
// ###=========== fin contribuyente ==========###

// ###=========== inicia persona ==========###

$("#form-nueva-persona").on("submit", function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);

    let doc = ($('div[id="modal-nueva-persona"] [name=nuevo_nro_documento]').val()).trim();
    let nom = ($('div[id="modal-nueva-persona"] [name=nuevo_nombres]').val()).trim();
    let apep = ($('div[id="modal-nueva-persona"] [name=nuevo_apellido_paterno]').val()).trim();
    let apem = ($('div[id="modal-nueva-persona"] [name=nuevo_apellido_materno]').val()).trim();
    let txt = '';

    if (doc == '' || nom == '' || apep =='' || apem =='') {
        txt += (doc == '' ? 'nro documento de identidad' : '');
        txt += (nom == '' ? (txt == '' ? 'nombres ' : ', nombres ') : '');
        txt += (apep == '' ? (txt == '' ? 'apellido paterno ' : ', apellido paterno ') : '');
        txt += (apem == '' ? (txt == '' ? 'apellido materno ' : ', apellido materno ') : '');

        Swal.fire({
            title: "Es necesario que ingrese por lo menos " + txt,
            icon: "error",
        });
    } else {
        guardarPersona(data);
    }
});

function guardarPersona(data) {
    const $button = $("#submit_nueva_persona");
    $button.prop('disabled', true);
    $button.html('Guardando...');
    $.ajax({
        type: 'POST',
        url: 'guardar-persona',
        data: data,
        dataType: 'JSON',
    }).done(function (response) {
        console.log(response);
        Lobibox.notify(response.tipo_estado, {
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: response.mensaje
        });
        if (response.tipo_estado == 'success') {
            $('#modal-nueva-persona').modal('hide');
            if (response.id_persona > 0) {
                document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_persona']").value = response.id_persona;
                document.querySelector("div[id='modal-requerimiento-pago'] input[name='tipo_documento_identidad']").value = document.querySelector("div[id='modal-nueva-persona'] select[name='id_doc_identidad']").options[document.querySelector("div[id='modal-nueva-persona'] select[name='id_doc_identidad']").selectedIndex].textContent;
                document.querySelector("div[id='modal-requerimiento-pago'] input[name='nro_documento']").value = document.querySelector("div[id='modal-nueva-persona'] input[name='nuevo_nro_documento']").value;
                document.querySelector("div[id='modal-requerimiento-pago'] input[name='nombre_destinatario']").value = (document.querySelector("div[id='modal-nueva-persona'] input[name='nuevo_nombres']").value).concat(' ',document.querySelector("div[id='modal-nueva-persona'] input[name='nuevo_apellido_paterno']").value).concat(' ',document.querySelector("div[id='modal-nueva-persona'] input[name='nuevo_apellido_materno']").value ) ;
            } else {
                Lobibox.notify('error', {
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Hubo un problema. no se encontró un id persona valido'
                });
            }

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
// ###=========== fin persona ==========###
