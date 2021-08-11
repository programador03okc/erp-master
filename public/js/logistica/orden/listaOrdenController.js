
var iTableCounter = 1;
var oInnerTable;
var tablaListaOrdenes;

class ListaOrdenCtrl {
    constructor(listaOrdenModel) {
        this.listaOrdenModel = listaOrdenModel;
    }
    init() {
        // this.listaOrdenView.init();
    }

    // filtros

    getDataSelectSede(id_empresa = null){
        return this.listaOrdenModel.getDataSelectSede(id_empresa);
    }

    
    obtenerListaOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoTotalOrden, montoTotalOrden, estado) {
        return this.listaOrdenModel.obtenerListaOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoTotalOrden, montoTotalOrden, estado);

    }

    obtenerDetalleOrdenElaboradas(id) {
        return this.listaOrdenModel.obtenerDetalleOrdenElaboradas(id);
    }




    abrirRequerimiento(idRequerimiento) {
        localStorage.setItem("id_requerimiento", idRequerimiento);
        let url = "/logistica/gestion-logistica/requerimiento/elaboracion/index";
        var win = window.open(url, '_blank');
        // Cambiar el foco al nuevo tab (punto opcional)
        win.focus();
    }



    // lista por item


    obtenerListaDetalleOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoSubtotal, subtotal, estado) {
        return this.listaOrdenModel.obtenerListaDetalleOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoSubtotal, subtotal, estado);
        
    }
    
    ver_orden(id){
        return this.listaOrdenModel.ver_orden(id);
    }
    
    actualizarEstadoOrdenPorRequerimiento(id_orden_compra,id_estado_orden_selected){
        return this.listaOrdenModel.actualizarEstadoOrdenPorRequerimiento(id_orden_compra,id_estado_orden_selected);

    }

    actualizarEstadoDetalleOrdenPorRequerimiento(id_detalle_orden_compra,id_estado_detalle_orden_selected){
        return this.listaOrdenModel.actualizarEstadoDetalleOrdenPorRequerimiento(id_detalle_orden_compra,id_estado_detalle_orden_selected);

    }


    anularOrden(id){
        return this.listaOrdenModel.anularOrden(id);
    }

    listarDocumentosVinculados(id){
        return this.listaOrdenModel.listarDocumentosVinculados(id);
    }


    descargarListaOrdenesVistaCabecera(){
        return this.listaOrdenModel.descargarListaOrdenesVistaCabecera();

    }

}

