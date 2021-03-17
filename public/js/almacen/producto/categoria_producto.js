$(function(){
    var vardataTables = funcDatatables();

    $('#listaCategoria').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_categorias',
        'columns': [
            {'data': 'id_categoria'},
            {'data': 'codigo'},
            {'data': 'tipo_descripcion'},
            {'data': 'descripcion'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    $('.group-table .mytable tbody').on('click', 'tr', function(){
        var status = $("#form-categoria").attr('type');
        var form = $('.page-main form[type=register]').attr('id');

        if (status !== "edition"){
            if ($(this).hasClass('eventClick')){
                $(this).removeClass('eventClick');
            } else {
                $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            var id = $(this)[0].firstChild.innerHTML;
            clearForm(form);
            mostrar_categoria(id);
            changeStateButton('historial');
        }
    });

    
});

function mostrar_categoria(id){
    baseUrl = 'mostrar_categoria/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_categoria]').val(response[0].id_categoria);
            $('[name=codigo]').val(response[0].codigo);
            $('[name=id_tipo_producto]').val(response[0].id_tipo_producto);
            $('[name=descripcion]').val(response[0].descripcion);
            // $('[name=estado]').val(response[0].estado);
            $('[id=estado] label').text('');
            $('[id=estado] label').append((response[0].estado == 1 ? 'Activo' : 'Inactivo'));
            $('[id=fecha_registro] label').text('');
            $('[id=fecha_registro] label').append(formatDateHour(response[0].fecha_registro));
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_categoria(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_categoria';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_categoria';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            console.log(response.length);
            if (response.length > 0){
                alert(response);
            } else {
                alert('SubCategoría registrado con éxito');
                $('#listaCategoria').DataTable().ajax.reload();
                changeStateButton('guardar');
                $('#form-categoria').attr('type', 'register');
                changeStateInput('form-categoria', true);
                $('[name=id_tipo_producto]').attr('disabled',true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_categoria(ids){
    baseUrl = 'anular_categoria/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'revisarCat/'+ids,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response >= 1){
                alert('No es posible anular. \nLa subcategoria seleccionada está relacionada con '
                +response+' marca(s).');
            }
            else {
                $.ajax({
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    url: baseUrl,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('SubCategoría anulada con éxito');
                            $('#listaCategoria').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-categoria');
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
