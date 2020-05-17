$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaUnidMed').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_unidmed',
        'columns': [
            {'data': 'id_unidad_medida'},
            {'data': 'abreviatura'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            },
            // {'render':
            //     function (data, type, row){
            //         return (formatDate(row['fecha_registro']));
            //     }
            // }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    $('.group-table .mytable tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaUnidMed').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        mostrar_unidmed(id);
        changeStateButton('historial');
    });

    
});

function mostrar_unidmed(id){
    baseUrl = 'mostrar_unidmed/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_unidad_medida]').val(response[0].id_unidad_medida);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=abreviatura]').val(response[0].abreviatura);
            $('[name=estado]').val(response[0].estado);
            // $('[id=fecha_registro] label').text('');
            // $('[id=fecha_registro] label').append(formatDateHour(response[0].fecha_registro));
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_unidmed(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_unidmed';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_unidmed';
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
                alert('Tipo de Movimiento registrado con exito');
                $('#listaUnidMed').DataTable().ajax.reload();
                clearForm('form-unidmed');
                changeStateButton('guardar');
                $('#form-unidmed').attr('type', 'register');
                changeStateInput('form-unidmed', true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_unidmed(ids){
    baseUrl = 'anular_unidmed/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Tipo de Movimiento anulado con exito');
                $('#listaUnidMed').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-unidmed');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}