$(function(){
//     $('[name=fecha]').val(fecha_actual());
    tipo_cambio();
});
function listarSaldos(){
    var almacen = $('[name=almacen]').val();
    // var fecha = $('[name=fecha]').val();
    
    var vardataTables = funcDatatables();
    $('#listaSaldos').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy':true,
        'ajax': 'listar_saldos/'+almacen,
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
            {'render': function (data, type, row) {
                    return (row['cod_posicion'] !== undefined ? row['cod_posicion'] : '');
                }
            },
            {'data': 'des_clasificacion'},
            {'data': 'des_categoria'},
            {'data': 'des_subcategoria'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        "order": [[4, "asc"]]
    });
    // vista_extendida();
    // tipo_cambio(fecha);
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