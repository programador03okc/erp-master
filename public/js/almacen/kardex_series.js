$(function(){
    var fecha = new Date();
    var anio = fecha.getFullYear();
    $('[name=fecha_inicio]').val(anio+'-01-01');
    $('[name=fecha_fin]').val(fecha_actual());
});

function listarKardexSeries(){
    var serie = $('[name=serie]').val();
    var descripcion = $('[name=descripcion]').val();
    console.log('serie:'+serie);
    $('.dataTable').dataTable().fnDestroy();

    if (serie == '' && descripcion == ''){
        alert('No ha ingresado ningún parametro de entrada!');
    } else {
        var vardataTables = funcDatatables();
        $('#listaKardexSeries').dataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'bDestroy': true,
            'retrieve': true,
            'ajax': 'listar_kardex_serie/'+(serie !== '' ? serie : null)+'/'+(descripcion !== '' ? descripcion : null),
            'columns': [
                {'data': 'id_prod_serie'},
                {'data': 'serie'},
                {'data': 'descripcion'},
                {'data': 'fecha_guia_com'},
                {'data': 'guia_com'},
                {'data': 'razon_social_prove'},
                {'data': 'almacen_compra'},
                {'data': 'fecha_guia_ven'},
                {'data': 'guia_ven'},
                {'data': 'razon_social_cliente'},
                {'data': 'almacen_venta'},
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
    }
}

// function download_kardex_excel(){
//     var prod = $('[name=id_producto]').val();
//     var fini = $('[name=fecha_inicio]').val();
//     var ffin = $('[name=fecha_fin]').val();
//     var alm = $('[name=almacen]').val();
//     console.log(prod+'/'+alm+'/'+fini+'/'+ffin);
//     window.open('kardex_detallado/'+prod+'/'+alm+'/'+fini+'/'+ffin);
// }

function datos_producto(id_producto){
    $.ajax({
        type: 'GET',
        url: 'datos_producto/'+id_producto,
        dataType: 'JSON',
        success: function(response){
            $('#datos_producto tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}