var vardataTables = funcDatatables();
var cantidadFiltrosActivosCabecera=0;
var cantidadFiltrosActivosDetalle=0;

class ListaOrdenView {
    constructor(listaOrdenCtrl){
        this.listaOrdenCtrl = listaOrdenCtrl;
    }

    init() {
        this.vista_extendida()
        this.tipoVistaPorCabecera();
    }

    initializeEventHandler(){
        $('#listar_ordenes').on("click","button.handleClickTipoVistaPorCabecera", ()=>{
            this.tipoVistaPorCabecera();
        });
        $('#modal-editar-estado-orden').on("click","button.handleClickUpdateEstadoOrdenCompra", ()=>{
            this.updateEstadoOrdenCompra();
        });
        $('#listar_ordenes').on("click","button.handleClickTipoVistaPorItem", ()=>{
            this.tipoVistaPorItem();
        });
        $('#modal-editar-estado-detalle-orden').on("click","button.handleClickUpdateEstadoDetalleOrdenCompra", ()=>{
            this.updateEstadoDetalleOrdenCompra();
        });

        // $('#modal-ver-orden').on("click","span.handleClickEditarEstadoOrden", (e)=>{
        //     this.editarEstadoOrden(e.currentTarget);
        // });
        $('#listaOrdenes tbody').on("click","label.handleClickAbrirOrden",(e)=>{
            this.abrirOrden(e.currentTarget.dataset.idOrden);
        });
        
        $('#listaOrdenes tbody').on("click","button.handleClickAbrirOrdenPDF",(e)=>{
            this.abrirOrdenPDF(e.currentTarget.dataset.idOrdenCompra);
        });
        $('#listaOrdenes tbody').on("click","label.handleClickAbrirRequerimiento",(e)=>{
            // var data = $('#listaOrdenes').DataTable().row($(this).parents("tr")).data();
            this.abrirRequerimiento(e.currentTarget.dataset.idRequerimiento);
        });
        $('#listaOrdenes tbody').on("click","button.handleCliclVerDetalleOrden",(e)=>{
            this.verDetalleOrden(e.currentTarget);
        });
        
        $('#listaOrdenes tbody').on("click","button.handleClickAnularOrden",(e)=>{
            this.anularOrden(e.currentTarget);
        });
        
        $('#listaOrdenes tbody').on("click","a.handleClickObtenerArchivos",(e)=>{
            this.obtenerArchivos(e.currentTarget.dataset.id, e.currentTarget.dataset.tipo);
        });
        $('#listaOrdenes').on("click","span.handleClickEditarEstadoOrden", (e)=>{
            this.editarEstadoOrden(e.currentTarget);
        });
    


        $('#listaDetalleOrden tbody').on("click","span.handleClickVerOrdenModal",(e)=>{
            this.verOrdenModal(e.currentTarget);
        });
        $('#listaDetalleOrden tbody').on("click","span.handleClickEditarEstadoItemOrden",(e)=>{
            this.editarEstadoItemOrden(e.currentTarget);
        });
        
        $('#listaDetalleOrden tbody').on("click","button.handleClickAnularOrden",(e)=>{
            this.anularOrden(e.currentTarget);
        });
        $('#listaDetalleOrden tbody').on("click","button.handleClickAbrirOrdenPDF",(e)=>{
            this.abrirOrdenPDF(e.currentTarget.dataset.idOrdenCompra);
        });
        $('#listaDetalleOrden tbody').on("click","button.handleClickAbrirOrden",(e)=>{
            this.abrirOrden(e.currentTarget.dataset.idOrdenCompra);
        });
        $('#listaDetalleOrden tbody').on("click","button.handleClickDocumentosVinculados",(e)=>{
            this.documentosVinculados(e.currentTarget);
        });


        $('#modal-filtro-lista-ordenes-elaboradas').on("click","input.handleCheckTipoOrden",(e)=>{
            this.chkTipoOrden(e);
        });

        $('#modal-filtro-lista-ordenes-elaboradas').on("click","input.handleCheckVinculadoPor",(e)=>{
            this.chkVinculadoPor(e);
        });
        $('#modal-filtro-lista-ordenes-elaboradas').on("click","input.handleCheckEmpresa",(e)=>{
            this.chkEmpresa(e);
        });
        $('#modal-filtro-lista-ordenes-elaboradas').on("click","input.handleCheckSede",(e)=>{
            this.chkSede(e);
        });
        $('#modal-filtro-lista-ordenes-elaboradas').on("click","input.handleCheckTipoProveedor",(e)=>{
            this.chkTipoProveedor(e);
        });
        $('#modal-filtro-lista-ordenes-elaboradas').on("click","input.handleCheckEnAlmacen",(e)=>{
            this.chkEnAlmacen(e);
        });
        $('#modal-filtro-lista-ordenes-elaboradas').on("click","input.handleCheckMontoOrden",(e)=>{
            this.chkMontoOrden(e);
        });
        $('#modal-filtro-lista-ordenes-elaboradas').on("click","input.handleCheckEstado",(e)=>{
            this.chkEstado(e);
        });
        $('#modal-filtro-lista-ordenes-elaboradas').on("change","select.handleChangeFilterReqByEmpresa",(e)=>{
            this.handleChangeFilterReqByEmpresa(e);
        });
        $('#modal-filtro-lista-ordenes-elaboradas').on("click","button.handleClickAplicarFiltrosVistaCabeceraOrden",()=>{
            this.aplicarFiltrosVistaCabeceraOrden();
        });

        $('#modal-filtro-lista-items-orden-elaboradas').on("click","input.handleCheckTipoOrden",(e)=>{
            this.chkTipoOrden(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on("click","input.handleCheckVinculadoPor",(e)=>{
            this.chkVinculadoPor(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on("click","input.handleCheckEmpresa",(e)=>{
            this.chkEmpresa(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on("change","select.handleChangeFilterReqByEmpresa",(e)=>{
            this.handleChangeFilterReqByEmpresa(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on("click","input.handleCheckSede",(e)=>{
            this.chkSede(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on("click","input.handleCheckTipoProveedor",(e)=>{
            this.chkTipoProveedor(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on("click","input.handleCheckEnAlmacen",(e)=>{
            this.chkEnAlmacen(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on("click","input.handleCheckSubtotal",(e)=>{
            this.chkSubtotal(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on("click","input.handleCheckEstado",(e)=>{
            this.chkEstado(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on("click","button.handleClickAplicarFiltrosVistaDetalleOrden",()=>{
            this.aplicarFiltrosVistaDetalleOrden();
        });
    }


    vista_extendida(){
        let body=document.getElementsByTagName('body')[0];
        body.classList.add("sidebar-collapse"); 
    }



    // botonera secundaria 
    tipoVistaPorCabecera(){
        document.querySelector("button[id='btnTipoVistaPorCabecera'").classList.add('active');
        document.querySelector("button[id='btnTipoVistaPorItemPara'").classList.remove('active');
        document.querySelector("div[id='contenedor-tabla-nivel-cabecera']").classList.remove('oculto');
        document.querySelector("div[id='contenedor-tabla-nivel-item']").classList.add('oculto');

        this.limpiarFiltrosActivosCabeceraOrden();
        
        this.obtenerListaOrdenesElaboradas();
        
    }
    tipoVistaPorItem(){
        document.querySelector("button[id='btnTipoVistaPorItemPara'").classList.add('active');
        document.querySelector("button[id='btnTipoVistaPorCabecera'").classList.remove('active');
        document.querySelector("div[id='contenedor-tabla-nivel-cabecera']").classList.add('oculto');
        document.querySelector("div[id='contenedor-tabla-nivel-item']").classList.remove('oculto');
        
        this.limpiarFiltrosActivosDetalleOrden();

        this.obtenerListaDetalleOrdenesElaboradas();

    }
    
    // filtros

    limpiarFiltrosActivosCabeceraOrden(){
        cantidadFiltrosActivosCabecera=0;
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkTipoOrden']").checked=false;
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='tipoOrden']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkVinculadoPor']").checked=false;
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='vinculadoPor']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkEmpresa']").checked=false;
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='empresa']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkSede']").checked=false;
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='sede']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkTipoProveedor']").checked=false;
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='tipoProveedor']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkEnAlmacen']").checked=false;
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='enAlmacen']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkMontoOrden']").checked=false;
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='signoTotalOrden']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='montoTotalOrden']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkEstado']").checked=false;
        document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='estado']").setAttribute('disabled',true);

    }
    limpiarFiltrosActivosDetalleOrden(){
        cantidadFiltrosActivosDetalle=0;
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkTipoOrden']").checked=false;
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='tipoOrden']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkVinculadoPor']").checked=false;
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='vinculadoPor']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkEmpresa']").checked=false;
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='empresa']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkSede']").checked=false;
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='sede']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkTipoProveedor']").checked=false;
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='tipoProveedor']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkEnAlmacen']").checked=false;
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='enAlmacen']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkSubtotal']").checked=false;
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='signoSubtotal']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='subtotal']").setAttribute('disabled',true);
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkEstado']").checked=false;
        document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='estado']").setAttribute('disabled',true);

    }

    exportTableToExcel(){
        this.listaOrdenCtrl.descargarListaOrdenesVistaCabecera();
    }

    filtroTablaListaOrdenesVistaCabecera(){
        $('#modal-filtro-lista-ordenes-elaboradas').modal({
            show: true,
            backdrop: 'true'
        });
    }

    filtroTablaListaOrdenesVistaDetalle(){
        $('#modal-filtro-lista-items-orden-elaboradas').modal({
            show: true,
            backdrop: 'true'
        });
    }

    getNameModalActive(){
     
        if(document.querySelector("div[id='modal-filtro-lista-items-orden-elaboradas']").classList.contains("in")==true){
            return document.querySelector("div[id='modal-filtro-lista-items-orden-elaboradas'] form").getAttribute('id');
        }else if(document.querySelector("div[id='modal-filtro-lista-ordenes-elaboradas']").classList.contains("in")==true){
            return document.querySelector("div[id='modal-filtro-lista-ordenes-elaboradas'] form").getAttribute('id');
        }else{
            return null;
        }
    
    }

    chkTipoOrden(e) {
        if (e.target.checked == true) {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='tipoOrden']").removeAttribute('disabled');
        } else {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='tipoOrden']").setAttribute('disabled', true);
        }
    }

    chkVinculadoPor(e) {
        if (e.target.checked == true) {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='vinculadoPor']").removeAttribute('disabled');
        } else {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='vinculadoPor']").setAttribute('disabled', true);
        }
    }

    chkEmpresa(e) {
        if (e.target.checked == true) {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='empresa']").removeAttribute('disabled');
        } else {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='empresa']").setAttribute('disabled', true);
        }
    }

    chkSede(e) {
        if (e.target.checked == true) {
            let idEmpresa = document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='empresa']").value;
            if(idEmpresa>0){
                document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='sede']").removeAttribute('disabled');
            }else{
                alert("antes debe seleccionar una empresa");
                document.querySelector("form[id="+(this.getNameModalActive())+"] input[name='chkSede']").checked=false;
                document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='sede']").setAttribute('disabled', true);
            }
        } else {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='sede']").setAttribute('disabled', true);
        }
    }

    chkTipoProveedor(e) {
        if (e.target.checked == true) {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='tipoProveedor']").removeAttribute('disabled');
        } else {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='tipoProveedor']").setAttribute('disabled', true);
        }
    }

    chkEnAlmacen(e) {
        if (e.target.checked == true) {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='enAlmacen']").removeAttribute('disabled');
        } else {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='enAlmacen']").setAttribute('disabled', true);
        }
    }

    chkMontoOrden(e) {
        if (e.target.checked == true) {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='signoTotalOrden']").removeAttribute('disabled');
            document.querySelector("form[id="+(this.getNameModalActive())+"] input[name='montoTotalOrden']").removeAttribute('disabled');
        } else {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='signoTotalOrden']").setAttribute('disabled', true);
            document.querySelector("form[id="+(this.getNameModalActive())+"] input[name='montoTotalOrden']").setAttribute('disabled', true);
        }
    }
    chkSubtotal(e) {
        if (e.target.checked == true) {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='signoSubtotal']").removeAttribute('disabled');
            document.querySelector("form[id="+(this.getNameModalActive())+"] input[name='subtotal']").removeAttribute('disabled');
        } else {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='signoSubtotal']").setAttribute('disabled', true);
            document.querySelector("form[id="+(this.getNameModalActive())+"] input[name='subtotal']").setAttribute('disabled', true);
        }
    }
    chkEstado(e) {
        if (e.target.checked == true) {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='estado']").removeAttribute('disabled');
        } else {
            document.querySelector("form[id="+(this.getNameModalActive())+"] select[name='estado']").setAttribute('disabled', true);
        }
    }

    handleChangeFilterReqByEmpresa(event) {
        let id_empresa = event.target.value;
        this.listaOrdenCtrl.getDataSelectSede(id_empresa).then( (res)=> {
            this.llenarSelectSede(res);
        }).catch(function (err) {
            console.log(err)
        })

    }

    llenarSelectSede(array) {
        let selectElement = document.querySelector("form[id='"+this.getNameModalActive()+"'] select[name='sede']");

        if (selectElement.options.length > 0) {
            var i, L = selectElement.options.length - 1;
            for (i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }

        array.forEach(element => {
            let option = document.createElement("option");
            option.text = element.descripcion;
            option.value = element.id_sede;
            selectElement.add(option);
        });
    }

    mostrarCantidadFiltrosActivosCabeceraOrden(){
        document.querySelector("button[id='btnFiltroListaOrdenCabecera'] span[id='cantidadFiltrosActivosCabecera']").textContent= cantidadFiltrosActivosCabecera;

    }
    mostrarCantidadFiltrosActivosDetalleOrden(){
        document.querySelector("button[id='btnFiltroListaOrdenDetalle'] span[id='cantidadFiltrosActivosDetalle']").textContent= cantidadFiltrosActivosDetalle;

    }

    aplicarFiltrosVistaCabeceraOrden(){
        cantidadFiltrosActivosCabecera=0;
        let chkTipoOrden =document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkTipoOrden']").checked;
        let chkVinculadoPor = document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkVinculadoPor']").checked;
        let chkEmpresa = document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkEmpresa']").checked;
        let chkSede = document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkSede']").checked;
        let chkTipoProveedor = document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkTipoProveedor']").checked;
        let chkEnAlmacen = document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkEnAlmacen']").checked;
        let chkMontoOrden = document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkMontoOrden']").checked;
        let chkEstado = document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='chkEstado']").checked;

        let tipoOrden = null;
        let vinculadoPor = null;
        let empresa = null;
        let sede = null;
        let tipoProveedor = null;
        let enAlmacen = null;
        let signoTotalOrden = null;
        let montoTotalOrden = null;
        let estado = null;


        if(chkTipoOrden == true){
            tipoOrden= document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='tipoOrden']").value;
            cantidadFiltrosActivosCabecera++;
        }
        if(chkVinculadoPor == true){
            cantidadFiltrosActivosCabecera++;
            vinculadoPor= document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='vinculadoPor']").value;

        }
        if(chkEmpresa == true){
            cantidadFiltrosActivosCabecera++;
            empresa= document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='empresa']").value;

        }
        if(chkSede == true){
            cantidadFiltrosActivosCabecera++;
            sede= document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='sede']").value;
            
        }
        if(chkTipoProveedor == true){
            cantidadFiltrosActivosCabecera++;
            tipoProveedor= document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='tipoProveedor']").value;

        }
        if(chkEnAlmacen == true){
            cantidadFiltrosActivosCabecera++;
            enAlmacen= document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='enAlmacen']").value;

        }
        if(chkMontoOrden == true){
            cantidadFiltrosActivosCabecera++;
            signoTotalOrden= document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='signoTotalOrden']").value;
            montoTotalOrden= document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] input[name='montoTotalOrden']").value;

            if(montoTotalOrden <= 0 || montoTotalOrden==''){
                alert("Debe igresar un monto mayor a cero");
                return false;
            }
        }
        if(chkEstado == true){
            cantidadFiltrosActivosCabecera++;

            estado= document.querySelector("form[id='formFiltroListaOrdenesElaboradas'] select[name='estado']").value;

        }

        $('#modal-filtro-lista-ordenes-elaboradas').modal('hide');
        this.obtenerListaOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoTotalOrden, montoTotalOrden, estado);

    }

    obtenerListaOrdenesElaboradas(tipoOrden=null, vinculadoPor=null, empresa=null, sede=null, tipoProveedor=null, enAlmacen=null, signoTotalOrden=null, montoTotalOrden=null, estado=null){
        this.listaOrdenCtrl.obtenerListaOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoTotalOrden, montoTotalOrden, estado).then((res)=> {
            this.construirTablaListaOrdenesElaboradas(res);
        }).catch((err)=> {
            console.log(err)
        })
    }


    aplicarFiltrosVistaDetalleOrden(){
        let chkTipoOrden =document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkTipoOrden']").checked;
        let chkVinculadoPor = document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkVinculadoPor']").checked;
        let chkEmpresa = document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkEmpresa']").checked;
        let chkSede = document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkSede']").checked;
        let chkTipoProveedor = document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkTipoProveedor']").checked;
        let chkEnAlmacen = document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkEnAlmacen']").checked;
        let chkSubtotal = document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkSubtotal']").checked;
        let chkEstado = document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='chkEstado']").checked;

        let tipoOrden = null;
        let vinculadoPor = null;
        let empresa = null;
        let sede = null;
        let tipoProveedor = null;
        let enAlmacen = null;
        let signoSubtotal = null;
        let subtotal = null;
        let estado = null;

        if(chkTipoOrden == true){
            tipoOrden= document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='tipoOrden']").value;
            cantidadFiltrosActivosDetalle++;
        }
        if(chkVinculadoPor == true){
            vinculadoPor= document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='vinculadoPor']").value;
            cantidadFiltrosActivosDetalle++;


        }
        if(chkEmpresa == true){
            empresa= document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='empresa']").value;
            cantidadFiltrosActivosDetalle++;


        }
        if(chkSede == true){
            sede= document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='sede']").value;
            cantidadFiltrosActivosDetalle++;

            
        }
        if(chkTipoProveedor == true){
            tipoProveedor= document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='tipoProveedor']").value;
            cantidadFiltrosActivosDetalle++;


        }
        if(chkEnAlmacen == true){
            enAlmacen= document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='enAlmacen']").value;
            cantidadFiltrosActivosDetalle++;


        }
        if(chkSubtotal == true){
            signoSubtotal= document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='signoSubtotal']").value;
            subtotal= document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] input[name='subtotal']").value;
            cantidadFiltrosActivosDetalle++;


            if(subtotal <= 0 || subtotal==''){
                alert("Debe igresar un monto mayor a cero");
                return false;
            }
        }
        if(chkEstado == true){
            estado= document.querySelector("form[id='formFiltroListaItemsOrdenElaboradas'] select[name='estado']").value;
            cantidadFiltrosActivosDetalle++;


        }

        $('#modal-filtro-lista-items-orden-elaboradas').modal('hide');
        
        this.obtenerListaDetalleOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoSubtotal, subtotal, estado);

    }

    construirTablaListaOrdenesElaboradas(data){
        console.log(data);
        let that=this;
        tablaListaOrdenes = $('#listaOrdenes').DataTable({
            'processing':true,
            'destroy':true,
            'language' : vardataTables[0],
            'data': data,
            "order": [[ 0, "desc" ]],

            // "dataSrc":'',
            'dom': 'Bfrtip',
            'scrollX': false,
            'columns': [
                {'data': 'id_orden_compra'},
                {'render':
                function (data, type, row, meta){
                    return `${(row.codigo_oportunidad ?? '')}`;
                    }
                },
                {'render':
                function (data, type, row, meta){
                    return (row.razon_social+' - RUC:'+row.nro_documento)
                }
                },
                {'render':
                function (data, type, row, meta){
                    return '<label class="lbl-codigo handleClickAbrirOrden" title="Ir a orden" data-id-orden="'+row.id_orden_compra+'">'+(row.codigo??'')+'</label>';
                    }
                },
                {
                    'render': function (data, type, row) {
                        let labelRequerimiento='';
                        (row['requerimientos']).forEach(element => {
                            labelRequerimiento += `<label class="lbl-codigo handleClickAbrirRequerimiento" title="Ir a requerimiento"  data-id-requerimiento="${element.id_requerimiento}" >${(element.codigo??'')}</label>`;
                        });
                        return labelRequerimiento;
                        
                    }
                },

                {'render':
                    function (data, type, row, meta){
                        let estadoDetalleOrdenHabilitadasActualizar=[1,2,3,4,5,6,15];
                        if(estadoDetalleOrdenHabilitadasActualizar.includes(row.estado) ==true){
                            return `<center><span class="label label-success handleClickEditarEstadoOrden" data-id-estado-orden-compra="${row.estado}" data-id-orden-compra="${row.id_orden_compra}" style="cursor:pointer;">${row.estado_doc}</span></center>`;
                        }else{
                            return `<center><span class="label label-default" data-id-estado-orden-compra="${row.estado}" data-id-orden-compra="${row.id_orden_compra}" >${row.estado_doc}</span></center>`;

                        }
                    }
                },
                {'render':
                    function (data, type, row, meta){
                    return `${(row.fecha_vencimiento_ocam ?? '')}`;
                }
            },
            {'render':
            function (data, type, row, meta){
                        return `${(row.fecha_ingreso_almacen ?? '')}`;
                    }
                },
                {'render':
                    function (data, type, row, meta){
                        return `${(row.estado_aprobacion_cc ?? '')}`;
                    }
                },
                {'render':
                    function (data, type, row, meta){
                        return `${(row.fecha_estado ?? '')}`;
                    }
                },
                {'render':
                    function (data, type, row, meta){
                        return `${(row.fecha_registro_requerimiento ?? '')}`;
                    }
                },
                {'render':
                    function (data, type, row, meta){
                        let output='No aplica';
                        if(row.id_tp_documento ==2){ // orden de compra
                    
                            let estimatedTimeOfArrive= moment(row['fecha'],'DD-MM-YYYY').add(row['plazo_entrega'], 'days').format('DD-MM-YYYY');
                            let sumaFechaConPlazo =moment(row['fecha'],"DD-MM-YYYY").add(row['plazo_entrega'], 'days').format("DD-MM-YYYY").toString();
                            let fechaActual= moment().format('DD-MM-YYYY').toString();
                            let dias_restantes= moment(sumaFechaConPlazo,'DD-MM-YYYY').diff(moment(fechaActual,'DD-MM-YYYY'), 'days');
                            let porc = dias_restantes * 100 / (parseFloat(row['plazo_entrega'])).toFixed(2);
                            let color = (porc > 50 ? 'success' : ((porc <= 50 && porc > 20) ? 'warning' : 'danger'));
                            output= `<div class="progress-group">
                            <span class="progress-text">${estimatedTimeOfArrive} <br> Nro días Restantes</span>
                            <span class="float-right"><b>${dias_restantes>0?dias_restantes:'0'}</b></span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-${color}" style="width: ${(porc<1)?'100':porc}%"></div>
                            </div>
                        </div>`;

                        }
                        return output;
                    }
                },
                {'data': 'descripcion_sede_empresa'},
                {'data': 'condicion'},
                {'data': 'fecha'},
                {'render':
                    function (data, type, row, meta){
                        let fechaOrden =moment(row.fecha);
                        let fechaRequerimiento =moment(row.fecha_registro_requerimiento);
                        let tiempoAtencionLogistica = fechaOrden.diff((fechaRequerimiento), 'days');
                        return `${tiempoAtencionLogistica>0?(tiempoAtencionLogistica+' días'):'0 días'} `;
                    }
                },
                {'render':
                    function (data, type, row, meta){
                        let fechaIngresoAlmacen =moment(row.fecha_ingreso_almacen);
                        let fechaOrden =moment(row.fecha);
                        let tiempoAtencionProveedor = fechaOrden.diff((fechaIngresoAlmacen), 'days');
                        if(row.fecha_ingreso_almacen !=null){
                            return `${tiempoAtencionProveedor>0?(tiempoAtencionProveedor+' días'):'0 días'}`;
                        }else{
                            return '';
                        }
                    }
                },
                {'data': 'facturas'},
                {'render':
                    function (data, type, row, meta){
                        return row.monto_total_presup>0?(parseFloat(row.monto_total_presup)).toFixed(2):'(No aplica)';

                    }
                },
                {'render':
                    function (data, type, row, meta){
                        let total=0;
                        if(row.id_moneda ==2){
                            if(parseFloat(row.tipo_cambio_compra) >0){
                                total = '<span title="$'+row.monto_total_orden+'">'+"S/"+($.number((row.monto_total_orden *row.tipo_cambio_compra),2))+'</span>';
                            }else{
                                total =(row.moneda_simbolo+(($.number(row.monto_total_orden,2))));

                            }
                        }else{
                            total =(row.moneda_simbolo+(($.number(row.monto_total_orden,2))));

                        }
                        return total;
                    }
                },
                {'render':
                    function (data, type, row, meta){
                        let containerOpenBrackets='<div class="btn-group" role="group" style="margin-bottom: 5px;display: flex;flex-direction: row;flex-wrap: nowrap;">';
                        let btnImprimirOrden= '<button type="button" class="btn btn-md btn-warning boton handleClickAbrirOrdenPDF" title="Abrir orden PDF"  data-toggle="tooltip" data-placement="bottom" data-id-orden-compra="'+row.id_orden_compra+'"  data-id-pago=""> <i class="fas fa-file-pdf"></i> </button>';
                        let btnAnularOrden='';
                        if(![6,27,28].includes(row.estado) ){
                            btnAnularOrden = '<button type="button" class="btn btn-md btn-danger boton handleClickAnularOrden" name="btnAnularOrden" title="Anular orden" data-codigo-orden="'+row.codigo+'" data-id-orden-compra="'+row.id_orden_compra+'"><i class="fas fa-backspace fa-xs"></i></button>';
                        }
                        let btnVerDetalle= `<button type="button" class="ver-detalle btn btn-primary boton handleCliclVerDetalleOrden" data-toggle="tooltip" data-placement="bottom" title="Ver Detalle" data-id="${row.id_orden_compra}">
                        <i class="fas fa-chevron-down"></i>
                        </button>`;
                        let containerCloseBrackets='</div>';
                        return (containerOpenBrackets+btnVerDetalle+btnImprimirOrden+btnAnularOrden+containerCloseBrackets);
                    }
                }
                
            ],
            'columnDefs': [
                { 'aTargets': [0], 'visible': false, 'searchable': false},
                { 'aTargets': [1], 'className': "text-right" },
                { 'aTargets': [19], 'className': "text-right"}
            ]
            ,"initComplete": function() {

                let listaOrdenes_filter = document.querySelector("div[id='listaOrdenes_filter']");

                var divInputGroup = document.createElement("div");
                divInputGroup.className = "input-group pull-left";
                divInputGroup.style = "padding-right: 15px;";
                listaOrdenes_filter.appendChild(divInputGroup);

                var divInputGroupBtn = document.createElement("div");
                divInputGroupBtn.className = "input-group-btn";
                divInputGroup.appendChild(divInputGroupBtn);     

                var buttonFiler = document.createElement("button");
                buttonFiler.type = "button";
                buttonFiler.id = "btnFiltroListaOrdenCabecera";
                buttonFiler.className = "btn btn-default pull-left";
                buttonFiler.innerHTML = "<i class='fas fa-filter'></i> Filtros: <span id='cantidadFiltrosActivosCabecera'>0</span>";
                buttonFiler.addEventListener('click', that.filtroTablaListaOrdenesVistaCabecera, false);

                divInputGroupBtn.appendChild(buttonFiler);   

                var buttonExportToExcel = document.createElement("button");
                buttonExportToExcel.type = "button";
                buttonExportToExcel.id = "btnExportarAExcel";
                buttonExportToExcel.className = "btn btn-default pull-left";
                buttonExportToExcel.innerHTML = "<i class='far fa-file-excel'></i> Descargar";
                buttonExportToExcel.addEventListener('click', that.exportTableToExcel.bind(that), false);

                divInputGroupBtn.appendChild(buttonExportToExcel);     
                
                that.mostrarCantidadFiltrosActivosCabeceraOrden();
                



            },
            "createdRow": function (row, data, dataIndex) {
               
                    $(row.childNodes[14]).css('background-color', '#b4effd');
                    $(row.childNodes[14]).css('font-weight', 'bold');
                    $(row.childNodes[15]).css('background-color', '#b4effd');
                    $(row.childNodes[15]).css('font-weight', 'bold');
                

            }
        });


    }

    construirDetalleOrdenElaboradas(table_id,row,response){
        var html = '';
        if (response.length > 0) {
            response.forEach(function (element) {
                html += `<tr>
                    <td style="border: none;">${(element.nro_orden !== null ? `<a  style="cursor:pointer;" class="handleClickObtenerArchivos" data-id="${element.id_oc_propia}" data-tipo="${element.tipo_oc_propia}">${element.nro_orden}</a>`:'')}</td>
                    <td style="border: none;">${element.codigo_oportunidad !== null ? element.codigo_oportunidad : ''}</td>
                    <td style="border: none;">${element.nombre_entidad !== null ? element.nombre_entidad : ''}</td>
                    <td style="border: none;">${element.nombre_corto_responsable !== null ? element.nombre_corto_responsable : ''}</td>
                    <td style="border: none;"><label class="lbl-codigo handleClickAbrirRequerimiento" title="Abrir Requerimiento" data-id-requerimiento="${element.id_requerimiento}">${element.codigo_req??''}</label></td>
                    <td style="border: none;">${element.codigo??''}</td>
                    <td style="border: none;">${element.part_number??''}</td>
                    <td style="border: none;">${element.descripcion? element.descripcion:(element.descripcion_adicional?element.descripcion_adicional:'')}</td>
                    <td style="border: none;">${element.cantidad?element.cantidad:''}</td>
                    <td style="border: none;">${element.abreviatura?element.abreviatura:''}</td>
                    <td style="border: none;">${element.moneda_simbolo}${$.number(element.precio,2)}</td>
                    <td style="border: none;">${element.moneda_simbolo}${$.number((element.cantidad*element.precio),2)}</td>
                    </tr>`;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">O/C</th>
                        <th style="border: none;">Cod.CDP</th>
                        <th style="border: none;">Cliente</th>
                        <th style="border: none;">Responsable</th>
                        <th style="border: none;">Cod.Req.</th>
                        <th style="border: none;">Código</th>
                        <th style="border: none;">PartNumber</th>
                        <th style="border: none;">Descripción</th>
                        <th style="border: none;">Cantidad</th>
                        <th style="border: none;">Und.Med</th>
                        <th style="border: none;">Prec.Unit.</th>
                        <th style="border: none;">Total</th>
                    </tr>
                </thead>
                <tbody style="background: #e7e8ea;">${html}</tbody>
                </table>`;
        }else{
            var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
            }
            row.child(tabla).show();
    }

    obtenerArchivos(id,tipo){
        obtenerArchivosMgcp(id, tipo);

    }

    abrirRequerimientoPDF(idRequerimiento){
        let url =`/logistica/gestion-logistica/requerimiento/elaboracion/imprimir-requerimiento-pdf/${idRequerimiento}/0`;
        var win = window.open(url, "_blank");
        win.focus(); 
    }
    abrirRequerimiento(idRequerimiento){
        localStorage.setItem('idRequerimiento', idRequerimiento);
        let url = "/logistica/gestion-logistica/requerimiento/elaboracion/index";
        var win = window.open(url, "_blank");
        win.focus(); 
    }

    abrirOrden(idOrden){
        sessionStorage.removeItem('reqCheckedList');
        sessionStorage.removeItem('tipoOrden');
        sessionStorage.setItem("idOrden",idOrden);
        sessionStorage.setItem("action",'historial');

        let url ="/logistica/gestion-logistica/compras/ordenes/elaborar/index";
        var win = window.open(url, '_blank');
        win.focus();
    }

    abrirOrdenPDF(idOrden){
        let url =`/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${idOrden}`;
        var win = window.open(url, "_blank");
        win.focus(); 
    }

 

    verDetalleOrden(obj){
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
            this.buildFormat(iTableCounter, id, row);
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
        }    }   


        buildFormat(table_id, id, row) {
            this.listaOrdenCtrl.obtenerDetalleOrdenElaboradas(id).then((res)=> {
                this.construirDetalleOrdenElaboradas(table_id,row,res);
            }).catch((err)=> {
                console.log(err)
            })
        }

    // vista nivel de items

    obtenerListaDetalleOrdenesElaboradas(tipoOrden=null, vinculadoPor=null, empresa=null, sede=null, tipoProveedor=null, enAlmacen=null, signoSubtotal=null, Subtotal=null, estado=null){
        this.listaOrdenCtrl.obtenerListaDetalleOrdenesElaboradas(tipoOrden, vinculadoPor, empresa, sede, tipoProveedor, enAlmacen, signoSubtotal, Subtotal, estado).then((res)=> {
            this.construirTablaListaDetalleOrdenesElaboradas(res);
        }).catch((err)=> {
            console.log(err)
        })
    }

    construirTablaListaDetalleOrdenesElaboradas(data){
        let that = this;
        $('#listaDetalleOrden').DataTable({
            'processing':true,
            'destroy':true,
            'language' : vardataTables[0],
            'dom': 'Bfrtip',
            'scrollX': false,
            'order': [13, 'desc'],
            'data': data,
            'columns': [
                { render: function (data, type, row) {     
                    return `<span class="label label-primary handleClickVerOrdenModal" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}"  data-codigo-requerimiento="${row.codigo_requerimiento}" data-id-requerimiento="${row.id_requerimiento}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: pointer;" title="Ver Orden">${row.codigo}</span>`;
                    }
                },
                { render: function (data, type, row) {   
                    return `${row.codigo_requerimiento?row.codigo_requerimiento:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.codigo_softlink?row.codigo_softlink:''}`;
    
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.concepto?row.concepto:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.razon_social_cliente?row.razon_social_cliente:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.razon_social?row.razon_social:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.subcategoria?row.subcategoria:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.categoria?row.categoria:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.part_number?row.part_number:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.alm_prod_descripcion?row.alm_prod_descripcion:''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.detalle_orden_precio?(row.simbolo_moneda+Util.formatoNumero(row.detalle_orden_precio,2)):''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.cdc_precio?((row.moneda_pvu=='s'?'S/':row.moneda_pvu=='d'?'$':'')+Util.formatoNumero(row.cdc_precio,2)):''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.fecha?moment(row.fecha).format('YYYY-MM-DD'):''}`;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.plazo_entrega>0?row.plazo_entrega+' días':''}`;
                    }
                },
                { render: function (data, type, row) {     
                    
                    let output='No aplica';
                    if(row['id_tp_documento'] ==2){ // orden de compra
                        let estimatedTimeOfArrive= moment(row['fecha'],'DD-MM-YYYY').add(row['plazo_entrega'], 'days').format('DD-MM-YYYY');
                        let sumaFechaConPlazo =moment(row['fecha'],"DD-MM-YYYY").add(row['plazo_entrega'], 'days').format("DD-MM-YYYY").toString();
                        let fechaActual= moment().format('DD-MM-YYYY').toString();
                        let dias_restantes= moment(sumaFechaConPlazo,'DD-MM-YYYY').diff(moment(fechaActual,'DD-MM-YYYY'), 'days');
                        let porc = dias_restantes * 100 / (parseFloat(row['plazo_entrega'])).toFixed(2);
                        let color = (porc > 50 ? 'success' : ((porc <= 50 && porc > 20) ? 'warning' : 'danger'));
                    output= `<div class="progress-group">
                        <span class="progress-text">${estimatedTimeOfArrive} <br> Nro días Restantes</span>
                        <span class="float-right"><b>${dias_restantes>0?dias_restantes:'0'}</b></span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-${color}" style="width: ${(porc<1)?'100':porc}%"></div>
                        </div>
                    </div>`;

                    }
                    return output;
                    }
                },
                { render: function (data, type, row) {     
                    return `${row.empresa_sede?row.empresa_sede:''}`;
                    }
                },
                { render: function (data, type, row) {    
                    let estadoDetalleOrdenHabilitadasActualizar=[1,2,3,4,5,6,15];
                    if(estadoDetalleOrdenHabilitadasActualizar.includes(row.id_detalle_orden_estado) ==true){
                        return `<span class="label label-success handleClickEditarEstadoItemOrden" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.detalle_orden_estado}</span>`;
                    }else{
                        return `<span class="label label-default" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: default;">${row.detalle_orden_estado}</span>`;
                    }
    
                    }
                },
                { render: function (data, type, row) {         
                        let containerOpenBrackets = '<div class="btn-group btn-group-sm" role="group" style="margin-bottom: 5px;display: flex;flex-direction: row;flex-wrap: nowrap;">';
                        let btnImprimirOrden = '<button type="button" class="btn btn-md btn-warning boton handleClickAbrirOrdenPDF" name="btnGenerarOrdenRequerimientoPDF" title="Abrir orden PDF" data-id-requerimiento="'+row.id_requerimiento+'"  data-codigo-requerimiento="'+row.codigo_requerimiento+'" data-id-orden-compra="'+row.id_orden_compra+'"><i class="fas fa-file-download fa-xs"></i></button>';
                        let btnAnularOrden='';
                        if(![6,27,28].includes(row.orden_estado) ){
                            btnAnularOrden = '<button type="button" class="btn btn-md btn-danger boton handleClickAnularOrden" name="btnAnularOrden" title="Anular orden" data-codigo-orden="'+row.codigo+'" data-id-orden-compra="'+row.id_orden_compra+'"><i class="fas fa-backspace fa-xs"></i></button>';
                        }
                        let btnDocumentosVinculados = '<button type="button" class="btn btn-md btn-primary boton handleClickDocumentosVinculados" name="btnDocumentosVinculados" title="Ver documentos vinculados" data-id-requerimiento="'+row.id_requerimiento+'"  data-codigo-requerimiento="'+row.codigo_requerimiento+'" data-id-orden-compra="'+row.id_orden_compra+'"><i class="fas fa-folder fa-xs"></i></button>';
                        let containerCloseBrackets = '</div>';
                        return (containerOpenBrackets+btnImprimirOrden+btnDocumentosVinculados+btnAnularOrden+containerCloseBrackets);

                    }   
                }   
            ],
            'columnDefs': [
                { 'aTargets': [0],'className': "text-center" },
                { 'aTargets': [1],'className': "text-center" },
                { 'aTargets': [2],'className': "text-center" },
                { 'aTargets': [3],'className': "text-left" },
                { 'aTargets': [4],'className': "text-center" },
                { 'aTargets': [5],'className': "text-center" },
                { 'aTargets': [6],'className': "text-center" },
                { 'aTargets': [7],'className': "text-center" },
                { 'aTargets': [8],'className': "text-center" },
                { 'aTargets': [9],'className': "text-left" },
                { 'aTargets': [10],'className': "text-right" },
                { 'aTargets': [11],'className': "text-right" },
                { 'aTargets': [12],'className': "text-center" },
                { 'aTargets': [13],'className': "text-center" },
                { 'aTargets': [14],'className': "text-center" },
                { 'aTargets': [15],'className': "text-center" },
                { 'aTargets': [16],'className': "text-center" },
                { 'aTargets': [17],'className': "text-center" }
            ],

            "initComplete": function() {

                let listaDetalleOrden_filter = document.querySelector("div[id='listaDetalleOrden_filter']");
                var buttonFiler = document.createElement("button");
                buttonFiler.type = "button";
                buttonFiler.id = "btnFiltroListaOrdenDetalle";
                buttonFiler.className = "btn btn-default pull-left";
                buttonFiler.style = "margin-right: 30px;";
                buttonFiler.innerHTML = "<i class='fas fa-filter'></i> Filtros: <span id='cantidadFiltrosActivosDetalle'>0</span>";
                buttonFiler.addEventListener('click', that.filtroTablaListaOrdenesVistaDetalle, false);

                listaDetalleOrden_filter.appendChild(buttonFiler);      
                
                that.mostrarCantidadFiltrosActivosDetalleOrden();


            
            }
            // 'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
    }

    verOrdenModal(obj){
        // let codigo = obj.dataset.codigoOrdenCompra;
        let id_orden = obj.dataset.idOrdenCompra;
        // let id_estado_actual = obj.dataset.idEstadoOrdenCompra;
        // console.log(id_orden);
    
        $('#modal-ver-orden').modal({
            show: true,
            backdrop: 'true'
        });
        this.listaOrdenCtrl.ver_orden(id_orden).then((res)=> {
            if (res.status ==200){
                this.llenarCabeceraOrden(res.data.orden);
                this.llenarTablaItemsOrden(res.data.detalle_orden);
            }else{
                alert("sin data");
            }
        }).catch((err)=> {
            console.log(err)
        })
    }

    llenarTablaItemsOrden(data){
        let that = this;
        $('#tablaItemOrdenCompra').dataTable({
            bDestroy: true,
            order: [[0, 'asc']],
            info:     true,
            iDisplayLength:2,
            paging:   true,
            searching: false,
            language: vardataTables[0],
            processing: true,
            bDestroy: true,
            data:data ,
            columns: [
                {'render':
                    function (data, type, row, meta){
                        return meta.row +1;
                    }
                },
     
                { data: 'codigo_producto' },
                { data: 'part_number' },
                { data: 'categoria' },
                { data: 'subcategoria' },
                { data: 'descripcion' },
                { data: 'unidad_medida' },
                { data: 'cantidad' },
                {'render':
                    function (data, type, row){
                        return `${row.precio_unitario?((row.simbolo_moneda?row.simbolo_moneda:'')+Util.formatoNumero(row.precio_unitario,2)):''}`;
                    }
                },
                {'render':
                    function (data, type, row){
                        return `${row.subtotal?((row.simbolo_moneda?row.simbolo_moneda:'')+Util.formatoNumero(row.subtotal,2)):''}`;
                    }
                },
                {'render':
                    function (data, type, row, meta){

                        return row.estado_detalle_orden??'';
                        // let estadoDetalleOrdenHabilitadasActualizar=[1,2,3,4,5,6,15];
    
                        // if(estadoDetalleOrdenHabilitadasActualizar.includes(row.id_estado_detalle_orden)==true){
                        //     return `<span class="label label-default handleClickEditarEstadoItemOrden" data-id-estado-detalle-orden-compra="${row.id_estado_detalle_orden}" data-id-orden-compra="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_item}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.estado_detalle_orden}</span>`;
                        // }else{
                        //     return `<span class="label label-default" data-id-estado-detalle-orden-compra="${row.id_estado_detalle_orden}" data-id-orden-compra="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_item}" style="cursor: default;" >${row.estado_detalle_orden}</span>`;
                        // }
                    }
                },
            ],
            'columnDefs': [
                { 'aTargets': [0],'className': "text-center" },
                { 'aTargets': [1],'className': "text-center" },
                { 'aTargets': [2],'className': "text-center" },
                { 'aTargets': [3],'className': "text-center" },
                { 'aTargets': [4],'className': "text-center" },
                { 'aTargets': [5],'className': "text-left" },
                { 'aTargets': [6],'className': "text-center" },
                { 'aTargets': [7],'className': "text-center" },
                { 'aTargets': [8],'className': "text-right" },
                { 'aTargets': [9],'className': "text-right" },
                { 'aTargets': [10],'className': "text-center" }
            ],
            "initComplete": function() {

                $('#tablaItemOrdenCompra tbody').on("click","span.handleClickEditarEstadoItemOrden",function(e){
                    that.editarEstadoItemOrden(e.currentTarget);
                });
            },
        })
    
        let tablelistaitem = document.getElementById('tablaItemOrdenCompra_wrapper');
        tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    }

    llenarCabeceraOrden(data){
        // console.log(data);
        document.querySelector("span[id='inputCodigo']").textContent = data.codigo;
        document.querySelector("p[id='inputProveedor']").textContent = data.razon_social+' RUC: '+data.nro_documento;
        document.querySelector("p[id='inputFecha']").textContent = data.fecha;
        document.querySelector("p[id='inputMoneda']").textContent = data.descripcion_moneda;
        document.querySelector("p[id='inputCondicion']").textContent = data.condicion+' '+data.plazo_dias+' días';
        document.querySelector("p[id='inputPlazoEntrega']").textContent = data.plazo_entrega;
        document.querySelector("p[id='inputCodigoSoftlink']").textContent = data.codigo_softlink;
        document.querySelector("p[id='inputEstado']").textContent = data.estado_doc;
   
    }

    editarEstadoOrden(obj){
        let id_orden = obj.dataset.idOrdenCompra;
        let id_estado_actual = obj.dataset.idEstadoOrdenCompra;
        let codigo = obj.dataset.codigoOrdenCompra;
    
        $('#modal-editar-estado-orden').modal({
            show: true,
            backdrop: 'true'
        });
    
        document.querySelector("div[id='modal-editar-estado-orden'] input[name='id_orden_compra'").value = id_orden;
        document.querySelector("div[id='modal-editar-estado-orden'] span[name='codigo_orden_compra'").textContent = codigo;
    
    }

    editarEstadoItemOrden(obj){
        let id_orden_compra = obj.dataset.idOrdenCompra;
        let id_detalle_orden = obj.dataset.idDetalleOrdenCompra;
        let id_estado_actual = obj.dataset.idEstadoDetalleOrdenCompra;
        let codigo_item = obj.dataset.codigoItem;
    
        $('#modal-editar-estado-detalle-orden').modal({
            show: true,
            backdrop: 'true'
        });
    
        document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_orden_compra'").value = id_orden_compra;
        document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_detalle_orden_compra'").value = id_detalle_orden;
        document.querySelector("div[id='modal-editar-estado-detalle-orden'] span[name='codigo_item_orden_compra'").textContent = codigo_item;
    
        document.querySelector("select[name='estado_detalle_orden']").value=id_estado_actual;

    }

    updateEstadoOrdenCompra(){
        let id_orden_compra = document.querySelector("div[id='modal-editar-estado-orden'] input[name='id_orden_compra'").value;
        let id_estado_orden_selected = document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'").value;
        let estado_orden_selected = document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'")[document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'").selectedIndex].textContent;

        this.listaOrdenCtrl.actualizarEstadoOrdenPorRequerimiento(id_orden_compra,id_estado_orden_selected).then((res)=>{
            this.tipoVistaPorCabecera();

            if(res ==1){
                Lobibox.notify('success', {
                    title:false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: `El estado de orden actualizado`
                });
                // document.querySelector("span[id='estado_orden']").textContent = estado_orden_selected;
                $('#modal-editar-estado-orden').modal('hide');
            }else{
                Swal.fire(
                    '',
                    'Lo sentimos hubo un problema en el servidor al intentar actualizar el estado, por favor vuelva a intentarlo',
                    'error'
                );
            }
        }).catch(function(err){
            console.log(err)
            Swal.fire(
                '',
                'Lo sentimos hubo un problema en el servidor al intentar actualizar el estado, por favor vuelva a intentarlo',
                'error'
            );
        })
        
    }

    updateEstadoDetalleOrdenCompra(){
        let id_orden_compra = document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_orden_compra'").value;
        let id_detalle_orden_compra = document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_detalle_orden_compra'").value;
        let id_estado_detalle_orden_selected = document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'").value;
        let estado_detalle_orden_selected = document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'")[document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'").selectedIndex].textContent;

        this.listaOrdenCtrl.actualizarEstadoDetalleOrdenPorRequerimiento(id_detalle_orden_compra,id_estado_detalle_orden_selected).then((res)=>{
            this.tipoVistaPorItem();
            if(res ==1){
                Lobibox.notify('success', {
                    title:false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: `El estado del item fue actualizado`
                });
                this.listaOrdenCtrl.ver_orden(id_orden_compra).then((res)=> {
                    if (res.status ==200){
                        this.llenarCabeceraOrden(res.data.orden);
                        this.llenarTablaItemsOrden(res.data.detalle_orden);
                    }else{
                        Lobibox.notify('info', {
                            title:false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: `sin data disponible para mostrar`
                        });
                     
                    }
                }).catch((err)=> {
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un problema en el servidor, por favor vuelva a intentarlo',
                        'error'
                    );
                    console.log(err)
                })
                $('#modal-editar-estado-detalle-orden').modal('hide');
            }else{
                Swal.fire(
                    '',
                    'Lo sentimos hubo un problema al intentar actualizar el estado, por favor vuelva a intentarlo',
                    'error'
                );
                
            }
        }).catch(function(err){
            console.log(err)
            Swal.fire(
                '',
                'Lo sentimos hubo un problema en el servidor al intentar actualizar el estado, por favor vuelva a intentarlo',
                'error'
            );
        })
        
    }

    generarOrdenRequerimientoPDF(obj){
        let id_orden = obj.dataset.idOrdenCompra;
        window.open('generar-orden-pdf/'+id_orden);
    }

    anularOrden(obj){
        let codigoOrden = obj.dataset.codigoOrden;
        let id = obj.dataset.idOrdenCompra;
        Swal.fire({
            title: 'Esta seguro que desea anular la orden '+codigoOrden+'?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, anular'

        }).then((result) => {
            if (result.isConfirmed) {
                this.listaOrdenCtrl.anularOrden(id).then((res)=> {
                    if (res.status == 200) {
                        Lobibox.notify('success', {
                            title:false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: 'Orden anulada'
                        });
                        // location.reload();
                        obj.closest('tr').remove();

                    } else {
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un error en el servidor al intentar anular la orden, por favor vuelva a intentarlo',
                            'error'
                        );
                        console.log(res);
                    }
                }).catch( (err)=> {
                    console.log(err)
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor, por favor vuelva a intentarlo',
                        'error'
                    );
                });


            }
        })

    }

    documentosVinculados(obj){
        $('#modal-documentos-vinculados').modal({
            show: true,
            backdrop: 'static'
        });

        let id_orden_compra = obj.dataset.idOrdenCompra;
        this.listaOrdenCtrl.listarDocumentosVinculados(id_orden_compra).then((res)=> {
            this.llenarTablaDocumentosVinculados(res.data);
        }).catch((err)=> {
        console.log(err)
        })
    }

    llenarTablaDocumentosVinculados(data){
        var vardataTables = funcDatatables();
        $('#tablaDocumentosVinculados').dataTable({
            'info':     false,
            'searching': false,
            'paging':   false,
            'language' : vardataTables[0],
            'processing': true,
            "bDestroy": true,
            'data':data,
            'columns': [
                {'render':
                    function (data, type, row){
                        return `<a href="${row.orden_fisica}" target="_blank"><span class="label label-warning">Orden Física</span></a> 
                        <a href="${row.orden_electronica}" target="_blank"><span class="label label-info">Orden Electrónica</span></a>`;
                    }
                }
            ]
        });
        let tableDocumentosVinculados = document.getElementById(
            'tablaDocumentosVinculados_wrapper'
        )
        tableDocumentosVinculados.childNodes[0].childNodes[0].hidden = true;
    }
    
}
