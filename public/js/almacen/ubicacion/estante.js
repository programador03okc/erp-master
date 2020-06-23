$(function(){
    var vardataTables = funcDatatables();
    // $('#listaEstante').dataTable({
    //     'dom': vardataTables[1],
    //     'buttons': vardataTables[2],
    //     'language' : vardataTables[0],
    // });
    $('#listaEstante tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaEstante').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        // clearForm(form);
        mostrar_estante(myId);
        changeStateButton('historial');
    }); 
});

function listar_estantes(almacen){
    console.log('almacen->'+almacen);
    var vardataTables = funcDatatables();
    $('#listaEstante').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_estantes_almacen/'+almacen,
        'columns': [
            {'data': 'id_estante'},
            {'data': 'alm_descripcion'},
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    $('[name=id_almacen]').val(almacen).trigger('change.select2');
}
function mostrar_estante(id){
    baseUrl = 'mostrar_estante/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response[0]);
            $('[name=id_estante]').val(response[0].id_estante);
            $('[name=id_almacen]').val(response[0].id_almacen).trigger('change.select2');
            $('[name=codigo]').val(response[0].codigo);
            $('[name=estado]').val(response[0].estado);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_estante(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_estantes';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_estante';
    }
    var token = $('#token').val();

    var id_almacen = $('[name=id_almacen]').val();
    var desde = $('[name=desde]').val();
    var hasta = $('[name=hasta]').val();

    if (desde > 0 && hasta > 0){
        var parametros = {
            "id_almacen" : id_almacen,
            "desde" : desde,
            "hasta" : hasta
        };
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: baseUrl,
            data: parametros,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Estante registrado con éxito');
                    $('#listaEstante').DataTable().ajax.reload();
                    changeStateButton('guardar');
                    clearForm('form-estante');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function anular_estante(ids){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'revisar_estante/'+ids,
        dataType: 'JSON',
        success: function(response){
            if (response >= 1){
                alert('No es posible anular. \nEl estante seleccionado está relacionado con '
                +response+' nivel(es).');
            }
            else {
                $.ajax({
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    url: 'anular_estante/'+ids,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('Estante anulado con éxito');
                            $('#listaEstante').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-estante');
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