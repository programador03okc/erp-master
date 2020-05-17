$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaOC tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaOC').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        // var unid = $(this)[0].childNodes[3].innerHTML;
        $('.modal-footer #id_oc').text(idTr);
        // $('.modal-footer #unid_med').text(unid);
    });
});
function listarOcs(){
    var vardataTables = funcDatatables();
    $('#listaOC').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_ocs',
        'columns': [
            {'data': 'id_orden_compra'},
            {'data': 'codigo'},
            {'data': 'razon_social'},
            {'data': 'fecha'},
            {'data': 'monto_total'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}
function ocModal(){
    $('#modal-oc').modal({
        show: true
    });
    clearDataTable();
    listarOcs();
}
function selectOC(){
    var myId = $('.modal-footer #id_oc').text();
    guia_compra_detModal(myId);
    $('#modal-oc').modal('hide');
}
