$(function(){
    $('#listaGuiasVenta tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaGuiasVenta').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        var idPr = $(this)[0].childNodes[5].innerHTML;
        $('.modal-footer #mid_guia_ven').text(id);
        $('.modal-footer #mid_guia_alm').text(idPr);
    });
});

function listarGuiasVenta(){
    var vardataTables = funcDatatables();
    console.log('des_Estado');
    $('#listaGuiasVenta').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'ajax': 'listar_guias_venta',
        'columns': [
            {'data': 'id_guia_ven'},
            {'data': 'razon_social'},
            {'render':
                function (data, type, row){
                    return (row['tp_doc_almacen']+'-'+row['serie']+'-'+row['numero']);
                }
            },
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'ope_descripcion'},
            {'data': 'estado_doc'},
            {'data': 'id_almacen'},
        ],
        'columnDefs': [{ 'aTargets': [0,6], 'sClass': 'invisible'}],
    });
}

function guia_ventaModal(){
    $('#modal-guia_venta').modal({
        show: true
    });
    clearDataTable();
    listarGuiasVenta();
}

function selectGuiaVenta(){
    var myId = $('.modal-footer #mid_guia_ven').text();
    var idPr = $('.modal-footer #mid_guia_alm').text();
    var page = $('.page-main').attr('type');

    if (page == "guia_venta"){
        var activeTab = $("#tab-guia_venta #myTab li.active a").attr('type');
        var activeForm = "form-"+activeTab.substring(1);
        console.log(myId);
        actualizar_tab(activeForm, myId);
    }    
    $('#modal-guia_venta').modal('hide');
}