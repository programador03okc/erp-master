$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

     $('#listaPeriodo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_periodo',
        'columns': [
            {'data': 'id_asistencia'},
            {'data': 'descripcion'},
            {'data': 'fecha_inicio'},
            {'data': 'fecha_fin'},
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
        mostrar_periodo(id);
        changeStateButton('historial');
    });

    $('[name=id_tipo_asistencia]').on('change', function(){
        var value = $(this).val();
        $('[name=descripcion]').val('');
        if(value == 1){
            $('[name=descripcion]').attr('disabled', true);
            $('[name=fecha_inicio]').focus();
        }else{
            $('[name=descripcion]').attr('disabled', false);
            $('[name=descripcion]').focus();
        }
    });
    resizeSide();
});

function mostrar_periodo(id){
    baseUrl = 'cargar_periodo/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_asistencia]').val(response[0].id_asistencia);
            $('[name=id_tipo_asistencia]').val(response[0].id_tipo_asistencia);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=fecha_inicio]').val(response[0].fecha_inicio);
            $('[name=fecha_fin]').val(response[0].fecha_fin);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_periodo(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_periodo';
        msj = 'Periodo registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_periodo';
        msj = 'Periodo editado con exito';
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
                $('#listaPeriodo').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}