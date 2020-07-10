$(function(){
//     $('[name=fecha]').val(fecha_actual());
    tipo_cambio();
    vista_extendida();
    mostrarSaldos();
});

function mostrarSaldos(){
    var almacen = $('[name=almacen]').val();
    if (almacen !== null && almacen !== 0 && almacen !== ''){
        var url = 'listar_saldos/'+almacen;
        listarSaldos(url);
    }
}

$("[name=todos_almacenes]").on( 'change', function() {
    if( $(this).is(':checked') ) {
        var url = 'listar_saldos_todo';
        listarSaldos(url);
    } else {
        mostrarSaldos();
    }
});

function listarSaldos(url){
    // var almacen = $('[name=almacen]').val();
    // // var fecha = $('[name=fecha]').val();
    // var url = 'listar_saldos/'+almacen;

    var vardataTables = funcDatatables();
    $('#listaSaldos').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy':true,
        "scrollX": true,
        'ajax': url,
        // 'ajax': {
        //     url:'listar_saldos/'+almacen,
        //     dataSrc:''
        // },
        'columns': [
            {'data': 'id_prod_ubi'},
            {'data': 'codigo'},
            {'data': 'codigo_anexo'},
            {'data': 'part_number'},
            {'data': 'des_categoria'},
            {'data': 'des_subcategoria'},
            {'data': 'descripcion'},
            {'data': 'abreviatura'},
            {'data': 'stock', 'class': 'right'},
            {'data': 'cantidad_reserva', 'class': 'right'},
            {'render':
                function (data, type, row){
                    if(row['cantidad_reserva'] !== null){
                        return '<button type="button" class="ver btn btn-info boton" data-toggle="tooltip" '+
                        'data-placement="bottom" title="Ver Requerimientos" data-id="'+row['id_producto']+'" data-almacen="'+row['id_almacen']+'" >'+
                        '<i class="fas fa-list-ul"></i></button>';
                    } else {
                        return '';
                    }
                }
            },
            {'data': 'simbolo'},
            {'data': 'soles', 'class': 'right'},
            {'data': 'dolares', 'class': 'right'},
            {'data': 'costo_promedio', 'class': 'right'},
            {'data': 'almacen_descripcion'},
            // {'render': function (data, type, row) {
            //         return (row['cod_posicion'] !== undefined ? row['cod_posicion'] : '');
            //     }
            // },
            {'data': 'des_clasificacion'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        "order": [[4, "asc"]]
    });
}

$('#listaSaldos tbody').on("click","tr", function(){
    var data = $('#listaSaldos').DataTable().row(this).data();
    console.log(data);
    let id = data.id_producto;
    let almacen = data.id_almacen;
    $('#modal-verRequerimientoEstado').modal({
        show: true
    });
    $('#nombreEstado').text('Requerimientos que generan la Reserva');
    console.log(id+','+ almacen);
    verRequerimientosReservados(id, almacen);
});

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
                '<td>'+element.codigo+'</td>'+
                '<td>'+element.concepto+'</td>'+
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