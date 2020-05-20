$(function(){
    $("#form-cta_banco").on("submit", function(e){
        e.preventDefault();
        guardar_cta_banco();
    });
});
function agregar_cta_banco(tipo, banco){
    var id_contribuyente = $('[name=id_contrib]').val();
    if (id_contribuyente !== ''){
        $('#modal-cta_banco').modal({
            show: true
        });
        $('[name=id_contribuyente]').val(id_contribuyente);
        $('[name=id_tipo_cuenta]').val(tipo);
        $('[name=id_banco]').val(banco);
    }
}
function guardar_cta_banco(){
    var formData = new FormData($('#form-cta_banco')[0]);
    // console.log(formData);
    $.ajax({
        type: 'POST',
        url: '/guardar_cuenta_banco',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            alert('Cuenta Banco registrada con éxito');
            $('#modal-cta_banco').modal('hide');
            if (response['tipo'] == "2"){
                $('[name=id_cta_detraccion]').html(response['html']);
            } else {
                $('[name=id_cta_principal]').html(response['html']);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}