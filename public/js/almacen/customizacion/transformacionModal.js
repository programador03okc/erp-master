$(function(){
    $('#listaTransformaciones tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaTransformaciones').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        var idPr = $(this)[0].childNodes[1].innerHTML;
        $('.modal-footer #id_transformacion').text(id);
        $('.modal-footer #codigo').text(idPr);
    });
});

function listarTransformaciones(){
    var vardataTables = funcDatatables();
    $('#listaTransformaciones').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_transformaciones',
        'columns': [
            {'data': 'id_transformacion'},
            {'data': 'codigo'},
            {'data': 'codigo_oportunidad'},
            // {'render':
            //     function (data, type, row){
            //         return (row['codigo_oportunidad']!== null ? row['codigo_oportunidad'] : '');
            //     }
            // },
            {'data': 'cod_req'},
            {'render':
                function (data, type, row){
                    return (row['serie'] !== null ? (row['serie']+'-'+row['numero']) : '');
                }
            },
            {'render':
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function transformacionModal(){
    $('#modal-transformacion').modal({
        show: true
    });
    clearDataTable();
    listarTransformaciones();
}

function selectTransformacion(){
    var myId = $('.modal-footer #id_transformacion').text();
    // var code = $('.modal-footer #codigo').text();
    // var page = $('.page-main').attr('type');

    if (myId !== ''){
        mostrar_transformacion(myId);
    }
    $('#modal-transformacion').modal('hide');
}