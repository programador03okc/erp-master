$(document).ready(function(){
    $('[name=id_doc_identidad]').val('2');
    $("#form-proveedor").on("submit", function(e){
        e.preventDefault();
        guardar_proveedor();
    });
});

function evaluarDocumentoSeleccionado(event){
    let valor = event.target.value;
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
    var page = $('.page-main').attr('type');

    if (page == "ordenesDespacho"){
        $('[name=transportista]').val('si');
    } else {
        $('[name=transportista]').val('no');
    }
    $('[name=id_proveedor]').val('');
    $('[name=nro_documento_prov]').val('');
    $('[name=id_doc_identidad]').val('2');
    $('[name=direccion_fiscal]').val('');
    $('[name=razon_social]').val('');

    $("#submitProveedor").removeAttr("disabled");
}
function ubigeoModalProveedor(){
    $('#modal-ubigeo').modal({
        show: true
    });
    modalPage='modal-proveedor';
    listarUbigeos();
}
function guardar_proveedor(){
    let ruc = $('[name=nro_documento_prov]').val();
    let tp = $('[name=id_doc_identidad]').val();
    
    if (ruc.length !== 11 && tp == 2){
        alert('Debe ingresar un RUC con 11 digitos!');
    } else {
        $("#submitProveedor").attr('disabled','true');
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
                // console.log(response);
                
                if (response['id_proveedor'] > 0){
                    alert('Proveedor registrado con éxito');
                    $('#modal-proveedor').modal('hide');
        
                    var page = $('.page-main').attr('type');
                    
                    if (page == "ordenesDespacho"){

                        if (origen_tr == 'grupoDespacho'){
                            $('[name=gd_id_proveedor]').val(response['id_proveedor']);
                            $('[name=gd_razon_social]').val(response['razon_social']);
                        } 
                        else if (origen_tr == 'transportista'){
                            $('[name=tr_id_proveedor]').val(response['id_proveedor']);
                            $('[name=tr_razon_social]').val(response['razon_social']);
                        }
                    }
                    if( page == "orden-requerimiento"){
                        $('[name=id_proveedor]').val(response['id_proveedor']);
                        $('[name=razon_social]').val(response['razon_social']);
                    }
                } else {
                    alert('Ya se encuentra registrado un Proveedor con dicho Nro de Documento!');
                    $("#submitProveedor").removeAttr("disabled");
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });    
    }
}