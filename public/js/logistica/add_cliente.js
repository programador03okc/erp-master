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
    // let tipo_cliente = document.querySelector("form[id='form-requerimiento'] select[name='tipo_cliente']").value;
    let tipo_cliente = $('[name=tipo_cliente]').val();
    if(tipo_cliente == 1){
        habilitarInputPersonaNatural();
    }else if(tipo_cliente ==2){
        habilitarInputPersonaJuridica();
    }

    $('[name=id_proveedor]').val('');
    $('[name=nro_documento]').val('');
    // $('[name=id_doc_identidad]').val('');
    $('[name=direccion_fiscal]').val('');
    $('[name=razon_social]').val('');
    $('[name=nombre]').val('');
    $('[name=apellido_paterno]').val('');
    $('[name=apellido_materno]').val('');
    $('[name=telefono]').val('');
    $('[name=direccion]').val('');
    $('[name=email]').val('');
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
    let email = document.querySelector("form[id='form-agregar-cliente'] input[name='email']").value;

    let  data={
        'tipo_cliente' : tipo_cliente?tipo_cliente:null,
        'tipo_documento' : id_doc_identidad?id_doc_identidad:null,
        'nro_documento' : nro_documento?nro_documento:null,
        'nombre' : nombre?nombre:null,
        'apellido_paterno' : apellido_paterno?apellido_paterno:null,
        'apellido_materno' : apellido_materno?apellido_materno:null,
        'razon_social' : razon_social?razon_social:null,
        'telefono' : telefono?telefono:null,
        'direccion' : direccion?direccion:null,
        'email' : email?email:null
    };

    return data;
}

function guardar_cliente(){
    var msj = validaCampos();
    if (msj.length > 0){
        alert(msj);
    } else {
        let payload = get_data_form_agregar_cliente();
        $.ajax({
            type: 'POST',
            url: 'save_cliente',
            data: payload,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if(response.status == 200){
                    alert('Cliente registrado con éxito');
                    let page = document.getElementsByClassName('page-main')[0].getAttribute('type');

                    if (page=='requerimiento'){
                        if(response.data.tipo_cliente == 1){
                            document.querySelector("form[id='form-requerimiento'] input[name='id_persona']").value = response.data.id;
                            document.querySelector("form[id='form-requerimiento'] input[name='dni_persona']").value = response.data.nro_documento;
                            document.querySelector("form[id='form-requerimiento'] input[name='nombre_persona']").value = response.data.nombre_completo;
                            document.querySelector("form[id='form-requerimiento'] input[name='direccion_entrega']").value =response.data.direccion;
                            document.querySelector("form[id='form-requerimiento'] input[name='telefono_cliente']").value =response.data.telefono;
                            document.querySelector("form[id='form-requerimiento'] input[name='email_cliente']").value = response.data.email;
                        }
                        else if(response.data.tipo_cliente ==2){
                            document.querySelector("form[id='form-requerimiento'] input[name='id_cliente']").value = response.data.id;
                            document.querySelector("form[id='form-requerimiento'] input[name='cliente_ruc']").value = response.data.nro_documento;
                            document.querySelector("form[id='form-requerimiento'] input[name='cliente_razon_social']").value = response.data.razon_social;
                            document.querySelector("form[id='form-requerimiento'] input[name='direccion_entrega']").value =response.data.direccion;
                            document.querySelector("form[id='form-requerimiento'] input[name='telefono_cliente']").value =response.data.telefono;
                            document.querySelector("form[id='form-requerimiento'] input[name='email_cliente']").value = response.data.email;
                        }
                    } else if (page=='requerimientosPendientes'){
                        if(response.data.tipo_cliente == 1){
                            $('[name=id_persona]').val(response.data.id);
                            $('[name=dni_persona]').val(response.data.nro_documento);
                            $('[name=nombre_persona]').val(response.data.nombre_completo);
                            $('[name=direccion_destino]').val(response.data.direccion);
                            $('[name=telefono_cliente]').val(response.data.telefono);
                            $('[name=correo_cliente]').val(response.data.email);
                        }
                        else if(response.data.tipo_cliente == 2){
                            $('[name=id_cliente]').val(response.data.id);
                            $('[name=cliente_ruc]').val(response.data.nro_documento);
                            $('[name=cliente_razon_social]').val(response.data.razon_social);
                            $('[name=direccion_destino]').val(response.data.direccion);
                            $('[name=telefono_cliente]').val(response.data.telefono);
                            $('[name=correo_cliente]').val(response.data.email);
                        }
                    }
                    $('#modal-add-cliente').modal('hide');
                } 
                else if(response.status == 0){
                    alert('El Nro de documento del cliente ya existe!');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            alert('fail, Error al guardar');
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
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
    document.querySelector("form[id='form-agregar-cliente'] input[name='email']").value= '';
}

function evaluarDocumentoSeleccionado(event){
    let valor =event.target.value;
    if (valor != '2'){ // si tipo de documento no es RUC
        $('#btnConsultaSunat').addClass('disabled');
    }else{
        $('#btnConsultaSunat').removeClass('disabled');
    }
}

function validaCampos(){
    var tipo = $('[name=tipo_cliente]').val();
    var name = $('[name=nombre]').val();
    var ap_p = $('[name=apellido_paterno]').val();
    var ap_m = $('[name=apellido_materno]').val();
    var razo = $('[name=razon_social]').val();
    var text = '';

    if (tipo == '1'){
        if (name == '' || name == null){
            text += 'Es necesario que ingrese un Nombre\n';
        }
        if (ap_p == '' || ap_p == null){
            text += 'Es necesario que ingrese un Apellido Paterno\n';
        }
        if (ap_m == '' || ap_m == null){
            text += 'Es necesario que ingrese un Apellido Materno\n';
        }
    } 
    else if (tipo == '2') {
        if (razo == '' || razo == null){
            text += 'Es necesario que ingrese una Razon Social';
        }
    }
    return text;
}