$(function(){
    $('[name=id_empresa]').val(4);
    $('[name=almacen]').val(1);
    var fecha = new Date();
    var yyyy = fecha.getFullYear();
    $('[name=fecha_inicio]').val(yyyy+'-01-01');
    $('[name=fecha_fin]').val(yyyy+'-12-31');

    $('[name=todos_documentos]').prop('checked', true);
    $('[name=documento] option').each(function(){
        $(this).prop("selected",true);
    });
    console.log($('[name=todos_documentos]').prop('checked'));
    $('[name=todas_condiciones]').prop('checked', true);
    $('[name=condicion] option').each(function(){
        $(this).prop("selected",true);
    });
    console.log($('[name=todas_condiciones]').prop('checked'));


});
function open_filtros(){
    console.log('open_filtros');
    $('#modal-filtros').modal({
        show:true
    });
}
$('[name=todos_documentos]').change(function(){
    if($(this).prop('checked') == true) {
        $('[name=documento] option').each(function(){
            $(this).prop("selected",true);
        });
    }else{
        $('[name=documento] option').each(function(){
            $(this).prop("selected",false);
        });
    }
});
$('[name=todas_condiciones]').change(function(){
    if($(this).prop('checked') == true) {
        $('[name=condicion] option').each(function(){
            $(this).prop("selected",true);
        });
    }else{
        $('[name=condicion] option').each(function(){
            $(this).prop("selected",false);
        });
    }
});
$('[name=todas_empresas]').change(function(){
    if($(this).prop('checked') == true) {
        $('[name=almacen] option').each(function(){
            $(this).prop("selected",true);
        });
    }else{
        $('[name=almacen] option').each(function(){
            $(this).prop("selected",false);
        });
    }
    // if($(this).prop('checked') == true) {
    //     $('[name=id_empresa] option').each(function(){
    //         $(this).prop("selected",true);
    //     });
    // }else{
    //     $('[name=id_empresa] option').each(function(){
    //         $(this).prop("selected",false);
    //     });
    // }
});
$('[name=todos_almacenes]').change(function(){
    if($(this).prop('checked') == true) {
        $('[name=almacen] option').each(function(){
            $(this).prop("selected",true);
        });
    }else{
        $('[name=almacen] option').each(function(){
            $(this).prop("selected",false);
        });
    }
});

$('[name=id_empresa]').change(function(){
    var emp = $('[name=id_empresa]').val();
    if (emp > 0){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'select_almacenes_empresa/'+emp,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                var htmls = '';
                var prim = false;
                Object.keys(response).forEach(function (key){
                    if (!prim){
                        htmls += '<option value="'+response[key]['id_almacen']+'" selected>'+response[key]['descripcion']+'</option>';
                        prim = true;
                    } else {
                        htmls += '<option value="'+response[key]['id_almacen']+'">'+response[key]['descripcion']+'</option>';
                    }
                });
                console.log(htmls);
                $('[name=almacen]').html(htmls);
                $('[name=todas_empresas]').prop("checked",false);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
});
function limpiar_proveedor(){
    $('[name=id_proveedor]').val('');
    $('[name=id_contrib]').val('');
    $('[name=razon_social]').val('');
}
function limpiar_transportista(){
    $('[name=id_proveedor_tra]').val('');
    $('[name=id_contrib_tra]').val('');
    $('[name=razon_social_tra]').val('');
}