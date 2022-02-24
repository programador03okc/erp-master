var iTableCounter = 1;
var oInnerTable;

$("#ordenesPendientes tbody").on("click", "td button.ver-detalle", function () {
    var tr = $(this).closest("tr");
    var row = table.row(tr);
    var id = $(this).data("id");

    if (row.child.isShown()) {
        //  This row is already open - close it
        row.child.hide();
        tr.removeClass("shown");
    } else {
        // Open this row
        //    row.child( format(iTableCounter, id) ).show();
        format(iTableCounter, id, row);
        tr.addClass("shown");
        // try datatable stuff
        oInnerTable = $("#ordenesPendientes_" + iTableCounter).dataTable({
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
            columns: [
                //   { data:'refCount' },
                //   { data:'section.codeRange.sNumber.sectionNumber' },
                //   { data:'section.title' }
            ]
        });
        iTableCounter = iTableCounter + 1;
    }
});

function format(table_id, id, row) {
    $.ajax({
        type: "GET",
        url: "detalleOrden/" + id,
        dataType: "JSON",
        success: function (response) {
            var html = "";

            if (response.length > 0) {
                response.forEach(function (element) {
                    html += `<tr>
                    <td style="border: none;">${element.nro_orden !== null
                            ? ` <a href="#" class="archivos" data-id="${element.id_oc_propia}" data-tipo="${element.tipo}">
                            ${element.nro_orden}</a>`
                            : ""
                        } 
                    </td>
                    <td style="border: none;">${element.codigo_oportunidad !== null
                            ? element.codigo_oportunidad
                            : ""
                        }</td>
                    <td style="border: none;">${element.razon_social !== null
                            ? element.razon_social
                            : ""
                        }</td>
                    <td style="border: none;">${element.nombre_corto !== null
                            ? element.nombre_corto
                            : ""
                        }</td>
                    <td style="border: none;"><label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento(${element.id_requerimiento
                        })">${element.codigo_req ?? ''}</label>${(element.tiene_transformacion ? ' <i class="fas fa-random red"></i> ' : '')}<br> ${element.sede_req ?? ''}</td>
                    <td style="border: none;">${element.codigo}</td>
                    <td style="border: none;">${element.part_number !== null ? element.part_number : ""
                        }</td>
                    <td style="border: none;">${element.descripcion}</td>
                    <td style="border: none;">${element.cantidad}</td>
                    <td style="border: none;">${element.abreviatura}</td>
                    <td style="border: none;">${element.cantidad_ingresada !== null
                            ? element.cantidad_ingresada
                            : "0"
                        }</td>
                    <td style="border: none;">${formatNumber.decimal(
                            element.precio,
                            "",
                            -3
                        )}</td>
                    <td style="border: none;">${formatNumber.decimal(
                            element.precio * element.cantidad,
                            "",
                            -3
                        )}</td>
                    </tr>`;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">O/C</th>
                        <th style="border: none;">C.P.</th>
                        <th style="border: none;">Cliente</th>
                        <th style="border: none;">Responsable</th>
                        <th style="border: none;">Cod.Req.</th>
                        <th style="border: none;">Código</th>
                        <th style="border: none;">PartNumber</th>
                        <th style="border: none;">Descripción</th>
                        <th style="border: none;">Cant.</th>
                        <th style="border: none;">Und.Med</th>
                        <th style="border: none;">Cant. Ingresada</th>
                        <th style="border: none;">Unitario</th>
                        <th style="border: none;">Total</th>
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

$("#ordenesPendientes tbody").on("click", "a.archivos", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    var tipo = $(this).data("tipo");
    obtenerArchivosMgcp(id, tipo);
});

function abrir_requerimiento(id_requerimiento) {
    // Abrir nuevo tab
    localStorage.setItem("idRequerimiento", id_requerimiento);
    let url = "/necesidades/requerimiento/elaboracion/index";
    var win = window.open(url, "_blank");
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}
