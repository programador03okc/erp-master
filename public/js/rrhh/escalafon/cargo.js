$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaCargo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        "processing": true,
        "bDestroy": true,
        'ajax': 'listar_cargo',
        'columns': [
            {'data': 'id_cargo'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return  (formatDecimal(row['sueldo_rango_minimo']));
                }
            },
            {'render':
                function (data, type, row){
                    return  (formatDecimal(row['sueldo_rango_maximo']));
                }
            },
            {'render':
                function (data, type, row){
                    return  (formatDecimal(row['sueldo_fijo']));
                }
            },
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
        mostrar_cargo(id);
        changeStateButton('historial');
    });
    resizeSide();
});

function mostrar_cargo(id){
    baseUrl = 'cargar_cargo/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_cargo]').val(response[0].id_cargo);
            $('[name=id_empresa]').val(response[0].id_empresa);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=sueldo_rango_minimo]').val(response[0].sueldo_rango_minimo);
            $('[name=sueldo_rango_maximo]').val(response[0].sueldo_rango_maximo);
            $('[name=sueldo_fijo]').val(response[0].sueldo_fijo);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_cargo(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_cargo';
        msj = 'Cargo registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_cargo';
        msj = 'Cargo editado con exito';
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
                $('#listaCargo').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_cargo(ids){
    baseUrl = 'anular_cargo/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Cargo anulado con exito');
                $('#listaCargo').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-cargo');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}