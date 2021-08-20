var iTableCounter = 1;
var oInnerTable;
var tableGuias;

$("#listaGuias tbody").on("click", "td button.detalle", function () {
    var tr = $(this).closest("tr");
    var row = tableGuias.row(tr);
    var id = $(this).data("id");

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass("shown");
    } else {
        detalleFacturasGuia(iTableCounter, id, row);
        tr.addClass("shown");
        oInnerTable = $("#listaGuias_" + iTableCounter).dataTable({
            //    data: sections,
            autoWidth: true,
            deferRender: true,
            info: false,
            lengthChange: false,
            ordering: false,
            paging: false,
            scrollX: false,
            scrollY: false,
            searching: false,
            columns: []
        });
        iTableCounter = iTableCounter + 1;
    }
});

function detalleFacturasGuia(table_id, id, row) {
    $.ajax({
        type: "GET",
        url: "detalleFacturasGuias/" + id,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            var html = "";
            var i = 1;

            if (response.length > 0) {
                response.forEach(element => {
                    html += `<tr id="${element.id_doc_ven}">
                        <td style="border: none;">${i}</td>
                        <td style="border: none; text-align: center">
                            ${element.serie_numero !== null
                            ? element.serie_numero
                            : ""
                        }
                        </td>
                        <td style="border: none; text-align: center">
                            ${element.empresa_razon_social}
                        </td>
                        <td style="border: none; text-align: center">
                            ${formatDate(element.fecha_emision)}
                        </td>
                        <td style="border: none; text-align: center">
                            ${element.razon_social}
                        </td>
                        <td style="border: none; text-align: center">
                            ${formatNumber.decimal(
                            element.total_a_pagar,
                            element.simbolo,
                            -2
                        )}
                        </td>
                        <td style="border: none; text-align: center">
                            ${element.nombre_corto}
                        </td>
                        <td style="border: none; text-align: center">
                            ${element.condicion +
                        (element.credito_dias !== null
                            ? element.credito_dias + " días"
                            : "")}
                        </td>
                        <td style="border: none; text-align: center">
                            <div style="display: flex;">
                                <button type="button" class="ver_doc btn btn-info btn-xs btn-flat" data-toggle="tooltip" 
                                    data-placement="bottom" title="Ver Factura"
                                    onClick="verDocumentoVenta(${element.id_doc_ven}, 'guia')" >
                                    <i class="fas fa-file-alt"></i></button>
                                
                            <div/>
                        </td>
                        </tr>`;
                    // <button type="button" class="autogenerar btn btn-success boton btn-flat" data-toggle="tooltip" 
                    //     data-placement="bottom" title="Autogenerar Docs de Compra" 
                    //     onClick="autogenerarDocsCompra(${element.id_doc_ven})" >
                    //     <i class="fas fa-sync-alt"></i></button>
                    i++;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <td style="border: none; text-align: center">#</td>
                        <td style="border: none; text-align: center">Documento</td>
                        <td style="border: none; text-align: center">Empresa</td>
                        <td style="border: none; text-align: center">Fecha Emisión</td>
                        <td style="border: none; text-align: center">Cliente</td>
                        <td style="border: none; text-align: center">Total a pagar</td>
                        <td style="border: none; text-align: center">Registrado por</td>
                        <td style="border: none; text-align: center">Condición Pago</td>
                        <td style="border: none; text-align: center"></td>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table>`;
            } else {
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
            }
            row.child(tabla).show();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function autogenerarDocsCompra(id_doc_ven) {

    $.ajax({
        type: "GET",
        url: "autogenerarDocumentosCompra/" + id_doc_ven,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            if (response == 'ok') {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: "Se ha autogenerado los documentos de compra correctamente."
                });
            } else {
                Lobibox.notify("error", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: "No se ha podido autogenerar los documentos de compra."
                });
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}