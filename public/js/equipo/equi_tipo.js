$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaEquiTipo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_equi_tipos',
        'columns': [
            {'data': 'id_tipo'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
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
        var status = $("#form-equi_tipo").attr('type');
        if (status !== "edition"){
            if ($(this).hasClass('eventClick')){
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

function mostrar_tipo(id){
    baseUrl = 'mostrar_equi_tipo/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_tipo]').val(response[0].id_tipo);
            // $('[name=codigo]').val(response[0].codigo);
            $('[name=descripcion]').val(response[0].descripcion);
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

function save_equi_tipo(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_equi_tipo';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_equi_tipo';
    }
    var des = $('[name=descripcion]').val().trim();
    console.log(des);
    if (des.length > 0){
        $.ajax({
            type: 'POST',
            // headers: {'X-CSRF-TOKEN': token},
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response.length > 0){
                    alert(response);
                } else {
                    alert('Tipo registrado con exito');
                    $('#listaEquiTipo').DataTable().ajax.reload();
                    changeStateButton('guardar');
                    $('#form-equi_tipo').attr('type', 'register');
                    changeStateInput('form-equi_tipo', true);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('Debe ingresar una descripción!');
    }
}

function anular_equi_tipo(ids){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'anular_equi_tipo/'+ids,
        dataType: 'JSON',
        success: function(response){
            console.log(response.length);
            if (response.length > 0){
                alert(response);
            } else {
                alert('Se anuló el tipo con éxito!');
                $('#listaEquiTipo').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-equi_tipo');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });            
}