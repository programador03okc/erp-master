$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaPensiones').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': 'listar_pension',
        'columns': [
            {'data': 'id_pension'},
            {'render':
                function (data, type, row, meta){
                    return  (leftZero(2 , meta.row + 1));
                }
            },
            {'data': 'descripcion'},
            {'render':
                function (data, type, row, meta){
                    return  (formatDecimal(row['porcentaje_general']));
                }
            },
            {'render':
                function (data, type, row, meta){
                    return  (formatDecimal(row['aporte']));
                }
            },
            {'render':
                function (data, type, row, meta){
                    return  (formatDecimal(row['prima_seguro']));
                }
            },
            {'render':
                function (data, type, row, meta){
                    return  (formatDecimal(row['comision']));
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [2, 'asc']
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
        mostrar_pension(id);
        changeStateButton('historial');
    });
    $('#listaPensiones').DataTable().on("draw", function(){
        resizeSide();
    });
});

function mostrar_pension(id){
    baseUrl = 'cargar_pension/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_pension]').val(response[0].id_pension);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=porcentaje_general]').val(response[0].porcentaje_general);
            $('[name=aporte]').val(response[0].aporte);
            $('[name=prima_seguro]').val(response[0].prima_seguro);
            $('[name=comision]').val(response[0].comision);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_pension(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_pension';
        msj = 'Fondo de Pensión registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_pension';
        msj = 'Fondo de Pensión editado con exito';
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
                $('#listaPensiones').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_pension(ids){
    baseUrl = 'anular_pension/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Fondo de Pensión anulado con exito');
                $('#listaPensiones').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-con_derecho_hab');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}