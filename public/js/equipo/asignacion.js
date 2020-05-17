$(function(){
    listar_solicitudes();
    // listar_equipos();
    $('#listaSolicitudes tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaSolicitudes').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        var trab = $(this)[0].childNodes[2].innerHTML;
        var area = $(this)[0].childNodes[3].innerHTML;
        var fini = $(this)[0].childNodes[5].innerHTML;
        var ffin = $(this)[0].childNodes[6].innerHTML;
        $('[name=id_solicitud]').val(id);
        $('[name=area_solicitud]').val(area);
        $('[name=trabajador]').val(trab);
        $('[name=fecha_inicio]').val(fini);
        $('[name=fecha_fin]').val(ffin);
        console.log('id'+id);
    });
});
function listar_solicitudes(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaSolicitudes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        pageLength : 5,
        ajax: { url:'listar_solicitudes_aprobadas',
                dataSrc:''},
        'columns': [
            {'data': 'id_solicitud'},
            {'data': 'fecha_solicitud'},
            {'data': 'nombre_trabajador'},
            {'data': 'area_descripcion'},
            {'data': 'des_categoria'},
            {'data': 'asignaciones_pendientes'},
            {'data': 'fecha_inicio'},
            {'data': 'fecha_fin'},
            {'defaultContent': 
                '<button type="button" class="asignar btn btn-success boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Asignar" >'+
                '<i class="fas fa-share"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    asignar("#listaSolicitudes tbody", tabla);

}
function asignar(tbody, tabla){
    console.log("asignar");
    $(tbody).on("click","button.asignar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        $('[name=id_solicitud]').val(data.id_solicitud);
        $('[name=area_solicitud]').val(data.area_solicitud);
        $('[name=trabajador]').val(data.trabajador);
        $('[name=fecha_inicio]').val(data.fecha_inicio);
        $('[name=fecha_fin]').val(data.fecha_fin);
        console.log(data);
        $('#modal-asignacion_equipos').modal({
            show:true
        });
        listar_equipos(data.id_categoria);
        // asignacionModal(data, id_sol, area, trab, fini, ffin);
    });
}
