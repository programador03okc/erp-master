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

var getSaldosPorAlmacen = function() {
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'GET',
            url: 'listar-saldos-por-almacen',
            datatype: "JSON",
            data: data,
            success: function(response){
                resolve(response)  
            },
            error: function(err) {
                reject(err) // Reject the promise and go to catch()
                }
        });
    
    });
}

function listarSaldos(){
    getSaldosPorAlmacen().then(function(data) {
        var table = document.getElementById("listaSaldos").tHead;
        table.parentNode.removeChild(table);
        document.getElementById("listaSaldos").createTHead();
        buildTableListaSaldos(data);
    });
}

function buildTableListaSaldos(obj){
    var table = document.getElementById("listaSaldos").tHead;
 
    
    var row = table.insertRow(0);
    row.insertCell(0).outerHTML  = '<th rowspan="2" hidden >Id</th>';
    row.insertCell(1).outerHTML  = '<th rowspan="2">Código</th>';
    row.insertCell(2).outerHTML  = '<th rowspan="2">Part Number</th>';
    row.insertCell(3).outerHTML  = '<th rowspan="2">Categoría</th>';
    row.insertCell(4).outerHTML  = '<th rowspan="2">SubCategoría</th>';
    row.insertCell(5).outerHTML  = '<th rowspan="2">Descripción</th>';
    let startTd =6;
    let firstElement = obj.data[0].stock_almacenes;
    
    for (let i = 0; i < firstElement.length; i++) {
        const almacen = firstElement[i].almacen_descripcion;
        row.insertCell(startTd).outerHTML  = '<th colspan="2">'+almacen+'</th>';
        startTd++;
    }
    row.insertCell(startTd).outerHTML  = '<th rowspan="2">Unid.medida</th>';
    row.insertCell(startTd+1).outerHTML  = '<th rowspan="2">id_item</th>';
    row.insertCell(startTd+2).outerHTML  = '<th rowspan="2">id_servicio</th>';
    var row2 = table.insertRow(1);

    let cantidadAlmacenes = firstElement.length;
    let detallePorAlmacen = cantidadAlmacenes*2;
    for (let i = 0; i < detallePorAlmacen ; i++) {
        if(i%2 == 0 ){ //par
            row2.insertCell(i).outerHTML  = '<th>Stock</th>';
        }else{ //impar
            row2.insertCell(i).outerHTML  = '<th>Reserva</th>';
        }
    }


    fillDataListaSaldos(obj);
}

function fillDataListaSaldos(obj){
    var vardataTables = funcDatatables();
    $('#listaSaldos').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'processing': true,
        'data': obj.data,
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'des_categoria'},
            {'data': 'des_subcategoria'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][0]['id_almacen'] == 1){
                        if(row['stock_almacenes'][0]['stock'] >0){
                            return ('<button class="btn btn-sm btn-info" onClick="selectValue(this,'+row['stock_almacenes'][0]['id_almacen']+',\''+row['stock_almacenes'][0]['almacen_descripcion']+'\');">'+row['stock_almacenes'][0]['stock']+'</button>')
                        }else if(row['id_servicio'] >0){
                            return ('<button class="btn btn-sm btn-info" onClick="selectValue(this,'+row['stock_almacenes'][0]['id_almacen']+',\''+row['stock_almacenes'][0]['almacen_descripcion']+'\');">Servicio</button>')
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
                            return ('<button class="btn btn-sm btn-info" onClick="selectValue(this,'+row['stock_almacenes'][1]['id_almacen']+',\''+row['stock_almacenes'][1]['almacen_descripcion']+'\');">'+row['stock_almacenes'][1]['stock']+'</button>')
                        }else if(row['id_servicio'] >0){
                            return ('<button class="btn btn-sm btn-info" onClick="selectValue(this,'+row['stock_almacenes'][1]['id_almacen']+',\''+row['stock_almacenes'][1]['almacen_descripcion']+'\');">Servicio</button>')    
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
            {'data': 'id_item'},
            {'data': 'id_servicio'}
        ],
        'columnDefs': [{ 'aTargets': [0,10,11,12], 'sClass': 'invisible'}],
        'order': [
            [5, 'asc']
        ]
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
        show: true,
        backdrop: 'true',
        keyboard: true


    });
    listarSaldos(id_almacen);
}

function selectValue(element,id_almacen,almacen_descripcion){

    if(document.querySelector("form[id='form-requerimiento']") != null) {
         detalleRequerimientoModal(event);
    }


    var id = element.parentElement.parentElement.childNodes[0].innerText;
    var co = element.parentElement.parentElement.childNodes[1].innerText;
    var pn = element.parentElement.parentElement.childNodes[2].innerText;
    var cat = element.parentElement.parentElement.childNodes[3].innerText;
    var subcat = element.parentElement.parentElement.childNodes[4].innerText;
    var de = element.parentElement.parentElement.childNodes[5].innerText;
    var ca = element.innerText;
    var un = element.parentElement.parentElement.childNodes[10].innerText;
    var idItem = element.parentElement.parentElement.childNodes[11].innerText;
    var idAlmacenReserva = id_almacen;
    var descripcionAlmacenReserva = almacen_descripcion;
    var idser = element.parentElement.parentElement.childNodes[12].innerText;

    if(id >0){
        $('[name=id_tipo_item]').val(1);
    }else if(idser > 0){
        $('[name=id_tipo_item]').val(2);
    }
    $('[name=id_producto]').val(id);
    $('[name=id_servicio]').val(idser);
    $('[name=codigo_item]').val(co);
    $('[name=part_number]').val(pn);
    $('[name=descripcion_item]').val(de);
    $('[name=categoria]').val(cat);
    $('[name=subcategoria]').val(subcat);
    $('[name=cantidad_item]').val(ca);
    $('[name=unidad_medida_item]').val(un);
    $('[name=id_item]').val(idItem);
    $('[name=id_almacen_reserva]').val(idAlmacenReserva);
    $('[name=almacen_descripcion]').val(descripcionAlmacenReserva);

    obtenerPromociones(id,idAlmacenReserva,de);

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