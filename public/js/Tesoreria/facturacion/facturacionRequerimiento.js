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
                    html += `<tr id="${element.id_doc_ven}">
                        <td style="border: none;">${i}</td>
                        <td style="border: none; text-align: center">
                            ${
                                element.serie_numero !== null
                                    ? element.serie_numero
                                    : ""
                            }</td>
                        <td style="border: none; text-align: center">
                            ${element.empresa_razon_social}</td>
                        <td style="border: none; text-align: center">
                            ${formatDate(element.fecha_emision)}</td>
                        <td style="border: none; text-align: center">
                            ${element.razon_social}</td>
                        <td style="border: none; text-align: center">
                            ${formatNumber.decimal(
                                element.total_a_pagar,
                                element.simbolo,
                                -2
                            )}</td>
                        <td style="border: none; text-align: center">
                            ${element.nombre_corto}</td>
                        <td style="border: none; text-align: center">
                            ${element.condicion +
                                (element.credito_dias !== null
                                    ? " " + element.credito_dias + " días"
                                    : "")}</td>
                        <td style="border: none; text-align: center">
                            <div style="display: flex;">
                                <button type="button" class="ver_doc btn btn-info btn-xs btn-flat" data-toggle="tooltip"
                                    data-placement="bottom" title="Ver Factura"
                                    onClick="verDocumentoVenta(${
                                        element.id_doc_ven
                                    }, 'requerimiento')">
                                <i class="fas fa-file-alt"></i></button>
                                <button type="button" class="btn btn-success btn-xs btn-flat adjuntar-documentos" title="Adjuntar" data-id-doc="${element.id_doc_ven}" data-id-requerimiento="${id}">
                                    <i class="fas fa-file-alt"></i>
                                </button>
                            <div/>
                        </td>
                        <td><td/>
                        </tr>`;
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
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
$(document).on('click','.adjuntar-documentos',function () {
    var data_id_doc = $(this).attr('data-id-doc');
    $('#modal-adjuntos-factura').modal('show');
    $('[name="id_doc_ven"]').val($(this).attr('data-id-doc'));
    $('[name="id_requerimiento"]').val($(this).attr('data-id-requerimiento'));
    $('[data-action="table-body"]').html('');
});
var array_adjuntos=[];

$(document).on('change','[data-action="adjuntos"]',function () {
    var file = $(this)[0].files;
    $.each(file, function (index, element) {
        array_adjuntos.push(element);
    });
    adjuntosSeleccionados();
});
function adjuntosSeleccionados() {
    var html='';
    $.each(array_adjuntos, function (indexInArray, valueOfElement) {
        html+='<tr data-key="'+indexInArray+'">'
            html+='<td>'
                html+=valueOfElement.name
            html+='</td>'
            html+='<td><buton class="btn btn-danger" data-action="eliminar-adjunto" data-key="'+indexInArray+'"><i class="fas fa-trash-alt"></i></button></td>'
        html+='</tr>'
    });
    $('[data-action="table-body"]').html(html);
}
$(document).on('click','[data-action="eliminar-adjunto"]',function () {
    var key_item = $(this).attr('data-key');
    array_adjuntos = array_adjuntos.filter((item, key) => key !== parseInt(key_item));
    if (array_adjuntos.length===0) {
        $('[name="adjuntos[]"]').val('');
    }
    adjuntosSeleccionados()
});
$(document).on('submit','[data-form="guardar-adjuntos"]',function (e) {
    e.preventDefault();
    var data_forma_adjuntos = new FormData($(this)[0]);
    $.each(array_adjuntos, function (indexInArray, valueOfElement) {
        data_forma_adjuntos.append('archivos[]', valueOfElement);
    });

    $.ajax({
        type: 'POST',
        url: 'guardar-adjuntos-factura',
        data: data_forma_adjuntos,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
        },
        success: (response) => {
        },
        fail: (jqXHR, textStatus, errorThrown) => {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
});
