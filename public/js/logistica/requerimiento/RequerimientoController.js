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
}

const requerimientoCtrl = new RequerimientoCtrl(requerimientoView);

window.onload = function() {
    requerimientoView.init();
};