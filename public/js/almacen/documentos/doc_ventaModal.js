$(function(){
    $('#listaDocsVenta tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaDocsVenta').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        // var idPr = $(this)[0].childNodes[5].innerHTML;
        $('.modal-footer #mid_doc_ven').text(id);
        // $('.modal-footer #mid_doc_prov').text(idPr);
    });
});

function listarDocsVenta(){
    var vardataTables = funcDatatables();
    $('#listaDocsVenta').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_docs_venta',
        'columns': [
            {'data': 'id_doc_ven'},
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
            {'data': 'estado_doc'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function doc_ventaModal(){
    $('#modal-doc_venta').modal({
        show: true
    });
    clearDataTable();
    listarDocsVenta();
}

function selectDocVenta(){
    var myId = $('.modal-footer #mid_doc_ven').text();
    var page = $('.page-main').attr('type');

    if (page == "doc_venta"){
        // var activeTab = $("#tab-doc_compra #myTab li.active a").attr('type');
        // var activeForm = "form-"+activeTab.substring(1);
        // actualizar_tab(activeForm, myId);
        mostrar_doc_venta(myId);
    }    
    $('#modal-doc_venta').modal('hide');
}