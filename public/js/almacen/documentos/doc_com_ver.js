function documentosVer(id) {
    $("#modal-doc_ver").modal({
        show: true
    });
    $.ajax({
        type: "GET",
        url: "documentos_ver/" + id,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            let html = "";
            $("[name=id_doc_com]").val();
            response["docs"].forEach(element => {
                html += `
                <tr>
                    <td colSpan="14">
                        <button type="button" class="btn btn-danger btn-xs " data-toggle="tooltip" 
                        data-placement="bottom" title="Anular Documento" onClick="anularDocCompra(${element.id_doc_com
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
                    <th >Tipo de Cambio: S/ ${(element.tipo_cambio !== null ? element.tipo_cambio : '')}</td>
                    <th colSpan="2">Empresa-Sede: </th>
                    <td colSpan="3">${element.sede_descripcion}</td>
                </tr>
                <tr>
                    <th colSpan="2">Proveedor: </th>
                    <td colSpan="3">${(element.nro_documento !== null
                        ? element.nro_documento
                        : "") +
                    " - " +
                    element.razon_social}</td>
                    <th colSpan="2">Importe: </th>
                    <td colSpan="2">${formatNumber.decimal(
                        element.total_a_pagar,
                        element.simbolo,
                        -2
                    )}</td>
                    <th colSpan="2">Condición: </th>
                    <td colSpan="3">${element.condicion_descripcion +
                    (element.credito_dias !== null
                        ? element.credito_dias + " días"
                        : "")}</td>
                </tr>
                <tr><td colSpan="12"></td></tr>
                <tr style="background-color: Gainsboro;">
                    <th>#</th>
                    <th>Guía</th>
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
                    detalle => detalle.id_doc == element.id_doc_com
                );

                detalles.forEach(item => {
                    html += `<tr>
                        <td>${i}</td>
                        <td>${item.serie !== null
                            ? item.serie + "-" + item.numero
                            : ""
                        }</td>
                        <td>${item.codigo !== null ? item.codigo : ""}</td>
                        <td>${item.part_number !== null ? item.part_number : ""
                        }</td>
                        <td>${item.descripcion !== null
                            ? item.descripcion
                            : item.servicio_descripcion
                        }</td>
                        <td class="right">${item.cantidad}</td>
                        <td>${item.abreviatura}</td>
                        <td class="right">${item.precio_unitario}</td>
                        <td class="right">${item.sub_total}</td>
                        <td class="right">${item.porcen_dscto}</td>
                        <td class="right">${item.total_dscto}</td>
                        <td class="right">${formatNumber.decimal(
                            item.precio_total,
                            "",
                            -2
                        )}</td>
                    </tr>`;
                    i++;
                });
                html += `<tr>
                    <td colSpan="11" class="text-right">SubTotal</td>
                    <th class="right">${formatNumber.decimal(
                    element.sub_total,
                    element.simbolo,
                    -2
                )}</th>
                </tr>
                <tr>
                    <td colSpan="11" class="text-right">IGV</td>
                    <th class="right">${formatNumber.decimal(
                    element.total_igv,
                    element.simbolo,
                    -2
                )}</th>
                </tr>
                <tr>
                    <td colSpan="11" class="text-right">Total</td>
                    <th class="right">${formatNumber.decimal(
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

function anularDocCompra(id) {
    let rspta = confirm(
        "¿Está seguro que desea anular éste documento de compra?"
    );

    if (rspta) {
        $.ajax({
            type: "GET",
            url: "anular_doc_com/" + id,
            dataType: "JSON",
            success: function (response) {
                console.log(response);
                alert("Se annulo correctamente el documento.");
                listarIngresos();
                $("#modal-doc_ver").modal("hide");
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
