$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaTipoAport').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': 'listar_tipo_aporte',
        'columns': [
            {'data': 'id_variable_aportacion'},
            {'render':
                function (data, type, row, meta){
                    return  (leftZero(2 , meta.row + 1));
                }
            },
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [1, 'asc']
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
        mostrar_tipo_aporte(id);
        changeStateButton('historial');
    });

});

function mostrar_tipo_aporte(id){
    baseUrl = 'cargar_tipo_aporte/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_variable_aportacion]').val(response[0].id_variable_aportacion);
            $('[name=descripcion]').val(response[0].descripcion);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_tipo_aporte(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_tipo_aporte';
        msj = 'Tipo de aportacion registrada con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_tipo_aporte';
        msj = 'Tipo de aportacion editada con exito';
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
                $('#listaTipoAport').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_tipo_aporte(ids){
    baseUrl = 'anular_tipo_aporte/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Tipo de aportacion anulada con exito');
                $('#listaTipoAport').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-tipo_aporte');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}