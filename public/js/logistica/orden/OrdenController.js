class OrdenCtrl{
    constructor(ordenModel) {
        this.ordenModel = ordenModel;
    }
    init() {
        this.ordenView.init();
    }
    getTipoCambioCompra(fecha){
        return ordenModel.getTipoCambioCompra(fecha);

    }
    // limpiar tabla
    limpiarTabla(identificador){
        let nodeTbody = document.querySelector("table[id='" + identificador + "'] tbody");

        for(var i = nodeTbody.rows.length - 1; i > 0; i--)
        {
            nodeTbody.deleteRow(i);
        }   
    }

    updateInputStockComprometido(event){

    }






    eliminarItemDeObj(keySelected){
        let OperacionEliminar= false;
        if(keySelected.length >0){
            if(typeof detalleOrdenList =='undefined'){
                detalleOrdenList.forEach((element,index) => {
                    if(element.id == keySelected){
                        if(element.estado ==0){
                            detalleOrdenList.splice( index, 1 );
                            OperacionEliminar=true;
                        }else{
                            detalleOrdenList[index].estado=7;
                            OperacionEliminar=true;
                        }
                    }
                });
            }else{
                detalleOrdenList.forEach((element,index) => {
                    if(element.id == keySelected){
                        if(element.estado ==0){
                            detalleOrdenList.splice( index, 1 );
                            OperacionEliminar=true;
                        }else{
                            detalleOrdenList[index].estado=7;
                            OperacionEliminar=true;
                        }
                    }
                });
            } 
        } 
    
        if(OperacionEliminar==false){
            alert("hubo un error al intentar eliminar el item");
        }
    }

    // agregar nuevo producto
    getcatalogoProductos(){
    
        return ordenModel.getlistarItems();
    }


 


    verDetalleRequerimientoModalVincularRequerimiento(obj) {
       
    }

    obtenerDetalleRequerimientos(id){
        return ordenModel.obtenerDetalleRequerimientos(id);
    }

    obtenerDetalleRequerimientos(idRequerimiento){
        return ordenModel.obtenerDetalleRequerimientos(idRequerimiento);
    }

    anularOrden(id,sustento){
        return ordenModel.anularOrden(id,sustento);

    }

    obtenerRequerimiento(id){
        return ordenModel.obtenerRequerimiento(id);
    }


}
