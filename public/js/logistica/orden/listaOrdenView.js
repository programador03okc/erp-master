var vardataTables = funcDatatables();
var cantidadFiltrosActivosCabecera = 0;
var cantidadFiltrosActivosDetalle = 0;
var tempDataProveedorParaPago = [];
class ListaOrdenView {
    constructor(listaOrdenCtrl) {
        this.listaOrdenCtrl = listaOrdenCtrl;

        this.ActualParametroTipoOrdenCabecera = 'SIN_FILTRO';
        this.ActualParametroEmpresaCabecera = 'SIN_FILTRO';
        this.ActualParametroSedeCabecera = 'SIN_FILTRO';
        this.ActualParametroFechaDesdeCabecera = 'SIN_FILTRO';
        this.ActualParametroFechaHasta = 'SIN_FILTRO';
        this.ActualParametroEstadoCabecera = 'SIN_FILTRO';

    }

    init() {
        this.vista_extendida()
        this.tipoVistaPorCabecera();
    }

    initializeEventHandler() {
        $('#listar_ordenes').on("click", "button.handleClickTipoVistaPorCabecera", () => {
            this.tipoVistaPorCabecera();
        });
        $('#modal-editar-estado-orden').on("click", "button.handleClickUpdateEstadoOrdenCompra", (e) => {
            this.updateEstadoOrdenCompra(e.currentTarget);
        });
        $('#listar_ordenes').on("click", "button.handleClickTipoVistaPorItem", () => {
            this.tipoVistaPorItem();
        });
        $('#modal-editar-estado-detalle-orden').on("click", "button.handleClickUpdateEstadoDetalleOrdenCompra", (e) => {
            this.updateEstadoDetalleOrdenCompra(e.currentTarget);
        });

        // $('#modal-ver-orden').on("click","span.handleClickEditarEstadoOrden", (e)=>{
        //     this.editarEstadoOrden(e.currentTarget);
        // });
        $('#listaOrdenes tbody').on("click", "label.handleClickAbrirOrden", (e) => {
            this.abrirOrden(e.currentTarget.dataset.idOrden);
        });
        $('#listaDetalleOrden tbody').on("click", "label.handleClickAbrirOrden", (e) => {
            this.abrirOrden(e.currentTarget.dataset.idOrden);
        });

        $('#listaOrdenes tbody').on("click", "button.handleClickAbrirOrdenPDF", (e) => {
            this.abrirOrdenPDF(e.currentTarget.dataset.idOrdenCompra);
        });
        // $('#listaOrdenes tbody').on("click", "label.handleClickAbrirRequerimiento", (e) => {

        //     // var data = $('#listaOrdenes').DataTable().row($(this).parents("tr")).data();
        //     this.abrirRequerimiento(e.currentTarget.dataset.idRequerimiento);
        // });
        $('#listaOrdenes tbody').on("click", "button.handleCliclVerDetalleOrden", (e) => {
            this.verDetalleOrden(e.currentTarget);
        });

        $('#listaOrdenes tbody').on("click", "button.handleClickAnularOrden", (e) => {
            this.anularOrden(e.currentTarget);
        });

        $('#listaOrdenes tbody').on("click", "a.handleClickObtenerArchivos", (e) => {
            this.obtenerArchivos(e.currentTarget.dataset.id, e.currentTarget.dataset.tipo);
        });
        $('#listaOrdenes').on("click", "a.handleClickEditarEstadoOrden", (e) => {
            this.editarEstadoOrden(e.currentTarget);
        });
        $('#listaOrdenes').on("click", "button.handleClickModalEnviarOrdenAPago", (e) => {
            this.modalEnviarOrdenAPago(e.currentTarget);
        });

        $('#modal-enviar-solicitud-pago').on("change", "select.handleChangeTipoDestinatario", (e) => {
            this.changeTipoDestinatario(e.currentTarget);
        });

        $('#modal-enviar-solicitud-pago').on("click", "button.handleClickEnviarSolicitudDePago", (e) => {
            this.registrarSolicitudDePago(e.currentTarget);
        });
        $('#modal-enviar-solicitud-pago').on("click", "button.handleClickInfoAdicionalCuentaSeleccionada", (e) => {
            this.mostrarInfoAdicionalCuentaSeleccionada(e.currentTarget);
        });

        $('#modal-enviar-solicitud-pago').on("blur", "input.handleBlurBuscarDestinatarioPorNumeroDocumento", (e) => {
            this.buscarDestinatarioPorNumeroDeDocumento(e.currentTarget);
        });

        $('#modal-enviar-solicitud-pago').on("focusin", "input.handleFocusInputNombreDestinatario", (e) => {
            this.focusInputNombreDestinatario(e.currentTarget);
        });
        $('#modal-enviar-solicitud-pago').on("focusout", "input.handleFocusOutInputNombreDestinatario", (e) => {
            this.focusOutInputNombreDestinatario(e.currentTarget);
        });
        $('#modal-enviar-solicitud-pago').on("keyup", "input.handleKeyUpBuscarDestinatarioPorNombre", (e) => {
            this.buscarDestinatarioPorNombre(e.currentTarget);
        });
        $('#modal-enviar-solicitud-pago').on("change", "select.handleChangeCuenta", (e) => {
            this.actualizarIdCuentaBancariaDeInput(e.currentTarget);
        });

        $('#listaDestinatariosEncontrados').on("click", "tr.handleClickSeleccionarDestinatario", (e) => {
            this.seleccionarDestinatario(e.currentTarget);
        });

        // $('#listaDetalleOrden tbody').on("click", "a.handleClickVerOrdenModal", (e) => {
        //     this.verOrdenModal(e.currentTarget);
        // });
        $('#listaDetalleOrden tbody').on("click", "a.handleClickEditarEstadoItemOrden", (e) => {
            this.editarEstadoItemOrden(e.currentTarget);
        });

        $('#listaDetalleOrden tbody').on("click", "button.handleClickAbrirOrdenPDF", (e) => {
            this.abrirOrdenPDF(e.currentTarget.dataset.idOrdenCompra);
        });
        $('#listaDetalleOrden tbody').on("click", "button.handleClickAbrirOrden", (e) => {
            this.abrirOrden(e.currentTarget.dataset.idOrdenCompra);
        });
        $('#listaDetalleOrden tbody').on("click", "button.handleClickDocumentosVinculados", (e) => {
            this.documentosVinculados(e.currentTarget);
        });

        $('#modal-filtro-lista-ordenes-elaboradas').on("change", "select.handleChangeUpdateValorFiltroOrdenesElaboradas", (e) => {
            this.updateValorFiltroOrdenesElaboradas();
        });

        $('#modal-filtro-lista-ordenes-elaboradas').on("change", "select.handleChangeFiltroEmpresa", (e) => {
            this.handleChangeFiltroEmpresa(e);
        });


        $('#modal-filtro-lista-ordenes-elaboradas').on("click", "input[type=checkbox]", (e) => {
            this.estadoCheckFiltroOrdenesElaboradasCabecera(e);
        });

        $('#modal-filtro-lista-ordenes-elaboradas').on('hidden.bs.modal', () => {
            this.updateValorFiltroOrdenesElaboradas();
            if (this.updateContadorFiltroOrdenesElaboradas() == 0) {
                this.obtenerListaOrdenesElaboradas('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');
            } else {
                this.obtenerListaOrdenesElaboradas(this.ActualParametroTipoOrdenCabecera, this.ActualParametroEmpresaCabecera, this.ActualParametroSedeCabecera, this.ActualParametroFechaDesdeCabecera, this.ActualParametroFechaHastaCabecera, this.ActualParametroEstadoCabecera);
            }
        });


        $('#modal-filtro-lista-items-orden-elaboradas').on("change", "select.handleChangeFiltroEmpresa", (e) => {
            this.handleChangeFiltroEmpresa(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on("click", "input[type=checkbox]", (e) => {
            this.estadoCheckFiltroOrdenesElaboradasDetalle(e);
        });
        $('#modal-filtro-lista-items-orden-elaboradas').on('hidden.bs.modal', () => {
            this.updateValorFiltroDetalleOrdenesElaboradas();
            if (this.updateContadorFiltroDetalleOrdenesElaboradas() == 0) {
                this.obtenerListaDetalleOrdenesElaboradas('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');

            } else {
                this.obtenerListaDetalleOrdenesElaboradas(this.ActualParametroEmpresaDetalle, this.ActualParametroSedeDetalle, this.ActualParametroFechaDesdeDetalle, this.ActualParametroFechaHastaDetalle, this.ActualParametroEstadoDetalle);

            }
        });

    }

    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        if (nodeTbody != null) {
            while (nodeTbody.children.length > 0) {
                nodeTbody.removeChild(nodeTbody.lastChild);
            }
        }
    }

    vista_extendida() {
        let body = document.getElementsByTagName('body')[0];
        body.classList.add("sidebar-collapse");
    }



    // botonera secundaria 
    tipoVistaPorCabecera() {
        document.querySelector("button[id='btnTipoVistaPorCabecera'").classList.add('active');
        document.querySelector("button[id='btnTipoVistaPorItemPara'").classList.remove('active');
        document.querySelector("div[id='contenedor-tabla-nivel-cabecera']").classList.remove('oculto');
        document.querySelector("div[id='contenedor-tabla-nivel-item']").classList.add('oculto');
        if (this.updateContadorFiltroOrdenesElaboradas() == 0) {
            this.obtenerListaOrdenesElaboradas('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');

        }
    }
    tipoVistaPorItem() {
        document.querySelector("button[id='btnTipoVistaPorItemPara'").classList.add('active');
        document.querySelector("button[id='btnTipoVistaPorCabecera'").classList.remove('active');
        document.querySelector("div[id='contenedor-tabla-nivel-cabecera']").classList.add('oculto');
        document.querySelector("div[id='contenedor-tabla-nivel-item']").classList.remove('oculto');
        if (this.updateContadorFiltroDetalleOrdenesElaboradas() == 0) {
            this.obtenerListaDetalleOrdenesElaboradas('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');
        }
    }

    // filtros


    exportTableToExcel() {
        window.open('listar-ordenes-excel');
    }

    filtroTablaListaOrdenesVistaCabecera() {
        $('#modal-filtro-lista-ordenes-elaboradas').modal({
            show: true,
            backdrop: 'true'
        });
    }

    filtroTablaListaOrdenesVistaDetalle() {
        $('#modal-filtro-lista-items-orden-elaboradas').modal({
            show: true,
            backdrop: 'true'
        });
    }

    getNameModalActive() {

        if (document.querySelector("div[id='modal-filtro-lista-items-orden-elaboradas']").classList.contains("in") == true) {
            return document.querySelector("div[id='modal-filtro-lista-items-orden-elaboradas'] div.modal-body").firstElementChild.getAttribute('id');
        } else if (document.querySelector("div[id='modal-filtro-lista-ordenes-elaboradas']").classList.contains("in") == true) {
            return document.querySelector("div[id='modal-filtro-lista-ordenes-elaboradas'] div.modal-body").firstElementChild.getAttribute('id');
        } else {
            return null;
        }

    }



    handleChangeFiltroEmpresa(event) {
        let id_empresa = event.target.value;
        this.listaOrdenCtrl.getDataSelectSede(id_empresa).then((res) => {
            this.llenarSelectSede(res);
        }).catch(function (err) {
            console.log(err)
        })

    }

    llenarSelectSede(array) {
        let selectElement = document.querySelector("div[id='" + this.getNameModalActive() + "'] select[name='sede']");

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

    mostrarCantidadFiltrosActivosCabeceraOrden() {
        document.querySelector("button[id='btnFiltroListaOrdenCabecera'] span[id='cantidadFiltrosActivosCabecera']").textContent = cantidadFiltrosActivosCabecera;

    }
    mostrarCantidadFiltrosActivosDetalleOrden() {
        document.querySelector("button[id='btnFiltroListaOrdenDetalle'] span[id='cantidadFiltrosActivosDetalle']").textContent = cantidadFiltrosActivosDetalle;

    }

    estadoCheckFiltroOrdenesElaboradasCabecera(e) {
        const modalFiltrosOrdenesElaboradas = document.querySelector("div[id='modal-filtro-lista-ordenes-elaboradas']");
        switch (e.currentTarget.getAttribute('name')) {
            case 'chkTipoOrden':
                if (e.currentTarget.checked == true) {
                    modalFiltrosOrdenesElaboradas.querySelector("select[name='tipoOrden']").removeAttribute("readOnly")
                } else {
                    modalFiltrosOrdenesElaboradas.querySelector("select[name='tipoOrden']").setAttribute("readOnly", true)
                }
                break;
            case 'chkEmpresa':
                if (e.currentTarget.checked == true) {
                    modalFiltrosOrdenesElaboradas.querySelector("select[name='empresa']").removeAttribute("readOnly")
                } else {
                    modalFiltrosOrdenesElaboradas.querySelector("select[name='empresa']").setAttribute("readOnly", true)
                }
                break;
            case 'chkSede':
                if (e.currentTarget.checked == true) {
                    modalFiltrosOrdenesElaboradas.querySelector("select[name='sede']").removeAttribute("readOnly")
                } else {
                    modalFiltrosOrdenesElaboradas.querySelector("select[name='sede']").setAttribute("readOnly", true)
                }
                break;
            case 'chkFechaRegistro':
                if (e.currentTarget.checked == true) {
                    modalFiltrosOrdenesElaboradas.querySelector("input[name='fechaRegistroDesde']").removeAttribute("readOnly")
                    modalFiltrosOrdenesElaboradas.querySelector("input[name='fechaRegistroHasta']").removeAttribute("readOnly")
                } else {
                    modalFiltrosOrdenesElaboradas.querySelector("input[name='fechaRegistroDesde']").setAttribute("readOnly", true)
                    modalFiltrosOrdenesElaboradas.querySelector("input[name='fechaRegistroHasta']").setAttribute("readOnly", true)
                }
                break;
            case 'chkEstado':
                if (e.currentTarget.checked == true) {
                    modalFiltrosOrdenesElaboradas.querySelector("select[name='estado']").removeAttribute("readOnly")
                } else {
                    modalFiltrosOrdenesElaboradas.querySelector("select[name='estado']").setAttribute("readOnly", true)
                }
                break;
            default:
                break;
        }

    }

    estadoCheckFiltroOrdenesElaboradasDetalle(e) {
        const modalFiltrosDetalleOrdenesElaboradas = document.querySelector("div[id='modal-filtro-lista-items-orden-elaboradas']");
        switch (e.currentTarget.getAttribute('name')) {
            case 'chkEmpresa':
                if (e.currentTarget.checked == true) {
                    modalFiltrosDetalleOrdenesElaboradas.querySelector("select[name='empresa']").removeAttribute("readOnly")
                } else {
                    modalFiltrosDetalleOrdenesElaboradas.querySelector("select[name='empresa']").setAttribute("readOnly", true)
                }
                break;
            case 'chkSede':
                if (e.currentTarget.checked == true) {
                    modalFiltrosDetalleOrdenesElaboradas.querySelector("select[name='sede']").removeAttribute("readOnly")
                } else {
                    modalFiltrosDetalleOrdenesElaboradas.querySelector("select[name='sede']").setAttribute("readOnly", true)
                }
                break;
            case 'chkFechaRegistro':
                if (e.currentTarget.checked == true) {
                    modalFiltrosDetalleOrdenesElaboradas.querySelector("input[name='fechaRegistroDesde']").removeAttribute("readOnly")
                    modalFiltrosDetalleOrdenesElaboradas.querySelector("input[name='fechaRegistroHasta']").removeAttribute("readOnly")
                } else {
                    modalFiltrosDetalleOrdenesElaboradas.querySelector("input[name='fechaRegistroDesde']").setAttribute("readOnly", true)
                    modalFiltrosDetalleOrdenesElaboradas.querySelector("input[name='fechaRegistroHasta']").setAttribute("readOnly", true)
                }
                break;
            case 'chkEstado':
                if (e.currentTarget.checked == true) {
                    modalFiltrosDetalleOrdenesElaboradas.querySelector("select[name='estado']").removeAttribute("readOnly")
                } else {
                    modalFiltrosDetalleOrdenesElaboradas.querySelector("select[name='estado']").setAttribute("readOnly", true)
                }
                break;
            default:
                break;
        }

    }


    updateValorFiltroOrdenesElaboradas() {
        const modalFiltroListaOrdenesElaboradas = document.querySelector("div[id='modal-filtro-lista-ordenes-elaboradas']");
        if (modalFiltroListaOrdenesElaboradas.querySelector("select[name='tipoOrden']").getAttribute("readonly") == null) {
            this.ActualParametroTipoOrdenCabecera = modalFiltroListaOrdenesElaboradas.querySelector("select[name='tipoOrden']").value;
        }
        if (modalFiltroListaOrdenesElaboradas.querySelector("select[name='empresa']").getAttribute("readonly") == null) {
            this.ActualParametroEmpresaCabecera = modalFiltroListaOrdenesElaboradas.querySelector("select[name='empresa']").value;
        }
        if (modalFiltroListaOrdenesElaboradas.querySelector("select[name='sede']").getAttribute("readonly") == null) {
            this.ActualParametroSedeCabecera = modalFiltroListaOrdenesElaboradas.querySelector("select[name='sede']").value;
        }
        if (modalFiltroListaOrdenesElaboradas.querySelector("input[name='fechaRegistroDesde']").getAttribute("readonly") == null) {
            this.ActualParametroFechaDesdeCabecera = modalFiltroListaOrdenesElaboradas.querySelector("input[name='fechaRegistroDesde']").value.length > 0 ? modalFiltroListaOrdenesElaboradas.querySelector("input[name='fechaRegistroDesde']").value : 'SIN_FILTRO';
        }
        if (modalFiltroListaOrdenesElaboradas.querySelector("input[name='fechaRegistroHasta']").getAttribute("readonly") == null) {
            this.ActualParametroFechaHastaCabecera = modalFiltroListaOrdenesElaboradas.querySelector("input[name='fechaRegistroHasta']").value.length > 0 ? modalFiltroListaOrdenesElaboradas.querySelector("input[name='fechaRegistroHasta']").value : 'SIN_FILTRO';
        }
        if (modalFiltroListaOrdenesElaboradas.querySelector("select[name='estado']").getAttribute("readonly") == null) {
            this.ActualParametroEstadoCabecera = modalFiltroListaOrdenesElaboradas.querySelector("select[name='estado']").value;
        }
    }
    updateValorFiltroDetalleOrdenesElaboradas() {
        const modalFiltroListaDetalleOrdenesElaboradas = document.querySelector("div[id='modal-filtro-lista-items-orden-elaboradas']");

        if (modalFiltroListaDetalleOrdenesElaboradas.querySelector("select[name='empresa']").getAttribute("readonly") == null) {
            this.ActualParametroEmpresaDetalle = modalFiltroListaDetalleOrdenesElaboradas.querySelector("select[name='empresa']").value;
        }
        if (modalFiltroListaDetalleOrdenesElaboradas.querySelector("select[name='sede']").getAttribute("readonly") == null) {
            this.ActualParametroSedeDetalle = modalFiltroListaDetalleOrdenesElaboradas.querySelector("select[name='sede']").value;
        }
        if (modalFiltroListaDetalleOrdenesElaboradas.querySelector("input[name='fechaRegistroDesde']").getAttribute("readonly") == null) {
            this.ActualParametroFechaDesdeDetalle = modalFiltroListaDetalleOrdenesElaboradas.querySelector("input[name='fechaRegistroDesde']").value.length > 0 ? modalFiltroListaDetalleOrdenesElaboradas.querySelector("input[name='fechaRegistroDesde']").value : 'SIN_FILTRO';
        }
        if (modalFiltroListaDetalleOrdenesElaboradas.querySelector("input[name='fechaRegistroHasta']").getAttribute("readonly") == null) {
            this.ActualParametroFechaHastaDetalle = modalFiltroListaDetalleOrdenesElaboradas.querySelector("input[name='fechaRegistroHasta']").value.length > 0 ? modalFiltroListaDetalleOrdenesElaboradas.querySelector("input[name='fechaRegistroHasta']").value : 'SIN_FILTRO';
        }
        if (modalFiltroListaDetalleOrdenesElaboradas.querySelector("select[name='estado']").getAttribute("readonly") == null) {
            this.ActualParametroEstadoDetalle = modalFiltroListaDetalleOrdenesElaboradas.querySelector("select[name='estado']").value;
        }
    }

    obtenerListaOrdenesElaboradas(tipoOrden = 'SIN_FILTRO', idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', fechaRegistroDesde = 'SIN_FILTRO', fechaRegistroHasta = 'SIN_FILTRO', idEstado = 'SIN_FILTRO') {
        this.listaOrdenCtrl.obtenerListaOrdenesElaboradas(tipoOrden, idEmpresa, idSede, fechaRegistroDesde, fechaRegistroHasta, idEstado).then((res) => {
            this.construirTablaListaOrdenesElaboradas(res);
        }).catch((err) => {
            console.log(err)
        })
    }



    construirTablaListaOrdenesElaboradas(data) {
        // console.log(data);
        let that = this;
        tablaListaOrdenes = $('#listaOrdenes').DataTable({
            'processing': true,
            'destroy': true,
            'stateSave': true,
            'language': vardataTables[0],
            'buttons': [
                {
                    text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : 0',
                    attr: {
                        id: 'btnFiltroListaOrdenCabecera'
                    },
                    action: () => {
                        this.filtroTablaListaOrdenesVistaCabecera();

                    },
                    className: 'btn-default btn-sm'
                },
                {
                    text: '<span class="far fa-file-excel" aria-hidden="true"></span> Descargar',
                    attr: {
                        id: 'btnExportarAExcel'
                    },
                    action: () => {
                        this.exportTableToExcel();

                    },
                    className: 'btn-default btn-sm'
                }
            ],
            'data': data,
            "order": [[0, "desc"]],

            // "dataSrc":'',
            'dom': 'Bfrtip',
            'scrollX': false,
            'columns': [
                { 'data': 'id_orden_compra' },
                {
                    'render':
                        function (data, type, row, meta) {
                            return `${(row.codigo_oportunidad ?? '')}`;
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return (row.razon_social + ' - RUC:' + row.nro_documento)
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return '<label class="lbl-codigo handleClickAbrirOrden" title="Ir a orden" data-id-orden="' + row.id_orden_compra + '">' + (row.codigo ?? '') + '</label>';
                        }
                },
                {
                    'render': function (data, type, row) {
                        let labelRequerimiento = '';
                        if (row.requerimientos != undefined && row.requerimientos.length > 0) {
                            (row.requerimientos).forEach(element => {
                                labelRequerimiento += `${(element.estado == 38 || element.estado == 39) ? `<i class="fas fa-exclamation-triangle orange" title="${element.estado == 38 ? 'Por regularizar' : (element.estado == 38 ? 'En pausa' : '')}" data-id-requerimiento="${element.id_requerimiento}"></i>` : ''} <a href="/necesidades/requerimiento/elaboracion/index?id=${element.id_requerimiento}" target="_blank" title="Abrir Requerimiento">${element.codigo ?? ''}</a>`;


                            });
                            return labelRequerimiento;

                        } else {
                            return '';
                        }

                    }
                },

                {
                    'render':
                        function (data, type, row, meta) {
                            let cantidadRequerimientosConEstadosPorRegularizarOenPausa = 0;
                            let estadoDetalleOrdenHabilitadasActualizar = [1, 2, 3, 4, 5, 6, 15];


                            if (row.requerimientos != undefined && row.requerimientos.length > 0) {
                                (row.requerimientos).forEach(element => {
                                    if (element.estado == 38 || element.estado == 39) {
                                        cantidadRequerimientosConEstadosPorRegularizarOenPausa++;
                                    }
                                });
                            }
                            if (cantidadRequerimientosConEstadosPorRegularizarOenPausa > 0) {
                                return `<center><span class="label label-default" data-id-estado-orden-compra="${row.estado}" data-codigo-orden="${row.codigo}" data-id-orden-compra="${row.id_orden_compra}" >${row.estado_doc}</span></center>`;


                            } else {
                                if (estadoDetalleOrdenHabilitadasActualizar.includes(row.estado) == true) {

                                    return `<center><a class="handleClickEditarEstadoOrden" data-id-estado-orden-compra="${row.estado}" data-codigo-orden="${row.codigo}" data-id-orden-compra="${row.id_orden_compra}" style="cursor:pointer;">${row.estado_doc}</a></center>`;
                                } else {
                                    return `<center><span class="label label-default" data-id-estado-orden-compra="${row.estado}" data-codigo-orden="${row.codigo}" data-id-orden-compra="${row.id_orden_compra}" >${row.estado_doc}</span></center>`;

                                }
                            }
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return `${(row.fecha_vencimiento_ocam ?? '')}`;
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return `${(row.fecha_ingreso_almacen ?? '')}`;
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return `${(row.estado_aprobacion_cc ?? '')}`;
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return `${(row.fecha_estado ?? '')}`;
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            return `${(row.fecha_registro_requerimiento ?? '')}`;
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            let output = 'No aplica';
                            if (row.id_tp_documento == 2) { // orden de compra

                                let estimatedTimeOfArrive = moment(row['fecha_formato'], 'DD-MM-YYYY').add(row['plazo_entrega'], 'days').format('DD-MM-YYYY');
                                let sumaFechaConPlazo = moment(row['fecha_formato'], "DD-MM-YYYY").add(row['plazo_entrega'], 'days').format("DD-MM-YYYY").toString();
                                let fechaActual = moment().format('DD-MM-YYYY').toString();
                                let dias_restantes = moment(sumaFechaConPlazo, 'DD-MM-YYYY').diff(moment(fechaActual, 'DD-MM-YYYY'), 'days');
                                let porc = dias_restantes * 100 / (parseFloat(row['plazo_entrega'])).toFixed(2);
                                let color = (porc > 50 ? 'success' : ((porc <= 50 && porc > 20) ? 'warning' : 'danger'));
                                output = `<div class="progress-group">
                            <span class="progress-text">${estimatedTimeOfArrive} <br> Nro días Restantes</span>
                            <span class="float-right"><b>${dias_restantes > 0 ? dias_restantes : '0'}</b></span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-${color}" style="width: ${(porc < 1) ? '100' : porc}%"></div>
                            </div>
                        </div>`;

                            }
                            return output;
                        }
                },
                { 'data': 'descripcion_sede_empresa' },
                { 'data': 'condicion' },
                { 'data': 'fecha' },
                {
                    'render':
                        function (data, type, row, meta) {
                            let fechaOrden = moment(row.fecha);
                            let fechaRequerimiento = moment(row.fecha_registro_requerimiento);
                            let tiempoAtencionLogistica = fechaOrden.diff((fechaRequerimiento), 'days');
                            return `${tiempoAtencionLogistica > 0 ? (tiempoAtencionLogistica + ' días') : '0 días'} `;
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            let fechaIngresoAlmacen = moment(row.fecha_ingreso_almacen);
                            let fechaOrden = moment(row.fecha);
                            let tiempoAtencionProveedor = fechaOrden.diff((fechaIngresoAlmacen), 'days');
                            if (row.fecha_ingreso_almacen != null) {
                                return `${tiempoAtencionProveedor > 0 ? (tiempoAtencionProveedor + ' días') : '0 días'}`;
                            } else {
                                return '';
                            }
                        }
                },
                { 'data': 'facturas' },
                {
                    'render':
                        function (data, type, row, meta) {
                            return row.monto_total_presup > 0 ? (parseFloat(row.monto_total_presup)).toFixed(2) : '(No aplica)';

                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {
                            let total = 0;
                            if (row.id_moneda == 2) {
                                if (parseFloat(row.tipo_cambio_compra) > 0) {
                                    total = '<span title="$' + row.monto_total_orden + '">' + "S/" + ($.number((row.monto_total_orden * row.tipo_cambio_compra), 2)) + '</span>';
                                } else {
                                    total = (row.moneda_simbolo + (($.number(row.monto_total_orden, 2))));

                                }
                            } else {
                                total = (row.moneda_simbolo + (($.number(row.monto_total_orden, 2))));

                            }
                            return total;
                        }
                },
                {
                    'render':
                        function (data, type, row, meta) {

                            let cantidadRequerimientosConEstadosPorRegularizarOenPausa = 0;
                            if (row.requerimientos != undefined && row.requerimientos.length > 0) {
                                (row.requerimientos).forEach(element => {
                                    if (element.estado == 38 || element.estado == 39) {
                                        cantidadRequerimientosConEstadosPorRegularizarOenPausa++;
                                    }
                                });
                            }

                            let containerOpenBrackets = '<div class="btn-group btn-group-xs" role="group" style="margin-bottom: 5px;display: flex;flex-direction: row;flex-wrap: nowrap;">';
                            let btnImprimirOrden = '<button type="button" class="btn btn-sm btn-warning boton handleClickAbrirOrdenPDF" title="Abrir orden PDF"  data-toggle="tooltip" data-placement="bottom" data-id-orden-compra="' + row.id_orden_compra + '"  data-id-pago=""> <i class="fas fa-file-pdf"></i> </button>';

                            let btnAnularOrden = '';
                            if (![6, 27, 28].includes(row.estado)) {
                                if (cantidadRequerimientosConEstadosPorRegularizarOenPausa > 0) {
                                    btnAnularOrden = '<button type="button" class="btn btn-sm btn-danger boton handleClickAnularOrden" name="btnAnularOrden" title="Anular orden" data-codigo-orden="' + row.codigo + '" data-id-orden-compra="' + row.id_orden_compra + '" disabled ><i class="fas fa-backspace fa-xs"></i></button>';
                                } else {
                                    btnAnularOrden = '<button type="button" class="btn btn-sm btn-danger boton handleClickAnularOrden" name="btnAnularOrden" title="Anular orden" data-codigo-orden="' + row.codigo + '" data-id-orden-compra="' + row.id_orden_compra + '"><i class="fas fa-backspace fa-xs"></i></button>';

                                }
                            }
                            let btnVerDetalle = `<button type="button" class="ver-detalle btn btn-sm btn-primary boton handleCliclVerDetalleOrden" data-toggle="tooltip" data-placement="bottom" title="Ver Detalle" data-id="${row.id_orden_compra}">
                        <i class="fas fa-chevron-down"></i>
                        </button>`;

                            let btnEnviarAPago = '';
                            if (row.id_condicion == 1) {
                                btnEnviarAPago = `<button type="button" class="btn btn-sm btn-${([5,6,8].includes((row.estado_pago)) ? 'success' : 'info')} boton handleClickModalEnviarOrdenAPago" name="btnEnviarOrdenAPago" title="${([5,6,8].includes((row.estado_pago)) ? 'Ya se envió a pago' : 'Enviar a pago?')}" 
                                data-id-orden-compra="${row.id_orden_compra ?? ''}" 
                                data-codigo-orden="${row.codigo ?? ''}" 
                                data-id-proveedor="${row.id_proveedor ?? ''}" 
                                data-id-cuenta-principal="${row.id_cta_principal ?? ''}"
                                data-estado-pago="${row.estado_pago ?? ''}"
                                data-id-prioridad-pago="${row.id_prioridad_pago ?? ''}"
                                data-id-tipo-destinatario-pago="${row.id_tipo_destinatario_pago ?? ''}"
                                data-id-cuenta-contribuyente-pago="${row.id_cta_principal ?? ''}"
                                data-id-contribuyente-pago="${row.id_contribuyente ?? ''}"

                                data-id-persona-pago="${row.id_persona_pago ?? ''}"
                                data-id-cuenta-persona-pago="${row.id_cuenta_persona_pago ?? ''}"
                                data-comentario-pago="${row.comentario_pago ?? ''}" >
                                    <i class="fas fa-money-check-alt fa-xs"></i>
                                </button>`;

                            }

                            let containerCloseBrackets = '</div>';

                            return (containerOpenBrackets + btnVerDetalle + btnImprimirOrden + btnEnviarAPago + btnAnularOrden + containerCloseBrackets);

                        }
                }

            ],
            'columnDefs': [
                { 'aTargets': [0], 'visible': false, 'searchable': false },
                { 'aTargets': [1], 'className': "text-center" },
                { 'aTargets': [3], 'className': "text-center" },
                { 'aTargets': [4], 'className': "text-center" },
                { 'aTargets': [5], 'className': "text-center" },
                { 'aTargets': [6], 'className': "text-center" },
                { 'aTargets': [7], 'className': "text-center" },
                { 'aTargets': [8], 'className': "text-center" },
                { 'aTargets': [9], 'className': "text-center" },
                { 'aTargets': [10], 'className': "text-center" },
                { 'aTargets': [12], 'className': "text-center" },
                { 'aTargets': [13], 'className': "text-center" },
                { 'aTargets': [14], 'className': "text-center" },
                { 'aTargets': [15], 'className': "text-center" },
                { 'aTargets': [16], 'className': "text-center" },
                { 'aTargets': [17], 'className': "text-right" },
                { 'aTargets': [18], 'className': "text-right" },
                { 'aTargets': [19], 'className': "text-right" },
                { 'aTargets': [20], 'className': "text-center" }
            ]
            , "initComplete": function () {

                that.updateContadorFiltroOrdenesElaboradas();

            },
            "createdRow": function (row, data, dataIndex) {

                $(row.childNodes[14]).css('background-color', '#b4effd');
                $(row.childNodes[14]).css('font-weight', 'bold');
                $(row.childNodes[15]).css('background-color', '#b4effd');
                $(row.childNodes[15]).css('font-weight', 'bold');


            }
        });


    }


    updateContadorFiltroOrdenesElaboradas() {
        let contadorCheckActivo = 0;
        const allCheckBoxFiltroOrdenesElaboradasNivelCabecera = document.querySelectorAll("div[id='modal-filtro-lista-ordenes-elaboradas'] input[type='checkbox']");
        allCheckBoxFiltroOrdenesElaboradasNivelCabecera.forEach(element => {
            if (element.checked == true) {
                contadorCheckActivo++;
            }
        });
        document.querySelector("button[id='btnFiltroListaOrdenCabecera'] span") ? document.querySelector("button[id='btnFiltroListaOrdenCabecera'] span").innerHTML = '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : ' + contadorCheckActivo : false
        return contadorCheckActivo;
    }

    updateContadorFiltroDetalleOrdenesElaboradas() {
        let contadorCheckActivo = 0;
        const allCheckBoxFiltroOrdenesElaboradasNivelDetalle = document.querySelectorAll("div[id='modal-filtro-lista-items-orden-elaboradas'] input[type='checkbox']");
        allCheckBoxFiltroOrdenesElaboradasNivelDetalle.forEach(element => {
            if (element.checked == true) {
                contadorCheckActivo++;
            }
        });
        document.querySelector("button[id='btnFiltroListaOrdenDetalle'] span") ? document.querySelector("button[id='btnFiltroListaOrdenDetalle'] span").innerHTML = '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : ' + contadorCheckActivo : false
        return contadorCheckActivo;
    }

    construirDetalleOrdenElaboradas(table_id, row, response) {
        var html = '';
        if (response.length > 0) {
            response.forEach(function (element) {
                let stock_comprometido = 0;
                (element.reserva).forEach(reserva => {
                    if (reserva.estado == 1) {
                        stock_comprometido += parseFloat(reserva.stock_comprometido);
                    }
                });

                html += `<tr>
                    <td style="border: none;">${(element.nro_orden !== null ? `<a  style="cursor:pointer;" class="handleClickObtenerArchivos" data-id="${element.id_oc_propia}" data-tipo="${element.tipo_oc_propia}">${element.nro_orden}</a>` : '')}</td>
                    <td style="border: none;">${element.codigo_oportunidad !== null ? element.codigo_oportunidad : ''}</td>
                    <td style="border: none;">${element.nombre_entidad !== null ? element.nombre_entidad : ''}</td>
                    <td style="border: none;">${element.nombre_corto_responsable !== null ? element.nombre_corto_responsable : ''}</td>
                    <td style="border: none;"><a href="/necesidades/requerimiento/elaboracion/index?id=${element.id_requerimiento}" target="_blank" title="Abrir Requerimiento">${element.codigo_req ?? ''}</a></td>
                    <td style="border: none;">${element.codigo ?? ''}</td>
                    <td style="border: none;">${element.part_number ?? ''}</td>
                    <td style="border: none;">${element.descripcion ? element.descripcion : (element.descripcion_adicional ? element.descripcion_adicional : '')}</td>
                    <td style="border: none;">${element.cantidad ? element.cantidad : ''}</td>
                    <td style="border: none;">${element.abreviatura ? element.abreviatura : ''}</td>
                    <td style="border: none;">${element.moneda_simbolo}${$.number(element.precio, 2)}</td>
                    <td style="border: none;">${element.moneda_simbolo}${$.number((element.cantidad * element.precio), 2)}</td>
                    <td style="border: none; text-align:center;">${stock_comprometido != null ? stock_comprometido : ''}</td>

                    </tr>`;
            });
            var tabla = `<table class="table table-sm" style="border: none; font-size:x-small;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">O/C</th>
                        <th style="border: none;">Cod.CDP</th>
                        <th style="border: none;">Cliente</th>
                        <th style="border: none;">Responsable</th>
                        <th style="border: none;">Cod.Req.</th>
                        <th style="border: none;">Código</th>
                        <th style="border: none;">Part number</th>
                        <th style="border: none;">Descripción</th>
                        <th style="border: none;">Cantidad</th>
                        <th style="border: none;">Und.Med</th>
                        <th style="border: none;">Prec.Unit.</th>
                        <th style="border: none;">Total</th>
                        <th style="border: none;">Reserva almacén</th>
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

    obtenerArchivos(id, tipo) {
        obtenerArchivosMgcp(id, tipo);

    }

    abrirRequerimientoPDF(idRequerimiento) {
        let url = `/necesidades/requerimiento/elaboracion/imprimir-requerimiento-pdf/${idRequerimiento}/0`;
        var win = window.open(url, "_blank");
        win.focus();
    }
    abrirRequerimiento(idRequerimiento) {
        localStorage.setItem('idRequerimiento', idRequerimiento);
        let url = "/necesidades/requerimiento/elaboracion/index";
        var win = window.open(url, "_blank");
        win.focus();
    }

    abrirOrden(idOrden) {
        sessionStorage.removeItem('reqCheckedList');
        sessionStorage.removeItem('tipoOrden');
        sessionStorage.setItem("idOrden", idOrden);
        sessionStorage.setItem("action", 'historial');

        let url = "/logistica/gestion-logistica/compras/ordenes/elaborar/index";
        var win = window.open(url, '_blank');
        win.focus();
    }

    abrirOrdenPDF(idOrden) {
        let url = `/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/${idOrden}`;
        var win = window.open(url, "_blank");
        win.focus();
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
            this.buildFormat(obj, iTableCounter, id, row);
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


    buildFormat(obj, table_id, id, row) {
        obj.setAttribute('disabled', true);
        this.listaOrdenCtrl.obtenerDetalleOrdenElaboradas(id).then((res) => {
            console.log(res);
            obj.removeAttribute('disabled');
            this.construirDetalleOrdenElaboradas(table_id, row, res);
        }).catch((err) => {
            console.log(err)
        })
    }

    // vista nivel de items

    obtenerListaDetalleOrdenesElaboradas(idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', fechaRegistroDesde = 'SIN_FILTRO', fechaRegistroHasta = 'SIN_FILTRO', idEstado = 'SIN_FILTRO') {
        this.listaOrdenCtrl.obtenerListaDetalleOrdenesElaboradas(idEmpresa, idSede, fechaRegistroDesde, fechaRegistroHasta, idEstado).then((res) => {
            this.construirTablaListaDetalleOrdenesElaboradas(res);
        }).catch((err) => {
            console.log(err)
        })
    }

    construirTablaListaDetalleOrdenesElaboradas(data) {
        let that = this;
        $('#listaDetalleOrden').DataTable({
            'processing': true,
            'destroy': true,
            'language': vardataTables[0],
            'dom': 'Bfrtip',
            'buttons': [
                {
                    text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : 0',
                    attr: {
                        id: 'btnFiltroListaOrdenDetalle'
                    },
                    action: () => {
                        this.filtroTablaListaOrdenesVistaDetalle();

                    },
                    className: 'btn-default btn-sm'
                },
            ],
            'scrollX': false,
            'order': [13, 'desc'],
            'data': data,
            'columns': [
                {
                    render: function (data, type, row) {
                        return `<label class="lbl-codigo handleClickAbrirOrden"  data-id-orden="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}"  style="cursor: pointer;" title="Ver Orden">${row.codigo}</label>`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.codigo_requerimiento ? row.codigo_requerimiento : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.codigo_softlink ? row.codigo_softlink : ''}`;

                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.concepto ? row.concepto : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.razon_social_cliente ? row.razon_social_cliente : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.razon_social ? row.razon_social : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.subcategoria ? row.subcategoria : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.categoria ? row.categoria : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.part_number ? row.part_number : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.alm_prod_descripcion ? row.alm_prod_descripcion : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.detalle_orden_precio ? (row.simbolo_moneda + Util.formatoNumero(row.detalle_orden_precio, 2)) : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.cdc_precio ? ((row.moneda_pvu == 's' ? 'S/' : row.moneda_pvu == 'd' ? '$' : '') + Util.formatoNumero(row.cdc_precio, 2)) : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        // return `${row.fecha ? moment(row.fecha).format('YYYY-MM-DD') : ''}`;
                        return `${row.fecha ? row.fecha : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.plazo_entrega > 0 ? row.plazo_entrega + ' días' : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {

                        let output = 'No aplica';
                        if (row['id_tp_documento'] == 2) { // orden de compra
                            let estimatedTimeOfArrive = moment(row['fecha_formato'], 'DD-MM-YYYY').add(row['plazo_entrega'], 'days').format('DD-MM-YYYY');
                            let sumaFechaConPlazo = moment(row['fecha_formato'], "DD-MM-YYYY").add(row['plazo_entrega'], 'days').format("DD-MM-YYYY").toString();
                            let fechaActual = moment().format('DD-MM-YYYY').toString();
                            let dias_restantes = moment(sumaFechaConPlazo, 'DD-MM-YYYY').diff(moment(fechaActual, 'DD-MM-YYYY'), 'days');
                            let porc = dias_restantes * 100 / (parseFloat(row['plazo_entrega'])).toFixed(2);
                            let color = (porc > 50 ? 'success' : ((porc <= 50 && porc > 20) ? 'warning' : 'danger'));
                            output = `<div class="progress-group">
                        <span class="progress-text">${estimatedTimeOfArrive} <br> Nro días Restantes</span>
                        <span class="float-right"><b>${dias_restantes > 0 ? dias_restantes : '0'}</b></span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-${color}" style="width: ${(porc < 1) ? '100' : porc}%"></div>
                        </div>
                    </div>`;

                        }
                        return output;
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.empresa_sede ? row.empresa_sede : ''}`;
                    }
                },
                {
                    render: function (data, type, row) {
                        let cantidadRequerimientosConEstadosPorRegularizarOenPausa = 0;
                        if (row.requerimientos != undefined && row.requerimientos.length > 0) {
                            (row.requerimientos).forEach(element => {
                                if (element.estado == 38 || element.estado == 39) {
                                    cantidadRequerimientosConEstadosPorRegularizarOenPausa++;
                                }
                            });
                        }
                        let estadoDetalleOrdenHabilitadasActualizar = [1, 2, 3, 4, 5, 6, 15];
                        if (cantidadRequerimientosConEstadosPorRegularizarOenPausa > 0) {
                            return `<span class="" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: default;">${row.detalle_orden_estado}</span>`;
                        } else {

                            if (estadoDetalleOrdenHabilitadasActualizar.includes(row.id_detalle_orden_estado) == true) {
                                return `<a class="handleClickEditarEstadoItemOrden" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.detalle_orden_estado}</a>`;
                            } else {
                                return `<span class="" data-id-estado-detalle-orden-compra="${row.id_detalle_orden_estado}" data-id-orden-compra="${row.detalle_orden_id_orden_compra}" data-id-detalle-orden-compra="${row.detalle_orden_id_detalle_orden}" data-codigo-item="${row.alm_prod_codigo}" style="cursor: default;">${row.detalle_orden_estado}</span>`;
                            }

                        }

                    }
                },
                {
                    render: function (data, type, row) {

                        let cantidadRequerimientosConEstadosPorRegularizarOenPausa = 0;
                        if (row.requerimientos != undefined && row.requerimientos.length > 0) {
                            (row.requerimientos).forEach(element => {
                                if (element.estado == 38 || element.estado == 39) {
                                    cantidadRequerimientosConEstadosPorRegularizarOenPausa++;
                                }
                            });
                        }

                        let containerOpenBrackets = '<div class="btn-group btn-group-xs" role="group" style="margin-bottom: 5px;display: flex;flex-direction: row;flex-wrap: nowrap;">';
                        let btnImprimirOrden = '<button type="button" class="btn btn-sm btn-warning boton handleClickAbrirOrdenPDF" name="btnGenerarOrdenRequerimientoPDF" title="Abrir orden PDF" data-id-requerimiento="' + row.id_requerimiento + '"  data-codigo-requerimiento="' + row.codigo_requerimiento + '" data-id-orden-compra="' + row.id_orden_compra + '"><i class="fas fa-file-download fa-xs"></i></button>';
                        let btnDocumentosVinculados = '<button type="button" class="btn btn-sm btn-primary boton handleClickDocumentosVinculados" name="btnDocumentosVinculados" title="Ver documentos vinculados" data-id-requerimiento="' + row.id_requerimiento + '"  data-codigo-requerimiento="' + row.codigo_requerimiento + '" data-id-orden-compra="' + row.id_orden_compra + '"><i class="fas fa-folder fa-xs"></i></button>';
                        let containerCloseBrackets = '</div>';



                        return (containerOpenBrackets + btnImprimirOrden + btnDocumentosVinculados + containerCloseBrackets);



                    }
                }
            ],
            'columnDefs': [
                { 'aTargets': [0], 'className': "text-center" },
                { 'aTargets': [1], 'className': "text-center" },
                { 'aTargets': [2], 'className': "text-center" },
                { 'aTargets': [3], 'className': "text-left" },
                { 'aTargets': [4], 'className': "text-center" },
                { 'aTargets': [5], 'className': "text-center" },
                { 'aTargets': [6], 'className': "text-center" },
                { 'aTargets': [7], 'className': "text-center" },
                { 'aTargets': [8], 'className': "text-center" },
                { 'aTargets': [9], 'className': "text-left" },
                { 'aTargets': [10], 'className': "text-right" },
                { 'aTargets': [11], 'className': "text-right" },
                { 'aTargets': [12], 'className': "text-center" },
                { 'aTargets': [13], 'className': "text-center" },
                { 'aTargets': [14], 'className': "text-center" },
                { 'aTargets': [15], 'className': "text-center" },
                { 'aTargets': [16], 'className': "text-center" },
                { 'aTargets': [17], 'className': "text-center" }
            ],

            "initComplete": function () {
                that.updateContadorFiltroDetalleOrdenesElaboradas();

            }
            // 'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
    }

    // verOrdenModal(obj) {
    //     // let codigo = obj.dataset.codigoOrdenCompra;
    //     let id_orden = obj.dataset.idOrdenCompra;
    //     // let id_estado_actual = obj.dataset.idEstadoOrdenCompra;
    //     // console.log(id_orden);

    //     $('#modal-ver-orden').modal({
    //         show: true,
    //         backdrop: 'true'
    //     });
    //     this.listaOrdenCtrl.mostrarOrden(id_orden).then((res) => {
    //         console.log(res);
    //         if (res.status == 200) {
    //             this.llenarCabeceraOrden(res.head);
    //             this.llenarTablaItemsOrden(res.detalle);
    //         } else {
    //             Swal.fire(
    //                 '',
    //                 'Sin data para mostrar',
    //                 'info'
    //             );
    //         }
    //     }).catch((err) => {
    //         console.log(err)
    //     })
    // }

    // llenarTablaItemsOrden(data) {
    //     let that = this;
    //     $('#tablaItemOrdenCompra').dataTable({
    //         bDestroy: true,
    //         order: [[0, 'asc']],
    //         info: true,
    //         iDisplayLength: 2,
    //         paging: true,
    //         searching: false,
    //         language: vardataTables[0],
    //         processing: true,
    //         bDestroy: true,
    //         data: data,
    //         columns: [
    //             {
    //                 'render':
    //                     function (data, type, row, meta) {
    //                         return meta.row + 1;
    //                     }
    //             },

    //             { data: 'codigo_producto' },
    //             { data: 'part_number' },
    //             { data: 'descripcion' },
    //             { data: 'unidad_medida' },
    //             { data: 'cantidad' },
    //             {
    //                 'render':
    //                     function (data, type, row) {
    //                         return `${row.precio_unitario ? ((row.simbolo_moneda ? row.simbolo_moneda : '') + Util.formatoNumero(row.precio_unitario, 2)) : ''}`;
    //                     }
    //             },
    //             {
    //                 'render':
    //                     function (data, type, row) {
    //                         return `${row.subtotal ? ((row.simbolo_moneda ? row.simbolo_moneda : '') + Util.formatoNumero(row.subtotal, 2)) : ''}`;
    //                     }
    //             },
    //             {
    //                 'render':
    //                     function (data, type, row, meta) {

    //                         return row.estado_detalle_orden ?? '';
    //                         // let estadoDetalleOrdenHabilitadasActualizar=[1,2,3,4,5,6,15];

    //                         // if(estadoDetalleOrdenHabilitadasActualizar.includes(row.id_estado_detalle_orden)==true){
    //                         //     return `<span class="label label-default handleClickEditarEstadoItemOrden" data-id-estado-detalle-orden-compra="${row.id_estado_detalle_orden}" data-id-orden-compra="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_item}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.estado_detalle_orden}</span>`;
    //                         // }else{
    //                         //     return `<span class="label label-default" data-id-estado-detalle-orden-compra="${row.id_estado_detalle_orden}" data-id-orden-compra="${row.id_orden_compra}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_item}" style="cursor: default;" >${row.estado_detalle_orden}</span>`;
    //                         // }
    //                     }
    //             },
    //         ],
    //         'columnDefs': [
    //             { 'aTargets': [0], 'className': "text-center" },
    //             { 'aTargets': [1], 'className': "text-center" },
    //             { 'aTargets': [2], 'className': "text-center" },
    //             { 'aTargets': [3], 'className': "text-left" },
    //             { 'aTargets': [4], 'className': "text-center" },
    //             { 'aTargets': [5], 'className': "text-center" },
    //             { 'aTargets': [6], 'className': "text-right" },
    //             { 'aTargets': [7], 'className': "text-right" },
    //             { 'aTargets': [8], 'className': "text-center" }
    //         ],
    //         "initComplete": function () {

    //             $('#tablaItemOrdenCompra tbody').on("click", "span.handleClickEditarEstadoItemOrden", function (e) {
    //                 that.editarEstadoItemOrden(e.currentTarget);
    //             });
    //         },
    //     })

    //     let tablelistaitem = document.getElementById('tablaItemOrdenCompra_wrapper');
    //     tablelistaitem.childNodes[0].childNodes[0].hidden = true;
    // }

    // llenarCabeceraOrden(data) {
    //     // console.log(data);
    //     document.querySelector("span[id='inputCodigo']").textContent = data.codigo_orden;
    //     document.querySelector("p[id='inputProveedor']").textContent = data.razon_social + ' RUC: ' + data.nro_documento;
    //     document.querySelector("p[id='inputFecha']").textContent = data.fecha;
    //     document.querySelector("p[id='inputMoneda']").textContent = data.moneda_descripcion;
    //     document.querySelector("p[id='inputCondicion']").textContent = data.condicion;
    //     document.querySelector("p[id='inputPlazoEntrega']").textContent = data.plazo_entrega;
    //     document.querySelector("p[id='inputCodigoSoftlink']").textContent = data.codigo_softlink;
    //     document.querySelector("p[id='inputEstado']").textContent = data.estado_doc;

    // }

    editarEstadoOrden(obj) {
        let id_orden = obj.dataset.idOrdenCompra;
        let id_estado_actual = obj.dataset.idEstadoOrdenCompra;
        let codigo = obj.dataset.codigoOrden;

        $('#modal-editar-estado-orden').modal({
            show: true,
            backdrop: 'true'
        });

        document.querySelector("div[id='modal-editar-estado-orden'] input[name='id_orden_compra'").value = id_orden;
        document.querySelector("div[id='modal-editar-estado-orden'] span[name='codigo_orden'").textContent = codigo;
        document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'").value = id_estado_actual;

    }

    editarEstadoItemOrden(obj) {
        let id_orden_compra = obj.dataset.idOrdenCompra;
        let id_detalle_orden = obj.dataset.idDetalleOrdenCompra;
        let id_estado_actual = obj.dataset.idEstadoDetalleOrdenCompra;
        let codigoItem = obj.dataset.codigoItem;

        $('#modal-editar-estado-detalle-orden').modal({
            show: true,
            backdrop: 'true'
        });

        document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_orden_compra'").value = id_orden_compra;
        document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_detalle_orden_compra'").value = id_detalle_orden;
        document.querySelector("div[id='modal-editar-estado-detalle-orden'] span[name='codigo_item_orden_compra'").textContent = codigoItem;

        document.querySelector("select[name='estado_detalle_orden']").value = id_estado_actual;

    }

    updateEstadoOrdenCompra(obj) {
        let id_orden_compra = document.querySelector("div[id='modal-editar-estado-orden'] input[name='id_orden_compra'").value;
        let id_estado_orden_selected = document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'").value;
        let estado_orden_selected = document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'")[document.querySelector("div[id='modal-editar-estado-orden'] select[name='estado_orden'").selectedIndex].textContent;
        obj.setAttribute("disabled", "true");
        this.listaOrdenCtrl.actualizarEstadoOrdenPorRequerimiento(id_orden_compra, id_estado_orden_selected).then((res) => {
            obj.removeAttribute("disabled");

            this.tipoVistaPorCabecera();

            if (res == 1) {
                Lobibox.notify('success', {
                    title: false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: `El estado de orden actualizado`
                });
                // document.querySelector("span[id='estado_orden']").textContent = estado_orden_selected;
                $('#modal-editar-estado-orden').modal('hide');
            } else {
                Swal.fire(
                    '',
                    'Lo sentimos hubo un problema en el servidor al intentar actualizar el estado, por favor vuelva a intentarlo',
                    'error'
                );
            }
        }).catch(function (err) {
            console.log(err)
            Swal.fire(
                '',
                'Lo sentimos hubo un problema en el servidor al intentar actualizar el estado, por favor vuelva a intentarlo',
                'error'
            );
        })

    }

    updateEstadoDetalleOrdenCompra(obj) {
        let id_orden_compra = document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_orden_compra'").value;
        let id_detalle_orden_compra = document.querySelector("div[id='modal-editar-estado-detalle-orden'] input[name='id_detalle_orden_compra'").value;
        let id_estado_detalle_orden_selected = document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'").value;
        let estado_detalle_orden_selected = document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'")[document.querySelector("div[id='modal-editar-estado-detalle-orden'] select[name='estado_detalle_orden'").selectedIndex].textContent;
        obj.setAttribute("disabled", true);
        this.listaOrdenCtrl.actualizarEstadoDetalleOrdenPorRequerimiento(id_detalle_orden_compra, id_estado_detalle_orden_selected).then((res) => {
            obj.removeAttribute("disabled");
            this.tipoVistaPorItem();
            if (res == 1) {
                Lobibox.notify('success', {
                    title: false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: `El estado del item fue actualizado`
                });
                // this.listaOrdenCtrl.mostrarOrden(id_orden_compra).then((res) => {
                //     if (res.status == 200) {
                //         this.llenarCabeceraOrden(res.head);
                //         this.llenarTablaItemsOrden(res.detalle);
                //     } else {
                //         Lobibox.notify('info', {
                //             title: false,
                //             size: 'mini',
                //             rounded: true,
                //             sound: false,
                //             delayIndicator: false,
                //             msg: `sin data disponible para mostrar`
                //         });

                //     }
                // }).catch((err) => {
                //     Swal.fire(
                //         '',
                //         'Lo sentimos hubo un problema en el servidor, por favor vuelva a intentarlo',
                //         'error'
                //     );
                //     console.log(err)
                // })
                $('#modal-editar-estado-detalle-orden').modal('hide');
            } else {
                Swal.fire(
                    '',
                    'Lo sentimos hubo un problema al intentar actualizar el estado, por favor vuelva a intentarlo',
                    'error'
                );

            }
        }).catch(function (err) {
            console.log(err)
            Swal.fire(
                '',
                'Lo sentimos hubo un problema en el servidor al intentar actualizar el estado, por favor vuelva a intentarlo',
                'error'
            );
        })

    }

    generarOrdenRequerimientoPDF(obj) {
        let id_orden = obj.dataset.idOrdenCompra;
        window.open('generar-orden-pdf/' + id_orden);
    }

    anularOrden(obj) {
        let codigoOrden = obj.dataset.codigoOrden;
        let id = obj.dataset.idOrdenCompra;
        Swal.fire({
            title: 'Esta seguro que desea anular la orden ' + codigoOrden + '?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Si, anular'

        }).then((result) => {
            if (result.isConfirmed) {
                // inicio  sustento
                let sustentoAnularOrden = '';
                Swal.fire({
                    title: 'Sustente el motivo de la anulación de orden',
                    input: 'textarea',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Registrar',

                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        sustentoAnularOrden = result.value;
                        // enviar anular orden
                        this.listaOrdenCtrl.anularOrden(id, sustentoAnularOrden).then((res) => {
                            if (res.status == 200) {
                                $("#wrapper-okc").LoadingOverlay("hide", true);

                                Lobibox.notify('success', {
                                    title: false,
                                    size: 'mini',
                                    rounded: true,
                                    sound: false,
                                    delayIndicator: false,
                                    msg: 'Orden anulada'
                                });
                                // location.reload();
                                obj.closest('tr').remove();

                                if (document.querySelector("button[id='btnTipoVistaPorItemPara']").classList.contains('active')) {
                                    this.tipoVistaPorItem();
                                }

                            } else {

                                $("#wrapper-okc").LoadingOverlay("hide", true);

                                Swal.fire(
                                    '',
                                    res.mensaje.toString(),
                                    res.tipo_estado
                                );

                                if (res.status_migracion_softlink != null) {

                                    Lobibox.notify(res.status_migracion_softlink.tipo, {
                                        title: false,
                                        size: 'mini',
                                        rounded: true,
                                        sound: false,
                                        delayIndicator: false,
                                        msg: res.status_migracion_softlink.mensaje
                                    });

                                }
                                console.log(res);
                            }
                        }).catch((err) => {
                            $("#wrapper-okc").LoadingOverlay("hide", true);
                            console.log(err)
                            Swal.fire(
                                '',
                                'Lo sentimos hubo un error en el servidor, por favor vuelva a intentarlo',
                                'error'
                            );
                        });
                        // fin envio anular orden
                    }
                })
                // fin susntento


            }
        })

    }

    documentosVinculados(obj) {
        $('#modal-documentos-vinculados').modal({
            show: true,
            backdrop: 'static'
        });

        let id_orden_compra = obj.dataset.idOrdenCompra;
        this.listaOrdenCtrl.listarDocumentosVinculados(id_orden_compra).then((res) => {
            this.llenarTablaDocumentosVinculados(res.data);
        }).catch((err) => {
            console.log(err)
        })
    }

    llenarTablaDocumentosVinculados(data) {
        var vardataTables = funcDatatables();
        $('#tablaDocumentosVinculados').dataTable({
            'info': false,
            'searching': false,
            'paging': false,
            'language': vardataTables[0],
            'processing': true,
            "bDestroy": true,
            'data': data,
            'columns': [
                {
                    'render':
                        function (data, type, row) {
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



    // ###============  Inicia enviar orden a pago ============###

    limpiarFormEnviarOrdenAPago() {
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_proveedor']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = '';

        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_persona']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='tipo_documento_identidad']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nro_documento']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] textarea[name='comentario']").value = '';
        this.limpiarTabla('listaDestinatariosEncontrados');
        document.querySelector("div[id='modal-enviar-solicitud-pago'] span[id='cantidadDestinatariosEncontrados']").textContent = 0;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_persona']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value = "";
        let selectCuenta = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']");
        if (selectCuenta != null) {
            while (selectCuenta.children.length > 0) {
                selectCuenta.removeChild(selectCuenta.lastChild);
            }
        }
    }

    restablecerValoresPorDefectoFormEnviarOrdenAPago() {
        document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_prioridad']").value = 1;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_tipo_destinatario']").value = 2;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nro_documento']").setAttribute("disabled", true);
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").setAttribute("disabled", true);
        tempDataProveedorParaPago = [];

    }

    modalEnviarOrdenAPago(obj) {
        document.querySelector("div[id='modal-enviar-solicitud-pago'] span[id='codigo_orden']").textContent = '';
        this.limpiarFormEnviarOrdenAPago();
        this.restablecerValoresPorDefectoFormEnviarOrdenAPago();
        $('#modal-enviar-solicitud-pago').modal({
            show: true,
            backdrop: 'static'
        });
        
        document.querySelector("div[id='modal-enviar-solicitud-pago'] span[id='codigo_orden']").textContent = obj.dataset.codigoOrden;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_orden_compra']").value = obj.dataset.idOrdenCompra;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_proveedor']").value = obj.dataset.idProveedor;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = obj.dataset.idCuentaPrincipal;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] textarea[name='comentario']").value = obj.dataset.comentarioPago != null ? obj.dataset.comentarioPago : '';

        if (obj.dataset.estadoPago == 8) {
            document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_prioridad']").value = obj.dataset.idPrioridadPago;
            document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_tipo_destinatario']").value = obj.dataset.idTipoDestinatarioPago;

            if (obj.dataset.idTipoDestinatarioPago == 1) {
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nro_documento']").removeAttribute("disabled");
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").removeAttribute("disabled");
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value = obj.dataset.idPersonaPago;
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_persona']").value = obj.dataset.idCuentaPersonaPago;
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value = '';
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = '';
                obtenerPersona(obj.dataset.idPersonaPago);
                obtenerCuentasBancariasPersona(obj.dataset.idPersonaPago);


            } else if (obj.dataset.idTipoDestinatarioPago == 2) {
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value = obj.dataset.idContribuyentePago;
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = obj.dataset.idCuentaContribuyentePago;
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value = '';
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_persona']").value = '';

                obtenerContribuyente(obj.dataset.idContribuyentePago);
                obtenerCuentasBancariasContribuyente(obj.dataset.idContribuyentePago);
            }
        } else {
            this.obtenerContribuyentePorIdProveedor(obj.dataset.idProveedor)
        }

    }

    getContribuyentePorIdProveedor(id) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `obtener-contribuyente-por-id-proveedor/${id}`,
                dataType: 'JSON',
                beforeSend: data => {
                    $("#modal-enviar-solicitud-pago .modal-content").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success(response) {
                    $("#modal-enviar-solicitud-pago .modal-content").LoadingOverlay("hide", true);
                    resolve(response);
                },
                error: function (err) {
                    $("#modal-enviar-solicitud-pago .modal-content").LoadingOverlay("hide", true);
                    reject(err)
                }
            });
        });
    }


    obtenerContribuyentePorIdProveedor(idProveedor) {
        this.getContribuyentePorIdProveedor(idProveedor).then((res) => {
            // console.log(res);
            if (res.tipo_estado == 'success') {
                tempDataProveedorParaPago = res.data;
                this.llenarInputsDeDestinatario(res.data);
            } else {
                Lobibox.notify(res.tipo_estado, {
                    title: false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: res.mensaje
                });
            }
        }).catch(function (err) {
            console.log(err)
        })
    }

    llenarInputsDeDestinatario(data) {
        // console.log(data);
        document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_tipo_destinatario']").value = 2;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value = data.id_contribuyente != '' && data.id_contribuyente != null ? data.id_contribuyente : '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='tipo_documento_identidad']").value = data.tipo_documento_identidad != null ? data.tipo_documento_identidad.descripcion : '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nro_documento']").value = data.nro_documento != '' && data.nro_documento != null ? data.nro_documento : '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").value = data.razon_social != null && data.razon_social != '' ? data.razon_social : '';

        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_persona']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value = "";
        let selectCuenta = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']");
        if (selectCuenta != null) {
            while (selectCuenta.children.length > 0) {
                selectCuenta.removeChild(selectCuenta.lastChild);
            }
        }



        if (data.id_contribuyente > 0) {
            obtenerCuentasBancariasContribuyente(data.id_contribuyente);
        }
    }

    // obtenerCuentasBancariasContribuyente(id_contribuyente) { // esta función es distinta a la de requerimiento de pago
    //     // console.log(id_contribuyente);

    //     if (id_contribuyente > 0) {
    //         $.ajax({
    //             type: 'GET',
    //             url: 'obtener-cuenta-contribuyente/' + id_contribuyente,
    //             dataType: 'JSON',
    //         }).done(function (response) {
    //             // console.log(response);
    //             if (response.tipo_estado == 'success') {

    //                 if (response.data.length > 0) {

    //                     // llenar cuenta bancaria
    //                     let idCuentePorDefecto = document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value;
    //                     console.log(idCuentePorDefecto);

    //                     document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value = "";
    //                     let selectCuenta = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']");
    //                     if (selectCuenta != null) {
    //                         while (selectCuenta.children.length > 0) {
    //                             selectCuenta.removeChild(selectCuenta.lastChild);
    //                         }
    //                     }
    //                     // console.log(response.data);
    //                     (response.data).forEach(element => {
    //                         document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").insertAdjacentHTML('beforeend', `
    //                         <option 
    //                             data-nro-cuenta="${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : ''}" 
    //                             data-nro-cci="${element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != "" ? element.nro_cuenta_interbancaria : ''}" 
    //                             data-tipo-cuenta="${element.tipo_cuenta != null ? element.tipo_cuenta.descripcion : ''}" 
    //                             data-banco="${element.banco != null && element.banco.contribuyente != null ? element.banco.contribuyente.razon_social : ''}" 
    //                             data-moneda="${element.moneda != null ? element.moneda.descripcion : ''}" 
    //                             value="${element.id_cuenta_contribuyente}" ${element.id_cuenta_contribuyente == idCuentePorDefecto ? 'selected' : ''}
    //                             >${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : (element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != "" ? (element.nro_cuenta_interbancaria + " (CCI)") : "")}</option>
    //                         `);
    //                     });
    //                     if (idCuentePorDefecto == null || idCuentePorDefecto == '') {
    //                         document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value;
    //                     }

    //                 } else {
    //                     Lobibox.notify('error', {
    //                         size: "mini",
    //                         rounded: true,
    //                         sound: false,
    //                         delayIndicator: false,
    //                         msg: 'Hubo un problema. no se encontró un id cuenta valido'
    //                     });
    //                 }

    //             } else {
    //                 console.log(response);

    //                 Lobibox.notify(response.tipo_estado, {
    //                     size: "mini",
    //                     rounded: true,
    //                     sound: false,
    //                     delayIndicator: false,
    //                     msg: response.mensaje
    //                 });
    //             }

    //         }).always(function () {

    //         }).fail(function (jqXHR) {
    //             Lobibox.notify('error', {
    //                 size: "mini",
    //                 rounded: true,
    //                 sound: false,
    //                 delayIndicator: false,
    //                 msg: 'Hubo un problema. Por favor actualice la página e intente de nuevo.'
    //             });
    //             console.log('Error devuelto: ' + jqXHR.responseText);
    //         });
    //     } else {
    //         Lobibox.notify('error', {
    //             size: "mini",
    //             rounded: true,
    //             sound: false,
    //             delayIndicator: false,
    //             msg: 'Hubo un problema. no se encontró un id persona valido para obtener una respuesta'
    //         });
    //     }
    // }

    changeTipoDestinatario(obj) {
        if (obj.value == 1) { // tipo persona
            this.limpiarFormEnviarOrdenAPago();

            document.querySelector("div[id='modal-enviar-solicitud-pago'] button[id='btnAgregarNuevoDestiantario']").removeAttribute("disabled");
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nro_documento']").removeAttribute("disabled");
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").removeAttribute("disabled");
        } else if (obj.value == 2) { // tipo contribuyente

            this.limpiarFormEnviarOrdenAPago();

            document.querySelector("div[id='modal-enviar-solicitud-pago'] button[id='btnAgregarNuevoDestiantario']").setAttribute("disabled", true);
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nro_documento']").setAttribute("disabled", true);
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").setAttribute("disabled", true);
            this.llenarInputsDeDestinatario(tempDataProveedorParaPago);
        }

    }

    validarFormularioEnvioOrdenAPago() {
        let continuar = false;
        let menseje=[];

        if(document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value == '' && document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value == ''){
            menseje.push('Debe seleccionar una persona o un contribuyente');
        }else{
            if(document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value == ''){
                menseje.push('Debe seleccionar una cuanta bancaria');
            }else{
                continuar = true;
            }
        } 

        if(menseje.length>0){
            Swal.fire(
                '',
                menseje.toString(),
                'warning'
            );
        }
        return continuar;
    }

    registrarSolicitudDePago() {
        // console.log('enviar a pago');

        if (this.validarFormularioEnvioOrdenAPago()) {
            let formData = new FormData($('#form-enviar_solicitud_pago')[0]);
            $.ajax({
                type: 'POST',
                url: 'registrar-solicitud-de-pago',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {

                    var customElement = $("<div>", {
                        "css": {
                            "font-size": "20px",
                            "text-align": "center",
                            "position": "absolute",
                            "overflow": "auto",
                            "top": "50%"
                        },
                        "class": "your-custom-class",
                        "text": "Enviando Solicitud de pago"
                    });

                    $('#modal-enviar-solicitud-pago .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        custom: customElement,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) => {
                    $('#modal-enviar-solicitud-pago .modal-content').LoadingOverlay("hide", true);
                    console.log(response);
                    if (response.tipo_estado == 'success') {

                        Lobibox.notify('success', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: response.mensaje
                        });
                        $('#modal-enviar-solicitud-pago').modal('hide');

                        this.tipoVistaPorCabecera();

                    } else {
                        $('#modal-enviar-solicitud-pago .modal-content').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            response.mensaje,
                            'error'
                        );
                    }
                },
                statusCode: {
                    404: function () {
                        $('#modal-enviar-solicitud-pago .modal-content').LoadingOverlay("hide", true);
                        Swal.fire(
                            'Error 404',
                            'Lo sentimos hubo un problema con el servidor, la ruta a la que se quiere acceder para guardar no esta disponible, por favor vuelva a intentarlo más tarde.',
                            'error'
                        );
                    }
                },
                fail: (jqXHR, textStatus, errorThrown) => {
                    $('#modal-enviar-solicitud-pago .modal-content').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar enviar la orden a pago, por favor vuelva a intentarlo',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }
    }

    mostrarInfoAdicionalCuentaSeleccionada() {
        document.querySelector("div[id='modal-info-adicional-cuenta-seleccionada'] div[class='modal-body']").innerHTML = '';
        let selectCuenta = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']");
        if (selectCuenta.value > 0) {
            $('#modal-info-adicional-cuenta-seleccionada').modal({
                show: true
            });
            document.querySelector("div[id='modal-info-adicional-cuenta-seleccionada'] div[class='modal-body']").insertAdjacentHTML('beforeend', `<div>
            
            <dl>
                <dt>Banco</dt>
                <dd>${selectCuenta.options[(selectCuenta.selectedIndex)].dataset.banco}</dd>
                <dt>Tipo Cuenta</dt>
                <dd>${selectCuenta.options[(selectCuenta.selectedIndex)].dataset.tipoCuenta}</dd>
                <dt>Moneda</dt>
                <dd>${selectCuenta.options[(selectCuenta.selectedIndex)].dataset.moneda}</dd>
                <dt>Nro cuenta</dt>
                <dd>${selectCuenta.options[(selectCuenta.selectedIndex)].dataset.nroCuenta}</dd>
                <dt>Nro CCI</dt>
                <dd>${selectCuenta.options[(selectCuenta.selectedIndex)].dataset.nroCci}</dd>
            </dl>
            </div>`);
        } else {
            Swal.fire(
                'Información de cuenta',
                'Debe seleccionar una persona o contribuyente que cuente con información de cuenta bancaria',
                'info'
            );
        }
    }

    buscarDestinatarioPorNumeroDeDocumento(obj) {
        let idTipoDestinatario = parseInt(document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_tipo_destinatario']").value);
        if (idTipoDestinatario == 1) {

            let nroDocumento = (obj.value).trim();
            if (nroDocumento.length > 0 && idTipoDestinatario > 0) {
                $.ajax({
                    type: 'POST',
                    url: 'obtener-destinatario-por-nro-documento',
                    data: { 'nroDocumento': nroDocumento, 'idTipoDestinatario': idTipoDestinatario },
                    dataType: 'JSON',
                    beforeSend: data => {

                        $("input[name='nombre_destinatario']").LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) => {
                        $("input[name='nombre_destinatario']").LoadingOverlay("hide", true);


                        if (response.tipo_estado == 'success') {
                            if (response.data != null && response.data.length > 0) {
                                if (idTipoDestinatario == 1) { // persona
                                    document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").value = response.data[0]['nombre_completo'];
                                    document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value = response.data[0]['id_persona'];
                                    if (response.data[0]['tipo_documento_identidad'] != null) {
                                        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='tipo_documento_identidad']").value = (response.data[0]['tipo_documento_identidad']['descripcion']) != null ? response.data[0]['tipo_documento_identidad']['descripcion'] : '';
                                    }

                                    // llenar cuenta bancaria
                                    document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value = "";
                                    let selectCuenta = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']");
                                    if (selectCuenta != null) {
                                        while (selectCuenta.children.length > 0) {
                                            selectCuenta.removeChild(selectCuenta.lastChild);
                                        }
                                    }
                                    (response.data[0].cuenta_persona).forEach(element => {
                                        document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").insertAdjacentHTML('beforeend', `
                                        <option 
                                            data-nro-cuenta="${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : ''}" 
                                            data-nro-cci="${element.nro_cci != null && element.nro_cci != "" ? element.nro_cci : ''}" 
                                            data-tipo-cuenta="${element.tipo_cuenta != null ? element.tipo_cuenta.descripcion : ''}" 
                                            data-banco="${element.banco != null && element.banco.contribuyente != null ? element.banco.contribuyente.razon_social : ''}" 
                                            data-moneda="${element.moneda != null ? element.moneda.descripcion : ''}" 
                                            value="${element.id_cuenta_bancaria}"
                                            >${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : (element.nro_cci != null && element.nro_cci != "" ? (element.nro_cci + " (CCI)") : "")}</option>
                                        `);
                                    });


                                } else if (idTipoDestinatario == 2) { // contribuyente
                                    document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").value = response.data[0]['razon_social'];
                                    document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value = response.data[0]['id_contribuyente'];
                                    if (response.data[0]['tipo_documento_identidad'] != null) {
                                        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='tipo_documento_identidad']").value = (response.data[0]['tipo_documento_identidad']['descripcion']) != null ? response.data[0]['tipo_documento_identidad']['descripcion'] : '';
                                    }
                                    // llenar cuenta bancaria
                                    document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value = "";
                                    let selectCuenta = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']");
                                    if (selectCuenta != null) {
                                        while (selectCuenta.children.length > 0) {
                                            selectCuenta.removeChild(selectCuenta.lastChild);
                                        }
                                    }
                                    (response.data[0].cuenta_contribuyente).forEach(element => {
                                        document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").insertAdjacentHTML('beforeend', `
                                        <option 
                                            data-nro-cuenta="${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : ''}" 
                                            data-nro-cci="${element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != "" ? element.nro_cuenta_interbancaria : ''}" 
                                            data-tipo-cuenta="${element.tipo_cuenta != null ? element.tipo_cuenta.descripcion : ''}" 
                                            data-banco="${element.banco != null && element.banco.contribuyente != null ? element.banco.contribuyente.razon_social : ''}" 
                                            data-moneda="${element.moneda != null ? element.moneda.descripcion : ''}" 
                                            value="${element.id_cuenta_contribuyente}"
                                            >${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : (element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != "" ? (element.nro_cuenta_interbancaria + " (CCI)") : "")}</option>
                                        `);

                                    });
                                }
                                this.listarEnResultadoDestinatario(response.data, idTipoDestinatario);
                            } else {
                                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value = "";
                                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value = "";
                                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").value = "";
                                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='tipo_documento_identidad']").value = "";
                                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_persona']").value = "";
                                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = "";
                                document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value = "";

                                let selectCuenta = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']");
                                if (selectCuenta != null) {
                                    while (selectCuenta.children.length > 0) {
                                        selectCuenta.removeChild(selectCuenta.lastChild);
                                    }
                                }

                            }
                            Lobibox.notify(response.tipo_estado, {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: response.mensaje
                            });
                        } else {
                            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value = "";
                            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value = "";
                            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").value = "";
                            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='tipo_documento_identidad']").value = "";
                            document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value = "";
                            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_persona']").value = "";
                            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = "";

                            let selectCuenta = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']");
                            if (selectCuenta != null) {
                                while (selectCuenta.children.length > 0) {
                                    selectCuenta.removeChild(selectCuenta.lastChild);
                                }
                            }
                        }

                    }
                }).fail((jqXHR, textStatus, errorThrown) => {
                    $("input[name='nombre_destinatario']").LoadingOverlay("hide", true);

                    Swal.fire(
                        '',
                        'Hubo un problema al intentar obtener la data, por favor vuelva a intentarlo.',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        }
    }

    listarEnResultadoDestinatario(data, idTipoDestinatario) {

        document.querySelector("div[id='modal-enviar-solicitud-pago'] span[id='cantidadDestinatariosEncontrados']").textContent = data.length;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] table[id='listaDestinatariosEncontrados']").innerHTML = '';
        data.forEach(element => {
            if (idTipoDestinatario == 1) {
                document.querySelector("div[id='modal-enviar-solicitud-pago'] table[id='listaDestinatariosEncontrados']").insertAdjacentHTML('beforeend', `
                <tr class="handleClickSeleccionarDestinatario" style="cursor:pointer;" 
                data-id-persona="${element.id_persona != null ? element.id_persona : ''}"
                data-id-contribuyente="${element.id_contribuyente != null ? element.id_contribuyente : ''}"
                data-tipo-documento-identidad="${element.tipo_documento_identidad != null ? element.tipo_documento_identidad.descripcion : ''}"
                data-numero-documento="${element.nro_documento != null ? element.nro_documento : ''}"
                data-nombre-destinatario="${element.nombre_completo != null ? element.nombre_completo : ''}"
                data-cuenta="${JSON.stringify(element.cuenta_persona)}"
                >
                <td>${element.tipo_documento_identidad != null ? element.tipo_documento_identidad.descripcion : ''}</td>
                <td>${element.nro_documento != null ? element.nro_documento : ''}</td>
                <td>${element.nombre_completo != null ? element.nombre_completo : ''}</td>
                <td>${element.cuenta_persona.length > 0 ? '<span class="label label-success">Con cuenta</span>' : '<span class="label label-danger">Sin cuenta</span>'}</td>

                </tr>
                `);
            }
            if (idTipoDestinatario == 2) {
                document.querySelector("div[id='modal-enviar-solicitud-pago'] table[id='listaDestinatariosEncontrados']").insertAdjacentHTML('beforeend', `
                <tr class="handleClickSeleccionarDestinatario" style="cursor:pointer;"
                data-id-persona="${element.id_persona != null ? element.id_persona : ''}"
                data-id-contribuyente="${element.id_contribuyente != null ? element.id_contribuyente : ''}"
                data-tipo-documento-identidad="${element.tipo_documento_identidad != null ? element.tipo_documento_identidad.descripcion : ''}"
                data-numero-documento="${element.nro_documento != null ? element.nro_documento : ''}"
                data-nombre-destinatario="${element.razon_social != null ? element.razon_social : ''}"
                data-cuenta="${JSON.stringify(element.cuenta_contribuyente)}"
                >
                <td>${element.tipo_documento_identidad != null ? element.tipo_documento_identidad.descripcion : ''}</td>
                <td>${element.nro_documento != null ? element.nro_documento : ''}</td>
                <td>${element.razon_social ? element.razon_social : ''}</td>
                <td>${element.cuenta_contribuyente.length > 0 ? '<span class="label label-success">Con cuenta</span>' : '<span class="label label-danger">Sin cuenta</span>'}</td>


                </tr>
                `);
            }
        });
    }


    focusInputNombreDestinatario(obj) {
        document.querySelector("div[id='modal-enviar-solicitud-pago'] div[id='resultadoDestinatario']").classList.remove("oculto");

    }
    focusOutInputNombreDestinatario(obj) {
        setTimeout(() => {
            document.querySelector("div[id='modal-enviar-solicitud-pago'] div[id='resultadoDestinatario']").classList.add("oculto");
        }, 500);
    }

    actualizarIdCuentaBancariaDeInput(obj) {
        let idTipoDestinatario = parseInt(document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_tipo_destinatario']").value);
        if (obj.value > 0) {
            if (idTipoDestinatario == 1) {
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_persona']").value = obj.value;
            } else if (idTipoDestinatario == 2) {
                document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = obj.value;

            } else {
                Swal.fire(
                    '',
                    'Hubo un problema al intentar obtener el tipo de destinatario, por favor vuelva a intentarlo refrescando la página',
                    'error'
                );
            }
        } else {
            Swal.fire(
                '',
                'Hubo un problema al intentar obtener el id de la cuenta seleccionada, por favor vuelva a intentarlo refrescando la página',
                'error'
            );
        }
    }
    buscarDestinatarioPorNombre(obj) {
        let nombreDestinatario = obj.value;
        let idTipoDestinatario = parseInt(document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_tipo_destinatario']").value);

        if (idTipoDestinatario == 1) {
            if (!(nombreDestinatario).trim().length == 0) {
                document.querySelector("div[id='modal-enviar-solicitud-pago'] div[id='resultadoDestinatario']").classList.remove("oculto");
                $.ajax({
                    type: 'POST',
                    url: 'obtener-destinatario-por-nombre',
                    data: { 'nombreDestinatario': nombreDestinatario, 'idTipoDestinatario': idTipoDestinatario },
                    dataType: 'JSON',
                    beforeSend: data => {

                        $("input[name='nro_documento']").LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                        $("div[id='resultadoDestinatario']").LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) => {
                        $("input[name='nro_documento']").LoadingOverlay("hide", true);
                        $("div[id='resultadoDestinatario']").LoadingOverlay("hide", true);


                        if (response.tipo_estado == 'success') {
                            if (response.data != null && response.data.length > 0) {
                                this.listarEnResultadoDestinatario(response.data, idTipoDestinatario);

                            }
                        }

                    }
                }).fail((jqXHR, textStatus, errorThrown) => {
                    $("input[name='nro_documento']").LoadingOverlay("hide", true);
                    $("div[id='resultadoDestinatario']").LoadingOverlay("hide", true);

                    Swal.fire(
                        '',
                        'Hubo un problema al intentar obtener la data, por favor vuelva a intentarlo.',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });

            }

            if ((nombreDestinatario).trim().length == 0 && (document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value > 0 || document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value > 0)) {
                this.limpiarInputDestinatario();
            }
        }

    }

    limpiarInputDestinatario() {
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value = "";
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value = "";
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").value = "";
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nro_documento']").value = "";
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='tipo_documento_identidad']").value = "";

        this.limpiarTabla("listaDestinatariosEncontrados");
        document.querySelector("div[id='modal-enviar-solicitud-pago'] span[id='cantidadDestinatariosEncontrados']").textContent = 0;

        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_persona']").value = "";
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = "";
        let selectCuenta = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']");
        if (selectCuenta != null) {
            while (selectCuenta.children.length > 0) {
                selectCuenta.removeChild(selectCuenta.lastChild);
            }
        }

    }

    seleccionarDestinatario(obj) {

        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value = obj.dataset.idPersona;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value = obj.dataset.idContribuyente;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nro_documento']").value = obj.dataset.numeroDocumento;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='nombre_destinatario']").value = obj.dataset.nombreDestinatario;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='tipo_documento_identidad']").value = obj.dataset.tipoDocumentoIdentidad;

        if (obj.dataset.idPersona > 0) {
            obtenerCuentasBancariasPersona(obj.dataset.idPersona);
        } else if (obj.dataset.idContribuyente > 0) {
            obtenerCuentasBancariasContribuyente(obj.dataset.idContribuyente);
        } else {

            Swal.fire(
                'Obtener cuenta bancaria',
                'Hubo un problema. no se encontró un id persona o id contribuyente valido para poder obtener las cuentas bancarias',
                'error'
            );

        }
    }
    // ###============ Fin enviar orden a pago ============###

}
