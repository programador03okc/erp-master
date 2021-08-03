function cancelarRequerimiento() {
    const requerimientoModel = new RequerimientoModel();
    const requerimientoController = new RequerimientoCtrl(requerimientoModel);
    const requerimientoView = new RequerimientoView(requerimientoController);
    requerimientoView.RestablecerFormularioRequerimiento();   

}