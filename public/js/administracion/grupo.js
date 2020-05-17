$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaGrupo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        "processing": true,
        "bDestroy": true,
        'ajax': 'listar_grupo',
        'columns': [
            {'data': 'id_grupo'},
            {'data': 'empresa'},
            {'data': 'sede'},
            {'data': 'descripcion'},
            {'data': 'codigo'}
        ],
        'order': [
            [1, 'asc'], [2, 'asc']
        ]
    });
    $('#listaGrupo').DataTable().on("draw", function(){
        resizeSide();
    })

    $('.group-table .mytable tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        mostrar_grupo(id);
        changeStateButton('historial');
    });
    resizeSide();
});

function buscarSede(value, type, seleccion){
    $('[name=sede]').empty();
    if (type == 'llenar'){
        $('[name=sede]').append('<option value="" disabled>Elija una opción</option>');
    }else{
        $('[name=sede]').append('<option value="" disabled selected>Elija una opción</option>');
    }
    baseUrl = 'mostrar_combos_emp/' + value;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (type == 'llenar'){
                Object.keys(response.sedes).forEach(function(key){
                    if (response.sedes[key].id_sede == seleccion){
                        var opt = '<option value="'+response.sedes[key].id_sede+'" selected>'+response.sedes[key].descripcion+'</option>';
                    }else{
                        var opt = '<option value="'+response.sedes[key].id_sede+'">'+response.sedes[key].descripcion+'</option>';
                    }
                    $('[name=sede]').append(opt);
                });
            }else{
                Object.keys(response.sedes).forEach(function(key){
                    var opt = '<option value="'+response.sedes[key].id_sede+'">'+response.sedes[key].descripcion+'</option>';
                    $('[name=sede]').append(opt);
                });
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_grupo(id){
    baseUrl = 'cargar_grupo/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_grupo]').val(response[0].id_grupo);
            $('[name=empresa]').val(response[0].id_empresa);
            buscarSede(response[0].id_empresa, 'llenar', response[0].id_sede);
            $('[name=sede]').text(response[0].id_sede);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=codigo]').val(response[0].codigo);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_grupo(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_grupo';
        msj = 'Grupo registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_grupo';
        msj = 'Grupo editado con exito';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            if (response == 'ok'){
                alert(msj);
                $('#listaGrupo').DataTable().ajax.reload();
                changeStateButton('guardar');
            }else if(response == 'exist'){
                alert('Error, Ya existe un grupo con esa descripción');
                changeStateButton('historial');
            }else if(response == 'null'){
                alert('Error, No se grabaron los datos, intente más tarde');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_grupo(ids){
    baseUrl = 'anular_grupo/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Grupo anulado con exito');
                $('#listaGrupo').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-grupo');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}