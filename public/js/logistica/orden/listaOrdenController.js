
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


    exportTableToExcel(tableID,filename){
        // // return listaOrdenModel.descargarListaOrdenesVistaCabecera();
        // var uri = 'data:application/vnd.ms-excel;base64,'
        // , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
        // , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
        // , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
    
        // var table = tableID;
        // var name = filename;
    
        // if (!table.nodeType) table = document.getElementById(table)
        //     var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }
        //     window.location.href = uri + base64(format(template, ctx));
        //     window.location.download = filename;
        //     // window.location.click();
        
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
        
        // Specify file name
        filename = filename?filename+'.xls':'excel_data.xls';
        
        // Create download link element
        downloadLink = document.createElement("a");
        
        document.body.appendChild(downloadLink);
        
        if(navigator.msSaveOrOpenBlob){
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob( blob, filename);
        }else{
            // Create a link to the file
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        
            // Setting the file name
            downloadLink.download = filename;
            
            //triggering the function
            downloadLink.click();
        }
    }

}

const listaOrdenCtrl = new ListaOrdenCtrl(listaOrdenView);

window.onload = function () {
    listaOrdenView.init();
};