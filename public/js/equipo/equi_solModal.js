$(function(){
    // listar_solicitudes();
    var form = $('.page-main form[type=register]').attr('id');
    $('#listaSolicitudes tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaSolicitudes').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_solicitud').text(id);
        console.log(id);
        // clearForm(form);
        // mostrar_solicitud(id);
        // changeStateButton('historial');
    });
});
function equi_solModal(){
    $('#modal-equi_sol').modal({
        show: true
    });
    clearDataTable();
    listar_solicitudes();
}
function selectSolicitud(){
    var myId = $('.modal-footer #id_solicitud').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "equi_sol"){
        clearForm(form);
        changeStateButton('historial');
        console.log($(":file").filestyle('disabled'));
        mostrar_solicitud(myId);
        var sede = $('[name=id_sede]').val();
        console.log('cbo sede '+sede);
        var grupo = $('[name=id_grupo]').val();
        console.log('cbo grupo '+grupo);
        var area = $('[name=id_area]').val();
        console.log('cbo area '+area);
    }
    $('#modal-equi_sol').modal('hide');
}
function listar_solicitudes(){
    // var id_grupo = auth_user.id_grupo;
    var id_trabajador = auth_user.id_trabajador;
    var id_usuario = auth_user.id_usuario;
    console.log(auth_user);
    var vardataTables = funcDatatables();
    $('#listaSolicitudes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        ajax: { url:'mostrar_solicitudes/'+id_trabajador+'/'+id_usuario,
                dataSrc:''},
        'columns': [
            {'data': 'id_solicitud'},
            {'data': 'codigo'},
            {'data': 'fecha_solicitud'},
            {'data': 'area_descripcion'},
            {'data': 'nombre_trabajador'},
            {'data': 'estado_doc'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_registro']));
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
