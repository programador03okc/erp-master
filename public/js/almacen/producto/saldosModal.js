$(function(){
    /* Seleccionar valor del DataTable */
    // $('#listaSaldos tbody').on('click', 'tr', function(){
    //     console.log($(this));
    //     if ($(this).hasClass('eventClick')){
    //         $(this).removeClass('eventClick');
    //     } else {
    //         $('#listaSaldos').dataTable().$('tr.eventClick').removeClass('eventClick');
    //         $(this).addClass('eventClick');
    //     }
    //     var myId = $(this)[0].firstChild.innerHTML;
    //     var codi = $(this)[0].childNodes[1].innerHTML;
    //     var partnum = $(this)[0].childNodes[2].innerHTML;
    //     var desc = $(this)[0].childNodes[3].innerHTML;
    //     var cat = $(this)[0].childNodes[4].innerHTML;
    //     var subcat = $(this)[0].childNodes[5].innerHTML;
    //     var stoc = $(this)[0].childNodes[6].innerHTML;
    //     var rese = $(this)[0].childNodes[7].innerHTML;
    //     var unid = $(this)[0].childNodes[8].innerHTML;
    //     var idItem = $(this)[0].childNodes[9].innerHTML;

    //     var cant = parseFloat(stoc) - parseFloat(rese);

    //     $('#saldo_id_producto').text(myId);
    //     $('#saldo_codigo_item').text(codi);
    //     $('#part_number').text(partnum);
    //     $('#saldo_descripcion_item').text(desc);
    //     $('#categoria').text(cat);
    //     $('#subcategoria').text(subcat);
    //     $('#saldo_cantidad_item').text(cant);
    //     $('#saldo_unidad_medida_item').text(unid);
    //     $('#id_item').text(idItem);

    // });

});    

function listarSaldos(id_almacen){
    var vardataTables = funcDatatables();
    $('#listaSaldos').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar-saldos-por-almacen',
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'descripcion'},
            {'data': 'des_categoria'},
            {'data': 'des_subcategoria'},
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][0]['id_almacen'] == 1){
                        if(row['stock_almacenes'][0]['stock'] >0){
                            return ('<button class="btn btn-sm btn-info" onClick="selectValue(this,'+row['stock_almacenes'][0]['id_almacen']+');">'+row['stock_almacenes'][0]['stock']+'</button>')
                        }else{
                            return row['stock_almacenes'][0]['stock'];
                        }
                    }else{
                         return '-';
                    }
                }
            },
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][0]['id_almacen'] == 1){
                        return (row['stock_almacenes'][0]['cantidad_reserva']);
                    }else{
                        return '-';
                    }
                }
            },
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][1]['id_almacen'] == 2){
                        if(row['stock_almacenes'][1]['stock'] >0){
                            return ('<button class="btn btn-sm btn-info" onClick="selectValue(this,'+row['stock_almacenes'][1]['id_almacen']+');">'+row['stock_almacenes'][1]['stock']+'</button>')

                        }else{
                                return row['stock_almacenes'][0]['stock'];
                        }
                    }else{
                        return '-';
                    }
                }
            },
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][1]['id_almacen'] == 2){
                        return (row['stock_almacenes'][1]['cantidad_reserva']);
                    }else{
                        return '-';
                    }
                }
            },
            {'data': 'id_unidad_medida'},
            {'data': 'id_item'}
        ],
        'columnDefs': [{ 'aTargets': [0,10,11], 'sClass': 'invisible'}],
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

function selectValue(element,id_almacen){
    console.log(element.parentElement.parentElement.childNodes[1].innerText)
    var id = element.parentElement.parentElement.childNodes[0].innerText;
    var co = element.parentElement.parentElement.childNodes[1].innerText;
    var pn = element.parentElement.parentElement.childNodes[2].innerText;
    var de = element.parentElement.parentElement.childNodes[3].innerText;
    var cat = element.parentElement.parentElement.childNodes[4].innerText;
    var subcat = element.parentElement.parentElement.childNodes[5].innerText;
    var ca = element.parentElement.parentElement.childNodes[6].innerText;
    var un = element.parentElement.parentElement.childNodes[7].innerText;
    var idItem = element.parentElement.parentElement.childNodes[8].innerText;
    var idAlmacenReserva = id_almacen;

    $('[name=id_producto]').val(id);
    $('[name=codigo_item]').val(co);
    $('[name=part_number]').val(pn);
    $('[name=descripcion_item]').val(de);
    $('[name=categoria]').val(cat);
    $('[name=subcategoria]').val(subcat);
    $('[name=cantidad_item]').val(ca);
    $('[name=unidad_medida_item]').val(un);
    $('[name=id_item]').val(idItem);
    $('[name=id_almacen_reserva]').val(idAlmacenReserva);

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