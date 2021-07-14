
var detalleOrdenList=[];
var tablaListaRequerimientosParaVincular;
var iTableCounter = 1;
var oInnerTable;

class OrdenCtrl{
    constructor(OrdenView) {
        this.ordenView = OrdenView;
    }
    init() {
        this.ordenView.init();
    }
    getTipoCambioCompra(fecha){
        return ordenModel.getTipoCambioCompra(fecha);

    }
    // limpiar tabla
    limpiarTabla(identificador){
         // let nodeTbody = document.querySelector("table[id='" + identificador + "'] tbody");
        // if(nodeTbody!=null){
        //     while (nodeTbody.children.length > 0) {
        //         nodeTbody.removeChild(nodeTbody.lastChild);
        //     }
    
        // }
        let nodeTbody = document.querySelector("table[id='" + identificador + "'] tbody");

        for(var i = nodeTbody.rows.length - 1; i > 0; i--)
        {
            nodeTbody.deleteRow(i);
        }
    }

    obtenerRequerimiento(reqTrueList,tipoOrden){
        this.limpiarTabla('listaDetalleOrden');
        let idTipoItem = 0;
        let idTipoOrden = 0;
        if(tipoOrden== 'COMPRA'){
            idTipoItem=1;
            idTipoOrden=2;
        }else if(tipoOrden =='SERVICIO'){
            idTipoItem=2;
            idTipoOrden=3;

        }
        detalleOrdenList=[];
        $.ajax({
            type: 'POST',
            url: 'detalle-requerimiento-orden',
            data:{'requerimientoList':reqTrueList},
            dataType: 'JSON',
            success: function(response){
                response.det_req.forEach(element => {
                    if(element.cantidad >0 && (![28,5].includes(element.estado)) && element.id_tipo_item==idTipoItem){
                        detalleOrdenList.push(
                            {
                                'id': element.id,
                                'id_detalle_requerimiento': element.id_detalle_requerimiento,
                                'codigo_item': element.codigo_item,
                                'id_producto':element.id_producto,
                                'id_item': element.id_item,
                                'id_tipo_item': element.id_tipo_item,
                                'id_requerimiento':element.id_requerimiento,
                                'codigo_requerimiento': element.codigo_requerimiento,
                                'cantidad': element.cantidad,
                                'cantidad_a_comprar': element.cantidad_a_comprar?element.cantidad_a_comprar:element.cantidad,
                                'descripcion_producto':element.descripcion,
                                'descripcion_adicional':element.descripcion_adicional,
                                'estado': element.estado,
                                'fecha_registro':element.fecha_registro,
                                'id_unidad_medida':element.id_unidad_medida,
                                'lugar_entrega': element.lugar_entrega,
                                'observacion': element.observacion,
                                'part_number': element.part_number,
                                'precio_unitario':element.precio_unitario,
                                'stock_comprometido':element.stock_comprometido,
                                'subtotal':element.subtotal,
                                'unidad_medida':element.unidad_medida
                            }
                        );
                        if(detalleOrdenList.length ==0){
                            alert("No puede generar una orden sin antes agregar item(s) base");
        
                        }else{
                            ordenView.loadHeadRequerimiento(response.requerimiento[0],idTipoOrden);
                            ordenView.listar_detalle_orden_requerimiento(detalleOrdenList);
                            changeStateInput('form-crear-orden-requerimiento', false);
                            changeStateButton('editar');
                            
                        }
                    }
                });
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
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

    openModalEliminarItemOrden(obj){
        var ask = confirm('Esta seguro que quiere anular el item ?');
        if (ask == true){
            ordenView.eliminadoFilaTablaListaDetalleOrden(obj);
            let id= obj.dataset.id;
            if(id.length >0){

                    detalleOrdenList = detalleOrdenList.filter((item, i) => item.id != id);

                this.calcTotalOrdenDetalleList();
            }else{
                alert('Hubo un problema al intentar anular el item');
            }
        }else{
            return false;
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

    selectItem(){
        let data = {
            'id': this.makeId(),
            'cantidad': 1,
            'cantidad_a_comprar': 1,
            'codigo_item': null,
            'codigo_producto': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='codigo']").textContent,
            'codigo_requerimiento': "",
            'descripcion_adicional': null,
            'descripcion_producto': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='descripcion']").textContent,
            'estado': 0,
            'garantia': null,
            'id_detalle_orden': null,
            'id_detalle_requerimiento': null,
            'id_item': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_item']").textContent,
            'id_tipo_item':1,
            'id_producto': parseInt(document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent),
            'id_requerimiento': null,
            'id_unidad_medida': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_unidad_medida']").textContent,
            'lugar_despacho': null,
            'part_number': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='part_number']").textContent +  parseInt(document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent)>0? parseInt(document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='id_producto']").textContent):' (Sin mapear)',
            'precio_unitario': 0,
            'id_moneda': 1,
            'stock_comprometido': null,
            'subtotal': 0,
            'tiene_transformacion': false,
            'unidad_medida': document.querySelector("div[id='modal-catalogo-items'] div[class='modal-footer'] label[id='unidad_medida']").textContent
            };
            this.agregarProductoADetalleOrdenList(data);
        
            $('#modal-catalogo-items').modal('hide');
    }

    makeId (){
        let ID = "";
        let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for ( var i = 0; i < 12; i++ ) {
          ID += characters.charAt(Math.floor(Math.random() * 36));
        }
        return ID;
    }

    agregarProductoADetalleOrdenList(data){
        if(typeof detalleOrdenList != 'undefined'){
            detalleOrdenList.push(data);
            ordenView.listar_detalle_orden_requerimiento(detalleOrdenList);
    
        }else{
            alert("Hubo un problema al agregar el producto al Listado");
        }
    }

    calcTotalDetalleOrden(keySelected){
        let sizeInputTotal = document.querySelectorAll("div[name='subtotal']").length;
        for (let index = 0; index < sizeInputTotal; index++) {
            let key = document.querySelectorAll("div[name='subtotal']")[index].dataset.key;
            if(key == keySelected){
                let precio = document.querySelectorAll("input[name='precio']")[index].value?document.querySelectorAll("input[name='precio']")[index].value:0;
                let cantidad =document.querySelectorAll("input[name='cantidad_a_comprar']")[index].value;
                let subtotal = (parseFloat(precio) * parseFloat(cantidad)).toFixed(2);
                document.querySelectorAll("div[name='subtotal']")[index].textContent=subtotal;
                    detalleOrdenList.forEach((element,index) => {
                        if(element.id == key){
                                detalleOrdenList[index].subtotal = subtotal;
                            
                        }
                    });
                
            }
        }
        
        this.calcTotalOrdenDetalleList();
    
    }

    calcTotalOrdenDetalleList(hasIGV =null){
        
        let sizeInputTotal = document.querySelectorAll("input[name='subtotal']").length;
        let total =0;
        let simbolo_moneda_selected = document.querySelector("select[name='id_moneda']")[document.querySelector("select[name='id_moneda']").selectedIndex].dataset.simboloMoneda;

        if (hasIGV == null){
            hasIGV= document.querySelector("input[name='incluye_igv']").checked;
        }

        if(hasIGV == true){
            for (let index = 0; index < sizeInputTotal; index++) {
                let num = document.querySelectorAll("input[name='subtotal']")[index].value?document.querySelectorAll("input[name='subtotal']")[index].value:0;
                total += parseFloat(num);
            }
    
            let montoNeto= (Math.round(total * 100) / 100).toFixed(2);
            let igv = (Math.round((total*0.18) * 100) / 100).toFixed(2);
            let montoTotal= (Math.round((parseFloat(montoNeto)+parseFloat(igv)) * 100) / 100).toFixed(2)
            document.querySelector("tfoot span[name='simboloMoneda']").textContent= simbolo_moneda_selected;
            document.querySelector("label[name='montoNeto']").textContent=montoNeto;
            document.querySelector("label[name='igv']").textContent= igv;
            document.querySelector("label[name='montoTotal']").textContent= montoTotal;
        }else if(hasIGV == false){
            for (let index = 0; index < sizeInputTotal; index++) {
                let num = document.querySelectorAll("input[name='subtotal']")[index].value?document.querySelectorAll("input[name='subtotal']")[index].value:0;
                total += parseFloat(num);
            }

            let montoNeto= (Math.round(total * 100) / 100).toFixed(2);
            let montoTotal= (Math.round((parseFloat(montoNeto)) * 100) / 100).toFixed(2)
            document.querySelector("tfoot span[name='simboloMoneda']").textContent= simbolo_moneda_selected;
            document.querySelector("label[name='montoNeto']").textContent=montoNeto;
            document.querySelector("label[name='igv']").textContent= '0.00';
            document.querySelector("label[name='montoTotal']").textContent= montoTotal;
        }



    
    }

    // guardar orden
    countRequirementsInObj(){
        let idRequerimientoList=[];
        let size=0;
        listCheckReq.forEach(element => {
            if(element.stateCheck ==true){
                idRequerimientoList.push(element.id_req);
            } 
        });
        let idRequerimientoListUnique = Array.from(new Set(idRequerimientoList));
        // console.log(idRequerimientoList);
        // console.log(idRequerimientoListUnique);
        size = idRequerimientoListUnique.length;
        return size;
    }

    validaOrdenRequerimiento(){
        var codigo_orden = $('[name=codigo_orden]').val();
        var id_proveedor = $('[name=id_proveedor]').val();
        var plazo_entrega = $('[name=plazo_entrega]').val();
        var id_tp_documento = $('[name=id_tp_documento]').val();
        var msj = '';
        if (codigo_orden == ''){
            msj+='\n Es necesario que ingrese un código de orden Softlink';
        }
        if (id_proveedor == ''){
            msj+='\n Es necesario que seleccione un Proveedor';
        }
        if (id_tp_documento!= '3' && plazo_entrega == ''){
            msj+='\n Es necesario que ingrese un plazo de entrega';
        }
        let cantidadInconsistenteInputPrecio=0;
        let cantidadInconsistenteMapeoProducto=0;
        // let inputPrecio= document.querySelectorAll("table[id='listaDetalleOrden'] input[name='precio']");
        detalleOrdenList.forEach((element)=>{
            if(!parseFloat(element.precio_unitario) >0  && element.estado !=7){
                cantidadInconsistenteInputPrecio++;
            }
            if((element.id_tipo_item==1) && (element.id_producto =='' || element.id_producto ==null)){
                cantidadInconsistenteMapeoProducto++;
            }

        })
        if(cantidadInconsistenteInputPrecio>0){
            msj+='\n Es necesario que ingrese un precio / precio mayor a cero';
        }
        if(cantidadInconsistenteMapeoProducto>0){
            msj+='\n Tiene productos sin mapear';
        }

        let cantidadInconsistenteInputCantidadAComprar=0;
        let inputCantidadAComprar= document.querySelectorAll("table[id='listaDetalleOrden'] input[name='cantidad_a_comprar']");
        inputCantidadAComprar.forEach((element)=>{
            if(element.value == null || element.value =='' || element.value ==0){
                cantidadInconsistenteInputCantidadAComprar++;
            }
        })
        if(cantidadInconsistenteInputCantidadAComprar>0){
            msj+='\n Es necesario que ingrese una cantidad a comprar / cantidad a comprar mayor a cero';
    
        }           
        return  msj;
    }

    guardar_orden_requerimiento(action,data){
        if (action == 'register'){
            var msj = this.validaOrdenRequerimiento();
            if (msj.length > 0){
                alert(msj);
                // changeStateButton('editar');
                // changeStateButton('guardar');
                // $('#form-crear-orden-requerimiento').attr('type', 'register');
                // changeStateInput('form-crear-orden-requerimiento', false);
            } else{
                $.ajax({
                    type: 'POST',
                    url: 'guardar',
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        // console.log(response);
                        if (response > 0){
                            alert('Orden de registrada con éxito');
                            changeStateButton('guardar');
                            $('#form-crear-orden-requerimiento').attr('type', 'register');
                            changeStateInput('form-crear-orden-requerimiento', true);
    
                            sessionStorage.removeItem('reqCheckedList');
                            sessionStorage.removeItem('tipoOrden');
                            window.open("generar-orden-pdf/"+response, '_blank');
    
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        
        }else if(action == 'edition'){
            $.ajax({
                type: 'POST',
                url: 'actualizar',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    // console.log(response);
                    if (response > 0){
                        alert("Orden Actualizada");
                        changeStateButton('guardar');
                        $('#form-crear-orden-requerimiento').attr('type', 'register');
                        changeStateInput('form-crear-orden-requerimiento', true);
                    }
                }
            }).fail( function(jqXHR, textStatus, errorThrown){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });   
        }else{
            alert("Hubo un error en la acción de la botonera, el action no esta definido");
        }
    }


    getRequerimientosPendientes(id_empresa=null,id_sede=null) {
        return ordenModel.getRequerimientosPendientes(id_empresa,id_sede);
        // return ordenesData;
    }


    verDetalleRequerimientoModalVincularRequerimiento(obj) {
        let tr = obj.closest('tr');
        var row = tablaListaRequerimientosParaVincular.row(tr);
        var id = obj.dataset.idRequerimiento;
        if (row.child.isShown()) {
            //  This row is already open - close it
            row.child.hide();
            tr.classList.remove('shown');
        }
        else {
            // Open this row
            //    row.child( format(iTableCounter, id) ).show();
            ordenCtrl.buildFormatModalVincularRequerimiento(iTableCounter, id, row);
            tr.classList.add('shown');
            // try datatable stuff
            oInnerTable = $('#listaRequerimientosParaVincular_' + iTableCounter).dataTable({
                //    data: sections, 
                autoWidth: true,
                deferRender: true,
                info: false,
                lengthChange: false,
                ordering: false,
                paging: false,
                scrollX: false,
                scrollY: false,
                searching: false,
                columns: [
                ]
            });
            iTableCounter = iTableCounter + 1;
        }
    }

    buildFormatModalVincularRequerimiento(table_id, id, row) {
        ordenModel.obtenerDetalleRequerimientos(id).then(function(res) {
            ordenView.construirDetalleRequerimientoModalVincularRequerimiento(table_id,row,res);
        }).catch(function(err) {
            console.log(err)
        })
    }


    vincularRequerimiento(idRequerimiento){
        let i=0;
        ordenModel.obtenerDetalleRequerimientos(idRequerimiento).then(function(res) {
            res.forEach((element) => {
                i++;
                ordenCtrl.agregarProductoADetalleOrdenList({
                    'id': ordenCtrl.makeId(),
                    'cantidad': 1,
                    'cantidad_a_comprar': 1,
                    'codigo_item': null,
                    'codigo_producto': element.producto_codigo,
                    'codigo_requerimiento': element.codigo_requerimiento,
                    'descripcion_adicional': null,
                    'descripcion_producto': element.producto_descripcion !=null? element.producto_descripcion: element.descripcion,
                    'estado': 0,
                    'garantia': null,
                    'id_detalle_orden': null,
                    'id_detalle_requerimiento': element.id_detalle_requerimiento,
                    'id_item':null,
                    'id_tipo_item':1,
                    'id_producto': element.id_producto,
                    'id_requerimiento': element.id_requerimiento,
                    'id_unidad_medida': element.id_unidad_medida,
                    'lugar_despacho': null,
                    'part_number':(element.part_number!=null? element.part_number:'')+(!element.id_producto>0 ?'(Sin mapear)':''),
                    'precio_unitario': 0,
                    'id_moneda': 1,
                    'stock_comprometido': null,
                    'subtotal': 0,
                    'tiene_transformacion': false,
                    'unidad_medida': element.abreviatura
                    });
        });

        if(i>0){
            ordenView.estadoVinculoRequerimiento({'mensaje':`Se agregó ${i} Item(s) a la orden`,'estado':'200'})
            
        }else{
            ordenView.estadoVinculoRequerimiento({'mensaje':`No se puedo agregar Item(s) a la orden`,'estado':'204'})

        }



        }).catch(function(err) {
            console.log(err)
        })

    }

}

const ordenCtrl = new OrdenCtrl(ordenView);

window.onload = function() {
    ordenView.init();
};