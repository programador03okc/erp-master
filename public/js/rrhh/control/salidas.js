$(function(){
    $('.mytable tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        mostrar_salida_id(id);
        changeStateButton('historial');
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
                mostrar_salida_table(response[0].id_trabajador);
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

function mostrar_salida_table(id){
    var vardataTables = funcDatatables();
    $('#trab-salidas').empty();
    baseUrl = 'listar_salidas/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            dataSet = response;
            columns = [
                {'data': 'id_permiso'},
                {'data': 'inicio'},
                {'data': 'mes'},
                {'data': 'tipo'},
                {'data': 'fecha'},
                {'data': 'hora'},
                {'data': 'autoriza'}
            ];

            $('#ListaSalidas').dataTable({
                'dom': 'lfrtip',
                'language' : vardataTables[0],
                'processing': true,
                'bDestroy': true,
                'data': dataSet,
                'columns': columns,
                'columnDefs': [{'aTargets': [0], 'sClass': 'invisible'}, {'aTargets': [1], 'sClass': 'invisible'}, {'aTargets': [2], 'sClass': 'invisible'}],
                'order': [[1, 'desc']]
            });
            resizeSide();
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_salida_id(id){
    baseUrl = 'cargar_salidas/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_permiso]').val(response[0].id_permiso);
            $('[name=tipo]').val(response[0].tipo);
            $('[name=id_tipo_permiso]').val(response[0].id_tipo_permiso);
            $('[name=fecha_inicio_permiso]').val(response[0].fecha_inicio_permiso);
            $('[name=fecha_fin_permiso]').val(response[0].fecha_fin_permiso);
            $('[name=id_trabajador_autoriza]').val(response[0].id_trabajador_autoriza).trigger('change.select2');
            $('[name=hora_inicio]').val(response[0].hora_inicio);
            $('[name=hora_fin]').val(response[0].hora_fin);
            $('[name=motivo]').val(response[0].motivo);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_salidas(data, action){
    var msj;
    var id_trabajador = $('[name=id_trabajador]').val();
    var id_tipo_permiso = $('[name=id_tipo_permiso]').val();
    var id_trabajador_autoriza = $('[name=id_trabajador_autoriza]').val();

    if (id_trabajador > 0){
        if(id_tipo_permiso > 0){
            if(id_trabajador_autoriza > 0){
                if (action == 'register'){
                    baseUrl = 'guardar_salidas';
                    msj = 'Salida/Permiso registrado con exito';
                }else if(action == 'edition'){
                    baseUrl = 'editar_salidas';
                    msj = 'Salida/Permiso editado con exito';
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
                            mostrar_salida_table(response);
                            changeStateButton('guardar');
                        }
                    }
                }).fail( function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }else{
                alert('Debe seleccionar el trabajador que autoriza');
            }
        }else{
            alert('Debe seleccionar el tipo de permiso');
        }
    }else{
        alert('Debe seleccionar un trabajador que se encuentre registrado en el sistema');
    }
}

function anular_salidas(ids){
    var trab = $('[name=id_trabajador]').val();
    baseUrl = 'anular_salidas/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Salida/Permiso anulado con exito');
                mostrar_salida_table(trab);
                changeStateButton('anular');
                clearForm('form-salida');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}