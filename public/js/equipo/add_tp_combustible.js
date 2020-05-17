$(function(){
    $("#form-tp_combustible").on("submit", function(e){
        e.preventDefault();
        var formData = new FormData($('#form-tp_combustible')[0]);
        console.log(formData);
        $.ajax({
            type: 'POST',
            url: '/guardar_tp_combustible',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                console.log(response['msj']);
                console.log(response['html']);
                if (response['msj'].length > 0){
                    alert(response['msj']);
                } else {
                    alert('Tipo de combustible registrado con éxito');
                    $('#modal-tp_combustible').modal('hide');
                    $('[name=tp_combustible]').html('');
                    var html = '<option value="0" disabled>Elija una opción</option>'+response['html'];
                    $('[name=tp_combustible]').html(html);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    });
    return false;
});

function agregar_tp_combustible(){
    $('#modal-tp_combustible').modal({
        show: true
    });
}
