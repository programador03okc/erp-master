var itemsParaCompraList=[]
var reqTrueList=[]
var listCheckReq=[]
var infoStateInput = [];
var tempDetalleItemsParaCompraCC = [];

//================ Controller ==================
class RequerimientoPendienteCtrl{
    constructor(RequerimientoPendienteView) {
        this.requerimientoPendienteView = RequerimientoPendienteView;
    }
    init() {
        this.requerimientoPendienteView.init();
    }

    getRequerimientosPendientes(id_empresa=null,id_sede=null) {
        return requerimientoPendienteModel.getRequerimientosPendientes(id_empresa,id_sede);
        // return ordenesData;
    }
    // filtros
    getDataSelectSede(id_empresa = null){
        return requerimientoPendienteModel.getDataSelectSede(id_empresa);
    }

    // limpiar tabla
    limpiarTabla(identificador){
        const customTabla = new CustomTabla(identificador); //CustomTabla.js
        customTabla.limpiarTabla;
    }

    //clean character
    cleanCharacterReference(text){
        let str = text;
        let characterReferenceList=['&nbsp;','nbsp;','&amp;','amp;','NBSP;',"&lt;",/(\r\n|\n|\r)/gm];
        characterReferenceList.forEach(element => {
            while (str.search(element) > -1) {
                str=  str.replace(element,"");
    
            }
        });
            return str.trim();
    }
    // check
    controlListCheckReq(id,stateCheck){
        if (stateCheck.length == 0) {
            let newCheckReq = {
                id_req: id,
                stateCheck: stateCheck,
            };
            listCheckReq.push(newCheckReq);
            requerimientoPendienteView.statusBtnGenerarOrden();
        }else{
            let arrIdReq=[];
            let newCheckReq = {
                id_req: id,
                stateCheck: stateCheck,
            };
        
            listCheckReq.map(value => {
                    arrIdReq.push(value.id_req);
            });
        
            if (arrIdReq.includes(newCheckReq.id_req) == true) {
                // actualiza
                listCheckReq.map(value => {
                    if (value.id_req == newCheckReq.id_req) {
                        value.stateCheck = newCheckReq.stateCheck
                        // console.log(newCheckReq.stateCheck);
                    }
                });
            } else {
                listCheckReq.push(newCheckReq)
            }
        
            requerimientoPendienteView.statusBtnGenerarOrden();
        }
    }

    // atender con almacén

    openModalAtenderConAlmacen(obj){
        $('#modal-atender-con-almacen').modal({
            show: true,
            backdrop: 'true'
        });

        return requerimientoPendienteModel.getDataItemsRequerimientoParaAtenderConAlmacen(obj.dataset.idRequerimiento);
    }

    updateSelectAlmacenAAtender(obj,event){
        let idValor = event.target.value;
        // let textValor = event.target.options[event.target.selectedIndex].textContent;
        let indiceSelected = event.target.dataset.indice;
        itemsParaAtenderConAlmacenList.forEach((element, index) => {
            if (index == indiceSelected) {
                itemsParaAtenderConAlmacenList[index].id_almacen_reserva = parseInt(idValor);
            }
        });
        // console.log(itemsParaAtenderConAlmacenList);
    }

    updateObjCantidadAAtender(indice, valor){
        itemsParaAtenderConAlmacenList.forEach((element, index) => {
            if (index == indice) {
                itemsParaAtenderConAlmacenList[index].cantidad_a_atender = valor;
            }
        });
    }

    updateInputCantidadAAtender(obj,event){
        let nuevoValor = event.target.value;
        let indiceSelected = event.target.dataset.indice;
        let cantidad = event.target.parentNode.parentNode.children[5].textContent;
        if(parseInt(nuevoValor) > parseInt(cantidad) || parseInt(nuevoValor) <= 0 ){
    
            obj.parentNode.parentNode.querySelector("input[name='cantidad_a_atender']").value= cantidad;
            itemsParaAtenderConAlmacenList.forEach((element, index) => {
                if (index == indiceSelected) {
                    itemsParaAtenderConAlmacenList[index].cantidad_a_atender = cantidad;
                }
            });
        }else{
            itemsParaAtenderConAlmacenList.forEach((element, index) => {
                if (index == indiceSelected) {
                    itemsParaAtenderConAlmacenList[index].cantidad_a_atender = nuevoValor;
                }
            });
        }
    }

    guardarAtendidoConAlmacen(){
        var newItemsParaAtenderConAlmacenList = [];
        var itemsBaseList = [];
        itemsBaseList = itemsParaAtenderConAlmacenList.filter(function( obj ) {
            return (obj.tiene_transformacion ==false);
        });
        // console.log(itemsBaseList);
        newItemsParaAtenderConAlmacenList = itemsParaAtenderConAlmacenList.filter(function( obj ) {
            return (obj.id_almacen_reserva >0) && (obj.cantidad_a_atender >0);
        });
        // console.log(newItemsParaAtenderConAlmacenList);
        var hasCantidadNoPermitida = false;
        newItemsParaAtenderConAlmacenList.forEach(element => {
            if(element.cantidad_a_atender > element.cantidad){
                alert("No puede reservar una 'cantidad a atender' mayor a la 'cantidad' ");
                hasCantidadNoPermitida=true;
            } 
        });
        if(hasCantidadNoPermitida== false){
            if(newItemsParaAtenderConAlmacenList.length >0){
                return requerimientoPendienteModel.guardarAtendidoConAlmacen({'lista_items_reservar':newItemsParaAtenderConAlmacenList,'lista_items_base':itemsBaseList});

            }else{
                alert("seleccione un almacén y especifique una cantidad a atender mayor a cero.");
            }
    
        }

    }

    getDataItemsRequerimientoParaAtenderConAlmacen(id_requerimiento){
        return requerimientoPendienteModel.getDataItemsRequerimientoParaAtenderConAlmacen(id_requerimiento);

    }

    // Agregar item base
    openModalAgregarItemBase(obj){
        let id_requerimiento = obj.dataset.idRequerimiento;
        reqTrueList=[id_requerimiento];
        itemsParaCompraList=[];


        this.limpiarTabla('ListaItemsParaComprar');

        $('#modal-agregar-items-para-compra').modal({
            show: true,
            backdrop: 'static'
        });

        
        return requerimientoPendienteModel.tieneItemsParaCompra(reqTrueList).then(function(res) {
            itemsParaCompraList= res.data;
            requerimientoPendienteView.componerTdItemsParaCompra(res.data,res.categoria,res.subcategoria,res.clasificacion,res.moneda,res.unidad_medida);
        }).catch(function(err) {
            console.log(err)
        })

    }

    cleanPartNumbreCharacters(data){
        data.forEach((element,index )=> {
            if(element.part_no !=null || element.part_no != undefined){
                data[index].part_no =requerimientoPendienteCtrl.cleanCharacterReference(element.part_no) ;
            }
        });
        return data;
    }

    getDataListaItemsCuadroCostosPorIdRequerimiento(){
        return requerimientoPendienteModel.getDataListaItemsCuadroCostosPorIdRequerimiento(reqTrueList).then(function(response) {
            tempDetalleItemsParaCompraCC= requerimientoPendienteCtrl.cleanPartNumbreCharacters(response.data);
        }).catch(function(err) {
            console.log(err)
        })
        // return requerimientoPendienteModel.getDataListaItemsCuadroCostosPorIdRequerimiento(reqTrueList);
    }

    getDataListaItemsCuadroCostosPorIdRequerimientoPendienteCompra(){

       return requerimientoPendienteModel.getDataListaItemsCuadroCostosPorIdRequerimientoPendienteCompra(reqTrueList).then(function(response) {
            if (response.status == 200) {
                let detalleItemsParaCompraCCPendienteCompra =  requerimientoPendienteCtrl.cleanPartNumbreCharacters(response.data);
                requerimientoPendienteView.llenarTablaDetalleCuadroCostos(detalleItemsParaCompraCCPendienteCompra);
            }
        }).catch(function(err) {
            console.log(err)
        })
    }

    guardarItemParaCompraEnCatalogo(obj,indice){

    }

    eliminarItemDeListadoParaCompra(indice){
        itemsParaCompraList = (itemsParaCompraList).filter((item, i) => i !== indice);
        this.validarObjItemsParaCompra();
    
    }

    validarObjItemsParaCompra(){
        infoStateInput = [];
        if ((itemsParaCompraList).length > 0) {
            
            (itemsParaCompraList).forEach(element => {
                if (element.id_producto == '' || element.id_producto == null) {
                    infoStateInput.push('Guardar item');
                }
                if (element.id_unidad_medida == '' || element.id_unidad_medida == null) {
                    infoStateInput.push('Completar Unidad de Medida');
                }
                if (element.cantidad == '' || element.cantidad == null) {
                    infoStateInput.push('Completar Cantidad');
                }
    
            });
    
            if (infoStateInput.length > 0) {
    
                document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").setAttribute('title', 'Falta: ' + infoStateInput.join());
                document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").setAttribute('disabled', true);
            } else {
                document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").setAttribute('title', 'Siguiente');
                document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").removeAttribute('disabled');
    
            }
        }
    }

    retornarItemAlDetalleCC(id){

    }
    
    procesarItemParaCompraDetalleCuadroCostos(obj,id){
        let detalleItemsParaCompraCCSelected = '';
 

        // console.log(tempDetalleItemsParaCompraCC);
        tempDetalleItemsParaCompraCC.forEach(element => {
            if (element.id == id) {
                detalleItemsParaCompraCCSelected = element;
            }
        });
        // mostrarCatalogoItems();
        // console.log(tempDetalleItemsParaCompraCC);
    
        let data_item_CC_selected = {
            'id': detalleItemsParaCompraCCSelected.id?detalleItemsParaCompraCCSelected.id:null,
            'id_cc_am_filas': detalleItemsParaCompraCCSelected.id_cc_am_filas?detalleItemsParaCompraCCSelected.id_cc_am_filas:null,
            'id_cc_venta_filas': detalleItemsParaCompraCCSelected.id_cc_venta_filas?detalleItemsParaCompraCCSelected.id_cc_venta_filas:null,
            'id_item': "",
            'id_producto': "",
            'id_tipo_item': "1",
            'id_cc_am': detalleItemsParaCompraCCSelected.id_cc_am?detalleItemsParaCompraCCSelected.id_cc_am:null,
            'id_cc_venta': detalleItemsParaCompraCCSelected.id_cc_venta?detalleItemsParaCompraCCSelected.id_cc_venta:null,
            'part_number': detalleItemsParaCompraCCSelected.part_no,
            'descripcion': requerimientoPendienteCtrl.cleanCharacterReference(detalleItemsParaCompraCCSelected.descripcion),
            'alm_prod_codigo': "",
            'categoria': "",
            'clasificacion': "NUEVO",
            'codigo_item': "",
            'id_categoria': '',
            'id_clasif': 5,
            'id_subcategoria': '',
            'id_unidad_medida': 30,
            'unidad_medida': "Caja",
            'subcategoria': "",
            'id_moneda': 1,
            'cantidad': detalleItemsParaCompraCCSelected.cantidad,
            'precio': "",
            'tiene_transformacion': false
    
        };

        this.buscarItemEnCatalogo(data_item_CC_selected).then(function (data) {
            // Run this when your request was successful
            if (data.length > 0) {
                if (data.length == 1) {
                    // console.log(data)
                    // console.log(data[0]);
                    data[0].id = data_item_CC_selected.id;
                    data[0].id_cc_am_filas = data_item_CC_selected.id_cc_am_filas;
                    data[0].id_cc_venta_filas = data_item_CC_selected.id_cc_venta_filas;
                    data[0].cantidad = data_item_CC_selected.cantidad;
                    data[0].id_cc_am = data_item_CC_selected.id_cc_am;
                    data[0].id_cc_venta = data_item_CC_selected.id_cc_venta;
                    data[0].precio = '';
                    data[0].tiene_transformacion = false;
    
                    if (data[0].id_moneda == null) {
                        data[0].id_moneda = 1;
                        data[0].moneda = 'Soles';
                    }
                    // console.log(data[0]);
                    (itemsParaCompraList).push(data[0]);
                    requerimientoPendienteCtrl.quitarItemDetalleCuadroCostosDeTabla(obj,id);
    
                    requerimientoPendienteCtrl.agregarItemATablaListaItemsParaCompra(itemsParaCompraList);
                }
                if(data.length >1){
                    alert("La busqueda a tenido más de una coincidencia");
                    // console.log(data);
    
                }
            } else {
                (itemsParaCompraList).push(data_item_CC_selected);
                requerimientoPendienteCtrl.quitarItemDetalleCuadroCostosDeTabla(obj,id);
    
                requerimientoPendienteCtrl.agregarItemATablaListaItemsParaCompra(itemsParaCompraList);
    
                alert('No se encontró el producto seleccionado en el catalogo');
            }
     
        }).catch(function (err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })
    }

    buscarItemEnCatalogo(data){
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                data: data,
                url: `buscar-item-catalogo`,
                dataType: 'JSON',
                success(response) {
                    resolve(response) // Resolve promise and go to then() 
                },
                error: function (err) {
                    reject(err) // Reject the promise and go to catch()
                }
            });
        });
    }

    quitarItemDetalleCuadroCostosDeTabla(obj,id){
        if((itemsParaCompraList).length >0){
            (itemsParaCompraList).forEach(element => {
                if(element.id == id){
                    obj.parentNode.parentNode.remove();
                }
            });
        }else{
            alert("no se agrego correctamente el item base");
        }
    
    }

    agregarItemATablaListaItemsParaCompra(data){
        if (dataSelect.length > 0) {
            requerimientoPendienteView.componerTdItemsParaCompra(data, dataSelect[0].categoria, dataSelect[0].subcategoria, dataSelect[0].clasificacion, dataSelect[0].moneda, dataSelect[0].unidad_medida);
        } else {
            getDataAllSelect().then(function (response) {
                if (response.length > 0) {
                    dataSelect = response;
                    requerimientoPendienteView.componerTdItemsParaCompra(data, response[0].categoria, response[0].subcategoria, response[0].clasificacion, response[0].moneda, response[0].unidad_medida);
                } else {
                    alert('No se pudo obtener data de select de item');
                }
            }).catch(function (err) {
                // Run this when promise was rejected via reject()
                console.log(err)
            })
        }
    }

    quitarItemsDetalleCuadroCostosAgregadosACompra(data){
        let idList=[];
        // console.log(data);
        data.forEach(element => {
            idList.push(element.id_cc_am_filas?element.id_cc_am_filas:element.id_cc_venta_filas); 
        });
    
        var tableBody = document.querySelector("table[id='ListaModalDetalleCuadroCostos'] tbody");
        let trs = tableBody.querySelectorAll('tr');
    
        trs.forEach(tr => {
            if(idList.includes(parseInt(tr.children[9].children[0].dataset.id))){
                tr.remove();
            }
            
        });
    }
    validarObjItemsParaCompra(){
        infoStateInput = [];
        if ((itemsParaCompraList).length > 0) {
            console.log((itemsParaCompraList));
            (itemsParaCompraList).forEach(element => {
                if (element.id_producto == '' || element.id_producto == null) {
                    infoStateInput.push('Guardar item');
                }
                // if (element.id_categoria == '' || element.id_categoria == null) {
                //     infoStateInput.push('Completar Categoría');
                // }
                // if (element.id_subcategoria == '' || element.id_subcategoria == null) {
                //     infoStateInput.push('Completar Subcategoría');
                // }
                // if (element.id_clasif == '' || element.id_clasif == null) {
                //     infoStateInput.push('Completar Clasificación');
                // }
                if (element.id_unidad_medida == '' || element.id_unidad_medida == null) {
                    infoStateInput.push('Completar Unidad de Medida');
                }
                if (element.cantidad == '' || element.cantidad == null) {
                    infoStateInput.push('Completar Cantidad');
                }
    
            });
    
            if (infoStateInput.length > 0) {
    
                document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").setAttribute('title', 'Falta: ' + infoStateInput.join());
                document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").setAttribute('disabled', true);
            } else {
                document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").setAttribute('title', 'Siguiente');
                document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").removeAttribute('disabled');
    
            }
        }
    }

    // ver detalle cuadro de costos
    openModalCuadroCostos(obj){
        let id_requerimiento_seleccionado = obj.dataset.idRequerimiento;
        $('#modal-ver-cuadro-costos').modal({
            show: true,
            backdrop: 'true'
        });
        return requerimientoPendienteModel.getDataListaItemsCuadroCostosPorIdRequerimiento([id_requerimiento_seleccionado]);
        

    }
    // Crear orden por requerimiento
    crearOrdenPorRequerimiento(obj){

    }


}

const requerimientoPendienteCtrl = new RequerimientoPendienteCtrl(requerimientoPendienteView);

window.onload = function() {
    requerimientoPendienteCtrl.init();
};