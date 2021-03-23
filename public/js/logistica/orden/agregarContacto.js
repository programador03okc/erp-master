$(function(){
    $("#form-agregar-contacto").on("submit", function(e){
        e.preventDefault();
        guardar_contacto();
    });
});

function agregar_contacto(){
    let razon_social_proveedor = document.querySelector("div[type='crear-orden-requerimiento'] input[name='razon_social']").value;
    let id_contrib = document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contrib']").value;
    if(id_contrib >0){
        $('#modal-agregar-contacto-proveedor').modal({
            show: true
        });

    }else{
        alert("Debe seleccionar un proveedor");
    }
   
    document.querySelector("form[id='form-agregar-contacto'] input[name='id_contribuyente']").value= id_contrib;
    document.querySelector("form[id='form-agregar-contacto'] span[id='razon_social_proveedor']").textContent= razon_social_proveedor;
}

function capturarDataContactoProveedor(){
    let id_contribuyente = document.querySelector("form[id='form-agregar-contacto'] input[name='id_contribuyente']").value;
    let nombre = document.querySelector("form[id='form-agregar-contacto'] input[name='nombre']").value;
    let cargo = document.querySelector("form[id='form-agregar-contacto'] input[name='cargo']").value;
    let email = document.querySelector("form[id='form-agregar-contacto'] input[name='email']").value;
    let telefono = document.querySelector("form[id='form-agregar-contacto'] input[name='telefono']").value;
    let direccion = document.querySelector("form[id='form-agregar-contacto'] input[name='direccion']").value;
    
    return {id_contribuyente,nombre,cargo,email,telefono,direccion};

}

function guardar_contacto(){



    var formData = capturarDataContactoProveedor();
    // console.log(formData);
    $.ajax({
        type: 'POST',
        url: 'guardar_contacto',
        data: formData,
        cache: false,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.status =='200'){
                alert('Contacto registrado con éxito');
                document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contrib']").value = response.data.id_contribuyente;
                document.querySelector("div[type='crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value = response.data.id_datos_contacto;
                document.querySelector("div[type='crear-orden-requerimiento'] input[name='contacto_proveedor_nombre']").value = response.data.nombre;
                document.querySelector("div[type='crear-orden-requerimiento'] input[name='contacto_proveedor_telefono']").value = response.data.telefono;
                $('#modal-agregar-contacto-proveedor').modal('hide');

            }else{
                alert('Hubo un error al intentar guardar el contacto');
            }

                
            
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });        
}