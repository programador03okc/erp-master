class RequerimientoCtrl{
    constructor(RequerimientoView) {
        this.requerimientoView = RequerimientoView;
    }
    init() {
        this.requerimientoView.init();
    }

    obtenerSede(idEmpresa){
        return requerimientoModel.obtenerSede(idEmpresa);

    }
    obtenerAlmacenes(sede){
        return requerimientoModel.obtenerAlmacenes(sede);

    }

    obtenerListaPartidas(idGrupo,idProyecto){
        if(idProyecto == 0 || idProyecto == '' || idProyecto == null){
            idProyecto = '';
        }
        return requerimientoModel.obtenerListaPartidas(idGrupo,idProyecto);
    }

    obtenerCentroCostos(){
        return requerimientoModel.obtenerCentroCostos();
    }

    getcategoriaAdjunto(){
        return requerimientoModel.getcategoriaAdjunto();

    }
    
    getRequerimiento(idRequerimiento){
        return requerimientoModel.getRequerimiento(idRequerimiento);

    }
    // listado 
    getListadoElaborados(idEmpresa, idSede, idGrupo, idPrioridad){
        return requerimientoModel.getListadoElaborados(idEmpresa, idSede, idGrupo, idPrioridad);

    }
    // aprobar
    getListadoAprobacion(idEmpresa, idSede, idGrupo, idPrioridad){
        return requerimientoModel.getListadoAprobacion(idEmpresa, idSede, idGrupo, idPrioridad);

    }


    // filtros listado
    getSedesPorEmpresa(idEmpresa){
        return requerimientoModel.getSedesPorEmpresa(idEmpresa);
    }
  
}

const requerimientoCtrl = new RequerimientoCtrl(requerimientoView);

