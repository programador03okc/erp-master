$(function(){
    $('#listaNivel tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaNivel').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        // clearForm(form);
        mostrar_nivel(myId);
        changeStateButton('historial');
    }); 
});

function listar_niveles(estante){
    var vardataTables = funcDatatables();
    $('#listaNivel').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_niveles_estante/'+estante,
        'columns': [
            {'data': 'id_nivel'},
            {'data': 'alm_descripcion'},
            {'data': 'cod_estante'},
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    // console.log($('[name=id_estante_nivel]'));
    // console.log($('select[name="id_estante_nivel"] option'));
    // $('[name=id_estante_nivel]').val(estante).trigger('change.select2');
}

function listar_estantes_nivel(id_estante){
    console.log('listar_estantes '+id_estante);
    $.ajax({
        type: 'GET',
        url: 'listar_estantes',
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var option = '';
            for (var i=0;i<response['data'].length;i++){

                if (response['data'][i].id_estante == id_estante){
                    option+='<option value="'+response['data'][i].id_estante+'" selected>'+response['data'][i].codigo+'</option>';
                } else {
                    option+='<option value="'+response['data'][i].id_estante+'">'+response['data'][i].codigo+'</option>';
                }
            }
            $('[name=id_estante_nivel]').html('<option value="0">Elija una opción</option>'+option);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_nivel(id){
    baseUrl = 'mostrar_nivel/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response[0]);
            $('[name=id_nivel]').val(response[0].id_nivel);
            $('[name=id_estante_nivel]').val(response[0].id_estante);
            $('[name=id_almacen_nivel]').val(response[0].id_almacen).trigger('change.select2');
            $('[name=codigo_nivel]').val(response[0].codigo);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_nivel(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_niveles';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_nivel';
    }
    var token = $('#token').val();

    var cod_estante = $('select[name="id_estante_nivel"] option:selected').text();
    var id_estante = $('[name=id_estante_nivel]').val();
    var desde = $('[name=nivel_desde]').val();
    var hasta = $('[name=nivel_hasta]').val();

    if (desde !== "" && hasta !== ""){
        var parametros = {
            "id_estante" : id_estante,
            "cod_estante" : cod_estante,
            "desde" : desde,
            "hasta" : hasta
        };
        console.log(parametros);
        $.ajax({
            type: 'POST',
            url: baseUrl,
            data: parametros,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Nivel registrado con éxito');
                    $('#listaNivel').DataTable().ajax.reload();
                    changeStateButton('guardar');
                    clearForm('form-nivel');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function anular_nivel(ids){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'revisar_nivel/'+ids,
        dataType: 'JSON',
        success: function(response){
            if (response >= 1){
                alert('No es posible anular. \nEl nivel seleccionado está relacionado con '
                +response+' nivel(es).');
            }
            else {
                $.ajax({
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    url: 'anular_nivel/'+ids,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('nivel anulado con éxito');
                            $('#listaNivel').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-nivel');
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}