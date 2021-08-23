//================ Model ================== 
var itemsParaAtenderConAlmacenList=[];
var dataSelect=[];
class RequerimientoPendienteModel {
    constructor () {
    }
    // Getter
    // get requerimientosPendientes() {
    //     return this.getRequerimientosPendientes();
    // }
    // Método
    getRequerimientosPendientes(id_empresa=null,id_sede=null) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    type: 'GET',
                    url:`requerimientos-pendientes/${id_empresa}/${id_sede}`,
                    dataType: 'JSON',
                    success(response) {
                        resolve(response.data) // Resolve promise and go to then() 
                    },
                    error: function(err) {
                    reject(err) // Reject the promise and go to catch()
                    }
                    });
                });
    }

    // filtros 
    getDataSelectSede(id_empresa){
        
        return new Promise(function(resolve, reject) {
            if(id_empresa >0){
                $.ajax({
                    type: 'GET',
                    url: `listar-sedes-por-empresa/` + id_empresa,
                    dataType: 'JSON',
                    success(response) {
                        resolve(response) // Resolve promise and go to then() 
                    },
                    error: function(err) {
                    reject(err) // Reject the promise and go to catch()
                    }
                    });
                }else{
                    resolve(false);
                }
            });
         
    } 

    // atender con almacén

    getDataItemsRequerimientoParaAtenderConAlmacen(id_requerimiento){
        let codigoRequerimiento='';
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`/logistica/gestion-logistica/requerimiento/elaboracion/mostrar-requerimiento/${id_requerimiento}/0`,
                dataType: 'JSON',
                success(response) {
                    if(response.det_req !=undefined && response.det_req.length >0){

                        codigoRequerimiento=response.requerimiento[0].codigo;
                        itemsParaAtenderConAlmacenList=response.det_req;
                        itemsParaAtenderConAlmacenList.forEach((element,index) => {
                            itemsParaAtenderConAlmacenList[index].cantidad_a_atender =0;
                            
                        });
                        requerimientoPendienteModel.getAlmacenes().then(function (res) {
                            // Run this when your request was successful
                        
                            let data_almacenes= res.data;
                            if (data_almacenes.length > 0) {
                                resolve({'codigo_requerimiento':codigoRequerimiento,'detalle_requerimiento':(response.det_req).filter(item => item.tiene_transformacion == false ),'almacenes':data_almacenes}); // Resolve promise and go to then() 
                            } else {
                            
                            }
                    
                        }).catch(function (err) {
                            // Run this when promise was rejected via reject()
                            console.log(err)
                        })

                    }else{
                        alert("Hubo un error, no se puedo cargar la data del requerimiento.");
                    }

                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
                }
                });
            });
        }

    getAlmacenes(){
            return new Promise(function (resolve, reject) {
                $.ajax({
                    type: 'GET',
                    url:  `listar-almacenes`,
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

    guardarAtendidoConAlmacen(payload){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'POST',
                url: 'guardar-atencion-con-almacen',
                data: payload,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
    
                    $('#modal-atender-con-almacen .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) =>{
                    resolve(response);
                },
                fail:  (jqXHR, textStatus, errorThrown) =>{
                    $('#modal-atender-con-almacen .modal-content').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar guardar la reserva, por favor vuelva a intentarlo',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
            });
    }
        

    // Agregar item base 
    tieneItemsParaCompra(reqTrueList){
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                data:{'requerimientoList':reqTrueList},
                url:  `tiene-items-para-compra`,
                dataType: 'JSON',
                success(response) {

                    if (dataSelect.length > 0) {
                        resolve({'data':response.det_req,
                                'tiene_total_items_agregados':response.tiene_total_items_agregados,
                                'categoria':dataSelect[0].categoria,
                                'subcategoria':dataSelect[0].subcategoria,
                                'clasificacion': dataSelect[0].clasificacion,
                                'monedad':dataSelect[0].moneda,
                                'unidad_medida':dataSelect[0].unidad_medida});
                
                    } else {
                        requerimientoPendienteModel.getDataAllSelect().then(function (res) {
                            if (res.length > 0) {
                                dataSelect = res;
                
                                resolve({'data':response.det_req,
                                'tiene_total_items_agregados':response.tiene_total_items_agregados,
                                'categoria':res[0].categoria,
                                'subcategoria':res[0].subcategoria,
                                'clasificacion': res[0].clasificacion,
                                'monedad':res[0].moneda,
                                'unidad_medida':res[0].unidad_medida});
                            } else {
                                alert('No se pudo obtener data de select de item');
                            }
                
                        }).catch(function (err) {
                            // Run this when promise was rejected via reject()
                            console.log(err)
                        })
                
                    }
                    // resolve(response) // Resolve promise and go to then() 
                },
                error: function (err) {
                    reject(err) // Reject the promise and go to catch()
                }
            });
        });
    }

    getDataAllSelect(){
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `grupo-select-item-para-compra`,
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
    getDataListaItemsCuadroCostosPorIdRequerimientoPendienteCompra(reqTrueList){
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                url: `lista_items-cuadro-costos-por-requerimiento-pendiente-compra`,
                data: { 'requerimientoList': reqTrueList },
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

    guardarMasItemsAlDetalleRequerimiento(id_requerimiento_list,item_list){
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                url: `guardar-items-detalle-requerimiento`,
                data: { 'id_requerimiento_list': id_requerimiento_list, 'items':item_list },
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

    // ver detalle cuadro de costos
    getDataListaItemsCuadroCostosPorIdRequerimiento(reqTrueList){
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                url: `lista_items-cuadro-costos-por-requerimiento`,
                data: { 'requerimientoList': reqTrueList },
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


    // Crear orden por requerimiento

    obtenerDetalleRequerimientos(id){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`detalle-requerimiento/${id}`,
                dataType: 'JSON',
                success(response) {
                    resolve(response);
                },
                error: function(err) {
                reject(err)
                }
                });
            });
    }

}

const requerimientoPendienteModel = new RequerimientoPendienteModel();

