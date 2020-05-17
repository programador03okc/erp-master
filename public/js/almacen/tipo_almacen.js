$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaTipoAlmacen').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_tipo_almacen',
        'columns': [
            {'data': 'id_tipo_almacen'},
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
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        mostrar_tipo_almacen(id);
        changeStateButton('historial');
    });
});

function mostrar_tipo_almacen(id){
    baseUrl = 'cargar_tipo_almacen/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_tipo_almacen]').val(response[0].id_tipo_almacen);
            $('[name=descripcion]').val(response[0].descripcion);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_tipo_almacen(data, action){
    console.log(data);
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_tipo_almacen';
        msj = 'Tipo Almacén registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_tipo_almacen';
        msj = 'Tipo Almacén editado con exito';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert(msj);
                $('#listaTipoAlmacen').DataTable().ajax.reload();
                changeStateButton('guardar');
                $('#form-tipo_almacen').attr('type', 'register');
                changeStateInput('form-tipo_almacen', true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_tipo_almacen(ids){
    baseUrl = 'anular_tipo_almacen/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Almacén anulado con exito');
                $('#listaTipoAlmacen').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-tipo_almacen');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}