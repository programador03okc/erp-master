function limpiarTabla(idElement) {
    let nodeTbodyList = document.querySelectorAll("table[id='" + idElement + "'] tbody");
    nodeTbodyList.forEach(element => {
        if (element != null) {
            while (element.children.length > 0) {
                element.removeChild(element.lastChild);
            }
        }
        
    });
}

function openRegistroPago(data) {
    $('#form-procesarPago')[0].reset();
    limpiarTabla('tablaDatosPagoEnCuotas');

    document.querySelector("select[name='vincularCuotaARegistroDePago[]']").innerHTML = ""
    var id = data.data('id');
    var tipo = data.data('tipo');
    var codigo = data.data('cod');
    var total = data.data('total'); // monto_total de cabecera del doc( orden)
    var pago = (data.data('pago') !== null ? parseFloat(data.data('pago')) : 0); // suma de los pagos reallizados
    var moneda = data.data('moneda');
    var nrodoc = data.data('nrodoc');
    var prov = data.data('prov');
    var tpcta = data.data('tpcta');
    var cta = data.data('cta');
    var cci = data.data('cci');
    var banco = data.data('banco');
    var empresa = data.data('empresa');
    var idempresa = data.data('idEmpresa');
    var motivo = data.data('motivo');
    var comentarioPagoLogistica = data.data('comentarioPagoLogistica');
    var observacionRequerimiento = data.data('observacionRequerimiento');
    var cantidadAdjuntosLogisticos = data.data('cantidadAdjuntosLogisticos');
    var tienePagoEnCuotas = data.data('tienePagoEnCuotas');
    var sumaCuotaConAutorizacion = data.data('sumaCuotaConAutorizacion');
    var tipoImpuesto = data.data('tipoImpuesto');

    var total_pago = formatDecimal(parseFloat(total) - pago);
    console.log(data);

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
        $('[name=titulo_motivo]').text('Motivo:');

        document.querySelector("div[id='modal-procesarPago'] div[id='contenedor_adjunto_logistica']").classList.add("oculto");

    }
    else if (tipo == 'orden') {
        $('[name=id_requerimiento_pago]').val('');
        $('[name=id_oc]').val(id);
        $('[name=id_doc_com]').val('');
        $('[name=titulo_motivo]').text('Forma de pago:');

        document.querySelector("div[id='modal-procesarPago'] div[id='contenedor_adjunto_logistica']").classList.remove("oculto");
        document.querySelector("div[id='modal-procesarPago'] div[id='contenedor_adjunto_logistica'] label[name='adjuntoslogistica']").textContent=`Ver(${cantidadAdjuntosLogisticos})`;

        if(tienePagoEnCuotas){
            document.querySelector("div[id='modal-procesarPago'] fieldset[id='fieldsetDatosPagoEnCuotas']").removeAttribute("hidden");
            document.querySelector("div[id='modal-procesarPago'] div[id='contenedorVinculoACuota']").removeAttribute("hidden");
            listarPagoEnCuotas(tipo,id);
            total_pago = formatDecimal(sumaCuotaConAutorizacion);

        }else{
            document.querySelector("div[id='modal-procesarPago'] fieldset[id='fieldsetDatosPagoEnCuotas']").setAttribute("hidden",true);
            document.querySelector("div[id='modal-procesarPago'] div[id='contenedorVinculoACuota']").setAttribute("hidden",true);

        }

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
    $('[name=id_empresa]').val(idempresa ?? '');
    $('[name=id_cuenta_origen]').val('');
    $('[name=simbolo]').val(moneda);
    $('[name=nro_documento]').text(nrodoc !== 'undefined' ? nrodoc : '');
    $('[name=razon_social]').text(decodeURIComponent(prov));
    $('[name=tp_cta_bancaria]').text(cta !== 'undefined' ? tpcta : '');
    $('[name=cta_bancaria]').text(cta !== 'undefined' ? cta : '');
    $('[name=cta_cci]').text(cci !== 'undefined' ? cci : '');
    $('[name=banco]').text(banco !== 'undefined' ? banco : '');
    $('[name=empresa_razon_social]').text(empresa !== 'undefined' ? empresa : '');
    $('[name=motivo]').text(motivo !== undefined ? decodeURIComponent(motivo) : '');
    $('[name=comentario_pago_logistica]').text(comentarioPagoLogistica ?? '');
    $('[name=observacion_requerimiento]').text(observacionRequerimiento ?? '');
    $('[name=tipo_impuesto]').text(tipoImpuesto ==1? 'Detracción':(tipoImpuesto==2?'Renta':''));
    
    if (comentarioPagoLogistica != undefined && comentarioPagoLogistica != '') {
        document.querySelector("div[id='modal-procesarPago'] div[id='contenedor_comentario_pago_logistica']").classList.remove("oculto");
    } else {
        document.querySelector("div[id='modal-procesarPago'] div[id='contenedor_comentario_pago_logistica']").classList.add("oculto");
    }
    if (observacionRequerimiento != undefined && observacionRequerimiento != '') {
        document.querySelector("div[id='modal-procesarPago'] div[id='contenedor_observacion_requerimiento']").classList.remove("oculto");
    } else {
        document.querySelector("div[id='modal-procesarPago'] div[id='contenedor_observacion_requerimiento']").classList.add("oculto");
    }

    listarCuentasOrigen();
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

// function obtenerObservacionRequerimiento() {
//     $.ajax({
//         type: 'GET',
//         url: 'obtenerObserva/' + id,
//         dataType: 'JSON',
//         success: function (response) {
//             console.log(response);
//             $('#modal-verTransferenciasPorRequerimiento').modal({
//                 show: true
//             });
//             $('#transferenciasPorRequerimiento tbody').html(response);
//         }
//     }).fail(function (jqXHR, textStatus, errorThrown) {
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function listarCuentasOrigen() {
    var id_empresa = $('[name=id_empresa]').val();
    $("select[name='id_cuenta_origen']").LoadingOverlay("show", {
        imageAutoResize: true,
        progress: true,
        imageColor: "#3c8dbc"
    });
    // console.log(id_empresa);
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
        title: "¿Está seguro que desea autorizar el pago?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6", //"#00a65a",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Autorizar"
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

function enviarPagoEnCuotas(id,idPagoCuotaDetalle,tipo,event) {

    const obj= event.currentTarget;

    Swal.fire({
        title: "¿Está seguro que desea autorizar el pago?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6", //"#00a65a",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Autorizar"
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'enviarAPago',
                data: {
                    'tipo': tipo,
                    'id': id,
                    'idPagoCuotaDetalle': idPagoCuotaDetalle
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
                    formatPagosEnCuotas(iTableCounter, id, tableOrdenes.row($(this).closest('tr')), "orden");


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


function listarPagoEnCuotas(tipo,id){
    $.ajax({
        type: 'GET',
        url: 'listarPagosEnCuotas/' + tipo + '/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            var htmlOptionVincularConPago = '';
            var i = 1;

            let orden = response.orden;
            let numeroCuotas = response.numero_de_cuotas;
            let detalle = response.detalle;
            let sumaMontoTotalMontoCuota=0;
            if (response.hasOwnProperty('detalle') && detalle.length > 0) {
                detalle.forEach(element => {

                    if(element.id_estado !=7){
                        sumaMontoTotalMontoCuota+=parseFloat(element.monto_cuota);
                    }
                    enlaceAdjunto=[];
                    (element.adjuntos).forEach(element => {
                        enlaceAdjunto.push('<a href="/files/logistica/comporbantes_proveedor/'+element.archivo+'" target="_blank">'+element.archivo+'</a>');
                    });
                    
                    html += '<tr id="' + element.id_pago_cuota_detalle + '">' +
                        '<td style="border: none; text-align: center">' + i + '</td>' +
                        '<td style="border: none; text-align: center; color: #8b3447 !important;font-weight: bold;">' + (element.monto_cuota !== null ? element.monto_cuota : '') + '</td>' +
                        '<td style="border: none; text-align: center">' + ((element.observacion)?element.observacion:'') + '</td>' +
                        '<td style="border: none; text-align: center">' + (numeroCuotas>1?(i+'/'+numeroCuotas):i)+ '</td>' +
                        '<td style="border: none; text-align: center">' + enlaceAdjunto.toString().replace(",","<br>") + '</td>' +
                        '<td style="border: none; text-align: center">' + (element.fecha_autorizacion !=null?element.fecha_autorizacion:'') + '</td>' +
                        '<td style="border: none; text-align: center">' +element.estado.descripcion+'</td>' +
                        '</tr>';
                    i++;
                });

                // option vincular cuota con pago
                detalle.forEach((element,index) => {
                    if(element.id_estado ==5){
                        htmlOptionVincularConPago+=`<option value="${element.id_pago_cuota_detalle}" selected>cuota #${index+1}</option>`
                    }
                });

 
            }
            else {
                var html = `
                <tr><td>No hay registros para mostrar</td></tr>`;
            }
            document.querySelector("table[id='tablaDatosPagoEnCuotas'] tbody").insertAdjacentHTML('beforeend', html );
            document.querySelector("select[name='vincularCuotaARegistroDePago[]']").insertAdjacentHTML('beforeend', htmlOptionVincularConPago );
            document.querySelector("table[id='tablaDatosPagoEnCuotas'] span[id='sumaMontoTotalPagado']").textContent= sumaMontoTotalMontoCuota;

    
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}