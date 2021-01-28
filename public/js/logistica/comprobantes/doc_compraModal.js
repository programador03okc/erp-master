$(function(){
    $('#listaDocsCompra tbody').on('click', 'tr', function(){
        // console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaDocsCompra').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        // var idPr = $(this)[0].childNodes[5].innerHTML;
        $('.modal-footer #mid_doc_com').text(id);
        // $('.modal-footer #mid_doc_prov').text(idPr);
    });
});

function listarDocsCompra(){
    var vardataTables = funcDatatables();
    $('#listaDocsCompra').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_docs_compra',
        'columns': [
            {'data': 'id_doc_com'},
            {'data': 'razon_social'},
            {'render':
                function (data, type, row){
                    return (row['serie']+'-'+row['numero']);
                }
            },
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'des_estado'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function doc_compraModal(){
    $('#modal-doc_compra').modal({
        show: true
    });
    listarDocsCompra();
}

function selectDocCompra(){
    var myId = $('.modal-footer #mid_doc_com').text();
    var page = $('.page-main').attr('type');

    if (page == "doc_compra"){
        // console.log(myId);
        mostrar_doc_compra(myId);
    }    
    $('#modal-doc_compra').modal('hide');
}