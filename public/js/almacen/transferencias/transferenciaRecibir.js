function open_transferencia_detalle(data) {
    if (data !== null) {
        $("#modal-transferencia_detalle").modal({
            show: true
        });
        $("#guia").text(data.guia_ven);
        $("[name=id_guia_ven]").val(data.id_guia_ven);
        $("[name=fecha_almacen]").val(fecha_actual());
        $("[name=id_almacen_destino]").val(data.id_almacen_destino);
        $("[name=almacen_destino]").val(data.alm_destino_descripcion);
        $("[name=responsable_destino]").val(usuario_session);
        $("[name=estado]").val(data.estado);
        $("#submit_transferencia").removeAttr("disabled");

        if (data.estado == 14 || data.estado == 7) {
            $("#submit_transferencia").text("Cerrar");
        } else {
            $("#submit_transferencia").text("Recibir");
        }
        listarGuiaTransferenciaDetalle(data.id_guia_ven);
    }
}

let listaDetalleTransferencia = [];
let motivos = [];

function listarGuiaTransferenciaDetalle(id_guia_ven) {
    console.log(id_guia_ven);
    listaDetalleTransferencia = [];
    motivos = [];
    $.ajax({
        type: "GET",
        url: "listarGuiaTransferenciaDetalle/" + id_guia_ven,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            motivos = response['motivos'];
            listaDetalleTransferencia = response['detalleTransferencia'];
            mostrarListaDetalleTransferencia();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrarListaDetalleTransferencia() {
    var html = "";
    var html_series = "";
    let html_motivos = "";
    var i = 1;

    listaDetalleTransferencia.forEach(element => {
        html_series = "";
        element.series.forEach(ser => {
            if (html_series == "") {
                html_series += "<br>" + ser.serie;
            } else {
                html_series += ", " + ser.serie;
            }
        });
        element.cantidad_recibida = (element.cantidad_recibida == undefined ?
            element.cantidad : element.cantidad_recibida);
        // element.id_motivo = (element.id_motivo == undefined ? 0 : element.id_motivo);
        element.observacion = (element.observacion == undefined ? '' : element.observacion);

        // var opt_motivos = `<option value="0" ${element.id_motivo == 0 ? 'selected' : ''}>Ninguno</option>`;
        // motivos.forEach(mot => {
        //     if (element.id_motivo == mot.id_motivo) {
        //         opt_motivos += `<option value="${mot.id_motivo}" selected>${mot.descripcion}</option>`;
        //     } else {
        //         opt_motivos += `<option value="${mot.id_motivo}">${mot.descripcion}</option>`;
        //     }
        // });
        // html_motivos = `<select class="form-control motivo_perdida" data-id="${element.id_guia_ven_det}" >${opt_motivos}</select>`;
        // <td>${html_motivos}</td>

        html += `<tr id="${element.id_guia_ven_det}">
        <td>${element.codigo_trans}</td>
        <td>${element.codigo}</td>
        <td>${element.part_number !== null ? element.part_number : ""}</td>
        <td>${element.descripcion}<strong>${html_series}</strong></td>
        <td>${element.cantidad}</td>
        <td><input type="number" class="input-data right recibida" style="width:60px;" value="${element.cantidad_recibida}" 
            max="${element.cantidad}" data-id="${element.id_guia_ven_det}" data-idtra="${element.id_trans_detalle}" data-cantidad="${element.cantidad}"/></td>
        <td>${element.abreviatura}</td>
        <td><input type="text" class="input-data obs" value="${element.observacion}"/></td>
        </tr>`;
        i++;
    });
    $("#listaTransferenciaDetalleRecibir tbody").html(html);
}

$('#listaTransferenciaDetalleRecibir tbody').on("change", ".recibida", function () {
    let id_guia_ven_det = $(this).data('id');
    let cantidad_recibida = parseFloat($(this).val());
    listaDetalleTransferencia.forEach(element => {
        if (element.id_guia_ven_det == id_guia_ven_det) {
            element.cantidad_recibida = cantidad_recibida;
        }
    });
    mostrarListaDetalleTransferencia();
});

$('#listaTransferenciaDetalleRecibir tbody').on("change", ".motivo_perdida", function () {
    let id_guia_ven_det = $(this).data('id');
    let id_motivo = $(this).val();
    listaDetalleTransferencia.forEach(element => {
        if (element.id_guia_ven_det == id_guia_ven_det) {
            element.id_motivo = id_motivo;
        }
    });
    mostrarListaDetalleTransferencia();
});

$('#listaTransferenciaDetalleRecibir tbody').on("change", ".obs", function () {
    let id_guia_ven_det = $(this).data('id');
    let observacion = $(this).val();
    listaDetalleTransferencia.forEach(element => {
        if (element.id_guia_ven_det == id_guia_ven_det) {
            element.observacion = observacion;
        }
    });
    mostrarListaDetalleTransferencia();
});

function recibir() {

    var detalle = [];
    let valida_cant = 0;

    listaDetalleTransferencia.forEach(element => {

        // if (parseFloat(element.cantidad) !== parseFloat(element.cantidad_recibida)
        //     && element.id_motivo == "0") {
        //     valida_cant++;
        // } else {
        var nuevo = {
            id_guia_ven_det: element.id_guia_ven_det,
            id_trans_detalle: element.id_trans_detalle,
            id_detalle_requerimiento: element.id_detalle_requerimiento,
            cantidad_recibida: element.cantidad_recibida,
            observacion: element.observacion,
            // id_motivo_perdida: element.id_motivo
        };
        detalle.push(nuevo);
        // }
    });

    if (valida_cant > 0) {
        Swal.fire({
            title: "Es necesario que seleccione un motivo de pérdida en " + valida_cant + " item(s).",
            text: "La cantidad enviada y recibida no coincide, se requiere sustentación.",
            icon: "error"
        });

    } else {

        Swal.fire({
            title: "¿Está seguro que desea guardar la guía de ingreso?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00a65a", //"#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Si, Guardar"
        }).then(result => {
            if (result.isConfirmed) {

                $("#submit_transferencia").attr("disabled", "true");

                var id_transferencia = $("[name=id_transferencia]").val();
                var id_guia_ven = $("[name=id_guia_ven]").val();
                var id_req = $("[name=id_requerimiento]").val();
                var fecha_almacen = $("[name=fecha_almacen]").val();
                var id_almacen_destino = $("[name=id_almacen_destino]").val();
                var responsable_destino = $("[name=responsable_destino]").val();
                var guia_ingreso_compra = $("[name=guia_ingreso_compra]").val();
                var comentario_recibir = $("[name=comentario_recibir]").val();

                var data = "id_transferencia=" + id_transferencia +
                    "&id_guia_ven=" + id_guia_ven +
                    "&id_requerimiento=" + id_req +
                    "&fecha_almacen=" + fecha_almacen +
                    "&responsable_destino=" + responsable_destino +
                    "&id_almacen_destino=" + id_almacen_destino +
                    "&guia_ingreso_compra=" + guia_ingreso_compra +
                    "&comentario_recibir=" + comentario_recibir +
                    "&detalle=" + JSON.stringify(detalle);
                console.log(data);

                $.ajax({
                    type: "POST",
                    url: "guardarIngresoTransferencia",
                    data: data,
                    dataType: "JSON",
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
                        $("#modal-transferencia_detalle").modal("hide");
                        // listarTransferenciasPorRecibir();
                        $("#listaTransferenciasPorRecibir").DataTable().ajax.reload(null, false);
                        $("#nro_por_recibir").text(response.nroPorRecibir);
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        });
    }

}
