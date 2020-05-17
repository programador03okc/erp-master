$(function(){
    $('#ListaPrestamoTrab tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        mostrar_prestamo_id(miTr);
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
                mostrar_prestamo_table(response[0].id_trabajador);
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

function mostrar_prestamo_table(id){
    $('#trab-prestamo').empty();
    baseUrl = 'listar_prestamo/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#trab-prestamo').append(response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_prestamo_id(id){
    baseUrl = 'cargar_prestamo/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_prestamo]').val(response[0].id_prestamo);
            $('[name=concepto]').val(response[0].concepto);
            $('[name=fecha_prestamo]').val(response[0].fecha_prestamo);
            $('[name=nro_cuotas]').val(response[0].nro_cuotas);
            $('[name=monto_prestamo]').val(response[0].monto_prestamo);
            $('[name=porcentaje]').val(response[0].porcentaje);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_prestamo(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_prestamo';
        msj = 'Préstamo registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_prestamo';
        msj = 'Préstamo editado con exito';
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
                mostrar_prestamo_table(response);
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_prestamo(ids){
    var trab = $('[name=id_trabajador]').val();
    baseUrl = 'anular_prestamo/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Préstamo anulado con exito');
                mostrar_prestamo_table(trab);
                changeStateButton('anular');
                clearForm('form-prestamo');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}