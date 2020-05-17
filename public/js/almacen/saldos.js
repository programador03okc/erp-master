$(function(){
    $('[name=fecha]').val(fecha_actual());
});
function listarSaldos(){
    var almacen = $('[name=almacen]').val();
    var fecha = $('[name=fecha]').val();
    
    var vardataTables = funcDatatables();
    $('#listaSaldos').dataTable({
        'destroy':true,
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': {
            url:'listar_saldos/'+almacen+'/'+fecha,
            dataSrc:''
        },
        'columns': [
            {'data': 'id_prod_ubi'},
            {'data': 'codigo'},
            {'data': 'codigo_anexo'},
            {'data': 'cod_antiguo'},
            {'data': 'descripcion'},
            {'data': 'abreviatura'},
            {'data': 'stock'},
            {'data': 'simbolo'},
            {'data': 'soles'},
            {'data': 'dolares'},
            {'data': 'costo_promedio'},
            {'data': 'cod_posicion'},
            {'data': 'des_clasificacion'},
            {'data': 'des_categoria'},
            {'data': 'des_subcategoria'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        "order": [[4, "asc"]]
    });
    vista_extendida();
    tipo_cambio(fecha);
}
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}
function tipo_cambio(fecha){
    $.ajax({
        type: 'GET',
        url: 'tipo_cambio_compra/'+fecha,
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