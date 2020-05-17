$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaTransportistas tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaTransportistas').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var idCo = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[3].innerHTML;
        $('.modal-footer #id_proveedor_tra').text(idTr);
        $('.modal-footer #id_contribuyente_tra').text(idCo);
        $('.modal-footer #razon_social_tra').text(des);
    });
});

function listar_transportistas_com(){
    var vardataTables = funcDatatables();
    $('#listaTransportistas').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_transportistas_com',
        'columns': [
            {'data': 'transportista'},
            {'data': 'id_contribuyente'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
        ],
        'columnDefs': [{ 'aTargets': [0,1], 'sClass': 'invisible'}],
    });
}

function listar_transportistas_ven(){
    var vardataTables = funcDatatables();
    $('#listaTransportistas').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_transportistas_ven',
        'columns': [
            {'data': 'transportista'},
            {'data': 'id_contribuyente'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
        ],
        'columnDefs': [{ 'aTargets': [0,1], 'sClass': 'invisible'}],
    });
}

function transportistaModal(tipo){
    $('#modal-transportista').modal({
        show: true
    });
    // clearDataTable();
    if (tipo == "compra"){
        listar_transportistas_com();
    } else if (tipo == "venta"){
        listar_transportistas_ven();
    }
}

function selectTransportista(){
    var myId = $('.modal-footer #id_proveedor_tra').text();
    var idCo = $('.modal-footer #id_contribuyente_tra').text();
    var des = $('.modal-footer #razon_social_tra').text();
    // var page = $('.page-main').attr('type');
    // var form = $('.page-main form[type=register]').attr('id');

    console.log('cliente'+myId+' razon_social'+des);
    $('[name=id_proveedor_tra]').val(myId);
    $('[name=id_contrib_tra]').val(idCo);
    $('[name=razon_social_tra]').val(des);
    
    
    $('#modal-transportista').modal('hide');
}