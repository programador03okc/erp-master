var vardataTables = funcDatatables();
var cantidadFiltrosActivosCabecera = 0;
var cantidadFiltrosActivosDetalle = 0;
var tempDataProveedorParaPago = [];
let $tablaListaOrdenesElaborados;
let $tablaListaItemsOrdenesElaborados;
var tempArchivoAdjuntoRequerimientoCabeceraList=[];
class ListaOrdenView {
    constructor(listaOrdenCtrl) {
        this.listaOrdenCtrl = listaOrdenCtrl;
        this.filtro = 'SIN_FILTRO';
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
        $('#listaItemsOrden tbody').on("click", "label.handleClickAbrirOrden", (e) => {
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
        $(document).on("change", "select.handleChangeFiltroListaOrdenes", (e) => {
            // this.modalEnviarOrdenAPago(e.currentTarget);
            this.filtro = e.currentTarget.value;

            this.mostrarListaOrdenesElaboradas(e.currentTarget.value)

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
        $('#modal-enviar-solicitud-pago').on("click", "input.handleCkeckPagoCuotas", (e) => {
            this.updateLabelModalEnviarSolicitudPago(e.currentTarget.checked);
        });

        $('#modal-enviar-solicitud-pago').on("change", "select.handleChangeNumeroDeCuotas", (e) => {
            this.updateMontoAPagarEnCuotas();
        });


        $('#listaDestinatariosEncontrados').on("click", "tr.handleClickSeleccionarDestinatario", (e) => {
            this.seleccionarDestinatario(e.currentTarget);
        });

        // $('#listaItemsOrden tbody').on("click", "a.handleClickVerOrdenModal", (e) => {
        //     this.verOrdenModal(e.currentTarget);
        // });
        $('#listaItemsOrden tbody').on("click", "a.handleClickEditarEstadoItemOrden", (e) => {
            this.editarEstadoItemOrden(e.currentTarget);
        });

        $('#listaItemsOrden tbody').on("click", "button.handleClickAbrirOrdenPDF", (e) => {
            this.abrirOrdenPDF(e.currentTarget.dataset.idOrdenCompra);
        });
        $('#listaItemsOrden tbody').on("click", "button.handleClickAbrirOrden", (e) => {
            this.abrirOrden(e.currentTarget.dataset.idOrdenCompra);
        });
        $('#listaItemsOrden tbody').on("click", "button.handleClickDocumentosVinculados", (e) => {
            this.documentosVinculados(e.currentTarget);
        });

        // $('#modal-filtro-lista-ordenes-elaboradas').on("change", "select.handleChangeUpdateValorFiltroOrdenesElaboradas", (e) => {
        //     this.updateValorFiltroOrdenesElaboradas();
        // });

        // $('#modal-filtro-lista-ordenes-elaboradas').on("change", "select.handleChangeFiltroEmpresa", (e) => {
        //     this.handleChangeFiltroEmpresa(e);
        // });


        // $('#modal-filtro-lista-ordenes-elaboradas').on("click", "input[type=checkbox]", (e) => {
        //     this.estadoCheckFiltroOrdenesElaboradasCabecera(e);
        // });

        // $('#modal-filtro-lista-ordenes-elaboradas').on('hidden.bs.modal', () => {
        //     this.updateValorFiltroOrdenesElaboradas();
        //     if (this.updateContadorFiltroOrdenesElaboradas() == 0) {
        //         // this.obtenerListaOrdenesElaboradas('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');
        //     } else {
        //         // this.obtenerListaOrdenesElaboradas(this.ActualParametroTipoOrdenCabecera, this.ActualParametroEmpresaCabecera, this.ActualParametroSedeCabecera, this.ActualParametroFechaDesdeCabecera, this.ActualParametroFechaHastaCabecera, this.ActualParametroEstadoCabecera);
        //     }
        // });


        // $('#modal-filtro-lista-items-orden-elaboradas').on("change", "select.handleChangeFiltroEmpresa", (e) => {
        //     this.handleChangeFiltroEmpresa(e);
        // });
        // $('#modal-filtro-lista-items-orden-elaboradas').on("click", "input[type=checkbox]", (e) => {
        //     this.estadoCheckFiltroOrdenesElaboradasDetalle(e);
        // });
        // $('#modal-filtro-lista-items-orden-elaboradas').on('hidden.bs.modal', () => {
        //     this.updateValorFiltroDetalleOrdenesElaboradas();
        //     if (this.updateContadorFiltroDetalleOrdenesElaboradas() == 0) {
        //         // this.obtenerListaDetalleOrdenesElaboradas('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');

        //     } else {
        //         // this.obtenerListaDetalleOrdenesElaboradas(this.ActualParametroEmpresaDetalle, this.ActualParametroSedeDetalle, this.ActualParametroFechaDesdeDetalle, this.ActualParametroFechaHastaDetalle, this.ActualParametroEstadoDetalle);

        //     }
        // });
        $(document).on('click','.adjuntar-archivos', (e)=> {
            tempArchivoAdjuntoRequerimientoCabeceraList=[];
            $(":file").filestyle('clear');
            this.limpiarTabla('adjuntosCabecera');
            var data_id = e.currentTarget.dataset.id,
                data_codigo = e.currentTarget.dataset.codigo,
                data_id_moneda = e.currentTarget.dataset.idMoneda;
            $('#modal-adjuntar-orden [name=id_orden]').val(data_id);
            $('#modal-adjuntar-orden [name=id_moneda]').val(data_id_moneda);
            $('#modal-adjuntar-orden [name=codigo_orden]').val(e.currentTarget.dataset.codigo);
            $('#modal-adjuntar-orden .codigo').text(data_codigo);
            $('#modal-adjuntar-orden .codigo').css('color','cadetblue');
            $('#modal-adjuntar-orden').modal('show');
            this.obteneAdjuntosOrden(data_id).then((res) => {

                let htmlAdjunto = '';
                // console.log(res.length);
                if (res.length > 0) {
                    (res).forEach(element => {

                        tempArchivoAdjuntoRequerimientoCabeceraList.push(
                            {
                                'id':element.id_adjunto,
                                'category':element.categoria_adjunto_id,
                                'fecha_emision':element.fecha_emision,
                                'monto_total': element.monto_total,
                                'id_moneda': element.id_moneda,
                                'nro_comprobante':(element.nro_comprobante !=null && element.nro_comprobante.length > 0?element.nro_comprobante:""),
                                'nameFile':element.archivo,
                                'accion':'',
                                'file': null
                        }
                        );

                            htmlAdjunto+= '<tr id="'+element.id_adjunto+'">'
                                htmlAdjunto+='<td>'
                                    htmlAdjunto+='<a href="/files/logistica/comporbantes_proveedor/'+element.archivo+'" target="_blank">'+element.archivo+'</a>'
                                htmlAdjunto+='</td>'

                                htmlAdjunto+='<td>'
                                    htmlAdjunto+='<span name="fecha_emision_text">'+element.fecha_emision+'</span><input type="date" class="form-control handleChangeFechaEmision oculto" name="fecha_emision" placeholder="Fecha emisión"  value="'+element.fecha_emision+'">'
                                htmlAdjunto+='</td>'

                                htmlAdjunto+='<td>'
                                    htmlAdjunto+='<span name="nro_comprobante_text">'+(element.nro_comprobante !=null && element.nro_comprobante.length > 0?element.nro_comprobante:"")+'</span><input type="text" class="form-control handleChangeNroComprobante oculto" name="nro_comprobante"  placeholder="Nro comprobante" value="'+element.nro_comprobante+'">'
                                htmlAdjunto+='</td>'

                                htmlAdjunto+='<td>'
                                    htmlAdjunto+=''+element.descripcion_categoria_adjunto+''
                                htmlAdjunto+='</td>'
                                htmlAdjunto+='<td>'
                                    htmlAdjunto+='<div style="display:flex;"><button type="button" class="btn btn-sm btn-warning boton handleClickEditarAdjuntoProveedor" title="Editar" data-id-adjunto="'+element.id_adjunto+'" '+(![27,5,122,14,17,3].includes(auth_user.id_usuario)?'disables':'')+'> <i class="fas fa-edit"></i> </button>'
                                    htmlAdjunto+='<button type="button" class="btn btn-sm btn-danger boton handleClickAnularAdjuntoProveedor" title="Anular" data-id-adjunto="'+element.id_adjunto+'" '+(![27,5,122,14,17,3].includes(auth_user.id_usuario)?'disables':'')+'> <i class="fas fa-trash"></i> </button></div>'
                                htmlAdjunto+='</td>'
                            htmlAdjunto+= '</tr>'

                    });
                }else{
                    htmlAdjunto = `<tr>
                    <td style="text-align:center;" colspan="3">Sin adjuntos para mostrar</td>
                    </tr>`;
                }
                $('#form-adjunto-orden #body_adjuntos_logisticos').html(htmlAdjunto)


            }).catch(function (err) {
                console.log(err)
            })

            this.obteneAdjuntosPago(data_id).then((res) => {

                let htmlPago = '';
                console.log(res.data);
                if (res.data.length > 0) {
                    (res.data).forEach(element => {

                            htmlPago+= '<tr id="'+element.id_orden+'">'

                                element.adjuntos.forEach(nombreAdjunto => {
                                    htmlPago+='<td>'
                                        htmlPago+='<a href="/files/tesoreria/pagos/'+nombreAdjunto+'" target="_blank">'+nombreAdjunto+'</a>'
                                    htmlPago+='</td>'

                                });
                            htmlPago+= '</tr>'

                    });
                }else{
                    htmlPago = `<tr>
                    <td style="text-align:center;" colspan="3">Sin adjuntos para mostrar</td>
                    </tr>`;
                }
                $('#form-adjunto-orden #body_adjuntos_pago').html(htmlPago)


            }).catch(function (err) {
                console.log(err)
            })
        });

        $(document).on("change", "input.handleChangeAgregarAdjuntoRequerimientoCompraCabecera", (e) => {
            this.agregarAdjuntoRequerimientoCabeceraCompra(e.currentTarget);
        });
        $(document).on("click", "button.handleClickEliminarArchivoCabeceraRequerimientoCompra", (e) => {
            this.eliminarAdjuntoRequerimientoCompraCabecera(e.currentTarget);
        });
        $(document).on("submit", "#form-adjunto-orden", (e) => {
            e.preventDefault();

            this.guardarAdjuntos();
        });

        $(document).on("change", "input.handleChangeFechaEmision", (e) => {
            this.actualizarFechaEmisionDeAdjunto(e.currentTarget);
        });
        $(document).on("change", "input.handleChangeNroComprobante", (e) => {
            this.actualizarNroComprobanteDeAdjunto(e.currentTarget);
        });
        $(document).on("change", "input.handleChangeMontoTotalComprobante", (e) => {
            this.actualizarMontoTotalComprobanteDeAdjunto(e.currentTarget);
        });
        $(document).on("click", "button.handleClickEditarAdjuntoProveedor", (e) => {
            this.editarAdjuntoProveedor(e.currentTarget);
        });
        $(document).on("click", "button.handleClickAnularAdjuntoProveedor", (e) => {
            this.anularAdjuntoProveedor(e.currentTarget);
        });


    }

    limpiarTabla(idElement) {
        let nodeTbodyList = document.querySelectorAll("table[id='" + idElement + "'] tbody");
        nodeTbodyList.forEach(element => {
            if (element != null) {
                while (element.children.length > 0) {
                    element.removeChild(element.lastChild);
                }
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
        // if (this.updateContadorFiltroOrdenesElaboradas() == 0) {
            // this.obtenerListaOrdenesElaboradas('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');
            this.mostrarListaOrdenesElaboradas();

        // }
    }
    tipoVistaPorItem() {
        document.querySelector("button[id='btnTipoVistaPorItemPara'").classList.add('active');
        document.querySelector("button[id='btnTipoVistaPorCabecera'").classList.remove('active');
        document.querySelector("div[id='contenedor-tabla-nivel-cabecera']").classList.add('oculto');
        document.querySelector("div[id='contenedor-tabla-nivel-item']").classList.remove('oculto');
        // if (this.updateContadorFiltroDetalleOrdenesElaboradas() == 0) {
            // this.obtenerListaDetalleOrdenesElaboradas('SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO', 'SIN_FILTRO');
            this.mostrarListaItemsOrdenesElaboradas();
        // }
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
                    <td style="border: none;">${element.moneda_simbolo}${$.number(element.precio, 2,".",",")}</td>
                    <td style="border: none;">${element.moneda_simbolo}${$.number((element.cantidad * element.precio), 2,".",",")}</td>
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
        var row = $tablaListaOrdenesElaborados.row(tr);
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
            // console.log(res);
            obj.removeAttribute('disabled');
            this.construirDetalleOrdenElaboradas(table_id, row, res);
        }).catch((err) => {
            console.log(err)
        })
    }

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

        // document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_total_orden']").value = '';
        // document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_a_pagar']").value = '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] span[id='condicion_de_envio_pago']").textContent="";

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

            document.querySelector("select[name='numero_de_cuotas']").removeAttribute("disabled");
            document.querySelector("input[name='pagoEnCuotasCheckbox']").removeAttribute("disabled");
        tempArchivoAdjuntoRequerimientoCabeceraList=[];
        $(":file").filestyle('clear');
        this.limpiarTabla('adjuntosCabecera');
        this.limpiarTabla('historialEnviosAPagoLogistica');
        $('#form-enviar_solicitud_pago')[0].reset();
        document.querySelector("table[id='historialEnviosAPagoLogistica'] span[name='estadoHistorialEnvioAPagoLogistica']").textContent= '';
        document.querySelector("table[id='historialEnviosAPagoLogistica'] span[name='sumaMontoTotalPagado']").textContent= '';
        document.querySelector("div[id='modal-enviar-solicitud-pago'] textarea[name='comentario']").value= '';

        document.querySelector("div[id='modal-enviar-solicitud-pago'] span[id='codigo_orden']").textContent = '';
        this.limpiarFormEnviarOrdenAPago();
        this.restablecerValoresPorDefectoFormEnviarOrdenAPago();

        document.querySelector("div[id='modal-enviar-solicitud-pago'] span[id='codigo_orden']").textContent = obj.dataset.codigoOrden;

        $.ajax({
            type: 'GET',
            url: `calcular-prioridad/${obj.dataset.idOrdenCompra}`,
            dataType: 'JSON',
            beforeSend: (data) => {
        },
            success(response) {
                document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_prioridad']").value = response.prioridad_id;
            },
            error: function (err) {
                reject(err)
            }
        });




        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_orden_compra']").value = obj.dataset.idOrdenCompra;

        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_total_orden']").setAttribute("data-monto-total-orden",obj.dataset.montoTotalOrden);
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_total_orden']").value = $.number(obj.dataset.montoTotalOrden,2,".",",");
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_a_pagar']").value =parseFloat(obj.dataset.montoTotalOrden)>0 ?(parseFloat(obj.dataset.montoTotalOrden)).toFixed(2):0;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='numero_de_cuotas']").value =(parseInt(obj.dataset.numeroDeCuotas));

        // document.querySelector("div[id='modal-enviar-solicitud-pago'] div[name='simboloMoneda']").textContent = obj.dataset.simboloMonedaOrden;
        $( "div[name*='simboloMoneda']" ).text(obj.dataset.simboloMonedaOrden);
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_moneda']").value =obj.dataset.idMonedaOrden;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='simbolo_moneda']").value =obj.dataset.simboloMonedaOrden;

        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_proveedor']").value = obj.dataset.idProveedor;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_cuenta_contribuyente']").value = obj.dataset.idCuentaPrincipal;
        document.querySelector("div[id='modal-enviar-solicitud-pago'] textarea[name='comentario']").value = obj.dataset.comentarioPago != null ? obj.dataset.comentarioPago : '';

        // this.updateLabelModalEnviarSolicitudPago((obj.dataset.tienePagoEnCuotas === "true"));
        this.updateLabelModalEnviarSolicitudPago(JSON.parse((obj.dataset.tienePagoEnCuotas).toLowerCase()));

        if (obj.dataset.estadoPago == 8) {
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

                console.log(obj.dataset);
                obtenerContribuyente(obj.dataset.idContribuyentePago);

                obtenerCuentasBancariasContribuyente(obj.dataset.idContribuyentePago, (parseInt(obj.dataset.idCuentaContribuyentePago)>0?obj.dataset.idCuentaContribuyentePago:null ));
            }
        } else {
            this.obtenerContribuyentePorIdProveedor(obj.dataset.idProveedor,  obj.dataset.idCuentaContribuyentePago);
        }

        // this.obtenerMontosParaPago(obj.dataset.idOrdenCompra);

        this.obteneAdjuntosOrden(obj.dataset.idOrdenCompra).then((res) => {

            let htmlAdjunto = '';
            // console.log(res);
            if (res.length > 0) {
                (res).forEach(element => {

                    tempArchivoAdjuntoRequerimientoCabeceraList.push(
                        {
                            'id':element.id_adjunto,
                            'category':element.categoria_adjunto_id,
                            'fecha_emision':element.fecha_emision,
                            'nro_comprobante':(element.nro_comprobante !=null && element.nro_comprobante.length > 0?element.nro_comprobante:""),
                            'id_moneda':element.id_moneda,
                            'monto_total':element.monto_total,
                            'nameFile':element.archivo,
                            'accion':'',
                            'file':[element.id_adjunto]
                    }
                    );

                        htmlAdjunto+= '<tr id="'+element.id_adjunto+'">'
                            htmlAdjunto+='<td>'
                                htmlAdjunto+='<a href="/files/logistica/comporbantes_proveedor/'+element.archivo+'" target="_blank">'+element.archivo+'</a>'
                            htmlAdjunto+='</td>'

                            htmlAdjunto+='<td>'
                                htmlAdjunto+='<span name="fecha_emision_text">'+element.fecha_emision+'</span><input type="date" class="form-control handleChangeFechaEmision oculto" name="fecha_emision" placeholder="Fecha emisión"  value="'+element.fecha_emision+'">'
                            htmlAdjunto+='</td>'

                            htmlAdjunto+='<td>'
                                htmlAdjunto+='<span name="nro_comprobante_text">'+(element.nro_comprobante !=null && element.nro_comprobante.length > 0?element.nro_comprobante:"")+'</span><input type="text" class="form-control handleChangeNroComprobante oculto" name="nro_comprobante"  placeholder="Nro comprobante" value="'+element.nro_comprobante+'">'
                            htmlAdjunto+='</td>'

                            htmlAdjunto+='<td>'
                                htmlAdjunto+=''+element.descripcion_categoria_adjunto+''
                            htmlAdjunto+='</td>'

                            htmlAdjunto+='<td>'
                            htmlAdjunto+=''+(element.simbolo_moneda!=null ? element.simbolo_moneda:'') + (element.monto_total !=null ? element.monto_total:'')
                            htmlAdjunto+='</td>'

                            htmlAdjunto+='<td>'
                                htmlAdjunto+='<div style="display:flex;"><button type="button" class="btn btn-sm btn-warning boton handleClickEditarAdjuntoProveedor" title="Editar" data-id-adjunto="'+element.id_adjunto+'" '+(![27,5,122,14,17,3].includes(auth_user.id_usuario)?'disables':'')+'> <i class="fas fa-edit"></i> </button>'
                                htmlAdjunto+='<button type="button" class="btn btn-sm btn-danger boton handleClickAnularAdjuntoProveedor" title="Anular" data-id-adjunto="'+element.id_adjunto+'" '+(![27,5,122,14,17,3].includes(auth_user.id_usuario)?'disables':'')+'> <i class="fas fa-trash"></i> </button></div>'
                            htmlAdjunto+='</td>'
                        htmlAdjunto+= '</tr>'

                });
            }else{
                htmlAdjunto = `<tr>
                <td style="text-align:center;" colspan="3">Sin adjuntos para mostrar</td>
                </tr>`;
            }
            $('#form-enviar_solicitud_pago #body_adjuntos_logisticos').html(htmlAdjunto)


        }).catch(function (err) {
            console.log(err)
        })

        this.obteneHistorialDeEnviosAPagoEnCuotas(obj.dataset.idOrdenCompra).then((res) => {
            // console.log(res);
            let htmlTable = '';

            let sumaMontoTotalMontoCuota=0;

            console.log(res);
            if (res.hasOwnProperty('detalle') && res.detalle.length > 0) {
                let contadorCuota=0;
                (res.detalle).forEach((element,index) => {
                    if(element.id_estado !=7){
                        contadorCuota++;
                        sumaMontoTotalMontoCuota+=parseFloat(element.monto_cuota);
                    let enlaceAdjunto=[];
                        htmlTable+= '<tr id="'+element.id_pago_cuota_detalle+'">'
                            htmlTable+='<td>'
                                htmlTable+= contadorCuota;
                            htmlTable+='</td>'

                            htmlTable+='<td>'
                                htmlTable+= $.number(element.monto_cuota,2,".",",");
                            htmlTable+='</td>'

                            htmlTable+='<td>'
                                htmlTable+= element.observacion??'';
                            htmlTable+='</td>'

                            htmlTable+='<td>'
                                htmlTable+= element.fecha_registro
                            htmlTable+='</td>'
                            htmlTable+='<td>'

                                if(element.adjuntos.length >0){
                                    (element.adjuntos).forEach(adjunto => {
                                        // enlaceAdjunto.push(`<a href="/files/logistica/comporbantes_proveedor/${adjunto.archivo}" target="_blank">${adjunto.archivo}</a>`);
                                        enlaceAdjunto.push( `<a data-toggle="collapse" href="#collapse${adjunto.id_adjunto}" aria-expanded="false" aria-controls="collapse${adjunto.id_adjunto}">
                                        ${adjunto.archivo}</a>
                                        <i class="fas fa-caret-left"></i>
                                        <div class="collapse" id="collapse${adjunto.id_adjunto}">

                                        <dl>
                                        <dt>Archivo</dt>
                                        <dd><a href="/files/logistica/comporbantes_proveedor/${adjunto.archivo}" target="_blank">Descargar</a></dd>
                                        <dt>Nro Comprobante</dt>
                                        <dd>${adjunto.nro_comprobante}</dd>
                                        <dt>Fecha emisión</dt>
                                        <dd>${adjunto.fecha_emision}</dd>
                                        <dt>Monto</dt>
                                        <dd>${adjunto.moneda !=null?adjunto.moneda.simbolo:''} ${adjunto.monto_total=null?adjunto.monto_total:''}</dd>
                                        </dl>
                                        </div>`);
                                    });
                                }

                                htmlTable+=enlaceAdjunto.toString().replace(",","<br>");
                            htmlTable+='</td>'
                        htmlTable+= '</tr>'
                    }
                });
                // console.log(obj.dataset.numeroDeEnviosAPago);
                if(obj.dataset.numeroDeEnviosAPago>0){
                    document.querySelector("select[name='numero_de_cuotas']").setAttribute("disabled",true);
                    document.querySelector("input[name='pagoEnCuotasCheckbox']").setAttribute("disabled",true);
                }

            }else{
                htmlTable = `<tr>
                <td style="text-align:center;" colspan="5">Sin data para mostrar</td>
                </tr>`;

            }
            $('#form-enviar_solicitud_pago #body_historial_de_envios_a_pago_en_cuotas').html(htmlTable)

            document.querySelector("table[id='historialEnviosAPagoLogistica'] span[name='sumaMontoTotalPagado']").textContent= sumaMontoTotalMontoCuota;

            if(parseFloat(sumaMontoTotalMontoCuota) == parseFloat(document.querySelector("input[name='monto_total_orden']").dataset.montoTotalOrden)){
                document.querySelector("table[id='historialEnviosAPagoLogistica'] span[name='estadoHistorialEnvioAPagoLogistica']").textContent= "Cuotas completadas, Orden pagada";
            }else{
                document.querySelector("table[id='historialEnviosAPagoLogistica'] span[name='estadoHistorialEnvioAPagoLogistica']").textContent= '';
            }



        }).catch(function (err) {
            console.log(err)
        })

        this.updateMontoAPagarEnCuotas();

        $('#modal-enviar-solicitud-pago').modal({
            show: true,
            backdrop: 'static'
        });
    }

    updateLabelModalEnviarSolicitudPago(tienePagoEnCuotas){

        if(tienePagoEnCuotas ===true){
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='pagoEnCuotasCheckbox']").checked = true;
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_a_pagar']").setAttribute("readOnly",true);
            document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='numero_de_cuotas']").removeAttribute("disabled");
            document.querySelector("div[id='modal-enviar-solicitud-pago'] span[id='condicion_de_envio_pago']").textContent = "(Pago en cuotas)";
            document.querySelector("div[id='modal-enviar-solicitud-pago'] div[id='group-historialEnviosAPagoLogistica']").removeAttribute("hidden");
            document.querySelector("div[id='modal-enviar-solicitud-pago'] div[id='group-adjuntosLogisticosRegistrados']").setAttribute("hidden",true);
        }else{
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='pagoEnCuotasCheckbox']").checked = false;
            document.querySelector("div[id='modal-enviar-solicitud-pago'] span[id='condicion_de_envio_pago']").textContent = "";
            document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='numero_de_cuotas']").setAttribute("disabled",true);
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_a_pagar']").removeAttribute("readOnly");
            document.querySelector("div[id='modal-enviar-solicitud-pago'] div[id='group-historialEnviosAPagoLogistica']").setAttribute("hidden",true);
            document.querySelector("div[id='modal-enviar-solicitud-pago'] div[id='group-adjuntosLogisticosRegistrados']").removeAttribute("hidden");

        }
    }

    updateMontoAPagarEnCuotas(){
        let numeroDeCuotas = document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='numero_de_cuotas']").value??0;
        // console.log(numeroDeCuotas);
        if (numeroDeCuotas > 1){
            let cuota= parseFloat(document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_total_orden']").dataset.montoTotalOrden) / parseInt(numeroDeCuotas);
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_a_pagar']").value=(cuota);
        }

        if (numeroDeCuotas == 1){
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_a_pagar']").removeAttribute("readonly");
        }else{
            document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_a_pagar']").setAttribute("readonly",true);

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


    obtenerContribuyentePorIdProveedor(idProveedor, idCuenta=null) {
        this.getContribuyentePorIdProveedor(idProveedor).then((res) => {
            // console.log(res);
            if (res.tipo_estado == 'success') {
                tempDataProveedorParaPago = res.data;
                this.llenarInputsDeDestinatario(res.data,idCuenta);
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


    llenarInputsDeDestinatario(data,idCuenta=null) {
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
            obtenerCuentasBancariasContribuyente(data.id_contribuyente, (idCuenta > 0? idCuenta:null));
        }
    }


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
        let menseje = [];

        if (document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_contribuyente']").value == '' && document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_persona']").value == '') {
            menseje.push('Debe seleccionar una persona o un contribuyente');
        } else {
            if (document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value == '') {
                menseje.push('Debe seleccionar una cuanta bancaria');
            } else {
                continuar = true;
            }
        }
        if (( document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='numero_de_cuotas']").value >1) && (parseInt(document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='numero_de_cuotas']").value) == (parseInt(document.querySelector("div[id='modal-enviar-solicitud-pago'] table[id='historialEnviosAPagoLogistica'] tbody").childElementCount)))){
            menseje.push('No se puede superar el limite de cuota establecida');
            continuar = false;
        }
        if (( document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='numero_de_cuotas']").value ==1) && (parseFloat(document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_a_pagar']").value) > parseFloat(document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_total_orden']").dataset.montoTotalOrden)) ){
            menseje.push('No se puede enviar un monto mayor al total de la orden');
            continuar = false;

        }
        if (( document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='numero_de_cuotas']").value ==1)
        && (
            parseFloat((document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_a_pagar']").value).replace(',','')) +
            parseFloat(document.querySelector("div[id='modal-enviar-solicitud-pago'] span[name='sumaMontoTotalPagado']").textContent)
            > parseFloat(document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_total_orden']").dataset.montoTotalOrden))){
            menseje.push('El "monto a enviar" más(+) "la suma de las cutas" supera el monto total de la orden');
            continuar = false;

        }

        if (menseje.length > 0) {
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

        let selec_cuenta = $('#form-enviar_solicitud_pago').find('select[name="id_cuenta"]').val();

        if (parseInt(selec_cuenta)>0) {

            if (this.validarFormularioEnvioOrdenAPago() && selec_cuenta) {

                let formData = new FormData($('#form-enviar_solicitud_pago')[0]);
                if(tempArchivoAdjuntoRequerimientoCabeceraList.length>0){
                    formData.append(`archivoAdjuntoRequerimientoObject`, JSON.stringify(tempArchivoAdjuntoRequerimientoCabeceraList));
                    formData.append(`pagoEnCuotasCheckbox`, document.querySelector("input[name='pagoEnCuotasCheckbox']").checked);
                    tempArchivoAdjuntoRequerimientoCabeceraList.forEach(element => {
                        formData.append(`archivo_adjunto_list[]`, element.file);
                });
                }
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
                            Swal.fire(
                                '',
                                response.mensaje,
                                'error'
                            );
                        }
                    },
                    // statusCode: {
                    //     404: function () {
                    //         $('#modal-enviar-solicitud-pago .modal-content').LoadingOverlay("hide", true);
                    //         Swal.fire(
                    //             'Error 404',
                    //             'Lo sentimos hubo un problema con el servidor, la ruta a la que se quiere acceder para guardar no esta disponible, por favor vuelva a intentarlo más tarde.',
                    //             'error'
                    //         );
                    //     }
                    // },
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

        }else{
            Swal.fire(
                'Informativo',
                'Debe seleccionar una cuanta bancaria',
                'warning'
            )
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
        let option = `<option value="" selected disabled>Elija una opción</option>`;
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
                                        option += `
                                        <option
                                            data-nro-cuenta="${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : ''}"
                                            data-nro-cci="${element.nro_cci != null && element.nro_cci != "" ? element.nro_cci : ''}"
                                            data-tipo-cuenta="${element.tipo_cuenta != null ? element.tipo_cuenta.descripcion : ''}"
                                            data-banco="${element.banco != null && element.banco.contribuyente != null ? element.banco.contribuyente.razon_social : ''}"
                                            data-moneda="${element.moneda != null ? element.moneda.descripcion : ''}"
                                            value="${element.id_cuenta_bancaria}">${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : (element.nro_cci != null && element.nro_cci != "" ? (element.nro_cci + " (CCI)") : "")}
                                        </option>`;
                                        document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").insertAdjacentHTML('beforeend', option);
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
                                        option += `
                                        <option
                                            data-nro-cuenta="${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : ''}"
                                            data-nro-cci="${element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != "" ? element.nro_cuenta_interbancaria : ''}"
                                            data-tipo-cuenta="${element.tipo_cuenta != null ? element.tipo_cuenta.descripcion : ''}"
                                            data-banco="${element.banco != null && element.banco.contribuyente != null ? element.banco.contribuyente.razon_social : ''}"
                                            data-moneda="${element.moneda != null ? element.moneda.descripcion : ''}"
                                            value="${element.id_cuenta_contribuyente}">${element.nro_cuenta != null && element.nro_cuenta != "" ? element.nro_cuenta : (element.nro_cuenta_interbancaria != null && element.nro_cuenta_interbancaria != "" ? (element.nro_cuenta_interbancaria + " (CCI)") : "")}
                                        </option>`;
                                        document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").insertAdjacentHTML('beforeend', option);

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

        let idCuentaEnvioPago = parseInt(document.querySelector("div[id='modal-enviar-solicitud-pago'] select[name='id_cuenta']").value);


        if (obj.dataset.idPersona > 0) {
            obtenerCuentasBancariasPersona(obj.dataset.idPersona);
        } else if (obj.dataset.idContribuyente > 0) {
            obtenerCuentasBancariasContribuyente(obj.dataset.idContribuyente,(idCuentaEnvioPago >0 ?idCuentaEnvioPago:null));
        } else {

            Swal.fire(
                'Obtener cuenta bancaria',
                'Hubo un problema. no se encontró un id persona o id contribuyente valido para poder obtener las cuentas bancarias',
                'error'
            );

        }
    }
    // ###============ Fin enviar orden a pago ============###


    mostrarListaOrdenesElaboradas(filtro = 'SIN_FILTRO') {

        let that = this;
        let fecha_actual = new Date();
        let fecha_inicio = "01-01-"+fecha_actual.getFullYear();
        fecha_actual = moment().format("DD-MM-YYYY");

        vista_extendida();
        var vardataTables = funcDatatables();
        // const button_filtro = (array_accesos.find(element => element === 287)?{
        //         text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : 0',
        //         attr: {
        //             id: 'btnFiltrosListaOrdenesElaboradas',
        //             disabled: true
        //         },
        //         action: () => {
        //             // this.abrirModalFiltrosRequerimientosElaborados();

        //         },
        //         className: 'btn-default btn-sm'
        //     }:[]),
          const  button_descargar_excel = (array_accesos.find(element => element === 244)?{
                text: '<span class="far fa-file-excel" aria-hidden="true"></span> Descargar',
                attr: {
                    id: 'btnDescargarListaOrdenesElaboradasExcel',
                    disabled: false

                },
                action: () => {
                    this.exportarListaOrdenesElaboradasNivelCabeceraExcel();

                },
                className: 'btn-default btn-sm'
            }:[]);
        $tablaListaOrdenesElaborados = $('#listaOrdenes').DataTable({
            'dom': vardataTables[1],
            'buttons': [button_descargar_excel],
            'language': vardataTables[0],
            'order': [6, 'desc'],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'lista-ordenes-elaboradas',
                'type': 'POST',
                'data': {
                    'filtro': filtro,
                    'fecha_inicio':fecha_inicio,
                    'fecha_actual':fecha_actual,
                },
                beforeSend: data => {
                    $("#listaOrdenes").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
            },
            'columns': [
                {
                    'data': 'codigo','className': 'text-center',
                    render: function (data, type, row) {
                        return `<label class="lbl-codigo handleClickAbrirOrden" title="Ir a orden" data-id-orden="${row.id}">${row.codigo}</label>`;
                    }
                },
                { 'data': 'codigo_softlink', 'name': 'codigo_softlink' },
                {
                'data': 'data_codigo_requerimiento','className': 'text-center', 'defaultContent':""
                // render: function (data, type, row) {
                    // if (data != null && data.length > 0) {
                    //     return (data).map(e => (`<a href="/necesidades/requerimiento/elaboracion/index?id=${e.id_requerimiento}" target="_blank" title="Abrir Requerimiento">${e.codigo_requerimiento}</a>`)).join(",");
                    // } else {
                    //     return 'Sin Código';
                    // }
                // }
            },
                {
                    'data': 'data_codigo_oportunidad','className': 'text-center', 'defaultContent':""
                    // render: function (data) {
                        // if (data != null && data.length > 0) {

                        //     return data.map(e => e.id_cc >0 ?(e.cc_codigo_oportunidad):'').join(",");
                        // } else {
                        //     return 'Sin Código';
                        // }

                    // }
                },
                { 'data': 'descripcion_sede_empresa', 'name': 'descripcion_sede_empresa','className': 'text-center' },
                { 'data': 'simbolo_moneda', 'name': 'simbolo_moneda','className': 'text-center','searchable':false },
                { 'data': 'fecha_emision', 'name': 'fecha_emision','className': 'text-center' },
                { 'data': 'fecha_llegada',
                    render: function (data, type, row) {
                        return row.fecha_llegada !=null?(row.fecha_llegada + " / " + row.dias_restantes+' días'):'';
                    }
                },
                { 'data': 'data_atencion_logistica', 'className': 'text-center', 'searchable': false, 'defaultContent':"Sin determinar"
                    // render: function (data) {
                    //     if (data != null && data.length > 0) {
                    //         return data.map(e => (e.atencion_logistica)).join(", ");
                    //     } else {
                    //         return 'Sin determinar';
                    //     }


                    // }
                },
                { 'data': 'razon_social_proveedor',
                    render: function (data, type, row) {
                        return row.razon_social_proveedor + " RUC:" + row.nro_documento_proveedor;
                    }
                },
                { 'data': 'condicion', 'name': 'condicion','searchable':false },
                { 'data': 'razon_social_proveedor',
                    render: function (data, type, row) {
                        return `<a class="handleClickEditarEstadoOrden" data-id-estado-orden-compra="${row.id_estado}" data-codigo-orden="${row.codigo}" data-id-orden-compra="${row.id}" style="cursor:pointer;">${row.descripcion_estado}</a>`;
                    }
                },
                { 'data': 'descripcion_estado_pago', 'name': 'descripcion_estado_pago','className': 'text-center' },
                { 'data': 'monto_total', 'className': 'text-right',
                    render: function (data, type, row) {
                        return row.simbolo_moneda + $.number(row.monto_total,2,".",",");
                    }
                },
                {
                    'data': 'data_importe_oportunidad', 'defaultContent':"-"
                    // render: function (data) {
                        // if (data != null && data.length > 0) {

                        //     return data.map(e => e.id_cc >0 ?(e.cc_moneda_oportunidad=='s'?('S/'+$.number(e.cc_importe_oportunidad,2)):('$'+$.number(e.cc_importe_oportunidad,2))):'').join(",");
                        // } else {
                        //     return 'Sin Código';
                        // }

                    // }
                },
                {
                    'searchable': false,'render':
                        function (data, type, row, meta) {
                            // let cantidadRequerimientosConEstadosPorRegularizarOenPausa = 0;
                            // if(row.data_requerimiento !=null && row.data_requerimiento.length>0){
                            //     (row.data_requerimiento).forEach(element => {
                            //         if([38,39].includes(element.id_estado)==true){
                            //             cantidadRequerimientosConEstadosPorRegularizarOenPausa++;
                            //         }
                            //     });

                            // }

                            let containerOpenBrackets = '<div class="btn-group btn-group-xs" role="group" style="margin-bottom: 5px;display: flex;flex-direction: row;flex-wrap: nowrap;">';
                            let btnImprimirOrden = (array_accesos.find(element => element === 252)?'<button type="button" class="btn btn-sm btn-warning boton handleClickAbrirOrdenPDF" title="Abrir orden PDF"  data-toggle="tooltip" data-placement="bottom" data-id-orden-compra="' + row.id + '"  data-id-pago=""> <i class="fas fa-file-pdf"></i> </button>':'');

                            let btnAnularOrden = '';
                            if (row.fecha_ultimo_ingreso_almacen != null || [5,6,8,9, 10].includes(row.estado_pago) ==true) {
                                btnAnularOrden = (array_accesos.find(element => element === 248)?'<button type="button" class="btn btn-sm btn-default boton" name="btnAnularOrden" title="Anular orden" data-codigo-orden="' + row.codigo + '" data-id-orden-compra="' + row.id + '" disabled ><i class="fas fa-backspace fa-xs"></i></button>':'');
                            } else {
                                btnAnularOrden = (array_accesos.find(element => element === 248)?'<button type="button" class="btn btn-sm btn-danger boton handleClickAnularOrden" name="btnAnularOrden" title="Anular orden" data-codigo-orden="' + row.codigo + '" data-id-orden-compra="' + row.id + '"><i class="fas fa-backspace fa-xs"></i></button>':'');
                            }
                            let btnVerDetalle = (array_accesos.find(element => element === 245)?`<button type="button" class="ver-detalle btn btn-sm btn-primary boton handleCliclVerDetalleOrden" data-toggle="tooltip" data-placement="bottom" title="Ver Detalle" data-id="${row.id}">
                                                <i class="fas fa-chevron-down"></i>
                                                </button>`:'');
                            let btnEnviarAPago = (array_accesos.find(element => element === 247)?`<button type="button" class="btn btn-sm btn-${([5, 6, 8, 9].includes((row.estado_pago)) ? 'success' : 'info')} boton handleClickModalEnviarOrdenAPago" name="btnEnviarOrdenAPago" ${row.tiene_pago_en_cuotas == true?'style="background-color:purple""':''} title="${([5, 6, 8,9].includes((row.estado_pago)) ? 'Ya se envió a pago' : 'Enviar a pago?')}"
                                data-id-orden-compra="${row.id ?? ''}"
                                data-codigo-orden="${row.codigo ?? ''}"
                                data-monto-total-orden="${row.monto_total ?? ''}"
                                data-id-moneda-orden="${row.id_moneda ?? ''}"
                                data-simbolo-moneda-orden="${row.simbolo_moneda ?? ''}"
                                data-id-proveedor="${row.id_proveedor ?? ''}"
                                data-id-cuenta-principal="${row.id_cta_principal ?? ''}"
                                data-estado-pago="${row.estado_pago ?? ''}"
                                data-id-prioridad-pago="${row.id_prioridad_pago ?? ''}"
                                data-id-tipo-destinatario-pago="${row.id_tipo_destinatario_pago ?? '2'}"
                                data-id-cuenta-contribuyente-pago="${row.id_cta_principal ?? ''}"
                                data-id-contribuyente-pago="${row.id_contribuyente ?? ''}"
                                data-tiene-pago-en-cuotas="${JSON.parse((row.tiene_pago_en_cuotas)) ?? false}"
                                data-numero-de-cuotas="${(row.numero_de_cuotas) ?? 0}"
                                data-numero-envios-a-pago="${(row.numero_envios_a_pago) ?? 0}"

                                data-id-persona-pago="${row.id_persona_pago ?? ''}"
                                data-id-cuenta-persona-pago="${row.id_cuenta_persona_pago ?? ''}"
                                data-comentario-pago="${row.comentario_pago ?? ''}" >
                                    <i class="fas fa-money-check-alt fa-xs"></i>
                                </button>`:'');

                            let btnAdjuntar = (array_accesos.find(element => element === 249)?`<button type="button"  class="btn btn-default adjuntar-archivos" data-toggle="tooltip" title="Adjuntar archivos" data-codigo="${row.codigo}" data-id="${row.id}" data-codigo="${row.codigo}" data-id-moneda="${row.id_moneda}" ><i class="fas fa-paperclip fa-xs"></i></button>`:'');
                            let containerCloseBrackets = '</div>';
                            return (containerOpenBrackets + btnVerDetalle + btnImprimirOrden + btnEnviarAPago + btnAnularOrden + btnAdjuntar + containerCloseBrackets);

                        }
                }
            ],

            'columnDefs': [


            ],
            'initComplete': function () {
                // that.updateContadorFiltroRequerimientosElaborados();

                //Boton de busqueda
                const $filter = $('#listaOrdenes_filter');
                const $input = $filter.find('input');

                const selectFiltro= `<select class="form-control input-sm ml-4 handleChangeFiltroListaOrdenes" id="selectFiltroListaOrden" style="margin-left: 1rem;">
                    <option value="SIN_FILTRO" >Todo</option>
                    <option value="ORDENES_SIN_ENVIAR_A_PAGO">Ordenes sin envío a pago</option>
                    <option value="ORDENES_AUTORIZADAS_PARA_PAGO">Ordenes autorizadas para pago</option>
                    </select>`;
                document.querySelector("div[id='listaOrdenes_wrapper'] div[class='dt-buttons btn-group']").insertAdjacentHTML('afterbegin', selectFiltro);
                document.querySelector("select[id='selectFiltroListaOrden']").value=that.filtro;

                $filter.append('<button id="btnBuscarOrden" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');


                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscarOrden').trigger('click');
                    }
                });
                $('#btnBuscarOrden').on('click', (e) => {
                    $tablaListaOrdenesElaborados.search($input.val()).draw();
                })
                //Fin boton de busqueda

                let html_fechas = `

                <input type="text" class="form-control date-picker input-sm" size="10" id="fecha-inicio"
                value="`+fecha_inicio+`"/> <input type="text" class="form-control date-picker input-sm" size="10" id="txtOrdenPendienteFechaFin"
                    value="`+fecha_actual+`"/> `;
                $('#listaOrdenes_wrapper .dt-buttons').append(html_fechas);

                $('input.date-picker').datepicker({
                    language: "es",
                    orientation: "bottom auto",
                    format: 'dd-mm-yyyy',
                    autoclose: true
                });
            },
            "drawCallback": function (settings) {

                if ($tablaListaOrdenesElaborados.rows().data().length == 0) {
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
                $('#listaOrdenes_filter input').prop('disabled', false);
                $('#btnBuscarOrden').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaOrdenes_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaOrdenes").LoadingOverlay("hide", true);
            },
            "createdRow": function (row, data, dataIndex) {

                $(row.childNodes[8]).css('background-color', '#b4effd');
                // $(row.childNodes[8]).css('font-weight', 'bold');


            }
        });
        //Desactiva el buscador del DataTable al realizar una busqueda
        $tablaListaOrdenesElaborados.on('search.dt', function () {
            $('#tableDatos_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
    }




    mostrarListaItemsOrdenesElaboradas() {
        let that = this;
        vista_extendida();
        var vardataTables = funcDatatables();
        // const button_filtro = (array_accesos.find(element => element === 288)?{
        //         text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : 0',
        //         attr: {
        //             id: 'btnFiltrosListaItemsOrdenesElaboradas',
        //             disabled: true
        //         },
        //         action: () => {
        //             // this.abrirModalFiltrosRequerimientosElaborados();

        //         },
        //         className: 'btn-default btn-sm'
        //     }:[]),
           const button_descargar_excel= (array_accesos.find(element => element === 251)?{
                text: '<span class="far fa-file-excel" aria-hidden="true"></span> Descargar',
                attr: {
                    id: 'btnDescargarListaItemsOrdenesElaboradasExcel',
                    disabled: false

                },
                action: () => {
                    this.exportarListaOrdenesElaboradasNivelDetalleExcel();

                },
                className: 'btn-default btn-sm'
            }:[]);
        $tablaListaItemsOrdenesElaborados = $('#listaItemsOrden').DataTable({
            'dom': vardataTables[1],
            'buttons': [button_descargar_excel],
            'language': vardataTables[0],
            'order': [[15, 'desc']],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'lista-items-ordenes-elaboradas',
                'type': 'POST',
                // 'data': { 'meOrAll': meOrAll, 'idEmpresa': idEmpresa, 'idSede': idSede, 'idGrupo': idGrupo, 'idDivision': idDivision, 'fechaRegistroDesde': fechaRegistroDesde, 'fechaRegistroHasta': fechaRegistroHasta, 'idEstado': idEstado },
                beforeSend: data => {
                    $("#listaItemsOrden").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
            },
            'columns': [
                {
                    'data': 'codigo_orden','className': 'text-center',
                    render: function (data, type, row) {
                        return `<label class="lbl-codigo handleClickAbrirOrden" title="Ir a orden" data-id-orden="${row.id_orden}">${row.codigo_orden}</label>`;
                    }
                },
                {
                    'data': 'codigo_requerimiento','className': 'text-center',
                    render: function (data,type, row) {
                        if (row.codigo_requerimiento != null && row.codigo_requerimiento.length > 0) {
                            return `<a href="/necesidades/requerimiento/elaboracion/index?id=${row.id_requerimiento}" target="_blank" title="Abrir Requerimiento">${row.codigo_requerimiento}</a>`;
                        } else {
                            return 'Sin Código';
                        }
                    }
                },
                { 'data': 'codigo_softlink', 'name': 'codigo_softlink' ,'className': 'text-center'},
                { 'data': 'concepto_requerimiento', 'name': 'concepto_requerimiento','className': 'text-center' },
                { 'data': 'razon_social_cliente', 'name': 'razon_social_cliente' },
                { 'data': 'razon_social_proveedor', 'name': 'razon_social_proveedor' },
                { 'data': 'descripcion_subcategoria', 'name': 'descripcion_subcategoria' },
                { 'data': 'descripcion_categoria', 'name': 'descripcion_categoria' },
                { 'data': 'codigo_producto', 'name': 'codigo_producto' },
                { 'data': 'part_number_producto', 'name': 'part_number_producto' },
                { 'data': 'cod_softlink_producto', 'name': 'cod_softlink_producto' },
                { 'data': 'descripcion_producto', 'name': 'descripcion_producto' },
                { 'data': 'cantidad', 'name': 'cantidad' },
                { 'data': 'abreviatura_unidad_medida_det_orden', 'name': 'abreviatura_unidad_medida_det_orden' },
                { 'data': 'precio', 'className': 'text-right',
                render: function (data, type, row) {
                    return row.simbolo_moneda_orden + $.number(row.precio,2,".",",");
                    }
                },
                { 'data': 'cc_fila_precio', 'className': 'text-right',
                render: function (data, type, row) {
                    return (row.cc_moneda =='s'?'S/':(row.cc_moneda=='d'?'$':'')) + $.number(row.cc_fila_precio,2,".",",");
                    }
                },
                { 'data': 'fecha_emision', 'name': 'fecha_emision','className': 'text-center' },
                { 'data': 'plazo_entrega', 'name': 'plazo_entrega','className': 'text-center' },
                { 'data': 'fecha_ingreso_almacen', 'name': 'fecha_ingreso_almacen' ,'className': 'text-center'},
                // { 'data': 'tiempo_atencion_proveedor', 'name': 'tiempo_atencion_proveedor' },
                { 'data': 'descripcion_sede_empresa', 'name': 'descripcion_sede_empresa' },
                { 'data': 'descripcion_estado', 'className': 'text-center',
                render: function (data, type, row) {
                    let estadoDetalleOrdenHabilitadasActualizar = [1, 2, 3, 4, 5, 6, 15];
                    if (estadoDetalleOrdenHabilitadasActualizar.includes(row.id_estado) == true) {
                        return `<a class="handleClickEditarEstadoItemOrden" data-id-estado-detalle-orden-compra="${row.id_estado}" data-id-orden-compra="${row.id_orden}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_producto}" style="cursor: pointer;" title="Cambiar Estado de Item">${row.descripcion_estado}</a>`;
                    } else {
                        return `<span class="" data-id-estado-detalle-orden-compra="${row.id_estado}" data-id-orden-compra="${row.id_orden}" data-id-detalle-orden-compra="${row.id_detalle_orden}" data-codigo-item="${row.codigo_producto}" style="cursor: default;">${row.descripcion_estado}</span>`;
                    }

                    }
                },
                {
                    'render':
                        function (data, type, row, meta) {

                            let containerOpenBrackets = '<div class="btn-group btn-group-xs" role="group" style="margin-bottom: 5px;display: flex;flex-direction: row;flex-wrap: nowrap;">';
                            let btnImprimirOrden = (array_accesos.find(element => element === 246)?'<button type="button" class="btn btn-sm btn-warning boton handleClickAbrirOrdenPDF" name="btnGenerarOrdenRequerimientoPDF" title="Abrir orden PDF" data-id-requerimiento="' + row.id_requerimiento + '"  data-codigo-requerimiento="' + row.codigo_requerimiento + '" data-id-orden-compra="' + row.id_orden + '"><i class="fas fa-file-download fa-xs"></i></button>':'');
                            let btnDocumentosVinculados = (array_accesos.find(element => element === 253)?'<button type="button" class="btn btn-sm btn-primary boton handleClickDocumentosVinculados" name="btnDocumentosVinculados" title="Ver documentos vinculados" data-id-requerimiento="' + row.id_requerimiento + '"  data-codigo-requerimiento="' + row.codigo_requerimiento + '" data-id-orden-compra="' + row.id_orden + '"><i class="fas fa-folder fa-xs"></i></button>':'');
                            let containerCloseBrackets = '</div>';

                            return (containerOpenBrackets + btnImprimirOrden + btnDocumentosVinculados + containerCloseBrackets);


                        }
                }
            ],

            'columnDefs': [


            ],
            'initComplete': function () {

                //Boton de busqueda
                const $filter = $('#listaItemsOrden_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscarItemOrden" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscarItemOrden').trigger('click');
                    }
                });
                $('#btnBuscarItemOrden').on('click', (e) => {
                    $tablaListaItemsOrdenesElaborados.search($input.val()).draw();
                })
                //Fin boton de busqueda

            },
            "drawCallback": function (settings) {
                if ($tablaListaItemsOrdenesElaborados.rows().data().length == 0) {
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
                $('#listaItemsOrden_filter input').prop('disabled', false);
                $('#btnBuscarItemOrden').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaItemsOrden_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaItemsOrden").LoadingOverlay("hide", true);
            },
            "createdRow": function (row, data, dataIndex) {

                $(row.childNodes[18]).css('background-color', '#b4effd');

            }
        });
        //Desactiva el buscador del DataTable al realizar una busqueda
        $tablaListaItemsOrdenesElaborados.on('search.dt', function () {
            $('#tableDatos_filter input').prop('disabled', true);
            $('#btnBuscarItemOrden').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
    }


    exportarListaOrdenesElaboradasNivelCabeceraExcel() {
        window.open(`exportar-lista-ordenes-elaboradas-nivel-cabecera-excel/${this.filtro}`);
    }
    exportarListaOrdenesElaboradasNivelDetalleExcel() {
        window.open('exportar-lista-ordenes-elaboradas-nivel-detalle-excel');
    }

    agregarAdjuntoRequerimientoCabeceraCompra(obj){
        if (obj.files != undefined && obj.files.length > 0) {
            // console.log(obj.files);
            // if((obj.files.length + tempArchivoAdjuntoRequerimientoCabeceraList.length)>1){
            //     Swal.fire(
            //         '',
            //         'Solo puedes subir un máximo de 1 archivos',
            //         'warning'
            //     );
            // }else{
                Array.prototype.forEach.call(obj.files, (file) => {
                    // console.log(file);
                    if (this.estaHabilitadoLaExtension(file) == true) {
                        let payload = {
                            id: this.makeId(),
                            category: 2, //default: factura
                            fecha_emision: moment().format('YYYY-MM-DD'),
                            nro_comprobante: '',
                            id_moneda: document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_moneda']").value >0?document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='id_moneda']").value: (document.querySelector("div[id='modal-adjuntar-orden'] input[name='id_moneda']").value>0?document.querySelector("div[id='modal-adjuntar-orden'] input[name='id_moneda']").value:''),
                            monto_total: document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='monto_a_pagar']").value,
                            nameFile: file.name,
                            accion: 'GUARDAR',
                            file: file
                        };
                        this.addToTablaArchivosRequerimientoCabecera(payload);

                        tempArchivoAdjuntoRequerimientoCabeceraList.push(payload);
                        console.log(tempArchivoAdjuntoRequerimientoCabeceraList);
                        // console.log(payload);
                    } else {
                        Swal.fire(
                            'Este tipo de archivo no esta permitido adjuntar',
                            file.name,
                            'warning'
                        );
                    }
                });

            // }


        }
        this.calcTamañoTotalAdjuntoLogisticoParaSubir();

        return false;

    }

    actualizarFechaEmisionDeAdjunto(obj) {

        if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
            let indice = tempArchivoAdjuntoRequerimientoCabeceraList.findIndex(elemnt => elemnt.id == obj.closest('tr').id);
            tempArchivoAdjuntoRequerimientoCabeceraList[indice].fecha_emision = obj.value;
            if (tempArchivoAdjuntoRequerimientoCabeceraList[indice].id > 0) {

            }
            var regExp = /[a-zA-Z]/g; //expresión regular
            if (regExp.test(tempArchivoAdjuntoRequerimientoCabeceraList[indice].id) == false) {
                tempArchivoAdjuntoRequerimientoCabeceraList[indice].accion = 'ACTUALIZAR';
            } else {
                tempArchivoAdjuntoRequerimientoCabeceraList[indice].accion = 'GUARDAR';
            }

        } else {
            Swal.fire(
                '',
                'Hubo un error inesperado al intentar cambiar la categoría del adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
                'error'
            );
        }
    }
    actualizarMontoTotalComprobanteDeAdjunto(obj) {

        if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
            let indice = tempArchivoAdjuntoRequerimientoCabeceraList.findIndex(elemnt => elemnt.id == obj.closest('tr').id);
            tempArchivoAdjuntoRequerimientoCabeceraList[indice].monto_total = obj.value;
            if (tempArchivoAdjuntoRequerimientoCabeceraList[indice].id > 0) {

            }
            var regExp = /[a-zA-Z]/g; //expresión regular
            if (regExp.test(tempArchivoAdjuntoRequerimientoCabeceraList[indice].id) == false) {
                tempArchivoAdjuntoRequerimientoCabeceraList[indice].accion = 'ACTUALIZAR';
            } else {
                tempArchivoAdjuntoRequerimientoCabeceraList[indice].accion = 'GUARDAR';
            }

        } else {
            Swal.fire(
                '',
                'Hubo un error inesperado al intentar cambiar el monto del adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
                'error'
            );
        }
    }

    editarAdjuntoProveedor(obj){
        obj.closest("tr").querySelector("input[name='fecha_emision']").classList.remove("oculto");
        obj.closest("tr").querySelector("span[name='fecha_emision_text']").classList.add("oculto");
        obj.closest("tr").querySelector("input[name='nro_comprobante']").classList.remove("oculto");
        obj.closest("tr").querySelector("span[name='nro_comprobante_text']").classList.add("oculto");
    }

    anularAdjuntoProveedor(obj){
        if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
            let indice = tempArchivoAdjuntoRequerimientoCabeceraList.findIndex(elemnt => elemnt.id == obj.closest('tr').id);
            tempArchivoAdjuntoRequerimientoCabeceraList[indice].accion = 'ANULAR';
            obj.closest('tr').classList.add("bg-danger");
        } else {
            Swal.fire(
                '',
                'Hubo un error inesperado al intentar cambiar la categoría del adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
                'error'
            );
        }
    }

    actualizarNroComprobanteDeAdjunto(obj) {

        if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
            let indice = tempArchivoAdjuntoRequerimientoCabeceraList.findIndex(elemnt => elemnt.id == obj.closest('tr').id);
            tempArchivoAdjuntoRequerimientoCabeceraList[indice].nro_comprobante = obj.value;
            if (tempArchivoAdjuntoRequerimientoCabeceraList[indice].id > 0) {

            }
            var regExp = /[a-zA-Z]/g; //expresión regular
            if (regExp.test(tempArchivoAdjuntoRequerimientoCabeceraList[indice].id) == false) {
                tempArchivoAdjuntoRequerimientoCabeceraList[indice].accion = 'ACTUALIZAR';
            } else {
                tempArchivoAdjuntoRequerimientoCabeceraList[indice].accion = 'GUARDAR';
            }

        } else {
            Swal.fire(
                '',
                'Hubo un error inesperado al intentar cambiar la categoría del adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
                'error'
            );
        }
    }

    estaHabilitadoLaExtension(file) {
        let extension = (file.name.match(/(?<=\.)\w+$/g) !=null)?file.name.match(/(?<=\.)\w+$/g)[0].toLowerCase():''; // assuming that this file has any extension
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
            || extension === ''
        ) {
            return false;
        } else {
            return true;
        }
    }

    getcategoriaAdjunto() {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listas-categorias-adjunto`,
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
    addToTablaArchivosRequerimientoCabecera(payload) {
        const simboloMonedaOrden = document.querySelector("div[id='modal-enviar-solicitud-pago'] input[name='simbolo_moneda']").value;
        this.getcategoriaAdjunto().then((categoriaAdjuntoList) => {
            // console.log(payload);
            let html = '';
            html = `
            <tr id="${payload.id}" style="text-align:center">
            <td style="text-align:left;">${payload.nameFile}</td>
            <td style="text-align:left;"><input type="date" class="form-control handleChangeFechaEmision" name="fecha_emision" placeholder="Fecha emisión"  value="${moment().format("YYYY-MM-DD")}"></td>
            <td style="text-align:left;"><input type="text" class="form-control handleChangeNroComprobante" name="nro_comprobante"  placeholder="Nro comprobante"></td>
            <td>
                <select class="form-control handleChangeCategoriaAdjunto" name="categoriaAdjunto">
            `;
            categoriaAdjuntoList.forEach(element => {
                if (element.id_categoria_adjunto == payload.category) {
                    html += `<option value="${element.id_categoria_adjunto}" selected>${element.descripcion}</option>`
                } else {
                    html += `<option value="${element.id_categoria_adjunto}">${element.descripcion}</option>`

                }
            });
            html += `</select>
            </td>
            <td style="text-align:left;">
                <div class="input-group">
                <div class="input-group-addon" style="background:lightgray;" name="simboloMoneda">${simboloMonedaOrden}</div>
                <input type="number" class="form-control handleChangeMontoTotalComprobante" placeholder="Monto comprobante" value="${payload.monto_total??''}" step="any">
                </div>
            </td>
            <td style="text-align:center;">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoCabeceraRequerimientoCompra" name="btnEliminarArchivoRequerimientoPago" title="Eliminar" data-id="${payload.id}" ><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
            </tr>`;

            document.querySelector("div[id='"+document.querySelector("div[class='modal fade in']").id+"'] tbody[id='body_archivos_requerimiento_compra_cabecera']").insertAdjacentHTML('beforeend', html);

        }).catch(function (err) {
            console.log(err)
        })
    }
    makeId() {
        let ID = "";
        let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for (let i = 0; i < 12; i++) {
            ID += characters.charAt(Math.floor(Math.random() * 36));
        }
        return ID;
    }

    calcTamañoTotalAdjuntoLogisticoParaSubir(){
        let tamañoTotalArchivoParaSubir=0;

        tempArchivoAdjuntoRequerimientoCabeceraList.forEach(element => {
            tamañoTotalArchivoParaSubir+=element.size;

        });
            document.querySelector("div[id='modal-adjuntar-orden'] span[id='tamaño_total_archivos_para_subir']").textContent= $.number((tamañoTotalArchivoParaSubir/1000000),2,".",",")+'MB';
    }
    eliminarAdjuntoRequerimientoCompraCabecera(obj){
        obj.closest("tr").remove();
        var regExp = /[a-zA-Z]/g; //expresión regular
        if ((regExp.test(obj.dataset.id) == true)) {
            tempArchivoAdjuntoRequerimientoCabeceraList = tempArchivoAdjuntoRequerimientoCabeceraList.filter((element, i) => element.id != obj.dataset.id);
        } else {
            if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
                let indice = tempArchivoAdjuntoRequerimientoCabeceraList.findIndex(elemnt => elemnt.id == obj.dataset.id);
                tempArchivoAdjuntoRequerimientoCabeceraList[indice].accion = 'ELIMINAR';
            } else {
                Swal.fire(
                    '',
                    'Hubo un error inesperado al intentar eliminar el adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
                    'error'
                );
            }

        }
    }
    guardarAdjuntos(){
        if(tempArchivoAdjuntoRequerimientoCabeceraList.length>0){
            let formData = new FormData($('#modal-adjuntar-orden #form-adjunto-orden')[0]);
            formData.append(`archivoAdjuntoRequerimientoObject`, JSON.stringify(tempArchivoAdjuntoRequerimientoCabeceraList));

            if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
                tempArchivoAdjuntoRequerimientoCabeceraList.forEach(element => {

                        formData.append(`id_adjunto[]`, element.id);
                        formData.append(`fecha_emision_adjunto[]`, element.fecha_emision);
                        formData.append(`nro_comprobante_adjunto[]`, element.nro_comprobante);
                        formData.append(`categoria_adjunto[]`, element.category);
                        formData.append(`archivo_adjunto_list[]`, element.file);
                        formData.append(`nombre_real_adjunto[]`, element.nameFile);
                        formData.append(`accion[]`, element.accion);

                });

            }

            $.ajax({
                type: 'POST',
                url: 'guardar-adjunto-orden',
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                data: formData,
                dataType: 'JSON',
                beforeSend:  (data)=> { // Are not working with dataType:'jsonp'
                    $('#modal-adjuntar-orden .modal-content').LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success: (response) =>{
                    if (response.status =='success') {
                        $('#modal-adjuntar-orden .modal-content').LoadingOverlay("hide", true);

                        Lobibox.notify('success', {
                            title:false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: response.mensaje
                        });
                        $('#modal-adjuntar-orden').modal('hide');

                    } else {
                        $('#modal-adjuntar-orden .modal-content').LoadingOverlay("hide", true);
                        // console.log(response);
                        Swal.fire(
                            '',
                            response.mensaje,
                            'error'
                        );
                    }
                },
                fail:  (jqXHR, textStatus, errorThrown) =>{
                    $('#modal-adjuntar-orden .modal-content').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Lo sentimos hubo un error en el servidor al intentar guardar los adjuntos, por favor vuelva a intentarlo',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });

        }else{
            Swal.fire(
                '',
                'No existen adjuntos para guardar',
                'warning'
            );
        }
    }

    obteneAdjuntosOrden(id_orden) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-archivos-adjuntos-orden/${id_orden}`,
                dataType: 'JSON',
                beforeSend: (data) => {
                // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("show", {
                //     imageAutoResize: true,
                //     progress: true,
                //     imageColor: "#3c8dbc"
                // });
            },
                success(response) {
                    // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("hide", true);
                    resolve(response);
                },
                error: function (err) {
                    // $('#modal-adjuntar-orden #adjuntosDePagos').LoadingOverlay("hide", true);
                    reject(err)
                }
            });
        });
    }
    obteneHistorialDeEnviosAPagoEnCuotas(id_orden) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `historial-de-envios-a-pago-en-cuotas/${id_orden}`,
                dataType: 'JSON',
                beforeSend: (data) => {
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

    obteneAdjuntosPago(idOrden) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-archivos-adjuntos-pago-requerimiento/${idOrden}`,
                dataType: 'JSON',
                beforeSend: (data) => {
                $('#modal-adjuntar-orden #adjuntosPago').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
                success(response) {
                    $('#modal-adjuntar-orden #adjuntosPago').LoadingOverlay("hide", true);
                    resolve(response);
                },
                error: function (err) {
                    $('#modal-adjuntar-orden #adjuntosPago').LoadingOverlay("hide", true);
                    reject(err)
                }
            });
        });
    }
}

