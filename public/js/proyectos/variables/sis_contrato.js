$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaSisContrato').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_sis_contratos',
        'columns': [
            {'data': 'id_sis_contrato'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            },
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_registro']));
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    $('.group-table .mytable tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        mostrar_sis_contrato(id);
        changeStateButton('historial');
    });

    
});

function mostrar_sis_contrato(id){
    baseUrl = 'mostrar_sis_contrato/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_sis_contrato]').val(response[0].id_sis_contrato);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=codigo]').val(response[0].codigo);
            // $('[name=estado]').val(response[0].estado);
            $('[id=fecha_registro] label').text('');
            $('[id=fecha_registro] label').append(formatDateHour(response[0].fecha_registro));
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_sis_contrato(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_sis_contrato';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_sis_contrato';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Sistema de Contrato registrado con exito');
                $('#listaSisContrato').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_sis_contrato(ids){
    baseUrl = 'anular_sis_contrato/'+ids;
    // $.ajax({
    //     type: 'GET',
    //     headers: {'X-CSRF-TOKEN': token},
    //     url: 'revisarsis_contrato/'+ids,
    //     dataType: 'JSON',
    //     success: function(response){
    //         console.log(response);
    //         if (response >= 1){
    //             alert('No es posible anular. \nEl sis_contrato seleccionado está relacionado con '
    //             +response+' categoría(s).');
    //         }
    //         else {
                $.ajax({
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    url: baseUrl,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('Sistema de Contrato anulado con exito');
                            $('#listaSisContrato').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-sis_contrato');
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