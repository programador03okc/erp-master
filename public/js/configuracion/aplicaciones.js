$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaAplicaciones').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        "processing": true,
        "bDestroy": true,
        'ajax': 'listar_aplicaciones',
        'columns': [
            {'data': 'id_aplicacion'},
            {'data': 'modulo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row, meta){
                    return ('<div class="text-center"><button type="button" class="btn btn-sm btn-success btn-flat botonList" data-toggle="tooltip" data-placement="bottom" title="Agregar Botones" onClick="agregarBotonMenu('+row['id_aplicacion']+');"><i class="fas fa-plus"></i></button>');
                }
            }
        ],
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
        mostrar_aplicaciones(id);
        changeStateButton('historial');
    });
    resizeSide();
});

function agregarBotonMenu(id){
alert(id);
}

function mostrar_aplicaciones(id){
    baseUrl = 'cargar_aplicaciones/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_aplicacion]').val(response[0][0].id_aplicacion);
            $('[name=modulo]').val(response[0][0].id_padre);
            $('[name=descripcion]').val(response[0][0].descripcion);
            $('[name=ruta]').val(response[0][0].ruta);
            $('#sub_modulo').html(response[1]);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_aplicaciones(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_aplicaciones';
        msj = 'Aplicación registrada con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_aplicaciones';
        msj = 'Aplicación editada con exito';
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
                $('#listaAplicaciones').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_aplicaciones(ids){
    baseUrl = 'anular_aplicaciones/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Módulo anulado con exito');
                $('#listaAplicaciones').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-aplicaciones');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function cambiarModulo(value){
    baseUrl = 'cargar_submodulos/'+value;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        success: function(response){
            $('[name=sub_modulo]').html('<option value="0" disabled>Elija una opción</option>' + response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}