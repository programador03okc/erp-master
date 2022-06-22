let origenVer = "";

function verDocumentoVenta(id, origen) {
    origenVer = origen;
    console.log("id" + id);
    $("#modal-doc_ven_ver").modal({
        show: true
    });
    $.ajax({
        type: "GET",
        url: "documentos_ver/" + id,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            let html = "";

            response["docs"].forEach(element => {
                html += `
                <tr>
                    <td colSpan="14">
                        <button type="button" class="btn btn-danger btn-xs " data-toggle="tooltip" 
                        data-placement="bottom" title="Anular Documento" onClick="anularDocVenta(${element.id_doc_ven
                    });">
                        <i class="fas fa-trash"></i> Anular Documento</button>
                    </td>
                </tr>
                <tr>
                    <th colSpan="2">Documento: </th>
                    <td colSpan="2">${element.tp_doc +
                    " " +
                    element.serie +
                    "-" +
                    element.numero}</td>
                    <th></td>
                    <th colSpan="2">Empresa: </th>
                    <td colSpan="2">${element.empresa_razon_social}</td>
                    <th colSpan="2">Fecha Emisión: </th>
                    <td colSpan="3">${formatDate(element.fecha_emision)}</td>
                </tr>
                <tr>
                    <th colSpan="2">Proveedor: </th>
                    <td colSpan="3">${(element.nro_documento !== null
                        ? element.nro_documento + " - "
                        : "") + element.razon_social}</td>
                    <th colSpan="2">Importe: </th>
                    <td colSpan="2">${formatNumber.decimal(
                            element.total_a_pagar,
                            element.simbolo,
                            -2
                        )}</td>
                    <th colSpan="2">Condición: </th>
                    <td colSpan="3">${(element.condicion_descripcion !== null
                        ? element.condicion_descripcion
                        : "") +
                    (element.credito_dias !== null
                        ? " " + element.credito_dias + " días"
                        : "")}</td>
                </tr>
                <tr><td colSpan="12"></td></tr>
                <tr style="background-color: Gainsboro;">
                    <th>#</th>
                    <th>Guía/Req</th>
                    <th>Código</th>
                    <th>PartNumber</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Unid</th>
                    <th>Unitario</th>
                    <th>Sub Total</th>
                    <th>% Dscto</th>
                    <th>Dcsto</th>
                    <th>Total</th>
                </tr>`;

                var i = 1;
                let detalles = response["detalles"].filter(
                    detalle => detalle.id_doc == element.id_doc_ven
                );

                detalles.forEach(item => {
                    html += `<tr>
                        <td>${i}</td>
                        <td>${item.serie !== null
                            ? item.serie + "-" + item.numero
                            : item.codigo_req !== null
                                ? item.codigo_req
                                : ""
                        }</td>
                        <td>${item.codigo !== null ? item.codigo : ""}</td>
                        <td>${item.part_number !== null ? item.part_number : ""
                        }</td>
                        <td>${item.descripcion !== null
                            ? item.descripcion
                            : item.servicio_descripcion
                        }</td>
                        <td class="text-right">${item.cantidad}</td>
                        <td>${item.abreviatura}</td>
                        <td class="text-right">${item.precio_unitario}</td>
                        <td class="text-right">${item.sub_total}</td>
                        <td class="text-right">${item.porcen_dscto}</td>
                        <td class="text-right">${item.total_dscto}</td>
                        <td class="text-right">${formatNumber.decimal(
                            item.precio_total,
                            "",
                            -2
                        )}</td>
                    </tr>`;
                    i++;
                });
                html += `<tr>
                    <td colSpan="11" class="text-right">SubTotal</td>
                    <th class="text-right">${formatNumber.decimal(
                    element.sub_total,
                    element.simbolo,
                    -2
                )}</th>
                </tr>
                <tr>
                    <td colSpan="11" class="text-right">IGV</td>
                    <th class="text-right">${formatNumber.decimal(
                    element.total_igv,
                    element.simbolo,
                    -2
                )}</th>
                </tr>
                <tr>
                    <td colSpan="11" class="text-right">Total</td>
                    <th class="text-right">${formatNumber.decimal(
                    element.total_a_pagar,
                    element.simbolo,
                    -2
                )}</th>
                </tr>
                <tr><td colSpan="12"></td></tr>`;
            });
            $("#documentos tbody").html(html);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularDocVenta(id) {
    Swal.fire({
        title: "¿Está seguro que desea anular éste documento de venta?",
        // text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {
        if (result.isConfirmed) {
            $("#modal-doc_ven_anula").modal({
                show: true
            });
            $("[name=id_doc_ven_anula]").val(id);
        }
    });
}

$("#form-AnulaDocVen").on("submit", function (e) {
    console.log("submit");
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);

    $.ajax({
        type: "POST",
        url: "anular_doc_ven",
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
            $("#modal-doc_ven_anula").modal("hide");
            $("#modal-doc_ven_ver").modal("hide");
            let facturacion = new Facturacion();

            if (origenVer == "guia") {
                facturacion.listarGuias();
            } else if (origenVer == "requerimiento") {
                facturacion.listarRequerimientos();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
});