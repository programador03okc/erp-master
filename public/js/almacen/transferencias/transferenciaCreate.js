function generar_transferencia(id_guia){
    $.ajax({
        type: 'GET',
        url: 'transferencia/'+id_guia,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            alert(response);
            listarTransferenciasPorEnviar();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}