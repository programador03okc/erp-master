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
                beforeSend: data => {
    
                    $("#modal-catalogo-items .modal-body").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success(response) {
                    resolve(response.data);
                    $("#modal-catalogo-items .modal-body").LoadingOverlay("hide", true);

                },
                error: function(err) {
                reject(err) // Reject the promise and go to catch()
                $("#modal-catalogo-items .modal-body").LoadingOverlay("hide", true);

                },
                "drawCallback": function( settings ) {
                    $("#modal-catalogo-items .modal-body").LoadingOverlay("hide", true);
                },
                });
            });
    }

    getRequerimientosPendientes(id_empresa=null,id_sede=null) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`requerimientos-pendientes/${id_empresa}/${id_sede}/SIN_FILTRO/SIN_FILTRO/SIN_FILTRO/SIN_FILTRO`,
                dataType: 'JSON',
                beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
    
                $('#modal-vincular-requerimiento-orden .modal-body').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
                success(response) {
                    resolve(response.data) // Resolve promise and go to then() 
                    $('#modal-vincular-requerimiento-orden .modal-body').LoadingOverlay("hide", true);

                },
                error: function(err) {
                    $('#modal-vincular-requerimiento-orden .modal-body').LoadingOverlay("hide", true);
                reject(err) // Reject the promise and go to catch()
                },
                "drawCallback": function( settings ) {
                    $('#modal-vincular-requerimiento-orden .modal-body').LoadingOverlay("hide", true);
                },
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
    anularOrden(id){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'PUT',
                url:`anular/${id}`,
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

    obtenerRequerimiento(id){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`requerimiento/${id}`,
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

