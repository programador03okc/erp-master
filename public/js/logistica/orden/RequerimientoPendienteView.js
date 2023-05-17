
// ============== View =========================
var vardataTables = funcDatatables();

var itemsParaCompraList = []
var reqTrueList = []
var listCheckReq = []
var infoStateInput = [];
var tempDetalleItemsParaCompraCC = [];

var $tablaListaRequerimientosPendientes;
var iTableCounter = 1;
var oInnerTable;

var objBtnMapeo;
var trRequerimientosPendientes;

var reqTrueList = [];
var $tablaListaRequerimientosAtendidos;
class RequerimientoPendienteView {
    constructor(requerimientoPendienteCtrl) {
        this.requerimientoPendienteCtrl = requerimientoPendienteCtrl;
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        vista_extendida();

        this.ActualParametroEmpresa = 'SIN_FILTRO';
        this.ActualParametroSede = 'SIN_FILTRO';
        this.ActualParametroFechaDesde = 'SIN_FILTRO';
        this.ActualParametroFechaHasta = 'SIN_FILTRO';
        this.ActualParametroReserva = 'SIN_FILTRO';
        this.ActualParametroOrden = 'SIN_FILTRO';
        this.ActualParametroEstado = 'SIN_FILTRO';

    }

    initializeEventHandler() {
        // $('#modal-atender-con-almacen').on("click","button.handleClickGuardarAtendidoConAlmacen", ()=>{
        //     this.guardarAtendidoConAlmacen();
        // });
        $('#lista_compras').on("click", ".handleClickTabRequerimientosPendientes", () => {
            this.tabRequerimientosPendientes();
        });
        $('#lista_compras').on("click", ".handleClickTabRequerimientosAtendidos", () => {
            this.tabRequerimientosAtendidos();
        });

        $('#modal-filtro-requerimientos-pendientes').on("click", "input[type=checkbox]", (e) => {
            this.estadoCheckFiltroRequerimientosPendientes(e);
        });
        $('#modal-filtro-requerimientos-pendientes').on("change", "select.handleChangeUpdateValorFiltroRequerimientosPendientes", (e) => {
            this.updateValorFiltroRequerimientosPendientes(e);
        });
        $('#modal-filtro-requerimientos-pendientes').on("blur", "input.handleBlurUpdateValorFiltroRequerimientosPendientes", (e) => {
            this.updateValorFiltroRequerimientosPendientes(e);
        });

        $('#modal-filtro-requerimientos-atendidos').on("click", "input[type=checkbox]", (e) => {
            this.estadoCheckFiltroRequerimientosAtendidos(e);
        });
        $('#modal-filtro-requerimientos-atendidos').on("change", "select.handleChangeUpdateValorFiltroRequerimientosAtendidos", (e) => {
            this.updateValorFiltroRequerimientosAtendidos(e);
        });
        $('#modal-filtro-requerimientos-atendidos').on("blur", "input.handleBlurUpdateValorFiltroRequerimientosAtendidos", (e) => {
            this.updateValorFiltroRequerimientosAtendidos(e);
        });


        $('#requerimientos_pendientes').on("click", "button.handleClickCrearOrdenCompra", () => {
            this.crearOrdenCompra();
        });

        $('#listaRequerimientosPendientes tbody').on("click", "label.handleClickAbrirRequerimiento", (e) => {
            this.abrirRequerimiento(e.currentTarget.dataset.idRequerimiento);
        });
        // $('#listaRequerimientosPendientes tbody').on("click","button.handleClickObservarRequerimientoLogistica",(e)=>{
        //     this.observarRequerimientoLogistica(e.currentTarget.dataset.idRequerimiento);
        // });
        // $('#form-observar-requerimiento-logistica').on("click","button.handleClickRegistrarObservaciónRequerimientoLogistica",()=>{
        //     this.registrarObservaciónRequerimientoLogistica();
        // });

        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickVerDetalleRequerimiento", (e) => {
            // var data = $('#listaRequerimientosPendientes').DataTable().row($(this).parents("tr")).data();
            this.verDetalleRequerimiento(e.currentTarget);
        });
        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickObservarRequerimientoLogistico", (e) => {
            this.observarRequerimientoLogistico(e.currentTarget);
        });
        $('#listaRequerimientosAtendidos tbody').on("click", "button.handleClickVerDetalleRequerimiento", (e) => {
            this.verDetalleRequerimientoAtendidos(e.currentTarget);
        });
        $('#listaRequerimientosAtendidos tbody').on("click", "button.handleClickRetornarAListaPendientes", (e) => {
            this.retornarAListaPendientes(e.currentTarget);
        });

        $('body').on("click", "span.handleClickModalVerOrdenDeRequerimiento", (e) => { // tab para lista pendiente tab lista atendidos
            this.modalVerOrdenDeRequerimiento(e.currentTarget);
        });

        $('body').on("click", "label.handleClickAbrirOrden", (e) => { // tab para lista pendiente tab lista atendidos
            this.abrirOrden(e.currentTarget);
        });


        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickAtenderConAlmacen", (e) => {
            this.atenderConAlmacen(e.currentTarget);
        });

        $('#listaRequerimientosAtendidos tbody').on("click", "button.handleClickAtenderConAlmacen", (e) => {
            this.atenderConAlmacen(e.currentTarget);
        });


        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickOpenModalCuadroCostos", (e) => {
            this.openModalCuadroCostos(e.currentTarget);
        });
        $('#listaRequerimientosAtendidos tbody').on("click", "button.handleClickOpenModalCuadroCostos", (e) => {
            this.openModalCuadroCostos(e.currentTarget);
        });

        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickCrearOrdenCompraPorRequerimiento", (e) => {
            this.crearOrdenCompraPorRequerimiento(e.currentTarget);
        });
        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickGestionarEstadoRequerimiento", (e) => {
            this.gestionarEstadoRequerimiento(e.currentTarget);
        });
        // $('#modal-gestionar-estado-requerimiento').on("click", "button.handleClickControlTodoCheckEnAtencionTotal", (e) => {
        //     this.controlCheckEnAtencionTotal(e.currentTarget);
        // });
        // $('#modal-gestionar-estado-requerimiento').on("click", "input.handleCheckPressMarcarItemAtendidoTotal", (e) => {
        //     this.checkPressItemAtendidoTotal(e.currentTarget);
        // });
        $('#modal-gestionar-estado-requerimiento').on("blur", "input.handleBlurUpdateValorNuevaCantidad", (e) => {
            this.updateValorNuevaCantidad(e.currentTarget);
        });
        $('#modal-gestionar-estado-requerimiento').on("click", "button.handleClickActualizarGestionEstadoRequerimiento", () => {
            this.actualizarGestionEstadoRequerimiento();
        });
        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickCrearOrdenServicioPorRequerimiento", (e) => {
            this.crearOrdenServicioPorRequerimiento(e.currentTarget);
        });
        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickSolicitudCotizacionExcel", (e) => {
            this.solicitudCotizacionExcel(e.currentTarget);
        });
        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickVerAdjuntoDetalleRequerimiento", (e) => {
            this.verAdjuntoDetalleRequerimiento(e.currentTarget);
        });
        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickActualizarTipoItem", (e) => {
            this.actualizarTipoItem(e.currentTarget);
        });
        $('#listaRequerimientosAtendidos tbody').on("click", "button.handleClickVerAdjuntoDetalleRequerimiento", (e) => {
            this.verAdjuntoDetalleRequerimiento(e.currentTarget);
        });
        $('#listaRequerimientosAtendidos tbody').on("click", "button.handleClickAnularReservaActiva", (e) => {
            this.anularReservaActiva(e.currentTarget);
        });
        $('#listaRequerimientosPendientes tbody').on("click", "button.handleClickVerTodoAdjuntos", (e) => {
            this.verTodoAdjuntos(e.currentTarget);
        });
        $('#listaRequerimientosAtendidos tbody').on("click", "button.handleClickVerTodoAdjuntos", (e) => {
            this.verTodoAdjuntos(e.currentTarget);
        });
        $('#modal-adjuntos-detalle-requerimiento').on("click", "button.handleClickDescargarArchivoDetalleRequerimiento", (e) => {
            this.descargarArchivoDetalleRequerimiento(e.currentTarget);
        });
        $('#modal-todo-adjuntos').on("click", "button.handleClickDescargarArchivoDetalleRequerimiento", (e) => {
            this.descargarArchivoDetalleRequerimiento(e.currentTarget);
        });
        $('#modal-todo-adjuntos').on("click", "button.handleClickDescargarArchivoRequerimiento", (e) => {
            this.descargarArchivoRequerimiento(e.currentTarget);
        });


        $('#modal-filtro-requerimientos-pendientes').on("change", "select.handleChangeFiltroEmpresa", (e) => {
            // this.getDataSelectSede(e.currentTarget.value);
        });


        $('#modal-nueva-reserva').on("click", "button.handleClickSeleccionarAlmacenParaReserva", (e) => {
            this.seleccionarAlmacenParaReserva(e.currentTarget);
        });


        $('#listaRequerimientosPendientes tbody').on("click", "button.mapeo", (e) => {
            var id_requerimiento = e.currentTarget.dataset.idRequerimiento;
            var codigo = e.currentTarget.dataset.codigo;
            objBtnMapeo = e.currentTarget;
            $('#modal-mapeoItemsRequerimiento').modal({
                show: true
            });
            $('[name=id_requerimiento]').val(id_requerimiento);
            $('#cod_requerimiento').text(codigo);
            listarItemsRequerimientoMapeo(id_requerimiento);

            $('#submit_mapeoItemsRequerimiento').removeAttr('disabled');
        });

        $('#listaItemsRequerimientoParaAtenderConAlmacen tbody').on("click", "button.handleClickAbrirModalNuevaReserva", (e) => {
            this.abrirModalNuevaReserva(e.currentTarget);
        });
        $('#listaItemsRequerimientoParaAtenderConAlmacen tbody').on("click", "button.handleClickAbrirModaHistorialReserva", (e) => {
            this.abrirModalHistorialReserva(e.currentTarget);
        });
        $('#modal-nueva-reserva').on("click", "button.handleClickAgregarReserva", (e) => {
            e.currentTarget.setAttribute("disabled", true);
            this.agregarReserva(e.currentTarget);
        });
        $('#modal-nueva-reserva').on("click", "button.handleClickAnularReserva", (e) => {
            this.anularReserva(e.currentTarget);
        });
        // $('#modal-nueva-reserva').on("change", "select.handleChangeObtenerStockAlmacen", () => {
        //     this.handleChangeObtenerStockAlmacen();
        // });

        $('#modal-filtro-requerimientos-pendientes').on('hidden.bs.modal', () => {
            this.updateValorFiltroRequerimientosPendientes();

            if (this.updateContadorFiltroRequerimientosPendientes() == 0) {
                this.renderRequerimientoPendienteList('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO','SIN_FILTRO');
            } else {
                this.renderRequerimientoPendienteList(this.ActualParametroEmpresa, this.ActualParametroSede, this.ActualParametroFechaDesde, this.ActualParametroFechaHasta, this.ActualParametroReserva, this.ActualParametroOrden,this.ActualParametroEstado);
            }


        });
        $('#modal-filtro-requerimientos-atendidos').on('hidden.bs.modal', () => {
            this.updateValorFiltroRequerimientosAtendidos();

            if (this.updateContadorFiltroRequerimientosAtendidos() == 0) {
                this.renderRequerimientoAtendidosList('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');
            } else {
                this.renderRequerimientoAtendidosList(this.ActualParametroEmpresa, this.ActualParametroSede, this.ActualParametroFechaDesde, this.ActualParametroFechaHasta, this.ActualParametroReserva, this.ActualParametroOrden);
            }


        });

        $('#modal-por-regularizar').on("click", "a.handleClickMapear", (e) => {

            $('#modal-mapeoItemsRequerimiento').modal({
                show: true
            });
            $('[name=id_requerimiento]').val(e.currentTarget.dataset.idRequerimiento);
            $('#cod_requerimiento').text(e.currentTarget.dataset.codigoRequerimiento);
            listarItemsRequerimientoMapeo(e.currentTarget.dataset.idRequerimiento);

            $('#submit_mapeoItemsRequerimiento').removeAttr('disabled');
        });

        // Handle click on checkbox
        $('#listaRequerimientosPendientes').on('click', 'input[type="checkbox"]', (e) => {

            let that = this;
            // var $row = $(this).closest('tr');
            var $row = e.currentTarget.closest('tr');

            // Get row data
            var data = $tablaListaRequerimientosPendientes.row($row).data();
            // Get row ID
            var rowId = data.id_requerimiento;
            var idEstadoRequerimiento = data.estado;
            var cantidadMapeados = data.count_mapeados;
            var cantidadTipoProducto = data.cantidad_tipo_producto;
            var cantidadTipoServicio = data.cantidad_tipo_servicio;
            // console.log(data);
            // Determine whether row ID is in the list of selected row IDs
            var index = $.inArray(rowId, reqTrueList);


            if (idEstadoRequerimiento == 38) {
                e.currentTarget.checked = false;
                Swal.fire(
                    '',
                    'No puede generar una orden si el requerimiento esta por regularizar',
                    'warning'
                );
            }
            if ((cantidadMapeados == 0 && cantidadTipoProducto > 0)) {
                e.currentTarget.checked = false;
                Swal.fire(
                    '',
                    'No puede generar una orden si tiene aun productos sin mapear',
                    'warning'
                );
            }

            // If checkbox is checked and row ID is not in list of selected row IDs
            if (e.currentTarget.checked && index === -1) {
                let idx = reqTrueList.indexOf(parseInt(rowId));
                if ((idx == -1)) {
                    reqTrueList.push(parseInt(rowId));
                }

                // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
            } else if (!e.currentTarget.checked && index !== -1) {
                reqTrueList.splice(index, 1);
            }

            if (e.currentTarget.checked) {
                // $row.addClass('selected');
                $row.classList.add('selected');
                document.getElementById('btnCrearOrdenCompra').removeAttribute('disabled');


            } else {
                $row.classList.remove('selected');
                document.getElementById('btnCrearOrdenCompra').setAttribute('disabled', true);

            }

            this.updateContadorRequerimientosPendientesSeleccionados();


            // this.stopPropagation();
        });


        $('#lista_compras').on("click", "button.handleClickLimpiarRequerimientosPendientesSeleccionadosConCheck", () => {
            this.limpiarRequerimientosPendientesSeleccionadosConCheck();
        });

    }


    tabRequerimientosPendientes() {
        this.renderRequerimientoPendienteList('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');
    }

    tabRequerimientosAtendidos() {
        this.renderRequerimientoAtendidosList('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');

    }

    // control de estado de check de filtro
    estadoCheckFiltroRequerimientosPendientes(e) {
        switch (e.currentTarget.getAttribute('name')) {
            case 'chkEmpresa':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='empresa']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='empresa']").setAttribute("readOnly", true)
                }
                break;
            case 'chkSede':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='sede']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='sede']").setAttribute("readOnly", true)
                }
                break;
            case 'chkFechaRegistro':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] input[name='fechaRegistroDesde']").removeAttribute("readOnly")
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] input[name='fechaRegistroHasta']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] input[name='fechaRegistroDesde']").setAttribute("readOnly", true)
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] input[name='fechaRegistroHasta']").setAttribute("readOnly", true)
                }
                break;
            case 'chkReservaAlmacen':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='reserva']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='reserva']").setAttribute("readOnly", true)
                }
                break;
            case 'chkOrden':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='orden']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='orden']").setAttribute("readOnly", true)
                }
                break;
            case 'chkEstado':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='estado']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='estado']").setAttribute("readOnly", true)
                }
                break;
            default:
                break;
        }
    }
    estadoCheckFiltroRequerimientosAtendidos(e) {
        switch (e.currentTarget.getAttribute('name')) {
            case 'chkEmpresa':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] select[name='empresa']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] select[name='empresa']").setAttribute("readOnly", true)
                }
                break;
            case 'chkSede':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] select[name='sede']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] select[name='sede']").setAttribute("readOnly", true)
                }
                break;
            case 'chkFechaRegistro':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] input[name='fechaRegistroDesde']").removeAttribute("readOnly")
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] input[name='fechaRegistroHasta']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] input[name='fechaRegistroDesde']").setAttribute("readOnly", true)
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] input[name='fechaRegistroHasta']").setAttribute("readOnly", true)
                }
                break;
            case 'chkReservaAlmacen':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] select[name='reserva']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] select[name='reserva']").setAttribute("readOnly", true)
                }
                break;
            case 'chkOrden':
                if (e.currentTarget.checked == true) {
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] select[name='orden']").removeAttribute("readOnly")
                } else {
                    document.querySelector("div[id='modal-filtro-requerimientos-atendidos'] select[name='orden']").setAttribute("readOnly", true)
                }
                break;
            default:
                break;
        }
    }

    updateContadorFiltroRequerimientosPendientes() {

        let contadorCheckActivo = 0;
        const allCheckBoxFiltroRequerimientosPendientes = document.querySelectorAll("div[id='modal-filtro-requerimientos-pendientes'] input[type='checkbox']");
        allCheckBoxFiltroRequerimientosPendientes.forEach(element => {
            if (element.checked == true) {
                contadorCheckActivo++;
            }
        });
        document.querySelector("button[id='btnFiltrosRequerimientosPendientes'] span").innerHTML = '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : ' + contadorCheckActivo
        return contadorCheckActivo;

    }
    updateContadorFiltroRequerimientosAtendidos() {

        let contadorCheckActivo = 0;
        const allCheckBoxFiltroRequerimientosAtendidos = document.querySelectorAll("div[id='modal-filtro-requerimientos-atendidos'] input[type='checkbox']");
        allCheckBoxFiltroRequerimientosAtendidos.forEach(element => {
            if (element.checked == true) {
                contadorCheckActivo++;
            }
        });
        document.querySelector("button[id='btnFiltrosRequerimientosAtendidos'] span").innerHTML = '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : ' + contadorCheckActivo
        return contadorCheckActivo;

    }

    updateValorFiltroRequerimientosPendientes() {


        const modalRequerimientosPendientes = document.querySelector("div[id='modal-filtro-requerimientos-pendientes']");

        if (modalRequerimientosPendientes.querySelector("select[name='empresa']").getAttribute("readonly") == null) {
            this.ActualParametroEmpresa = modalRequerimientosPendientes.querySelector("select[name='empresa']").value;
        }else{
            this.ActualParametroEmpresa= 'SIN_FILTRO';
        }
        if (modalRequerimientosPendientes.querySelector("select[name='sede']").getAttribute("readonly") == null) {
            this.ActualParametroSede = modalRequerimientosPendientes.querySelector("select[name='sede']").value;
        }else{
            this.ActualParametroSede= 'SIN_FILTRO';
        }
        if (modalRequerimientosPendientes.querySelector("input[name='fechaRegistroDesde']").getAttribute("readonly") == null) {
            this.ActualParametroFechaDesde = modalRequerimientosPendientes.querySelector("input[name='fechaRegistroDesde']").value.length > 0 ? modalRequerimientosPendientes.querySelector("input[name='fechaRegistroDesde']").value : 'SIN_FILTRO';
        }else{
            this.ActualParametroFechaDesde= 'SIN_FILTRO';

        }
        if (modalRequerimientosPendientes.querySelector("input[name='fechaRegistroHasta']").getAttribute("readonly") == null) {
            this.ActualParametroFechaHasta = modalRequerimientosPendientes.querySelector("input[name='fechaRegistroHasta']").value.length > 0 ? modalRequerimientosPendientes.querySelector("input[name='fechaRegistroHasta']").value : 'SIN_FILTRO';
        }else{
            this.ActualParametroFechaHasta= 'SIN_FILTRO';

        }
        if (modalRequerimientosPendientes.querySelector("select[name='reserva']").getAttribute("readonly") == null) {
            this.ActualParametroReserva = modalRequerimientosPendientes.querySelector("select[name='reserva']").value;
        }else{
            this.ActualParametroReserva= 'SIN_FILTRO';

        }
        if (modalRequerimientosPendientes.querySelector("select[name='orden']").getAttribute("readonly") == null) {
            this.ActualParametroOrden = modalRequerimientosPendientes.querySelector("select[name='orden']").value;
        }else{
            this.ActualParametroOrden= 'SIN_FILTRO';

        }
        if (modalRequerimientosPendientes.querySelector("select[name='estado']").getAttribute("readonly") == null) {
            this.ActualParametroEstado = modalRequerimientosPendientes.querySelector("select[name='estado']").value;
        }else{
            this.ActualParametroEstado= 'SIN_FILTRO';

        }

    }
    updateValorFiltroRequerimientosAtendidos() {


        const modalRequerimientosAtendidos = document.querySelector("div[id='modal-filtro-requerimientos-atendidos']");

        if (modalRequerimientosAtendidos.querySelector("select[name='empresa']").getAttribute("readonly") == null) {
            this.ActualParametroEmpresa = modalRequerimientosAtendidos.querySelector("select[name='empresa']").value;
        }
        if (modalRequerimientosAtendidos.querySelector("select[name='sede']").getAttribute("readonly") == null) {
            this.ActualParametroSede = modalRequerimientosAtendidos.querySelector("select[name='sede']").value;
        }
        if (modalRequerimientosAtendidos.querySelector("input[name='fechaRegistroDesde']").getAttribute("readonly") == null) {
            this.ActualParametroFechaDesde = modalRequerimientosAtendidos.querySelector("input[name='fechaRegistroDesde']").value.length > 0 ? modalRequerimientosAtendidos.querySelector("input[name='fechaRegistroDesde']").value : 'SIN_FILTRO';
        }
        if (modalRequerimientosAtendidos.querySelector("input[name='fechaRegistroHasta']").getAttribute("readonly") == null) {
            this.ActualParametroFechaHasta = modalRequerimientosAtendidos.querySelector("input[name='fechaRegistroHasta']").value.length > 0 ? modalRequerimientosAtendidos.querySelector("input[name='fechaRegistroHasta']").value : 'SIN_FILTRO';
        }
        if (modalRequerimientosAtendidos.querySelector("select[name='reserva']").getAttribute("readonly") == null) {
            this.ActualParametroReserva = modalRequerimientosAtendidos.querySelector("select[name='reserva']").value;
        }
        if (modalRequerimientosAtendidos.querySelector("select[name='orden']").getAttribute("readonly") == null) {
            this.ActualParametroOrden = modalRequerimientosAtendidos.querySelector("select[name='orden']").value;
        }

    }


    renderRequerimientoPendienteList(idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', fechaRegistroDesde = 'SIN_FILTRO', fechaRegistroHasta = 'SIN_FILTRO', reserva = 'SIN_FILTRO', orden = 'SIN_FILTRO', estado= 'SIN_FILTRO') {
        let that = this;
        const button_nueva_orden = (array_accesos.find(element => element === 228) ? {
            text: '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nueva orden',
            attr: {
                disabled: true,
                id: 'btnCrearOrdenCompra'
            },
            action: () => {
                this.crearOrdenCompra();

            },
            className: 'btn-warning btn-sm'
        } : []),
            button_filtro = (array_accesos.find(element => element === 229) ? {
                text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : 0',
                attr: {
                    id: 'btnFiltrosRequerimientosPendientes'
                },
                action: () => {
                    this.abrirModalFiltrosRequerimientosPendientes();

                },
                className: 'btn-default btn-sm'
            } : []),
            button_descargar_excel = (array_accesos.find(element => element === 229) ? {
                text: '<span class="far fa-file-excel" aria-hidden="true"></span> Descargar',
                attr: {
                    id: 'btnDescargarExcelRequerimientosPendientes'
                },
                action: () => {
                    this.exportarListaRequerimientosPendientesExcel();

                },
                className: 'btn-default btn-sm'
            } : []);
        $tablaListaRequerimientosPendientes = $('#listaRequerimientosPendientes').DataTable({
            'dom': 'Blfrtip',
            'buttons': [button_nueva_orden, button_filtro, button_descargar_excel],
            'language': vardataTables[0],
            'order': [[4, 'desc'],[1, 'desc']],
            'serverSide': true,
            'destroy': true,
            'stateSave': true,
            'bLengthChange': false,
            "pageLength": 20,
            'ajax': {
                'url': 'requerimientos-pendientes',
                'type': 'POST',
                'data': { 'idEmpresa': idEmpresa, 'idSede': idSede, 'fechaRegistroDesde': fechaRegistroDesde, 'fechaRegistroHasta': fechaRegistroHasta, 'reserva': reserva, 'orden': orden, 'estado':estado },
                beforeSend: data => {

                    $("#listaRequerimientosPendientes").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                }

            },
            'columns': [
                { 'data': 'id_requerimiento', 'name': 'alm_req.id_requerimiento', "searchable": false, 'render': function (data, type, row) {
                    return `<div class="text-center"><input type="checkbox" data-estado="${row.estado}" data-cantidad-tipo-producto="${row.cantidad_tipo_producto}" data-cantidad-tipo-servicios="${row.cantidad_tipo_servicio}" data-mapeos-pendientes="${row.count_pendientes}" data-mapeados="${row.count_mapeados}" data-id-requerimiento="${row.id_requerimiento}" /></div>`;
                }},
                { 'data': 'descripcion_prioridad', 'name': 'adm_prioridad.descripcion', 'render': function (data, type, row) {

                    return `${row['termometro']}`;
                }},
                { 'data': 'empresa_sede', 'name': 'sis_sede.descripcion', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'alm_req.codigo', 'className': 'text-center',  'render': function (data, type, row) {
                    return `${row.estado == 38 ? '<i class="fas fa-exclamation-triangle ' + (row.count_pendientes > 0 ? 'red' : 'orange') + ' handleClickAbrirModalPorRegularizar" style="cursor:pointer;" title="Por regularizar' + (row.count_pendientes > 0 ? '(Tiene ' + row.count_pendientes + ' item(s) pendientes por mapear)' : '') + '" data-id-requerimiento="' + row.id_requerimiento + '" ></i> &nbsp;' : ''}<a href="/necesidades/requerimiento/elaboracion/index?id=${row.id_requerimiento}" target="_blank" title="Abrir Requerimiento">${row.codigo}</a> ${row.tiene_transformacion == true ? '<i class="fas fa-random text-danger" title="Con transformación"></i>' : ''} `;
                }},
                { 'data': 'fecha_registro', 'name': 'alm_req.fecha_registro', 'className': 'text-center' },
                { 'data': 'fecha_entrega', 'name': 'alm_req.fecha_entrega', 'className': 'text-center',render:function(data, type, row){
                    // return (row.fecha_entrega!= '' && row.fecha_entrega != null)?(moment(row.fecha_entrega).format('DD-MM-YYYY')):'';
                    return row.fecha_entrega;
                }},
                { 'data': 'concepto', 'name': 'alm_req.concepto', 'className': 'text-left' },
                { 'data': 'tipo_req_desc', 'name': 'alm_tp_req.descripcion', 'className': 'text-center' },
                { 'data': 'division', 'name': 'division.descripcion', 'className': 'text-center', "searchable": false, 'render': function (data, type, row) {
                    return row.division != null ? JSON.parse(row.division.replace(/&quot;/g, '"')).join(",") : '';
                }},
                { 'data': 'cc_solicitado_por', 'name': 'cc_view.name', 'className': 'text-center','render': function (data, type, row) {
                    if (row.id_tipo_requerimiento != 1) {
                        return row.solicitado_por != null ? row.solicitado_por : '';

                    } else {
                        return row.cc_solicitado_por != null ? row.cc_solicitado_por : '';
                    }
                }},
                { 'data': 'nombre_usuario', 'name': 'nombre_usuario', 'className': 'text-center' },
                { 'data': 'observacion', 'name': 'alm_req.observacion', 'className': 'text-center' },
                { 'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc', 'className': 'text-center','render': function (data, type, row) {
                    return '<span class="label label-' + row.bootstrap_color + ' estadoRequerimiento" title="' + (row['estado_doc'] == 'En pausa' ? 'Retiro de aprobación por actualización de CDP' : '') + '">' + row['estado_doc'] + '</span>';
                }},
                { 'data': 'id_requerimiento', 'name': 'alm_req.id_requerimiento', 'className': 'text-center', "searchable": false,'render': function (data, type, row) {
                    // if(permisoCrearOrdenPorRequerimiento == '1') {
                    let observacionLogisticaSinSustento = '';
                    // let idObservacionLogistica=0;
                    //         (row.historial_aprobacion).forEach(element => {
                    //             if(element.id_vobo ==3 && element.id_rol ==4 && element.tiene_sustento ==false){
                    //                 observacionLogisticaSinSustento=element.detalle_observacion??'';
                    //                 idObservacionLogistica=element.id_aprobacion??0;
                    //             }
                    //         });

                    let tieneTransformacion = row.tiene_transformacion;
                    let cantidadItemBase = row.cantidad_items_base;
                    let btnRetornarAListaPendientes = '<button type="button" class="btn btn-default btn-xs handleClickRetornarAListaPendientes" style="color:red;" name="btnRetornarAListaPendientes" title="Retornar a lista de pendiente" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '"><i class="fas fa-arrow-left fa-xs"></i></button>';
                    if (tieneTransformacion == true && cantidadItemBase == 0) {
                        return ('<div class="btn-group" role="group">' +
                            '</div>' +
                            '<div class="btn-group" role="group">' + (array_accesos.find(element => element === 226) ? '<button type="button" class="btn btn-info btn-xs handleClickOpenModalCuadroCostos" name="btnVercuadroCostos" title="Ver Cuadro Costos" data-id-requerimiento="' + row.id_requerimiento + '" >' +
                                '<i class="fas fa-eye fa-sm"></i>' +
                                '</button>' : '') +

                            (([17, 27, 1, 3, 77].includes(auth_user.id_usuario)) ? (btnRetornarAListaPendientes) : '') +

                            '</div>');
                    } else {
                        let openDiv = '<div class="btn-group" role="group">';
                        let btnVerDetalleRequerimiento = (array_accesos.find(element => element === 220) ? '<button type="button" class="btn btn-default btn-xs handleClickVerDetalleRequerimiento" name="btnVerDetalleRequerimiento" title="Ver detalle requerimiento" data-id-requerimiento="' + row.id_requerimiento + '" ><i class="fas fa-chevron-down fa-sm"></i></button>' : '');
                        // let btnObservarRequerimientoLogistica= '<button type="button" class="btn btn-default btn-xs handleClickObservarRequerimientoLogistica" name="btnObservarRequerimientoLogistica" title="Observar requerimiento" data-id-requerimiento="' + row.id_requerimiento + '" style="background: gold;" ><i class="fas fa-exclamation-triangle fa-sm"></i></button>';

                        // let btnAgregarItemBase = '<button type="button" class="btn btn-success btn-xs" name="btnAgregarItemBase" title="Mapear productos" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.openModalAgregarItemBase(this);"  ><i class="fas fa-sign-out-alt"></i></button>';
                        let btnMapearProductos = (array_accesos.find(element => element === 222) ? '<button type="button" class="mapeo btn btn-success btn-xs" title="Mapear productos" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo="' + row.codigo + '"  ><i class="fas fa-sign-out-alt"></i> <span class="badge" title="Cantidad items sin mapear" name="cantidadAdjuntosRequerimiento" style="position:absolute;border: solid 0.1px;z-index: 9;top: -9px;left: 0px;font-size: 0.9rem;">' + row.count_pendientes + '</span></button>' : '');
                        let btnVerAdjuntosModal = (array_accesos.find(element => element === 223) ? '<button type="button" class="btn btn-xs btn-default  handleClickVerAgregarAdjuntosRequerimiento" name="btnVerAgregarAdjuntosRequerimiento" data-id-requerimiento="' + row['id_requerimiento'] + '" data-codigo-requerimiento="' + row.codigo + '" title="Ver archivos adjuntos"><i class="fas fa-paperclip fa-xs"></i></button>' : '');
                        let btnAtenderAlmacen = '';
                        let btnCrearOrdenCompra = '';
                        let btnGestionarEstadoRequerimiento = '';
                        let btnObservarRequerimientoLogistico = '<button type="button" class="btn btn-warning btn-xs handleClickObservarRequerimientoLogistico" name="btnObservarRequerimientoLogistico" title="Observar requerimiento" data-codigo-requerimiento="' + row.codigo + '" data-id-requerimiento="' + row.id_requerimiento + '"  data-observacion-logistica-sin-sustento="' + observacionLogisticaSinSustento + '"  ><i class="fas fa-exclamation-circle"></i></button>';
                        let btnCrearOrdenServicio = (array_accesos.find(element => element === 224) ? '<button type="button" class="btn btn-warning btn-xs handleClickCrearOrdenServicioPorRequerimiento" name="btnCrearOrdenServicioPorRequerimiento" title="Crear Orden de Servicio" data-id-requerimiento="' + row.id_requerimiento + '"  >OS</button>' : '');
                        let btnExportarExcel = (array_accesos.find(element => element === 221) ? '<button type="button" class="btn btn-default btn-xs handleClickSolicitudCotizacionExcel" name="btnSolicitudCotizacionExcel" title="Solicitud cotización excel" data-id-requerimiento="' + row.id_requerimiento + '" style="color:green;" ><i class="far fa-file-excel"></i></button>' : '');
                        // if (row.cantidad_adjuntos_activos.cabecera > 0 || row.cantidad_adjuntos_activos.detalle > 0) {
                        // btnVerAdjuntosModal = '<button type="button" class="btn btn-xs btn-default  handleClickVerAgregarAdjuntosRequerimiento" name="btnVerAgregarAdjuntosRequerimiento" data-id-requerimiento="' + row['id_requerimiento'] + '" data-codigo-requerimiento="' + row.codigo + '" title="Ver archivos adjuntos"><i class="fas fa-paperclip fa-xs"></i></button>';


                        // }
                        if (row.count_mapeados > 0) {
                            if (row.estado == 38 || row.estado == 39) { // estado por regularizar | estado  en pausa

                                btnAtenderAlmacen = (array_accesos.find(element => element === 225) ? '<button type="button" class="btn btn-primary btn-xs" name="btnOpenModalAtenderConAlmacen" title="Reserva en almacén" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '" data-almacen-requerimiento="' + row.almacen_requerimiento + '" disabled><i class="fas fa-dolly fa-sm"></i></button>' : '');
                                btnCrearOrdenCompra = (array_accesos.find(element => element === 283) ? '<button type="button" class="btn btn-warning btn-xs" name="btnCrearOrdenCompraPorRequerimiento" title="Crear Orden de Compra" data-id-requerimiento="' + row.id_requerimiento + '"  disabled>OC</button>' : '');
                                btnCrearOrdenServicio = (array_accesos.find(element => element === 224) ? '<button type="button" class="btn btn-danger btn-xs" name="btnCrearOrdenServicioPorRequerimiento" title="Crear Orden de Servicio" data-id-requerimiento="' + row.id_requerimiento + '" disabled >OS</button>' : '');

                            } else {
                                // if (row.id_tipo_requerimiento == 4) { //tipo de compras para stock
                                // btnAtenderAlmacen = '<button type="button" class="btn btn-primary btn-xs" name="btnOpenModalAtenderConAlmacen" title="El requerimiento es de tipo compras para stock" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '" data-almacen-requerimiento="' + row.almacen_requerimiento + '" disabled><i class="fas fa-dolly fa-sm"></i></button>';

                                // } else {
                                btnAtenderAlmacen = (array_accesos.find(element => element === 225) ? '<button type="button" class="btn btn-primary btn-xs handleClickAtenderConAlmacen" name="btnOpenModalAtenderConAlmacen" title="Reserva en almacén" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '" data-almacen-requerimiento="' + row.almacen_requerimiento + '"><i class="fas fa-dolly fa-sm"></i></button>' : '');
                                // }
                                btnCrearOrdenCompra = (array_accesos.find(element => element === 283) ? '<button type="button" class="btn btn-warning btn-xs handleClickCrearOrdenCompraPorRequerimiento" name="btnCrearOrdenCompraPorRequerimiento" title="Crear Orden de Compra" data-id-requerimiento="' + row.id_requerimiento + '"  >OC</button>' : '');
                                if (row.id_tipo_requerimiento != 1)// diferentes a; tipo Atención inmediata (MGCP)
                                    btnGestionarEstadoRequerimiento = (array_accesos.find(element => element === 227) ? '<button type="button" class="btn btn-danger btn-xs handleClickGestionarEstadoRequerimiento" name="btnCrearGestionarEstadoRequerimiento" title="Ajuste de necesidad de requerimiento" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '" data-estado-requerimiento="' + row.estado_doc + '"> <i class="fas fa-crop-alt"></i></button>' : '');

                            }
                        }


                        let btnVercuadroCostos = '';
                        if (row.id_tipo_requerimiento == 1) {
                            btnVercuadroCostos = array_accesos.find(element => element === 226) ? '<button type="button" class="btn btn-default btn-xs handleClickOpenModalCuadroCostos" name="btnVercuadroCostos" title="Ver Cuadro Costos" data-id-requerimiento="' + row.id_requerimiento + '" ><i class="fas fa-eye fa-sm"></i></button>' : '';
                        }


                        let closeDiv = '</div>';
                        let botones = '';

                        if (row.estado == 1 || row.estado == 3 || row.estado == 4 || row.estado == 12) {
                            botones = openDiv + btnVerDetalleRequerimiento + (row.id_tipo_requerimiento != 1 ? btnObservarRequerimientoLogistico : '') + btnExportarExcel + closeDiv;
                        } else {
                            botones = openDiv + btnVerDetalleRequerimiento + (row.id_tipo_requerimiento != 1 ? btnObservarRequerimientoLogistico : '') + btnExportarExcel + btnAtenderAlmacen + btnMapearProductos +
                                btnCrearOrdenCompra + btnVercuadroCostos + btnVerAdjuntosModal + btnGestionarEstadoRequerimiento;

                            if (row.cantidad_tipo_servicio > 0) {
                                botones += btnCrearOrdenServicio + closeDiv;
                            } else {
                                botones += closeDiv;
                            }
                        }
                        return botones;
                    }

                }}
            ],
            'columnDefs': [
            ],
            'rowCallback': function (row, data, dataIndex) {
                // Get row ID
                var rowId = data.id_requerimiento;
                // If row ID is in the list of selected row IDs
                if ($.inArray(rowId, reqTrueList) !== -1) {
                    $(row).find('input[type="checkbox"]').prop('checked', true);
                    $(row).addClass('selected');
                }

            },
            'initComplete': function () {

                //Boton de busqueda
                const $filter = $('#listaRequerimientosPendientes_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscarRequerimientosPendientes" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscarRequerimientosPendientes').trigger('click');
                    }
                });
                $('#btnBuscarRequerimientosPendientes').on('click', (e) => {
                    $tablaListaRequerimientosPendientes.search($input.val()).draw();
                })
                //Fin boton de busqueda



                that.updateContadorFiltroRequerimientosPendientes();


                // var trs = this.$('tr');
                // for (let i = 0; i < trs.length; i++) {
                //     trs[i].childNodes[0].childNodes[0].childNodes[0].addEventListener('click', handleTrClick);
                // }
                // function handleTrClick() {
                //     if (this.classList.contains('eventClick')) {
                //         this.classList.remove('eventClick');
                //     } else {
                //         const rows = Array.from(document.querySelectorAll('tr.eventClick'));
                //         rows.forEach(row => {
                //             row.classList.remove('eventClick');
                //         });
                //         this.classList.add('eventClick');
                //     }
                //     if(this.dataset.estado ==38){
                //         this.checked = false;
                //         Swal.fire(
                //             '',
                //             'No puede generar una orden si el requerimiento esta por regularizar',
                //             'warning'
                //         );
                //     }
                //     if (this.dataset.mapeados == 0) {
                //         this.checked = false;
                //         Swal.fire(
                //             '',
                //             'No puede generar una orden si tiene aun productos sin mapear',
                //             'warning'
                //         );
                //     } else {
                //         let id = this.dataset.idRequerimiento
                //         let stateCheck = this.checked
                //         that.requerimientoPendienteCtrl.controlListCheckReq(id, stateCheck);

                //     }
                // }
            },
            "drawCallback": function (settings) {
                //Botón de búsqueda
                $('#listaRequerimientosPendientes_filter input').prop('disabled', false);
                $('#btnBuscarRequerimientosPendientes').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaRequerimientosPendientes_filter input').trigger('focus');
                //fin botón búsqueda
                if ($tablaListaRequerimientosPendientes.rows().data().length == 0) {
                    Lobibox.notify('info', {
                        title: false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: `No se encontro data disponible para mostrar`
                    });
                }
                //Botón de búsqueda
                $('#listaRequerimientosPendientes_filter input').prop('disabled', false);
                $('#btnBuscarRequerimientosPendientes').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaRequerimientosPendientes_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaRequerimientosPendientes").LoadingOverlay("hide", true);

                const $filter = document.querySelector("div[id='listaRequerimientosPendientes_wrapper'] div[class~='btn-group']");

                if (!document.querySelector("button[id='btnLimpiarRequerimientosPendientesSeleccionados']")) {
                    $filter.insertAdjacentHTML('afterbegin', `<button class="btn btn-sm btn-default handleClickLimpiarRequerimientosPendientesSeleccionadosConCheck" type="button" id="btnLimpiarRequerimientosPendientesSeleccionados" disabled>
                    Limpiar Seleccionados <span class="badge" id='contadorRequerimientosPendientesSeleccionados'>${reqTrueList.length}</span>
                    </button>`);

                }
                that.updateContadorRequerimientosPendientesSeleccionados();

            },
            "createdRow": function (row, data, dataIndex) {

                let color = '#ffffff';
                switch (data.bootstrap_color) {
                    case 'default':
                        color = '#777777';
                        break;
                    case 'primary':
                        color = '#3c8dbc';
                        break;
                    case 'success':
                        color = '#5cb85c';
                        break;
                    case 'secundary':
                        color = '#ffffff';
                        break;
                    case 'warning':
                        color = '#f39c12';
                        break;
                    case 'info':
                        color = '#72bcd4';
                        break;
                    case 'danger':
                        color = '#d9534f';
                        break;

                    default:
                        color = '#ffffff';
                        break;
                }
                $(row.childNodes[12]).css('background-color', color);
            }

        });
    }

    updateContadorRequerimientosPendientesSeleccionados() {
        const contador = document.querySelector("span[id='contadorRequerimientosPendientesSeleccionados']");
        contador.textContent = reqTrueList.length;

        if (reqTrueList.length > 0) {

            document.querySelector("button[id='btnLimpiarRequerimientosPendientesSeleccionados']").removeAttribute("disabled");
            document.querySelector("button[id='btnLimpiarRequerimientosPendientesSeleccionados']").classList.replace('btn-default', 'btn-info');
            //asegurar que este el check marcado en la pagina actual
            let trs = document.querySelector("table[id='listaRequerimientosPendientes'] tbody").children;

            for (let index = 1; index < trs.length; index++) {
                // console.log(reqTrueList.includes(parseInt(trs[index].querySelector("input[type='checkbox']"))));
                if (trs[index].querySelector("input[type='checkbox']") ? reqTrueList.includes(parseInt(trs[index].querySelector("input[type='checkbox']").dataset.idRequerimiento)) : false) {
                    trs[index].querySelector("input[type='checkbox']").checked = true;
                }
            }
        } else {
            document.querySelector("button[id='btnLimpiarRequerimientosPendientesSeleccionados']").setAttribute("disabled", true);
            document.querySelector("button[id='btnLimpiarRequerimientosPendientesSeleccionados']").classList.replace('btn-info', 'btn-default');


        }
    }

    limpiarRequerimientosPendientesSeleccionadosConCheck() {

        Swal.fire({
            title: 'Esta seguro que desea limpiar todas las selecciones?',
            text: "Se quitara el check de seleccion a todo los requerimientos",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, limpiar'

        }).then((result) => {
            if (result.isConfirmed) {
                reqTrueList = [];
                sessionStorage.removeItem('idOrden');
                sessionStorage.removeItem('reqCheckedList');
                sessionStorage.removeItem('tipoOrden');
                $tablaListaRequerimientosPendientes.ajax.reload(null, false);

            }
        });
    }


    renderRequerimientoAtendidosList(idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', fechaRegistroDesde = 'SIN_FILTRO', fechaRegistroHasta = 'SIN_FILTRO', reserva = 'SIN_FILTRO', orden = 'SIN_FILTRO') {
        let that = this;
        const button_filtros = (array_accesos.find(element => element === 230) ? {
            text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : 0',
            attr: {
                id: 'btnFiltrosRequerimientosAtendidos'
            },
            action: () => {
                this.abrirModalFiltrosRequerimientosAtendidos();

            },
            className: 'btn-default btn-sm'
        } : []),
            button_descarga_Excel = (array_accesos.find(element => element === 231) ? {
                text: '<psan class="far fa-file-excel" aria-hidden="true"></span> Descargar',
                attr: {
                    id: 'btnExportarTablaRequerimientosAtendidosExcel'
                },
                action: () => {
                    this.exportTablaRequerimientosAtentidosExcel();

                },
                className: 'btn-default btn-sm'
            } : []);
        $tablaListaRequerimientosAtendidos = $('#listaRequerimientosAtendidos').DataTable({
            'dom': vardataTables[1],
            'buttons': [button_filtros, button_descarga_Excel],
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'serverSide': true,
            'destroy': true,
            'bLengthChange': false,
            "pageLength": 20,
            'ajax': {
                'url': 'requerimientos-atendidos',
                'type': 'POST',
                'data': { 'idEmpresa': idEmpresa, 'idSede': idSede, 'fechaRegistroDesde': fechaRegistroDesde, 'fechaRegistroHasta': fechaRegistroHasta, 'reserva': reserva, 'orden': orden },
                beforeSend: data => {

                    $("#listaRequerimientosAtendidos").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                }

            },
            'columns': [
                { 'data': 'empresa_sede', 'name': 'sis_sede.descripcion', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'alm_req.codigo', 'className': 'text-center' },
                { 'data': 'fecha_registro', 'name': 'alm_req.fecha_registro', 'className': 'text-center' },
                { 'data': 'fecha_entrega', 'name': 'alm_req.fecha_entrega', 'className': 'text-center' },
                { 'data': 'concepto', 'name': 'alm_req.concepto', 'className': 'text-left' },
                { 'data': 'tipo_req_desc', 'name': 'alm_tp_req.descripcion', 'className': 'text-center' },
                { 'data': 'division', 'name': 'division.descripcion', 'className': 'text-center', "searchable": false },
                { 'data': 'cc_solicitado_por', 'name': 'cc_view.name', 'className': 'text-center' },
                { 'data': 'nombre_usuario', 'name': 'nombre_usuario', 'className': 'text-center' },
                { 'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc', 'className': 'text-center' },
                { 'data': 'id_requerimiento', 'name': 'alm_req.id_requerimiento', 'className': 'text-center', "searchable": false }



            ],
            'columnDefs': [

                {
                    'render': function (data, type, row) {
                        return `${row.estado == 38 ? '<i class="fas fa-exclamation-triangle ' + (row.count_pendientes > 0 ? 'red' : 'orange') + ' handleClickAbrirModalPorRegularizar" style="cursor:pointer;" title="Por regularizar' + (row.count_pendientes > 0 ? '(Tiene ' + row.count_pendientes + ' item(s) pendientes por mapear)' : '') + '" data-id-requerimiento="' + row.id_requerimiento + '" ></i> &nbsp;' : ''}<a href="/necesidades/requerimiento/elaboracion/index?id=${row.id_requerimiento}" target="_blank" title="Abrir Requerimiento">${row.codigo}</a> ${row.tiene_transformacion == true ? '<i class="fas fa-random text-danger" title="Con transformación"></i>' : ''} `;
                    }, targets: 1
                },
                {
                    'render': function (data, type, row) {
                        return row.division != null ? JSON.parse(row.division.replace(/&quot;/g, '"')).join(",") : '';
                    }, targets: 6
                },
                {
                    'render': function (data, type, row) {
                        if (row.id_tipo_requerimiento != 1) {
                            return row.solicitado_por != null ? row.solicitado_por : '';

                        } else {
                            return row.cc_solicitado_por != null ? row.cc_solicitado_por : '';
                        }
                    }, targets: 7
                },
                {
                    'render': function (data, type, row) {
                        return '<span class="label label-' + row.bootstrap_color + ' estadoRequerimiento" title="' + (row['estado_doc'] == 'En pausa' ? 'Retiro de aprobación por actualización de CDP' : '') + '">' + row['estado_doc'] + '</span>';
                    }, targets: 9
                },
                {
                    'render': function (data, type, row) {
                        // if(permisoCrearOrdenPorRequerimiento == '1') {
                        let tieneTransformacion = row.tiene_transformacion;
                        let cantidadItemBase = row.cantidad_items_base;
                        let btnRetornarAListaPendientes = '<button type="button" class="btn btn-default btn-xs handleClickRetornarAListaPendientes" style="color:red;" name="btnRetornarAListaPendientes" title="Retornar a lista de pendiente" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '"><i class="fas fa-arrow-left fa-xs"></i></button>';

                        if (tieneTransformacion == true && cantidadItemBase == 0) {
                            return ('<div class="btn-group" role="group">' +
                                '</div>' +
                                '<div class="btn-group" role="group">' + (array_accesos.find(element => element === 232) ? '<button type="button" class="btn btn-default btn-xs handleClickVerDetalleRequerimiento" name="btnVerDetalleRequerimiento" title="Ver detalle requerimiento" data-id-requerimiento="' + row.id_requerimiento + '" ><i class="fas fa-chevron-down fa-sm"></i></button>' : '')
                                + (array_accesos.find(element => element === 235) ? '<button type="button" class="btn btn-info btn-xs" name="btnVercuadroCostos" title="Ver Cuadro Costos" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.openModalCuadroCostos(this);">' +
                                    '<i class="fas fa-eye fa-sm"></i>' +
                                    '</button>' : '') +
                                (array_accesos.find(element => element === 232) ? '<button type="button" class="btn btn-default btn-xs handleClickVerDetalleRequerimiento" name="btnVerDetalleRequerimiento" title="Ver detalle requerimiento" data-id-requerimiento="' + row.id_requerimiento + '" ><i class="fas fa-chevron-down fa-sm"></i></button>' : '')
                                + (([17, 27, 1, 3, 77].includes(auth_user.id_usuario)) ? (btnRetornarAListaPendientes) : '')
                                + '</div>');
                        } else {
                            let openDiv = '<div class="btn-group" role="group">';
                            let btnVerDetalleRequerimiento = (array_accesos.find(element => element === 232) ? '<button type="button" class="btn btn-default btn-xs handleClickVerDetalleRequerimiento" name="btnVerDetalleRequerimiento" title="Ver detalle requerimiento" data-id-requerimiento="' + row.id_requerimiento + '" ><i class="fas fa-chevron-down fa-sm"></i></button>' : '');
                            // let btnObservarRequerimientoLogistica= '<button type="button" class="btn btn-default btn-xs handleClickObservarRequerimientoLogistica" name="btnObservarRequerimientoLogistica" title="Observar requerimiento" data-id-requerimiento="' + row.id_requerimiento + '" style="background: gold;" ><i class="fas fa-exclamation-triangle fa-sm"></i></button>';

                            // let btnAgregarItemBase = '<button type="button" class="btn btn-success btn-xs" name="btnAgregarItemBase" title="Mapear productos" data-id-requerimiento="' + row.id_requerimiento + '"  onclick="requerimientoPendienteView.openModalAgregarItemBase(this);"  ><i class="fas fa-sign-out-alt"></i></button>';
                            let btnVerAdjuntosModal = (array_accesos.find(element => element === 234) ? '<button type="button" class="btn btn-xs btn-default  handleClickVerAgregarAdjuntosRequerimiento" name="btnVerAgregarAdjuntosRequerimiento" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '" title="Ver archivos adjuntos"><i class="fas fa-paperclip fa-xs"></i></button>' : '');
                            let btnAtenderAlmacen = '';
                            let btnRetornarAListaPendientes = '<button type="button" class="btn btn-default btn-xs handleClickRetornarAListaPendientes" style="color:red;" name="btnRetornarAListaPendientes" title="Retornar a lista de pendiente" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '"><i class="fas fa-arrow-left fa-xs"></i></button>';
                            // if (row.cantidad_adjuntos_activos.cabecera > 0 || row.cantidad_adjuntos_activos.detalle > 0) {
                            //     btnVerAdjuntosModal = '<button type="button" class="btn btn-xs btn-default  handleClickVerAgregarAdjuntosRequerimiento" name="btnVerAgregarAdjuntosRequerimiento" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '" title="Ver archivos adjuntos"><i class="fas fa-paperclip fa-xs"></i></button>';

                            // }
                            if (row.count_mapeados > 0) {
                                if (row.estado == 38 || row.estado == 39) { // estado por regularizar | estado  en pausa

                                    btnAtenderAlmacen = (array_accesos.find(element => element === 233) ? '<button type="button" class="btn btn-primary btn-xs" name="btnOpenModalAtenderConAlmacen" title="Reserva en almacén" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '" data-almacen-requerimiento="' + row.almacen_requerimiento + '" disabled><i class="fas fa-dolly fa-sm"></i></button>' : '');

                                } else {
                                    // if (row.id_tipo_requerimiento == 4) { //tipo de compras para stock
                                    // btnAtenderAlmacen = '<button type="button" class="btn btn-primary btn-xs" name="btnOpenModalAtenderConAlmacen" title="El requerimiento es de tipo compras para stock"  disabled><i class="fas fa-dolly fa-sm"></i></button>';

                                    // } else {
                                    btnAtenderAlmacen = (array_accesos.find(element => element === 233) ? '<button type="button" class="btn btn-primary btn-xs handleClickAtenderConAlmacen" name="btnOpenModalAtenderConAlmacen" title="Reserva en almacén" data-id-requerimiento="' + row.id_requerimiento + '" data-codigo-requerimiento="' + row.codigo + '" data-almacen-requerimiento="' + row.almacen_requerimiento + '"><i class="fas fa-dolly fa-sm"></i></button>' : '');
                                    // }

                                }
                            }


                            let btnVercuadroCostos = '';
                            if (row.id_tipo_requerimiento == 1) {
                                btnVercuadroCostos = (array_accesos.find(element => element === 235) ? '<button type="button" class="btn btn-default btn-xs handleClickOpenModalCuadroCostos" name="btnVercuadroCostos" title="Ver Cuadro Costos" data-id-requerimiento="' + row.id_requerimiento + '" ><i class="fas fa-eye fa-sm"></i></button>' : '');
                            }


                            let closeDiv = '</div>';

                            if (row.cantidad_tipo_servicio > 0) {
                                return (openDiv + btnVerDetalleRequerimiento + btnAtenderAlmacen + btnVercuadroCostos + btnVerAdjuntosModal + (([17, 27, 1, 3, 77].includes(auth_user.id_usuario)) ? (btnRetornarAListaPendientes) : '') + closeDiv);
                            } else {
                                return (openDiv + btnVerDetalleRequerimiento + btnAtenderAlmacen + btnVercuadroCostos + btnVerAdjuntosModal + (([17, 27, 1, 3, 77].includes(auth_user.id_usuario)) ? (btnRetornarAListaPendientes) : '') + closeDiv);
                            }
                        }

                    }, targets: 10
                }

            ],
            'rowCallback': function (row, data, dataIndex) {
                // Get row ID
                var rowId = data.id_requerimiento;
                // If row ID is in the list of selected row IDs
                if ($.inArray(rowId, reqTrueList) !== -1) {
                    $(row).find('input[type="checkbox"]').prop('checked', true);
                    $(row).addClass('selected');
                }
            },
            'initComplete': function () {

                //Boton de busqueda
                const $filter = $('#listaRequerimientosAtendidos_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscarRequerimientosAtendidos" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscarRequerimientosAtendidos').trigger('click');
                    }
                });
                $('#btnBuscarRequerimientosAtendidos').on('click', (e) => {
                    $tablaListaRequerimientosAtendidos.search($input.val()).draw();
                })
                //Fin boton de busqueda

                that.updateContadorFiltroRequerimientosAtendidos();



            },
            "drawCallback": function (settings) {
                //Botón de búsqueda
                $('#listaRequerimientosAtendidos_filter input').prop('disabled', false);
                $('#btnBuscarRequerimientosAtendidos').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaRequerimientosAtendidos_filter input').trigger('focus');
                //fin botón búsqueda
                if ($tablaListaRequerimientosAtendidos.rows().data().length == 0) {
                    Lobibox.notify('info', {
                        title: false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: `No se encontro data disponible para mostrar`
                    });
                }
                //Botón de búsqueda
                $('#listaRequerimientosAtendidos_filter input').prop('disabled', false);
                $('#btnBuscarRequerimientosAtendidos').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaRequerimientosAtendidos_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaRequerimientosAtendidos").LoadingOverlay("hide", true);
            },
            "createdRow": function (row, data, dataIndex) {
                let color = '#ffffff';
                switch (data.bootstrap_color) {
                    case 'default':
                        color = '#777777';
                        break;
                    case 'primary':
                        color = '#3c8dbc';
                        break;
                    case 'success':
                        color = '#5cb85c';
                        break;
                    case 'secundary':
                        color = '#ffffff';
                        break;
                    case 'warning':
                        color = '#f39c12';
                        break;
                    case 'info':
                        color = '#72bcd4';
                        break;
                    case 'danger':
                        color = '#d9534f';
                        break;

                    default:
                        color = '#ffffff';
                        break;
                }
                $(row.childNodes[9]).css('background-color', color);
            }

        });
    }




    // getDataSelectSede(idEmpresa) {

    //     if (idEmpresa > 0) {
    //         this.requerimientoPendienteCtrl.obtenerSede(idEmpresa).then((res) => {
    //             this.llenarSelectFiltroSede(res);
    //         }).catch(function (err) {
    //             console.log(err)
    //         })
    //     } else {
    //         let selectElement = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='sede']");
    //         if (selectElement.options.length > 0) {
    //             let i, L = selectElement.options.length - 1;
    //             for (i = L; i >= 0; i--) {
    //                 selectElement.remove(i);
    //             }
    //             let option = document.createElement("option");

    //             option.value = 'SIN_FILTRO';
    //             option.text = '-----------------';
    //             selectElement.add(option);
    //         }
    //     }
    //     return false;
    // }

    // llenarSelectFiltroSede(array) {
    //     let selectElement = document.querySelector("div[id='modal-filtro-requerimientos-pendientes'] select[name='sede']");
    //     if (selectElement.options.length > 0) {
    //         let i, L = selectElement.options.length - 1;
    //         for (i = L; i >= 0; i--) {
    //             selectElement.remove(i);
    //         }
    //     }
    //     array.forEach(element => {
    //         let option = document.createElement("option");
    //         option.text = element.descripcion;
    //         option.value = element.id_sede;
    //         option.setAttribute('data-ubigeo', element.id_ubigeo);
    //         option.setAttribute('data-name-ubigeo', element.ubigeo_descripcion);
    //         if (element.codigo == 'LIMA' || element.codigo == 'Lima') { // default sede lima
    //             option.selected = true;

    //         }

    //         selectElement.add(option);
    //     });

    // }

    // observarRequerimientoLogistica(idRequerimiento){
    //     $('#modal-observar-requerimiento-logistica').modal({
    //         show: true
    //     });
    // }
    // registrarObservaciónRequerimientoLogistica(){
    //         Lobibox.notify('success', {
    //             title:false,
    //             size: 'mini',
    //             rounded: true,
    //             sound: false,
    //             delayIndicator: false,
    //             msg: `Observación registrada`
    //         });
    // }




    abrirRequerimiento(idRequerimiento) {
        // Abrir nuevo tab
        localStorage.setItem('idRequerimiento', idRequerimiento);
        let url = "/necesidades/requerimiento/elaboracion/index";
        // var win = window.open(url, '_blank');
        var win = location.href = url;
        // Cambiar el foco al nuevo tab (punto opcional)
        win.focus();
    }




    verDetalleRequerimiento(obj) {
        let tr = obj.closest('tr');
        var row = $tablaListaRequerimientosPendientes.row(tr);
        var id = obj.dataset.idRequerimiento;
        if (row.child.isShown()) {
            //  This row is already open - close it
            row.child.hide();
            tr.classList.remove('shown');
        }
        else {
            // Open this row
            //    row.child( format(iTableCounter, id) ).show();
            this.buildFormatListaRequerimientosPendientes(obj, iTableCounter, id, row);
            tr.classList.add('shown');
            // try datatable stuff
            oInnerTable = $('#listaRequerimientosPendientes_' + iTableCounter).dataTable({
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

    verDetalleRequerimientoAtendidos(obj) {
        let tr = obj.closest('tr');
        var row = $tablaListaRequerimientosAtendidos.row(tr);
        var id = obj.dataset.idRequerimiento;
        if (row.child.isShown()) {
            //  This row is already open - close it
            row.child.hide();
            tr.classList.remove('shown');
        }
        else {
            // Open this row
            //    row.child( format(iTableCounter, id) ).show();
            this.buildFormatListaRequerimientosAtendidos(obj, iTableCounter, id, row);
            tr.classList.add('shown');
            // try datatable stuff
            oInnerTable = $('#listaRequerimientosPendientes_' + iTableCounter).dataTable({
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

    retornarAListaPendientes(obj) {
        let tr = obj.closest('tr');
        var idRequerimiento = obj.dataset.idRequerimiento;
        var codigoRequerimiento = obj.dataset.codigoRequerimiento;

        Swal.fire({
            title: 'Esta seguro de retornar el requerimiento ' + codigoRequerimiento + ' a la lista de pendientes?',
            text: "El nuevo estado de requerimiento sera: atención parcial",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, retornar'

        }).then((result) => {
            if (result.isConfirmed) {
                this.requerimientoPendienteCtrl.retornarRequerimientoAtendidoAListaPendientes(idRequerimiento).then((res) => {
                    if (res.estado == 'success') {
                        tr.remove();
                        Lobibox.notify('success', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: res.mensaje
                        });

                    } else {
                        Swal.fire(
                            'Error en el servidor',
                            res.mensaje,
                            res.estado
                        );
                    }

                });
            }
        })

    }
    buildFormatListaRequerimientosPendientes(obj, table_id, id, row) {
        obj.setAttribute('disabled', true);

        this.requerimientoPendienteCtrl.obtenerDetalleRequerimientos(id).then((res) => {
            obj.removeAttribute('disabled');
            this.construirDetalleRequerimientoListaRequerimientosPendientes(table_id, row, res);
        }).catch((err) => {
            console.log(err)
        })
    }
    buildFormatListaRequerimientosAtendidos(obj, table_id, id, row) {
        obj.setAttribute('disabled', true);

        this.requerimientoPendienteCtrl.obtenerDetalleRequerimientos(id).then((res) => {
            obj.removeAttribute('disabled');
            this.construirDetalleRequerimientoListaRequerimientosAtendidos(table_id, row, res);
        }).catch((err) => {
            console.log(err)
        })
    }


    modalVerOrdenDeRequerimiento(obj) {

        $('#modal-ver-orden-de-requerimiento').modal({
            show: true,
            backdrop: 'static'
        });

        document.querySelector("div[id='modal-ver-orden-de-requerimiento'] span[id='codigo']").textContent = '';
        document.querySelector("div[id='modal-ver-orden-de-requerimiento'] div[id='contenedor-ordenes-de-requerimiento']").innerHTML = '';

        let linkOrden = [];
        if (JSON.parse(obj.dataset.orden).length > 0) {
            (JSON.parse(obj.dataset.orden)).forEach(element => {
                linkOrden.push(`<label class='lbl-codigo handleClickAbrirOrden' title='Ir a orden' data-id-orden='${element.id_orden}'>${element.codigo}</label>`);

            });
            document.querySelector("div[id='modal-ver-orden-de-requerimiento'] div[id='contenedor-ordenes-de-requerimiento']").innerHTML = linkOrden.toString();
        }

        document.querySelector("div[id='modal-ver-orden-de-requerimiento'] span[id='codigo']").textContent = obj.dataset.codigoRequerimiento != null ? obj.dataset.codigoRequerimiento : '';

    }

    abrirOrden(obj) {
        if (obj.dataset.idOrden > 0) {
            sessionStorage.removeItem('reqCheckedList');
            sessionStorage.removeItem('tipoOrden');
            sessionStorage.setItem("idOrden", obj.dataset.idOrden);
            sessionStorage.setItem("action", 'historial');

            let url = "/logistica/gestion-logistica/compras/ordenes/elaborar/index";
            var win = window.open(url, '_blank');
            win.focus();
        }
    }




    construirDetalleRequerimientoListaRequerimientosPendientes(table_id, row, response) {

        var html = '';
        console.log(response);
        if (response.length > 0) {
            response.forEach(function (element) {
                // if(element.tiene_transformacion==false){
                let stockComprometido = 0;
                (element.reserva).forEach(reserva => {
                    if (reserva.estado != 7) {
                        stockComprometido += parseFloat(reserva.stock_comprometido);
                    }
                });

                let atencionOrden = 0;
                let objOrdenList = [];
                (element.ordenes_compra).forEach(orden => { // TODO: no incluir anulados
                    if (orden.estado != 7) {
                        atencionOrden += parseFloat(orden.cantidad);
                        objOrdenList.push({ 'id_orden': orden.id_orden_compra, 'codigo': orden.codigo });
                    }
                });

                let cantidadAdjuntosDetalleRequerimiento = 0;
                (element.adjunto_detalle_requerimiento).forEach(adjuntoItem => {
                    cantidadAdjuntosDetalleRequerimiento++;
                });
                //
                html += `<tr>
                        <td style="border: none; text-align:center;" data-part-number="${element.part_number}" data-producto-part-number="${element.producto_part_number}">${(element.producto_part_number != null ? element.producto_part_number : (element.part_number != null ? element.part_number : ''))} ${element.tiene_transformacion == true ? '<br><span class="label label-default">Transformado</span>' : ''}</td>
                        <td style="border: none; text-align:left;">${element.producto_codigo != null ? element.producto_codigo : ''}</td>
                        <td style="border: none; text-align:left;">${element.producto_codigo_softlink != null ? element.producto_codigo_softlink : ''}</td>
                        <td style="border: none; text-align:left;">${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion ? element.descripcion : '')}</td>
                        <td style="border: none; text-align:center;">${element.unidad_medida_producto != null ? element.unidad_medida_producto : element.abreviatura}</td>
                        <td style="border: none; text-align:center;">${element.cantidad > 0 ? element.cantidad : ''}</td>
                        <td style="border: none; text-align:center;">${(element.precio_unitario > 0 ? ((element.moneda_simbolo ? element.moneda_simbolo : ((element.moneda_simbolo ? element.moneda_simbolo : '') + '0.00')) + $.number(element.precio_unitario, 2)) : (element.moneda_simbolo ? element.moneda_simbolo : '') + '0.00')}</td>
                        <td style="border: none; text-align:center;">${(parseFloat(element.subtotal) > 0 ? ((element.moneda_simbolo ? element.moneda_simbolo : '') + $.number(element.subtotal, 2)) : ((element.moneda_simbolo ? element.moneda_simbolo : '') + $.number((element.cantidad * element.precio_unitario), 2)))}</td>
                        <td style="border: none; text-align:center;">${element.motivo != null ? element.motivo : ''}</td>
                        <td style="border: none; text-align:center;">${stockComprometido != null && stockComprometido > 0 ? stockComprometido : '0'}</td>
                        <td style="border: none; text-align:center;">${atencionOrden != null && atencionOrden > 0 ? `<span class="label label-info handleClickModalVerOrdenDeRequerimiento" data-codigo-requerimiento="${element.codigo_requerimiento}" data-orden=${JSON.stringify(objOrdenList)} style="cursor:pointer;" >${atencionOrden}</span>` : '0'} </td>
                        <td style="border: none; text-align:center;">${element.estado_doc != null && element.tiene_transformacion == false ? element.estado_doc : ''}</td>
                        <td style="border: none; text-align:center;"> <div style="display:flex;">
                            ${cantidadAdjuntosDetalleRequerimiento > 0 ? `<button type="button" class="btn btn-default btn-xs handleClickVerAdjuntoDetalleRequerimiento" name="btnVerAdjuntoDetalleRequerimiento" title="Ver adjuntos" data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" data-descripcion="${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion ? element.descripcion : '')}" ><i class="fas fa-paperclip"></i></button>` : ''}
                            <button type="button" class="btn btn-primary btn-xs handleClickActualizarTipoItem" name="btnActualizarTipoItem" title="Tipo Item: ${element.id_tipo_item == 1 ? 'Producto' : (element.id_tipo_item == 2 ? 'Servicio' : '')}" data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" data-descripcion="${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion ? element.descripcion : '')}" >${element.id_tipo_item == 1 ? 'P' : (element.id_tipo_item == 2 ? 'S' : '')}</button>
                            </div>
                        </td>
                        </tr>`;
                // }
            });
            var tabla = `<table class="table table-condensed table-bordered"
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none; text-align:center;">Part number</th>
                        <th style="border: none; text-align:center;">Cód. producto</th>
                        <th style="border: none; text-align:center;">Cód. softlink</th>
                        <th style="border: none; text-align:center;">Descripcion</th>
                        <th style="border: none; text-align:center;">Unidad medida</th>
                        <th style="border: none; text-align:center;">Cantidad</th>
                        <th style="border: none; text-align:center;">Precio unitario</th>
                        <th style="border: none; text-align:center;">Subtotal</th>
                        <th style="border: none; text-align:center;">Motivo</th>
                        <th style="border: none; text-align:center;">Reserva almacén</th>
                        <th style="border: none; text-align:center;">Atención Orden</th>
                        <th style="border: none; text-align:center;">Estado</th>
                        <th style="border: none; text-align:center;">Acción</th>
                    </tr>
                </thead>
                <tbody style="background: #e7e8ea;">${html}</tbody>
                </table>`;
        } else {
            var tabla = `<table class="table table-sm" style="border: none;"
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
        }
        row.child(tabla).show();
    }

    construirDetalleRequerimientoListaRequerimientosAtendidos(table_id, row, response) {

        var html = '';
        // console.log(response);
        if (response.length > 0) {
            response.forEach(function (element) {
                // if(element.tiene_transformacion==false){
                let stockComprometido = 0;
                (element.reserva).forEach(reserva => {
                    if (reserva.estado != 7) {
                        stockComprometido += parseFloat(reserva.stock_comprometido);
                    }
                });

                let atencionOrden = 0;
                let objOrdenList = [];
                (element.ordenes_compra).forEach(orden => {
                    if (orden.estado != 7) {
                        atencionOrden += parseFloat(orden.cantidad);
                        objOrdenList.push({ 'id_orden': orden.id_orden_compra, 'codigo': orden.codigo });
                    }

                });

                let cantidadAdjuntosDetalleRequerimiento = 0;
                (element.adjunto_detalle_requerimiento).forEach(adjuntoItem => {
                    cantidadAdjuntosDetalleRequerimiento++;
                });

                html += `<tr>
                        <td style="border: none; text-align:center;" data-part-number="${element.part_number}" data-producto-part-number="${element.producto_part_number}">${(element.producto_part_number != null ? element.producto_part_number : (element.part_number != null ? element.part_number : ''))} ${element.tiene_transformacion == true ? '<br><span class="label label-default">Transformado</span>' : ''}</td>
                        <td style="border: none; text-align:left;">${element.producto_codigo != null ? element.producto_codigo : ''}</td>
                        <td style="border: none; text-align:left;">${element.producto_codigo_softlink != null ? element.producto_codigo_softlink : ''}</td>
                        <td style="border: none; text-align:left;">${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion ? element.descripcion : '')}</td>
                        <td style="border: none; text-align:center;">${element.abreviatura != null ? element.abreviatura : ''}</td>
                        <td style="border: none; text-align:center;">${element.cantidad > 0 ? element.cantidad : ''}</td>
                        <td style="border: none; text-align:center;">${(element.precio_unitario > 0 ? ((element.moneda_simbolo ? element.moneda_simbolo : ((element.moneda_simbolo ? element.moneda_simbolo : '') + '0.00')) + $.number(element.precio_unitario, 2)) : (element.moneda_simbolo ? element.moneda_simbolo : '') + '0.00')}</td>
                        <td style="border: none; text-align:center;">${(parseFloat(element.subtotal) > 0 ? ((element.moneda_simbolo ? element.moneda_simbolo : '') + $.number(element.subtotal, 2)) : ((element.moneda_simbolo ? element.moneda_simbolo : '') + $.number((element.cantidad * element.precio_unitario), 2)))}</td>
                        <td style="border: none; text-align:center;">${element.motivo != null ? element.motivo : ''}</td>
                        <td style="border: none; text-align:center;">
                            ${stockComprometido != null && parseFloat(stockComprometido) > 0 ? '<span class="label label-default">' + stockComprometido + '</span>' : '0'}
                        </td>
                        <td style="border: none; text-align:center;">
                            ${atencionOrden != null && atencionOrden > 0 ? `<span class="label label-info handleClickModalVerOrdenDeRequerimiento" data-codigo-requerimiento="${element.codigo_requerimiento}" data-orden=${JSON.stringify(objOrdenList)}  style="cursor:pointer;">${atencionOrden}</span>` : '0'}
                        </td>
                        <td style="border: none; text-align:center;">${element.estado_doc != null && element.tiene_transformacion == false ? element.estado_doc : ''}</td>
                        <td style="border: none; text-align:center;">
                        ${cantidadAdjuntosDetalleRequerimiento > 0 ? `<button type="button" class="btn btn-default btn-xs handleClickVerAdjuntoDetalleRequerimiento" name="btnVerAdjuntoDetalleRequerimiento" title="Ver adjuntos" data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" data-descripcion="${element.producto_descripcion != null ? element.producto_descripcion : 'no mapeado'}" ><i class="fas fa-paperclip"></i></button>` : ''}
                        <button type="button" class="btn btn-danger btn-xs handleClickAnularReservaActiva" name="btnAnularReservaAtendida" title="${stockComprometido != null && parseFloat(stockComprometido) > 0 ? 'Anular reserva' : 'Sin reservas'}" data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" data-descripcion="${element.producto_descripcion != null ? element.producto_descripcion : 'no mapeado'}" ${stockComprometido != null && parseFloat(stockComprometido) > 0 ? '' : 'disabled'} ><i class="fas fa-minus-circle"></i></button>
                        </td>
                        </tr>`;
                // }
            });
            var tabla = `<table class="table table-condensed table-bordered"
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none; text-align:center;">Part number</th>
                        <th style="border: none; text-align:center;">Cód. producto</th>
                        <th style="border: none; text-align:center;">Cód. softlink</th>
                        <th style="border: none; text-align:center;">Descripcion</th>
                        <th style="border: none; text-align:center;">Unidad medida</th>
                        <th style="border: none; text-align:center;">Cantidad</th>
                        <th style="border: none; text-align:center;">Precio unitario</th>
                        <th style="border: none; text-align:center;">Subtotal</th>
                        <th style="border: none; text-align:center;">Motivo</th>
                        <th style="border: none; text-align:center;">Reserva almacén</th>
                        <th style="border: none; text-align:center;">Atención Orden</th>
                        <th style="border: none; text-align:center;">Estado</th>
                        <th style="border: none; text-align:center;">Acción</th>
                    </tr>
                </thead>
                <tbody style="background: #e7e8ea;">${html}</tbody>
                </table>`;
        } else {
            var tabla = `<table class="table table-sm" style="border: none;"
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
        }
        row.child(tabla).show();
    }

    // filtros

    abrirModalFiltrosRequerimientosPendientes() {
        $('#modal-filtro-requerimientos-pendientes').modal({
            show: true,
            backdrop: 'static'
        });
    }

    exportarListaRequerimientosPendientesExcel() {
        window.open(`exportar-lista-requerimientos-pendientes-excel`);

    }

    abrirModalFiltrosRequerimientosAtendidos() {
        $('#modal-filtro-requerimientos-atendidos').modal({
            show: true,
            backdrop: 'static'
        });
    }

    exportTablaRequerimientosAtentidosExcel() {
        window.open(`reporte-requerimientos-atendidos-excel/${this.ActualParametroEmpresa}/${this.ActualParametroSede}/${this.ActualParametroFechaDesde}/${this.ActualParametroFechaHasta}/${this.ActualParametroReserva}/${this.ActualParametroOrden}`);

    }
    // chkEmpresa(e) {

    //     if (e.target.checked == true) {
    //         document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='empresa']").removeAttribute('readOnly');

    //     } else {
    //         document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='empresa']").setAttribute('readOnly', true);

    //     }
    // }

    // chkSede(e) {

    //     if (e.target.checked == true) {
    //         document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='sede']").removeAttribute('readOnly');
    //     } else {
    //         document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='sede']").setAttribute('readOnly', true);

    //     }
    // }


    // handleChangeFilterReqByEmpresa(event) {
    //     let id_empresa = event.target.value;
    //     requerimientoPendienteCtrl.getDataSelectSede(id_empresa).then(function (res) {
    //         requerimientoPendienteView.llenarSelectSede(res);
    //     }).catch(function (err) {
    //         console.log(err)
    //     })

    // }

    llenarSelectSede(array) {
        let selectElement = document.querySelector("select[name='sede']");

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

    // aplicarFiltros() {
    //     let idEmpresa = null;
    //     let idSede = null;

    //     let chkEmpresa = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] input[name='chkEmpresa']").checked;
    //     let chkSede = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] input[name='chkSede']").checked;

    //     if (chkEmpresa == true) {
    //         idEmpresa = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='empresa']").value;

    //     }
    //     if (chkSede == true) {
    //         idSede = document.querySelector("form[id='formFiltroListaRequerimientosPendientes'] select[name='sede']").value;
    //     }

    //     $('#modal-filtro-requerimientos-pendientes').modal('hide');

    //     this.renderRequerimientoPendienteListModule(idEmpresa > 0 ? idEmpresa : null, idSede > 0 ? idSede : null);

    // }






    // atender con almacen
    atenderConAlmacen(obj) {
        $('#modal-atender-con-almacen').modal({
            show: true,
            backdrop: 'true'
        });

        trRequerimientosPendientes = obj.closest("tr");

        document.querySelector("form[id='form-reserva-almacen'] input[name='id_requerimiento']").value = obj.dataset.idRequerimiento;
        // let codigoRequerimiento =obj.dataset.codigo;
        document.querySelector("div[id='modal-atender-con-almacen'] span[id='codigo_requerimiento']").textContent = obj.dataset.codigoRequerimiento;
        document.querySelector("div[id='modal-atender-con-almacen'] span[id='almacen_requerimiento']").textContent = obj.dataset.almacenRequerimiento;
        this.llenarTablaModalAtenderConAlmacen(obj.dataset.idRequerimiento);

    }

    llenarTablaModalAtenderConAlmacen(idRequerimiento) {
        this.requerimientoPendienteCtrl.limpiarTabla('listaItemsRequerimientoParaAtenderConAlmacen');
        this.requerimientoPendienteCtrl.openModalAtenderConAlmacen(idRequerimiento).then((res) => {
            this.construirTablaListaItemsRequerimientoParaAtenderConAlmacen(res.data);
        }).catch(function (err) {
            console.log(err)
        })
    }

    construirTablaListaItemsRequerimientoParaAtenderConAlmacen(data) {
        $('#listaItemsRequerimientoParaAtenderConAlmacen').dataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'language': vardataTables[0],
            "bDestroy": true,
            "bInfo": false,
            // 'paging': true,
            "bLengthChange": false,
            // "pageLength": 3,
            'data': data,

            // 'order': [[0, 'desc']],
            // "scrollY": 200,
            // "scrollX": true,

            // 'searching': false,
            // 'scrollCollapse': true,
            // 'processing': true,
            'columns': [

                {
                    render: function (data, type, row) {
                        return (row.producto != null ? row.producto.codigo : '');
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.producto != null ? row.producto.cod_softlink : '');
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.producto != null ? row.producto.part_number : '');
                    }
                },
                {
                    render: function (data, type, row) {
                        return ((row.producto && row.producto.descripcion != null && row.producto.descripcion != '') ? row.producto.descripcion : (row.descripcion != null ? row.descripcion : ''));
                    }
                },
                {
                    render: function (data, type, row) {
                        return row.unidad_medida != null ? row.unidad_medida.descripcion : '';
                    }
                },
                {
                    render: function (data, type, row) {
                        return row.cantidad != null ? row.cantidad : '';
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.producto != null ? (row.producto.moneda != null ? row.producto.moneda.descripcion : '') : '');
                    }
                },
                {
                    render: function (data, type, row) {
                        return row.proveedor_seleccionado != null ? row.proveedor_seleccionado : '';
                    }
                },
                {
                    render: function (data, type, row) {
                        let estado = (row.estado.estado_doc != null ? row.estado.estado_doc : '');
                        let productoTransformado = row.tiene_transformacion == true ? '<br><span class="label label-default">Producto Transformado</span>' : '';
                        return (estado + productoTransformado);
                    }
                },
                {
                    render: function (data, type, row) {
                        let cantidadReservada = 0;
                        if (row.reserva != null) {
                            (row.reserva).forEach(element => {
                                cantidadReservada += parseFloat(element.stock_comprometido);
                            });
                        }
                        return cantidadReservada; //cantidad reservada
                    }
                },
                {
                    render: function (data, type, row) {
                        let codigoReserva = [];
                        if (row.reserva != null) {
                            (row.reserva).forEach(element => {
                                codigoReserva.push(element.codigo ? element.codigo : (element.id_reserva ? element.id_reserva : ''));
                            });
                        }
                        return codigoReserva.length > 0 ? codigoReserva : '(Sin reserva)'; //codigo o id reservada
                    }
                },
                {
                    render: function (data, type, row) {
                        if (row.id_producto > 0) {

                            if (document.querySelector("li[class~='handleClickTabRequerimientosAtendidos']").classList.contains("active") == true) {
                                return `<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
                                <button type="button" class="btn btn-xs btn-info btnHistorialReserva handleClickAbrirModaHistorialReserva"
                                    data-codigo-requerimiento="${document.querySelector("span[id='codigo_requerimiento']").textContent}"
                                    data-almacen-requerimiento="${document.querySelector("span[id='almacen_requerimiento']").textContent}"
                                    data-id-detalle-requerimiento="${row.id_detalle_requerimiento}"
                                    title="Historial reserva" ><i class="fas fa-eye fa-xs"></i></button>
                                </div></center>`;
                            } else {

                                return `<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
                                <button type="button" class="btn btn-xs btn-success btnNuevaReserva handleClickAbrirModalNuevaReserva"
                                    data-codigo-requerimiento="${document.querySelector("span[id='codigo_requerimiento']").textContent}"
                                    data-almacen-requerimiento="${document.querySelector("span[id='almacen_requerimiento']").textContent}"
                                    data-id-requerimiento="${row.id_requerimiento}"
                                    data-id-detalle-requerimiento="${row.id_detalle_requerimiento}"
                                    data-id-producto="${row.id_producto}"
                                    title="Nueva reserva" ><i class="fas fa-box fa-xs"></i></button>
                                <button type="button" class="btn btn-xs btn-info btnHistorialReserva handleClickAbrirModaHistorialReserva"
                                    data-codigo-requerimiento="${document.querySelector("span[id='codigo_requerimiento']").textContent}"
                                    data-almacen-requerimiento="${document.querySelector("span[id='almacen_requerimiento']").textContent}"
                                    data-id-detalle-requerimiento="${row.id_detalle_requerimiento}"
                                    data-id-producto="${row.id_producto}"
                                    title="Historial reserva" ><i class="fas fa-eye fa-xs"></i></button>
                                </div></center>`;
                            }

                        } else {
                            return '(Sin mapear)';
                        }
                    }
                },

                // {
                //     'render':
                //         function (data, type, row, meta) {
                //             let select = '';
                //             if (row.tiene_transformacion == false) {
                //                 select = `<input type="hidden" name="idDetalleRequerimiento[]" value="${row.id_detalle_requerimiento}"><select class="form-control selectAlmacenReserva" name="almacenReserva[]" >`;
                //                 select += `<option value ="0">Sin selección</option>`;
                //                 data_almacenes.forEach(element => {
                //                     if (row.id_almacen_reserva == element.id_almacen) {
                //                         select += `<option value="${element.id_almacen}" data-id-empresa="${element.id_empresa}" selected>${element.descripcion}</option> `;

                //                     } else {
                //                         select += `<option value="${element.id_almacen}" data-id-empresa="${element.id_empresa}">${element.descripcion}</option> `;
                //                     }
                //                 });
                //                 select += `</select>`;
                //             }


                //             return select;
                //         }
                // },
                // {
                //     'render':
                //         function (data, type, row, meta) {
                //             let action = '';
                //             if (row.tiene_transformacion == false) {
                //                 action = `<input type="number" min="0" name="cantidadReserva[]" class="form-control inputCantidadArReservar handleBlurUpdateInputCantidadAAtender"  data-cantidad="${row.cantidad}" style="width: 70px;" data-indice="${meta.row}" value="${parseInt(row.stock_comprometido ? row.stock_comprometido : 0)}" />`;

                //                 that.updateObjCantidadAAtender(meta.row, row.stock_comprometido);

                //             }
                //             return action;
                //         }
                // }
            ],

            'columnDefs': [
                { 'targets': 0, 'className': "text-center" },
                { 'targets': 1, 'className': "text-center" },
                { 'targets': 2, 'className': "text-left", "width": "280px" },
                { 'targets': 3, 'className': "text-center" },
                { 'targets': 4, 'className': "text-center" },
                { 'targets': 5, 'className': "text-left" },
                { 'targets': 6, 'className': "text-center" },
                { 'targets': 7, 'className': "text-center" },
                { 'targets': 8, 'className': "text-center" },
                { 'targets': 9, 'className': "text-center" }

            ],
            'initComplete': function () {


            },
            "createdRow": function (row, data, dataIndex) {
                $(row.childNodes[2]).css('width', '280px');

                // $(row.childNodes[7]).css('background-color', '#586c86');
                // $(row.childNodes[7]).css('font-weight', 'bold');
                // $(row.childNodes[8]).css('background-color', '#586c86');
                // $(row.childNodes[8]).css('font-weight', 'bold');

            }
        });
    }

    abrirModalHistorialReserva(obj) {
        $('#modal-historial-reserva').modal({
            show: true,
            backdrop: 'true'
        });

        if (parseInt(obj.dataset.idDetalleRequerimiento) > 0) {
            this.requerimientoPendienteCtrl.obtenerHistorialDetalleRequerimientoParaReserva(obj.dataset.idDetalleRequerimiento).then((res) => {
                $('#modal-historial-reserva .modal-content').LoadingOverlay("hide", true);
                if (res.status == 200) {
                    this.llenarModalHistorialReserva(res.data);
                }
            }).catch(function (err) {
                Swal.fire(
                    '',
                    'Hubo un problema al intentar obtener la data del producto',
                    'error'
                );
            })

        }
    }

    llenarModalHistorialReserva(data) {
        if (data.id_producto > 0) {
            document.querySelector("div[id='modal-historial-reserva'] label[id='partNumber']").textContent = data.producto.part_number != null ? data.producto.part_number : (data.part_number != null ? data.part_number : '');
            document.querySelector("div[id='modal-historial-reserva'] label[id='descripcion']").textContent = data.producto.descripcion != null ? data.producto.descripcion : (data.descripcion != null ? data.descripcion : '');
            document.querySelector("div[id='modal-historial-reserva'] label[id='cantidad']").textContent = data.cantidad;
            document.querySelector("div[id='modal-historial-reserva'] label[id='unidadMedida']").textContent = data.unidad_medida.descripcion;
            this.listarTablaHistorialReservaProducto(data.reserva);
        } else {
            $('#modal-historial-reserva').modal('hide');
            Swal.fire(
                '',
                'Lo sentimos no se encontro que el producto seleccionado este mapeado, debe mapear el producto antes de realizar una reseva',
                'warning'
            );

        }
    }

    listarTablaHistorialReservaProducto(data) {
        this.requerimientoPendienteCtrl.limpiarTabla('listaHistorialReserva');
        // let cantidadTotalStockComprometido=0;
        if (data.length > 0) {
            (data).forEach(element => {
                // cantidadTotalStockComprometido+= element.stock_comprometido;
                document.querySelector("tbody[id='bodyListaHistorialReservaProducto']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td>${(element.codigo != null && element.codigo != '') ? element.codigo : (element.id_reserva)}</td>
                <td>${element.almacen.descripcion}</td>
                <td>${element.stock_comprometido}</td>
                <td>${element.usuario.nombre_corto}</td>
                <td>${element.estado.estado_doc}</td>
                </tr>`);
            });
            // document.querySelector("table[id='listaHistorialReserva'] label[name='totalReservado']").textContent=cantidadTotalStockComprometido;
        } else {
            document.querySelector("tbody[id='bodyListaHistorialReservaProducto']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td colspan="5" style="text-align:center;">(Sin reservas)</td>

            </tr>`);
        }
    }

    abrirModalNuevaReserva(obj) {

        this.limpiarModalNuevaReserva();
        // document.querySelector("div[id='modal-nueva-reserva'] div[id='contenedor-info-stock']").classList.add('oculto');

        $('#modal-nueva-reserva').modal({
            show: true,
            backdrop: 'true'
        });
        document.querySelector("div[id='modal-nueva-reserva'] span[id='codigoRequerimiento']").textContent = obj.dataset.codigoRequerimiento;
        document.querySelector("div[id='modal-nueva-reserva'] span[id='almacenRequerimiento']").textContent = obj.dataset.almacenRequerimiento;

        if (parseInt(obj.dataset.idDetalleRequerimiento) > 0) {

            this.requerimientoPendienteCtrl.obtenerDetalleRequerimientoParaReserva(obj.dataset.idDetalleRequerimiento).then((res1) => {

                // this.requerimientoPendienteCtrl.obtenerAlmacenPorDefectoRequerimiento(obj.dataset.idRequerimiento).then((res2) => {
                // console.log(res1);
                // console.log(res2);
                $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
                if (res1.status == 200) {
                    this.llenarModalNuevaReserva(res1.data);
                }
                // if (res2.status == 200) {
                //     this.seleccionarAlmacenPorDefectoRequerimientoParaReserva(res2.data)
                //     // this.handleChangeObtenerStockAlmacen();

                // }
                // }).catch(function (err) {
                //     Swal.fire(
                //         '',
                //         'Hubo un problema al  intentarobtener la data del requerimiento para obtener el almacén',
                //         'error'
                //     );
                // })


            }).catch(function (err) {
                Swal.fire(
                    '',
                    'Hubo un problema al  intentarobtener la data del producto',
                    'error'
                );
            })
            //inicio obtener lista de almacenes con stock del producto selecciondo

            this.construirTablaAlmacenesConStockDisponible(obj.dataset.idProducto);

            // this.obtenerAlmacenesConStockDisponible(obj.dataset.idProducto).then((response) => {
            //     console.log(response);
            //     this.construirTablaAlmacenesConStockDisponible(response['data']);

            // }).catch(function (err) {
            //     Swal.fire(
            //         '',
            //         'Hubo un problema al  intentarobtener la data del requerimiento para obtener el almacén',
            //         'error'
            //     );
            // })
            //fin obtener lista de almacenes con stock del producto selecciondo



        }
    }

    // obtenerAlmacenesConStockDisponible(idProducto){
    //     return new Promise(function(resolve, reject) {
    //         $.ajax({
    //             type: 'GET',
    //             url: 'almacenes-con-stock-disponible/'+idProducto,
    //             processData: false,
    //             contentType: false,
    //             dataType: 'JSON',
    //             beforeSend:  (data)=> {

    //             },
    //             success: (response) =>{
    //                 resolve(response);
    //             },
    //             fail:  (jqXHR, textStatus, errorThrown) =>{
    //                 Swal.fire(
    //                     '',
    //                     'Lo sentimos hubo un error en el servidor al intentar obtener la data, por favor vuelva a intentarlo',
    //                     'error'
    //                 );
    //                 console.log(jqXHR);
    //                 console.log(textStatus);
    //                 console.log(errorThrown);
    //             }
    //         });
    //         });
    // }

    construirTablaAlmacenesConStockDisponible(idProducto) {

        $('#listaAlmacenesConStockDeProducto').dataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'language': vardataTables[0],
            "bDestroy": true,
            "bInfo": false,
            "bLengthChange": false,
            "pageLength": 5,
            "autoWidth": false,
            'ajax': {
                'url': 'almacenes-con-stock-disponible/' + idProducto,
                'type': 'GET',
                beforeSend: data => {

                }

            },
            'columns': [
                {
                    'data': 'descripcion', 'name': 'alm_almacen.descripcion',
                    render: function (data, type, row) {
                        return ((row.codigo != null || row.descripcion != null) ? row.codigo + ' - ' + row.descripcion : '');
                    }
                },
                { 'data': 'stock', 'className': "text-center" },
                { 'data': 'cantidad_stock_comprometido', 'className': "text-center", 'searchable': false, 'orderable': false },
                {
                    'searchable': false, 'orderable': false,
                    render: function (data, type, row) {
                        return (row.stock - row.cantidad_stock_comprometido);
                    }
                },

                {
                    render: function (data, type, row) {

                        return `<center><div class="btn-group" role="group" style="margin-bottom: 5px;">
                    <button type="button" class="btn btn-xs btn-success handleClickSeleccionarAlmacenParaReserva"
                        data-id-almacen="${row.id_almacen ?? ''}"
                        data-almacen-requerimiento="${row.codigo}-${row.descripcion}"
                        data-stock-disponible="${(row.stock - row.cantidad_stock_comprometido)}"
                        title="Agregar y guardar" ${(row.stock - row.cantidad_stock_comprometido) == 0 ? 'disabled' : ''} >Seleccionar</button>
                    </div></center>`;

                    }
                },
            ],

            'columnDefs': [
                { 'targets': 0, "width": "80%" },
                { 'targets': 1, "width": "10%", 'className': "text-center" },
                { 'targets': 2, "width": "10%", 'className': "text-center" }

            ]
        });
    }


    // seleccionarAlmacenPorDefectoRequerimientoParaReserva(data) {
    //     if (data.id_almacen > 0) {
    //         document.querySelector("div[id='modal-nueva-reserva'] input[name='almacenReserva']").value = data.id_almacen;
    //     }
    // }

    llenarModalNuevaReserva(data) {
        // console.log(data);
        if (data.id_producto > 0) {
            document.querySelector("form[id='form-nueva-reserva'] input[name='idProducto']").value = data.id_producto;
            document.querySelector("form[id='form-nueva-reserva'] input[name='idRequerimiento']").value = data.id_requerimiento;
            document.querySelector("form[id='form-nueva-reserva'] input[name='idDetalleRequerimiento']").value = data.id_detalle_requerimiento;
            document.querySelector("form[id='form-nueva-reserva'] input[name='idUnidadMedida']").value = data.id_unidad_medida;
            document.querySelector("form[id='form-nueva-reserva'] label[id='partNumber']").textContent = data.producto.part_number != null ? data.producto.part_number : (data.part_number != null ? data.part_number : '');
            document.querySelector("form[id='form-nueva-reserva'] label[id='descripcion']").textContent = data.producto.descripcion != null ? data.producto.descripcion : (data.descripcion != null ? data.descripcion : '');
            document.querySelector("form[id='form-nueva-reserva'] label[id='cantidad']").textContent = data.cantidad;
            document.querySelector("form[id='form-nueva-reserva'] label[id='unidadMedida']").textContent = data.unidad_medida.descripcion;
            document.querySelector("form[id='form-nueva-reserva'] label[id='cantidadEnOrdenes']").textContent = data.ordenes_compra != null && data.ordenes_compra.length > 0 ? data.ordenes_compra.reduce((a, b) => +a + +b.cantidad, 0) : '0';
            document.querySelector("div[id='modal-nueva-reserva'] input[name='cantidadReserva']").value = parseFloat(document.querySelector("div[id='modal-nueva-reserva'] label[id='cantidad']").textContent) > 0 ? document.querySelector("div[id='modal-nueva-reserva'] label[id='cantidad']").textContent : 0;
            this.listarTablaListaConReserva(data.reserva);
        } else {
            $('#modal-nueva-reserva').modal('hide');
            Swal.fire(
                '',
                'Lo sentimos no se encontro que el producto seleccionado este mapeado, debe mapear el producto antes de realizar una reseva',
                'warning'
            );

        }

    }

    listarTablaListaConReserva(data) {
        this.requerimientoPendienteCtrl.limpiarTabla('listaConReserva');
        let cantidadTotalStockComprometido = 0;
        if (data.length > 0) {
            (data).forEach(element => {
                if (element.estado.id_estado_doc != 7) {
                    cantidadTotalStockComprometido += parseFloat(element.stock_comprometido);

                    let botonAnularReserva = `<button type="button" class="btn btn-xs btn-danger btnAnularReserva handleClickAnularReserva" data-codigo-reserva="${element.codigo}" data-id-reserva="${element.id_reserva}"  data-id-detalle-requerimiento="${element.id_detalle_requerimiento}" title="Anular"><i class="fas fa-times fa-xs"></i></button>`;
                    document.querySelector("tbody[id='bodyListaConReserva']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td>${(element.codigo != null && element.codigo != '') ? element.codigo : (element.id_reserva)}</td>
                    <td>${element.almacen.descripcion}</td>
                    <td>${element.stock_comprometido}</td>
                    <td>${element.usuario.nombre_corto}</td>
                    <td>${element.estado.estado_doc}</td>
                    <td>${element.estado.id_estado_doc ==1 ? botonAnularReserva:''}</td>
                    </tr>`);
                }
            });
            document.querySelector("table[id='listaConReserva'] label[name='totalReservado']").textContent = cantidadTotalStockComprometido;
        } else {
            document.querySelector("tbody[id='bodyListaConReserva']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
            <td colspan="5" style="text-align:center;">(Sin reservas)</td>

            </tr>`);
            document.querySelector("table[id='listaConReserva'] label[name='totalReservado']").textContent = 0;

        }
    }

    limpiarModalNuevaReserva() {
        document.querySelector("form[id='form-nueva-reserva'] input[name='idProducto']").value = '';
        document.querySelector("form[id='form-nueva-reserva'] input[name='idDetalleRequerimiento']").value = '';
        document.querySelector("form[id='form-nueva-reserva'] label[id='partNumber']").textContent = '';
        document.querySelector("form[id='form-nueva-reserva'] label[id='descripcion']").textContent = '';
        document.querySelector("form[id='form-nueva-reserva'] label[id='cantidad']").textContent = '';
        document.querySelector("form[id='form-nueva-reserva'] label[id='unidadMedida']").textContent = '';
        document.querySelector("form[id='form-nueva-reserva'] input[name='cantidadReserva']").value = '';
        document.querySelector("form[id='form-nueva-reserva'] input[name='almacenReserva']").value = 0;
        document.querySelector("form[id='form-nueva-reserva'] input[name='nombreAlmacenReserva']").value = '';
        document.querySelector("form[id='form-nueva-reserva'] input[name='stockDisponible']").value = 0;
        this.requerimientoPendienteCtrl.limpiarTabla('listaConReserva');
        // document.querySelector("form[id='form-nueva-reserva'] label[id='totalCantidadAtendidoConOrden']").textContent='';
        // document.querySelector("form[id='form-nueva-reserva'] label[id='totalCantidadConReserva']").textContent='';
        // document.querySelector("form[id='form-nueva-reserva'] label[id='total']").textContent='';
    }

    validarModalNuevaReserva() {
        let mensaje = '';
        let idProducto = document.querySelector("form[id='form-nueva-reserva'] input[name='idProducto']").value;
        let idDetalleRequerimiento = document.querySelector("form[id='form-nueva-reserva'] input[name='idDetalleRequerimiento']").value;
        let cantidadReserva = document.querySelector("form[id='form-nueva-reserva'] input[name='cantidadReserva']").value;
        let almacenReserva = document.querySelector("form[id='form-nueva-reserva'] input[name='almacenReserva']").value;
        if (!idProducto, !idDetalleRequerimiento > 0) {
            mensaje += '<li style="text-align: left;">El producto / item de requerimiento no tiene un ID valido.</li>';
        }
        if (!parseFloat(cantidadReserva) > 0 || parseFloat(cantidadReserva) < 0) {
            mensaje += '<li style="text-align: left;">Debe ingresar una cantidad a reservar mayor a cero.</li>';
        }
        if ((parseFloat(cantidadReserva) + parseFloat(document.querySelector("form[id='form-nueva-reserva'] label[name='totalReservado']").textContent)) > parseFloat(document.querySelector("form[id='form-nueva-reserva'] label[id='cantidad']").textContent)) {
            mensaje += '<li style="text-align: left;">La cantidad a reservar con la cantidad total reservada supera la cantidad solicitada, debe Ingresar un valor menor.</li>';
        }
        if ((parseFloat(cantidadReserva)) > parseFloat(document.querySelector("form[id='form-nueva-reserva'] input[name='stockDisponible']").value)) {
            mensaje += '<li style="text-align: left;">La cantidad a reservar no puede ser mayor a la cantidad disponible en stock.</li>';
        }
        if (!parseFloat(almacenReserva) > 0) {
            mensaje += '<li style="text-align: left;">Debe seleccionar un almacén.</li>';
        }
        return mensaje;
    }

    anularReserva(obj) {
        let idProducto = document.querySelector("div[id='modal-nueva-reserva'] input[name='idProducto']").value;
        let motivoDeAnulacion = '';
        Swal.fire({
            title: 'Esta seguro que desea anular la reserva ' + (obj.dataset.codigoReserva != '' ? obj.dataset.codigoReserva : obj.dataset.idReserva) + '?. Escriba un motivo',
            input: 'textarea',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Registrar',

            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            motivoDeAnulacion = result.value;
            let formData = new FormData();
            formData.append(`idReserva`, obj.dataset.idReserva);
            formData.append(`idDetalleRequerimiento`, obj.dataset.idDetalleRequerimiento);
            formData.append(`motivoDeAnulacion`, motivoDeAnulacion);
            if (motivoDeAnulacion == null || (motivoDeAnulacion).trim() == '') {

                Swal.fire(
                    '',
                    'Debe ingresar un motivo para anular',
                    'info'
                );
                return false;
            }
            if (result.isConfirmed) {

                $.ajax({
                    type: 'POST',
                    url: 'anular-reserva-almacen',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: (data) => { // Are not working with dataType:'jsonp'

                        $('#modal-nueva-reserva .modal-content').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) => {
                        // console.log(response);
                        if (response.id_reserva > 0) {
                            $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);

                            Lobibox.notify(response.tipo_estado, {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: response.mensaje
                            });

                            this.listarTablaListaConReserva(response.data);
                            this.llenarTablaModalAtenderConAlmacen(document.querySelector("form[id='form-nueva-reserva'] input[name='idRequerimiento']").value);


                        } else {
                            $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
                            Swal.fire(
                                '',
                                response.mensaje,
                                response.tipo_estado
                            );
                            console.log(response);
                        }

                        if (response.lista_finalizados.length > 0) {
                            (response.lista_finalizados).forEach(element => {
                                Swal.fire({
                                    title: '',
                                    html: `Se finalizó el cuadro de presupuesto ${element.cuadro_presupuesto.oportunidad.codigo_oportunidad} del requerimiento ${element.requerimiento.codigo}`,
                                    icon: 'info'
                                });
                            });
                        }
                        this.construirTablaAlmacenesConStockDisponible(idProducto);

                        // if (response.lista_restablecidos.length > 0) {
                        //     (response.lista_restablecidos).forEach(element => {
                        //         Swal.fire({
                        //             title: '',
                        //             html: `Se restableció el cuadro de presupuesto ${element.codigo_cuadro_presupuesto} del requerimiento ${element.codigo_requerimiento}`,
                        //             icon: 'info'
                        //         });
                        //     });
                        // }

                    },
                    fail: (jqXHR, textStatus, errorThrown) => {
                        $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un problema en el servidor al intentar anular la reserva, por favor vuelva a intentarlo',
                            'error'
                        );
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
        })

        $(document).off('focusin.modal'); // fix focus textarea de sweetalert
    }

    // handleChangeObtenerStockAlmacen() {
    //     let idAlmacen = document.getElementsByName("almacenReserva")[0].value;
    //     let cantidadReservar = document.getElementsByName("cantidadReserva")[0].value;
    //     if (idAlmacen > 0) {
    //         if (!cantidadReservar > 0) {
    //             Swal.fire({
    //                 icon: 'warning',
    //                 title: 'Oops...',
    //                 text: 'Primero debe ingresar una cantidad a reservar que sea mayor a cero',
    //             });
    //             document.getElementsByName("cantidadReserva")[0].closest('div').classList.add('has-error');

    //         } else {
    //             document.getElementsByName("cantidadReserva")[0].closest('div').classList.remove('has-error');

    //             const idProducto = document.querySelector("div[id='modal-nueva-reserva'] input[name='idProducto']").value;
    //             // const cantidadReserva = document.querySelector("div[id='modal-nueva-reserva'] input[name='cantidadReserva']").value > 0 ? document.querySelector("div[id='modal-nueva-reserva'] input[name='cantidadReserva']").value : 0;
    //             $.ajax({
    //                 type: 'POST',
    //                 url: 'obtener-stock-almacen',
    //                 data: { 'idAlmacen': idAlmacen, 'idProducto': idProducto },
    //                 dataType: 'JSON',
    //             }).done((response) => {

    //                 document.querySelector("div[id='modal-nueva-reserva'] div[id='contenedor-info-stock']").classList.remove('oculto');
    //                 document.querySelector("div[id='modal-nueva-reserva'] div[id='contenedor-info-stock'] span[id='info-stock-almacen']").textContent = response.stock;
    //                 document.querySelector("div[id='modal-nueva-reserva'] div[id='contenedor-info-stock'] span[id='info-reservas-activas']").textContent = response.reservas;
    //                 document.querySelector("div[id='modal-nueva-reserva'] div[id='contenedor-info-stock'] span[id='info-saldo-disponible']").textContent = response.saldo;

    //             }).fail((jqXHR, textStatus, errorThrown) => {
    //                 Swal.fire(
    //                     '',
    //                     'Lo sentimos hubo un error en el servidor al intentar consultar el stock del almacén seleccionado, por favor vuelva a intentarlo',
    //                     'error'
    //                 );
    //                 console.log(jqXHR);
    //                 console.log(textStatus);
    //                 console.log(errorThrown);
    //             });
    //         }

    //     } else {
    //         document.querySelector("div[id='modal-nueva-reserva'] div[id='contenedor-info-stock']").classList.add('oculto');

    //     }


    // }

    seleccionarAlmacenParaReserva(obj) {

        let idAlmacen = obj.dataset.idAlmacen;
        let almacenRequerimiento = obj.dataset.almacenRequerimiento;
        let stockDisponible = parseFloat(obj.dataset.stockDisponible);
        let cantidadSolicitada = parseFloat(document.querySelector("div[id='modal-nueva-reserva'] label[id='cantidad']").textContent);
        let cantidadReservada = parseFloat(document.querySelector("form[id='form-nueva-reserva'] label[name='totalReservado']").textContent);
        let cantidadAReservar = parseFloat(document.querySelector("div[id='modal-nueva-reserva'] input[name='cantidadReserva']").value);

        if (stockDisponible <= cantidadSolicitada) {
            document.querySelector("div[id='modal-nueva-reserva'] input[name='cantidadReserva']").value = stockDisponible;
        } else if (stockDisponible > cantidadSolicitada) {
            document.querySelector("div[id='modal-nueva-reserva'] input[name='cantidadReserva']").value = cantidadSolicitada;
        }

        document.querySelector("div[id='modal-nueva-reserva'] input[name='almacenReserva']").value = idAlmacen;
        document.querySelector("div[id='modal-nueva-reserva'] input[name='stockDisponible']").value = stockDisponible;
        document.querySelector("div[id='modal-nueva-reserva'] input[name='nombreAlmacenReserva']").value = almacenRequerimiento;
    }

    agregarReserva(obj) {
        let idProducto = document.querySelector("div[id='modal-nueva-reserva'] input[name='idProducto']").value;
        let mensajeValidacion = this.validarModalNuevaReserva();
        if ((mensajeValidacion.length > 0)) {
            Swal.fire({
                title: '',
                html: '<ol>' + mensajeValidacion + '</ol>',
                icon: 'warning'
            }
            );
            obj.removeAttribute("disabled");

        } else {
            let formData = new FormData($('#form-nueva-reserva')[0]);
            $.ajax({
                type: 'POST',
                url: 'guardar-reserva-almacen',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => { // Are not working with dataType:'jsonp'

                    $('#modal-nueva-reserva .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) => {
                    if (response.id_reserva > 0) {
                        $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);

                        Lobibox.notify('success', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: `${response.mensaje}`
                        });
                        obj.removeAttribute("disabled");
                        this.construirTablaAlmacenesConStockDisponible(idProducto);
                        this.listarTablaListaConReserva(response.data)
                        this.llenarTablaModalAtenderConAlmacen(document.querySelector("form[id='form-nueva-reserva'] input[name='idRequerimiento']").value);
                        if (response.estado_requerimiento.hasOwnProperty('id')) {
                            if (response.estado_requerimiento.id == 28 || response.estado_requerimiento.id == 5) {
                                trRequerimientosPendientes.remove();
                            }
                        }

                        // finalidados
                        if (response.lista_finalizados.length > 0) {
                            response.lista_finalizados.forEach(element => {
                                Swal.fire({
                                    title: '',
                                    html: `Se finalizó el cuadro de presupuesto ${element.cuadro_presupuesto.oportunidad.codigo_oportunidad} del requerimiento ${element.requerimiento.codigo}`,
                                    icon: 'info'
                                });
                            });
                        }


                    } else {
                        $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
                        // console.log(response);
                        Swal.fire(
                            '',
                            response.mensaje,
                            response.tipo_estado
                        );

                        obj.removeAttribute("disabled");
                        console.log(response);
                    }
                },
                fail: (jqXHR, textStatus, errorThrown) => {
                    $('#modal-nueva-reserva .modal-content').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar guardar la reserva, por favor vuelva a intentarlo',
                        'error'
                    );
                    obj.removeAttribute("disabled");

                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }
    }


    updateObjCantidadAAtender(indice, valor) {
        itemsParaAtenderConAlmacenList.forEach((element, index) => {
            if (index == indice) {
                itemsParaAtenderConAlmacenList[index].cantidad_a_atender = valor;
            }
        });
    }


    componerTdItemsParaCompra(data, selectCategoria, selectSubCategoria, selectClasCategoria, selectMoneda, selectUnidadMedida) {
        let htmls = '<tr></tr>';
        $('#ListaItemsParaComprar tbody').html(htmls);
        var table = document.getElementById("ListaItemsParaComprar");


        for (var a = 0; a < data.length; a++) {
            if (data[a].estado != 7) {

                var row = table.insertRow(-1);

                if (data[a].id_producto == '') {
                    row.insertCell(0).innerHTML = data[a].alm_prod_codigo ? data[a].alm_prod_codigo : '';
                    row.insertCell(1).innerHTML = `<input type="text" class="form-control" name="part_number" data-id_cc_am="${data[a].id_cc_am ? data[a].id_cc_am : ''}" data-id_cc_venta="${data[a].id_cc_venta ? data[a].id_cc_venta : ''}"  value="${data[a].part_number ? data[a].part_number : ''}" data-indice="${a}" onkeyup="updateInputPartNumberModalItemsParaCompra(event);">`;
                    row.insertCell(2).innerHTML = this.makeSelectedToSelect(a, 'categoria', selectCategoria, data[a].id_categoria, '');
                    row.insertCell(3).innerHTML = this.makeSelectedToSelect(a, 'subcategoria', selectSubCategoria, data[a].id_subcategoria, '');
                    row.insertCell(4).innerHTML = this.makeSelectedToSelect(a, 'clasificacion', selectClasCategoria, data[a].id_clasif, '');
                    row.insertCell(5).innerHTML = `<span name="descripcion">${data[a].descripcion ? data[a].descripcion : '-'}</span> `;
                    row.insertCell(6).innerHTML = this.makeSelectedToSelect(a, 'unidad_medida', selectUnidadMedida, data[a].id_unidad_medida, '');
                    row.insertCell(7).innerHTML = `<input type="text" class="form-control" name="cantidad" data-indice="${a}" onkeyup ="requerimientoPendienteView.updateInputCantidadModalItemsParaCompra(event);" value="${data[a].cantidad}">`;
                } else {
                    row.insertCell(0).innerHTML = data[a].alm_prod_codigo ? data[a].alm_prod_codigo : '';
                    row.insertCell(1).innerHTML = `<input type="text" class="form-control" name="part_number" value="${data[a].part_number ? data[a].part_number : ''}" data-indice="${a}" onkeyup="requerimientoPendienteView.updateInputPartNumberModalItemsParaCompra(event);" disabled>`;
                    row.insertCell(2).innerHTML = this.makeSelectedToSelect(a, 'categoria', selectCategoria, data[a].id_categoria, 'disabled');
                    row.insertCell(3).innerHTML = this.makeSelectedToSelect(a, 'subcategoria', selectSubCategoria, data[a].id_subcategoria, 'disabled');
                    row.insertCell(4).innerHTML = this.makeSelectedToSelect(a, 'clasificacion', selectClasCategoria, data[a].id_clasif, 'disabled');
                    row.insertCell(5).innerHTML = `<span name="descripcion">${data[a].descripcion ? data[a].descripcion : '-'}</span> `;
                    row.insertCell(6).innerHTML = this.makeSelectedToSelect(a, 'unidad_medida', selectUnidadMedida, data[a].id_unidad_medida, '');
                    row.insertCell(7).innerHTML = `<input type="text" class="form-control" name="cantidad" data-indice="${a}" onkeyup="requerimientoPendienteView.updateInputCantidadModalItemsParaCompra(event);" value="${data[a].cantidad}">`;
                }

                var tdBtnAction = row.insertCell(8);
                var btnAction = '';
                // tdBtnAction.className = classHiden;
                var hasAttrDisabled = '';
                tdBtnAction.setAttribute('width', 'auto');

                btnAction = `<div class="btn-group btn-group-sm" role="group" aria-label="Second group">`;
                if (data[a].id_producto == '') {
                    btnAction += `<button class="btn btn-success btn-sm"  name="btnGuardarItem" data-toggle="tooltip" title="Guardar en Catálogo" onClick="requerimientoPendienteView.guardarItemParaCompraEnCatalogo(this, ${a});" ${hasAttrDisabled}><i class="fas fa-save"></i></button>`;

                }
                // btnAction += `<button class="btn btn-primary btn-sm" name="btnRemplazarItem" data-toggle="tooltip" title="Remplazar" onClick="buscarRemplazarItemParaCompra(this, ${a});" ${hasAttrDisabled}><i class="fas fa-search"></i></button>`;
                btnAction += `<button class="btn btn-danger btn-sm"   name="btnEliminarItem" data-toggle="tooltip" title="Eliminar" data-id="${data[a].id}" onclick="requerimientoPendienteView.eliminarItemDeListadoParaCompra(this, ${a});" ${hasAttrDisabled} ><i class="fas fa-trash-alt"></i></button>`;
                btnAction += `</div>`;
                tdBtnAction.innerHTML = btnAction;
            }
        }
        // requerimientoPendienteCtrl.quitarItemsDetalleCuadroCostosAgregadosACompra(data);
        // requerimientoPendienteCtrl.validarObjItemsParaCompra();

    }


    updateInputCategoriaModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputCategoriaModalItemsParaCompra(event)
    }
    updateInputSubcategoriaModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputSubcategoriaModalItemsParaCompra(event);
    }
    updateInputClasificacionModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputClasificacionModalItemsParaCompra(event)
    }
    updateInputUnidadMedidaModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputUnidadMedidaModalItemsParaCompra(event);
    }

    updateInputCantidadModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputCantidadModalItemsParaCompra(event);
    }
    updateInputPartNumberModalItemsParaCompra(event) {
        this.requerimientoPendienteCtrl.updateInputPartNumberModalItemsParaCompra(event);
    }

    guardarItemParaCompraEnCatalogo(obj, indice) {
        this.requerimientoPendienteCtrl.guardarItemParaCompraEnCatalogo(obj, indice);

    }
    eliminarItemDeListadoParaCompra(obj, indice) {
        let id = obj.dataset.id;
        let tr = obj.parentNode.parentNode.parentNode;
        this.requerimientoPendienteCtrl.eliminarItemDeListadoParaCompra(indice)
        this.retornarItemAlDetalleCC(id);
        tr.remove(tr);
        this.actualizarIndicesDeTabla();


    }

    retornarItemAlDetalleCC(id) {
        var table = document.querySelector("table[id='ListaModalDetalleCuadroCostos'] tbody");
        var trs = table.querySelectorAll("tr");
        let idItemDetCCList = [];
        // console.log(trs);
        // if(trs.length ==1){
        //     if(trs[0].className=='odd'){
        //         trs[0].remove();
        //     }
        // }

        if (trs.length > 1) {
            trs.forEach(tr => {
                idItemDetCCList.push(tr.children[9].children[0].dataset.id)
            });
        }
        if (!idItemDetCCList.includes(id)) {
            tempDetalleItemsParaCompraCC.forEach(element => {
                if (element.id == id) {
                    var row = table.insertRow(-1);
                    row.style.cursor = "default";

                    row.insertCell(0).innerHTML = element.part_no ? element.part_no : '';
                    var tdDesc = row.insertCell(1)
                    tdDesc.setAttribute('width', '50%')
                    tdDesc.innerHTML = element.descripcion ? element.descripcion : '';

                    row.insertCell(2).innerHTML = element.pvu_oc ? element.pvu_oc : '';
                    row.insertCell(3).innerHTML = element.flete_oc ? element.flete_oc : '';
                    row.insertCell(4).innerHTML = element.cantidad ? element.cantidad : '';
                    row.insertCell(5).innerHTML = element.garantia ? element.garantia : '';
                    row.insertCell(6).innerHTML = element.razon_social_proveedor ? element.razon_social_proveedor : '';
                    row.insertCell(7).innerHTML = element.nombre_autor ? element.nombre_autor : '';
                    row.insertCell(8).innerHTML = element.fecha_creacion ? element.fecha_creacion : '';
                    row.insertCell(9).innerHTML = `<button class="btn btn-xs btn-default" data-id="${element.id}"
                        onclick="requerimientoPendienteCtrl.procesarItemParaCompraDetalleCuadroCostos(this,${element.id});"
                        title="Agregar Item"
                        style="background-color:#714fa7;
                        color:white;">
                        <i class="fas fa-plus"></i>
                        </button>`;

                }

            });
        }
    }

    actualizarIndicesDeTabla() {
        let trs = document.querySelector("table[id='ListaItemsParaComprar'] tbody").children;
        let i = 0;
        for (let index = 1; index < trs.length; index++) {
            trs[index].querySelector("input[name='part_number']").dataset.indice = i;
            trs[index].querySelector("select[name='categoria']").dataset.indice = i;
            trs[index].querySelector("select[name='subcategoria']").dataset.indice = i;
            trs[index].querySelector("select[name='clasificacion']").dataset.indice = i;
            trs[index].querySelector("select[name='unidad_medida']").dataset.indice = i;
            trs[index].querySelector("input[name='cantidad']").dataset.indice = i;
            i++;
        }
    }


    makeSelectedToSelect(indice, type, data, id, hasDisabled) {

        let html = '';
        switch (type) {
            case 'categoria':
                html = `<select class="form-control" name="categoria" ${hasDisabled} data-indice="${indice}" onChange="requerimientoPendienteView.updateInputCategoriaModalItemsParaCompra(event);">`;
                data.forEach(item => {
                    if (item.id_categoria == id) {
                        html += `<option value="${item.id_categoria}" selected>${item.descripcion}</option>`;
                    } else {
                        html += `<option value="${item.id_categoria}">${item.descripcion}</option>`;
                    }
                });
                html += '</select>';
                break;
            case 'subcategoria':
                html = `<select class="form-control" name="subcategoria" ${hasDisabled} data-indice="${indice}" onChange="requerimientoPendienteView.updateInputSubcategoriaModalItemsParaCompra(event);">`;
                data.forEach(item => {
                    if (item.id_subcategoria == id) {
                        html += `<option value="${item.id_subcategoria}" selected>${item.descripcion}</option>`;
                    } else {
                        html += `<option value="${item.id_subcategoria}">${item.descripcion}</option>`;
                    }
                });
                html += '</select>';
                break;
            case 'clasificacion':
                html = `<select class="form-control" name="clasificacion" ${hasDisabled} data-indice="${indice}" onChange="requerimientoPendienteView.updateInputClasificacionModalItemsParaCompra(event);">`;
                data.forEach(item => {
                    if (item.id_clasificacion == id) {
                        html += `<option value="${item.id_clasificacion}" selected>${item.descripcion}</option>`;
                    } else {
                        html += `<option value="${item.id_clasificacion}">${item.descripcion}</option>`;

                    }
                });
                html += '</select>';
                break;
            case 'unidad_medida':
                html = `<select class="form-control" name="unidad_medida" ${hasDisabled} data-indice="${indice}" onChange="requerimientoPendienteView.updateInputUnidadMedidaModalItemsParaCompra(event);">`;
                data.forEach(item => {
                    if (item.id_unidad_medida == id) {
                        html += `<option value="${item.id_unidad_medida}" selected>${item.descripcion}</option>`;
                    } else {
                        html += `<option value="${item.id_unidad_medida}">${item.descripcion}</option>`;

                    }
                });
                html += '</select>';
                break;

            default:
                break;
        }

        return html;
    }


    llenarTablaDetalleCuadroCostos(data) {
        var dataTableListaModalDetalleCuadroCostos = $('#ListaModalDetalleCuadroCostos').DataTable({
            'processing': false,
            'serverSide': false,
            'bDestroy': true,
            'bInfo': false,
            'dom': 'Bfrtip',
            'paging': false,
            'searching': false,
            'order': false,
            'columnDefs': [{
                'targets': "_all",
                'orderable': false
            }],
            'data': data,
            'columns': [
                {
                    'render': function (data, type, row) {
                        return `${row['part_no'] ? row['part_no'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['descripcion'] ? row['descripcion'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['pvu_oc'] ? row['pvu_oc'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['flete_oc'] ? row['flete_oc'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['cantidad'] ? row['cantidad'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['garantia'] ? row['garantia'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['razon_social_proveedor'] ? row['razon_social_proveedor'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['nombre_autor'] ? row['nombre_autor'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['fecha_creacion'] ? row['fecha_creacion'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `<button class="btn btn-xs btn-default"data-id="${row.id}" onclick="requerimientoPendienteCtrl.procesarItemParaCompraDetalleCuadroCostos(this,${row['id']});" title="Agregar Item" style="background-color:#714fa7; color:white;"><i class="fas fa-plus"></i></button>`;
                    }
                }
            ]
        });
        $('#ListaModalDetalleCuadroCostos thead th').off('click')
        document.querySelector("table[id='ListaModalDetalleCuadroCostos']").tHead.style.fontSize = '11px',
            document.querySelector("table[id='ListaModalDetalleCuadroCostos']").tBodies[0].style.fontSize = '11px';
        // dataTableListaModalDetalleCuadroCostos.buttons().destroy();
        document.querySelector("table[id='ListaModalDetalleCuadroCostos'] thead").style.backgroundColor = "#5d4d6d";
        $('#ListaModalDetalleCuadroCostos tr').css('cursor', 'default');

    }

    guardarItemsEnDetalleRequerimiento() {
        this.requerimientoPendienteCtrl.guardarItemsEnDetalleRequerimiento();

    }

    // agregarItemsBaseParaCompraFinalizado(response) {

    //     if (response.status == 200) {
    //         alert(response.mensaje);
    //         $('#modal-agregar-items-para-compra').modal('hide');
    //         requerimientoPendienteView.renderRequerimientoPendienteList(null, null);
    //     } else {
    //         alert(response.mensaje);
    //     }

    // }

    // totalItemsAgregadosParaCompraCompletada() {

    //     alert('Ya fueron agregados todos los items disponibles del Cuadro de Costos al Requerimiento');
    //     document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").setAttribute('disabled', true);
    //     let btnEliminarItem = document.querySelectorAll("div[id='modal-agregar-items-para-compra'] button[name='btnEliminarItem']");
    //     for (var i = 0; i < btnEliminarItem.length; i++) {
    //         btnEliminarItem[i].setAttribute('disabled', true);
    //     }

    // }
    // totalItemsAgregadosParaCompraPendiente() {

    //     document.querySelector("div[id='modal-agregar-items-para-compra'] button[id='btnIrAGuardarItemsEnDetalleRequerimiento']").removeAttribute('disabled');
    //     let btnEliminarItem = document.querySelectorAll("div[id='modal-agregar-items-para-compra'] button[name='btnEliminarItem']");
    //     for (var i = 0; i < btnEliminarItem.length; i++) {
    //         btnEliminarItem[i].removeAttribute('disabled');
    //     }

    // }


    // ver detalle cuadro de costos
    openModalCuadroCostos(obj) {
        $('#modal-ver-cuadro-costos').modal({
            show: true,
            backdrop: 'true'
        });
        this.requerimientoPendienteCtrl.openModalCuadroCostos(obj).then((res) => {
            if (res.status == 200) {
                this.llenarCabeceraModalDetalleCuadroCostos(res.head)
                this.construirTablaListaDetalleCuadroCostos(res.detalle);
            }
        }).catch(function (err) {
            console.log(err)
        })
    }

    llenarCabeceraModalDetalleCuadroCostos(data) {
        document.querySelector("div[id='modal-ver-cuadro-costos'] span[id='codigo']").textContent = data.orden_am;
    }

    construirTablaListaDetalleCuadroCostos(data) {

        var dataTablelistaModalVerCuadroCostos = $('#listaModalVerCuadroCostos').DataTable({
            'processing': false,
            'serverSide': false,
            'bDestroy': true,
            'buttons': [],
            'bInfo': false,
            'dom': 'Bfrtip',
            'paging': false,
            'searching': false,
            'data': data,
            'columns': [
                {
                    'render': function (data, type, row) {
                        return `${row['part_no'] ? row['part_no'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['descripcion']}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['pvu_oc'] > 0 ? 'S/' + row['pvu_oc'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['flete_oc'] ? row['flete_oc'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['cantidad']}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['garantia'] ? row['garantia'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['origen_costo'] ? row['origen_costo'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['razon_social_proveedor']}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        let simboloMoneda = (row.moneda_costo_unitario_proveedor == 's') ? 'S/' : (row.moneda_costo_unitario_proveedor == 'd') ? '$' : row.moneda_costo_unitario_proveedor;

                        return `${simboloMoneda}${row['costo_unitario_proveedor'] ? $.number(row['costo_unitario_proveedor'], 2) : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['plazo_proveedor'] ? row['plazo_proveedor'] : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `S/${row['flete_proveedor'] ? $.number(row['flete_proveedor'], 2) : ''}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['fondo_proveedor'] ? ('<span style="color:red">' + row['fondo_proveedor'] + ' </span>') : 'Ninguno'}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        let simboloMoneda = (row.moneda_costo_unitario_proveedor == 's') ? 'S/' : (row.moneda_costo_unitario_proveedor == 'd') ? '$' : row.moneda_costo_unitario_proveedor;

                        //    let costoUnitario = (Math.round((row.cantidad*row.costo_unitario_proveedor) * 100) / 100).toFixed(2);
                        let costoUnitario = $.number((row.cantidad * row.costo_unitario_proveedor), 2);
                        return `${simboloMoneda}${costoUnitario}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        // let costoUnitario = (Math.round((row.cantidad*row.costo_unitario_proveedor) * 100) / 100).toFixed(2);
                        let costoUnitario = row.cantidad * row.costo_unitario_proveedor;
                        let tipoCambio = row.tipo_cambio;
                        let costoUnitarioSoles = costoUnitario * tipoCambio;
                        return `S/${$.number(costoUnitarioSoles, 2)}`;
                    }
                },
                {
                    'render': function (data, type, row) {

                        // let totalFleteProveedor= (Math.round((row.cantidad*row.flete_proveedor) * 100) / 100).toFixed(2);
                        let totalFleteProveedor = $.number((row.cantidad * row.flete_proveedor), 2);
                        return `S/${(totalFleteProveedor)}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        // let simboloMoneda=( row.moneda_costo_unitario_proveedor == 's')?'S/':(row.moneda_costo_unitario_proveedor=='d')?'$':row.moneda_costo_unitario_proveedor;

                        // let totalFleteProveedor= (Math.round((row.cantidad*row.flete_proveedor) * 100) / 100).toFixed(2);
                        let totalFleteProveedor = row.cantidad * row.flete_proveedor;
                        // let costoUnitario = (Math.round((row.cantidad*row.costo_unitario_proveedor) * 100) / 100).toFixed(2);
                        let costoUnitario = row.cantidad * row.costo_unitario_proveedor;
                        let tipoCambio = row.tipo_cambio;
                        let costoUnitarioSoles = costoUnitario * tipoCambio;
                        let costoCompraMasFlete = costoUnitarioSoles + totalFleteProveedor;
                        return `S/${$.number(costoCompraMasFlete, 2)}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['nombre_autor']}`;
                    }
                },
                {
                    'render': function (data, type, row) {
                        return `${row['created_at']}`;
                    }
                }
            ],
            'columnDefs': [
                { 'aTargets': [0], 'className': "text-center" },
                { 'aTargets': [1], 'className': "text-left" },
                { 'aTargets': [2], 'className': "text-right" },
                { 'aTargets': [3], 'className': "text-right" },
                { 'aTargets': [4], 'className': "text-center" },
                { 'aTargets': [5], 'className': "text-center" },
                { 'aTargets': [6], 'className': "text-center" },
                { 'aTargets': [7], 'className': "text-center" },
                { 'aTargets': [8], 'className': "text-left" },
                { 'aTargets': [9], 'className': "text-right" },
                { 'aTargets': [10], 'className': "text-center" },
                { 'aTargets': [11], 'className': "text-right" },
                { 'aTargets': [12], 'className': "text-right" },
                { 'aTargets': [13], 'className': "text-right" },
                { 'aTargets': [14], 'className': "text-right" },
                { 'aTargets': [15], 'className': "text-right" },
                { 'aTargets': [16], 'className': "text-center" },
                { 'aTargets': [17], 'className': "text-center" }
            ],
        });

        document.querySelector("table[id='listaModalVerCuadroCostos']").tHead.style.fontSize = '11px',
            document.querySelector("table[id='listaModalVerCuadroCostos']").tBodies[0].style.fontSize = '11px';
        // dataTablelistaModalVerCuadroCostos.buttons().destroy();
        document.querySelector("table[id='listaModalVerCuadroCostos'] thead").style.backgroundColor = "#5d4d6d";
        $('#listaModalVerCuadroCostos tr').css('cursor', 'default');
    }
    updateValorNuevaCantidad(obj) {
        if (obj.value != '' || parseFloat(obj.value) > 0) {
            let cantidadOriginal = parseFloat(obj.closest('tr').querySelector("input[class~='cantidadOriginal']").value);
            let cantidadAtendidaTotal = parseFloat(obj.closest('tr').querySelector("input[class~='atencionOrden']").value) + parseFloat(obj.closest('tr').querySelector("input[class~='stockComprometido']").value)
            let maximaCantidadToleradaParaAnular = (parseFloat(obj.getAttribute("max")) - cantidadAtendidaTotal);
            if ((parseFloat(obj.value) + cantidadAtendidaTotal) <= parseFloat(obj.getAttribute("max"))) {
            } else {
                Swal.fire(
                    'Considere la cantidad atendida',
                    'La cantidad para anular ingresada (' + obj.value + ') no es valida, la "cantidad solicitada" (' + obj.getAttribute("max") + ') menos(-) la "atención total" (' + cantidadAtendidaTotal + ') da como máximo para anular ' + maximaCantidadToleradaParaAnular,
                    'warning'
                );
                obj.value = maximaCantidadToleradaParaAnular;

            }
            obj.closest('tr').querySelector("input[class~='cantidadVirtual']").value = parseFloat(cantidadOriginal - obj.value);
        }
        this.determinarNuevoEstadoPorAjuste();

    }

    gestionarEstadoRequerimiento(obj) {
        $('#modal-gestionar-estado-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
        document.querySelector("input[name='forzarActualizarEstadoRequerimiento']").value = 'NO';

        document.querySelector("div[id='modal-gestionar-estado-requerimiento'] form[id='form-gestionar-estado-requerimiento'] input[name='idRequerimiento']").value = obj.dataset.idRequerimiento;
        document.querySelector("div[id='modal-gestionar-estado-requerimiento'] span[id='codigoRequerimiento']").textContent = obj.dataset.codigoRequerimiento;
        document.querySelector("div[id='modal-gestionar-estado-requerimiento'] span[id='estadoActualRequerimiento']").textContent = obj.dataset.estadoRequerimiento;
        document.querySelector("div[id='modal-gestionar-estado-requerimiento'] span[id='estadoVirtualRequerimiento']").textContent = obj.dataset.estadoRequerimiento;

        this.requerimientoPendienteCtrl.obtenerDetalleRequerimientos(obj.dataset.idRequerimiento).then((res) => {
            this.construirTablaDetalleRequerimientoPendientesParaAjustarNecesidad(res);
        }).catch((err) => {
            console.log(err)
        })
    }

    construirTablaDetalleRequerimientoPendientesParaAjustarNecesidad(response) {
        this.requerimientoPendienteCtrl.limpiarTabla('listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada');
        let cantidadTotalItems = response.length;
        if (cantidadTotalItems > 0) {
            response.forEach(function (element) {
                if ((element.tiene_transformacion == false || element.tiene_transformacion == null || element.tiene_transformacion == '')) {
                    let stockComprometido = 0;
                    (element.reserva).forEach(reserva => {
                        if (reserva.estado != 7) {
                            stockComprometido += parseFloat(reserva.stock_comprometido);
                        }
                    });
                    let atencionOrden = 0;
                    let objOrdenList = [];
                    (element.ordenes_compra).forEach(orden => {
                        if (orden.estado != 7) {
                            atencionOrden += parseFloat(orden.cantidad);
                            objOrdenList.push({ 'id_orden': orden.id_orden_compra, 'codigo': orden.codigo });
                        }

                    });

                    // if (parseFloat(atencionOrden + stockComprometido) < (element.cantidad > 0 ? element.cantidad : 0)) { //considerar solo no atendidos menores a la cantidad solicitada
                    // <td style="border: none; text-align:center; vertical-align: middle;"><input type="checkbox" class="checkEstadoAtendidoTotal handleCheckPressMarcarItemAtendidoTotal" name="estadoAtendidoTotal[]"></td>

                    document.querySelector("tbody[id='tbody_listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                    <td style="border: none; text-align:center; vertical-align: middle;" data-part-number="${element.part_number}" data-producto-part-number="${element.producto_part_number}">${(element.producto_part_number != null ? element.producto_part_number : (element.part_number != null ? element.part_number : ''))} ${element.tiene_transformacion == true ? '<br><span class="label label-default">Transformado</span>' : ''}
                    <input type="text" name="idDetalleRequerimiento[]" value="${element.id_detalle_requerimiento}" hidden>
                    </td>
                    <td style="border: none; text-align:left; vertical-align: middle;">${element.producto_codigo != null ? element.producto_codigo : ''}</td>
                    <td style="border: none; text-align:left; vertical-align: middle;">${element.producto_codigo_softlink != null ? element.producto_codigo_softlink : ''}</td>
                    <td style="border: none; text-align:left; vertical-align: middle;">${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion ? element.descripcion : '')}</td>
                    <td style="border: none; text-align:center; vertical-align: middle;">${element.abreviatura != null ? element.abreviatura : ''}</td>
                    <td style="border: none; text-align:center; vertical-align: middle;">${element.cantidad > 0 ? element.cantidad : ''} <input type="text" class="cantidadOriginal" name="cantidadOriginal[]" value="${element.cantidad > 0 ? element.cantidad : ''}" hidden></td>
                    <td style="border: none; text-align:center; vertical-align: middle;"><input type="number" max="${element.cantidad > 0 ? element.cantidad : 0}" class="form-control cantidadParaAnular handleBlurUpdateValorNuevaCantidad" name="cantidadParaAnular[]" ${([1, 15].includes(element.estado)) ? '' : 'readOnly'} ></td>
                    <td style="border: none; text-align:center; vertical-align: middle;"><input type="number" class="form-control cantidadVirtual" name="cantidadVirtual[]" value="" readOnly> </td>
                    <td style="border: none; text-align:center; vertical-align: middle;">
                        <div class="form-group">
                            <textarea type="text" class="form-control razonesDeAjusteDeNecesidad" name="razonesDeAjusteDeNecesidad[]" placeholder="ejm: Ajuste a pedido del area usuario / ajuste por compra eficiente" style="height: 60px;overflow: scroll;width: 200px;" ${([1, 15].includes(element.estado)) ? '' : 'readOnly'}>${element.razon_ajuste_necesidad ?? ''}</textarea></td>
                        </div>
                    <td style="border: none; text-align:center; vertical-align: middle;">
                        ${stockComprometido != null && parseFloat(stockComprometido) > 0 ? stockComprometido : '0'} <input type="text" class="stockComprometido" name="stockComprometido[]" value="${stockComprometido != null && parseFloat(stockComprometido) > 0 ? stockComprometido : 0}" hidden>
                    </td>
                    <td style="border: none; text-align:center; vertical-align: middle;">
                        ${atencionOrden != null && atencionOrden > 0 ? atencionOrden : '0'} <input type="text" class="atencionOrden" name="atencionOrden[]" value="${atencionOrden != null && atencionOrden > 0 ? atencionOrden : 0}" hidden>
                    </td>
                    <td style="border: none; text-align:center; vertical-align: middle;"><span style="color:blue;" name="[]"></span> </td>
                    </tr>`);
                    // }
                }

            });
            this.autoAjustarEnAtencionTotal();
        }
    }

    autoAjustarEnAtencionTotal() {
        (document.querySelector("tbody[id='tbody_listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada']").childNodes).forEach(element => {
            let cantidadOriginal = element.querySelector("input[class~='cantidadOriginal']").value;
            let cantidadAtendidaTotal = parseFloat(element.querySelector("input[class~='atencionOrden']").value) + parseFloat(element.querySelector("input[class~='stockComprometido']").value)
            let cantidadParaAnular = cantidadOriginal - cantidadAtendidaTotal;
            if (typeof cantidadParaAnular === 'number' && Math.sign(cantidadParaAnular) === -1) {
                cantidadParaAnular = 1;
            }
            element.querySelector("input[class~='cantidadParaAnular']").setAttribute("placeholder", cantidadParaAnular);
        });

        this.determinarNuevoEstadoPorAjuste();

    }
    // controlCheckEnAtencionTotal(obj) {
    //     obj.classList.toggle("active");
    //     if (obj.classList.contains("active")) {
    //         obj.childNodes[0].classList.replace("far", "fas");
    //         obj.childNodes[0].classList.replace("fa-square", "fa-check-square");
    //         (document.querySelector("tbody[id='tbody_listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada']").childNodes).forEach(element => {
    //             // element.querySelector("input[class~='checkEstadoAtendidoTotal']").checked = true;
    //             document.querySelector("div[id='modal-gestionar-estado-requerimiento'] span[id='estadoVirtualRequerimiento']").textContent = 'Atención total';
    //             document.querySelector("div[id='modal-gestionar-estado-requerimiento'] input[name='idNuevoEstado']").value = 5;
    //         });
    //     } else {
    //         obj.childNodes[0].classList.replace("fas", "far");
    //         obj.childNodes[0].classList.replace("fa-check-square", "fa-square");
    //         (document.querySelector("tbody[id='tbody_listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada']").childNodes).forEach(element => {
    //             // element.querySelector("input[class~='checkEstadoAtendidoTotal']").checked = false;
    //             this.determinarNuevoEstadoPorAjuste();

    //         });
    //     }
    // }


    // checkPressItemAtendidoTotal() {
    //     let cantidadCheckedMarcadoAtencionTotal = 0;
    //     let cantidadTotalItem = document.querySelector("tbody[id='tbody_listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada']").childNodes.length;
    //     (document.querySelector("tbody[id='tbody_listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada']").childNodes).forEach(element => {
    //         if (element.querySelector("input[class~='checkEstadoAtendidoTotal']").checked == true) {
    //             cantidadCheckedMarcadoAtencionTotal++;
    //         }
    //     });
    //     if (cantidadTotalItem == cantidadCheckedMarcadoAtencionTotal) {
    //         document.querySelector("div[id='modal-gestionar-estado-requerimiento'] span[id='estadoVirtualRequerimiento']").textContent = 'Atención total';
    //         document.querySelector("div[id='modal-gestionar-estado-requerimiento'] input[name='idNuevoEstado']").value = 5;
    //     } else {
    //         document.querySelector("div[id='modal-gestionar-estado-requerimiento'] span[id='estadoVirtualRequerimiento']").textContent = 'Atención parcial';
    //         document.querySelector("div[id='modal-gestionar-estado-requerimiento'] input[name='idNuevoEstado']").value = 15;
    //     }
    // }



    determinarNuevoEstadoPorAjuste() {
        let cantidadTotalItem = 0;
        let cantidadConEstadoAtencionTotal = 0;
        (document.querySelector("tbody[id='tbody_listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada']").childNodes).forEach(element => {
            cantidadTotalItem++;
            let cantidadOriginal = element.querySelector("input[class~='cantidadOriginal']").value;
            let cantidadAtendidaTotal = parseFloat(element.querySelector("input[class~='atencionOrden']").value) + parseFloat(element.querySelector("input[class~='stockComprometido']").value)
            let cantidadParaAnular = parseFloat(element.querySelector("input[class~='cantidadParaAnular']").value);

            if (cantidadOriginal <= parseFloat(cantidadAtendidaTotal + cantidadParaAnular)) {
                cantidadConEstadoAtencionTotal++;
            }
        });
        if (cantidadTotalItem == cantidadConEstadoAtencionTotal) {
            document.querySelector("div[id='modal-gestionar-estado-requerimiento'] span[id='estadoVirtualRequerimiento']").textContent = 'Atención total';
            document.querySelector("div[id='modal-gestionar-estado-requerimiento'] input[name='idNuevoEstado']").value = 5;
        } else {
            document.querySelector("div[id='modal-gestionar-estado-requerimiento'] span[id='estadoVirtualRequerimiento']").textContent = 'Atención parcial';
            document.querySelector("div[id='modal-gestionar-estado-requerimiento'] input[name='idNuevoEstado']").value = 15;
        }
    }

    validarModalGestionarEstadoRequerimiento() {
        let mensajes = [];
        let estado = 'success';
        let cantidadTextAreaRazonDeAjusteSinData = 0;
        (document.querySelector("tbody[id='tbody_listaItemsRequerimientoPendientesParaAjustarCantidadSolicitada']").childNodes).forEach(element => {
            if ((element.querySelector("input[class~='cantidadParaAnular']").value > 0)) {
                if (!element.querySelector("textarea[class~='razonesDeAjusteDeNecesidad']").value != '') {
                    estado = 'warning';
                    cantidadTextAreaRazonDeAjusteSinData++;
                    element.querySelector("textarea[class~='razonesDeAjusteDeNecesidad']").closest("div").classList.add("has-error");
                } else {
                    element.querySelector("textarea[class~='razonesDeAjusteDeNecesidad']").closest("div").classList.remove("has-error");

                }
            }

        });

        if (cantidadTextAreaRazonDeAjusteSinData > 0) {
            mensajes.push(`Le falta completar ${cantidadTextAreaRazonDeAjusteSinData} campo(s) en la razon para anulación`);
        }
        return { mensajes, estado };
    }

    actualizarGestionEstadoRequerimiento() {
        let validacion = this.validarModalGestionarEstadoRequerimiento();
        if (validacion.estado == 'success') {
            let formData = new FormData($('#form-gestionar-estado-requerimiento')[0]);
            // for (var pair of formData.entries()) {
            //     console.log(pair[0]+ ', ' + pair[1]);
            // }
            $.ajax({
                type: 'POST',
                url: 'actualizar-ajuste-estado-requerimiento',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {
                    $('#modal-gestionar-estado-requerimiento .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) => {
                    console.log(response);
                    $('#modal-gestionar-estado-requerimiento .modal-content').LoadingOverlay("hide", true);
                    Lobibox.notify(response.tipo_estado, {
                        title: false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: `${response.mensaje}`
                    });

                    if (response.tipo_estado == 'success') {
                        $('#modal-gestionar-estado-requerimiento').modal('hide');
                        $tablaListaRequerimientosPendientes.ajax.reload(null, false);
                    }

                    if (response.tipo_estado == 'info') {
                        Swal.fire({
                            title: 'Desea actualizar de todas forma el estado del requerimiento?',
                            text: "No podrás revertir esto.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Si, actualizar'

                        }).then((result) => {
                            if (result.isConfirmed) {
                                // inicio
                                document.querySelector("input[name='forzarActualizarEstadoRequerimiento']").value = 'SI';
                                this.actualizarGestionEstadoRequerimiento();
                                // fin
                            }
                        })
                    }

                },
                fail: (jqXHR, textStatus, errorThrown) => {
                    $('#modal-gestionar-estado-requerimiento .modal-content').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar actualizar, por favor vuelva a intentarlo',
                        'error'
                    );
                    obj.removeAttribute("disabled");

                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        } else {
            Swal.fire(
                '',
                validacion.mensajes.toString()
                ,
                'warning'
            );
        }
    }

    // Crear orden por requerimiento
    crearOrdenCompraPorRequerimiento(obj) {
        let idx = reqTrueList.indexOf(parseInt(obj.dataset.idRequerimiento));
        if ((idx == -1)) {
            reqTrueList.push(parseInt(obj.dataset.idRequerimiento));
        }
        console.log(reqTrueList);
        sessionStorage.removeItem('idOrden');
        sessionStorage.setItem('reqCheckedList', JSON.stringify(reqTrueList));
        sessionStorage.setItem('tipoOrden', 'COMPRA');
        sessionStorage.setItem('action', 'register');
        let url = "/logistica/gestion-logistica/compras/ordenes/elaborar/index";
        var win = location.href = url;
        this.updateContadorRequerimientosPendientesSeleccionados();

    }

    solicitudCotizacionExcel(obj) {
        window.open(`solicitud-cotizacion-excel/${obj.dataset.idRequerimiento}`);

    }

    // Crear orden de servicio por requerimiento
    crearOrdenServicioPorRequerimiento(obj) {
        let idx = reqTrueList.indexOf(parseInt(obj.dataset.idRequerimiento));
        if ((idx == -1)) {
            reqTrueList.push(parseInt(obj.dataset.idRequerimiento));
        }
        console.log(reqTrueList);
        sessionStorage.removeItem('idOrden');
        sessionStorage.setItem('reqCheckedList', JSON.stringify(reqTrueList));
        sessionStorage.setItem('tipoOrden', 'SERVICIO');
        sessionStorage.setItem('action', 'register');
        let url = "/logistica/gestion-logistica/compras/ordenes/elaborar/index";
        var win = location.href = url;
        this.updateContadorRequerimientosPendientesSeleccionados();


    }

    crearOrdenCompra() {
        this.requerimientoPendienteCtrl.crearOrdenCompra();

    }


    verAdjuntoDetalleRequerimiento(obj) {

        $('#modal-adjuntos-detalle-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });

        this.listarArchivosAdjuntosDetalleRequerimiento(obj.dataset.idDetalleRequerimiento);
        document.querySelector("div[id='modal-adjuntos-detalle-requerimiento'] small[id='descripcion-item']").textContent = obj.dataset.descripcion;

    }

    actualizarTipoItem(obj) {
        Swal.fire({
            title: 'Actualizar tipo de ítem',
            text: obj.dataset.descripcion,
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Servicio',
            denyButtonColor: '#3085d6',
            denyButtonText: `Producto`,
        }).then((result) => {
            let tipoItem = '';
            if (result.isConfirmed) {
                // actualizar a servicio
                tipoItem = 2;
            } else if (result.isDenied) {
                // actualizar a producto
                tipoItem = 1;
            }

            if (tipoItem > 0) {
                $.ajax({
                    type: 'POST',
                    url: 'actualizar-tipo-item-detalle-requerimiento',
                    data: { 'idDetalleRequerimiento': obj.dataset.idDetalleRequerimiento, 'idTipoItem': tipoItem },
                    dataType: 'JSON',
                    beforeSend: (data) => {
                        $('#wrapper-okc').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) => {
                        $('#wrapper-okc').LoadingOverlay("hide", true);

                        if (response.tipo_estado == 'success') {
                            Lobibox.notify('success', {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                delay: 5000,
                                msg: response.mensaje
                            });

                            $tablaListaRequerimientosPendientes.ajax.reload(null, false);

                        } else {
                            Swal.fire(
                                '',
                                response.mensaje,
                                response.tipo_estado
                            );
                            console.log(response);
                        }


                    },
                    fail: (jqXHR, textStatus, errorThrown) => {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un problema en el servidor al intentar actualizar el tipo de item, por favor vuelva a intentarlo',
                            'error'
                        );
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }

        })
    }

    anularReservaActiva(obj) {
        let motivoDeAnulacion = '';

        Swal.fire({
            title: '¿Está seguro que desea anular la reserva?. Escriba un motivo',
            input: 'textarea',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Registrar',
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            motivoDeAnulacion = result.value;
            let formData = new FormData();
            formData.append(`idDetalleRequerimiento`, obj.dataset.idDetalleRequerimiento);
            formData.append(`motivoDeAnulacion`, motivoDeAnulacion);
            if (motivoDeAnulacion == null || (motivoDeAnulacion).trim() == '') {
                Swal.fire(
                    '',
                    'Debe ingresar un motivo para anular',
                    'info'
                );
                return false;
            }
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'anular-toda-reserva-detalle-requerimiento',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: (data) => { // Are not working with dataType:'jsonp'

                        $('#wrapper-okc').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) => {
                        console.log(response);
                        if (response.tipo_estado == 'success') {
                            $('#wrapper-okc').LoadingOverlay("hide", true);

                            Lobibox.notify('success', {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                delay: 5000,
                                msg: response.mensaje
                            });

                            $tablaListaRequerimientosAtendidos.ajax.reload(null, false);


                        } else {
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                            Swal.fire(
                                '',
                                response.mensaje,
                                response.tipo_estado
                            );
                            console.log(response);
                        }


                    },
                    fail: (jqXHR, textStatus, errorThrown) => {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un problema en el servidor al intentar anular la reserva, por favor vuelva a intentarlo',
                            'error'
                        );
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
        });
    }

    listarArchivosAdjuntosDetalleRequerimiento(idDetalleRequerimiento) {

        $.ajax({
            type: 'GET',
            url: 'mostrar-archivos-adjuntos-detalle-requerimiento/' + idDetalleRequerimiento,
            dataType: 'JSON',
        }).done((response) => {
            this.construirTablaAdjuntoDetalleRequerimiento(response);


        }).always(() => {

        }).fail((jqXHR) => {
            Swal.fire(
                '',
                'Hubo un problema al intentar mostrar los adjuntos, por favor vuelva a intentarlo.',
                'error'
            );
            console.log('Error devuelto: ' + jqXHR.responseText);
        });

    }


    construirTablaAdjuntoDetalleRequerimiento(data) {
        // console.log(data);
        $('#listaAdjuntosDetalleRequerimiento').dataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'language': vardataTables[0],
            "bDestroy": true,
            "bInfo": false,
            // 'paging': true,
            "bLengthChange": false,
            // "pageLength": 3,
            'data': data,
            'order': [[0, 'desc']],

            'columns': [

                {
                    render: function (data, type, row) {
                        return (row.archivo != null ? row.archivo : '');
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.fecha_registro != null ? row.fecha_registro : '');
                    }
                },
                {
                    render: function (data, type, row) {

                        return `<button type="button" class="btn btn-success btn-sm handleClickDescargarArchivoDetalleRequerimiento" name="btnDescargarArchivoDetalleRequerimiento" title="Descargar"  data-id-adjunto="${row.id_adjunto}" data-archivo="${row.archivo}" >Descargar</button>`;
                    }
                }
            ],

            'columnDefs': [
                { 'targets': 0, 'className': "text-left", "width": "70%" },
                { 'targets': 1, 'className': "text-left", "width": "15%" },
                { 'targets': 2, 'className': "text-center", "width": "15%" }
            ],
            'initComplete': function () {


            }
        });
    }
    construirTablaTodoAdjuntoDetalleRequerimiento(data) {
        // console.log(data);
        $('#listaTodoAdjuntosDetalleRequerimiento').dataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'language': vardataTables[0],
            "bDestroy": true,
            "bInfo": false,
            // 'paging': true,
            "bLengthChange": false,
            // "pageLength": 3,
            'data': data,
            'order': [[0, 'desc']],

            'columns': [

                {
                    render: function (data, type, row) {
                        return (row.producto != null ? row.producto.codigo : '');
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.producto != null ? row.producto.part_number : (row.detalle_requerimiento.part_number != null ? row.detalle_requerimiento.part_number : ''));
                    }
                },

                {
                    render: function (data, type, row) {
                        return (row.producto != null ? row.producto.descripcion : (row.detalle_requerimiento.descripcion != null ? row.detalle_requerimiento.descripcion : ''));
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.archivo != null ? row.archivo : '');
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.fecha_registro != null ? row.fecha_registro : '');
                    }
                },
                {
                    render: function (data, type, row) {

                        return `<button type="button" class="btn btn-success btn-sm handleClickDescargarArchivoDetalleRequerimiento" name="btnDescargarArchivoDetalleRequerimiento" title="Descargar"  data-id-adjunto="${row.id_adjunto}" data-archivo="${row.archivo}" >Descargar</button>`;
                    }
                }
            ],

            'columnDefs': [
                { 'targets': 0, 'className': "text-center", "width": "10%" },
                { 'targets': 1, 'className': "text-center", "width": "10%" },
                { 'targets': 2, 'className': "text-left", "width": "40%" },
                { 'targets': 3, 'className': "text-left", "width": "20%" },
                { 'targets': 4, 'className': "text-center", "width": "10%" },
                { 'targets': 5, 'className': "text-center", "width": "10%" }
            ],
            'initComplete': function () {


            }
        });
    }
    construirTablaAdjuntoRequerimiento(data) {
        // console.log(data);
        $('#listaAdjuntosRequerimiento').dataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'language': vardataTables[0],
            "bDestroy": true,
            "bInfo": false,
            // 'paging': true,
            "bLengthChange": false,
            // "pageLength": 3,
            'data': data,
            'order': [[0, 'desc']],

            'columns': [

                {
                    render: function (data, type, row) {
                        return (row.archivo != null ? row.archivo : '');
                    }
                },
                {
                    render: function (data, type, row) {
                        return (row.fecha_registro != null ? row.fecha_registro : '');
                    }
                },
                {
                    render: function (data, type, row) {

                        return `<button type="button" class="btn btn-success btn-sm handleClickDescargarArchivoRequerimiento" name="btnDescargarArchivoRequerimiento" title="Descargar"  data-id-adjunto="${row.id_adjunto}" data-archivo="${row.archivo}" >Descargar</button>`;
                    }
                }
            ],

            'columnDefs': [
                { 'targets': 0, 'className': "text-left", "width": "70%" },
                { 'targets': 1, 'className': "text-left", "width": "15%" },
                { 'targets': 2, 'className': "text-center", "width": "15%" }
            ],
            'initComplete': function () {


            }
        });
    }


    descargarArchivoDetalleRequerimiento(obj) {
        if (obj.dataset.idAdjunto > 0) {
            window.open("/files/necesidades/requerimientos/bienes_servicios/detalle/" + obj.dataset.archivo);

        }

    }



    // verTodoAdjuntos(obj) {

    //     $('#modal-todo-adjuntos').modal({
    //         show: true,
    //         backdrop: 'true'
    //     });

    //     document.querySelector("div[id='modal-todo-adjuntos'] span[id='codigo-requerimiento']").textContent = obj.dataset.codigo;
    //     this.listarTodoArchivosAdjuntos(obj.dataset.idRequerimiento);

    // }

    // listarTodoArchivosAdjuntos(idRequerimiento) {
    //     $.ajax({
    //         type: 'GET',
    //         url: 'mostrar-todo-adjuntos-requerimiento/' + idRequerimiento,
    //         dataType: 'JSON',
    //     }).done((response) => {
    //         // console.log(response);
    //         this.construirTablaAdjuntoRequerimiento(response.adjunto_requerimiento);
    //         this.construirTablaTodoAdjuntoDetalleRequerimiento(response.adjuntos_detalle_requerimiento);

    //     }).always(() => {

    //     }).fail((jqXHR) => {
    //         Swal.fire(
    //             '',
    //             'Hubo un problema al intentar mostrar los adjuntos, por favor vuelva a intentarlo.',
    //             'error'
    //         );
    //         console.log('Error devuelto: ' + jqXHR.responseText);
    //     });
    // }
    // descargarArchivoRequerimiento(obj) {
    //     if (obj.dataset.idAdjunto > 0) {
    //         window.open("/files/necesidades/requerimientos/bienes_servicios/cabecera/" + obj.dataset.archivo);
    //     }

    // }
    obtenerTabActivo() {
        let allTab = document.querySelector("ul[class='nav nav-tabs']").children;
        for (let index = 0; index < allTab.length; index++) {
            if (allTab[index].classList.contains("active") == true) {
                return allTab[index].classList[0];
            }
        }
    }

    observarRequerimientoLogistico(obj) {
        let payload = {
            'id_requerimiento': parseInt(obj.dataset.idRequerimiento),
            'codigo_requerimiento': (obj.dataset.codigoRequerimiento).toString(),
            // 'id_observacion_logisica':parseInt(obj.dataset.idObservacionLogistica)??0
        }

        if (payload.id_requerimiento > 0) {
            Swal.fire({
                title: `Esta seguro que desea observar el requerimiento logístico: ${payload.codigo_requerimiento}`,
                text: "No podra revertir esta acción, Se solicitará un sustento.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Observar'

            }).then((result) => {
                if (result.isConfirmed) {
                    // inicio  sustento
                    let sustentoAnularOrden = '';
                    Swal.fire({
                        title: 'Sustente el motivo de la observación',
                        input: 'textarea',
                        inputAttributes: {
                            autocapitalize: 'off',
                        },
                        inputValue: obj.dataset.observacionLogisticaSinSustento ?? '',
                        showCancelButton: true,
                        confirmButtonText: 'Registrar',

                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            payload.sustento = (result.value).toString();
                            this.guardarObservacionLogistica(payload).then((res) => {
                                if (res.estado == 'success') {
                                    Lobibox.notify('success', {
                                        title: false,
                                        size: 'mini',
                                        rounded: true,
                                        sound: false,
                                        delayIndicator: false,
                                        msg: res.mensaje
                                    });

                                    if (this.obtenerTabActivo() == 'handleClickTabRequerimientosPendientes') {
                                        this.tabRequerimientosPendientes();

                                    } else if (this.obtenerTabActivo() == 'handleClickTabRequerimientosAtendidos') {
                                        this.tabRequerimientosAtendidos();
                                    }
                                } else {
                                    Swal.fire(
                                        'Error en el servidor',
                                        res.mensaje,
                                        res.estado
                                    );
                                }

                            });
                        }
                    })
                    // fin susntento
                }
            })
        } else {
            Swal.fire(
                '',
                'Hubo un problema al intentar encontrar el ID requerimiento del documento seleccionado, actualice el listado y vuelva a intentarlo',
                'error'
            );
        }
    }

    guardarObservacionLogistica(payload) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                url: 'guardar-observacion-logistica',
                dataType: 'JSON',
                data: payload,
                beforeSend: function (data) {

                    $("[class='tab-content']").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success(response) {
                    resolve(response);
                    $("[class='tab-content']").LoadingOverlay("hide", true);

                },
                fail: function (jqXHR, textStatus, errorThrown) {
                    $("[class='tab-content']").LoadingOverlay("hide", true);
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        });
    }
}
