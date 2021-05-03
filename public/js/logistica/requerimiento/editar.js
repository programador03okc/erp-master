function editRequerimiento(){
    // document.getElementsByName('concepto')[0].disabled=true;
    let id_estado_doc = document.getElementsByName('id_estado_doc')[0].value;
    // console.log(id_estado_doc);
    let cantidad_aprobaciones = $('[name=cantidad_aprobaciones]').val();

    if(id_estado_doc >1 && cantidad_aprobaciones >0){
        document.getElementsByName('descripcion_item')[0].disabled=true;
        // document.getElementById('basic-addon7').disabled=true;
        document.getElementsByName('unidad_medida_item')[0].disabled=true;
        document.getElementsByName('cantidad_item')[0].disabled=true;
        document.getElementsByName('precio_ref_item')[0].disabled=true;
    
    }
    // console.log("editando..")
    var btnEditarItem = document.getElementsByName("btnEditarItem");
    disabledControl(btnEditarItem,false);
    var btnAdjuntarArchivos = document.getElementsByName("btnAdjuntarArchivos");
    disabledControl(btnAdjuntarArchivos,false);
    var btnEliminarItem = document.getElementsByName("btnEliminarItem");
        disabledControl(btnEliminarItem,false);
    var btnEliminarAdjuntoRequerimiento = document.getElementsByName("btnEliminarAdjuntoRequerimiento");
        disabledControl(btnEliminarAdjuntoRequerimiento,false);
    return null;
}