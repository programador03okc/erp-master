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
    // getRequerimientosPendientes(empresa,sede,fechaRegistroDesde,fechaRegistroHasta, reserva, orden) {
    //         return new Promise(function(resolve, reject) {
    //             $.ajax({
    //                 type: 'GET',
    //                 url:`requerimientos-pendientes/${empresa}/${sede}/${fechaRegistroDesde}/${fechaRegistroHasta}/${reserva}/${orden}`,
    //                 dataType: 'JSON',
    //                 beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
    
    //                 $('#requerimientos_pendientes').LoadingOverlay("show", {
    //                     imageAutoResize: true,
    //                     progress: true,
    //                     imageColor: "#3c8dbc"
    //                 });
    //             },
    //                 success(response) {
    //                     resolve(response.data) // Resolve promise and go to then() 
    //                 },
    //                 fail:  (jqXHR, textStatus, errorThrown) =>{
    //                     $('#requerimientos_pendientes').LoadingOverlay("hide", true);
    //                     Swal.fire(
    //                         '',
    //                         'Lo sentimos hubo un error en el servidor al intentar cargar la lista de requerimientos pendientes, por favor vuelva a intentarlo',
    //                         'error'
    //                     );
    //                     console.log(jqXHR);
    //                     console.log(textStatus);
    //                     console.log(errorThrown);
    //                 }
    //                 });
    //             });
    // }

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

    obtenerSede(idEmpresa){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`listar-sedes-por-empresa/${idEmpresa}`,
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

    // atender con almacén

    getAllDataDetalleRequerimiento(idRequerimiento){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`todo-detalle-requeriento/${idRequerimiento}/SIN_TRANSFORMACION`,
                dataType: 'JSON',
                beforeSend: data => {
    
                    $("#modal-atender-con-almacen .modal-body").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success(response) {
                    resolve(response);
                    $("#modal-atender-con-almacen .modal-body").LoadingOverlay("hide", true);

                },
                "drawCallback": function( settings ) {
                    $("#modal-atender-con-almacen .modal-body").LoadingOverlay("hide", true);
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

    // guardarAtendidoConAlmacen(payload){
    //     return new Promise(function(resolve, reject) {
    //         $.ajax({
    //             type: 'POST',
    //             url: 'guardar-atencion-con-almacen',
    //             data: payload,
    //             processData: false,
    //             contentType: false,
    //             dataType: 'JSON',
    //             beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
    
    //                 $('#modal-atender-con-almacen .modal-content').LoadingOverlay("show", {
    //                     imageAutoResize: true,
    //                     progress: true,
    //                     imageColor: "#3c8dbc"
    //                 });
    //             },
    //             success: (response) =>{
    //                 resolve(response);
    //             },
    //             fail:  (jqXHR, textStatus, errorThrown) =>{
    //                 $('#modal-atender-con-almacen .modal-content').LoadingOverlay("hide", true);
    //                 Swal.fire(
    //                     '',
    //                     'Lo sentimos hubo un error en el servidor al intentar guardar la reserva, por favor vuelva a intentarlo',
    //                     'error'
    //                 );
    //                 console.log(jqXHR);
    //                 console.log(textStatus);
    //                 console.log(errorThrown);
    //             }
    //         });
    //         });
    // }
    obtenerDetalleRequerimientoParaReserva(idDetalleRequerimiento){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url: 'detalle-requeriento-para-reserva/'+idDetalleRequerimiento,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
    
                    $('#modal-nueva-reserva .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) =>{
                    resolve(response);
                },
                fail:  (jqXHR, textStatus, errorThrown) =>{
                    $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar obtener la data, por favor vuelva a intentarlo',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
            });
    }

    obtenerAlmacenPorDefectoRequerimiento(idRequerimiento){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url: 'almacen-requeriento/'+idRequerimiento,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
 
                },
                success: (response) =>{
                    resolve(response);
                },
                fail:  (jqXHR, textStatus, errorThrown) =>{
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar obtener la data, por favor vuelva a intentarlo',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
            });
    }
    obtenerHistorialDetalleRequerimientoParaReserva(idDetalleRequerimiento){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url: 'historial-reserva-producto/'+idDetalleRequerimiento,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
    
                    $('#modal-historial-reserva .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) =>{
                    resolve(response);
                },
                fail:  (jqXHR, textStatus, errorThrown) =>{
                    $('#modal-historial-reserva .modal-content').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar obtener la data, por favor vuelva a intentarlo',
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
    // tieneItemsParaCompra(reqTrueList){
    //     return new Promise(function (resolve, reject) {
    //         $.ajax({
    //             type: 'POST',
    //             data:{'requerimientoList':reqTrueList},
    //             url:  `tiene-items-para-compra`,
    //             dataType: 'JSON',
    //             success(response) {

    //                 if (dataSelect.length > 0) {
    //                     resolve({'data':response.det_req,
    //                             'tiene_total_items_agregados':response.tiene_total_items_agregados,
    //                             'categoria':dataSelect[0].categoria,
    //                             'subcategoria':dataSelect[0].subcategoria,
    //                             'clasificacion': dataSelect[0].clasificacion,
    //                             'monedad':dataSelect[0].moneda,
    //                             'unidad_medida':dataSelect[0].unidad_medida});
                
    //                 } else {
    //                     requerimientoPendienteModel.getDataAllSelect().then(function (res) {
    //                         if (res.length > 0) {
    //                             dataSelect = res;
                
    //                             resolve({'data':response.det_req,
    //                             'tiene_total_items_agregados':response.tiene_total_items_agregados,
    //                             'categoria':res[0].categoria,
    //                             'subcategoria':res[0].subcategoria,
    //                             'clasificacion': res[0].clasificacion,
    //                             'monedad':res[0].moneda,
    //                             'unidad_medida':res[0].unidad_medida});
    //                         } else {
    //                             alert('No se pudo obtener data de select de item');
    //                         }
                
    //                     }).catch(function (err) {
    //                         // Run this when promise was rejected via reject()
    //                         console.log(err)
    //                     })
                
    //                 }
    //                 // resolve(response) // Resolve promise and go to then() 
    //             },
    //             error: function (err) {
    //                 reject(err) // Reject the promise and go to catch()
    //             }
    //         });
    //     });
    // }

    // getDataAllSelect(){
    //     return new Promise(function (resolve, reject) {
    //         $.ajax({
    //             type: 'GET',
    //             url: `grupo-select-item-para-compra`,
    //             dataType: 'JSON',
    //             success(response) {
    //                 resolve(response) // Resolve promise and go to then() 
    //             },
    //             error: function (err) {
    //                 reject(err) // Reject the promise and go to catch()
    //             }
    //         });
    //     });
    // }
    // getDataListaItemsCuadroCostosPorIdRequerimientoPendienteCompra(reqTrueList){
    //     return new Promise(function (resolve, reject) {
    //         $.ajax({
    //             type: 'POST',
    //             url: `lista_items-cuadro-costos-por-requerimiento-pendiente-compra`,
    //             data: { 'requerimientoList': reqTrueList },
    //             dataType: 'JSON',
    //             success(response) {
    //                 resolve(response) // Resolve promise and go to then() 
    //             },
    //             error: function (err) {
    //                 reject(err) // Reject the promise and go to catch()
    //             }
    //         });
    //     });
    // }

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

