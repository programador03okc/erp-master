class RequerimientoModel {
    constructor () {
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
                success(response) {
                    resolve(response);
                },
                error: function(err) {
                    reject(err) 
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
                success(response) {
                    resolve(response);
                },
                error: function(err) {
                    reject(err) 
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
        return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'POST',
            url:`guardar-respuesta`,
            dataType: 'JSON',
            data:payload,
            success(response) {
                resolve(response);
            },
            error: function(err) {
                reject(err) 
            }
            });
        });
    }

    getRequerimiento(idRequerimiento){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'GET',
                url:`mostrar-requerimiento/${idRequerimiento}/null`,
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
 
}


const requerimientoModel = new RequerimientoModel();

