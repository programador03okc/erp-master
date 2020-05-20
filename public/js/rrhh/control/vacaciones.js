$(function(){
    $('#ListaVacacionesTrab tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        mostrar_vacaciones_id(miTr);
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
                mostrar_vacaciones_table(response[0].id_trabajador);
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

function mostrar_vacaciones_table(id){
    $('#trab-vacaciones').empty();
    baseUrl = 'listar_vacaciones/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#trab-vacaciones').append(response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_vacaciones_id(id){
    baseUrl = 'cargar_vacaciones/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_vacaciones]').val(response[0].id_vacaciones);
            $('[name=concepto]').val(response[0].concepto);
            $('[name=fecha_inicio]').val(response[0].fecha_inicio);
            $('[name=fecha_fin]').val(response[0].fecha_fin);
            $('[name=fecha_retorno]').val(response[0].fecha_retorno);
            $('[name=dias]').val(response[0].dias);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_vacaciones(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_vacaciones';
        msj = 'Vacaciones registradas con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_vacaciones';
        msj = 'Vacaciones editadas con exito';
    }

    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            if (response.id_trabajador > 0){
                alert(msj);
                mostrar_vacaciones_table(response.id_trabajador);
                imprimir(response.id_vacaciones);
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_vacaciones(ids){
    var trab = $('[name=id_trabajador]').val();
    baseUrl = 'anular_vacaciones/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Vacaciones anuladas con exito');
                mostrar_vacaciones_table(trab);
                changeStateButton('anular');
                clearForm('form-vacaciones');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}


function imprimir(id){
    window.open('generar_vacaciones/' + id, 'Vacaciones', 'height=700, width=800, scrollTo, resizable=1, scrollbars=1, location=0');
    return false;
}