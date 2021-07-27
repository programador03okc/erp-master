class RequerimientoCtrl{
    constructor(requerimientoModel) {
        this.requerimientoModel = requerimientoModel;
    }
    // init() {
    //     this.requerimientoView.init();
    // }

    getTipoCambioCompra(fecha){
        return this.requerimientoModel.getTipoCambioCompra(fecha);

    }

    obtenerSede(idEmpresa){
        return this.requerimientoModel.obtenerSede(idEmpresa);

    }
    obtenerAlmacenes(sede){
        return this.requerimientoModel.obtenerAlmacenes(sede);

    }

    obtenerListaPartidas(idGrupo,idProyecto){
        if(idProyecto == 0 || idProyecto == '' || idProyecto == null){
            idProyecto = '';
        }
        return this.requerimientoModel.obtenerListaPartidas(idGrupo,idProyecto);
    }

    obtenerCentroCostos(){
        return this.requerimientoModel.obtenerCentroCostos();
    }

    getcategoriaAdjunto(){
        return this.requerimientoModel.getcategoriaAdjunto();

    }
    
    getRequerimiento(idRequerimiento){
        return this.requerimientoModel.getRequerimiento(idRequerimiento);

    }
    // listado 
    getListadoElaborados(meOrAll,idEmpresa, idSede, idGrupo, division ,idPrioridad){
        return this.requerimientoModel.getListadoElaborados(meOrAll,idEmpresa, idSede, idGrupo, division, idPrioridad);

    }
    getListaDivisionesDeGrupo(idGrupo){
        return this.requerimientoModel.getListaDivisionesDeGrupo(idGrupo);

    }

    anularRequerimiento(idRequerimiento){
        return this.requerimientoModel.anularRequerimiento(idRequerimiento);
    }

    getCabeceraRequerimiento(idRequerimiento){
        return this.requerimientoModel.getCabeceraRequerimiento(idRequerimiento);

    }
    getHistorialAprobacion(idRequerimiento){
        return this.requerimientoModel.getHistorialAprobacion(idRequerimiento);

    }

    getTrazabilidadDetalleRequerimiento(idRequerimiento){
        return this.requerimientoModel.getTrazabilidadDetalleRequerimiento(idRequerimiento);

    }

    enviarRequerimientoAPago(idRequerimiento){
        return this.requerimientoModel.enviarRequerimientoAPago(idRequerimiento);

    }
    // aprobacion y revision
    getListadoAprobacion(idEmpresa, idSede, idGrupo, idPrioridad){
        return this.requerimientoModel.getListadoAprobacion(idEmpresa, idSede, idGrupo, idPrioridad);

    }

    guardarRespuesta(payload){
        return this.requerimientoModel.guardarRespuesta(payload);
    }


    // filtros listado
    getSedesPorEmpresa(idEmpresa){
        return this.requerimientoModel.getSedesPorEmpresa(idEmpresa);
    }
  
}

// const requerimientoCtrl = new RequerimientoCtrl(requerimientoView);

