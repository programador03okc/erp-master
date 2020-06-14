$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaSaldos tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaSaldos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        var codi = $(this)[0].childNodes[1].innerHTML;
        var desc = $(this)[0].childNodes[2].innerHTML;
        var stoc = $(this)[0].childNodes[3].innerHTML;

        $('[name=id_producto]').val(myId);
        $('[name=codigo_item]').val(codi);
        $('[name=descripcion_item]').val(desc);
        $('[name=cantidad_item]').val(stoc);

        $('#modal-saldos').modal('hide');
    });
});

function listarSaldos(id_almacen){
    var vardataTables = funcDatatables();
    $('#listaSaldos').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': '/listar_saldos/'+id_almacen,
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    if(row['cantidad_reserva'] != null){
                        return (row['stock'] - row['cantidad_reserva']);
                    }else{
                        return (row['stock']);
                    }
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function saldosModal(id_almacen){
    $('#modal-saldos').modal({
        show: true
    });
    listarSaldos(id_almacen);
}
