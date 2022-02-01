var tempArchivoAdjuntoRequerimientoPagoCabeceraList = [];
var tempIdArchivoAdjuntoRequerimientoPagoCabeceraToDeleteList = [];
var tempArchivoAdjuntoRequerimientoPagoDetalleList = [];
var tempIdArchivoAdjuntoRequerimientoPagoDetalleToDeleteList = [];
var objBotonAdjuntoRequerimientoPagoDetalleSeleccionado = [];

let $tablaListaRequerimientoPago;
var iTableCounter = 1;
var oInnerTable;

var tempCentroCostoSelected;
var tempObjectBtnPartida;
var tempObjectBtnCentroCostos;

var $tablaListaCuadroPresupuesto;
class ListarRequerimientoPagoView {

    constructor() {
        this.ActualParametroAllOrMe = 'SIN_FILTRO';
        this.ActualParametroEmpresa = 'SIN_FILTRO';
        this.ActualParametroSede = 'SIN_FILTRO';
        this.ActualParametroGrupo = 'SIN_FILTRO';
        this.ActualParametroDivision = 'SIN_FILTRO';
        this.ActualParametroFechaDesde = 'SIN_FILTRO';
        this.ActualParametroFechaHasta = 'SIN_FILTRO';
        this.ActualParametroEstado = 'SIN_FILTRO';

    }

    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        if (nodeTbody != null) {
            while (nodeTbody.children.length > 0) {
                nodeTbody.removeChild(nodeTbody.lastChild);
            }

        }
    }
    initializeEventHandler() {

        this.checkStatusBtnGuardar();

        $('#ListaRequerimientoPago').on("click", "button.handleClickVerDetalleRequerimientoPago", (e) => {
            this.verDetalleRequerimientoPago(e.currentTarget);
        });
        $('#ListaRequerimientoPago tbody').on("click", "button.handleClickEditarRequerimientoPago", (e) => {
            this.editarRequerimientoPago(e.currentTarget);
        });
        $('#ListaRequerimientoPago tbody').on("click", "button.handleClickAnularRequerimientoPago", (e) => {
            this.anularRequerimientoPago(e.currentTarget);
        });

        $('#modal-requerimiento-pago').on("change", "select.handleChangeOptEmpresa", (e) => {
            this.changeOptEmpresaSelect(e.currentTarget);
        });

        $('#modal-requerimiento-pago').on("change", "select.handleChangeOptGrupo", (e) => {
            this.changeOptGrupoSelect(e.currentTarget);
        });


        $('#modal-requerimiento-pago').on("click", "button.handleClickAgregarServicio", () => {
            this.agregarServicio();
            this.checkStatusBtnGuardar();
        });

        $('#ListaDetalleRequerimientoPago tbody').on("click", "button.handleClickEliminarItem", (e) => {
            this.eliminarItem(e.currentTarget);
            this.checkStatusBtnGuardar();
        });

        $('#ListaDetalleRequerimientoPago tbody').on("click", "button.handleClickCargarModalPartidas", (e) => {
            this.cargarModalPartidas(e);
        });

        $('#modal-partidas').on("click", "h5.handleClickapertura", (e) => {
            this.apertura(e.currentTarget.dataset.idPresup);
            this.changeBtnIcon(e);
        });

        $('#modal-partidas').on("click", "button.handleClickSelectPartida", (e) => {
            this.selectPartida(e.currentTarget.dataset.idPartida);
        });

        $('#ListaDetalleRequerimientoPago tbody').on("click", "button.handleClickCargarModalCentroCostos", (e) => {
            this.cargarModalCentroCostos(e);
        });

        $('#modal-centro-costos').on("click", "h5.handleClickapertura", (e) => {
            this.apertura(e.currentTarget.dataset.idPresup);
            this.changeBtnIcon(e);
        });
        $('#modal-centro-costos').on("click", "button.handleClickSelectCentroCosto", (e) => {
            this.selectCentroCosto(e.currentTarget.dataset.idCentroCosto, e.currentTarget.dataset.codigo, e.currentTarget.dataset.descripcionCentroCosto);
        });

        $('#ListaDetalleRequerimientoPago tbody').on("blur", "input.handleBurUpdateSubtotal", (e) => {
            this.updateSubtotal(e.target);
        });

        $('#modal-requerimiento-pago').on("click", "button.handleClickGuardarRequerimientoPago", () => {
            this.guardarRequerimientoPago();
        });
        $('#modal-requerimiento-pago').on("click", "button.handleClickRequerimientoPago", () => {
            this.actualizarRequerimientoPago();
        });
        $('#modal-requerimiento-pago').on("change", "select.handleChangeUpdateMoneda", () => {
            this.changeMonedaSelect();
        });
        $('#modal-requerimiento-pago').on("change", "select.handleCheckStatusValue", (e) => {
            this.checkStatusValue(e.currentTarget);
        });
        $('#modal-requerimiento-pago').on("keyup", "input.handleCheckStatusValue", (e) => {
            this.checkStatusValue(e.currentTarget);
        });
        $('#modal-requerimiento-pago').on("keyup", "textarea.handleCheckStatusValue", (e) => {
            this.checkStatusValue(e.currentTarget);
        });
        $('#modal-requerimiento-pago').on("click", "button.handleClickModalListaCuadroDePresupuesto", () => {
            this.modalListaCuadroDePresupuesto();
        });
        $('#modal-requerimiento-pago').on("click", "button.handleClickAdjuntarArchivoCabecera", () => {
            this.modalAdjuntarArchivosCabecera();
        });
        $('#modal-requerimiento-pago').on("click", "button.handleClickAdjuntarArchivoDetalle", (e) => {
            this.modalAdjuntarArchivosDetalle(e.currentTarget);
        });
        $('#listaCuadroPresupuesto').on("click", "button.handleClickSeleccionarCDP", (e) => {
            this.seleccionarCDP(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-requerimiento-pago').on("change", "input.handleChangeAgregarAdjuntoCabecera", (e) => {
            this.agregarAdjuntoRequerimientoPagoCabecera(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-requerimiento-pago').on("click", "button.handleClickDescargarArchivoCabeceraRequerimientoPago", (e) => {
            this.descargarArchivoRequerimientoPagoCabecera(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-requerimiento-pago').on("click", "button.handleClickEliminarArchivoCabeceraRequerimientoPago", (e) => {
            this.eliminarArchivoRequerimientoPagoCabecera(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-requerimiento-pago').on("change", "select.handleChangeCategoriaAdjunto", (e) => {
            this.actualizarCategoriaDeAdjunto(e.currentTarget);
        });

        $('#modal-adjuntar-archivos-requerimiento-pago-detalle').on("change", "input.handleChangeAgregarAdjuntoDetalle", (e) => {
            this.agregarAdjuntoRequerimientoPagoDetalle(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-requerimiento-pago-detalle').on("change", "input.handleClickEliminarArchivoRequerimientoPagoDetalle", (e) => {
            this.eliminarArchivoRequerimientoPagoDetalle(e.currentTarget);
        });
    }

    changeBtnIcon(obj) {

        if (obj.currentTarget.children[0].className == 'fas fa-chevron-right') {

            obj.currentTarget.children[0].classList.replace('fa-chevron-right', 'fa-chevron-down')
        } else {
            obj.currentTarget.children[0].classList.replace('fa-chevron-down', 'fa-chevron-right')
        }
    }

    mostrarListaRequerimientoPago(meOrAll = 'SIN_FILTRO', idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', idGrupo = 'SIN_FILTRO', idDivision = 'SIN_FILTRO', fechaRegistroDesde = 'SIN_FILTRO', fechaRegistroHasta = 'SIN_FILTRO', idEstado = 'SIN_FILTRO') {
        // console.log(meOrAll,idEmpresa,idSede,idGrupo,idDivision,fechaRegistroDesde,fechaRegistroHasta,idEstado);
        let that = this;
        vista_extendida();
        var vardataTables = funcDatatables();
        $tablaListaRequerimientoPago = $('#ListaRequerimientoPago').DataTable({
            'dom': vardataTables[1],
            'buttons': [
                {
                    text: '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo',
                    attr: {

                        id: 'btnNuevoRequerimientoPago'
                    },
                    action: () => {
                        this.nuevoRequerimientoPago();

                    },
                    className: 'btn-success btn-sm'
                },
                {
                    text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : 0',
                    attr: {
                        id: 'btnFiltrosListaRequerimientosElaborados',
                        disabled: true
                    },
                    action: () => {
                        // this.abrirModalFiltrosRequerimientosElaborados();

                    },
                    className: 'btn-default btn-sm'
                }
            ],
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'lista-requerimiento-pago',
                'type': 'POST',
                'data': { 'meOrAll': meOrAll, 'idEmpresa': idEmpresa, 'idSede': idSede, 'idGrupo': idGrupo, 'idDivision': idDivision, 'fechaRegistroDesde': fechaRegistroDesde, 'fechaRegistroHasta': fechaRegistroHasta, 'idEstado': idEstado },
                beforeSend: data => {

                    $("#ListaRequerimientoPago").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },

            },
            'columns': [
                { 'data': 'id_requerimiento_pago', 'name': 'requerimiento_pago.id_requerimiento_pago', 'visible': false },
                { 'data': 'prioridad', 'name': 'adm_prioridad.descripcion', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
                { 'data': 'concepto', 'name': 'concepto' },
                { 'data': 'descripcion_requerimiento_pago_tipo', 'name': 'descripcion_requerimiento_pago_tipo' },
                { 'data': 'fecha_registro', 'name': 'requerimiento_pago.fecha_registro', 'className': 'text-center' },
                { 'data': 'empresa_razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-center' },
                { 'data': 'sede', 'name': 'sis_sede.codigo', 'className': 'text-center' },
                { 'data': 'grupo', 'name': 'sis_grupo.descripcion', 'className': 'text-center' },
                { 'data': 'division', 'name': 'division.descripcion', 'className': 'text-center' },
                { 'data': 'monto_total', 'name': 'requerimiento_pago.monto_total', 'defaultContent': '', 'className': 'text-right' },
                { 'data': 'usuario_nombre_corto', 'name': 'sis_usua.nombre_corto' },
                { 'data': 'nombre_estado', 'name': 'adm_estado_doc.estado_doc' },
                { 'data': 'id_requerimiento_pago', 'name': 'requerimiento_pago.id_requerimiento_pago' }
            ],
            'columnDefs': [

                {
                    'render': function (data, type, row) {
                        return row['termometro'];
                    }, targets: 1
                },
                {
                    'render': function (data, type, row) {
                        return row['simbolo_moneda'].concat(' ', row['monto_total']);
                    }, targets: 10
                },
                {
                    'render': function (data, type, row) {
                        switch (row['id_estado']) {
                            case 1:
                                return '<span class="labelEstado label label-default">' + row['nombre_estado'] + '</span>';
                                break;
                            case 2:
                                return '<span class="labelEstado label label-success">' + row['nombre_estado'] + '</span>';
                                break;
                            case 3:
                                return '<span class="labelEstado label label-warning">' + row['nombre_estado'] + '</span>';
                                break;
                            case 5:
                                return '<span class="labelEstado label label-primary">' + row['nombre_estado'] + '</span>';
                                break;
                            case 7:
                                return '<span class="labelEstado label label-danger">' + row['nombre_estado'] + '</span>';
                                break;
                            default:
                                return '<span class="labelEstado label label-default">' + row['nombre_estado'] + '</span>';
                                break;

                        }
                    }, targets: 12, className: 'text-center'
                },
                {
                    'render': function (data, type, row) {
                        let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                        let containerCloseBrackets = '</div></center>';
                        let btnEditar = '';
                        let btnAnular = '';
                        if (row.id_usuario == auth_user.id_usuario && (row.id_estado == 1 || row.id_estado == 3)) {
                            btnEditar = '<button type="button" class="btn btn-xs btn-warning btnEditarRequerimientoPago handleClickEditarRequerimientoPago" data-id-requerimiento-pago="' + row.id_requerimiento_pago + '" data-codigo-requerimiento-pago="' + row.codigo + '" title="Editar"><i class="fas fa-edit fa-xs"></i></button>';
                            btnAnular = '<button type="button" class="btn btn-xs btn-danger btnAnularRequerimientoPago handleClickAnularRequerimientoPago" data-id-requerimiento-pago="' + row.id_requerimiento_pago + '" data-codigo-requerimiento-pago="' + row.codigo + '" title="Anular"><i class="fas fa-times fa-xs"></i></button>';
                        }
                        let btnVerDetalle = `<button type="button" class="btn btn-xs btn-primary desplegar-detalle handleClickVerDetalleRequerimientoPago" data-toggle="tooltip" data-placement="bottom" title="Ver Detalle" data-id-requerimiento-pago="${row.id_requerimiento_pago}">
                        <i class="fas fa-chevron-down"></i>
                        </button>`;


                        return containerOpenBrackets + btnVerDetalle + btnEditar + btnAnular + containerCloseBrackets;
                    }, targets: 13
                },

            ],
            'initComplete': function () {
                // that.updateContadorFiltroRequerimientosElaborados();

                //Boton de busqueda
                const $filter = $('#ListaRequerimientoPago_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaListaRequerimientoPago.search($input.val()).draw();
                })
                //Fin boton de busqueda

            },
            "drawCallback": function (settings) {
                if ($tablaListaRequerimientoPago.rows().data().length == 0) {
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
                $('#ListaRequerimientoPago_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#ListaRequerimientoPago_filter input').trigger('focus');
                //fin botón búsqueda
                $("#ListaRequerimientoPago").LoadingOverlay("hide", true);
            }
        });
        //Desactiva el buscador del DataTable al realizar una busqueda
        $tablaListaRequerimientoPago.on('search.dt', function () {
            $('#tableDatos_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

        // $('#ListaRequerimientoPago').DataTable().on("draw", function () {
        //     resizeSide();
        // });
    }

    verDetalleRequerimientoPago(obj) {
        let tr = obj.closest('tr');
        var row = $tablaListaRequerimientoPago.row(tr);
        var id = obj.dataset.idRequerimientoPago;
        if (row.child.isShown()) {
            //  This row is already open - close it
            row.child.hide();
            tr.classList.remove('shown');
        }
        else {
            // Open this row
            //    row.child( format(iTableCounter, id) ).show();
            this.buildFormatListaRequerimientosPago(obj, iTableCounter, id, row);
            tr.classList.add('shown');
            // try datatable stuff
            oInnerTable = $('#ListaRequerimientoPago_' + iTableCounter).dataTable({
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

    buildFormatListaRequerimientosPago(obj, table_id, id, row) {
        obj.setAttribute('disabled', true);

        this.obtenerDetalleRequerimientoPago(id).then((res) => {
            obj.removeAttribute('disabled');
            this.construirDetalleRequerimientoPago(table_id, row, res);
        }).catch((err) => {
            console.log(err)
        })
    }

    obtenerDetalleRequerimientoPago(id) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `detalle-requerimiento-pago/${id}`,
                dataType: 'JSON',
                success(response) {
                    resolve(response);
                },
                error: function (err) {
                    reject(err)
                }
            });
        });
    }

    construirDetalleRequerimientoPago(table_id, row, response) {
        var html = '';
        // console.log(response);
        if (response.length > 0) {
            response.forEach(function (element) {
                let cantidadAdjuntosDetalleRequerimiento = 0;
                html += `<tr>
                        <td style="border: none; text-align:center;" data-part-number="${element.part_number}" data-producto-part-number="${element.producto_part_number}">${(element.producto_part_number != null ? element.producto_part_number : (element.part_number != null ? element.part_number : ''))} ${element.tiene_transformacion == true ? '<br><span class="label label-default">Transformado</span>' : ''}</td>
                        <td style="border: none; text-align:left;">${element.producto_codigo != null ? element.producto_codigo : ''}</td>
                        <td style="border: none; text-align:left;">${element.producto_codigo_softlink != null ? element.producto_codigo_softlink : ''}</td>
                        <td style="border: none; text-align:left;">${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion ? element.descripcion : '')}</td>
                        <td style="border: none; text-align:center;">${element.moneda_descripcion != null ? element.moneda_descripcion : ''}</td>
                        <td style="border: none; text-align:center;">${element.cantidad > 0 ? element.cantidad : ''}</td>
                        <td style="border: none; text-align:center;">${(element.precio > 0 ? ((element.moneda_simbolo ? element.moneda_simbolo : ((element.moneda_simbolo ? element.moneda_simbolo : '') + '0.00')) + $.number(element.precio, 2)) : (element.moneda_simbolo ? element.moneda_simbolo : '') + '0.00')}</td>
                        <td style="border: none; text-align:center;">${(parseFloat(element.subtotal) > 0 ? ((element.moneda_simbolo ? element.moneda_simbolo : '') + $.number(element.subtotal, 2)) : ((element.moneda_simbolo ? element.moneda_simbolo : '') + $.number((element.cantidad * element.precio), 2)))}</td>
                        <td style="border: none; text-align:center;">${element.estado_doc != null ? element.estado_doc : ''}</td>
                        <td style="border: none; text-align:center;">${cantidadAdjuntosDetalleRequerimiento > 0 ? `<button type="button" class="btn btn-default btn-xs handleClickVerAdjuntoDetalleRequerimiento" name="btnVerAdjuntoDetalleRequerimiento" title="Ver adjuntos" data-id-detalle-requerimiento-pago="${element.id_requerimiento_pago_detalle}" data-descripcion="${element.producto_descripcion != null ? element.producto_descripcion : (element.descripcion ? element.descripcion : '')}" ><i class="fas fa-paperclip"></i></button>` : ''}</td>
                        </tr>`;
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
                        <th style="border: none; text-align:center;">Estado</th>
                        <th style="border: none; text-align:center;">Adjuntos</th>
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


    resetearFormularioRequerimientoPago() {
        $('#form-requerimiento-pago')[0].reset();
        tempArchivoAdjuntoRequerimientoPagoCabeceraList = [];
        tempArchivoAdjuntoRequerimientoPagoDetalleList = [];
        tempIdArchivoAdjuntoRequerimientoPagoCabeceraToDeleteList = [];
        tempIdArchivoAdjuntoRequerimientoPagoDetalleToDeleteList = [];
        objBotonAdjuntoRequerimientoPagoDetalleSeleccionado = [];
        this.limpiarTabla('ListaDetalleRequerimientoPago');
        this.calcularSubtotal();
        this.calcularTotal();
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value = '';
        this.updateContadorTotalAdjuntosRequerimientoPagoCabecera();
        this.limpiarTabla('listaArchivosRequerimientoPagoCabecera');

    }
    nuevoRequerimientoPago() {
        this.resetearFormularioRequerimientoPago();
        $('#modal-requerimiento-pago').modal({
            show: true,
            backdrop: 'static'
        });
        document.querySelector("div[id='modal-requerimiento-pago'] form[id='form-requerimiento-pago']").setAttribute("type", 'register');
        document.querySelector("div[id='modal-requerimiento-pago'] h3[id='modal-title']").textContent = "Nuevo requerimiento de pago";
        document.querySelector("div[id='modal-requerimiento-pago'] button[id='btnActualizarRequerimientoPago']").classList.add("oculto");
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='fecha_registro']").value = moment().format("YYYY-MM-DD");
    }


    changeOptEmpresaSelect(obj) {
        let idEmpresa = obj.value;
        if (idEmpresa > 0) {
            document.querySelector("div[id='modal-requerimiento-pago'] select[name='sede']").removeAttribute("disabled");
            document.querySelector("div[id='modal-requerimiento-pago'] select[name='grupo']").removeAttribute("disabled");

            this.construirOptSelectSede(idEmpresa);
        } else {

            document.querySelector("div[id='modal-requerimiento-pago'] select[name='sede']").setAttribute("disabled", true);
            document.querySelector("div[id='modal-requerimiento-pago'] select[name='grupo']").setAttribute("disabled", true);
            document.querySelector("div[id='modal-requerimiento-pago'] select[name='division']").setAttribute("disabled", true);
        }


        return false;
    }

    construirOptSelectSede(idEmpresa) {
        this.obtenerSede(idEmpresa).then((res) => {
            this.llenarSelectSede(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    obtenerSede(idEmpresa) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-sedes-por-empresa/${idEmpresa}`,
                dataType: 'JSON',
                success(response) {
                    resolve(response);
                },
                error: function (err) {
                    reject(err)
                }
            });
        });
    }

    llenarSelectSede(array) {

        let selectElement = document.querySelector("div[id='modal-requerimiento-pago'] select[name='sede']");
        if (selectElement.options.length > 0) {
            let i, L = selectElement.options.length - 1;
            for (i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }


        array.forEach(element => {
            let option = document.createElement("option");
            option.text = element.descripcion;
            option.value = element.id_sede;
            if (element.codigo == 'LIMA' || element.codigo == 'Lima') { // default sede lima
                option.selected = true;

            }
            option.setAttribute('data-ubigeo', element.id_ubigeo);
            option.setAttribute('data-name-ubigeo', element.ubigeo_descripcion);
            selectElement.add(option);
        });

        // if (array.length > 0) {
        //     this.updateSedeByPassingElement(selectElement);
        // }

    }


    changeOptGrupoSelect(obj) {
        let idGrupo = obj.value;
        let descripcionGrupo = obj.options[obj.selectedIndex].textContent;
        if (idGrupo > 0) {
            document.querySelector("div[id='modal-requerimiento-pago'] select[name='division']").removeAttribute("disabled");

            this.construirOptSelectDivision(idGrupo);
            if (idGrupo == 3 || descripcionGrupo == 'Proyectos') {
                document.querySelector("div[id='modal-requerimiento-pago'] div[id='contenedor-proyecto']").classList.remove("oculto");
                document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_cc']").value = '';
                document.querySelector("div[id='modal-requerimiento-pago'] input[name='codigo_oportunidad']").value = '';
                document.querySelector("div[id='modal-requerimiento-pago'] div[id='contenedor-cdp']").classList.add("oculto");
            } else {
                document.querySelector("div[id='modal-requerimiento-pago'] div[id='contenedor-proyecto']").classList.add("oculto");

            }
            if (idGrupo == 2 || descripcionGrupo == 'Comercial') {
                document.querySelector("div[id='modal-requerimiento-pago'] div[id='contenedor-cdp']").classList.remove("oculto");
                document.querySelector("div[id='modal-requerimiento-pago'] div[id='contenedor-proyecto']").classList.add("oculto");
                document.querySelector("div[id='modal-requerimiento-pago'] select[name='proyecto']").value = 0;

            } else {
                document.querySelector("div[id='modal-requerimiento-pago'] div[id='contenedor-cdp']").classList.add("oculto");

            }

        } else {

            document.querySelector("div[id='modal-requerimiento-pago'] select[name='division']").setAttribute("disabled", true);
        }
        return false;
    }

    construirOptSelectDivision(idGrupo) {
        this.obtenerDivision(idGrupo).then((res) => {
            this.llenarSelectDivision(res);
        }).catch(function (err) {
            console.log(err)
        })
    }
    obtenerDivision(idGrupo) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-division-por-grupo/${idGrupo}`,
                dataType: 'JSON',
                success(response) {
                    resolve(response);
                },
                error: function (err) {
                    reject(err)
                }
            });
        });
    }

    llenarSelectDivision(array) {
        let selectElement = document.querySelector("div[id='modal-requerimiento-pago'] select[name='division']");
        if (selectElement.options.length > 0) {
            let i, L = selectElement.options.length - 1;
            for (i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }


        array.forEach(element => {
            let option = document.createElement("option");
            option.text = element.descripcion;
            option.value = element.id_division;

            option.setAttribute('data-id-grupo', element.grupo_id);
            selectElement.add(option);
        });
    }

    makeId() {
        let ID = "";
        let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for (let i = 0; i < 12; i++) {
            ID += characters.charAt(Math.floor(Math.random() * 36));
        }
        return ID;
    }


    agregarServicio(data = null) {
        let idFila = data != null && data.id_requerimiento_pago_detalle > 0 ? data.id_requerimiento_pago_detalle : (this.makeId());
        let cantidadAdjuntos = data != null && data.adjunto != null ? data.adjunto.length : '0';
        document.querySelector("tbody[id='body_detalle_requerimiento_pago']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
        <td>    
            <input type="hidden"  class="idEstado" name="idEstado[]">
            <p class="descripcion-partida" title="${(data != null && data.partida != null ? data.partida.codigo : '')}">${(data != null && data.partida != null ? data.partida.descripcion : '(NO SELECCIONADO)')}</p>
            <button type="button" class="btn btn-xs btn-info handleClickCargarModalPartidas" name="partida">Seleccionar</button> 
            <div class="form-group">
                <h5></h5>
                <input type="text" class="partida" name="idPartida[]" value="${(data != null && data.partida != null ? data.partida.id_partida : '')}" hidden>
            </div>
        </td>
        <td>
            <p class="descripcion-centro-costo" title="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.codigo : (data != null && data.centro_costo != null ? data.centro_costo.codigo : '')}">${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.descripcion : data != null && data.centro_costo != null ? data.centro_costo.descripcion : '(NO SELECCIONADO)'} </p>
            <button type="button" class="btn btn-xs btn-primary handleClickCargarModalCentroCostos" name="centroCostos"  ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${data != null && data.centro_costo != null ? data.centro_costo.codigo : ''}" >Seleccionar</button> 
            <div class="form-group">
                <h5></h5>
                <input type="text" class="centroCosto" name="idCentroCosto[]" value="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.id : (data != null && data.centro_costo != null ? data.centro_costo.id_centro_costo : '')}" hidden>
            </div>
        </td>
        <td>(Servicio)<input type="hidden" name="partNumber[]"></td>
        <td>
            <div class="form-group">
                <h5></h5>
                <textarea class="form-control input-sm descripcion handleCheckStatusValue" name="descripcion[]" placeholder="Descripción">${data != null && typeof data.descripcion === 'string' ? data.descripcion : ""}</textarea>
            </div>
        </td>
        <td><select name="unidad[]" class="form-control input-sm oculto" value="17" readOnly><option value="17">Servicio</option></select> Servicio</td>
        <td>
            <div class="form-group">
                <h5></h5>
                <input class="form-control input-sm cantidad text-right handleCheckStatusValue handleBurUpdateSubtotal" type="number" min="1" name="cantidad[]"  placeholder="Cantidad" value="${data != null && typeof data.cantidad === 'string' ? data.cantidad : ""}">
            </div>
        </td>
        <td>
            <div class="form-group">
                <h5></h5>
                <input class="form-control input-sm precio text-right handleCheckStatusValue handleBurUpdateSubtotal" type="number" min="0" name="precioUnitario[]"  placeholder="Precio U." value="${data != null && typeof data.precio_unitario === 'string' ? data.precio_unitario : ""}">
            </div>
        </td>
        <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
        <td>
            <div class="btn-group" role="group">
                <input type="hidden" class="tipoItem" name="tipoItem[]" value="2">
                <input type="hidden" class="idRegister" name="idRegister[]" value="${idFila}">
                <button type="button" class="btn btn-warning btn-xs handleClickAdjuntarArchivoDetalle" data-id="${idFila}" name="btnAdjuntarArchivoItem" title="Adjuntos" >
                    <i class="fas fa-paperclip"></i>
                    <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">${cantidadAdjuntos}</span>    
                </button>
                <button type="button" class="btn btn-danger btn-xs handleClickEliminarItem" name="btnEliminarItem[]" title="Eliminar" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`);

    }


    eliminarItem(obj) {

        Swal.fire({
            title: 'Esta seguro?',
            text: "No podrás revertir esta acción",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'cancelar',
            confirmButtonText: 'Si, eliminar'

        }).then((result) => {
            if (result.isConfirmed) {
                let tr = obj.closest("tr");
                var regExp = /[a-zA-Z]/g;

                if (regExp.test(tr.querySelector("input[name='idRegister[]']").value) == true) {
                    tr.remove();
                    this.calcularSubtotal();
                    this.calcularTotal();
                } else {
                    tr.querySelector("input[class~='idEstado']").value = 7;
                    tr.classList.add("danger", "textRedStrikeHover");
                    tr.querySelector("button[name='btnEliminarItem[]']").setAttribute("disabled", true);
                    this.calcularSubtotal();
                    this.calcularTotal();
                }

                Lobibox.notify('success', {
                    title: false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'El item fue eliminado'
                });
            }
        })
    }

    checkStatusBtnGuardar() {
        if (document.querySelector("tbody[id='body_detalle_requerimiento_pago']").children.length > 0) {
            document.querySelector("div[id='modal-requerimiento-pago'] button[id='btnGuardarRequerimientoPago']").removeAttribute("disabled")
            document.querySelector("div[id='modal-requerimiento-pago'] button[id='btnGuardarRequerimientoPago']").setAttribute("title", "Guardar");
        } else {
            document.querySelector("div[id='modal-requerimiento-pago'] button[id='btnGuardarRequerimientoPago']").setAttribute("disabled", true);
            document.querySelector("div[id='modal-requerimiento-pago'] button[id='btnGuardarRequerimientoPago']").setAttribute("title", "Debe ingresar un item");
        }
    }

    // modal partidas
    cargarModalPartidas(obj) {
        tempObjectBtnPartida = obj.target;
        let id_grupo = document.querySelector("form[id='form-requerimiento-pago'] select[name='grupo']").value;
        let id_proyecto = document.querySelector("form[id='form-requerimiento-pago'] select[name='proyecto']").value;
        let usuarioProyectos = false;
        console.log(gruposUsuario);
        gruposUsuario.forEach(element => {
            if (element.id_grupo == 3) { // proyectos
                usuarioProyectos = true
            }
        });
        if (id_grupo > 0) {
            $('#modal-partidas').modal({
                show: true,
                backdrop: 'true'
            });
            this.listarPartidas(id_grupo, id_proyecto > 0 ? id_proyecto : '');
        } else {
            Swal.fire(
                '',
                'Debe seleccionar un grupo',
                'warning'
            );
        }
    }


    listarPartidas(idGrupo, idProyecto) {
        this.limpiarTabla('listaPartidas');

        this.obtenerListaPartidas(idGrupo, idProyecto).then((res) => {
            this.construirListaPartidas(res);

        }).catch(function (err) {
            console.log(err)
        })
    }

    obtenerListaPartidas(idGrupo, idProyecto) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `mostrar-partidas/${idGrupo}/${idProyecto}`,
                dataType: 'JSON',
                beforeSend: function (data) {
                    var customElement = $("<div>", {
                        "css": {
                            "font-size": "24px",
                            "text-align": "center",
                            "padding": "0px",
                            "margin-top": "-400px"
                        },
                        "class": "your-custom-class"
                    });

                    $('#modal-partidas div.modal-body').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        custom: customElement,
                        imageColor: "#3c8dbc"
                    });
                },
                success(response) {
                    resolve(response);
                },
                fail: function (jqXHR, textStatus, errorThrown) {
                    $('#modal-partidas div.modal-body').LoadingOverlay("hide", true);
                    alert("Hubo un problema al cargar las partidas. Por favor actualice la página e intente de nuevo");
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        });
    }

    construirListaPartidas(data) {

        let html = '';
        let isVisible = '';
        data['presupuesto'].forEach(resup => {
            html += ` 
            <div id='${resup.codigo}' class="panel panel-primary" style="width:100%; overflow: auto;">
                <h5 class="panel-heading handleClickapertura" data-id-presup="${resup.id_presup}" style="margin: 0; cursor: pointer;">
                <i class="fas fa-chevron-right"></i>
                    &nbsp; ${resup.descripcion} 
                </h5>
                <div id="pres-${resup.id_presup}" class="oculto" style="width:100%;">
                    <table class="table table-bordered table-condensed partidas" id="listaPartidas" width="100%" style="font-size:0.9em">
                        <tbody> 
            `;



            data['titulos'].forEach(titulo => {
                html += `
                <tr id="com-${titulo.id_titulo}">
                    <td><strong>${titulo.codigo}</strong></td>
                    <td><strong>${titulo.descripcion}</strong></td>
                    <td class="right ${isVisible}"><strong>S/${Util.formatoNumero(titulo.total, 2)}</strong></td>
                </tr> `;

                data['partidas'].forEach(partida => {
                    if (titulo.codigo == partida.cod_padre) {
                        html += `<tr id="par-${partida.id_partida}">
                            <td style="width:15%; text-align:left;" name="codigo">${partida.codigo}</td>
                            <td style="width:75%; text-align:left;" name="descripcion">${partida.des_pardet}</td>
                            <td style="width:15%; text-align:right;" name="importe_total" class="right ${isVisible}" data-presupuesto-total="${partida.importe_total}" >S/${Util.formatoNumero(partida.importe_total, 2)}</td>
                            <td style="width:5%; text-align:center;"><button class="btn btn-success btn-xs handleClickSelectPartida" data-id-partida="${partida.id_partida}">Seleccionar</button></td>
                        </tr>`;
                    }
                });


            });
            html += `
                    </tbody>
                </table>
            </div>
        </div>`;
        });
        document.querySelector("div[id='listaPartidas']").innerHTML = html;

        $('#modal-partidas div.modal-body').LoadingOverlay("hide", true);

    }

    apertura(idPresup) {
        // let idPresup = e.target.dataset.idPresup;
        if ($("#pres-" + idPresup + " ").hasClass('oculto')) {
            $("#pres-" + idPresup + " ").removeClass('oculto');
            $("#pres-" + idPresup + " ").addClass('visible');
        } else {
            $("#pres-" + idPresup + " ").removeClass('visible');
            $("#pres-" + idPresup + " ").addClass('oculto');
        }
    }

    selectPartida(idPartida) {
        let codigo = $("#par-" + idPartida + " ").find("td[name=codigo]")[0].innerHTML;
        let descripcion = $("#par-" + idPartida + " ").find("td[name=descripcion]")[0].innerHTML;
        // let presupuestoTotal = $("#par-" + idPartida + " ").find("td[name=importe_total]")[0].dataset.presupuestoTotal;
        tempObjectBtnPartida.nextElementSibling.querySelector("input").value = idPartida;
        tempObjectBtnPartida.textContent = 'Cambiar';

        let tr = tempObjectBtnPartida.closest("tr");
        // tr.querySelector("p[class='descripcion-partida']").dataset.idPartida = idPartida;
        tr.querySelector("p[class='descripcion-partida']").textContent = descripcion
        // tr.querySelector("p[class='descripcion-partida']").dataset.presupuestoTotal = presupuestoTotal;
        tr.querySelector("p[class='descripcion-partida']").setAttribute('title', codigo);

        // this.checkStatusValue(tempObjectBtnPartida.nextElementSibling.querySelector("input"));
        $('#modal-partidas').modal('hide');

    }
    // end modal partidas

    // modal centro costo
    cargarModalCentroCostos(obj) {
        tempObjectBtnCentroCostos = obj.target;

        $('#modal-centro-costos').modal({
            show: true
        });
        this.listarCentroCostos();
    }

    listarCentroCostos() {
        this.limpiarTabla('listaCentroCosto');

        this.obtenerCentroCostos().then((res) => {
            this.construirCentroCostos(res);
        }).catch(function (err) {
            console.log(err)
        })
    }

    obtenerCentroCostos() {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `mostrar-centro-costos`,
                dataType: 'JSON',
                beforeSend: function (data) {

                    $('#modal-centro-costos div.modal-body').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success(response) {
                    resolve(response);
                },
                fail: function (jqXHR, textStatus, errorThrown) {
                    $('#modal-centro-costos div.modal-body').LoadingOverlay("hide", true);
                    alert("Hubo un problema al cargar los centro de costo. Por favor actualice la página e intente de nuevo");
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        });
    }

    construirCentroCostos(data) {
        let html = '';
        data.forEach((padre, index) => {
            if (padre.id_padre == null) {
                html += `
                <div id='${index}' class="panel panel-primary" style="width:100%; overflow: auto;">
                <h5 class="panel-heading handleClickapertura" style="margin: 0; cursor: pointer;" data-id-presup="${index}">
                <i class="fas fa-chevron-right"></i>
                    &nbsp; ${padre.descripcion} 
                </h5>
                <div id="pres-${index}" class="oculto" style="width:100%;">
                    <table class="table table-bordered table-condensed partidas" id='listaCentroCosto' width="" style="font-size:0.9em">
                        <thead>
                            <tr>
                            <td style="width:5%"></td>
                            <td style="width:90%"></td>
                            <td style="width:5%"></td>
                            </tr>
                        </thead>
                        <tbody>`;

                data.forEach(hijo => {
                    if (padre.id_centro_costo == hijo.id_padre) {
                        if ((hijo.id_padre > 0) && (hijo.estado == 1)) {
                            if (hijo.nivel == 2) {
                                html += `
                                <tr id="com-${hijo.id_centro_costo}">
                                    <td><strong>${hijo.codigo}</strong></td>
                                    <td><strong>${hijo.descripcion}</strong></td>
                                    <td style="width:5%; text-align:center;"></td>
                                </tr> `;
                            }
                        }
                        data.forEach(hijo3 => {
                            if (hijo.id_centro_costo == hijo3.id_padre) {
                                if ((hijo3.id_padre > 0) && (hijo3.estado == 1)) {
                                    // console.log(hijo3);
                                    if (hijo3.nivel == 3) {
                                        html += `
                                        <tr id="com-${hijo3.id_centro_costo}">
                                            <td>${hijo3.codigo}</td>
                                            <td>${hijo3.descripcion}</td>
                                            <td style="width:5%; text-align:center;">${hijo3.seleccionable ? `<button class="btn btn-success btn-xs handleClickSelectCentroCosto" data-id-centro-costo="${hijo3.id_centro_costo}" data-codigo="${hijo3.codigo}" data-descripcion-centro-costo="${hijo3.descripcion}" >Seleccionar</button>` : ''}</td>
                                        </tr> `;
                                    }
                                }
                                data.forEach(hijo4 => {
                                    if (hijo3.id_centro_costo == hijo4.id_padre) {
                                        console.log(hijo4);
                                        if ((hijo4.id_padre > 0) && (hijo4.estado == 1)) {
                                            if (hijo4.nivel == 4) {
                                                html += `
                                                <tr id="com-${hijo4.id_centro_costo}">
                                                    <td>${hijo4.codigo}</td>
                                                    <td>${hijo4.descripcion}</td>
                                                    <td style="width:5%; text-align:center;">${hijo4.seleccionable ? `<button class="btn btn-success btn-xs handleClickSelectCentroCosto" data-id-centro-costo="${hijo4.id_centro_costo}" data-codigo="${hijo4.codigo}" data-descripcion-centro-costo="${hijo4.descripcion}">Seleccionar</button>` : ''}</td>
                                                </tr> `;
                                            }
                                        }
                                    }
                                });
                            }

                        });
                    }


                });
                html += `
                </tbody>
            </table>
        </div>
    </div>`;
            }
        });
        document.querySelector("div[name='centro-costos-panel']").innerHTML = html;



        $('#modal-centro-costos div.modal-body').LoadingOverlay("hide", true);

    }

    selectCentroCosto(idCentroCosto, codigo, descripcion) {
        // console.log(idCentroCosto);
        tempObjectBtnCentroCostos.nextElementSibling.querySelector("input").value = idCentroCosto;
        tempObjectBtnCentroCostos.textContent = 'Cambiar';

        let tr = tempObjectBtnCentroCostos.closest("tr");
        tr.querySelector("p[class='descripcion-centro-costo']").textContent = descripcion
        tr.querySelector("p[class='descripcion-centro-costo']").setAttribute('title', codigo);
        this.checkStatusValue(tempObjectBtnCentroCostos.nextElementSibling.querySelector("input"));
        $('#modal-centro-costos').modal('hide');
        tempObjectBtnCentroCostos = null;
        // componerTdItemDetalleRequerimiento();
    }
    // end modal centro costo

    changeMonedaSelect() {
        let simboloMonedaPresupuestoUtilizado = document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo
        let allSelectorSimboloMoneda = document.getElementsByName("simboloMoneda");
        if (allSelectorSimboloMoneda.length > 0) {
            allSelectorSimboloMoneda.forEach(element => {
                element.textContent = simboloMonedaPresupuestoUtilizado;
            });
        }
        document.querySelector("div[name='montoMoneda']").textContent = simboloMonedaPresupuestoUtilizado;
        this.calcularPresupuestoUtilizadoYSaldoPorPartida();

    }

    updateSubtotal(obj) {
        let tr = obj.closest("tr");
        let cantidad = parseFloat(tr.querySelector("input[class~='cantidad']").value);
        let precioUnitario = parseFloat(tr.querySelector("input[class~='precio']").value);
        let subtotal = (cantidad * precioUnitario);
        tr.querySelector("span[class='subtotal']").textContent = Util.formatoNumero(subtotal, 2);
        this.calcularTotal();
    }

    calcularSubtotal() {
        let TableTBody = document.querySelector("tbody[id='body_detalle_requerimiento_pago']");
        let childrenTableTbody = TableTBody.children;
        for (let index = 0; index < childrenTableTbody.length; index++) {

            let cantidad = parseFloat(childrenTableTbody[index].querySelector("input[class~='cantidad']").value ? childrenTableTbody[index].querySelector("input[class~='cantidad']").value : 0);
            let precioUnitario = parseFloat(childrenTableTbody[index].querySelector("input[class~='precio']").value ? childrenTableTbody[index].querySelector("input[class~='precio']").value : 0);
            childrenTableTbody[index].querySelector("span[class~='subtotal']").textContent = (cantidad * precioUnitario);

        }
    }


    calcularTotal() {
        let TableTBody = document.querySelector("tbody[id='body_detalle_requerimiento_pago']");
        let childrenTableTbody = TableTBody.children;
        let total = 0;
        for (let index = 0; index < childrenTableTbody.length; index++) {
            // console.log(childrenTableTbody[index]);
            let cantidad = parseFloat(childrenTableTbody[index].querySelector("input[class~='cantidad']").value ? childrenTableTbody[index].querySelector("input[class~='cantidad']").value : 0);
            let precioUnitario = parseFloat(childrenTableTbody[index].querySelector("input[class~='precio']").value ? childrenTableTbody[index].querySelector("input[class~='precio']").value : 0);
            total += (cantidad * precioUnitario);
        }
        document.querySelector("label[name='total']").textContent = Util.formatoNumero(total, 2);
        document.querySelector("input[name='monto_total']").value = total;
        document.querySelector("input[name='monto_total_read_only']").value = Util.formatoNumero(total, 2);
    }

    checkStatusValue(obj) {
        if (obj.value > 0 || obj.value.length > 0) {
            obj.closest('div').classList.remove("has-error");
            if (obj.closest("div").querySelector("span")) {
                obj.closest("div").querySelector("span").remove();
            }
        } else {
            obj.closest('div').classList.add("has-error");
        }
    }

    validarFormularioRequerimientoPago() {
        let continuar = true;
        if (document.querySelector("tbody[id='body_detalle_requerimiento_pago']").childElementCount == 0) {
            Swal.fire(
                '',
                'Ingrese por lo menos un producto/servicio',
                'warning'
            );
            return false;
        }
        if (document.querySelector("input[name='concepto']").value == '') {
            continuar = false;
            if (document.querySelector("input[name='concepto']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Ingrese un concepto/motivo)';
                document.querySelector("input[name='concepto']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("input[name='concepto']").closest('div').classList.add('has-error');
            }
        }
        if (!(document.querySelector("select[name='empresa']").value > 0)) {
            continuar = false;
            if (document.querySelector("select[name='empresa']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una empresa)';
                document.querySelector("select[name='empresa']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='empresa']").closest('div').classList.add('has-error');
            }
        }
        if (!(document.querySelector("select[name='sede']").value > 0)) {
            continuar = false;
            if (document.querySelector("select[name='sede']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una sede)';
                document.querySelector("select[name='sede']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='sede']").closest('div').classList.add('has-error');
            }
        }
        if (!(document.querySelector("select[name='grupo']").value > 0)) {
            continuar = false;
            if (document.querySelector("select[name='grupo']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione un grupo)';
                document.querySelector("select[name='grupo']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='grupo']").closest('div').classList.add('has-error');
            }
        }
        if (!(document.querySelector("select[name='division']").value > 0)) {
            continuar = false;
            if (document.querySelector("select[name='division']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una división)';
                document.querySelector("select[name='division']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='division']").closest('div').classList.add('has-error');
            }
        }

        if (document.querySelector("input[name='id_proveedor']").value == '') {
            continuar = false;
            if (document.querySelector("input[name='razon_social']").closest('div').parentElement.querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione un proveedor)';
                document.querySelector("input[name='razon_social']").closest('div').parentElement.querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("input[name='razon_social']").closest('div').parentElement.classList.add('has-error');
            }
        }
        if (document.querySelector("input[name='id_cuenta_principal_proveedor']").value == '') {
            continuar = false;
            if (document.querySelector("input[name='nro_cuenta_principal_proveedor']").closest('div').parentElement.querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una cuenta bancaria del proveedor)';
                document.querySelector("input[name='nro_cuenta_principal_proveedor']").closest('div').parentElement.querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("input[name='nro_cuenta_principal_proveedor']").closest('div').parentElement.classList.add('has-error');
            }
        }


        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento_pago']").children;
        for (let index = 0; index < tbodyChildren.length; index++) {
            if (!(tbodyChildren[index].querySelector("input[class~='centroCosto']").value > 0)) {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese un centro de costo';
                    tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').querySelector("h5").appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }
            if (!(tbodyChildren[index].querySelector("input[class~='cantidad']").value > 0)) {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese una cantidad';
                    tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').querySelector("h5").appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }
            if (tbodyChildren[index].querySelector("input[class~='precio']").value == '') {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='precio']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese un precio';
                    tbodyChildren[index].querySelector("input[class~='precio']").closest('td').querySelector("h5").appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='precio']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }
            if (tbodyChildren[index].querySelector("textarea[class~='descripcion']")) {
                if (tbodyChildren[index].querySelector("textarea[class~='descripcion']").value == '') {
                    continuar = false;
                    if (tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').querySelector("span") == null) {
                        let newSpanInfo = document.createElement("span");
                        newSpanInfo.classList.add('text-danger');
                        newSpanInfo.textContent = 'Ingrese una descripción';
                        tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').querySelector("h5").appendChild(newSpanInfo);
                        tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                    }
                }


            }
        }
        return continuar;
    }

    actualizarCategoriaDeAdjunto(obj) {
        if (tempArchivoAdjuntoRequerimientoPagoCabeceraList.length > 0) {
            let indice = tempArchivoAdjuntoRequerimientoPagoCabeceraList.findIndex(elemnt => elemnt.id == obj.closest('tr').id);
            tempArchivoAdjuntoRequerimientoPagoCabeceraList[indice].category = parseInt(obj.value) > 0 ? parseInt(obj.value) : 1;
        } else {
            Swal.fire(
                '',
                'Hubo un error inesperado al intentar cambiar la categoría del adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
                'error'
            );
        }
    }

    guardarRequerimientoPago() {

        if (this.validarFormularioRequerimientoPago()) {
            let formData = new FormData($('#form-requerimiento-pago')[0]);

            if (tempArchivoAdjuntoRequerimientoPagoCabeceraList.length > 0) {
                tempArchivoAdjuntoRequerimientoPagoCabeceraList.forEach(element => {
                    formData.append(`archivoAdjuntoRequerimientoPagoCabeceraFile${element.category}[]`, element.file);
                });
            }
            if (tempArchivoAdjuntoRequerimientoPagoDetalleList.length > 0) {
                tempArchivoAdjuntoRequerimientoPagoDetalleList.forEach(element => {
                    formData.append(`archivoAdjuntoRequerimientoPagoDetalle${element.id}[]`, element.file);
                    // if(Number.isInteger(element.id)){
                    // }
                });
            }

            $.ajax({
                type: 'POST',
                url: 'guardar-requerimiento-pago',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                beforeSend: (data) => {

                    var customElement = $("<div>", {
                        "css": {
                            "font-size": "24px",
                            "text-align": "center",
                            "padding": "0px",
                            "margin-top": "-400px"
                        },
                        "class": "your-custom-class",
                        "text": "Guardando requerimiento de pago..."
                    });

                    $('#wrapper-okc').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        custom: customElement,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) => {
                    // console.log(response);
                    if (response.id_requerimiento_pago > 0) {
                        $('#wrapper-okc').LoadingOverlay("hide", true);

                        Lobibox.notify('success', {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: response.mensaje
                        });
                        $("#ListaRequerimientoPago").DataTable().ajax.reload(null, false);
                        this.resetearFormularioRequerimientoPago();

                    } else {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            response.mensaje,
                            'error'
                        );
                    }
                },
                statusCode: {
                    404: function () {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            'Error 404',
                            'Lo sentimos hubo un problema con el servidor, la ruta a la que se quiere acceder para guardar no esta disponible, por favor vuelva a intentarlo más tarde.',
                            'error'
                        );
                    }
                },
                fail: (jqXHR, textStatus, errorThrown) => {
                    $('#wrapper-okc').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar guardar el requerimiento de pago, por favor vuelva a intentarlo',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });

        } else {
            Swal.fire(
                '',
                'Por favor ingrese los datos faltantes en el formulario',
                'warning'
            );
        }
    }


    actualizarRequerimientoPago() {
        if (document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value > 0) {
            if (this.validarFormularioRequerimientoPago()) {
                let formData = new FormData($('#form-requerimiento-pago')[0]);

                if (tempArchivoAdjuntoRequerimientoPagoCabeceraList.length > 0) {
                    tempArchivoAdjuntoRequerimientoPagoCabeceraList.forEach(element => {
                        formData.append(`archivoAdjuntoRequerimientoPagoCabeceraFile${element.category}[]`, element.file);
                    });
                }

                if (tempArchivoAdjuntoRequerimientoPagoDetalleList.length > 0) {
                    tempArchivoAdjuntoRequerimientoPagoDetalleList.forEach(element => {
                        formData.append(`archivoAdjuntoRequerimientoPagoDetalle${element.id}[]`, element.file);
                    });
                }

                formData.append(`idArchivoAdjuntoRequerimientoPagoCabeceraParaElminar`, JSON.stringify(tempIdArchivoAdjuntoRequerimientoPagoCabeceraToDeleteList));
                formData.append(`archivoAdjuntoRequerimientoPagoObject`, JSON.stringify(tempArchivoAdjuntoRequerimientoPagoCabeceraList));

                formData.append(`idArchivoAdjuntoRequerimientoPagoDetalleParaElminar`, JSON.stringify(tempIdArchivoAdjuntoRequerimientoPagoDetalleToDeleteList));


                $.ajax({
                    type: 'POST',
                    url: 'actualizar-requerimiento-pago',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: (data) => {

                        var customElement = $("<div>", {
                            "css": {
                                "font-size": "24px",
                                "text-align": "center",
                                "padding": "0px",
                                "margin-top": "-400px"
                            },
                            "class": "your-custom-class",
                            "text": "Actualizando requerimiento de pago..."
                        });

                        $('#wrapper-okc').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            custom: customElement,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) => {
                        console.log(response);
                        if (response.id_requerimiento_pago > 0) {
                            $('#wrapper-okc').LoadingOverlay("hide", true);

                            Lobibox.notify('success', {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: response.mensaje
                            });
                            $("#ListaRequerimientoPago").DataTable().ajax.reload(null, false);

                        } else {
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                            Swal.fire(
                                '',
                                response.mensaje,
                                'error'
                            );
                        }
                    },
                    statusCode: {
                        404: function () {
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                            Swal.fire(
                                'Error 404',
                                'Lo sentimos hubo un problema con el servidor, la ruta a la que se quiere acceder para actualizar no esta disponible, por favor vuelva a intentarlo más tarde.',
                                'error'
                            );
                        }
                    },
                    fail: (jqXHR, textStatus, errorThrown) => {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un error en el servidor al intentar actualizar el requerimiento de pago, por favor vuelva a intentarlo',
                            'error'
                        );
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });

            } else {
                Swal.fire(
                    '',
                    'Por favor ingrese los datos faltantes en el formulario',
                    'warning'
                );
            }
        } else {
            Swal.fire(
                '',
                'No se encontro un requerimiento de pago seleccionado, vuelva a intentarlo seleccionado un requerimiento de pago editable de la lista',
                'warning'
            );
        }
    }

    modalListaCuadroDePresupuesto() {
        $('#modal-lista-cuadro-presupuesto').modal({
            show: true
        });
        this.listarCuadroPresupuesto();

    }

    listarCuadroPresupuesto() {
        var vardataTables = funcDatatables();
        $tablaListaCuadroPresupuesto = $('#listaCuadroPresupuesto').DataTable({
            'dom': vardataTables[1],
            'buttons': [],
            'language': vardataTables[0],
            'order': [[7, 'desc']],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'lista-cuadro-presupuesto',
                'type': 'POST',
                beforeSend: data => {

                    $("#listaCuadroPresupuesto").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },

            },
            'columns': [
                { 'data': 'codigo_oportunidad', 'name': 'cc_view.codigo_oportunidad', 'className': 'text-center' },
                { 'data': 'descripcion_oportunidad', 'name': 'cc_view.descripcion_oportunidad', 'className': 'text-left' },
                { 'data': 'fecha_creacion', 'name': 'cc_view.fecha_creacion', 'className': 'text-center' },
                { 'data': 'fecha_limite', 'name': 'cc_view.fecha_limite', 'className': 'text-center' },
                { 'data': 'nombre_entidad', 'name': 'cc_view.nombre_entidad', 'className': 'text-left' },
                { 'data': 'name', 'name': 'cc_view.name', 'className': 'text-center' },
                { 'data': 'estado_aprobacion', 'name': 'cc_view.estado_aprobacion', 'className': 'text-center' },
                { 'data': 'id', 'name': 'cc_view.id', }
            ],
            'columnDefs': [


                {
                    'render': function (data, type, row) {
                        let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                        let containerCloseBrackets = '</div></center>';
                        let btnSeleccionar = `<button type="button" class="btn btn-xs btn-success handleClickSeleccionarCDP"  data-id-cc="${row.id}"  data-codigo-oportunidad="${row.codigo_oportunidad}" title="Seleccionar">Seleccionar</button>`;
                        return containerOpenBrackets + btnSeleccionar + containerCloseBrackets;
                    }, targets: 7
                },

            ],
            'initComplete': function () {
                // that.updateContadorFiltroRequerimientosElaborados();

                //Boton de busqueda
                const $filter = $('#listaCuadroPresupuesto_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscarCDP" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscarCDP').trigger('click');
                    }
                });
                $('#btnBuscarCDP').on('click', (e) => {
                    $tablaListaCuadroPresupuesto.search($input.val()).draw();
                })
                //Fin boton de busqueda

            },
            "drawCallback": function (settings) {
                if ($tablaListaCuadroPresupuesto.rows().data().length == 0) {
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
                $('#listaCuadroPresupuesto_filter input').prop('disabled', false);
                $('#btnBuscarCDP').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaCuadroPresupuesto_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaCuadroPresupuesto").LoadingOverlay("hide", true);
            }
        });
        //Desactiva el buscador del DataTable al realizar una busqueda
        $tablaListaCuadroPresupuesto.on('search.dt', function () {
            $('#tableDatos_filter input').prop('disabled', true);
            $('#btnBuscarCDP').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

    }


    seleccionarCDP(obj) {

        if (obj.dataset.idCc > 0) {
            document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_cc']").value = obj.dataset.idCc;
            document.querySelector("div[id='modal-requerimiento-pago'] input[name='codigo_oportunidad']").value = obj.dataset.codigoOportunidad;
        } else {
            Swal.fire(
                '',
                'Lo sentimos hubo un error al intentar obtener el id del cuadro de presupuesto, por favor vuelva a intentarlo',
                'error'
            );
        }
        $('#modal-lista-cuadro-presupuesto').modal('hide');
    }


    editarRequerimientoPago(obj) {
        this.resetearFormularioRequerimientoPago();
        $('#modal-requerimiento-pago').modal({
            show: true,
            backdrop: 'static'
        });
        document.querySelector("div[id='modal-requerimiento-pago'] form[id='form-requerimiento-pago']").setAttribute("type", 'edition');
        document.querySelector("div[id='modal-requerimiento-pago'] h3[id='modal-title']").textContent = "Editar requerimiento de pago";
        document.querySelector("div[id='modal-requerimiento-pago'] button[id='btnGuardarRequerimientoPago']").classList.add("oculto");
        document.querySelector("div[id='modal-requerimiento-pago'] button[id='btnActualizarRequerimientoPago']").classList.remove("oculto");
        document.querySelector("div[id='modal-requerimiento-pago'] button[id='btnActualizarRequerimientoPago']").removeAttribute("disabled");

        if (obj.dataset.idRequerimientoPago > 0) {
            this.cargarRequerimientoPago(obj.dataset.idRequerimientoPago);
        } else {
            Swal.fire(
                '',
                'Lo sentimos no se encontro un ID valido para cargar el requerimiento de pago seleccionado, por favor vuelva a intentarlo',
                'error'
            );
        }
    }

    anularRequerimientoPago(obj) {
        if (obj.dataset.idRequerimientoPago > 0) {
            Swal.fire({
                title: `Esta seguro que desea anular el requerimiento ${obj.dataset.codigoRequerimientoPago}?`,
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'cancelar',
                confirmButtonText: 'Si, eliminar'

            }).then((result) => {
                if (result.isConfirmed) {

                    this.realizarAnulacionRequerimientoPago(obj.dataset.idRequerimientoPago).then((response) => {
                        $("#wrapper-okc").LoadingOverlay("hide", true);
                        console.log(response);

                        if (response.status == 200) {

                            let tr = obj.closest("tr");
                            tr.remove();

                            Lobibox.notify('success', {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: response.mensaje
                            });

                        } else {
                            $("#wrapper-okc").LoadingOverlay("hide", true);

                            Swal.fire(
                                '',
                                response.mensaje,
                                response.tipo_estado
                            );

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
                }
            })
        } else {
            Swal.fire(
                '',
                'Lo sentimos no se encontro un ID valido para cargar el requerimiento de pago seleccionado, por favor vuelva a intentarlo',
                'error'
            );
        }
    }
    realizarAnulacionRequerimientoPago(id) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                url: `anular-requerimiento-pago`,
                data: { 'idRequerimientoPago': id },
                dataType: 'JSON',
                beforeSend: data => {

                    $("#wrapper-okc").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success(response) {
                    resolve(response);
                },
                error: function (err) {
                    reject(err)
                }
            });
        });
    }

    cargarRequerimientoPago(idRequerimientoPago) {
        this.obtenerRequerimientoPago(idRequerimientoPago).then((res) => {
            this.mostrarRequerimientoPago(res);

        }).catch(function (err) {
            console.log(err)
        });
    }

    obtenerRequerimientoPago(id) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `mostrar-requerimiento-pago/${id}`,
                dataType: 'JSON',
                success(response) {
                    resolve(response);
                },
                error: function (err) {
                    reject(err)
                }
            });
        });
    }

    mostrarRequerimientoPago(data) {
        if (data.id_empresa > 0) {
            this.construirOptSelectSede(data.id_empresa);
        }
        if (data.id_grupo > 0) {
            this.construirOptSelectDivision(data.id_grupo);
        }

        document.querySelector("div[id='modal-requerimiento-pago'] select[name='sede']").removeAttribute("disabled");
        document.querySelector("div[id='modal-requerimiento-pago'] select[name='grupo']").removeAttribute("disabled");
        document.querySelector("div[id='modal-requerimiento-pago'] select[name='division']").removeAttribute("disabled");

        const fechaRegistro = moment(data.fecha_registro, 'DD-MM-YYYY').format('YYYY-MM-DD')
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value = data.id_requerimiento_pago;
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_usuario']").value = data.id_usuario;
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_estado']").value = data.id_estado;
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='codigo']").value = data.codigo;
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='concepto']").value = data.concepto;
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='fecha_registro']").value = fechaRegistro;
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_proveedor']").value = data.id_proveedor;
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='razon_social']").value = data.proveedor != null ? data.proveedor.razon_social : '';
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_cuenta_principal_proveedor']").value = data.proveedor != null && data.proveedor.cuenta_contribuyente.length > 0 ? data.proveedor.cuenta_contribuyente[0].id_cuenta_contribuyente : '';
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='nro_cuenta_principal_proveedor']").value = data.proveedor != null && data.proveedor.cuenta_contribuyente.length > 0 ? data.proveedor.cuenta_contribuyente[0].nro_cuenta : '';
        document.querySelector("div[id='modal-requerimiento-pago'] select[name='periodo']").value = data.id_periodo;
        document.querySelector("div[id='modal-requerimiento-pago'] select[name='moneda']").value = data.id_moneda;
        document.querySelector("div[id='modal-requerimiento-pago'] select[name='prioridad']").value = data.id_prioridad;
        document.querySelector("div[id='modal-requerimiento-pago'] textarea[name='comentario']").value = data.comentario;
        document.querySelector("div[id='modal-requerimiento-pago'] select[name='empresa']").value = data.id_empresa;
        document.querySelector("div[id='modal-requerimiento-pago'] select[name='sede']").value = data.id_sede;
        document.querySelector("div[id='modal-requerimiento-pago'] select[name='grupo']").value = data.id_grupo;
        document.querySelector("div[id='modal-requerimiento-pago'] select[name='division']").value = data.id_division;
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='monto_total']").value = data.monto_total;
        document.querySelector("div[id='modal-requerimiento-pago'] input[name='monto_total_read_only']").value = $.number(data.monto_total, 2);

        this.limpiarTabla('ListaDetalleRequerimientoPago');

        data.detalle.forEach(element => {
            this.agregarServicio(element);
            this.calcularSubtotal();
            this.calcularTotal();

        });

        // agregar data adjuntos a variable temporal
        if (data.adjunto.length > 0) {
            (data.adjunto).forEach(element => {
                tempArchivoAdjuntoRequerimientoPagoCabeceraList.push(
                    {
                        'id': element.id_requerimiento_pago_adjunto,
                        'nameFile': element.archivo,
                        'category': element.id_categoria_adjunto,
                        'file': [],
                    }
                );

            });
        }
        this.updateContadorTotalAdjuntosRequerimientoPagoCabecera();

    }



    modalAdjuntarArchivosCabecera() {
        $('#modal-adjuntar-archivos-requerimiento-pago').modal({
            show: true
        });

        this.listarArchivosAdjuntosCabecera();
    }

    listarArchivosAdjuntosCabecera() {
        let idRequerimientoPago = document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value;
        if (parseInt(idRequerimientoPago) > 0) {
            this.getcategoriaAdjunto().then((categoriaAdjuntoList) => {
                this.getAdjuntosRequerimientoPagoCabecera(idRequerimientoPago).then((adjuntoList) => {
                    tempArchivoAdjuntoRequerimientoPagoCabeceraList = [];
                    (adjuntoList).forEach(element => {
                        tempArchivoAdjuntoRequerimientoPagoCabeceraList.push({
                            id: element.id_requerimiento_pago_adjunto,
                            category: element.id_categoria_adjunto,
                            nameFile: element.archivo,
                            file: []
                        });

                    });

                    this.construirTablaAdjuntosRequerimientoPagoCabecera(tempArchivoAdjuntoRequerimientoPagoCabeceraList, categoriaAdjuntoList);
                }).catch(function (err) {
                    console.log(err)
                })
            }).catch(function (err) {
                console.log(err)
            })
        }
    }

    getAdjuntosRequerimientoPagoCabecera(idRequerimientoPago) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-adjuntos-requerimiento-pago-cabecera/${idRequerimientoPago}`,
                dataType: 'JSON',
                success(response) {
                    resolve(response);
                },
                error: function (err) {
                    reject(err)
                }
            });
        });
    }
    getcategoriaAdjunto() {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-categoria-adjunto`,
                dataType: 'JSON',
                success(response) {
                    resolve(response);
                },
                error: function (err) {
                    reject(err)
                }
            });
        });
    }


    construirTablaAdjuntosRequerimientoPagoCabecera(adjuntoList, categoriaAdjuntoList) {
        // console.log(adjuntoList,categoriaAdjuntoList);
        this.limpiarTabla('listaArchivosRequerimientoPagoCabecera');

        let html = '';
        let hasDisableBtnEliminarArchivo = '';
        let estadoActual = document.querySelector("input[name='id_estado']").value;

        if (estadoActual == 1 || estadoActual == 3 || estadoActual == '') {
            if (document.querySelector("input[name='id_usuario']").value == auth_user.id_usuario) { //usuario en sesion == usuario requerimiento
                hasDisableBtnEliminarArchivo = '';
            } else {
                hasDisableBtnEliminarArchivo = 'disabled';
            }
        }

        adjuntoList.forEach(element => {
            html += `<tr id="${element.id}" style="text-align:center">
        <td style="text-align:left;">${element.nameFile}</td>
        <td>
            <select class="form-control handleChangeCategoriaAdjunto" name="categoriaAdjunto" ${hasDisableBtnEliminarArchivo}>
        `;
            categoriaAdjuntoList.forEach(categoria => {
                if (element.category == categoria.id_requerimiento_pago_categoria_adjunto) {
                    html += `<option value="${categoria.id_requerimiento_pago_categoria_adjunto}" selected >${categoria.descripcion}</option>`

                } else {
                    html += `<option value="${categoria.id_requerimiento_pago_categoria_adjunto}">${categoria.descripcion}</option>`
                }
            });
            html += `</select>
        </td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">`;
            if (Number.isInteger(element.id)) {
                html += `<button type="button" class="btn btn-info btn-xs handleClickDescargarArchivoCabeceraRequerimientoPago" name="btnDescargarArchivoCabeceraRequerimientoPago" title="Descargar" data-id="${element.id}" ><i class="fas fa-paperclip"></i></button>`;
            }
            html += `<button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoCabeceraRequerimientoPago" name="btnEliminarArchivoRequerimientoPago" title="Eliminar" data-id="${element.id}" ${hasDisableBtnEliminarArchivo} ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;
        });
        document.querySelector("tbody[id='body_archivos_requerimiento_pago_cabecera']").insertAdjacentHTML('beforeend', html);

    }

    descargarArchivoRequerimientoPagoCabecera(obj) {
        if (tempArchivoAdjuntoRequerimientoPagoCabeceraList.length > 0) {
            tempArchivoAdjuntoRequerimientoPagoCabeceraList.forEach(element => {
                if (element.id == obj.dataset.id) {
                    window.open("/files/necesidades/requerimientos/pago/cabecera/" + element.nameFile);
                }
            });
        }
    }

    eliminarArchivoRequerimientoPagoCabecera(obj) {
        obj.closest("tr").remove();
        tempIdArchivoAdjuntoRequerimientoPagoCabeceraToDeleteList.push(obj.dataset.id);
        tempArchivoAdjuntoRequerimientoPagoCabeceraList = tempArchivoAdjuntoRequerimientoPagoCabeceraList.filter((element, i) => element.id != obj.dataset.id);
        // ArchivoAdjunto.updateContadorTotalAdjuntosRequerimiento();
        this.updateContadorTotalAdjuntosRequerimientoPagoCabecera();

    }

    estaHabilitadoLaExtension(file) {
        let extension = file.name.match(/(?<=\.)\w+$/g)[0].toLowerCase(); // assuming that this file has any extension
        if (extension === 'dwg'
            || extension === 'dwt'
            || extension === 'cdr'
            || extension === 'back'
            || extension === 'backup'
            || extension === 'psd'
            || extension === 'sql'
            || extension === 'exe'
            || extension === 'html'
            || extension === 'js'
            || extension === 'php'
            || extension === 'ai'
            || extension === 'mp4'
            || extension === 'mp3'
            || extension === 'avi'
            || extension === 'mkv'
            || extension === 'flv'
            || extension === 'mov'
            || extension === 'wmv'
        ) {
            return false;
        } else {
            return true;
        }
    }

    agregarAdjuntoRequerimientoPagoCabecera(obj) {
        if (obj.files != undefined && obj.files.length > 0) {
            console.log(obj.files);

            Array.prototype.forEach.call(obj.files, (file) => {

                if (this.estaHabilitadoLaExtension(file) == true) {
                    let payload = {
                        id: this.makeId(),
                        category: 1, //default: otros adjuntos
                        nameFile: file.name,
                        file: file
                    };
                    this.addToTablaArchivosRequerimientoPagoCabecera(payload);

                    tempArchivoAdjuntoRequerimientoPagoCabeceraList.push(payload);
                } else {
                    Swal.fire(
                        'Este tipo de archivo no esta permitido adjuntar',
                        file.name,
                        'warning'
                    );
                }
            });

            this.updateContadorTotalAdjuntosRequerimientoPagoCabecera();


        }
        return false;
    }

    updateContadorTotalAdjuntosRequerimientoPagoCabecera() {
        document.querySelector("span[name='cantidadAdjuntosCabeceraRequerimientoPago']").textContent = tempArchivoAdjuntoRequerimientoPagoCabeceraList.length;
    }


    addToTablaArchivosRequerimientoPagoCabecera(payload) {
        this.getcategoriaAdjunto().then((categoriaAdjuntoList) => {
            this.agregarRegistroEnTablaAdjuntoRequerimientoPagoCabecera(payload, categoriaAdjuntoList);

        }).catch(function (err) {
            console.log(err)
        })
    }

    agregarRegistroEnTablaAdjuntoRequerimientoPagoCabecera(payload, categoriaAdjuntoList) {
        let html = '';
        html = `<tr id="${payload.id}" style="text-align:center">
        <td style="text-align:left;">${payload.nameFile}</td>
        <td>
            <select class="form-control handleChangeCategoriaAdjunto" name="categoriaAdjunto">
        `;
        categoriaAdjuntoList.forEach(element => {
            if (element.id_requerimiento_pago_categoria_adjunto == payload.category) {
                html += `<option value="${element.id_requerimiento_pago_categoria_adjunto}" selected>${element.descripcion}</option>`
            } else {
                html += `<option value="${element.id_requerimiento_pago_categoria_adjunto}">${element.descripcion}</option>`

            }
        });
        html += `</select>
        </td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoCabeceraRequerimientoPago" name="btnEliminarArchivoRequerimientoPago" title="Eliminar" data-id="${payload.id}" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;

        document.querySelector("tbody[id='body_archivos_requerimiento_pago_cabecera']").insertAdjacentHTML('beforeend', html);
    }


    modalAdjuntarArchivosDetalle(obj) {
        $('#modal-adjuntar-archivos-requerimiento-pago-detalle').modal({
            show: true
        });
        objBotonAdjuntoRequerimientoPagoDetalleSeleccionado = obj;
        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago-detalle'] span[id='descripcion']").textContent = (obj.closest('tr').querySelector("textarea[name='descripcion[]']").value).length > 0 ? obj.closest('tr').querySelector("textarea[name='descripcion[]']").value : '';
        this.listarArchivosAdjuntosDetalle(obj.dataset.id);
    }

    listarArchivosAdjuntosDetalle(idRequerimientoPagoDetalle) {
        if (parseInt(idRequerimientoPagoDetalle) > 0) {
            this.getAdjuntosRequerimientoPagoDetalle(idRequerimientoPagoDetalle).then((adjuntoList) => {
                console.log(adjuntoList);
                tempArchivoAdjuntoRequerimientoPagoDetalleList = [];
                (adjuntoList).forEach(element => {
                    tempArchivoAdjuntoRequerimientoPagoDetalleList.push({
                        id: element.id_requerimiento_pago_detalle_adjunto,
                        id_requerimiento_pago_detalle: element.id_requerimiento_pago_detalle,
                        nameFile: element.archivo,
                        file: []
                    });

                });

                this.construirTablaAdjuntosRequerimientoPagoDetalle(tempArchivoAdjuntoRequerimientoPagoDetalleList);
            }).catch(function (err) {
                console.log(err)
            })
        }
    }

    getAdjuntosRequerimientoPagoDetalle(idRequerimientoPagoDetalle) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-adjuntos-requerimiento-pago-detalle/${idRequerimientoPagoDetalle}`,
                dataType: 'JSON',
                success(response) {
                    resolve(response);
                },
                error: function (err) {
                    reject(err)
                }
            });
        });
    }

    construirTablaAdjuntosRequerimientoPagoDetalle(adjuntoList) {
        this.limpiarTabla('listaArchivosRequerimientoPagoDetalle');

        let html = '';
        let hasDisableBtnEliminarArchivo = '';
        let estadoActual = document.querySelector("input[name='id_estado']").value;

        if (estadoActual == 1 || estadoActual == 3 || estadoActual == '') {
            if (document.querySelector("input[name='id_usuario']").value == auth_user.id_usuario) { //usuario en sesion == usuario requerimiento
                hasDisableBtnEliminarArchivo = '';
            } else {
                hasDisableBtnEliminarArchivo = 'disabled';
            }
        }

        adjuntoList.forEach(element => {
            html += `<tr id="${element.id}" style="text-align:center">
        <td style="text-align:left;">${element.nameFile}</td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">`;
            if (Number.isInteger(element.id)) {
                html += `<button type="button" class="btn btn-info btn-xs handleClickDescargarArchivoRequerimientoPagoDetalle" name="btnDescargarArchivoRequerimientoPagoDetalle" title="Descargar" data-id="${element.id}" ><i class="fas fa-paperclip"></i></button>`;
            }
            html += `<button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoRequerimientoPagoDetalle" name="btnEliminarArchivoRequerimientoPagoDetalle" title="Eliminar" data-id="${element.id}" ${hasDisableBtnEliminarArchivo} ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;
        });
        document.querySelector("tbody[id='body_archivos_requerimiento_pago_detalle']").insertAdjacentHTML('beforeend', html);
    }

    agregarAdjuntoRequerimientoPagoDetalle(obj) {
        if (obj.files != undefined && obj.files.length > 0) {
            // console.log(obj.files);

            Array.prototype.forEach.call(obj.files, (file) => {

                if (this.estaHabilitadoLaExtension(file) == true) {
                    let payload = {
                        id: objBotonAdjuntoRequerimientoPagoDetalleSeleccionado.dataset.id,
                        id_requerimiento_pago_detalle: objBotonAdjuntoRequerimientoPagoDetalleSeleccionado.dataset.id,
                        nameFile: file.name,
                        file: file
                    };
                    this.agregarRegistroEnTablaAdjuntoRequerimientoPagoDetalle(payload);

                    tempArchivoAdjuntoRequerimientoPagoDetalleList.push(payload);
                } else {
                    Swal.fire(
                        'Este tipo de archivo no esta permitido adjuntar',
                        file.name,
                        'warning'
                    );
                }
            });

            this.updateContadorTotalAdjuntosRequerimientoPagoDetalle();



        }
        return false;
    }

    updateContadorTotalAdjuntosRequerimientoPagoDetalle() {
        if (typeof objBotonAdjuntoRequerimientoPagoDetalleSeleccionado == 'object') {
            objBotonAdjuntoRequerimientoPagoDetalleSeleccionado.querySelector("span[name='cantidadAdjuntosItem']").textContent = tempArchivoAdjuntoRequerimientoPagoDetalleList.length;
        }

    }


    agregarRegistroEnTablaAdjuntoRequerimientoPagoDetalle(payload) {

        let html = '';
        html = `<tr id="${payload.id}" style="text-align:center">
        <td style="text-align:left;">${payload.nameFile}</td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoRequerimientoPagoDetalle" name="btnEliminarArchivoRequerimientoPagoDetalle" title="Eliminar" data-id="${payload.id}" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;

        document.querySelector("tbody[id='body_archivos_requerimiento_pago_detalle']").insertAdjacentHTML('beforeend', html);
    }

    eliminarArchivoRequerimientoPagoDetalle(obj) {
        obj.closest("tr").remove();
        tempIdArchivoAdjuntoRequerimientoPagoDetalleToDeleteList.push(obj.dataset.id);
        tempArchivoAdjuntoRequerimientoPagoDetalleList = tempArchivoAdjuntoRequerimientoPagoDetalleList.filter((element, i) => element.id != obj.dataset.id);
        this.updateContadorTotalAdjuntosRequerimientoPagoDetalle();

    }

}