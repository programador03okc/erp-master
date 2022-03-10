
var iTableCounter = 1;
var oInnerTable;
var tableIncidencias;

$('#listaIncidencias tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableIncidencias.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatReportes(iTableCounter, id, row, "orden");
        tr.addClass('shown');
        oInnerTable = $('#listaIncidencias_' + iTableCounter).dataTable({
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


function formatReportes(table_id, id, row) {

    $.ajax({
        type: 'GET',
        url: 'listarFichasReporte/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            var i = 1;

            if (response.length > 0) {
                response.forEach(element => {
                    html += '<tr id="' + element.id_incidencia_reporte + '">' +
                        '<td style="border: none;">' + i + '</td>' +
                        '<td style="border: none; text-align: center">' + element.codigo + '</td>' +
                        '<td style="border: none; text-align: center">' + (element.fecha_reporte !== null ? formatDate(element.fecha_reporte) : '') + '</td>' +
                        '<td style="border: none; text-align: center">' + element.usuario.nombre_corto + '</td>' +
                        '<td style="border: none; text-align: center">' + element.acciones_realizadas + '</td>' +
                        '<td style="border: none; text-align: center">' + formatDateHour(element.fecha_registro) + '</td>' +
                        '<td style="border: none; text-align: center">' +
                        `<button type = "button" class= "btn btn-danger boton" data - toggle="tooltip" 
                            data - placement="bottom" data - row="${row}"
                            onClick = "anularFichaReporte(${element.id_incidencia_reporte})" title = "Anular Ficha Reporte">
                            <i class="fas fa-trash"></i></button` +
                        '</td>' +
                        '</tr>';
                    i++;
                });
                var tabla = `<table class= "table table-sm" style = "border: none;" 
                id = "detalle_${table_id}" >
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Código</th>
                        <th style="border: none;">Fecha Reporte</th>
                        <th style="border: none;">Responsable</th>
                        <th style="border: none;">Acciones realizadas</th>
                        <th style="border: none;">Fecha registro</th>
                        <th style="border: none;">Anular</th>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table> `;
            }
            else {
                var tabla = `<table class= "table table-sm" style = "border: none;" 
                id = "detalle_${table_id}" >
            <tbody>
                <tr><td>No hay registros para mostrar</td></tr>
            </tbody>
                </table> `;
            }
            row.child(tabla).show();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
