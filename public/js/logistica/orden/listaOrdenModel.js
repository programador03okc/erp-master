class ListaOrdenModel {
    constructor () {
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
    // 

    obtenerListaOrdenesElaboradas(tipoOrden=null, vinculadoPor=null, empresa=null, sede=null, tipoProveedor=null, enAlmacen=null, signoTotalOrden=null, montoTotalOrden=null, estado=null){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`listar-ordenes/${tipoOrden}/${vinculadoPor}/${empresa}/${sede}/${tipoProveedor}/${enAlmacen}/${signoTotalOrden}/${montoTotalOrden}/${estado}`,
                dataType: 'JSON',
                beforeSend:  (data)=> {
    
                $('#listaOrdenes').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
                success(response) {
                    resolve(response.data);
                    $('#listaOrdenes').LoadingOverlay("hide", true);

                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
                },
                "drawCallback": function( settings ) {
                    $('#listaOrdenes').LoadingOverlay("hide", true);
                }
                });
            });
    }
    obtenerDetalleOrdenElaboradas(id){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`detalle-orden/${id}`,
                dataType: 'JSON',
                success(response) {
                    resolve(response);
                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
                }
                });
            });
    }


    // lista por item

    obtenerListaDetalleOrdenesElaboradas(tipoOrden=null, vinculadoPor=null, empresa=null, sede=null, tipoProveedor=null, enAlmacen=null, signoSubtotal=null, subtotal=null, estado=null){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`listar-detalle-orden/${tipoOrden}/${vinculadoPor}/${empresa}/${sede}/${tipoProveedor}/${enAlmacen}/${signoSubtotal}/${subtotal}/${estado}`,
                dataType: 'JSON',
                beforeSend:  (data)=> {
    
                    $('#listaDetalleOrden').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success(response) {
                    resolve(response.data);
                    $('#listaDetalleOrden').LoadingOverlay("hide", true);

                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
                },
                "drawCallback": function( settings ) {
                    $('#listaDetalleOrden').LoadingOverlay("hide", true);
                }
                });
            });
    }

    ver_orden(id_orden){
        return new Promise(function(resolve, reject) {
                $.ajax({
                    type: 'GET',
                    url: `ver-orden/${id_orden}`,
                    dataType: 'JSON',
                    success(response) {
                        resolve(response) // Resolve promise and go to then() 
                    },
                    error: function(err) {
                    reject(err) // Reject the promise and go to catch()
                    }
                    });
            });
    }

    actualizarEstadoOrdenPorRequerimiento(id_orden_compra,id_estado_orden_selected){
        return new Promise(function(resolve, reject) {
                $.ajax({
                    type: 'POST',
                    url: `actualizar-estado`,
                    data:{'id_orden_compra':id_orden_compra, 'id_estado_orden_selected':id_estado_orden_selected},
                    dataType: 'JSON',
                    success(response) {
                        resolve(response) // Resolve promise and go to then() 
                    },
                    error: function(err) {
                    reject(err) // Reject the promise and go to catch()
                    }
                    });
            });
    }
    
    actualizarEstadoDetalleOrdenPorRequerimiento(id_detalle_orden_compra,id_estado_detalle_orden_selected){
        return new Promise(function(resolve, reject) {
                $.ajax({
                    type: 'POST',
                    url: `actualizar-estado-detalle`,
                    data:{'id_detalle_orden_compra':id_detalle_orden_compra, 'id_estado_detalle_orden_selected':id_estado_detalle_orden_selected},
                    dataType: 'JSON',
                    success(response) {
                        resolve(response) // Resolve promise and go to then() 
                    },
                    error: function(err) {
                    reject(err) // Reject the promise and go to catch()
                    }
                    });
            });
    }


    anularOrden(id_orden){

        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'PUT',
                url:`anular/${id_orden}`,
                dataType: 'JSON',
                success(response) {
                    resolve(response) // Resolve promise and go to then() 
                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
                }
                });
            });
    }


    listarDocumentosVinculados(id_orden){
        return new Promise(function(resolve, reject) {
                $.ajax({
                    type: 'GET',
                    url: `documentos-vinculados/${id_orden}`,
                    dataType: 'JSON',
                    success(response) {
                        resolve(response) // Resolve promise and go to then() 
                    },
                    error: function(err) {
                    reject(err) // Reject the promise and go to catch()
                    }
                    });
            });
    }


    descargarListaOrdenesVistaCabecera(){
        // window.open('descargar-excel-listar-ordenes');
        window.open('listar-ordenes-excel');
    }
}

