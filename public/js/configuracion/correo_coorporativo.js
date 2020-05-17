$(function() {
    mostrar_correo_coorporativo();
    $('.group-table .mytable tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        // clearForm(form);
        getCorreoCoorporativo(id);
        // console.log(id);

        changeStateButton('historial');
    });
})


function mostrar_correo_coorporativo(){
    var vardataTables = funcDatatables();
    $('#listaCorreoCoorporativo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': 'mostrar_correo_coorporativo/null',
        'columns': [
            {'data': 'id_smtp_authentication'},
            {'data': 'razon_social'},
            {'data': 'email'},
            {'data': 'smtp_server'},
            {
                render: function (data, type, row) {
                    if (row.estado == 1) {
                        status =
                            '<span class="label label-info" title="Activo" >Activo</span>'
                    } else if(row.estado ==7){
                        status = '<span class="label label-default" title="Anulado" >Anulado</span>'

                    }
                    return '<center>' + status + '</center>'
                },
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}]
    });
}


function getCorreoCoorporativo(id){
    baseUrl = 'mostrar_correo_coorporativo/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            $('[name=id_smtp_authentication]').val(response.data[0].id_smtp_authentication);
            $('[name=empresa]').val(response.data[0].id_empresa);
            $('[name=smtp_server]').val(response.data[0].smtp_server);
            $('[name=encryption]').val(response.data[0].encryption);
            $('[name=port]').val(response.data[0].port);
            $('[name=email]').val(response.data[0].email);
            $('[name=password]').val(response.data[0].password);
            $('[name=estado]').val(response.data[0].estado);
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_correo_coorporativo(data, action){
     
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_correo_coorporativo';
        msj = 'Correo registrado con exito';
        method = 'POST';
    }else if(action == 'edition'){
        baseUrl = 'actualizar_correo_coorporativo';
        msj = 'Correo Actualizado con exito';
        method = 'PUT';
    }

    $.ajax({
        type: method,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert(msj);
                $('#listaCorreoCoorporativo').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_correo_coorporativo(ids){
// console.log(ids);
baseUrl = 'anular_correo_coorporativo/'+ids;
$.ajax({
    type: 'delete',
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    url: baseUrl,
    dataType: 'JSON',
    success: function(response){
        if (response > 0){
            alert('Correo anulado con exito');
            $('#listaCorreoCoorporativo').DataTable().ajax.reload();
            changeStateButton('anular');
            clearForm('form-correo_coorporativo');
        }
    }
}).fail( function(jqXHR, textStatus, errorThrown){
    console.log(jqXHR);
    console.log(textStatus);
    console.log(errorThrown);
});
}