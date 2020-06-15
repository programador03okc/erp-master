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
    let option ='HTML_OPTION';
    let classModalEditarCotizacion = document.getElementById('modal-editar-cotizacion').getAttribute('class');
    if(classModalEditarCotizacion ==  "modal fade in"){
        option = 'DATA'; 
    }else{
        option = 'HTML_OPTION'; 
    }

    var formData = new FormData($('#form-proveedor')[0]);
    // console.log(formData);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: '/guardar_proveedor/'+option,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(classModalEditarCotizacion ==  "modal fade in"){
                document.querySelector('form[id="form-editar-cotizacion"] input[name="razon_social"]').value = response[0].razon_social;
                document.querySelector('form[id="form-editar-cotizacion"] input[name="id_proveedor"]').value = response[0].id_proveedor;
                $('#modal-proveedor').modal('hide');
                onChangeProveedorSave();

            }else{

                var html = '<option value="0" disabled>Elija una opción</option>'+response;
                alert('Proveedor registrado con éxito');
                $('[name=id_proveedor]').html('');
                $('[name=id_proveedor]').html(html);
                $('#modal-proveedor').modal('hide');
            }            
            alert('Proveedor registrado con éxito');
            $('#modal-proveedor').modal('hide');
            $('[name=id_proveedor]').html('');
            var html = '<option value="0" disabled>Elija una opción</option>'+response;
            $('[name=id_proveedor]').html(html);
            // $('[name=id_proveedor]').val(response.id_proveedor);
            // $('[name=id_contrib]').val(response.id_contribuyente);
            // $('[name=prov_razon_social]').val(response.razon_social);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });        
}