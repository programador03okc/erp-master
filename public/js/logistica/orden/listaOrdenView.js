var vardataTables = funcDatatables();
var cantidadFiltrosActivosCabecera = 0;
var cantidadFiltrosActivosDetalle = 0;

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
        this.listaOrdenCtrl.descargarListaOrdenesVistaCabecera();
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
                                }else{
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

                                let estimatedTimeOfArrive = moment(row['fecha'], 'DD-MM-YYYY').add(row['plazo_entrega'], 'days').format('DD-MM-YYYY');
                                let sumaFechaConPlazo = moment(row['fecha'], "DD-MM-YYYY").add(row['plazo_entrega'], 'days').format("DD-MM-YYYY").toString();
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
                            let containerCloseBrackets = '</div>';

                            return (containerOpenBrackets + btnVerDetalle + btnImprimirOrden + btnAnularOrden + containerCloseBrackets);

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
                            let estimatedTimeOfArrive = moment(row['fecha'], 'DD-MM-YYYY').add(row['plazo_entrega'], 'days').format('DD-MM-YYYY');
                            let sumaFechaConPlazo = moment(row['fecha'], "DD-MM-YYYY").add(row['plazo_entrega'], 'days').format("DD-MM-YYYY").toString();
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



                        return (containerOpenBrackets + btnImprimirOrden + btnDocumentosVinculados  + containerCloseBrackets);



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
                    title: 'Sustente el movivo de la anulación de orden',
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

                                if(res.status_migracion_softlink != null){
                                        
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

}
