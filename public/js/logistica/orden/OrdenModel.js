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
}


const ordenModel = new OrdenModel();

