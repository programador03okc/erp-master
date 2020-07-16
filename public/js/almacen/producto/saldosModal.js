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
        var partnum = $(this)[0].childNodes[2].innerHTML;
        var desc = $(this)[0].childNodes[3].innerHTML;
        var cat = $(this)[0].childNodes[4].innerHTML;
        var subcat = $(this)[0].childNodes[5].innerHTML;
        var stoc = $(this)[0].childNodes[6].innerHTML;
        var rese = $(this)[0].childNodes[7].innerHTML;
        var unid = $(this)[0].childNodes[8].innerHTML;
        var idItem = $(this)[0].childNodes[9].innerHTML;

        var cant = parseFloat(stoc) - parseFloat(rese);

        $('#saldo_id_producto').text(myId);
        $('#saldo_codigo_item').text(codi);
        $('#part_number').text(partnum);
        $('#saldo_descripcion_item').text(desc);
        $('#categoria').text(cat);
        $('#subcategoria').text(subcat);
        $('#saldo_cantidad_item').text(cant);
        $('#saldo_unidad_medida_item').text(unid);
        $('#id_item').text(idItem);

    });

});    

function listarSaldos(id_almacen){
    var vardataTables = funcDatatables();
    $('#listaSaldos').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_saldos/'+id_almacen,
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'descripcion'},
            {'data': 'des_categoria'},
            {'data': 'des_subcategoria'},
            {'data': 'stock'},
            {'render':
                function (data, type, row){
                    if(row['cantidad_reserva'] !== null){
                        return (row['cantidad_reserva']);
                    } else {
                        return '0';
                    }
                }
            },
            // {'render':
            //     function (data, type, row){
            //         if(row['cantidad_reserva'] !== null){
            //             return '<button type="button" class="ver btn btn-info boton" data-toggle="tooltip" '+
            //             'data-placement="bottom" title="Ver Requerimientos" data-id="'+row['id_producto']+'" data-almacen="'+id_almacen+'" >'+
            //             '<i class="fas fa-list-ul"></i></button>';
            //         } else {
            //             return '';
            //         }
            //     }
            // },
            {'data': 'id_unidad_medida'},
            {'data': 'id_item'}
        ],
        'columnDefs': [{ 'aTargets': [0,8,9], 'sClass': 'invisible'}],
    });
}

// $('#listaSaldos tbody').on("dblclick","tr", function(){
//     var data = $('#listaSaldos').DataTable().row(this).data();
//     console.log(data);
//     let id = data.id_producto;
//     let almacen = data.id_almacen;
//     $('#modal-verRequerimientoEstado').modal({
//         show: true
//     });
//     $('#nombreEstado').text('Requerimientos que generan la Reserva');
//     console.log(id+','+ almacen);
//     verRequerimientosReservados(id, almacen);
// });

function saldosModal(id_almacen){
    $('#modal-saldos').modal({
        show: true
    });
    listarSaldos(id_almacen);
}

function selectValue(){
    var id = $('#saldo_id_producto').text();
    var co = $('#saldo_codigo_item').text();
    var pn = $('#part_number').text();
    var de = $('#saldo_descripcion_item').text();
    var cat = $('#categoria').text();
    var subcat = $('#subcategoria').text();
    var ca = $('#saldo_cantidad_item').text();
    var un = $('#saldo_unidad_medida_item').text();
    var idItem = $('#id_item').text();

    $('[name=id_producto]').val(id);
    $('[name=codigo_item]').val(co);
    $('[name=part_number]').val(pn);
    $('[name=descripcion_item]').val(de);
    $('[name=categoria]').val(cat);
    $('[name=subcategoria]').val(subcat);
    $('[name=cantidad_item]').val(ca);
    $('[name=unidad_medida_item]').val(un);
    $('[name=id_item]').val(idItem);

    $('#modal-saldos').modal('hide');
    // var myId = $('.modal-footer label').text();
    // $('[name=id_persona]').val(myId);
}

function verRequerimientosReservados(id_producto,id_almacen){
    let baseUrl = 'verRequerimientosReservados/'+id_producto+'/'+id_almacen;
    console.log(baseUrl);
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            // response.forEach(element => {
            //     html+='<tr id="'+element.id_requerimiento+'">'+
            //     '<td>'+element.codigo+'</td>'+
            //     '<td>'+element.concepto+'</td>'+
            //     '</tr>';
            //     i++;
            // });
            // $('#listaRequerimientosEstado tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}