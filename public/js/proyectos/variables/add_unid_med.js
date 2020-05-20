$(function(){
    $("#form-unid_med").on("submit", function(e){
        e.preventDefault();
        guardar_unid_med();
    });
});
/////Agregar Unidad
function agregar_unidad(tp){
    $('#modal-unid_med').modal({
        show: true
    });
    $('[name=tipo]').val(tp);
    $('[name=descripcion_unidad]').val('');
    $('[name=abreviatura_unidad]').val('');
}

function guardar_unid_med(){
    var formData = new FormData($('#form-unid_med')[0]);
    console.log('data:'+formData);
    $.ajax({
        type: 'POST',
        url: 'add_unid_med',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var tp = $('[name=tipo]').val();
            console.log('tp:'+tp);
            $('[name=unid_medida_'+tp+']').html('');
            var html = '<option value="0">Elija una opción</option>'+response;
            $('[name=unid_medida_'+tp+']').html(html);
            $('#modal-unid_med').modal('hide');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}