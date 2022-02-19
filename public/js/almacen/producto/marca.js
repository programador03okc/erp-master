$(function(){
    listarSubCategorias();
    /* Seleccionar valor del DataTable */
    $('.group-table .mytable tbody').on('click', 'tr', function(){
        var status = $("#form-subcategoria").attr('type');
        if (status !== "edition"){
            if ($(this).hasClass('eventClick')){
                $(this).removeClass('eventClick');
            } else {
                $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
                $(this).addClass('eventClick');
            }
            var id = $(this)[0].firstChild.innerHTML;
            clearForm('form-subcategoria');
            mostrarMarca(id);
            changeStateButton('historial');
        }
    });
});
function listarSubCategorias(){
    var vardataTables = funcDatatables();
    $('#listaSubCategoria').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        "bDestroy": true,
        'ajax': 'listarMarcas',
        'columns': [
            {'data': 'id_subcategoria'},
            // {'data': 'codigo'},
            {'data': 'descripcion'},
            // {'data': 'estado'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
function mostrarMarca(id){
    baseUrl = 'mostrarMarca/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_subcategoria]').val(response[0].id_subcategoria);
            // $('[name=codigo]').val(response[0].codigo);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=estado]').val(response[0].estado);
            $('#fecha_registro label').text('');
            $('#fecha_registro label').append(formatDateHour(response[0].fecha_registro));
            $('#nombre_corto label').text('');
            $('#nombre_corto label').append(response[0].nombre_corto);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardarMarca(data, action){
    if (action == 'register'){
        baseUrl = 'guardarMarca';
    } else if (action == 'edition'){
        baseUrl = 'actualizarMarca';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });

            if (response.status==200){ 
                $('#listaSubCategoria').DataTable().ajax.reload();
                changeStateButton('guardar');
                $('#form-subcategoria').attr('type', 'register');
                changeStateInput('form-subcategoria', true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularMarca(ids){
    baseUrl = 'anularMarca/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
            if (response.status==200) {
                $('#listaSubCategoria').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-subcategoria');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}