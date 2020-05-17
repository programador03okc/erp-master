$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaTiposDocsAlmacen').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_tp_docs',
        'columns': [
            {'data': 'id_tp_doc_almacen'},
            {'data': 'cod_sunat'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDateHour(row['fecha_registro']));
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}]
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
        mostrar_tipo_doc(id);
        changeStateButton('historial');
    });
    
});

function mostrar_tipo_doc(id){
    baseUrl = 'mostrar_tp_doc/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_tp_doc_almacen]').val(response[0].id_tp_doc_almacen);
            $('[name=id_tp_doc]').val(response[0].id_tp_doc).trigger('change.select2');
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=tipo]').val(response[0].tipo);
            $('[name=abreviatura]').val(response[0].abreviatura);
            
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_tipo_doc(data, action){
    var id_usuario = auth_user.id_usuario;
    $('[name=usuario]').val(id_usuario);
    if (action == 'register'){
        baseUrl = 'guardar_tp_doc';
    } else if (action == 'edition'){
        baseUrl = 'update_tp_doc';
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
                alert('Tipo Documento registrado con éxito');
                $('#listaTiposDocsAlmacen').DataTable().ajax.reload();
                changeStateButton('guardar');
                clearForm('form-tipo_doc');
                $('#form-tipo_doc').attr('type', 'register');
                changeStateInput('form-tipo_doc', true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_tipo_doc(ids){
    baseUrl = 'anular_tp_doc/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('tipo_doc anulado con exito');
                $('#listaTiposDocsAlmacen').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-tipo_doc');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
