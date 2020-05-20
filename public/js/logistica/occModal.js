$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaOcc tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaOcc').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_occ').text(idTr);
    });
});

function listar_occ(){
    var vardataTables = funcDatatables();
    $('#listaOcc').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy': true,
        'ajax': '/listar_occ_pendientes',
        'columns': [
            {'data': 'id'},
            {'data': 'orden_compra'},
            {'data': 'ruc'},
            {'data': 'entidad'},
            {'data': 'monto_total'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function openOccModal(){
    var page = $('.page-main').attr('type');
    var abrir = false;

    if (page == "doc_venta"){
        var id_doc = $('[name=id_doc_ven]').val();
        console.log('id_doc: '+id_doc);
        if (id_doc !== ''){
            abrir = true;
        } else {
            alert('No se eligió un Documento!');
        }
    } else {
        abrir = true;
    }

    if (abrir){
        $('#modal-occ').modal({
            show: true
        });
        clearDataTable();
        listar_occ();
    }
}

function selectOcc(){
    var myId = $('.modal-footer #id_occ').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "requerimiento"){
        console.log('id:'+myId);
        console.log('copiar items');
        copiar_items_occ(myId);
        $('[name=id_occ]').val(myId);
        $('[name=cod_occ]').val(cod);
    } 
    else if (page == "doc_venta"){
        copiar_items_occ_doc(myId);
    }
    
    $('#modal-occ').modal('hide');
}