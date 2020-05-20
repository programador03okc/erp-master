$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaSede').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        "processing": true,
        "bDestroy": true,
        'ajax': 'listar_sede',
        'columns': [
            {'data': 'id_sede'},
            {'data': 'razon_social'},
            {'data': 'descripcion'}
        ],
        'order': [
            [1, 'asc']
        ]
    });
    $('#listaSede').DataTable().on("draw", function(){
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
        mostrar_sede(id);
        changeStateButton('historial');
    });
    resizeSide();
});

function buscarCodigo(value){
    baseUrl = 'buscar_codigo_empresa/' + value + '/' + 'return';
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#abrev').text(response);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_sede(id){
    baseUrl = 'cargar_sede/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            var nom = response[0].descripcion;
            var desc = nom.split('-');
            $('[name=id_sede]').val(response[0].id_sede);
            $('[name=empresa]').val(response[0].id_empresa);
            $('#abrev').text(response[0].abrev);
            $('[name=descripcion]').val(desc[1]);
            $('[name=direccion]').val(response[0].direccion);
            $('[name=abt]').val(response[0].codigo);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_sede(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_sede';
        msj = 'Sede registrada con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_sede';
        msj = 'Sede editada con exito';
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
                $('#listaSede').DataTable().ajax.reload();
                changeStateButton('guardar');
            }else if(response == 'exist'){
                alert('Error, Ya existe una sede con esa descripción');
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

function anular_sede(ids){
    baseUrl = 'anular_sede/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Sede anulada con exito');
                $('#listaSede').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-sede');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}