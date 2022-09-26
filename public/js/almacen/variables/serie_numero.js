$(function(){
    var form = $('.page-main form[type=register]').attr('id');
    listar_series_numeros();

    $('.group-table .mytable tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        mostrar_serie_numero(id);
        changeStateButton('historial');
    });
});
function listar_series_numeros(){
    var vardataTables = funcDatatables();
    var button_copiar = (array_accesos.find(element => element === 181)?vardataTables[2][0]:[]) ,
        button_excel = (array_accesos.find(element => element === 182)?vardataTables[2][1]:[]) ,
        button_pdf = (array_accesos.find(element => element === 183)?vardataTables[2][2]:[]) ,
        button_imprimir = (array_accesos.find(element => element === 184)?vardataTables[2][3]:[]);
    $('#listaSerieNumero').dataTable({
        'dom': vardataTables[1],
        'buttons': [button_copiar,button_excel,button_pdf,button_imprimir],
        'language' : vardataTables[0],
        'ajax': 'listar_series_numeros',
        'columns': [
            {'data': 'id_serie_numero'},
            {'data': 'tipo_doc'},
            {'data': 'empresa_sede'},
            {'data': 'serie'},
            {'data': 'numero'},
            {'data': 'estado_doc'},
            // {'data': 'nombre_corto'},
        ],
        "order": [[ 3, "desc" ], [ 4, "desc" ]],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
function mostrar_serie_numero(id){
    baseUrl = 'mostrar_serie_numero/'+id;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_serie_numero]').val(response[0].id_serie_numero);
            $('[name=id_tp_documento]').val(response[0].id_tp_documento).trigger('change.select2');
            $('[name=id_sede]').val(response[0].id_sede).trigger('change.select2');
            $('[name=serie]').val(response[0].serie);
            $('[name=numero]').val(response[0].numero);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function save_serie_numero(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_serie_numero';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_serie_numero';
    }
    console.log(data);
    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.length > 0){
                alert(response);
                changeStateButton('guardar');
                $('#form-serie_numero').attr('type', 'register');
                changeStateInput('form-serie_numero', true);
                $('.boton').removeClass('desactiva');
                $('#listaSerieNumero').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_serie_numero(ids){
    console.log('anular'+ids);
    baseUrl = 'anular_serie_numero/'+ids;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Se anuló con éxito');
                $('#listaSerieNumero').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-serie_numero');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function ceros_numero(numero){
    if (numero == 'numero'){
        var num = $('[name=numero]').val();
        $('[name=numero]').val(leftZero(7,num));
    }
}
function ceros_serie(serie){
    if (serie == 'serie'){
        var se = $('[name=serie]').val();
        $('[name=serie]').val(leftZero(4,se));
    }
}
