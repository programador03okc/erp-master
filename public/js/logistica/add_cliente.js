$(function(){
    $("#form-agregar-cliente").on("submit", function(e){
        e.preventDefault();
        guardar_cliente();
    });
});

function agregar_cliente(){
    $('#modal-add-cliente').modal({
        show: true
    });
    let tipo_cliente = document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']").value;
    if(tipo_cliente == 1){
        habilitarInputPersonaNatural();
    }else if(tipo_cliente ==2){
        habilitarInputPersonaJuridica();
    }

    // $('[name=id_proveedor]').val('');
    // $('[name=nro_documento]').val('');
    // $('[name=id_doc_identidad]').val('');
    // $('[name=direccion_fiscal]').val('');
    // $('[name=razon_social]').val('');
}

function handleChangeTipoCliente(e){
    if (e.target.value == 1){
        habilitarInputPersonaNatural();
        limpiarFormAgregarCliente();
    }else if(e.target.value == 2){
        habilitarInputPersonaJuridica();
        limpiarFormAgregarCliente();
    }
}

function habilitarInputPersonaNatural(){
    document.querySelector("form[id='form-agregar-cliente'] span[id='nombre_tipo_cliente']").textContent = ': Persona Natural';
    document.querySelector("form[id='form-agregar-cliente'] select[name='tipo_cliente']").value=1;
    document.querySelector("form[id='form-agregar-cliente'] select[name='id_doc_identidad']").value=1;
    hiddeElement('mostrar','form-agregar-cliente',['input-group-persona-natural']);
    hiddeElement('ocultar','form-agregar-cliente',['input-group-persona-juridica']);
}

function habilitarInputPersonaJuridica(){
    document.querySelector("form[id='form-agregar-cliente'] select[name='id_doc_identidad']").value=2;
    document.querySelector("form[id='form-agregar-cliente'] select[name='tipo_cliente']").value=2;
    document.querySelector("form[id='form-agregar-cliente'] span[id='nombre_tipo_cliente']").textContent = ': Persona Juridica';
    hiddeElement('mostrar','form-agregar-cliente',['input-group-persona-juridica']);
    hiddeElement('ocultar','form-agregar-cliente',['input-group-persona-natural']);
}

function get_data_form_agregar_cliente(){

    let tipo_cliente = document.querySelector("form[id='form-agregar-cliente'] select[name='tipo_cliente']").value;
    let id_doc_identidad = document.querySelector("form[id='form-agregar-cliente'] select[name='id_doc_identidad']").value;
    let nro_documento = document.querySelector("form[id='form-agregar-cliente'] input[name='nro_documento']").value;
    let nombre = document.querySelector("form[id='form-agregar-cliente'] input[name='nombre']").value;
    let apellido_paterno = document.querySelector("form[id='form-agregar-cliente'] input[name='apellido_paterno']").value;
    let apellido_materno = document.querySelector("form[id='form-agregar-cliente'] input[name='apellido_materno']").value;
    let razon_social = document.querySelector("form[id='form-agregar-cliente'] input[name='razon_social']").value;
    let telefono = document.querySelector("form[id='form-agregar-cliente'] input[name='telefono']").value;
    let direccion = document.querySelector("form[id='form-agregar-cliente'] input[name='direccion']").value;

    let  data={
        'tipo_cliente' : tipo_cliente?tipo_cliente:null,
        'tipo_documento' : id_doc_identidad?id_doc_identidad:null,
        'nro_documento' : nro_documento?nro_documento:null,
        'nombre' : nombre?nombre:null,
        'apellido_paterno' : apellido_paterno?apellido_paterno:null,
        'apellido_materno' : apellido_materno?apellido_materno:null,
        'razon_social' : razon_social?razon_social:null,
        'telefono' : telefono?telefono:null,
        'direccion' : direccion?direccion:null
    };

    return data;
}

function guardar_cliente(){
    let payload = get_data_form_agregar_cliente();
    // console.log(payload);
    $.ajax({
        type: 'POST',
        url: 'save_cliente',
        data: payload,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.status == 200){
                alert('Cliente registrado con éxito');
                var ask = confirm('¿Desea continuar agregando más clientes ?');
                if (ask == true){
                    return false;
                }else{
                    $('#modal-add-cliente').modal('hide');
                    
                }
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        alert('fail, Error al guardar');
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });    
}

function limpiarFormAgregarCliente(){
    // document.querySelector("form[id='form-agregar-cliente'] select[name='tipo_cliente']").value= '';
    // document.querySelector("form[id='form-agregar-cliente'] select[name='id_doc_identidad']").value= '';
    document.querySelector("form[id='form-agregar-cliente'] input[name='nro_documento']").value= '';
    document.querySelector("form[id='form-agregar-cliente'] input[name='nombre']").value= '';
    document.querySelector("form[id='form-agregar-cliente'] input[name='apellido_paterno']").value= '';
    document.querySelector("form[id='form-agregar-cliente'] input[name='apellido_materno']").value= '';
    document.querySelector("form[id='form-agregar-cliente'] input[name='razon_social']").value= '';
    document.querySelector("form[id='form-agregar-cliente'] input[name='telefono']").value= '';
    document.querySelector("form[id='form-agregar-cliente'] input[name='direccion']").value= '';
}

function evaluarDocumentoSeleccionado(event){
    let valor =event.target.value;
    if (valor != '2'){ // si tipo de documento no es RUC
        $('#btnConsultaSunat').addClass('disabled');
    }else{
        $('#btnConsultaSunat').removeClass('disabled');
    }
}