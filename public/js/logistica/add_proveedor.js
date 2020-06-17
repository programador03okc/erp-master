$(function(){
    $("#form-proveedor").on("submit", function(e){
        e.preventDefault();
        guardar_proveedor();
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

function agregar_proveedor(){
    $('#modal-proveedor').modal({
        show: true
    });
    $('[name=id_proveedor]').val('');
    $('[name=nro_documento]').val('');
    $('[name=id_doc_identidad]').val('');
    $('[name=direccion_fiscal]').val('');
    $('[name=razon_social]').val('');
}

function guardar_proveedor(){
    
    var formData = new FormData($('#form-proveedor')[0]);
    console.log(formData);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_proveedor',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            alert('Proveedor registrado con éxito');
            $('#modal-proveedor').modal('hide');
            $('[name=id_proveedor]').html('');
            var html = '<option value="0" disabled>Elija una opción</option>'+response;
            $('[name=id_proveedor]').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });    
}