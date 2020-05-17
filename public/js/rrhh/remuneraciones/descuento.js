$(function(){
    $('#ListaDescuentoTrab tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        mostrar_descuento_id(miTr);
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
                mostrar_descuento_table(response[0].id_trabajador);
                cargar_regimen(response[0].id_trabajador);
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

function cargar_regimen(id) {
    baseUrl = 'cargar_regimen/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_tipo_pla]').html('<option value="0" selected disabled>Elija una opción</option>'+response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_descuento_table(id){
    $('#trab-descuento').empty();
    baseUrl = 'listar_descuento/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#trab-descuento').append(response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_descuento_id(id){
    baseUrl = 'cargar_descuento/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_descuento]').val(response[0].id_descuento);
            $('[name=id_variable_descuento]').val(response[0].id_variable_descuento);
            $('[name=afecto]').val(response[0].afecto);
            $('[name=fecha]').val(response[0].fecha_descuento);
            $('[name=importe]').val(response[0].importe);
            $('[name=id_tipo_pla]').val(response[0].id_tipo_planilla);
            $('[name=motivo]').val(response[0].concepto);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_descuento(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_descuento';
        msj = 'Descuento registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_descuento';
        msj = 'Descuento editado con exito';
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
                mostrar_descuento_table(response);
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_descuento(ids){
    var trab = $('[name=id_trabajador]').val();
    baseUrl = 'anular_descuento/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Descuento anulado con exito');
                mostrar_descuento_table(trab);
                changeStateButton('anular');
                clearForm('form-descuento');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}