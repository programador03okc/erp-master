function ceros_numero_cambio(numero){
    if (numero == 'numero'){
        var num = $('[name=numero_nuevo]').val();
        $('[name=numero_nuevo]').val(leftZero(7,num));
    }
    else if (numero == 'serie'){
        var num = $('[name=serie_nuevo]').val();
        $('[name=serie_nuevo]').val(leftZero(4,num));
    }
}

$("#form-guia_ven_cambio").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    cambio_serie_numero(data);
});

function cambio_serie_numero(data){
    $("#submit_guia_ven_cambio").attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'cambio_serie_numero',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.length > 0){
                alert(response);
                $('#modal-guia_ven_cambio').modal('hide');
            } else {
                alert('Serie-Número cambiado con éxito');
                $('#modal-guia_ven_cambio').modal('hide');
                $('#despachosEntregados').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
