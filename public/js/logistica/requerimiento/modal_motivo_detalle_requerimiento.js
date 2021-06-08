function motivoModal(obj,indice){
    $('#modal-motivo-detalle-requerimiento').modal({
        show: true,
        backdrop: 'true'
    });
    
    document.querySelector("div[id='modal-motivo-detalle-requerimiento'] textarea[name='motivo']").value ="";
    document.querySelector("div[id='modal-motivo-detalle-requerimiento'] label[id='indice']").textContent = indice;

    if(data_item.length > 0){
        let descripcion = data_item[indice].des_item;
        document.querySelector("div[id='modal-motivo-detalle-requerimiento'] small[id='titulo-motivo']").textContent = descripcion;

    }
}


function agregarMotivo(){
    let motivo = document.querySelector("div[id='modal-motivo-detalle-requerimiento'] textarea[name='motivo']").value;
    let indiceSeleccionado = document.querySelector("div[id='modal-motivo-detalle-requerimiento'] label[id='indice']").textContent;

    if(indiceSeleccionado >= 0){
        if(motivo!='' && motivo !=null){
            data_item.forEach((element, index) => {
                if (index == indiceSeleccionado) {
                    data_item[index].motivo = motivo;
                }
            });
            componerTdItemDetalleRequerimiento();
            vista_extendida();
            $('#modal-motivo-detalle-requerimiento').modal('hide');
            // console.log(data_item);
        }else{
            alert("Debe ingresar un texto");
        }
    }else{
        alert("no se detecto un item seleccionado");
    }

}