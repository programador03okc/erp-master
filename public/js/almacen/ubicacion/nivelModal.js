 $(function(){
    /* Seleccionar valor del DataTable */
    $('#listaNivelM tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaNivelM').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var idEs = $(this)[0].childNodes[5].innerHTML;
        var idAl = $(this)[0].childNodes[6].innerHTML;
        $('.modal-footer #mid_nivel').text(idTr);
        $('.modal-footer #mid_estante_nivel').text(idEs);
        $('.modal-footer #mid_almacen_nivel').text(idAl);
    });
});

function listarNiveles(){
    var vardataTables = funcDatatables();
    $('#listaNivelM').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_niveles',
        'columns': [
            {'data': 'id_nivel'},
            {'data': 'alm_descripcion'},
            {'data': 'cod_estante'},
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            },
            {'data': 'id_estante'},
            {'data': 'id_almacen'},
        ],
        'columnDefs': [{ 'aTargets': [0,5,6], 'sClass': 'invisible'}],
    });
}

function nivelModal(){
    $('#modal-nivel').modal({
        show: true
    });
    clearDataTable();
    listarNiveles();
}

function selectNivel(){
    var myId = $('.modal-footer #mid_nivel').text();
    var idAl = $('.modal-footer #mid_almacen_nivel').text();
    var idEs = $('.modal-footer #mid_estante_nivel').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "ubicacion"){
        console.log('almacen',idAl,'estante',idEs);
        console.log('nivel',myId);
        listar_niveles_posicion(myId);
        listar_posiciones(myId);
        $('[name=id_almacen_posicion]').val(idAl).trigger('change.select2');
        $('[name=id_estante_posicion]').val(idEs).trigger('change.select2');
        // $('[name=id_nivel_posicion]').val(myId).trigger('change.select2');
    }
    
    $('#modal-nivel').modal('hide');
}