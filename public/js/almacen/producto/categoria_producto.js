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
                alert('Categoria registrado con exito');
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
                alert('No es posible anular. \nLa categoria seleccionada está relacionada con '
                +response+' subcategoría(s).');
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
                            alert('Categoria anulada con exito');
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
