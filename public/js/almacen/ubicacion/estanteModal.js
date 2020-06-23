$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaEstanteM tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaEstanteM').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var idAl = $(this)[0].childNodes[4].innerHTML;
        $('.modal-footer #mid_estante').text(idTr);
        $('.modal-footer #mid_almacen_estante').text(idAl);
    });
});

function listarEstantes(){
    var vardataTables = funcDatatables();
    $('#listaEstanteM').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_estantes',
        'columns': [
            {'data': 'id_estante'},
            {'data': 'alm_descripcion'},
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            },
            {'data': 'id_almacen'},
        ],
        'columnDefs': [{ 'aTargets': [0,4], 'sClass': 'invisible'}],
    });
}

function estanteModal(){
    $('#modal-estante').modal({
        show: true
    });
    clearDataTable();
    listarEstantes();
}

function selectEstante(){
    var myId = $('.modal-footer #mid_estante').text();
    var idAl = $('.modal-footer #mid_almacen_estante').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "ubicacion"){
        console.log('almacen '+idAl+' estante '+myId);
        listar_estantes_nivel(myId);
        listar_niveles(myId);
        $('[name=id_almacen_nivel]').val(idAl).trigger('change.select2');
        // $('[name=id_estante_nivel]').val(myId).trigger('change.select2');
    }
    
    $('#modal-estante').modal('hide');
}