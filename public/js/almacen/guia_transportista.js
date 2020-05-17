$(function(){
    $('#listaTransportista tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaTransportista').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        // clearForm(form);
        mostrar_transportista(myId);
        changeStateButton('historial');
    }); 
});

function listar_transportista(guia){
    console.log('guia'+guia);
    var vardataTables = funcDatatables();
    $('#listaTransportista').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_guia_transportista/'+guia,
        'columns': [
            {'data': 'id_guia_com_tra'},
            {'data': 'razon_social'},
            {'render':
                function (data, type, row){
                    return (row['serie']+'-'+row['numero']);
                }
            },
            {'data': 'placa'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            }
        ]
    });
}
function mostrar_transportista(id){
    baseUrl = 'mostrar_transportista/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response[0]);
            $('[name=id_guia_com_tra]').val(response[0].id_guia_com_tra);
            $('[name=id_proveedor_tra]').val(response[0].id_proveedor).trigger('change.select2');
            $('[name=serie_tra]').val(response[0].serie);
            $('[name=numero_tra]').val(response[0].numero);
            $('[name=fecha_emision_tra]').val(response[0].fecha_emision);
            $('[name=placa]').val(response[0].placa);
            $('[name=referencia]').val(response[0].referencia);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_transportista(data, action){
    console.log('data:'+data);
    if (action == 'register'){
        baseUrl = 'guardar_transportista';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_transportista';
    }
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log('response'+response);
            if (response > 0){
                alert('Transportista registrado con éxito');
                $('#listaTransportista').DataTable().ajax.reload();
                changeStateButton('guardar');
                clearForm('form-transportista');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_transportista(ids){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'anular_transportista/'+ids,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Transportista anulado con éxito');
                $('#listaTransportista').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-transportista');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}