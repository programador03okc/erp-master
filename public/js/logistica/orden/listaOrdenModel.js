class ListaOrdenModel {
    constructor () {
    }


    obtenerListaOrdenesElaboradas(){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`listar-ordenes`,
                dataType: 'JSON',
                success(response) {
                    resolve(response.data);
                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
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

    obtenerListaDetalleOrdenesElaboradas(){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`listar-detalle-orden`,
                dataType: 'JSON',
                success(response) {
                    resolve(response.data);
                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
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


    eliminarAtencionOrdenRequerimiento(id_orden){

        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`revertir/${id_orden}`,
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


}


const listaOrdenModel = new ListaOrdenModel();

