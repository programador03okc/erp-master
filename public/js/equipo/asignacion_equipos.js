$(function(){
    $('#listaEquipos tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaEquipos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        var cod = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;
        $('.modal-footer #id_equi').text(id);
        $('.modal-footer #cod_equi').text(cod);
        $('.modal-footer #des_equi').text(des);
        console.log('id_equipo'+id);
    });
});
function listar_equipos(id_categoria){
    var vardataTables = funcDatatables();
    $('#listaEquipos').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        // pageLength : 5,
        'ajax': 'equipos_disponibles/'+id_categoria,
        'columns': [
            {'data': 'id_equipo'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'fechas_uso'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
function selectEquipo(){
    var id = $('.modal-footer #id_equi').text();
    var cod = $('.modal-footer #cod_equi').text();
    var des = $('.modal-footer #des_equi').text();
    $('#modal-asignacion_equipos').modal('hide');
    asignacionModal(id, cod, des);
}