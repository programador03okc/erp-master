$(function(){
    $('#listaSerie tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaSerie').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        // clearForm(form);
        mostrar_serie(myId);
        changeStateButton('historial');
    }); 
});

function listar_series(producto){
    var vardataTables = funcDatatables();
    $('#listaSerie').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'ajax': 'listar_series_producto/'+producto,
        'columns': [
            {'data': 'id_prod_serie'},
            {'data': 'alm_descripcion'},
            {'data': 'serie'},
            {'data': 'guia_com'},
            {'data': 'guia_ven'},
            {'data': 'fecha_registro'},
            // {'render':
            //     function (data, type, row){
            //         return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
            //     }
            // }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
function mostrar_serie(id){
    baseUrl = 'mostrar_serie/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response[0]);
            $('[name=id_prod_serie]').val(response[0].id_prod_serie);
            $('[name=id_prod_ubi]').val(response[0].id_prod_ubi).trigger('change.select2');
            $('[name=serie]').val(response[0].serie);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_serie(data, action){
    console.log('data:'+data);
    if (action == 'register'){
        baseUrl = 'guardar_serie';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_serie';
    }
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log('response'+response);
            if (response > 0){
                alert('Serie registrada con éxito');
                $('#listaSerie').DataTable().ajax.reload();
                changeStateButton('guardar');
                clearForm('form-serie');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_serie(ids){
    // $.ajax({
    //     type: 'GET',
    //     headers: {'X-CSRF-TOKEN': token},
    //     url: 'revisar_serie/'+ids,
    //     dataType: 'JSON',
    //     success: function(response){
    //         if (response >= 1){
    //             alert('No es posible anular. \nEl serie seleccionado está relacionado con '
    //             +response+' serie(es).');
    //         }
    //         else {
                $.ajax({
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    url: 'anular_serie/'+ids,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('Serie anulada con éxito');
                            $('#listaSerie').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-serie');
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