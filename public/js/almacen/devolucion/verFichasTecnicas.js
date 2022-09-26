
function verFichasTecnicasAdjuntas(id_devolucion) {

    if (id_devolucion !== "") {
        $('#modal-verFichasTecnicasAdjuntas').modal({
            show: true
        });
        $('#adjuntosFichasTecnicas tbody').html('');

        $.ajax({
            type: 'GET',
            url: 'verFichasTecnicasAdjuntas/' + id_devolucion,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                if (response.length > 0) {
                    var html = '';
                    response.forEach(function (element) {
                        html += `<tr>
                            <td><a target="_blank" href="/files/cas/devoluciones/fichas/${element.adjunto}">${element.adjunto}</a></td>
                        </tr>`;
                    });
                    $('#adjuntosFichasTecnicas tbody').html(html);
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
