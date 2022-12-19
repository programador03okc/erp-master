$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');
<<<<<<< HEAD

    var button_copiar = (array_accesos.find(element => element === 203)?vardataTables[2][0]:[]) ,
        button_excel = (array_accesos.find(element => element === 204)?vardataTables[2][1]:[]) ,
        button_pdf = (array_accesos.find(element => element === 205)?vardataTables[2][2]:[]) ,
        button_imprimir = (array_accesos.find(element => element === 206)?vardataTables[2][3]:[]);

    $('#listaTiposDocsAlmacen').dataTable({
        'dom': vardataTables[1],
        'buttons': [button_copiar,button_excel,button_pdf,button_imprimir],
=======
    const button_copiar= (array_accesos.find(element => element === 203)?vardataTables[2][0]:[]),
    button_descargar_excel= (array_accesos.find(element => element === 204)?vardataTables[2][1]:[]),
    button_descargar_pdf= (array_accesos.find(element => element === 205)?vardataTables[2][2]:[]),
    button_imprimir= (array_accesos.find(element => element === 206)?vardataTables[2][3]:[]);
    $('#listaTiposDocsAlmacen').dataTable({
        'dom': vardataTables[1],
        'buttons': [button_copiar,button_descargar_excel,button_descargar_pdf,button_imprimir],
>>>>>>> develop
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
