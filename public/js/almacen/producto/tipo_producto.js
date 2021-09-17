$(function () {
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaTipo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'ajax': 'listar_tipos',
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
            mostrar_tipo(id);
            changeStateButton('historial');
        }
    });


});

function mostrar_tipo(id) {
    baseUrl = 'mostrar_tipo/' + id;
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

function save_tipo(data, action) {
    if (action == 'register') {
        baseUrl = 'guardar_tipo';
    } else if (action == 'edition') {
        baseUrl = 'actualizar_tipo';
    }
    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            console.log(response.length);
            if (response.length > 0) {
                alert(response);
            } else {
                alert('Categoría registrada con éxito');
                $('#listaTipo').DataTable().ajax.reload();
                changeStateButton('guardar');
                $('#form-tipo').attr('type', 'register');
                changeStateInput('form-tipo', true);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_tipo(ids) {
    baseUrl = 'anular_tipo/' + ids;
    // $.ajax({
    //     type: 'GET',
    //     headers: {'X-CSRF-TOKEN': token},
    //     url: 'revisarTipo/'+ids,
    //     dataType: 'JSON',
    //     success: function(response){
    //         console.log(response);
    //         if (response >= 1){
    //             alert('No es posible anular. \nEl tipo seleccionado está relacionado con '
    //             +response+' categoría(s).');
    //         }
    //         else {
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function (response) {
            console.log(response.length);
            if (response.length > 0) {
                alert(response);
            } else {
                alert('Categoría anulada con éxito');
                $('#listaTipo').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-tipo');
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    //         }
    //     }
    // }).fail( function( jqXHR, textStatus, errorThrown ){
    //     console.log(jqXHR);
    //     console.log(textStatus);
    //     console.log(errorThrown);
    // });

}