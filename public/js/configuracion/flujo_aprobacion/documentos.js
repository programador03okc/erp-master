$(function(){
    document.getElementById('btnHistorial').setAttribute('disabled',true);

    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');
    
    $('#listarDocumentos').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        "processing": true,
        "bDestroy": true,
        'ajax': 'listar-documentos',
        'columns': [
            {'data': 'id_estado_doc'},
            {'data': 'id_estado_doc'},
            {'data': 'estado_doc'},
            {'data': 'bootstrap_color'}
         ],
        'order': [
            [1, 'asc']
        ]
    });

    $('.group-table .mytable tbody').on('click', 'tr', function(){

        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        mostrar_documento(id);
        changeStateButton('historial');
        document.getElementById('btnHistorial').setAttribute('disabled',true);

    });
    resizeSide();

});

function mostrar_documento(id){
    baseUrl = 'cargar-documento/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_documento]').val(id);
            $('[name=estado_documento]').val(response[0][0].estado_doc);
            $('[name=color]').val(response[0][0].bootstrap_color);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_documento(data, action){
    // console.log(action);
    console.log(data);
    
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar-documento';
        msj = 'Documento registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'actualizar-documento';
        msj = 'Documento editado con exito';
    }
    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert(msj);
                $('#listarDocumentos').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}

function anular_documento(id){
    var form = $('.page-main form[type=register]').attr('id');

    baseUrl = 'anular-documento/'+id;
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Documento anulado con exito');
                $('#listarDocumentos').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm(form);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}