
var iTableCounter = 1;
var oInnerTable;
var tablaListaOrdenes;

class ListaOrdenCtrl {
    constructor(ListaOrdenView) {
        this.listaOrdenView = ListaOrdenView;
    }
    init() {
        // this.listaOrdenView.init();
    }

    // filtros

    getDataSelectSede(id_empresa = null){
        return listaOrdenModel.getDataSelectSede(id_empresa);
    }

    
    obtenerListaOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoTotalOrden, montoTotalOrden, estado) {
        return listaOrdenModel.obtenerListaOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoTotalOrden, montoTotalOrden, estado);

    }

    verDetalleOrden(obj) {
        let tr = obj.closest('tr');
        var row = tablaListaOrdenes.row(tr);
        var id = obj.dataset.id;
        if (row.child.isShown()) {
            //  This row is already open - close it
            row.child.hide();
            tr.classList.remove('shown');
        }
        else {
            // Open this row
            //    row.child( format(iTableCounter, id) ).show();
            listaOrdenCtrl.buildFormat(iTableCounter, id, row);
            tr.classList.add('shown');
            // try datatable stuff
            oInnerTable = $('#listaOrdenes_' + iTableCounter).dataTable({
                //    data: sections, 
                autoWidth: true,
                deferRender: true,
                info: false,
                lengthChange: false,
                ordering: false,
                paging: false,
                scrollX: false,
                scrollY: false,
                searching: false,
                columns: [
                ]
            });
            iTableCounter = iTableCounter + 1;
        }
    }

    buildFormat(table_id, id, row) {
        listaOrdenModel.obtenerDetalleOrdenElaboradas(id).then(function(res) {
            listaOrdenView.construirDetalleOrdenElaboradas(table_id,row,res);
        }).catch(function(err) {
            console.log(err)
        })
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
        return listaOrdenModel.obtenerListaDetalleOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoSubtotal, subtotal, estado);
        
    }
    
    ver_orden(id){
        return listaOrdenModel.ver_orden(id);
    }
    
    actualizarEstadoOrdenPorRequerimiento(id_orden_compra,id_estado_orden_selected){
        return listaOrdenModel.actualizarEstadoOrdenPorRequerimiento(id_orden_compra,id_estado_orden_selected);

    }

    actualizarEstadoDetalleOrdenPorRequerimiento(id_detalle_orden_compra,id_estado_detalle_orden_selected){
        return listaOrdenModel.actualizarEstadoDetalleOrdenPorRequerimiento(id_detalle_orden_compra,id_estado_detalle_orden_selected);

    }


    anularOrden(obj){
        return listaOrdenModel.anularOrden(obj);
    }

    listarDocumentosVinculados(id){
        return listaOrdenModel.listarDocumentosVinculados(id);
    }

}

const listaOrdenCtrl = new ListaOrdenCtrl(listaOrdenView);

window.onload = function () {
    listaOrdenView.init();
};