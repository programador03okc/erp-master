$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaTipoCombustible').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_tp_combustibles',
        'columns': [
            {'data': 'id_tp_combustible'},
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

    $('#listaTipoCombustible tbody').on('click', 'tr', function(){
        var status = $("#form-tp_combustible").attr('type');
        if (status !== "edition"){
            if ($(this).hasClass('eventClick')){
                $(this).removeClass('eventClick');
            } else {
                $('#listaTipoCombustible').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            var id = $(this)[0].firstChild.innerHTML;
            clearForm(form);
            mostrar_tp_combustible(id);
            changeStateButton('historial');
        }
    });
});

function mostrar_tp_combustible(id){
    baseUrl = 'mostrar_tp_combustible/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_tp_combustible]').val(response[0].id_tp_combustible);
            $('[name=codigo]').val(response[0].codigo);
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

function save_tp_combustible(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_tp_combustible';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_tp_combustible';
    }
    console.log(baseUrl);
    console.log(data);
    var des = $('[name=descripcion]').val().trim();
    var cod = $('[name=codigo]').val().trim();
    if (des.length > 0){
        if (cod.length > 0){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': token},
                url: baseUrl,
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response.length > 0){
                        alert(response);
                    } else {
                        alert('Tipo registrado con exito');
                        $('#listaTipoCombustible').DataTable().ajax.reload();
                        changeStateButton('guardar');
                        $('#form-tp_combustible').attr('type', 'register');
                        changeStateInput('form-tp_combustible', true);
                    }
                }
            }).fail( function( jqXHR, textSequi_tatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(erro);
            });
        } else {
            alert('Es necesario que ingrese una abreviatura!');    
        }
    } else {
        alert('Debe ingresar una descripción!');
    }
}

function anular_tp_combustible(ids){
    baseUrl = 'anular_tp_combustible/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.length > 0){
                alert(response);
            } else {
                alert('Tipo anulado con exito');
                $('#listaTipoCombustible').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-tp_combustible');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStat);
        console.log(errorThrown);
    });
    
}