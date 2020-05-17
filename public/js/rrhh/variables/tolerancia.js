$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaTolerancia').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': 'listar_tolerancias',
        'columns': [
            {'data': 'id_tolerancia'},
            {'render':
                function (data, type, row, meta){
                    return  (leftZero(2 , meta.row + 1));
                }
            },
            {'render':
                function (data, type, row){
                    return (row['tiempo'] + ' minutos');
                }
            },
            {'render':
                function (data, type, row){
                    if (row['periodo'] == 1){
                        return ('Diario');
                    }else if (row['periodo'] == 2){
                        return ('Semanal');
                    }else if (row['periodo'] == 3){
                        return ('Mensual');
                    }
                }
            },
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [0, 'asc' ]
        ]
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
        mostrar_tolerancia(id);
        changeStateButton('historial');
    });

});

function mostrar_tolerancia(id){
    baseUrl = 'cargar_tolerancia/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_tolerancia]').val(response[0].id_tolerancia);
            $('[name=tiempo]').val(response[0].tiempo);
            $('[name=periodo]').val(response[0].periodo);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_tolerancia(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_tolerancia';
        msj = 'Tolerancia registrada con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_tolerancia';
        msj = 'Tolerancia editada con exito';
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
                $('#listaTolerancia').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_tolerancia(ids){
    baseUrl = 'anular_tolerancia/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Tolerancia anulada con exito');
                $('#listaTolerancia').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-tolerancia');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}