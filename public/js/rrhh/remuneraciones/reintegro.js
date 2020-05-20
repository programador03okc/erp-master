$(function(){
    $('#ListaReintegroTrab tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        mostrar_reintegro_id(miTr);
    });
    resizeSide();
});

function buscarPersona(){
    var dni = $('[name=nro_documento]').val();
    baseUrl = 'cargar_trabajador_dni_esc/'+dni;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response[0].id_trabajador > 0) {
                $('[name=id_trabajador]').val(response[0].id_trabajador);
                $('[name=datos_trabajador]').val(response[0].nombres+' '+response[0].apellido_paterno+' '+response[0].apellido_materno);
                mostrar_reintegro_table(response[0].id_trabajador);
            }else{
                alert('No se encontró trabajador con dicho DNI');
                $('[name=nro_documento]').select();
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_reintegro_table(id){
    $('#trab-reintegro').empty();
    baseUrl = 'listar_reintegro/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#trab-reintegro').append(response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_reintegro_id(id){
    baseUrl = 'cargar_reintegro/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_reintegro]').val(response[0].id_reintegro);
            $('[name=id_variable_reintegro]').val(response[0].id_variable_reintegro);
            $('[name=afecto]').val(response[0].afecto);
            $('[name=fecha]').val(response[0].fecha);
            $('[name=afecto]').val(response[0].afecto);
            $('[name=importe]').val(response[0].importe);
            $('[name=motivo]').val(response[0].concepto);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_reintegro(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_reintegro';
        msj = 'Reintegro registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_reintegro';
        msj = 'Reintegro editado con exito';
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
                mostrar_reintegro_table(response);
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_reintegro(ids){
    var trab = $('[name=id_trabajador]').val();
    baseUrl = 'anular_reintegro/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Bonificación anulada con exito');
                mostrar_reintegro_table(trab);
                changeStateButton('anular');
                clearForm('form-reintegro');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}