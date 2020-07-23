$(function(){
//     $('[name=fecha]').val(fecha_actual());
    tipo_cambio();
    vista_extendida();
    listarSaldos();
});

// function mostrarSaldos(){
//     var almacen = $('[name=almacen]').val();
//     if (almacen !== null && almacen !== 0 && almacen !== ''){
//         var url = 'listar_saldos/'+almacen;
//         listarSaldos(url);
//     }
// }

// $("[name=todos_almacenes]").on( 'change', function() {
//     if( $(this).is(':checked') ) {
//         var url = 'listar_saldos_todo';
//         listarSaldos(url);
//     } else {
//         mostrarSaldos();
//     }
// });

// function listarSaldos(url){
//     // var almacen = $('[name=almacen]').val();
//     // // var fecha = $('[name=fecha]').val();
//     // var url = 'listar_saldos/'+almacen;

//     var vardataTables = funcDatatables();
//     $('#listaSaldos').DataTable({
//         'dom': vardataTables[1],
//         'buttons': vardataTables[2],
//         'language' : vardataTables[0],
//         'destroy':true,
//         'ajax': url,
//         // 'ajax': {
//             //     url:'listar_saldos/'+almacen,
//             //     dataSrc:''
//             // },
//         // "scrollX": true,
//         'columns': [
//             {'data': 'id_prod_ubi'},
//             {'data': 'codigo'},
//             {'data': 'codigo_anexo'},
//             {'data': 'part_number'},
//             {'data': 'des_categoria'},
//             {'data': 'des_subcategoria'},
//             {'data': 'descripcion'},
//             {'data': 'abreviatura'},
//             {'data': 'stock', 'class': 'right'},
//             {'data': 'cantidad_reserva', 'class': 'right'},
//             {'render':
//                 function (data, type, row){
//                     if(row['cantidad_reserva'] !== null){
//                         return '<button type="button" class="ver btn btn-info boton" data-toggle="tooltip" '+
//                         'data-placement="bottom" title="Ver Requerimientos" data-id="'+row['id_producto']+'" data-almacen="'+row['id_almacen']+'" >'+
//                         '<i class="fas fa-list-ul"></i></button>';
//                     } else {
//                         return '';
//                     }
//                 }
//             },
//             {'data': 'simbolo'},
//             {'data': 'soles', 'class': 'right'},
//             {'data': 'dolares', 'class': 'right'},
//             {'data': 'costo_promedio', 'class': 'right'},
//             {'data': 'almacen_descripcion'},
//             // {'render': function (data, type, row) {
//             //         return (row['cod_posicion'] !== undefined ? row['cod_posicion'] : '');
//             //     }
//             // },
//             {'data': 'des_clasificacion'},
//         ],
//         'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
//         "order": [[4, "asc"]]
//     });
// }

function listarSaldos(){
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
                        // if(row['stock_almacenes'][0]['stock'] >0){
                        //     return ('<button class="btn btn-sm btn-info" onClick="selectValue(this,'+row['stock_almacenes'][0]['id_almacen']+',\''+row['stock_almacenes'][0]['almacen_descripcion']+'\');">'+row['stock_almacenes'][0]['stock']+'</button>')
                        // }else{
                            return row['stock_almacenes'][0]['stock'];
                        // }
                    }else{
                         return '-';
                    }
                }
            },
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][0]['id_almacen'] == 1){
                        if(row['stock_almacenes'][0]['cantidad_reserva'] >0){
                            return ('<button class="btn btn-sm btn-info" onClick="openReservados('+row['id_producto']+','+row['stock_almacenes'][0]['id_almacen']+')">'+row['stock_almacenes'][0]['cantidad_reserva']+' </button>')
                        }else{
                            return (row['stock_almacenes'][0]['cantidad_reserva']);
                        }
                    }else{
                        return '-';
                    }
                }
            },
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][1]['id_almacen'] == 2){
                        // if(row['stock_almacenes'][1]['stock'] >0){
                        //     return ('<button class="btn btn-sm btn-info" onClick="selectValue(this,'+row['stock_almacenes'][1]['id_almacen']+',\''+row['stock_almacenes'][1]['almacen_descripcion']+'\');">'+row['stock_almacenes'][1]['stock']+'</button>')

                        // }else{
                                return row['stock_almacenes'][1]['stock'];
                        // }
                    }else{
                        return '-';
                    }
                }
            },
            {'render':
                function (data, type, row){
                    if(row['stock_almacenes'][1]['id_almacen'] == 2){
                        if(row['stock_almacenes'][1]['cantidad_reserva'] >0){
                            return ('<button class="btn btn-sm btn-info" onClick="openReservados('+row['id_producto']+','+row['stock_almacenes'][1]['id_almacen']+')">'+row['stock_almacenes'][1]['cantidad_reserva']+' </button>')
                        }else{
                            return (row['stock_almacenes'][1]['cantidad_reserva']);
                        }
                        // return (row['stock_almacenes'][1]['cantidad_reserva']);
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

// $('#listaSaldos tbody').on("click","tr", function(){
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

function openReservados(id_producto, id_almacen){
    $('#modal-verRequerimientoEstado').modal({
        show: true
    });
    $('#nombreEstado').text('Requerimientos que generan la Reserva');
    console.log(id_producto+','+ id_almacen);
    verRequerimientosReservados(id_producto, id_almacen);
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
            response.forEach(element => {
                html+='<tr id="'+element.id_requerimiento+'">'+
                '<td><label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento('+element.id_requerimiento+')">'+element.codigo+'</label></td>'+
                '<td>'+element.concepto+'</td>'+
                '<td>'+element.almacen_descripcion+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                '<td>'+element.nombre_corto+'</td>'+
                '</tr>';
                i++;
            });
            $('#listaRequerimientosEstado tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function tipo_cambio(){
    $.ajax({
        type: 'GET',
        url: 'tipo_cambio_compra/'+fecha_actual(),
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=tipo_cambio]').val(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function abrir_requerimiento(id_requerimiento){
    // Abrir nuevo tab
    localStorage.setItem("id_requerimiento",id_requerimiento);
    let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}