$(document).ready(function () {
    presupuestoInternoDetalle();
});
function presupuestoInternoDetalle() {
    $.ajax({
        type: 'GET',
        url: 'presupuesto-interno-detalle',
        data: data,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
            console.log(data);
        }
    }).done(function(response) {
        return response
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
