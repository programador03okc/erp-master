class ProveedorModel {
    constructor () {
    }
    getListaProveedores(){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`obtener-data-listado`,
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
    getProveedor(idProveedor){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`mostrar/${idProveedor}`,
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
