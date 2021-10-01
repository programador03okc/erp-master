
$("#form-orden_despacho_transportista").on("submit", function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    $('#submit_od_transportista').attr('disabled', 'true');
    despacho_transportista(data);
    $('#modal-orden_despacho_transportista').modal('hide');
});

function despacho_transportista(data) {
    $.ajax({
        type: 'POST',
        url: 'despacho_transportista',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response > 0) {
                $('#gruposDespachados').DataTable().ajax.reload();
                actualizaCantidadDespachosTabs();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function ceros_numero(numero) {
    if (numero == "numero") {
        var num = $("[name=numero]").val();
        $("[name=numero]").val(leftZero(7, num));
    } else if (numero == "serie") {
        var num = $("[name=serie]").val();
        $("[name=serie]").val(leftZero(4, num));
    }
}