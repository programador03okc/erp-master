$(function(){
    $("#form-contacto").on("submit", function(e){
        e.preventDefault();
        guardar_contacto();
    });
});
function agregar_contacto(){
    $('#modal-contacto').modal({
        show: true
    });
    var id_contribuyente = $('[name=id_contrib]').val();
    $('[name=id_contribuyente]').val(id_contribuyente);
}
function guardar_contacto(){



    var formData = new FormData($('#form-contacto')[0]);
    // console.log(formData);
    $.ajax({
        type: 'POST',
        url: '/guardar_contacto',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
                Lobibox.notify('success', {
                    title:false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Contacto registrado con éxito'
                });
                $('[name=id_contacto]').html('');
                var html = '<option value="0" disabled>Elija una opción</option>'+response;
                $('[name=id_contacto]').html(html);
                $('#modal-contacto').modal('hide');
                let classModalEditarCotizacion = document.getElementById('modal-editar-cotizacion').getAttribute('class');
                if(classModalEditarCotizacion ==  "modal fade in"){
                    onChangeContactoModalEditarCotizacion();
                    $('#modal-contacto').modal('hide');
                }
                
            
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        Swal.fire(
            '',
            'Hubo un problema al intentar guardar el contacto. '+ errorThrown,
            'error'
        );
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });        
}