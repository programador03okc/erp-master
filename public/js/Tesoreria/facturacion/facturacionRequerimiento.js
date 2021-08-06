var iTableCounter = 1;
var oInnerTable;
var tableRequerimientos;

$("#listaRequerimientos tbody").on("click", "td button.detalle", function() {
    var tr = $(this).closest("tr");
    var row = tableRequerimientos.row(tr);
    var id = $(this).data("id");

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass("shown");
    } else {
        detalleFacturasRequerimiento(iTableCounter, id, row);
        tr.addClass("shown");
        oInnerTable = $("#listaRequerimientos_" + iTableCounter).dataTable({
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

function detalleFacturasRequerimiento(table_id, id, row) {
    $.ajax({
        type: "GET",
        url: "detalleFacturasRequerimientos/" + id,
        dataType: "JSON",
        success: function(response) {
            console.log(response);
            var html = "";
            var i = 1;

            if (response.length > 0) {
                response.forEach(element => {
                    html +=
                        '<tr id="' +
                        element.id_doc_ven +
                        '">' +
                        '<td style="border: none;">' +
                        i +
                        "</td>" +
                        '<td style="border: none; text-align: center">' +
                        (element.serie_numero !== null
                            ? element.serie_numero
                            : "") +
                        "</td>" +
                        '<td style="border: none; text-align: center">' +
                        element.sede_descripcion +
                        "</td>" +
                        '<td style="border: none; text-align: center">' +
                        formatDate(element.fecha_emision) +
                        "</td>" +
                        '<td style="border: none; text-align: center">' +
                        element.razon_social +
                        "</td>" +
                        '<td style="border: none; text-align: center">' +
                        formatNumber.decimal(
                            element.total_a_pagar,
                            element.simbolo,
                            -2
                        ) +
                        "</td>" +
                        '<td style="border: none; text-align: center">' +
                        element.nombre_corto +
                        "</td>" +
                        '<td style="border: none; text-align: center">' +
                        `<button type="button" class="ver_doc btn btn-info btn-xs" data-toggle="tooltip" 
                            data-placement="bottom" title="Ver Factura"
                            onClick="documentosVer(${element.id_doc_ven}, 'requerimiento')">
                            <i class="fas fa-file-alt"></i></button>` +
                        "</td>" +
                        "</tr>";
                    i++;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <td style="border: none; text-align: center">#</td>
                        <td style="border: none; text-align: center">Documento</td>
                        <td style="border: none; text-align: center">Sede</td>
                        <td style="border: none; text-align: center">Fecha Emisión</td>
                        <td style="border: none; text-align: center">Cliente</td>
                        <td style="border: none; text-align: center">Total a pagar</td>
                        <td style="border: none; text-align: center">Registrado por</td>
                        <td style="border: none; text-align: center">Fecha Registro</td>
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
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
