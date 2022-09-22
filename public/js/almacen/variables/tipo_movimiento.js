$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');
    var button_copiar = (array_accesos.find(element => element === 192)?vardataTables[2][0]:[]) ,
        button_excel = (array_accesos.find(element => element === 193)?vardataTables[2][1]:[]) ,
        button_pdf = (array_accesos.find(element => element === 194)?vardataTables[2][2]:[]) ,
        button_imprimir = (array_accesos.find(element => element === 195)?vardataTables[2][3]:[]);

    $('#listaTipoMov').dataTable({
        'dom': vardataTables[1],
        'buttons': [button_copiar,button_excel,button_pdf,button_imprimir],
        'language' : vardataTables[0],
        'ajax': 'listar_tipoMov',
        'columns': [
            {'data': 'id_operacion'},
            {'data': 'cod_sunat'},
            {'render':
                function (data, type, row){
                    return ( (row['tipo'] == 1) ? 'Ingreso' :
                             ((row['tipo'] == 2) ? 'Salida' : ((row['tipo'] == 3) ? 'Ingreso/Salida':'')));
                }
            },
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            },
            // {'render':
            //     function (data, type, row){
            //         return (formatDate(row['fecha_registro']));
            //     }
            // }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    $('.group-table .mytable tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaTipoMov').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        mostrar_tipoMov(id);
        changeStateButton('historial');
    });


});

function mostrar_tipoMov(id){
    baseUrl = 'mostrar_tipoMov/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_operacion]').val(response[0].id_operacion);
            $('[name=tipo]').val(response[0].tipo);
            $('[name=cod_sunat]').val(response[0].cod_sunat);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=estado]').val(response[0].estado);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_tipoMov(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_tipoMov';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_tipoMov';
    }
    console.log(data);
    console.log(baseUrl);
    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Tipo de Operación registrado con exito');
                $('#listaTipoMov').DataTable().ajax.reload();
                clearForm('form-tipo_movimiento');
                changeStateButton('guardar');
                $('#form-tipo_movimiento').attr('type', 'register');
                changeStateInput('form-tipo_movimiento', true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_tipoMov(ids){
    baseUrl = 'anular_tipoMov/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Tipo de Operación anulado con exito');
                $('#listaTipoMov').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-tipoMov');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
