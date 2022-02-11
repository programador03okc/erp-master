$(function () {
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaClasificacion').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'ajax': 'listarClasificaciones',
        'columns': [
            { 'data': 'id_clasificacion' },
            { 'data': 'descripcion' },
            {
                'render':
                    function (data, type, row) {
                        return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                    }
            },
            {
                'render':
                    function (data, type, row) {
                        return (formatDate(row['fecha_registro']));
                    }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });

    $('.group-table .mytable tbody').on('click', 'tr', function () {
        var status = $("#form-clasificacion").attr('type');
        if (status !== "edition") {
            if ($(this).hasClass('eventClick')) {
                $(this).removeClass('eventClick');
            } else {
                $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            var id = $(this)[0].firstChild.innerHTML;
            clearForm(form);
            mostrarClasificacion(id);
            changeStateButton('historial');
        }
    });

});

function mostrarClasificacion(id) {
    baseUrl = 'mostrarClasificacion/' + id;
    $.ajax({
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': token },
        url: baseUrl,
        dataType: 'JSON',
        success: function (response) {
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

function guardarClasificacion(data, action) {
    if (action == 'register') {
        baseUrl = 'guardarClasificacion';
    } else if (action == 'edition') {
        baseUrl = 'actualizarClasificacion';
    }
    $.ajax({
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': token },
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response.length > 0) {
                alert(response);
            } else {
                alert('Clasificacion registrado con exito');
                $('#listaClasificacion').DataTable().ajax.reload();
                changeStateButton('guardar');
                $('#form-clasificacion').attr('type', 'register');
                changeStateInput('form-clasificacion', true);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularClasificacion(ids) {
    baseUrl = 'anularClasificacion/' + ids;
    $.ajax({
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': token },
        url: 'revisarClasificacion/' + ids,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response >= 1) {
                alert('No es posible anular. \nLa clasificacion seleccionada está relacionada con '
                    + response + ' producto(s).');
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
                            alert('Clasificacion anulada con exito');
                            $('#listaClasificacion').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-clasificacion');
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}