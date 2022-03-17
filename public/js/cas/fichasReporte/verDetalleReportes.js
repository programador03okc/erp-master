
var iTableCounter = 1;
var oInnerTable;
var tableIncidenciasx;

$('#listaIncidencias tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableIncidenciasx.row(tr);
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
                    html += `<tr id="${element.id_incidencia_reporte}">
                        <td style="border: none;"> ${i}</td>
                        <td style="border: none; text-align: center">
                            <a href="#" onClick="imprimirFichaReporte(${element.id_incidencia_reporte})">${element.codigo}</a></td>
                        <td style="border: none; text-align: center">${(element.fecha_reporte !== null ? formatDate(element.fecha_reporte) : '')}</td>
                        <td style="border: none; text-align: center">${element.usuario.nombre_corto}</td>
                        <td style="border: none; text-align: center">${element.acciones_realizadas}</td>
                        <td style="border: none; text-align: center">${(element.adjuntos.length > 0 ? '<a href="#" onClick="verAdjuntosFicha(' + element.id_incidencia_reporte + ');">' + element.adjuntos.length + ' archivos adjuntos </a>' : '')}</td>
                        <td style="border: none; text-align: center">${formatDateHour(element.fecha_registro)}</td>
                        
                        </tr>`;
                    // <td style="border: none; text-align: center">
                    //     <button type="button" class= "btn btn-danger boton" data-toggle="tooltip" 
                    //         data-placement="bottom" onClick="anularFichaReporte(${element.id_incidencia_reporte})" 
                    //         title="Anular Ficha Reporte">
                    //         <i class="fas fa-trash"></i>
                    //     </button>
                    // </td>
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
                        <th style="border: none;">Adjunto(s)</th>
                        <th style="border: none;">Fecha registro</th>
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

function verAdjuntosFicha(id_incidencia_reporte) {

    if (id_incidencia_reporte !== "") {
        $('#modal-verAdjuntosFicha').modal({
            show: true
        });
        $('#adjuntosFicha tbody').html('');

        $.ajax({
            type: 'GET',
            url: 'verAdjuntosFicha/' + id_incidencia_reporte,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                if (response.length > 0) {
                    var html = '';
                    response.forEach(function (element) {
                        html += `<tr>
                            <td><a target="_blank" href="/files/cas/incidencias/fichas/${element.adjunto}">${element.adjunto}</a></td>
                        </tr>`;
                    });
                    $('#adjuntosFicha tbody').html(html);
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function imprimirFichaReporte(id_ficha) {
    if (id_ficha !== null && id_ficha !== '') {
        window.open("imprimirFichaReporte/" + id_ficha);
    }
}