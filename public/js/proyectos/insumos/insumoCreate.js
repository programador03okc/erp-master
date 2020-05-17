function open_insumo_create(data){
    $('#modal-insumo_create').modal({
        show: true
    });
    if (data !== undefined){
        $('[name=id_insumo]').val(data.id_insumo);
        $('[name=codigo]').val(data.codigo);
        $('[name=descripcion_insumo]').val(data.descripcion);
        $('[name=tipo_insumo]').val(data.tp_insumo);
        $('[name=id_categoria_insumo]').val(data.id_categoria);
        $('[name=unid_medida_insumo]').val(data.unid_medida);
        $('[name=precio]').val(data.precio);
        $('[name=flete]').val(data.flete);
        $('[name=peso_unitario]').val(data.peso_unitario);
        $('[name=iu]').val(data.iu).trigger('change.select2');
    } else {
        limpiarCampos();
    }
}
function limpiarCampos(){
    $('[name=id_insumo]').val('');
    $('[name=codigo]').val('');
    $('[name=descripcion_insumo]').val('');
    $('[name=tipo_insumo]').val('');
    $('[name=id_categoria_insumo]').val('');
    $('[name=unid_medida_insumo]').val('');
    $('[name=precio]').val('');
    $('[name=flete]').val('');
    $('[name=peso_unitario]').val('');
    $('[name=iu]').val('').trigger('change.select2');
}
function guardar_insumo(){
    var id = $('[name=id_insumo]').val();
    var des = $('[name=descripcion_insumo]').val();
    var tp = $('[name=tipo_insumo]').val();
    var cat = $('[name=id_categoria_insumo]').val();
    var und = $('[name=unid_medida_insumo]').val();
    var pre = $('[name=precio]').val();
    var fle = $('[name=flete]').val();
    var pes = $('[name=peso_unitario]').val();
    var iu = $('[name=iu]').val();

    var data = 'id_insumo='+id+
            '&descripcion='+des+
            '&tp_insumo='+tp+
            '&id_categoria='+cat+
            '&unid_medida='+und+
            '&precio='+(pre!==undefined ? pre : 0)+
            '&flete='+fle+
            '&peso_unitario='+pes+
            '&iu='+iu;
    // var token = $('#token').val();
    
    var baseUrl;
    if (id !== ''){
        baseUrl = 'actualizar_insumo';
    } else {
        baseUrl = 'guardar_insumo';
    }
    var msj = verificaInsumo();
    console.log(data);

    if (msj.length > 0){
        alert(msj);
    } 
    else {
        $.ajax({
            type: 'POST',
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Insumo registrado con exito');
                    $('#listaInsumo').DataTable().ajax.reload();
                    limpiarCampos();
                    $('#modal-insumo_create').modal('hide');
                } else {
                    alert('Ya existe un insumo con dicha descripción!');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function verificaInsumo(){
    var descripcion_insumo = $('[name=descripcion_insumo]').val();
    var tipo_insumo = $('[name=tipo_insumo]').val();
    var id_categoria = $('[name=id_categoria_insumo]').val();
    var iu = $('[name=iu]').val();
    var precio = $('[name=precio]').val();
    var unid_medida_insumo = $('[name=unid_medida_insumo]').val();
    var msj = '';

    if (descripcion_insumo == ''){
        msj+='\n Es necesario que ingrese una descripción al insumo';
    }
    if (tipo_insumo == '0' || tipo_insumo == null){
        msj+='\n Es necesario que seleccione un tipo_insumo';
    }
    if (id_categoria == '0' || id_categoria == null){
        msj+='\n Es necesario que seleccione una categoría';
    }
    if (iu == '0' || iu == null){
        msj+='\n Es necesario que elija un Indice Unificado';
    }
    if (precio == ''){
        msj+='\n Es necesario que ingrese el precio';
    }
    if (unid_medida_insumo == '0' || unid_medida_insumo == null){
        msj+='\n Es necesario que elija una unidad de medida';
    }
    return msj;
}
