$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaEquiCat').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_equi_cats',
        'columns': [
            {'data': 'id_categoria'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'tipo_descripcion'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            },
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_registro']));
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    $('.group-table .mytable tbody').on('click', 'tr', function(){
        var status = $("#form-equi_cat").attr('type');
        if (status !== "edition"){
            if ($(this).hasClass('eventClick')){
                $(this).removeClass('eventClick');
            } else {
                $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            var id = $(this)[0].firstChild.innerHTML;
            clearForm(form);
            mostrar_cat(id);
            changeStateButton('historial');
        }
    });
});

function mostrar_cat(id){
    baseUrl = 'mostrar_equi_cat/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_categoria]').val(response[0].id_categoria);
            $('[name=codigo]').val(response[0].codigo);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=id_tipo]').val(response[0].id_tipo);
            // $('[name=estado]').val(response[0].estado);
            $('[id=fecha_registro] label').text('');
            $('[id=fecha_registro] label').append(formatDateHour(response[0].fecha_registro));
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_equi_cat(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_equi_cat';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_equi_cat';
    }
    var des = $('[name=descripcion]').val().trim();
    var tp = $('[name=id_tipo]').val();
    if (des.length > 0){
        if (tp > 0){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': token},
                url: baseUrl,
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response.length);
                    if (response.length > 0){
                        alert(response);
                    } else {
                        alert('Tipo registrado con exito');
                        $('#listaEquiCat').DataTable().ajax.reload();
                        changeStateButton('guardar');
                        $('#form-equi_cat').attr('type', 'register');
                        changeStateInput('form-equi_cat', true);
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        } else {
            alert('Es necesario que seleccione un tipo.');
        }
    } else {
        alert('Debe ingresar una descripción!');
    }
}

function anular_equi_cat(ids){
    baseUrl = 'anular_equi_cat/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response.length);
            if (response.length > 0){
                alert(response);
            } else {
                alert('Tipo anulado con exito');
                $('#listaEquiCat').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-equi_cat');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}