$(function(){
    $('#listaTrabajador tbody').on("click","tr", function(){
        var id = $(this)[0].firstChild.innerHTML;
        var doc = $(this)[0].childNodes[1].innerHTML;
        var nom = $(this)[0].childNodes[2].innerHTML;
        console.log(id);
        $('[name=id_trabajador]').val(id);
        $('[name=nro_documento]').val(doc);
        $('[name=nombre_trabajador]').val(nom);
        $('#modal-trabajador').modal('hide');
    });
});
function listarTrabajadores(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaTrabajador').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_trabajadores',
        'columns': [
            {'data': 'id_trabajador'},
            {'data': 'nro_documento'},
            {'data': 'nombre_trabajador'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
function trabajadorModal(){
    $('#modal-trabajador').modal({
        show: true
    });
    listarTrabajadores();
}
