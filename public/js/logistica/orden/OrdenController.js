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
 

    changeSede(obj){
        var id_empresa = obj.options[obj.selectedIndex].getAttribute('data-id-empresa');
        var id_ubigeo = obj.options[obj.selectedIndex].getAttribute('data-id-ubigeo');
        var ubigeo_descripcion = obj.options[obj.selectedIndex].getAttribute('data-ubigeo-descripcion');
        var direccion = obj.options[obj.selectedIndex].getAttribute('data-direccion');
        ordenView.changeLogoEmprsa(id_empresa);
        this.llenarUbigeo(direccion,id_ubigeo,ubigeo_descripcion);
    }

    llenarUbigeo(direccion,id_ubigeo,ubigeo_descripcion){
        document.querySelector("input[name='direccion_destino']").value=direccion;
        document.querySelector("input[name='id_ubigeo_destino']").value=id_ubigeo;
        document.querySelector("input[name='ubigeo_destino']").value=ubigeo_descripcion;
    }



    updateInObjCantidadAComprar(id,valor){
        detalleOrdenList.forEach((element,index) => {
                if(element.id == id){
                detalleOrdenList[index].cantidad_a_comprar = valor;
                }
        });
    }

    updateInputPrecio(event){
        let nuevoValor =event.target.value;
        let id = event.target.dataset.id;
        this.updateInObjPrecioReferencial(id,nuevoValor);
        this.calcTotalDetalleRequerimiento(id);
    }

    updateInObjPrecioReferencial(id,valor){
        
        detalleOrdenList.forEach((element,index) => {
            if(element.id == id){
                detalleOrdenList[index].precio_unitario = valor;
            }

        });
    }

    calcTotalDetalleRequerimiento(id){
        let simbolo_moneda_selected = document.querySelector("select[name='id_moneda']")[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda;
        let sizeInputTotal = document.querySelectorAll("input[name='subtotal']").length;
        for (let index = 0; index < sizeInputTotal; index++) {
            let idElement = document.querySelectorAll("input[name='subtotal']")[index].dataset.id;
            if(idElement == id){
                let precio = document.querySelectorAll("input[name='precio']")[index].value?document.querySelectorAll("input[name='precio']")[index].value:0;
                let cantidad =( document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value)>0?document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value:document.querySelectorAll("input[name='cantidad']")[index].value;
                let calSubtotal =(parseFloat(precio) * parseFloat(cantidad));
                
                let subtotal = formatDecimalDigitos(calSubtotal,2);
                // console.log(subtotal);
                document.querySelectorAll("input[name='subtotal']")[index].value=subtotal;
                ordenCtrl.updateInObjSubtotal(id,subtotal);
            }
        }
        let total =0;
        for (let index = 0; index < sizeInputTotal; index++) {
            let num = document.querySelectorAll("input[name='subtotal']")[index].value?document.querySelectorAll("input[name='subtotal']")[index].value:0;
            total += parseFloat(num);
        }

        let montoNeto= total;
        let igv = (total*0.18);
        let montoTotal=  parseFloat(montoNeto)+parseFloat(igv);
        document.querySelector("tfoot span[name='simboloMoneda']").textContent= simbolo_moneda_selected;
        document.querySelector("label[name='montoNeto']").textContent=Util.formatoNumero(montoNeto, 2);
        document.querySelector("label[name='igv']").textContent= Util.formatoNumero(igv, 3);
        document.querySelector("label[name='montoTotal']").textContent= Util.formatoNumero(montoTotal, 2);
    }

    updateInObjSubtotal(id,valor){
        detalleOrdenList.forEach((element,index) => {
            if(element.id == id){
                detalleOrdenList[index].subtotal = valor;
            }
    });
    }

    updateInputStockComprometido(event){

    }
    updateInputCantidadAComprar(event){
        let nuevoValor =event.target.value;
        let idSelected = event.target.dataset.id;
        let sizeInputCantidad = document.querySelectorAll("span[name='cantidad']").length;
        let cantidad =0;
        for (let index = 0; index < sizeInputCantidad; index++) {
            let id = document.querySelectorAll("span[name='cantidad']")[index].dataset.id;
            if(id == idSelected){
                cantidad = document.querySelectorAll("span[name='cantidad']")[index].textContent;
                if(parseFloat(nuevoValor) >0){                
                    // actualizar datadetreq cantidad
                    ordenCtrl.updateInObjCantidadAComprar(idSelected,nuevoValor);
                    ordenCtrl.calcTotalDetalleRequerimiento(idSelected);
    
                    // console.log(detalleOrdenList);
                    // 
                }
                
                // if(parseFloat(nuevoValor) > parseFloat(cantidad)){
                //     alert("La cantidad a comprar no puede ser mayor a la cantidad `solicitada");
                //     document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value= cantidad;
                //     updateInObjCantidadAComprar(rowNumberSelected,idRequerimientoSelected,idDetalleRequerimientoSelected,cantidad);
    
                // }
            }
        }
    }

    updateInputSubtotal(event){
        let nuevoValor =event.target.value;
        let idSelected = event.target.dataset.id;
        let cantidadAComprar =0;
        let precio =0;
        let sizeInputCantidad = document.querySelectorAll("span[name='cantidad']").length;
        for (let index = 0; index < sizeInputCantidad; index++) {
            let id = document.querySelectorAll("input[name='cantidad_a_comprar']")[index].dataset.id;
            if(id == idSelected){
                cantidadAComprar = document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value;
                precio = document.querySelectorAll("input[name='precio']")[index].value;
                if(parseFloat(nuevoValor) >0){                
                    // actualizar datadetreq cantidad
                    let nuevoPrecio= (nuevoValor/cantidadAComprar)
                    this.updateInObjPrecioReferencial(id,nuevoPrecio);
                    document.querySelectorAll("input[name='precio']")[index].value=nuevoPrecio;
                    ordenCtrl.calcTotalDetalleRequerimiento(idSelected);

                }
                
 
            }
        }
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


    getRequerimientosPendientes(id_empresa=null,id_sede=null) {
        return ordenModel.getRequerimientosPendientes(id_empresa,id_sede);
        // return ordenesData;
    }


    verDetalleRequerimientoModalVincularRequerimiento(obj) {
       
    }

    obtenerDetalleRequerimientos(id){
        return ordenModel.obtenerDetalleRequerimientos(id);
    }

    obtenerDetalleRequerimientos(idRequerimiento){
        return ordenModel.obtenerDetalleRequerimientos(idRequerimiento);
    }

    anularOrden(id){
        return ordenModel.anularOrden(id);

    }




}
