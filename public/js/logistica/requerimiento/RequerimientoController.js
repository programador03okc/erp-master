class RequerimientoCtrl{
    constructor(RequerimientoView) {
        this.requerimientoView = RequerimientoView;
    }
    init() {
        this.requerimientoView.init();
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
}

const requerimientoCtrl = new RequerimientoCtrl(requerimientoView);

window.onload = function() {
    requerimientoView.init();
};