$(function(){
    $('#listaValorizaciones tbody').on("click","tr", function(){
        var id = $(this)[0].firstChild.innerHTML;
        var idPres = $(this)[0].childNodes[1].innerHTML;
        console.log(id);
        $('[name=id_valorizacion]').val(id);
        $('[name=id_presup]').val(idPres);
        $('[name=modo]').val('update');
        mostrar_valorizacion(id);
        $('#modal-valorizacion').modal('hide');
    });
});

function listarValorizaciones(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaValorizaciones').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_valorizaciones',
        'columns': [
            {'data': 'id_valorizacion'},
            {'data': 'id_presup'},
            {'render': 
                function (data, type, row){
                    return ('Val Nro.'+row['numero']);
                }
            },
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render': 
                function (data, type, row){
                    return (formatNumber.decimal(row['sub_total'],row['simbolo'],-2));
                }, className: 'text-right'
            }
        ],
        'columnDefs': [{ 'aTargets': [0,1], 'sClass': 'invisible'}],
    });
}

function valorizacionModal(){
    $('#modal-valorizacion').modal({
        show: true
    });
    listarValorizaciones();
}
