$(function(){
    listar_mttos();
    var form = $('.page-main form[type=register]').attr('id');
    $('#listaMttos tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaMttos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_mtto').text(id);
        console.log(id);
    });
});
function mttoModal(){
    $('#modal-mtto').modal({
        show: true
    });
    clearDataTable();
    listar_mttos();
}
function selectMtto(){
    var myId = $('.modal-footer #id_mtto').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "mtto"){
        clearForm(form);
        mostrar_mtto(myId);
        changeStateButton('historial');
        console.log($(":file").filestyle('disabled'));
    }
    $('#modal-mtto').modal('hide');
}
function listar_mttos(){
    var vardataTables = funcDatatables();
    $('#listaMttos').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_mttos',
        'columns': [
            {'data': 'id_mtto'},
            {'data': 'codigo'},
            {'data': 'fecha_mtto'},
            {'data': 'des_equipo'},
        ]
    });
}
