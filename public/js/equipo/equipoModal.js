$(function(){
    listar_equipos();
    var form = $('.page-main form[type=register]').attr('id');
    $('#listaEquipos tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaEquipos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_equipo').text(id);
        console.log(id);
        clearForm(form);
        mostrar_equipo(id);
        changeStateButton('historial');
    });
});
function equipoModal(){
    $('#modal-equipo').modal({
        show: true
    });
    clearDataTable();
    listar_equipos();
}
function selectEquipo(){
    var myId = $('.modal-footer #id_equipo').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "equipo"){
        clearForm(form);
        mostrar_equipo(myId);
        changeStateButton('historial');
        console.log($(":file").filestyle('disabled'));
    }
    // else if (page == "guia_compra"){
    //     guardar_guia_detalle(myId,unid);
    // }
    $('#modal-equipo').modal('hide');
}
function listar_equipos(){
    var vardataTables = funcDatatables();
    $('#listaEquipos').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_equipos',
        'columns': [
            {'data': 'id_equipo'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
        ]
    });
}
function mostrar_equipo(id){
    baseUrl = 'mostrar_equipo/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_equipo]').val(response[0].id_equipo);
            $('[name=id_categoria]').val(response[0].id_categoria).trigger('change.select2');
            $('[name=propietario]').val(response[0].propietario).trigger('change.select2');
            $('[name=codigo]').val(response[0].codigo);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=cod_tarj_propiedad]').val(response[0].cod_tarj_propiedad);
            $('[name=placa]').val(response[0].placa);
            $('[name=serie]').val(response[0].serie);
            $('[name=marca]').val(response[0].marca);
            $('[name=modelo]').val(response[0].modelo);
            $('[name=motor]').val(response[0].motor);
            $('[name=anio_fabricacion]').val(response[0].anio_fabricacion);
            $('[name=caracteristicas_adic]').val(response[0].caracteristicas_adic);
            // $('[name=estado]').val(response[0].estado);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}