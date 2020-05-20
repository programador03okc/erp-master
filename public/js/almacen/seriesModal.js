$(function(){
    $('#listaSeriesAlmacen tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaSeriesAlmacen').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        var serie = $(this)[0].childNodes[1].innerHTML;
        var guia_com = $(this)[0].childNodes[2].innerHTML;
        
        if (!exist(serie)){
            agregar_serie(id, serie, guia_com);
            $('#modal-series').modal('hide');
        } else {
            alert('Dicha serie ya fue ingresada!');
        }
    });
});

function listarSeriesAlmacen(id_prod, id_almacen){
    var vardataTables = funcDatatables();
    $('#listaSeriesAlmacen').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_series_almacen/'+id_prod+'/'+id_almacen,
        'columns': [
            {'data': 'id_prod_serie'},
            {'data': 'serie'},
            {'data': 'guia_com'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function seriesModal(){
    $('#modal-series').modal({
        show: true
    });
    // clearDataTable();
    var id_prod = $('[name=id_producto]').val();
    var id_almacen = $('[name=id_almacen]').val();
    console.log('id_prod:'+id_prod);
    console.log('id_almacen:'+id_almacen);
    if (id_almacen !== 0 || id_almacen !== ''){
        listarSeriesAlmacen(id_prod,id_almacen);
    }
}

// function selectGuiaCompra(){
//     var myId = $('.modal-footer #mid_guia_com').text();
//     var idPr = $('.modal-footer #mid_guia_prov').text();
//     var page = $('.page-main').attr('type');

//     if (page == "guia_compra"){
//         var activeTab = $("#tab-guia_compra #myTab li.active a").attr('type');
//         var activeForm = "form-"+activeTab.substring(1);
//         actualizar_tab(activeForm, myId, idPr);
//     }    
//     $('#modal-guia_compra').modal('hide');
// }