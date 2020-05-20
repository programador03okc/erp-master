$(function(){
    $('#ListaDerechoHab tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        mostrar_derechohab_id(miTr);
    });
    resizeSide();
});

function buscarPersona(type){
    var dni;
    var baseUrl;
    if (type == 1){
        dni = $('[name=dni_trab]').val();
        baseUrl = 'cargar_trabajador_dni_esc/'+dni;
    }else{
        dni = $('[name=dni_per]').val();
        baseUrl = 'cargar_persona_dni_esc/'+dni;
    }
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (type == 1){
                if (response[0].id_trabajador > 0) {
                    $('[name=id_trabajador]').val(response[0].id_trabajador);
                    $('[name=descripcion_trab]').val(response[0].nombres+' '+response[0].apellido_paterno+' '+response[0].apellido_materno);
                    mostrar_derechohab_table(response[0].id_trabajador);
                }else{
                    alert('No se encontró trabajador con dicho DNI');
                    $('[name=dni_trab]').select();
                }
            }else{
                if (response[0].id_persona > 0) {
                    $('[name=id_persona]').val(response[0].id_persona);
                    $('[name=descripcion_pers]').val(response[0].nombres+' '+response[0].apellido_paterno+' '+response[0].apellido_materno);
                }else{
                    alert('No se encontró persona con dicho DNI');
                    $('[name=dni_per]').select();
                }
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_derechohab_table(id){
    $('#dhab').empty();
    baseUrl = 'listar_derecho_hab/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#dhab').append(response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_derechohab_id(id){
    baseUrl = 'cargar_derecho_hab/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_derecho_habiente]').val(response.id_derecho_habiente);
            $('[name=id_persona]').val(response.id_persona);
            $('[name=dni_per]').val(response.dni_persona);
            $('[name=descripcion_pers]').val(response.nombre_persona);
            $('[name=id_condicion_dh]').val(response.id_condicion_dh);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_derecho_hab(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_derecho_hab';
        msj = 'Derecho habiente registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_derecho_hab';
        msj = 'Derecho habiente editado con exito';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert(msj);
                mostrar_derechohab_table(response);
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_derecho_hab(ids){
    baseUrl = 'anular_derecho_hab/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Derecho habiente anulado con exito');
                mostrar_derechohab_table(response);
                changeStateButton('anular');
                clearForm('form-derecho_hab');
                $('[name=id_persona]').val('');
            $('[name=dni_per]').val('');
            $('[name=descripcion_pers]').val('');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}