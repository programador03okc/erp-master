$(function () {
    var vardataTables = funcDatatables();

    $('#listaSubCategorias').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'ajax': 'listarSubCategorias',
        'columns': [
            { 'data': 'id_categoria' },
            { 'data': 'clasificacion_descripcion' },
            { 'data': 'tipo_descripcion' },
            { 'data': 'descripcion' }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });

    $('.group-table .mytable tbody').on('click', 'tr', function () {
        var status = $("#form-categoria").attr('type');
        var form = $('.page-main form[type=register]').attr('id');

        if (status !== "edition") {
            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            var id = $(this)[0].firstChild.innerHTML;
            clearForm(form);
            mostrarSubCategoria(id);
            changeStateButton('historial');
        }
    });

});

function mostrarSubCategoria(id) {
    baseUrl = 'mostrarSubCategoria/' + id;
    $.ajax({
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': token },
        url: baseUrl,
        dataType: 'JSON',
        success: function (response) {
            $('[name=id_clasificacion]').val(response[0].id_clasificacion);
            $('[name=id_tipo_producto]').val(response[0].id_tipo_producto);
            $('[name=id_categoria]').val(response[0].id_categoria);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[id=estado] label').text('');
            $('[id=estado] label').append((response[0].estado == 1 ? 'Activo' : 'Inactivo'));
            $('[id=fecha_registro] label').text('');
            $('[id=fecha_registro] label').append(formatDateHour(response[0].fecha_registro));
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardarSubCategoria(data, action) {
    if (action == 'register') {
        baseUrl = 'guardarSubCategoria';
    } else if (action == 'edition') {
        baseUrl = 'actualizarSubCategoria';
    }
    $.ajax({
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': token },
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
<<<<<<< HEAD:public/js/almacen/producto/categoria_producto.js
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });

            if (response.status==200){ 
                $('#listaCategoria').DataTable().ajax.reload();
                changeStateButton('guardar');
                $('#form-categoria').attr('type', 'register');
                changeStateInput('form-categoria', true);
=======
            console.log(response.length);
            if (response.length > 0) {
                alert(response);
            } else {
                alert('SubCategoría registrado con éxito');
                $('#listaSubCategorias').DataTable().ajax.reload();
                changeStateButton('guardar');
                $('#form-subCategoria').attr('type', 'register');
                changeStateInput('form-subCategoria', true);
                $('[name=id_tipo_producto]').attr('disabled', true);
>>>>>>> 0afa8485695289e3277a2ac8f01bf5e23e49492e:public/js/almacen/producto/subCategoria.js
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularSubCategoria(ids) {

    baseUrl = 'anularSubCategoria/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response) {
            console.log(response);
<<<<<<< HEAD:public/js/almacen/producto/categoria_producto.js
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
            if (response.status==200) {
                $('#listaCategoria').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-categoria');
=======
            if (response >= 1) {
                alert('No es posible anular. \nLa subcategoria seleccionada está relacionada con '
                    + response + ' marca(s).');
            }
            else {
                $.ajax({
                    type: 'GET',
                    headers: { 'X-CSRF-TOKEN': token },
                    url: baseUrl,
                    dataType: 'JSON',
                    success: function (response) {
                        console.log(response);
                        if (response > 0) {
                            alert('SubCategoría anulada con éxito');
                            $('#listaSubCategorias').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-categoria');
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
>>>>>>> 0afa8485695289e3277a2ac8f01bf5e23e49492e:public/js/almacen/producto/subCategoria.js
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}

/*$("[name=id_clasificacion]").on('change', function () {
    var id_clasificacion = $(this).val();
    console.log(id_clasificacion);
    $('[name=id_tipo_producto]').html('');
    $.ajax({
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': token },
        url: 'mostrarCategoriasPorClasificacion/' + id_clasificacion,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            if (response.length > 0) {
                $('[name=id_tipo_producto]').html('');
                html = '<option value="0" >Elija una opción</option>';
                response.forEach(element => {
                    html += `<option value="${element.id_tipo_producto}" >${element.descripcion}</option>`;
                });
                $('[name=id_tipo_producto]').html(html);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    })
    });;*/
