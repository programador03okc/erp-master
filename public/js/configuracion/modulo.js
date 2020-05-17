$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaModulo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        "processing": true,
        "bDestroy": true,
        'ajax': 'listar_modulo',
        'columns': [
            {'data': 'id_modulo'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'ruta'}
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
        mostrar_modulo(id);
        changeStateButton('historial');
    });
    resizeSide();
});

function mostrar_modulo(id){
    baseUrl = 'cargar_modulo/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_modulo]').val(response[0][0].id_modulo);
            $('[name=tipo_mod]').val(response[0][0].tipo_modulo);
            $('[name=descripcion]').val(response[0][0].descripcion);
            $('[name=ruta]').val(response[0][0].ruta);
            if (response[0][0].tipo_modulo > 1){
                $('[name=ruta]').attr('readonly', true);
                $('#mod').removeClass('oculto');
                $('[name=padre_mod]').html(response[1]);
            }else{
                $('#mod').addClass('oculto');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_modulo(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_modulo';
        msj = 'Módulo registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_modulo';
        msj = 'Módulo editado con exito';
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
                $('#listaModulo').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_modulo(ids){
    baseUrl = 'anular_modulo/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Módulo anulado con exito');
                $('#listaModulo').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-modulo');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function cargarModulos(value){
    $('#mod').addClass('oculto');

    if (value == 2){
        $('[name=ruta]').attr('readonly', true);
        $('#mod').removeClass('oculto');
        baseUrl = 'cargar_modulos';

        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: baseUrl,
            dataType: 'JSON',
            success: function(response){
                $('[name=padre_mod]').html('<option value="0" disabled>Elija una opción</option>' + response);
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }else{
        $('[name=ruta]').removeAttr('readonly');
    }
}