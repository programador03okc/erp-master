function cancelarRequerimiento(){
    // console.log("cancelar");
    document.getElementById('btnCopiar').setAttribute("disabled",true);
    $('#estado_doc').text('');

    $('#body_detalle_requerimiento').html('<tr id="default_tr"><td></td><td colspan="7"> No hay datos registrados</td></tr>');
    $('[name=codigo]').val('');
    var btnEditarItem = document.getElementsByName("btnEditarItem");
        disabledControl(btnEditarItem,true);
    var btnAdjuntarArchivos = document.getElementsByName("btnAdjuntarArchivos");
        disabledControl(btnAdjuntarArchivos,false);
    var btnEliminarItem = document.getElementsByName("btnEliminarItem");
        disabledControl(btnEliminarItem,true);
}