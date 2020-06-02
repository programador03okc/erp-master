$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaTipoServ').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_tipoServ',
        'columns': [
            {'data': 'id_tipo_servicio'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    $('.group-table .mytable tbody').on('click', 'tr', function(){
        var status = $("#form-tipoServ").attr('type');
        if (status !== "edition"){
            if ($(this).hasClass('eventClick')){
                $(this).removeClass('eventClick');
            } else {
                $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            var id = $(this)[0].firstChild.innerHTML;
            clearForm(form);
            mostrar_tipoServ(id);
            changeStateButton('historial');
        }
    });
});

function mostrar_tipoServ(id){
    baseUrl = 'mostrar_tipoServ/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_tipo_servicio]').val(response[0].id_tipo_servicio);
            $('[name=descripcion]').val(response[0].descripcion);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_tipoServ(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_tipoServ';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_tipoServ';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.length > 0){
                alert(response);
            } else { 
                alert('TipoServ registrado con exito');
                $('#listaTipoServ').DataTable().ajax.reload();
                clearForm('form-tipoServ');
                
                changeStateButton('guardar');
                $('#form-tipoServ').attr('type', 'register');
                changeStateInput('form-tipoServ', true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_tipoServ(ids){
    baseUrl = 'anular_tipoServ/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'revisarTipoServ/'+ids,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response >= 1){
                alert('No es posible anular. \nEl tipoServ seleccionado está relacionado con '
                +response+' categoría(s).');
            }
            else {
                $.ajax({
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    url: baseUrl,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('TipoServ anulado con exito');
                            $('#listaTipoServ').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-tipoServ');
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