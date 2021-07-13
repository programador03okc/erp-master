class OrdenModel {
    constructor () {
    }
    getTipoCambioCompra(fecha){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`tipo-cambio-compra/${fecha}`,
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
    // modal listar items catalogo
    getlistarItems(){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`/logistica/mostrar_items`,
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


const ordenModel = new OrdenModel();

