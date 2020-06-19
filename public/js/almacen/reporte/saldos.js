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
        'ajax': url,
        // 'ajax': {
        //     url:'listar_saldos/'+almacen,
        //     dataSrc:''
        // },
        'columns': [
            {'data': 'id_prod_ubi'},
            {'data': 'codigo'},
            {'data': 'codigo_anexo'},
            {'data': 'cod_antiguo'},
            {'data': 'descripcion'},
            {'data': 'abreviatura'},
            {'data': 'stock', 'class': 'right'},
            {'data': 'simbolo'},
            {'data': 'soles', 'class': 'right'},
            {'data': 'dolares', 'class': 'right'},
            {'data': 'costo_promedio', 'class': 'right'},
            {'data': 'cantidad_reserva', 'class': 'right'},
            {'data': 'almacen_descripcion'},
            // {'render': function (data, type, row) {
            //         return (row['cod_posicion'] !== undefined ? row['cod_posicion'] : '');
            //     }
            // },
            {'data': 'des_clasificacion'},
            {'data': 'des_categoria'},
            {'data': 'des_subcategoria'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        "order": [[4, "asc"]]
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