$(function(){
    $('#listaUbicacion tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaUbicacion').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        // clearForm(form);
        mostrar_ubicacion(myId);
        changeStateButton('historial');
    }); 
});

function listar_ubicaciones(producto){
    var vardataTables = funcDatatables();
    $('#listaUbicacion').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'ajax': 'listar_ubicaciones_producto/'+producto,
        'columns': [
            {'data': 'id_prod_ubi'},
            {'data': 'id_almacen'},
            {'data': 'alm_descripcion'},
            {'data': 'cod_posicion'},
            {'data': 'stock'},
            {'data': 'costo_promedio'},
            {'data': 'valorizacion'},
            // {'render':
            //     function (data, type, row){
            //         return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
            //     }
            // }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
function mostrar_ubicacion(id){
    baseUrl = 'mostrar_ubicacion/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response[0]);
            $('[name=id_prod_ubi]').val(response[0].id_prod_ubi);
            $('[name=des_almacen]').val(response[0].des_almacen);
            $('[name=id_posicion]').val(response[0].id_posicion).trigger('change.select2');
            $('[name=stock]').val(response[0].stock);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_ubicacion(data, action){
    console.log('data:'+data);
    if (action == 'register'){
        baseUrl = 'guardar_ubicacion';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_ubicacion';
    }
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Ubicación asignada con éxito');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    $('#listaUbicacion').DataTable().ajax.reload();
    changeStateButton('guardar');
    clearForm('form-ubicacion');
}

function anular_ubicacion(ids){
    // $.ajax({
    //     type: 'GET',
    //     headers: {'X-CSRF-TOKEN': token},
    //     url: 'revisar_ubicacion/'+ids,
    //     dataType: 'JSON',
    //     success: function(response){
    //         if (response >= 1){
    //             alert('No es posible anular. \nEl ubicacion seleccionado está relacionado con '
    //             +response+' ubicacion(es).');
    //         }
    //         else {
                $.ajax({
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    url: 'anular_ubicacion/'+ids,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('ubicacion anulado con éxito');
                            $('#listaUbicacion').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-ubicacion');
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