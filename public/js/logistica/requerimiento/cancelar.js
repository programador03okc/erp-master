function cancelarRequerimiento() {
    // console.log("cancelar");
    document.getElementById('btnCopiar').setAttribute("disabled", true);
    $('#estado_doc').text('');

    // $('#body_detalle_requerimiento').html('<tr id="default_tr"><td></td><td colspan="7"> No hay datos registrados</td></tr>');
    $('[name=codigo]').val('');
    var btnEditarItem = document.getElementsByName("btnEditarItem");
    disabledControl(btnEditarItem, true);
    var btnAdjuntarArchivos = document.getElementsByName("btnAdjuntarArchivos");
    disabledControl(btnAdjuntarArchivos, false);
    var btnEliminarItem = document.getElementsByName("btnEliminarItem");
    disabledControl(btnEliminarItem, true);


    var btnImprimirRequerimiento = document.getElementsByName("btn-imprimir-requerimento-pdf");
    disabledControl(btnImprimirRequerimiento, true);
    var btnAdjuntosRequerimiento = document.getElementsByName("btn-adjuntos-requerimiento");
    disabledControl(btnAdjuntosRequerimiento, true);
    var btnTrazabilidadRequerimiento = document.getElementsByName("btn-ver-trazabilidad-requerimiento");
    disabledControl(btnTrazabilidadRequerimiento, true);

    limpiarInputsRequerimiento();

}

function limpiarInputsRequerimiento() {
    var elementsTextNumbre = document.querySelectorAll("input[type='text'],input[type='number'], textarea");
    for (var i = 0; i < elementsTextNumbre.length; i++) {
        elementsTextNumbre[i].value='';
    }
    var elementsSelect = document.querySelectorAll("select");
    for (var i = 0; i < elementsSelect.length; i++) {

        if(elementsSelect[i].name =='empresa' || elementsSelect[i].name =='rol_aprobante'){
            elementsSelect[i].value=0;
        }   
        if(elementsSelect[i].name =='sede'){
            while (elementsSelect[i].length > 0) {
                elementsSelect[i].remove(0);
            }
        }  

        if(elementsSelect[i].name =='moneda' || elementsSelect[i].name =='prioridad' || elementsSelect[i].name =='unidad[]'){
            elementsSelect[i].value=1;
        }   
    }


    let bodyDetalleRequerimiento= document.querySelector("tbody[id='body_detalle_requerimiento']");
    for (let i = 0; i < bodyDetalleRequerimiento.children.length; i++) {
        bodyDetalleRequerimiento.children[i].remove();
    }

    document.querySelector("table span[name='simboloMoneda']").textContent='S/';
    document.querySelector("table span[name='simbolo_moneda']").textContent='S/';
    document.querySelector("label[name='total']").textContent='';

    tempArchivoAdjuntoItemList = [];
    tempArchivoAdjuntoRequerimientoList = [];
    tempCentroCostoSelected=undefined;
    document.querySelector("span[name='cantidadAdjuntosRequerimiento']").textContent = tempArchivoAdjuntoRequerimientoList.length;

    // quitar info error de validación
    let divGroupHasError= document.querySelectorAll("div[class='form-group has-error']")
    if(divGroupHasError.length >0){
        for (let i = 0; i < divGroupHasError.length; i++) {
            divGroupHasError[i].querySelector("span[class='text-danger']").remove();
            divGroupHasError[i].classList.remove('has-error');
            
        }

    }

}