
function openRegistroPago(data) {
    var id = data.data('id');
    var tipo = data.data('tipo');
    var codigo = data.data('cod');
    var total = data.data('total');
    var pago = (data.data('pago') !== null ? parseFloat(data.data('pago')) : 0);
    var moneda = data.data('moneda');
    var nrodoc = data.data('nrodoc');
    var prov = data.data('prov');
    var tpcta = data.data('tpcta');
    var cta = data.data('cta');
    var cci = data.data('cci');

    var total_pago = formatDecimal(parseFloat(total) - pago);
    console.log(cta);

    const $modal = $('#modal-procesarPago');
    $modal.modal({
        show: true
    });
    //Limpieza para seleccionar archivo
    $modal.find('input[type=file]').val(null);
    $modal.find('div.bootstrap-filestyle').find('input[type=text]').val('');

    if (tipo == 'requerimiento') {
        $('[name=id_requerimiento_pago]').val(id);
        $('[name=id_oc]').val('');
        $('[name=id_doc_com]').val('');
    }
    else if (tipo == 'orden') {
        $('[name=id_requerimiento_pago]').val('');
        $('[name=id_oc]').val(id);
        $('[name=id_doc_com]').val('');
    }
    else if (tipo == 'comprobante') {
        $('[name=id_requerimiento_pago]').val('');
        $('[name=id_oc]').val('');
        $('[name=id_doc_com]').val(id);
    }

    $('[name=codigo]').val(codigo);
    $('[name=cod_serie_numero]').text(codigo);

    $('[name=total_pago]').val(total_pago);
    $('[name=total]').val(total_pago);
    $('[name=total_pagado]').text(formatNumber.decimal(pago, moneda, -2));
    $('[name=monto_total]').text(formatNumber.decimal(total, moneda, -2));

    $('[name=observacion]').val('');
    $('[name=id_empresa]').val('');
    $('[name=id_cuenta_origen]').val('');
    $('[name=simbolo]').val(moneda);
    $('[name=nro_documento]').text(nrodoc !== 'undefined' ? nrodoc : '');
    $('[name=razon_social]').text(decodeURIComponent(prov));
    $('[name=tp_cta_bancaria]').text(cta !== 'undefined' ? tpcta : '');
    $('[name=cta_bancaria]').text(cta !== 'undefined' ? cta : '');
    $('[name=cta_cci]').text(cci !== 'undefined' ? cci : '');

    $('#submit_procesarPago').removeAttr('disabled');
}

$("#form-procesarPago").on("submit", function (e) {
    e.preventDefault();
    $('#submit_procesarPago').attr('disabled', 'true');
    procesarPago();
});

function procesarPago() {
    var formData = new FormData($('#form-procesarPago')[0]);
    var id_oc = $('[name=id_oc]').val();
    var id_doc_com = $('[name=id_doc_com]').val();
    var id_requerimiento_pago = $('[name=id_requerimiento_pago]').val();
    console.log(formData);

    $.ajax({
        type: 'POST',
        url: 'procesarPago',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $('#modal-procesarPago').modal('hide');

            if (id_oc !== '') {
                $('#listaOrdenes').DataTable().ajax.reload(null, false);
            }
            else if (id_doc_com !== '') {
                $('#listaComprobantes').DataTable().ajax.reload(null, false);
            }
            else if (id_requerimiento_pago !== '') {
                $('#listaRequerimientos').DataTable().ajax.reload(null, false);
            }
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Pago registrado con éxito.'
            });

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listarCuentasOrigen() {
    var id_empresa = $('[name=id_empresa]').val();
    $("select[name='id_cuenta_origen']").LoadingOverlay("show", {
        imageAutoResize: true,
        progress: true,
        imageColor: "#3c8dbc"
    });
    console.log(id_empresa);
    $.ajax({
        type: 'GET',
        url: 'cuentasOrigen/' + id_empresa,
        dataType: 'JSON',
    }).done(function (response) {
        console.log(response);
        var option = '<option value="">Seleccione una cuenta</option>';

        if (response.length == 1) {
            response.forEach(element => {
                option += `<option value="${element.id_cuenta_contribuyente}" selected>${element.nro_cuenta}</option>`
            });
        } else {
            response.forEach(element => {
                option += `<option value="${element.id_cuenta_contribuyente}">${element.nro_cuenta}</option>`
            });
        }
        $('#id_cuenta_origen').html(option);

    }).always(function () {
        // $('#id_empresa').LoadingOverlay("hide", true);
        $("select[name='id_cuenta_origen']").LoadingOverlay("hide", true);
    }).fail(function (jqXHR) {
        Lobibox.notify('error', {
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Hubo un problema. Por favor actualice la página e intente de nuevo.'
        });
        //Cerrar el modal
        // $modal.modal('hide');
        console.log('Error devuelto: ' + jqXHR.responseText);
    });
}

function enviarAPago(tipo, id) {

    console.log(tipo);

    Swal.fire({
        title: "¿Está seguro que desea enviar a pago?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6", //"#00a65a",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Enviar"
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'enviarAPago',// + tipo + '/' + id,
                data: {
                    'tipo': tipo,
                    'id': id,
                },
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
                if (tipo == "orden") {
                    tableOrdenes.ajax.reload(null, false);
                }
                else if (tipo == "requerimiento") {
                    tableRequerimientos.ajax.reload(null, false);
                }
                else if (tipo == "orden") {
                    tableComprobantes.ajax.reload(null, false);
                }
            }).always(function () {
                // $("select[name='id_cuenta_origen']").LoadingOverlay("hide", true);
            }).fail(function (jqXHR) {
                Lobibox.notify('error', {
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Hubo un problema. Por favor actualice la página e intente de nuevo.'
                });
                //Cerrar el modal
                // $modal.modal('hide');
                console.log('Error devuelto: ' + jqXHR.responseText);
            });
        }
    });
}

function revertirEnvio(tipo, id) {

    console.log(tipo);

    Swal.fire({
        title: "¿Está seguro que desea revertir el envío?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6", //"#00a65a",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Revertir"
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'revertirEnvio',
                data: {
                    'tipo': tipo,
                    'id': id,
                },
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
                if (tipo == "orden") {
                    tableOrdenes.ajax.reload(null, false);
                }
                else if (tipo == "requerimiento") {
                    tableRequerimientos.ajax.reload(null, false);
                }
                else if (tipo == "orden") {
                    tableComprobantes.ajax.reload(null, false);
                }
            }).always(function () {
                // $("select[name='id_cuenta_origen']").LoadingOverlay("hide", true);
            }).fail(function (jqXHR) {
                Lobibox.notify('error', {
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Hubo un problema. Por favor actualice la página e intente de nuevo.'
                });
                //Cerrar el modal
                // $modal.modal('hide');
                console.log('Error devuelto: ' + jqXHR.responseText);
            });
        }
    });
}

function anularPago(id_pago, tipo) {

    console.log(tipo);

    Swal.fire({
        title: "¿Está seguro que anular éste pago?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Anular"
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'GET',
                url: 'anularPago/' + id_pago,
                dataType: 'JSON',
            }).done(function (response) {
                console.log(response);
                Lobibox.notify('success', {
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: response
                });
                if (tipo == "orden") {
                    tableOrdenes.ajax.reload(null, false);
                }
                else if (tipo == "requerimiento") {
                    tableRequerimientos.ajax.reload(null, false);
                }
                else if (tipo == "orden") {
                    tableComprobantes.ajax.reload(null, false);
                }
            }).always(function () {
                // $("select[name='id_cuenta_origen']").LoadingOverlay("hide", true);
            }).fail(function (jqXHR) {
                Lobibox.notify('error', {
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Hubo un problema. Por favor actualice la página e intente de nuevo.'
                });
                //Cerrar el modal
                // $modal.modal('hide');
                console.log('Error devuelto: ' + jqXHR.responseText);
            });
        }
    });
}
