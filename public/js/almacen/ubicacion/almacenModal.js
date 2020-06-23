$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaAlmacen tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaAlmacen').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #mid_almacen').text(idTr);
    });
});

function listarAlmacenes(){
    var vardataTables = funcDatatables();
    $('#listaAlmacen').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_almacenes',
        'columns': [
            {'data': 'id_almacen'},
            {'data': 'sede_descripcion'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'tp_almacen'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function almacenModal(){
    $('#modal-Almacen').modal({
        show: true
    });
    // $('#modal-Almacen').dataTable().fnDestroy();
    clearDataTable();
    listarAlmacenes();
}

function selectAlmacen(){
    var myId = $('.modal-footer #mid_almacen').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "ubicacion"){
        console.log(myId);
        listar_estantes(myId);
    }
    
    $('#modal-Almacen').modal('hide');
}