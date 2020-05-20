$(function(){   
    $("#form-cliente").on("submit", function(e){
        e.preventDefault();
        guardar_cliente();
    });
});

function evaluarDocumentoSeleccionado(event){
    let valor =event.target.value;
    if (valor != '2'){ // si tipo de documento no es RUC
        $('#btnConsultaSunat').addClass('disabled');
    }else{
        $('#btnConsultaSunat').removeClass('disabled');
    }
}

function agregar_cliente(){
    $('#modal-cliente').modal({
        show: true
    });
    $('[name=id_cliente]').val('');
    $('[name=nro_documento]').val('');
    $('[name=id_doc_identidad]').val('');
    $('[name=direccion_fiscal]').val('');
    $('[name=razon_social]').val('');
    document.getElementById("btnSubmitCliente").value = "Guardar";
    document.getElementById("btnSubmitCliente").disabled = false;
}

function guardar_cliente(){
    var formData = new FormData($('#form-cliente')[0]);
    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_cliente',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            alert('cliente registrado con éxito');
            $('#modal-cliente').modal('hide');
            console.log(response);
            $('[name=id_cliente]').val(response.id_cliente);
            $('[name=id_contrib]').val(response.id_contribuyente);
            $('[name=cliente_razon_social]').val(response.razon_social);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });        
}

function checkSubmit() {
    document.getElementById("btnSubmitCliente").value = "Enviando...";
    document.getElementById("btnSubmitCliente").disabled = true;
    return true;
}