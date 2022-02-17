var nombreModalPadre= ''

function modalNuevoDestinatario() {

    if(document.querySelector("div[class='modal fade in']").getAttribute("id")){
        nombreModalPadre="div[id='"+document.querySelector("div[class='modal fade in']").getAttribute("id")+"']";
    }

    let idTipoDestinatario = document.querySelector(nombreModalPadre+" select[name='id_tipo_destinatario']").value;
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

function obtenerContribuyente(id) {

    $.ajax({
        type: 'GET',
        url: 'obtener-contribuyente/'+id,
        dataType: 'JSON',
    }).done(function (response) {
        // console.log(response);

        document.querySelector(nombreModalPadre+" input[name='tipo_documento_identidad']").value = response.tipo_documento_identidad.descripcion;
        document.querySelector(nombreModalPadre+" input[name='nro_documento']").value = response.nro_documento;
        document.querySelector(nombreModalPadre+" input[name='nombre_destinatario']").value = response.razon_social;

    }).always(function () {
    
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
                document.querySelector(nombreModalPadre+" input[name='id_contribuyente']").value = response.id_contribuyente;
                document.querySelector(nombreModalPadre+" input[name='tipo_documento_identidad']").value = document.querySelector("div[id='modal-nuevo-contribuyente'] select[name='id_doc_identidad']").options[document.querySelector("div[id='modal-nuevo-contribuyente'] select[name='id_doc_identidad']").selectedIndex].textContent;
                document.querySelector(nombreModalPadre+" input[name='nro_documento']").value = document.querySelector("div[id='modal-nuevo-contribuyente'] input[name='nuevo_nro_documento']").value;
                document.querySelector(nombreModalPadre+" input[name='nombre_destinatario']").value = document.querySelector("div[id='modal-nuevo-contribuyente'] input[name='nuevo_razon_social']").value;

                // limpiar cuenta bancaria
                document.querySelector(nombreModalPadre+" input[name='id_cuenta_contribuyente']").value='';
                document.querySelector(nombreModalPadre+" select[name='id_cuenta']").value = "";
                let selectCuenta = document.querySelector(nombreModalPadre+" select[name='id_cuenta']");
                if (selectCuenta != null) {
                    while (selectCuenta.children.length > 0) {
                        selectCuenta.removeChild(selectCuenta.lastChild);
                    }
                }
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

function obtenerPersona(id) {

    $.ajax({
        type: 'GET',
        url: 'obtener-persona/'+id,
        dataType: 'JSON',
    }).done(function (response) {
        // console.log(response);

        document.querySelector(nombreModalPadre+" input[name='tipo_documento_identidad']").value = response.tipo_documento_identidad.descripcion;
        document.querySelector(nombreModalPadre+" input[name='nro_documento']").value = response.nro_documento;
        document.querySelector(nombreModalPadre+" input[name='nombre_destinatario']").value = response.nombre_completo;

    }).always(function () {
    
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
                document.querySelector(nombreModalPadre+" input[name='id_persona']").value = response.id_persona;
                document.querySelector(nombreModalPadre+" input[name='tipo_documento_identidad']").value = document.querySelector("div[id='modal-nueva-persona'] select[name='id_doc_identidad']").options[document.querySelector("div[id='modal-nueva-persona'] select[name='id_doc_identidad']").selectedIndex].textContent;
                document.querySelector(nombreModalPadre+" input[name='nro_documento']").value = document.querySelector("div[id='modal-nueva-persona'] input[name='nuevo_nro_documento']").value;
                document.querySelector(nombreModalPadre+" input[name='nombre_destinatario']").value = (document.querySelector("div[id='modal-nueva-persona'] input[name='nuevo_nombres']").value).concat(' ',document.querySelector("div[id='modal-nueva-persona'] input[name='nuevo_apellido_paterno']").value).concat(' ',document.querySelector("div[id='modal-nueva-persona'] input[name='nuevo_apellido_materno']").value ) ;
                // limpiar cuenta bancaria
                document.querySelector(nombreModalPadre+" input[name='id_cuenta_persona']").value='';
                document.querySelector(nombreModalPadre+" select[name='id_cuenta']").value = "";
                let selectCuenta = document.querySelector(nombreModalPadre+" select[name='id_cuenta']");
                if (selectCuenta != null) {
                    while (selectCuenta.children.length > 0) {
                        selectCuenta.removeChild(selectCuenta.lastChild);
                    }
                }
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
