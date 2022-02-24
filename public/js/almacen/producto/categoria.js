$(function () {
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaCategorias').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'ajax': 'listarCategorias',
        'columns': [
            { 'data': 'id_tipo_producto' },
            { 'data': 'clasificacion_descripcion' },
            { 'data': 'descripcion' }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }]
    });

    $('.group-table .mytable tbody').on('click', 'tr', function () {
        var status = $("#form-tipo").attr('type');
        if (status !== "edition") {
            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            var id = $(this)[0].firstChild.innerHTML;
            clearForm(form);
            mostrarCategoria(id);
            changeStateButton('historial');
        }
    });


});

function mostrarCategoria(id) {
    baseUrl = 'mostrarCategoria/' + id;
    $.ajax({
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': token },
        url: baseUrl,
        dataType: 'JSON',
        success: function (response) {
            $('[name=id_tipo_producto]').val(response[0].id_tipo_producto);
            $('[name=id_clasificacion]').val(response[0].id_clasificacion);
            $('[name=descripcion]').val(response[0].descripcion);
            // $('[name=estado]').val(response[0].estado);
            $('[id=fecha_registro] label').text('');
            $('[id=fecha_registro] label').append(formatDateHour(response[0].fecha_registro));
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardarCategoria(data, action) {
    if (action == 'register') {
        baseUrl = 'guardarCategoria';
    } else if (action == 'edition') {
        baseUrl = 'actualizarCategoria';
    }
    $.ajax({
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': token },
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });

            if (response.status == 200) {
                $('#listaCategorias').DataTable().ajax.reload();
                changeStateButton('guardar');
                $('#form-categoria').attr('type', 'register');
                changeStateInput('form-categoria', true);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularCategoria(ids) {
    baseUrl = 'anularCategoria/' + ids;
    $.ajax({
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': token },
        url: baseUrl,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
            if (response.status == 200) {
                $('#listaCategorias').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-tipo');
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}