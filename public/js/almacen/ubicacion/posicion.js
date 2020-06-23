$(function(){
    $('#listaPosicion tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaPosicion').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        // clearForm(form);
        mostrar_posicion(myId);
        changeStateButton('historial');
    }); 
});

function listar_posiciones(nivel){
    var vardataTables = funcDatatables();
    $('#listaPosicion').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_posiciones_nivel/'+nivel,
        'columns': [
            {'data': 'id_posicion'},
            {'data': 'alm_descripcion'},
            {'data': 'cod_estante'},
            {'data': 'cod_nivel'},
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    // $('[name=id_nivel_posicion]').val(nivel).trigger('change.select2');
}

function listar_niveles_posicion(id_nivel){
    console.log('listar_niveles_posicion '+id_nivel);
    $.ajax({
        type: 'GET',
        url: 'listar_niveles',
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var option = '';
            for (var i=0;i<response['data'].length;i++){

                if (response['data'][i].id_nivel == id_nivel){
                    option+='<option value="'+response['data'][i].id_nivel+'" selected>'+response['data'][i].codigo+'</option>';
                } else {
                    option+='<option value="'+response['data'][i].id_nivel+'">'+response['data'][i].codigo+'</option>';
                }
            }
            $('[name=id_nivel_posicion]').html('<option value="0">Elija una opción</option>'+option);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_posicion(id){
    baseUrl = 'mostrar_posicion/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response[0]);
            $('[name=id_posicion]').val(response[0].id_posicion);
            $('[name=id_almacen_posicion]').val(response[0].id_almacen).trigger('change.select2');
            $('[name=id_estante_posicion]').val(response[0].id_estante).trigger('change.select2');
            $('[name=id_nivel_posicion]').val(response[0].id_nivel);
            $('[name=codigo_posicion]').val(response[0].codigo);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_posicion(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_posiciones';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_posicion';
    }
    var token = $('#token').val();

    var id_nivel = $('[name=id_nivel_posicion]').val();
    var cod_nivel = $('select[name="id_nivel_posicion"] option:selected').text();
    var desde = $('[name=posicion_desde]').val();
    var hasta = $('[name=posicion_hasta]').val();

    if (desde !== "" && hasta !== ""){
        var parametros = {
            "id_nivel" : id_nivel,
            "cod_nivel" : cod_nivel,
            "desde" : desde,
            "hasta" : hasta
        };
        console.log(parametros);
        console.log();
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: baseUrl,
            data: parametros,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('posicion registrado con éxito');
                    $('#listaPosicion').DataTable().ajax.reload();
                    changeStateButton('guardar');
                    clearForm('form-posicion');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function anular_posicion(ids){
    // $.ajax({
    //     type: 'GET',
    //     headers: {'X-CSRF-TOKEN': token},
    //     url: 'revisar_posicion/'+ids,
    //     dataType: 'JSON',
    //     success: function(response){
    //         if (response >= 1){
    //             alert('No es posible anular. \nEl posicion seleccionado está relacionado con '
    //             +response+' posicion(es).');
    //         }
    //         else {
                $.ajax({
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    url: 'anular_posicion/'+ids,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('posicion anulado con éxito');
                            $('#listaPosicion').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-posicion');
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
    //         }
    //     }
    // }).fail( function( jqXHR, textStatus, errorThrown ){
    //     console.log(jqXHR);
    //     console.log(textStatus);
    //     console.log(errorThrown);
    // });
}