$(function(){
    var fecha = new Date();
    var anio = fecha.getFullYear();
    $('[name=fecha_inicio]').val(anio+'-01-01');
    $('[name=fecha_fin]').val(fecha_actual());
    // $('[name=id_empresa]').val('1').trigger('change.select2');
});

function generar_kardex(){
    var id_producto = $('[name=id_producto]').val();
    var almacen = $('[name=almacen]').val();
    var finicio = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();

    if (id_producto == ''){
        alert('Debe seleccionar un producto..!');
    } 
    else if (finicio > ffin){
        alert('La fecha inicio no puede ser mayor a la fecha fin');
    } 
    else {
        baseUrl = 'kardex_producto/'+id_producto+'/'+almacen+'/'+finicio+'/'+ffin;
        console.log(baseUrl);
        $.ajax({
            type: 'GET',
            url: baseUrl,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                vista_extendida();
                $('#kardex_producto tbody').html(response['html']);
                $('[name=suma_ing_cant]').text(response['suma_ing_cant']);
                $('[name=suma_sal_cant]').text(response['suma_sal_cant']);
                $('[name=suma_ing_val]').text(response['suma_ing_val']);
                $('[name=suma_sal_val]').text(response['suma_sal_val']);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function download_kardex_excel(){
    var prod = $('[name=id_producto]').val();
    var fini = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();
    var alm = $('[name=almacen]').val();
    console.log(prod+'/'+alm+'/'+fini+'/'+ffin);
    window.open('kardex_detallado/'+prod+'/'+alm+'/'+fini+'/'+ffin);
}

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

$('[name=id_empresa]').change(function(){
    var emp = $('[name=id_empresa]').val();
    if (emp > 0){
        $.ajax({
            type: 'GET',
            url: 'select_almacenes_empresa/'+emp,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                var htmls = '<option value="0">Elija una opción</option>';
                Object.keys(response).forEach(function (key){
                    htmls += '<option value="'+response[key]['id_almacen']+'">'+response[key]['codigo']+' - '+response[key]['descripcion']+'</option>';
                });
                console.log(htmls);
                $('[name=almacen]').html(htmls);
                $('[name=almacen]').val(0).trigger('change.select2');
                $('[name=todas_empresas]').prop("checked",false);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
});

function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}