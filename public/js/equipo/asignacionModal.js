$(function(){
    listar_asignaciones();
    // var form = $('.page-main form[type=register]').attr('id');
    $('#listaAsignaciones tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaAsignaciones').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_asignacion').text(id);
        console.log(id);
        // clearForm(form);
        // changeStateButton('historial');
    });
});
function asignacionModal(){
    $('#modal-asignaciones').modal({
        show: true
    });
    clearDataTable();
    listar_asignaciones();
}
function selectAsignacion(){
    var myId = $('.modal-footer #id_asignacion').text();
    mostrar_asignacion(myId);
    $('#modal-asignaciones').modal('hide');
}
function listar_asignaciones(){
    var vardataTables = funcDatatables();
    $('#listaAsignaciones').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        ajax: { url:'listar_asignaciones',
                dataSrc:''},
        'columns': [
            {'data': 'id_asignacion'},
            {'data': 'codigo'},
            {'data': 'area_descripcion'},
            {'data': 'equipo_descripcion'},
            {'data': 'observaciones'},
            {'data': 'nombre_trabajador'},
            {'data': 'fecha_inicio'},
            {'data': 'fecha_fin'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
function mostrar_asignacion(id){
    baseUrl = 'mostrar_asignacion/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_asignacion]').val(response[0].id_asignacion);
            $('[name=equipo_descripcion]').val(response[0].codigo+' - '+response[0].equipo_descripcion);
            $('[name=area_descripcion]').val(response[0].area_descripcion);
            $('[name=fecha_asignacion]').val(response[0].fecha_asignacion);
            $('[name=fecha_inicio]').val(response[0].fecha_inicio);
            $('[name=fecha_fin]').val(response[0].fecha_fin);
            $('[name=trabajador]').val(response[0].nombre_trabajador);
            $('[name=id_trabajador]').val(response[0].id_trabajador);
            $('[name=id_equipo]').val(response[0].id_equipo);
            $('[name=id_solicitud]').val(response[0].id_solicitud);
            listar_controles(response[0].id_asignacion);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}