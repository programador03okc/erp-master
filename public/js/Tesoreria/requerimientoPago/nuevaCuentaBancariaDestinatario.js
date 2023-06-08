var nombreModalPadre ='';

function restablecerDefaultModalCuentaDestinatario(){
    document.querySelector("div[id='modal-nueva-cuenta-bancaria-destinatario'] select[name='banco']").value = 1;
    document.querySelector("div[id='modal-nueva-cuenta-bancaria-destinatario'] select[name='tipo_cuenta_banco']").value = 1;
    document.querySelector("div[id='modal-nueva-cuenta-bancaria-destinatario'] select[name='moneda']").value = 1;
    document.querySelector("div[id='modal-nueva-cuenta-bancaria-destinatario'] input[name='nro_cuenta']").value = '';
    document.querySelector("div[id='modal-nueva-cuenta-bancaria-destinatario'] input[name='nro_cuenta_interbancaria']").value = '';
    document.querySelector("div[id='modal-nueva-cuenta-bancaria-destinatario'] input[name='swift']").value = '';

}

function obtenerNombreModalPadre(){

    if(document.querySelector("div[class='modal fade in']") !=null && document.querySelector("div[class='modal fade in']").getAttribute("id")){
        nombreModalPadre="div[id='"+document.querySelector("div[class='modal fade in']").getAttribute("id")+"']";
    }
}

function modalNuevaCuentaDestinatario() {
    restablecerDefaultModalCuentaDestinatario();

    obtenerNombreModalPadre();

    let idTipoDestinatario = document.querySelector(nombreModalPadre+" select[name='id_tipo_destinatario']").value;
    let idPersona = document.querySelector(nombreModalPadre+" input[name='id_persona']").value;
    let idContribuyente = document.querySelector(nombreModalPadre+" input[name='id_contribuyente']").value;
    if (idTipoDestinatario > 0 && (idPersona > 0 || idContribuyente > 0)) {
        $("#modal-nueva-cuenta-bancaria-destinatario").modal({
            show: true
        });

        document.querySelector("div[id='modal-nueva-cuenta-bancaria-destinatario'] span[id='nombre_destinatario']").textContent = document.querySelector(nombreModalPadre+" input[name='nombre_destinatario']").value;
        document.querySelector("div[id='modal-nueva-cuenta-bancaria-destinatario'] input[name='id_tipo_destinatario']").value = idTipoDestinatario;
        document.querySelector("div[id='modal-nueva-cuenta-bancaria-destinatario'] input[name='id_persona']").value = idPersona;
        document.querySelector("div[id='modal-nueva-cuenta-bancaria-destinatario'] input[name='id_contribuyente']").value = idContribuyente;

        $('.limpiar').val('');
    } else {
        Swal.fire(
            'Nueva cuenta',
            'Primero debe seleccionar una persona o contribuyente',
            'info'
        );
    }
}

// ###=========== inicio nueva cuanta bancaria destinatario ==========###

$("#form-nueva-cuenta-bancaria-destinatario").on("submit", function (e) {
    e.preventDefault();
    var data = $(this).serialize();

    let cta = ($('div[id="modal-nueva-cuenta-bancaria-destinatario"] [name=nro_cuenta]').val()).trim();
    let txt = '';

    if (cta == '') {
        txt += (cta == '' ? (txt == '' ? 'cuenta ' : ', cuenta ') : '');

        Swal.fire({
            title: "Es necesario que ingrese por lo menos " + txt,
            icon: "error",
        });
    } else {
        guardarCuentaBancariaDestinatario(data);
    }
});

function guardarCuentaBancariaDestinatario(data) {
    const $button = $("#submit_nuevo_contribuyente");
    $button.prop('disabled', true);
    $button.html('Guardando...');
    $.ajax({
        type: 'POST',
        url: 'guardar-cuenta-destinatario',
        data: data,
        dataType: 'JSON',
    }).done(function (response) {
        Lobibox.notify(response.tipo_estado, {
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: response.mensaje
        });
        if (response.tipo_estado == 'success') {
            $('#modal-nueva-cuenta-bancaria-destinatario').modal('hide');
            if (response.id_cuenta > 0) {
                if (response.id_tipo_destinatario == 1) {
                    document.querySelector(nombreModalPadre+" input[name='id_cuenta_persona']").value = response.id_cuenta;
                    obtenerCuentasBancariasPersona(document.querySelector(nombreModalPadre+" input[name='id_persona']").value);
                } else if (response.id_tipo_destinatario == 2) {
                    document.querySelector(nombreModalPadre+" input[name='id_cuenta_contribuyente']").value = response.id_cuenta;
                    obtenerCuentasBancariasContribuyente(document.querySelector(nombreModalPadre+" input[name='id_contribuyente']").value);
                }


            } else {
                Lobibox.notify('error', {
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Hubo un problema. no se encontró un id cuenta valido'
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
// ###=========== fin nueva cuanta bancaria destinatario ==========###


// ###=========== obtener y actualizar select cuenta bancaria ==========###
function obtenerCuentasBancariasPersona(id_persona) {
    let option = ``;
    // console.log(id_persona);
    if (id_persona > 0) {
        $.ajax({
            type: 'GET',
            url: 'obtener-cuenta-persona/' + id_persona,
            dataType: 'JSON',
        }).done(function (response) {
            // console.log(response);
            if (response.tipo_estado == 'success') {

                if (response.data.length > 0) {

                    // llenar cuenta bancaria
                    let idCuentePorDefecto = document.querySelector(nombreModalPadre+" input[name='id_cuenta_persona']").value;
                    document.querySelector(nombreModalPadre+" select[name='id_cuenta']").value = "";
                    let selectCuenta = document.querySelector(nombreModalPadre+" select[name='id_cuenta']");
                    if (selectCuenta != null) {
                        while (selectCuenta.children.length > 0) {
                            selectCuenta.removeChild(selectCuenta.lastChild);
                        }
                    }
                    (response.data).forEach(element => {
                        option += `
                        <option
                            data-nro-cuenta="${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : ''}"
                            data-nro-cci="${element.nro_cci != null && element.nro_cci != "" ? element.nro_cci : ''}"
                            data-tipo-cuenta="${element.tipo_cuenta != null ? element.tipo_cuenta.descripcion : ''}"
                            data-banco="${element.banco != null && element.banco.contribuyente != null ? element.banco.contribuyente.razon_social : ''}"
                            data-moneda="${element.moneda != null ? element.moneda.descripcion : ''}"
                            value="${element.id_cuenta_bancaria}" ${element.id_cuenta_bancaria == idCuentePorDefecto ? 'selected':''}>
                            ${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : (element.nro_cci != null && element.nro_cci != "" ? (element.nro_cci + " (CCI)") : "")}
                        </option>`;
                        document.querySelector(nombreModalPadre+" select[name='id_cuenta']").insertAdjacentHTML('beforeend', option);
                    });
                    // console.log(response);
                    // document.querySelector(nombreModalPadre+" select[name='id_cuenta']").insertAdjacentHTML('beforeend', `<option value="" selected>Seleccione...</option>`);
                    // $('#form-requerimiento-pago .modal-body select[name="id_cuenta"]').append('<option value="" selected="true">Seleccione...</option>');

                    if(idCuentePorDefecto==null || idCuentePorDefecto==''){
                        document.querySelector(nombreModalPadre+" input[name='id_cuenta_persona']").value=document.querySelector(nombreModalPadre+" select[name='id_cuenta']").value;
                    }


                } else {
                    Lobibox.notify('error', {
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: 'Hubo un problema. no se encontró un id cuenta valido'
                    });
                }

            } else {
                console.log(response);
                //limpiar cuenta
                document.querySelector(nombreModalPadre+" select[name='id_cuenta']").value = "";
                let selectCuenta = document.querySelector(nombreModalPadre+" select[name='id_cuenta']");
                if (selectCuenta != null) {
                    while (selectCuenta.children.length > 0) {
                        selectCuenta.removeChild(selectCuenta.lastChild);
                    }
                }
                Lobibox.notify(response.tipo_estado, {
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: response.mensaje
                });
            }

        }).always(function () {

        }).fail(function (jqXHR) {
            $("select[name='id_cuenta']").LoadingOverlay("hide", true);

            Lobibox.notify('error', {
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Hubo un problema. Por favor actualice la página e intente de nuevo.'
            });
            console.log('Error devuelto: ' + jqXHR.responseText);
        });
    } else {
        $("select[name='id_cuenta']").LoadingOverlay("hide", true);

        Lobibox.notify('error', {
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Hubo un problema. no se encontró un id persona valido para obtener una respuesta'
        });
    }

}

function obtenerCuentasBancariasContribuyente(id_contribuyente, id_cuenta=null) {

    obtenerNombreModalPadre();

    let option = ``;
    // let idMonedaDocumento = document.querySelector(nombreModalPadre+" input[name='id_moneda']").value;
    // console.log(idMonedaDocumento);
    if (id_contribuyente > 0) {
        $.ajax({
            type: 'GET',
            url: 'obtener-cuenta-contribuyente/' + id_contribuyente,
            dataType: 'JSON',
        }).done(function (response) {
            // console.log(response);
            // console.log(response);//no este
            if (response.tipo_estado == 'success') {
                if (response.data.length > 0) {
                    // llenar cuenta bancaria

                    // let idCuentePorDefecto =document.querySelector(nombreModalPadre+" input[name='id_cuenta_contribuyente']").value;
                    // let idCuentePorDefecto =0;
                    document.querySelector(nombreModalPadre+" select[name='id_cuenta']").value = "";
                    let selectCuenta = document.querySelector(nombreModalPadre+" select[name='id_cuenta']");
                    if (selectCuenta != null) {
                        while (selectCuenta.children.length > 0) {
                            selectCuenta.removeChild(selectCuenta.lastChild);
                        }
                    }

                    (response.data).forEach(element => {
                        
                            option += `
                            <option
                                data-nro-cuenta="${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : ''}"
                                data-nro-cci="${element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != "" ? element.nro_cuenta_interbancaria : ''}"
                                data-tipo-cuenta="${element.tipo_cuenta != null ? element.tipo_cuenta.descripcion : ''}"
                                data-banco="${element.banco != null && element.banco.contribuyente != null ? element.banco.contribuyente.razon_social : ''}"
                                data-moneda="${element.moneda != null ? element.moneda.descripcion : ''}"
                                value="${element.id_cuenta_contribuyente}" ${ id_cuenta >0 && element.id_cuenta_contribuyente == id_cuenta ? 'selected' : ''}>
                                ${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : (element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != "" ? (element.nro_cuenta_interbancaria + " (CCI)") : "")}
                                ${element.moneda != null ? ' ('+element.moneda.descripcion+')' : ''}
                            </option>`;

                        
                    });
                    document.querySelector(nombreModalPadre+" select[name='id_cuenta']").insertAdjacentHTML('beforeend', option);

                    if(idCuentePorDefecto==null || idCuentePorDefecto==''){
                        document.querySelector(nombreModalPadre+" input[name='id_cuenta_contribuyente']").value=document.querySelector(nombreModalPadre+" select[name='id_cuenta']").value;
                    }

                } else {
                    Lobibox.notify('error', {
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: 'Hubo un problema. no se encontró un id cuenta valido'
                    });
                }

            } else {
                console.log(response);
                //limpiar cuenta
                document.querySelector(nombreModalPadre+" select[name='id_cuenta']").value = "";
                let selectCuenta = document.querySelector(nombreModalPadre+" select[name='id_cuenta']");
                if (selectCuenta != null) {
                    while (selectCuenta.children.length > 0) {
                        selectCuenta.removeChild(selectCuenta.lastChild);
                    }
                }
                Lobibox.notify(response.tipo_estado, {
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: response.mensaje
                });
            }

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
    } else {
        Lobibox.notify('error', {
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Hubo un problema. no se encontró un id persona valido para obtener una respuesta'
        });
    }
}
// ###=========== obtener y actualizar select cuenta bancaria ==========###
