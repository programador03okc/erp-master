class RequerimientoModel {
    constructor () {
    }

    getDivisiones(){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`lista-divisiones`,
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
    obtenerAlmacenes(sede){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`cargar_almacenes/${sede}`,
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
    obtenerListaPartidas(idGrupo,idProyecto){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`mostrar-partidas/${idGrupo}/${idProyecto}`,
                dataType: 'JSON',
                beforeSend: function (data) { 
                    var customElement = $("<div>", {
                        "css": {
                            "font-size": "24px",
                            "text-align": "center",
                            "padding": "0px",
                            "margin-top": "-400px"
                        },
                        "class": "your-custom-class"
                    });
        
                    $('#modal-partidas div.modal-body').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        custom: customElement,
                        imageColor: "#3c8dbc"
                    });
                    },
                success(response) {
                    resolve(response);
                },
                fail: function (jqXHR, textStatus, errorThrown) {
                    $('#modal-partidas div.modal-body').LoadingOverlay("hide", true);
                    alert("Hubo un problema al cargar las partidas. Por favor actualice la página e intente de nuevo");
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
                });
            });
    }

    obtenerCentroCostos(){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`mostrar-centro-costos`,
                dataType: 'JSON',
                beforeSend: function (data) { 
        
                    $('#modal-centro-costos .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                    },
                success(response) {
                    resolve(response);
                },
                fail: function (jqXHR, textStatus, errorThrown) {
                    $('#modal-centro-costos .modal-content').LoadingOverlay("hide", true);
                    alert("Hubo un problema al cargar los centro de costo. Por favor actualice la página e intente de nuevo");
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
                });
            });
    }
    getcategoriaAdjunto(){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`mostrar-categoria-adjunto`,
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
    getListadoElaborados(meOrAll,idEmpresa, idSede, idGrupo,division, idPrioridad){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'POST',
                url:`elaborados`,
                dataType: 'JSON',
                data:{'meOrAll':meOrAll,'idEmpresa':idEmpresa,'idSede':idSede,'idGrupo':idGrupo,'division':division,'idPrioridad':idPrioridad},
                success(response) {
                    resolve(response);
                },
                error: function(err) {
                    reject(err) 
                }
                });
            });
    }
    getListaDivisionesDeGrupo(idGrupo){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`mostrar-divisiones/${idGrupo}`,
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


    getListadoAprobacion(idEmpresa, idSede, idGrupo, idPrioridad){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'POST',
                url:`listado-aprobacion`,
                dataType: 'JSON',
                data:{'idEmpresa':idEmpresa,'idSede':idSede,'idGrupo':idGrupo,'idPrioridad':idPrioridad},
                success(response) {
                    resolve(response);
                },
                error: function(err) {
                    reject(err) 
                }
                });
            });
    }

    guardarRespuesta(payload){
        
        return $.ajax({
            type: 'POST',
            url:`guardar-respuesta`,
            dataType: 'JSON',
            data:payload
            });
    }

    getRequerimiento(idRequerimiento){
        return  $.ajax({
                type: 'GET',
                url:`mostrar-requerimiento/${idRequerimiento}/null`,
                dataType: 'JSON'
                });
        
        // return new Promise(function(resolve, reject) {
        //     $.ajax({
        //         type: 'GET',
        //         url:`mostrar-requerimiento/${idRequerimiento}/null`,
        //         dataType: 'JSON',
        //         beforeSend: function (data) { 

        //             },
        //         success(response) {
        //             resolve(response);
        //         },
        //         error: function (jqXHR, textStatus, errorThrown) {
        //             $('#modal-requerimiento div.modal-body').LoadingOverlay("hide", true);
        //             Swal.fire(
        //                 '',
        //                 'Hubo un problema al registrar la respuesta. Por favor actualice la página e intente de nuevo',
        //                 'error'
        //             );
        //             console.log(jqXHR);
        //             console.log(textStatus);
        //             console.log(errorThrown);
        //         }
        //         });
        //     });
    }

    // listado 
    getSedesPorEmpresa(idEmpresa){
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

    anularRequerimiento(idRequerimiento){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'PUT',
                url:`anular-requerimiento/${idRequerimiento}`,
                dataType: 'JSON',
                beforeSend: function (data) { 
                    var customElement = $("<div>", {
                        "css": {
                            "font-size": "24px",
                            "text-align": "center",
                            "padding": "0px",
                            "margin-top": "-400px"
                        },
                        "class": "your-custom-class",
                        "text": "Anulando requerimiento..."

                    });
        
                    $('#wrapper-okc').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        custom: customElement,
                        imageColor: "#3c8dbc"
                    });
                    },
                success(response) {
                    resolve(response);
                },
                fail: function (jqXHR, textStatus, errorThrown) {
                    $('#wrapper-okc').LoadingOverlay("hide", true);
                    alert("Hubo un problema al anular el requerimiento. Por favor actualice la página e intente de nuevo");
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
                });
            });
    }

    getCabeceraRequerimiento(idRequerimiento){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`requerimiento/${idRequerimiento}`,
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

    getHistorialAprobacion(idRequerimiento){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`historial-aprobacion/${idRequerimiento}`,
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
    getTrazabilidadDetalleRequerimiento(idRequerimiento){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`trazabilidad-detalle-requerimiento/${idRequerimiento}`,
                dataType: 'JSON',
                success(response) {
                    resolve(response.data);
                },
                error: function(err) {
                    reject(err) 
                }
                });
            });
    }

    enviarRequerimientoAPago(idRequerimiento){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`requerimientoAPago/${idRequerimiento}`,
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


const requerimientoModel = new RequerimientoModel();

