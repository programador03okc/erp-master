$(function() {
    mostrar_configuracion_socket();
    $('.group-table .mytable tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        var modo = $(this)[0].childNodes[1].innerHTML;
        var host = $(this)[0].childNodes[2].innerHTML;
        var activado = $(this)[0].childNodes[3].innerHTML;
                
        document.querySelector("form[id='form-configuracion_socket'] input[name='id']").value= id;
        document.querySelector("form[id='form-configuracion_socket'] input[name='modo']").value= modo;
        document.querySelector("form[id='form-configuracion_socket'] input[name='host']").value = host;
        document.querySelector("form[id='form-configuracion_socket'] select[name='activado']").value= activado;
        

        changeStateButton('historial');
    });
})

function mostrar_configuracion_socket(){
    var vardataTables = funcDatatables();
    $('#listaConfiguracionSocket').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': 'socket_setting/all',
        'columns': [
            {'data': 'id'},
            {'data': 'modo'},
            {'data': 'host'},
            {'data': 'activado'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}]
    });
}


function save_configuracion_socket(data, action){
     
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_configuracion_socket';
        msj = 'configuración socket registrado con exito';
        method = 'POST';
    }else if(action == 'edition'){
        baseUrl = 'actualizar_configuracion_socket';
        msj = 'configuración socket Actualizado con exito';
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
                $('#listaConfiguracionSocket').DataTable().ajax.reload();
                changeStateButton('guardar');
            }else{
                alert('hubo un problema,no se pudo realizar la acción');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}


function anular_configuracion_socket(ids){
    // console.log(ids);
    baseUrl = 'anular_configuracion_socket/'+ids;
    $.ajax({
        type: 'delete',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('la configuracion socket fue anulado con exito');
                $('#listaConfiguracionSocket').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-configuracion_socket');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    }