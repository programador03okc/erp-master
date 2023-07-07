
var tempObjectBtnPartida;
var tempObjectBtnCentroCostos;
var tempIdRegisterActive;
var tempCentroCostoSelected;

var tempArchivoAdjuntoRequerimientoCabeceraList = [];
var tempArchivoAdjuntoRequerimientoDetalleList = [];
var objBotonAdjuntoRequerimientoDetalleSeleccionado = '';
class RequerimientoView {
    constructor(requerimientoCtrl) {
        this.requerimientoCtrl = requerimientoCtrl;
        const presupuestoInternoView = new PresupuestoInternoView(new PresupuestoInternoModel('{{csrf_token()}}'));
        this.presupuestoInternoView = presupuestoInternoView;
    }
    init() {
        // this.agregarFilaEvent();
        this.initializeEventHandler();
        // $('[name=periodo]').val(today.getFullYear());
        // this.getTipoCambioCompra();
        let idRequerimiento = localStorage.getItem("idRequerimiento");
        // console.log(idRequerimiento);
        if (idRequerimiento !== null) {
            this.cargarRequerimiento(idRequerimiento)
            localStorage.removeItem("idRequerimiento");
            vista_extendida();
        }
        let idRequerimientoByURL = parseInt(location.search.split('id=')[1]);

        if (idRequerimientoByURL > 0) {
            this.cargarRequerimiento(idRequerimientoByURL)
            vista_extendida();
        }


    }

    initializeEventHandler() {
        document.querySelector("button[class~='handleClickImprimirRequerimientoPdf']").addEventListener("click", this.imprimirRequerimientoPdf.bind(this), false);

        $('#form-requerimiento').on("click", "button.handleClickAdjuntarArchivoCabecera", (e) => {
            this.modalAdjuntarArchivosCabecera(e.currentTarget);
        });

        $('#modal-adjuntar-archivos-requerimiento').on("click", "button.handleClickEliminarArchivoCabeceraRequerimiento", (e) => {
            this.eliminarArchivoRequerimientoCabecera(e.currentTarget);
        });

        document.querySelector("input[class~='handleChangeUpdateConcepto']").addEventListener("keyup", this.updateConcepto.bind(this), false);
        document.querySelector("select[class~='handleChangeUpdateMoneda']").addEventListener("change", this.changeMonedaSelect.bind(this), false);
        document.querySelector("select[class~='handleChangeOptEmpresa']").addEventListener("change", this.changeOptEmpresaSelect.bind(this), false);
        document.querySelector("select[class~='handleChangeUpdateEmpresa']").addEventListener("change", this.updateEmpresa.bind(this), false);
        document.querySelector("select[class~='handleChangeOptUbigeo']").addEventListener("change", this.changeOptUbigeo.bind(this), false);
        document.querySelector("select[class~='handleChangeUpdateSede']").addEventListener("change", this.updateSede.bind(this), false);
        document.querySelector("input[class~='handleChangeFechaLimite']").addEventListener("change", this.updateFechaLimite.bind(this), false);
        document.querySelector("select[class~='handleChangeDivision']").addEventListener("change", this.updateDivision.bind(this), false);
        document.querySelector("select[class~='handleChangeTipoRequerimiento']").addEventListener("change", this.updateTipoRequerimiento.bind(this), false);


        $('#form-requerimiento').on("click", "button.handleClickAgregarProducto", () => {
            this.agregarFilaProducto();
            // if($("select[name='id_presupuesto_interno']").val()>0){
            //     this.presupuestoInternoView.ocultarOpcionCentroDeCosto();
            // }
        });
        $('#form-requerimiento').on("click", "button.handleClickAgregarServicio", () => {
            this.agregarFilaServicio();
            // if($("select[name='id_presupuesto_interno']").val()>0){
            //     this.presupuestoInternoView.ocultarOpcionCentroDeCosto();
            // }
        });

        $('#listaRequerimiento tbody').on("click", "button.handleClickCargarRequerimiento", (e) => {
            this.cargarRequerimiento(e.target.dataset.idRequerimiento);
        });

        $('#modal-adjuntar-archivos-requerimiento').on("change", "input.handleChangeAgregarAdjuntoRequerimiento", (e) => {
            this.agregarAdjuntoRequerimiento(e.currentTarget);
        });

        $('#modal-adjuntar-archivos-detalle-requerimiento').on("change", "input.handleChangeAgregarAdjuntoDetalle", (e) => {
            this.agregarAdjuntoRequerimientoPagoDetalle(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-detalle-requerimiento').on("click", "button.handleClickEliminarArchivoRequerimientoDetalle", (e) => {
            this.eliminarArchivoRequerimientoDetalle(e.currentTarget);
        });

        $('#ListaDetalleRequerimiento tbody').on("click", "button.handleClickCargarModalPartidas", (e) => {
            this.cargarModalPartidas(e);
        });

        $('#modal-partidas').on("click", "button.handleClickSelectPartida", (e) => {
            this.selectPartida(e.currentTarget.dataset.idPartida);
        });

        $('#modal-partidas').on("click", "h5.handleClickapertura", (e) => {
            this.apertura(e.currentTarget.dataset.idPresup);
            this.changeBtnIcon(e);
        });

        $('#modal-centro-costos').on("click", "h5.handleClickapertura", (e) => {
            this.apertura(e.currentTarget.dataset.idPresup);
            this.changeBtnIcon(e);
        });

        $('#modal-centro-costos').on("click", "button.handleClickSelectCentroCosto", (e) => {
            this.selectCentroCosto(e.currentTarget.dataset.idCentroCosto, e.currentTarget.dataset.codigo, e.currentTarget.dataset.descripcionCentroCosto);
        });

        $('#ListaDetalleRequerimiento tbody').on("click", "button.handleClickCargarModalCentroCostos", (e) => {
            this.cargarModalCentroCostos(e);
        });
        $('#ListaDetalleRequerimiento tbody').on("blur", "textarea.handleBlurUpdateDescripcionItem", (e) => {
            this.updateDescripcionItem(e.target);
        });
        $('#ListaDetalleRequerimiento tbody').on("blur", "input.handleBurUpdateSubtotal", (e) => {
            this.updateSubtotal(e.target);
        });
        $('#ListaDetalleRequerimiento tbody').on("blur", "input.handleBlurUpdateCantidadItem", (e) => {
            this.updateCantidadItem(e.target);
        });
        $('#ListaDetalleRequerimiento tbody').on("blur", "input.handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida", () => {
            this.calcularPresupuestoUtilizadoYSaldoPorPartida();
        });
        $('#ListaDetalleRequerimiento tbody').on("blur", "input.handleBlurUpdatePrecioItem", (e) => {
            this.updatePrecioItem(e.target);
        });
        $('#ListaDetalleRequerimiento tbody').on("click", "button.handleClickAdjuntarArchivoItem", (e) => {
            this.modalAdjuntarArchivosDetalle(e.currentTarget);
        });

        $('body').on("click", "button.handleClickDescargarArchivoCabeceraRequerimiento", (e) => {
            this.descargarArchivoRequerimiento(e.currentTarget);
        });
        $('body').on("click", "button.handleClickDescargarArchivoRequerimientoDetalle", (e) => {
            this.descargarArchivoItem(e.currentTarget);
        });

        $('#ListaDetalleRequerimiento tbody').on("click", "button.handleClickEliminarItem", (e) => {
            this.eliminarItem(e);
        });
        $('#ListaDetalleRequerimiento').on("click", "input.handleClickIncluyeIGV", (e) => {
            this.actualizarValorIncluyeIGV(e.currentTarget);
        });
        $('#ListaDetalleRequerimiento').on("click", "button.handleClickAsignarComoProductoTransformado", (e) => {
            this.asignarComoProductoTransformado(e.currentTarget);
        });

        $('body').on("change", "select.handleChangePresupuestoInterno", (e) => {
            this.deshabilitarOtrosTiposDePresupuesto('SELECCION_PRESUPUESTO_INTERNO', e.currentTarget.value); // deshabilitar el poder afectar otro presupuesto ejemplo: selector de proyectos, selctor de cdp 
        });

        $('#form-requerimiento').on("change", "select.handleChangeProyecto", (e) => {
            this.deshabilitarOtrosTiposDePresupuesto('SELECCION_PROYECTOS', e.currentTarget.value); // deshabilitar el poder afectar otro presupuesto ejemplo: selector de proyectos, selctor de cdp 
        });

        $('#listaCuadroPresupuesto').on("click", "button.handleClickSeleccionarCDP", (e) => {
            // console.log(e.currentTarget.dataset.idCc);
            this.deshabilitarOtrosTiposDePresupuesto('SELECCION_CDP', e.currentTarget.dataset.idCc);
        });

        $('body').on("click", "button.handleClickLimpiarSeleccionCuadroDePresupuesto", (e) => {
            this.deshabilitarOtrosTiposDePresupuesto('SELECCION_CDP', 0);
            document.querySelector("input[name='id_cc']").value = '';
            document.querySelector("input[name='codigo_oportunidad']").value = '';
        });

    }

    deshabilitarOtrosTiposDePresupuesto(origen, valor) {
        switch (origen) {
            case 'SELECCION_PRESUPUESTO_INTERNO':
                if (valor > 0) {
                    document.querySelector("select[name='id_proyecto']").setAttribute("disabled", true);
                    document.querySelector("select[name='id_proyecto']").value='';
                    document.querySelector("button[name='btnSearchCDP']").setAttribute("disabled", true);
                    document.querySelector("input[name='id_cc']").value='';

                } else {
                    document.querySelector("select[name='id_proyecto']").removeAttribute("disabled");
                    document.querySelector("button[name='btnSearchCDP']").removeAttribute("disabled");

                }
                break;
            case 'SELECCION_PROYECTOS':
                if (valor > 0) {
                    document.querySelector("select[name='id_presupuesto_interno']").setAttribute("disabled", true);
                    document.querySelector("select[name='id_presupuesto_interno']").value='';
                    document.querySelector("input[name='id_cc']").value='';
                    document.querySelector("button[name='btnSearchCDP']").setAttribute("disabled", true);
                } else {
                    document.querySelector("select[name='id_presupuesto_interno']").removeAttribute("disabled");
                    document.querySelector("button[name='btnSearchCDP']").removeAttribute("disabled");
                }
                break;
            case 'SELECCION_CDP':
                if (valor > 0) {
                    document.querySelector("select[name='id_presupuesto_interno']").setAttribute("disabled", true);
                    document.querySelector("select[name='id_presupuesto_interno']").value='';
                    document.querySelector("select[name='id_proyecto']").setAttribute("disabled", true);
                    document.querySelector("select[name='id_proyecto']").value='';
                } else {
                    document.querySelector("select[name='id_presupuesto_interno']").removeAttribute("disabled");
                    document.querySelector("select[name='id_proyecto']").removeAttribute("disabled");
                }
                break;

            default:
                break;
        }
    }

    editRequerimiento() {
        if (parseInt(document.querySelector("input[name='id_requerimiento']").value) > 0) {
            $("#form-requerimiento .activation").attr('disabled', false);
            document.getElementsByName("btn-adjuntos-requerimiento")[0].removeAttribute('disabled');

        }
    }

    mostrarHistorial() {

        $('#modal-historial-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });

        this.construirTablaHistorialRequerimientosElaborados({ 'meOrAll': 'ME', 'idEmpresa': 'SIN_FILTRO', 'idSede': 'SIN_FILTRO', 'idGrupo': 'SIN_FILTRO', 'idDivision': 'SIN_FILTRO', 'fechaRegistroDesde': 'SIN_FILTRO', 'fechaRegistroHasta': 'SIN_FILTRO', 'idEstado': 'SIN_FILTRO' });
        // this.requerimientoCtrl.getListadoElaborados().then((res)=> {

        // }).catch(function (err) {
        //     console.log(err)
        // })
    }

    construirTablaHistorialRequerimientosElaborados(parametros) {
        // console.log(data);

        var vardataTables = funcDatatables();
        let $tablaListaRequerimiento = $('#listaRequerimiento').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language': vardataTables[0],
            'buttons': [],
            'order': [[10, 'desc']],
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'elaborados',
                'type': 'POST',
                'data': (parametros),
                beforeSend: data => {

                    $("#listaRequerimiento").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
            },
            'columns': [
                { 'data': 'priori', 'name': 'adm_prioridad.descripcion', 'className': 'text-center' },
                {
                    'data': 'codigo', 'name': 'codigo', 'className': 'text-center', 'render': function (data, type, row) {
                        if (row.tiene_transformacion == true) {
                            return '<i class="fas fa-random"></i> ' + row.codigo;
                        } else {
                            return row.codigo;
                        }
                    }
                },
                { 'data': 'concepto', 'name': 'concepto' },
                { 'data': 'fecha_entrega', 'name': 'fecha_entrega', 'className': 'text-center' },
                { 'data': 'tipo_requerimiento', 'name': 'alm_tp_req.descripcion', 'className': 'text-center' },
                { 'data': 'razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-center' },
                { 'data': 'grupo', 'name': 'sis_grupo.descripcion' },
                { 'data': 'division', 'name': 'division.descripcion' },
                {
                    'render':
                        function (data, type, row) {
                            switch (row['estado']) {
                                case 1:
                                    return '<span class="label label-default">' + row['estado_doc'] + '</span>';
                                    break;
                                case 2:
                                    return '<span class="label label-success">' + row['estado_doc'] + '</span>';
                                    break;
                                case 3:
                                    return '<span class="label label-warning">' + row['estado_doc'] + '</span>';
                                    break;
                                case 5:
                                    return '<span class="label label-primary">' + row['estado_doc'] + '</span>';
                                    break;
                                case 7:
                                    return '<span class="label label-danger">' + row['estado_doc'] + '</span>';
                                    break;
                                default:
                                    return '<span class="label label-default">' + row['estado_doc'] + '</span>';
                                    break;

                            }
                        }
                },
                { 'data': 'fecha_registro', 'name': 'alm_req.fecha_registro', 'className': 'text-center' },
                { 'data': 'id_requerimiento' }

            ],
            'columnDefs': [
                {
                    'render': function (data, type, row) {
                        return row['termometro'];
                    }, targets: 0
                },
                {
                    'render': function (data, type, row) {
                        let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                        let containerCloseBrackets = '</div></center>';
                        let btnSeleccionar = '<button type="button" class="btn btn-xs btn-success handleClickCargarRequerimiento" title="Seleccionar" data-id-requerimiento="' + row.id_requerimiento + '" >Seleccionar</button>';
                        return containerOpenBrackets + btnSeleccionar + containerCloseBrackets;
                    }, targets: 10
                },
            ],
            "createdRow": function (row, data, dataIndex) {
                if (data.estado == 2) {
                    $(row.childNodes[8]).css('color', '#4fa75b');
                }
                if (data.estado == 3) {
                    $(row.childNodes[8]).css('color', '#ee9b1f');
                }
                if (data.estado == 7) {
                    $(row.childNodes[8]).css('color', '#d92b60');
                }
            },
            'initComplete': function () {
                //Boton de busqueda
                const $filter = $('#listaRequerimiento_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaListaRequerimiento.search($input.val()).draw();
                })
                //Fin boton de busqueda
            },
            "drawCallback": function (settings) {
                //Botón de búsqueda
                $('#listaRequerimiento_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaRequerimiento_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaRequerimiento").LoadingOverlay("hide", true);
            }
        });

    }

    cargarRequerimiento(idRequerimiento) {
        $('#modal-historial-requerimiento').modal('hide');
        const objecto = this;
        this.requerimientoCtrl.getHistorialRequerimiento(idRequerimiento).then((res) => {
            objecto.mostrarRequerimiento(res);

        }).catch(function (err) {
            console.log(err)
        });
    }


    mostrarRequerimiento(data) {
        let hasDisabledInput = 'disabled';
        tempArchivoAdjuntoRequerimientoCabeceraList = [];
        tempArchivoAdjuntoRequerimientoDetalleList = [];
        if (data.hasOwnProperty('requerimiento')) {

            this.RestablecerFormularioRequerimiento();

            if (parseFloat(data.requerimiento[0].monto_igv) > 0) {
                document.querySelector("input[name='incluye_igv']").checked = true;
            } else {
                document.querySelector("input[name='incluye_igv']").checked = false;
            }
            var btnImprimirRequerimiento = document.getElementsByName("btn-imprimir-requerimento-pdf");
            var btnAdjuntosRequerimiento = document.getElementsByName("btn-adjuntos-requerimiento");
            // let allButtonAdjuntarNuevo = document.querySelectorAll("input[name='nombre_archivo']");

            disabledControl(btnImprimirRequerimiento, false);
            disabledControl(btnAdjuntosRequerimiento, false);

            var btnTrazabilidadRequerimiento = document.getElementsByName("btn-ver-trazabilidad-requerimiento");
            disabledControl(btnTrazabilidadRequerimiento, false);

            // construir select con todas la divisiones
            let optionSelectDivisionHTML = '';
            this.requerimientoCtrl.getDivisiones().then((res) => {
                res.forEach(element => {
                    optionSelectDivisionHTML += `<option data-id-grupo="${element.grupo_id}" value="${element.id_division}">${element.descripcion}</option> `;
                });
                document.querySelector("select[name='division']").innerHTML = optionSelectDivisionHTML;
                this.mostrarCabeceraRequerimiento(data['requerimiento'][0]);

            }).catch(function (err) {
                console.log(err)
                Swal.fire(
                    '',
                    'Hubo un error al intentar cargar todo las divisiones',
                    'error'
                );
            })
            //

            if (data.hasOwnProperty('det_req')) {
                if (data['requerimiento'][0].estado == 7 || data['requerimiento'][0].estado == 2) {
                    changeStateButton('cancelar'); //init.js
                    $("#form-requerimiento .activation").attr('disabled', true);

                } else if (data['requerimiento'][0].estado == 1 && data['requerimiento'][0].id_usuario == auth_user.id_usuario) {
                    document.querySelector("form[id='form-requerimiento']").setAttribute('type', 'edition');
                    changeStateButton('historial'); //init.js
                    // allButtonAdjuntarNuevo.forEach(element => {
                    //     element.removeAttribute("disabled");
                    // });


                    $("#form-requerimiento .activation").attr('disabled', true);

                } else if ((data['requerimiento'][0].estado == 1 || data['requerimiento'][0].estado == 3)) {
                    document.querySelector("div[id='group-historial-revisiones']").removeAttribute('hidden');
                    this.mostrarHistorialRevisionAprobacion(data['historial_aprobacion']);
                    disabledControl(btnAdjuntosRequerimiento, true);

                    if (data['requerimiento'][0].id_usuario == auth_user.id_usuario) {
                        hasDisabledInput = '';
                        document.querySelector("form[id='form-requerimiento']").setAttribute('type', 'edition');
                        changeStateButton('editar'); //init.js
                        disabledControl(btnAdjuntosRequerimiento, false);

                        // allButtonAdjuntarNuevo.forEach(element => {
                        //     element.removeAttribute("disabled");
                        // });

                        $("#form-requerimiento .activation").attr('disabled', false);

                    }



                } else {
                    document.querySelector("div[id='group-historial-revisiones']").setAttribute('hidden', true);

                    // allButtonAdjuntarNuevo.forEach(element => {
                    //     element.setAttribute("disabled",true);
                    // });


                }
                this.mostrarDetalleRequerimiento(data, hasDisabledInput);
            }

        } else {
            Swal.fire(
                '',
                "El requerimiento que intenta cargar no existe",
                'waning'
            );
        }

        this.limpiarTabla('listaHistorialAprobacion');
        if (data.hasOwnProperty('historial_aprobacion')) {
            if (data['historial_aprobacion'].length > 0) {
                // console.log(data['historial_aprobacion']);
                let html = '';
                for (let i = 0; i < data['historial_aprobacion'].length; i++) {
                    html += `<tr>
                            <td style="text-align:center;">${data['historial_aprobacion'][i].nombre_corto ? data['historial_aprobacion'][i].nombre_corto : ''}</td>
                            <td style="text-align:center;">${data['historial_aprobacion'][i].accion ? data['historial_aprobacion'][i].accion : ''}${data['historial_aprobacion'][i].tiene_sustento == true ? ' ' : ''}</td>
                            <td style="text-align:left;">${data['historial_aprobacion'][i].detalle_observacion ? data['historial_aprobacion'][i].detalle_observacion : ''}</td>
                            <td style="text-align:center;">${data['historial_aprobacion'][i].fecha_vobo ? data['historial_aprobacion'][i].fecha_vobo : ''}</td>
                        </tr>`;
                }
                document.querySelector("tbody[id='body_historial_aprobacion']").insertAdjacentHTML('beforeend', html)
            }
        }
    }


    mostrarCabeceraRequerimiento(data) {


        // console.log(auth_user);
        // document.querySelector("input[name='id_usuario_session']").value =data.
        document.querySelector("input[name='id_usuario_req']").value = data.id_usuario;
        document.querySelector("input[name='id_requerimiento']").value = data.id_requerimiento;
        document.querySelector("span[id='codigo_requerimiento']").textContent = data.codigo;
        document.querySelector("input[name='id_cc']").value = data.id_cc ?? '';
        document.querySelector("input[name='codigo_oportunidad']").value = data.codigo_oportunidad ?? '';
        document.querySelector("input[name='id_grupo']").value = data.id_grupo;
        document.querySelector("input[name='estado']").value = data.estado;
        document.querySelector("span[id='estado_doc']").textContent = data.estado_doc;
        document.querySelector("input[name='monto_subtotal']").value = data.monto_subtotal;
        document.querySelector("input[name='monto_igv']").value = data.monto_igv;
        document.querySelector("input[name='monto_total']").value = data.monto_total;
        document.querySelector("span[id='nro_occ_softlink']").textContent = data.nro_occ_softlink != null ? 'OCC: ' + data.nro_occ_softlink : '';
        document.querySelector("input[name='fecha_requerimiento']").value = data.fecha_requerimiento;
        document.querySelector("input[name='concepto']").value = data.concepto;
        document.querySelector("select[name='moneda']").value = data.id_moneda;
        document.querySelector("select[name='periodo']").value = data.id_periodo;
        document.querySelector("select[name='prioridad']").value = data.id_prioridad;
        document.querySelector("select[name='rol_usuario']").value = data.id_rol;
        document.querySelector("select[name='empresa']").value = data.id_empresa;
        this.getDataSelectSede(data.id_empresa, data.id_sede);
        document.querySelector("select[name='sede']").value = data.id_sede;
        document.querySelector("input[name='fecha_entrega']").value = moment(data.fecha_entrega, "DD-MM-YYYY").format("YYYY-MM-DD");
        document.querySelector("select[name='division']").value = data.division_id;
        document.querySelector("select[name='tipo_requerimiento']").value = data.id_tipo_requerimiento;
        document.querySelector("input[name='id_trabajador']").value = data.trabajador_id;
        document.querySelector("input[name='nombre_trabajador']").value = data.nombre_trabajador;
        document.querySelector("select[name='fuente_id']").value = data.fuente_id;
        document.querySelector("select[name='fuente_det_id']").value = data.fuente_det_id;
        // document.querySelector("input[name='montoMoneda']").textContent =data.
        document.querySelector("input[name='monto']").value = data.monto_total;
        document.querySelector("select[name='id_almacen']").value = data.id_almacen;
        // document.querySelector("input[name='descripcion_grupo']").value =data.
        document.querySelector("select[name='tipo_cliente']").value = data.tipo_cliente;
        document.querySelector("input[name='id_cliente']").value = data.id_cliente;
        document.querySelector("input[name='cliente_ruc']").value = data.cliente_ruc;
        document.querySelector("input[name='cliente_razon_social']").value = data.cliente_razon_social;
        document.querySelector("input[name='id_persona']").value = data.id_persona;
        document.querySelector("input[name='dni_persona']").value = data.dni_persona;
        document.querySelector("input[name='nombre_persona']").value = data.nombre_persona;
        document.querySelector("input[name='ubigeo']").value = data.id_ubigeo_entrega;
        document.querySelector("input[name='name_ubigeo']").value = data.name_ubigeo;
        document.querySelector("input[name='telefono_cliente']").value = data.telefono;
        document.querySelector("input[name='email_cliente']").value = data.email;
        document.querySelector("input[name='direccion_entrega']").value = data.direccion_entrega;
        // document.querySelector("input[name='nombre_contacto']").value =data.
        // document.querySelector("input[name='cargo_contacto']").value =data.
        // document.querySelector("input[name='email_contacto']").value =data.
        // document.querySelector("input[name='telefono_contacto']").value =data.
        // document.querySelector("input[name='direccion_contacto']").value =data.
        document.querySelector("textarea[name='observacion']").value = data.observacion;
        tempArchivoAdjuntoRequerimientoCabeceraList = [];
        if ((data.adjuntos).length > 0) {
            (data.adjuntos).forEach(element => {
                tempArchivoAdjuntoRequerimientoCabeceraList.push({
                    id: element.id_adjunto,
                    category: element.categoria_adjunto_id,
                    nameFile: element.archivo,
                    action: '',
                    typeFile: null,
                    sizeFile: null,
                    file: []
                });

            });
            this.updateContadorTotalAdjuntosRequerimientoCabecera();

        }
        let simboloMonedaPresupuestoUtilizado = document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo;
        let allSelectorSimboloMoneda = document.getElementsByName("simboloMoneda");
        if (allSelectorSimboloMoneda.length > 0) {
            allSelectorSimboloMoneda.forEach(element => {
                element.textContent = simboloMonedaPresupuestoUtilizado;
            });
        }

        this.llenarComboProyectos(data.id_grupo,data.id_proyecto); 
        this.presupuestoInternoView.llenarComboPresupuestoInterno(data.id_grupo, data.division_id, data.id_presupuesto_interno);

    }


    mostrarHistorialRevisionAprobacion(data) {
        this.limpiarTabla('listaHistorialRevision');

        let html = '';
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                html += `<tr>
                    <td style="text-align:center;">${data[i].nombre_corto ? data[i].nombre_corto : ''}</td>
                    <td style="text-align:center;">${data[i].accion ? data[i].accion : ''}</td>
                    <td style="text-align:left;">${data[i].detalle_observacion ? data[i].detalle_observacion : ''}</td>
                    <td style="text-align:center;">${data[i].fecha_vobo ? data[i].fecha_vobo : ''}</td>
                </tr>`;
            }
        }
        document.querySelector("tbody[id='body_historial_revision']").insertAdjacentHTML('beforeend', html)
    }


    mostrarDetalleRequerimiento(data, hasDisabledInput) {
        let dataCabeceraRequerimiento = data['requerimiento'];
        let dataDetalleRequerimiento = data['det_req'];
        if (dataCabeceraRequerimiento[0].id_tipo_requerimiento == 6) {
            document.querySelector("div[id='input-group-incidencia']").removeAttribute("hidden");
            document.querySelector("input[name='id_incidencia']").value = dataCabeceraRequerimiento[0].id_incidencia ?? '';
            document.querySelector("input[name='codigo_incidencia']").value = dataCabeceraRequerimiento[0].codigo_incidencia ?? '';
            document.querySelector("input[name='cliente_incidencia']").value = dataCabeceraRequerimiento[0].cliente_incidencia ?? '';

        } else {
            document.querySelector("div[id='input-group-incidencia']").setAttribute("hidden", true);
        }

        this.limpiarTabla('ListaDetalleRequerimiento');
        vista_extendida();
        // console.log(dataDetalleRequerimiento);
        for (let i = 0; i < dataDetalleRequerimiento.length; i++) {
            // console.log(data);
            let cantidadAdjuntos = dataDetalleRequerimiento != null && dataDetalleRequerimiento[i].adjuntos ? (dataDetalleRequerimiento[i].adjuntos).filter((element, i) => element.estado != 7).length : 0;
            // fix unidad medida que toma el html de un select oculto y debe tener por defecto seleccionado el option que viene de dataa

            let objOptionSelectUnidad = document.querySelector("select[id='selectUnidadMedida']").getElementsByTagName('option');
            let newOptionUnidadMedida = '';
            for (let j = 0; j < objOptionSelectUnidad.length; j++) {
                if (objOptionSelectUnidad[j].value == dataDetalleRequerimiento[i].id_unidad_medida) {
                    newOptionUnidadMedida += `<option value="${objOptionSelectUnidad[j].value}" selected>${objOptionSelectUnidad[j].textContent}</option>`;
                } else {
                    newOptionUnidadMedida += `<option value="${objOptionSelectUnidad[j].value}">${objOptionSelectUnidad[j].textContent}</option>`;

                }

            }
            // console.log(newOptionUnidadMedida);
            // document.querySelector("select[id='selectUnidadMedida']").getElementsByTagName('option')[indexUm].setAttribute("selected", "");
            // console.log(objOptionSelectUnidad);
            //  fin fix unidad medida
            let idFila = dataDetalleRequerimiento[i].id_detalle_requerimiento > 0 ? dataDetalleRequerimiento[i].id_detalle_requerimiento : (this.makeId());
            console.log(dataDetalleRequerimiento);

            let idPartida='';
            let codigoPartida='';
            let descripcionPartida='';
            let totalPartida=0;
            if(dataDetalleRequerimiento[i].id_partida > 0){
                idPartida= dataDetalleRequerimiento[i].id_partida;
                codigoPartida= dataDetalleRequerimiento[i].codigo_partida;
                descripcionPartida= dataDetalleRequerimiento[i].descripcion_partida;
                totalPartida= dataDetalleRequerimiento[i].presupuesto_old_total_partida;
            }else if(dataDetalleRequerimiento[i].id_partida_pi>0){
                idPartida= dataDetalleRequerimiento[i].id_partida_pi;
                codigoPartida= dataDetalleRequerimiento[i].codigo_partida_presupuesto_interno;
                descripcionPartida= dataDetalleRequerimiento[i].descripcion_partida_presupuesto_interno;
                totalPartida= dataDetalleRequerimiento[i].presupuesto_interno_total_partida;
            }

            if (dataDetalleRequerimiento[i].id_tipo_item == 1) { // producto
                document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr data-estado="${dataDetalleRequerimiento[i].estado}" style="text-align:center; background-color:${dataDetalleRequerimiento[i].estado == 7 ? '#f5e4e4' : ''}; ">
                <td></td>
                <td><p class="descripcion-partida" data-id-partida="${idPartida}" data-presupuesto-total="${totalPartida}" title="${descripcionPartida}" >${codigoPartida}</p><button type="button" class="btn btn-xs btn-info activation handleClickCargarModalPartidas" name="partida" ${hasDisabledInput}>Seleccionar</button>
                    <div class="form-group">
                        <input type="text" class="partida" name="idPartida[]" value="${idPartida}" hidden>
                    </div>
                </td>
                <td><p class="descripcion-centro-costo" title="${dataDetalleRequerimiento[i].descripcion_centro_costo ?? '(NO SELECCIONADO)'}">${dataDetalleRequerimiento[i].codigo_centro_costo ?? '(NO SELECCIONADO)'} </p><button type="button" class="btn btn-xs btn-primary activation handleClickCargarModalCentroCostos" name="centroCostos"  ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" ${hasDisabledInput} >Seleccionar</button>
                    <div class="form-group">
                        <input type="text" class="centroCosto" name="idCentroCosto[]" value="${dataDetalleRequerimiento[i].id_centro_costo}" hidden>
                    </div>
                </td>
                <td>
                    <input class="form-control activation input-sm" type="text" name="partNumber[]" placeholder="Part number" value="${((dataDetalleRequerimiento[i].part_number != null && dataDetalleRequerimiento[i].part_number.length > 0) ? dataDetalleRequerimiento[i].part_number : (dataDetalleRequerimiento[i].producto_part_number != null ? dataDetalleRequerimiento[i].producto_part_number : ''))}" ${hasDisabledInput}> ${dataDetalleRequerimiento[i].tiene_transformacion == true ? '<br><span class="badge badge-secondary conSinTransformacionText">Transformado</span>' : '<span class="badge badge-secondary conSinTransformacionText"></span>'}
                    ${((dataDetalleRequerimiento[i].codigo_producto != null && dataDetalleRequerimiento[i].codigo_producto.length > 0) ? `<small> Código producto: ${dataDetalleRequerimiento[i].codigo_producto}</small>` : `<small> (Sin código de producto)</small>`)}
                    <input type="number" class="conTransformacion" max="1" min="0" name="conTransformacion[]" value="${dataDetalleRequerimiento[i].tiene_transformacion == true ? 1 : 0}" hidden>

                    </td>
                <td>
                    <div class="form-group">
                        <textarea class="form-control activation input-sm descripcion handleBlurUpdateDescripcionItem" name="descripcion[]" placeholder="Descripción" value="${((dataDetalleRequerimiento[i].descripcion != null && dataDetalleRequerimiento[i].descripcion > 0) ? dataDetalleRequerimiento[i].descripcion : (dataDetalleRequerimiento[i].producto_descripcion != null ? dataDetalleRequerimiento[i].producto_descripcion : ''))}"   ${hasDisabledInput} >${(dataDetalleRequerimiento[i].descripcion != null ? dataDetalleRequerimiento[i].descripcion : (dataDetalleRequerimiento[i].producto_descripcion != null ? dataDetalleRequerimiento[i].producto_descripcion : ''))}</textarea></td>
                    </div>
                <td><select name="unidad[]" class="form-control activation input-sm" value="${dataDetalleRequerimiento[i].id_unidad_medida}" ${hasDisabledInput} >${newOptionUnidadMedida}</select></td>
                <td>
                    <div class="form-group">
                        <input class="form-control activation input-sm cantidad text-right handleBurUpdateSubtotal handleBlurUpdateCantidadItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="1" name="cantidad[]"  value="${dataDetalleRequerimiento[i].cantidad ?? ''}"   placeholder="Cantidad" ${hasDisabledInput}>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control activation input-sm precio text-right handleBurUpdateSubtotal handleBlurUpdatePrecioItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="0" name="precioUnitario[]" value="${dataDetalleRequerimiento[i].precio_unitario ?? ''}" placeholder="Precio U." ${hasDisabledInput}>
                    </div>
                </td>
                <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                <td><textarea class="form-control activation input-sm" name="motivo[]"  value="${dataDetalleRequerimiento[i].motivo ?? ''}" placeholder="Motivo de requerimiento de item (opcional)" ${hasDisabledInput} >${dataDetalleRequerimiento[i].motivo ?? ''}</textarea></td>
                <td>
                    <div class="btn-group" role="group">
                        <input type="hidden" class="tipoItem" name="tipoItem[]" value="1">
                        <input type="hidden" class="idRegister" name="idRegister[]" value="${idFila}">
                        <button type="button" class="btn btn-warning btn-xs  handleClickAdjuntarArchivoItem"  data-id="${idFila}" name="btnAdjuntarArchivoItem[]" title="Adjuntos" >
                            <i class="fas fa-paperclip"></i>
                            <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">${cantidadAdjuntos}</span>
                        </button>
                        <button type="button" class="btn btn-danger btn-xs activation handleClickEliminarItem" name="btnEliminarItem[]" title="Eliminar" ${hasDisabledInput}><i class="fas fa-trash-alt"></i></button>
                        <button type="button" class="btn ${dataDetalleRequerimiento[i].tiene_transformacion == true ? 'btn-success' : 'btn-default'} btn-xs handleClickAsignarComoProductoTransformado" name="btnAsignarComoProductoTransformado[]" title="sin transformación" style="display:${dataCabeceraRequerimiento[0]['id_tipo_requerimiento'] == 6 ? 'block' : 'none'};"><i class="fas fa-random"></i></button>
                    </div>
                </td>
                </tr>`);
            } else { // servicio
                document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr data-estado="${dataDetalleRequerimiento[i].estado}" style="text-align:center;  background-color:${dataDetalleRequerimiento[i].estado == 7 ? '#f5e4e4' : ''};">
                    <td></td>
                    <td><p class="descripcion-partida" data-id-partida="${idPartida}" data-presupuesto-total="${totalPartida}" title="${descripcionPartida}" >${codigoPartida}</p><button type="button" class="btn btn-xs btn-info activation handleClickCargarModalPartidas" name="partida" ${hasDisabledInput}>Seleccionar</button>
                        <div class="form-group">
                            <input type="text" class="partida" name="idPartida[]" value="${idPartida}" hidden>
                        </div>
                    </td>
                    <td><p class="descripcion-centro-costo" title="${dataDetalleRequerimiento[i].descripcion_centro_costo ?? '(NO SELECCIONADO)'}">${dataDetalleRequerimiento[i].codigo_centro_costo ?? '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary activation handleClickCargarModalCentroCostos" name="centroCostos" ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" ${hasDisabledInput} >Seleccionar</button>
                        <div class="form-group">
                            <input type="text" class="centroCosto" name="idCentroCosto[]" value="${dataDetalleRequerimiento[i].id_centro_costo}" hidden>
                        </div>
                    </td>
                    <td>(Servicio)<input type="hidden" name="partNumber[]"></td>
                    <td>
                        <div class="form-group">
                        <textarea class="form-control activation input-sm descripcion handleBlurUpdateDescripcionItem" name="descripcion[]" placeholder="Descripción" value="${dataDetalleRequerimiento[i].descripcion ?? ''}" ${hasDisabledInput} >${dataDetalleRequerimiento[i].descripcion ?? ''}</textarea></td>
                        </div>
                    <td><select name="unidad[]" class="form-control activation input-sm" value="${dataDetalleRequerimiento[i].id_unidad_medida}"  ${hasDisabledInput}>${newOptionUnidadMedida}</select></td>
                    <td>
                        <div class="form-group">
                            <input class="form-control activation input-sm cantidad text-right handleBurUpdateSubtotal handleBlurUpdateCantidadItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="1" name="cantidad[]"  value="${dataDetalleRequerimiento[i].cantidad ?? ''}"  placeholder="Cantidad" ${hasDisabledInput}>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input class="form-control activation input-sm precio text-right handleBurUpdateSubtotal handleBlurUpdateCantidadItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="0" name="precioUnitario[]" value="${dataDetalleRequerimiento[i].precio_unitario ?? ''}"  placeholder="Precio U." ${hasDisabledInput}>
                        </div>
                    </td>
                    <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
                    <td><textarea class="form-control activation input-sm" name="motivo[]"  value="${dataDetalleRequerimiento[i].motivo ?? ''}" placeholder="Motivo de requerimiento de item (opcional)" ${hasDisabledInput} >${dataDetalleRequerimiento[i].motivo ?? ''}</textarea></td>
                    <td>
                        <div class="btn-group" role="group">
                            <input type="hidden" class="tipoItem" name="tipoItem[]" value="2">
                            <input type="hidden" class="idRegister" name="idRegister[]" value="${idFila}">
                            <button type="button" class="btn btn-warning btn-xs  handleClickAdjuntarArchivoItem"  data-id="${idFila}" name="btnAdjuntarArchivoItem[]" title="Adjuntos">
                                <i class="fas fa-paperclip"></i>
                                <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">${cantidadAdjuntos}</span>
                            </button>
                            <button type="button" class="btn btn-danger btn-xs activation handleClickEliminarItem" name="btnEliminarItem[]" title="Eliminar" ${hasDisabledInput} ><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                    </tr>`);
            }

        }
        this.updateContadorItem();
        this.autoUpdateSubtotal();
        this.calcularTotal();
        this.calcularPresupuestoUtilizadoYSaldoPorPartida();
        tempArchivoAdjuntoRequerimientoDetalleList = [];
        dataDetalleRequerimiento.forEach(element => {
            if (element.adjuntos.length > 0) {
                (element.adjuntos).forEach(adjunto => {
                    tempArchivoAdjuntoRequerimientoDetalleList.push({
                        id: adjunto.id_adjunto,
                        id_detalle_requerimiento: adjunto.id_detalle_requerimiento,
                        nameFile: adjunto.archivo,
                        action: '',
                        typeFile: null,
                        sizeFile: null,
                        file: []
                    });
                });

            }

        });

    }

    imprimirRequerimientoPdf() {
        var id = document.getElementsByName("id_requerimiento")[0].value;
        window.open('imprimir-requerimiento-pdf/' + id + '/0');

    }

    limpiarTabla(idElement) {
        let nodeTbody = document.querySelector("table[id='" + idElement + "'] tbody");
        if (nodeTbody != null) {
            while (nodeTbody.children.length > 0) {
                nodeTbody.removeChild(nodeTbody.lastChild);
            }

        }
    }
    // cabecera requerimiento
    changeMonedaSelect(e) {
        let simboloMonedaPresupuestoUtilizado = document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo
        let allSelectorSimboloMoneda = document.getElementsByName("simboloMoneda");
        if (allSelectorSimboloMoneda.length > 0) {
            allSelectorSimboloMoneda.forEach(element => {
                element.textContent = simboloMonedaPresupuestoUtilizado;
            });
        }

        // let moneda = e.target.value == 1 ? 'S/' : '$';

        document.querySelector("div[name='montoMoneda']").textContent = simboloMonedaPresupuestoUtilizado;
        // if (document.querySelector("form[id='form-requerimiento'] table span[class='moneda']")) {
        //     document.querySelectorAll("form[id='form-requerimiento'] span[class='moneda']").forEach(element => {
        //         element.textContent = moneda;
        //     });
        // }
        this.calcularPresupuestoUtilizadoYSaldoPorPartida();

    }

    changeOptEmpresaSelect(obj) {
        this.getDataSelectSede(obj.target.value);


    }

    getDataSelectSede(idEmpresa = null, idSede = null) {
        if (idEmpresa > 0) {
            this.requerimientoCtrl.obtenerSede(idEmpresa).then((res) => {
                this.llenarSelectSede(res, idSede);
                this.cargarAlmacenes($('[name=sede]').val());
                this.seleccionarAlmacen();
                this.llenarUbigeo();
            }).catch(function (err) {
                console.log(err)
            })


        }
        return false;
    }

    llenarSelectSede(array, idSede) {
        // console.log(idSede);
        let selectElement = document.querySelector("div[id='input-group-sede'] select[name='sede']");
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
            if (element.id_sede == idSede) {
                option.selected = true;

            }
            option.setAttribute('data-ubigeo', element.id_ubigeo);
            option.setAttribute('data-name-ubigeo', element.ubigeo_descripcion);
            selectElement.add(option);
        });

        if (array.length > 0) {
            this.updateSedeByPassingElement(selectElement);
        }

    }

    seleccionarAlmacen() {
        // let firstSede = data[0].id_sede;
        let selectAlmacen = document.querySelector("div[id='input-group-almacen'] select[name='id_almacen']");
        if (selectAlmacen.options.length > 0) {
            let i, L = selectAlmacen.options.length - 1;
            for (i = L; i > 0; i--) {
                if (selectAlmacen.options[i].dataset.idEmpresa == document.querySelector("select[id='empresa']").value) {
                    if ([4, 10, 11, 12, 13, 14].includes(parseInt(selectAlmacen.options[i].dataset.idSede)) == true) { ///default almacen lima
                        selectAlmacen.options[i].selected = true;
                    }
                }
            }
        }
    }

    llenarUbigeo() {
        let ubigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
        let nameUbigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;
        document.querySelector("input[name='ubigeo']").value = ubigeo;
        document.querySelector("input[name='name_ubigeo']").value = nameUbigeo;
        //let sede = $('[name=sede]').val();
    }

    changeOptUbigeo(e) {
        let ubigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.ubigeo;
        let nameUbigeo = document.querySelector("select[name='sede']").options[document.querySelector("select[name='sede']").selectedIndex].dataset.nameUbigeo;

        document.querySelector("input[name='ubigeo']").value = ubigeo;
        document.querySelector("input[name='name_ubigeo']").value = nameUbigeo;
        this.cargarAlmacenes($('[name=sede]').val());
    }

    cargarAlmacenes(sede) {
        // console.log(sede);
        if (sede !== '') {
            this.requerimientoCtrl.obtenerAlmacenes(sede).then((res) => {
                // console.log(res);
                let option = '';
                if (res.length > 0) {
                    for (let i = 0; i < res.length; i++) {
                        if (res[i].estado != 7 && res[i].id_tipo_almacen == 1) {
                            option += '<option data-id-sede="' + res[i].id_sede + '" data-id-empresa="' + res[i].id_empresa + '" data-id-tipo-almacen="' + res[i].id_tipo_almacen + '" value="' + res[i].id_almacen + '" selected>' + res[i].codigo + ' - ' + res[i].descripcion + '</option>';
                            break;
                        }
                    }
                } else {
                    Swal.fire(
                        '',
                        'La sede seleccionada no tiene un almacén origen, consultar con almacén',
                        'warning'
                    );
                }
                $('[name=id_almacen]').html('<option value="0" disabled selected>Elija una opción</option>' + option);
            }).catch(function (err) {
                console.log(err)
            })
        }
    }

    changeStockParaAlmacen(event) {

        if (event.target.checked) {
            document.querySelector("div[id='input-group-asignar_trabajador']").classList.add("oculto");
        } else {
            document.querySelector("div[id='input-group-asignar_trabajador']").classList.remove("oculto");
        }
    }

    changeProyecto(event) {
        tempCentroCostoSelected = {
            'id': event.target.options[event.target.selectedIndex].getAttribute('data-id-centro-costo'),
            'codigo': event.target.options[event.target.selectedIndex].getAttribute('data-codigo-centro-costo'),
            'descripcion': event.target.options[event.target.selectedIndex].getAttribute('data-descripcion-centro-costo')
        };
        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        if (tempCentroCostoSelected.id > 0) {
            if (tbodyChildren.length > 0) {
                for (let i = 0; i < tbodyChildren.length; i++) {
                    tbodyChildren[i].querySelector("input[class='centroCosto']").value = tempCentroCostoSelected.id;
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").setAttribute('title', tempCentroCostoSelected.descripcion);
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").textContent = tempCentroCostoSelected.codigo;
                    tbodyChildren[i].querySelector("button[name='centroCostos']").setAttribute('disabled', true);
                    tbodyChildren[i].querySelector("button[name='centroCostos']").setAttribute('title', 'El centro de costo esta asignado a un proyecto');
                }
            }

        } else {
            Swal.fire(
                '',
                'El proyecto seleccionado no tiene un centro de costo preasignado, puede seleccionar manualmente',
                'info'
            );
            if (tbodyChildren.length > 0) {
                for (let i = 0; i < tbodyChildren.length; i++) {
                    tbodyChildren[i].querySelector("input[class='centroCosto']").value = '';
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").setAttribute('title', '');
                    tbodyChildren[i].querySelector("p[class='descripcion-centro-costo']").textContent = '';
                    tbodyChildren[i].querySelector("button[name='centroCostos']").removeAttribute('disabled');
                    tbodyChildren[i].querySelector("button[name='centroCostos']").setAttribute('title', '');
                }
            }
        }
    }

    updateConcepto(obj) {

        if (obj.target.value.length > 0) {
            obj.target.closest('div').classList.remove("has-error");
            if (obj.target.closest("div").querySelector("span")) {
                obj.target.closest("div").querySelector("span").remove();
            }
        } else {
            obj.target.closest('div').classList.add("has-error");
        }
    }
    updateEmpresa(obj) {

        if (obj.target.value > 0) {
            obj.target.closest('div').classList.remove("has-error");
            if (obj.target.closest("div").querySelector("span")) {
                obj.target.closest("div").querySelector("span").remove();

            }
        } else {
            obj.target.closest('div').classList.add("has-error");
        }
    }
    updateSede(obj) {
        if (obj.target.value > 0) {
            obj.target.closest('div').classList.remove("has-error");
            if (obj.target.closest("div").querySelector("span")) {
                obj.target.closest("div").querySelector("span").remove();
            }
        } else {
            obj.target.closest('div').classList.add("has-error");
        }
    }
    updateSedeByPassingElement(obj) {
        if (obj.value > 0) {
            obj.closest('div').classList.remove("has-error");
            if (obj.closest("div").querySelector("span")) {
                obj.closest("div").querySelector("span").remove();
            }
        } else {
            obj.closest('div').classList.add("has-error");
        }
    }
    updateFechaLimite(obj) {
        if (obj.target.value.length > 0) {
            obj.target.closest('div').classList.remove("has-error");
            if (obj.target.closest("div").querySelector("span")) {
                obj.target.closest("div").querySelector("span").remove();
            }
        } else {
            obj.target.closest('div').classList.add("has-error");
        }
    }
    updateDivision(obj) {
        let currentIdGrupo = obj.target.options[obj.target.selectedIndex].dataset.idGrupo;
        document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value = currentIdGrupo;

        if (obj.target.value > 0) {
            obj.target.closest('div').classList.remove("has-error");
            if (obj.target.closest("div").querySelector("span")) {
                obj.target.closest("div").querySelector("span").remove();
            }
        } else {
            obj.target.closest('div').classList.add("has-error");
        }

        this.presupuestoInternoView.llenarComboPresupuestoInterno(currentIdGrupo, obj.target.value,null);

        this.llenarComboProyectos(currentIdGrupo); 

        // mostrar sección segun el grupo de la división seleccionada y el grupo al que pertenece el usuario

        grupos.forEach(element => {
            id_grupo_usuario_sesion_list.push(element.id_grupo);
        });

        if (currentIdGrupo == 2 && id_grupo_usuario_sesion_list.includes(2)) { // seleccion de una división que pertenece al grupo comercial y debe el usuario tener acceso al grupo comercial
            document.querySelector("select[name='id_proyecto']").value = "";
            document.querySelector("select[name='id_presupuesto_interno']").removeAttribute("disabled");
            document.querySelector("button[name='btnSearchCDP']").removeAttribute("disabled");
            document.querySelector("select[name='id_proyecto']").removeAttribute("disabled");

            hiddeElement('mostrar', 'form-requerimiento', [
                'input-group-cdp'
            ]);
        }else{
            hiddeElement('ocultar', 'form-requerimiento', [
                'input-group-cdp'
            ]);
        }

        if (currentIdGrupo == 3 && id_grupo_usuario_sesion_list.includes(3)) { // seleccion de una división que pertenece al grupo proyectos y debe el usuario tener acceso al grupo proyectos
            document.querySelector("input[name='id_cc']").value = "";
            document.querySelector("input[name='codigo_oportunidad']").value = '';
            document.querySelector("select[name='id_presupuesto_interno']").removeAttribute("disabled");
            document.querySelector("input[name='id_cc']").removeAttribute("disabled");
            document.querySelector("select[name='id_proyecto']").removeAttribute("disabled");

            // hiddeElement('mostrar', 'form-requerimiento', [
            //     'input-group-proyecto'
            // ]);
        }

    }

    llenarComboProyectos(idGrupo,idProyecto=null){
        this.requerimientoCtrl.obtenerListaProyectos(idGrupo).then((res) => {
            this.construirListaProyecto(res,idProyecto);
        }).catch(function (err) {
            console.log(err)
        })
    }

    
    construirListaProyecto(data,idProyecto=null){
        // console.log(data);

        let selectElement = document.querySelector("div[id='input-group-proyecto'] select[name='id_proyecto']");
        selectElement.innerHTML='';
        let option = document.createElement("option");
        option.text = "Seleccionar un proyecto";
        option.value = '';
        selectElement.add(option);

        data.forEach(element => {
            let option = document.createElement("option");
            option.text = element.codigo+' - '+element.descripcion;
            option.value = element.id_proyecto;
            option.setAttribute('data-codigo', element.codigo);
            option.setAttribute('data-id-centro-costo', element.id_centro_costo);
            option.setAttribute('data-codigo-centro-costo', element.codigo_centro_costo);
            option.setAttribute('data-descripcion-centro-costo', element.descripcion_centro_costo);
            if (element.id_proyecto == idProyecto) {
                option.selected = true;
            }
            selectElement.add(option);
        });
    }


    updateTipoRequerimiento(obj) {
        if (obj.target.value > 0) {
            obj.target.closest('div').classList.remove("has-error");
            if (obj.target.closest("div").querySelector("span")) {
                obj.target.closest("div").querySelector("span").remove();
            }
        } else {
            obj.target.closest('div').classList.add("has-error");
        }

        if (obj.target.value == 6 || obj.target.value == 7) { // se seleccionó el tipo de requerimiento de "atención de garantías" o "Otros"
            document.querySelector("div[id='input-group-incidencia']").removeAttribute('hidden');
        } else {
            document.querySelector("div[id='input-group-incidencia']").setAttribute('hidden', true);
        }

        if (obj.target.value != 4) { // se seleccionó el tipo de requerimiento diferente a compras para stock
            this.actualizarEstadoBotonProductoTransformado('ACTIVAR');
        } else {
            this.actualizarEstadoBotonProductoTransformado('DESACTIVAR');
        }
    }

    actualizarEstadoBotonProductoTransformado(opcion) {
        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        switch (opcion) {
            case 'ACTIVAR':
                for (let i = 0; i < tbodyChildren.length; i++) {
                    tbodyChildren[i].querySelector("button[name='btnAsignarComoProductoTransformado[]']") ? tbodyChildren[i].querySelector("button[name='btnAsignarComoProductoTransformado[]']").style.display = "block" : false;
                }
                break;

            case 'DESACTIVAR':
                for (let i = 0; i < tbodyChildren.length; i++) {
                    tbodyChildren[i].querySelector("button[name='btnAsignarComoProductoTransformado[]']") ? tbodyChildren[i].querySelector("button[name='btnAsignarComoProductoTransformado[]']").style.display = "none" : false;
                }
                break;

            default:
                break;
        }
    }

    // detalle requerimiento

    makeId() {
        let ID = "";
        let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for (let i = 0; i < 12; i++) {
            ID += characters.charAt(Math.floor(Math.random() * 36));
        }
        return ID;
    }

    agregarFilaProducto() {

        vista_extendida();
        let idFila = this.makeId();
        // fix unidad medida que toma el html de un select oculto y debe tener por defecto seleccionado el option que viene de data
        let um = document.querySelector("select[id='selectUnidadMedida']").getElementsByTagName('option');
        let indexUm = 0;
        for (let i = 0; i < um.length; i++) {
            if (um[i].value == 1) {
                indexUm = i;
            }
        }
        document.querySelector("select[id='selectUnidadMedida']").getElementsByTagName('option')[indexUm].setAttribute("selected", "");
        //  fin fix unidad medida

        let tipoRequerimiento = document.querySelector("form[id='form-requerimiento'] select[name='tipo_requerimiento']").value;
        let idGrupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;

        document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
        <td></td>
        <td><p class="descripcion-partida">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-info handleClickCargarModalPartidas" name="partida">Seleccionar</button>
            <div class="form-group">
                <input type="text" class="partida" name="idPartida[]" hidden>
            </div>
        </td>
        <td><p class="descripcion-centro-costo" title="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.descripcion : '(NO SELECCIONADO)'}">${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.codigo : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary handleClickCargarModalCentroCostos" name="centroCostos"  ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" >Seleccionar</button>
            <div class="form-group">
                <input type="text" class="centroCosto" name="idCentroCosto[]" value="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.id : ''}" hidden>
            </div>
        </td>
        <td>
            <input class="form-control input-sm" type="text" name="partNumber[]" placeholder="Part number">
            <input type="number" class="conTransformacion" max="1" min="0" name="conTransformacion[]" value="0" hidden>
            <br><span class="badge badge-secondary conSinTransformacionText"></span>

        </td>
        <td>
            <div class="form-group">
                <textarea class="form-control input-sm descripcion handleBlurUpdateDescripcionItem" name="descripcion[]" placeholder="Descripción" ></textarea></td>
            </div>
        <td><select name="unidad[]" class="form-control input-sm">${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
        <td>
            <div class="form-group">
                <input class="form-control input-sm cantidad text-right handleBurUpdateSubtotal handleBlurUpdateCantidadItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="1" name="cantidad[]" placeholder="Cantidad">
            </div>
        </td>
        <td>
            <div class="form-group">
                <input class="form-control input-sm precio text-right handleBurUpdateSubtotal handleBlurUpdatePrecioItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="0" name="precioUnitario[]" placeholder="Precio U."></td>
            </div>
        <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
        <td><textarea class="form-control input-sm" name="motivo[]" placeholder="Motivo de requerimiento de item (opcional)"></textarea></td>
        <td>
            <div class="btn-group" role="group">
                <input type="hidden" class="tipoItem" name="tipoItem[]" value="1">
                <input type="hidden" class="idRegister" name="idRegister[]" value="${idFila}">
                <button type="button" class="btn btn-warning btn-xs handleClickAdjuntarArchivoItem" name="btnAdjuntarArchivoItem[]" data-id="${idFila}" title="Adjuntos" >
                    <i class="fas fa-paperclip"></i>
                    <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>
                </button>
                <button type="button" class="btn btn-danger btn-xs handleClickEliminarItem" name="btnEliminarItem[]" title="Eliminar"  ><i class="fas fa-trash-alt"></i></button>
                <button type="button" class="btn btn-default btn-xs handleClickAsignarComoProductoTransformado" name="btnAsignarComoProductoTransformado[]" title="sin transformación" style="display:${document.querySelector("select[name='tipo_requerimiento']").value == 6 ? "block" : "none"};"><i class="fas fa-random"></i></button>
            </div>
        </td>
        </tr>`);

        this.updateContadorItem();
    }
    agregarFilaServicio() {
        vista_extendida();
        let idFila = this.makeId();

        // fix unidad medida que toma el html de un select oculto y debe tener por defecto seleccionado el option que viene de data
        let um = document.querySelector("select[id='selectUnidadMedida']").getElementsByTagName('option');
        let indexUm = 0;
        for (let i = 0; i < um.length; i++) {
            if (um[i].value == 17) {
                indexUm = i;
            }
        }
        document.querySelector("select[id='selectUnidadMedida']").getElementsByTagName('option')[indexUm].setAttribute("selected", "");
        //  fin fix unidad medida

        document.querySelector("tbody[id='body_detalle_requerimiento']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
        <td></td>
        <td><p class="descripcion-partida">(NO SELECCIONADO)</p><button type="button" class="btn btn-xs btn-info handleClickCargarModalPartidas" name="partida">Seleccionar</button>
            <div class="form-group">
                <input type="text" class="partida" name="idPartida[]" hidden>
            </div>
            </td>
            <td><p class="descripcion-centro-costo" title="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.descripcion : '(NO SELECCIONADO)'}">${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.codigo : '(NO SELECCIONADO)'}</p><button type="button" class="btn btn-xs btn-primary handleClickCargarModalCentroCostos" name="centroCostos"  ${tempCentroCostoSelected != undefined ? 'disabled' : ''} title="${tempCentroCostoSelected != undefined ? 'El centro de costo esta asignado a un proyecto' : ''}" >Seleccionar</button>
            <div class="form-group">
                <input type="text" class="centroCosto" name="idCentroCosto[]" value="${tempCentroCostoSelected != undefined ? tempCentroCostoSelected.id : ''}" hidden>
            </div>
        </td>
        <td>(Servicio)<input type="hidden" name="partNumber[]"></td>
        <td>
            <div class="form-group">
                <textarea class="form-control input-sm descripcion handleBlurUpdateDescripcionItem" name="descripcion[]" placeholder="Descripción"></textarea>
            </div>
        </td>
        <td><select name="unidad[]" class="form-control input-sm">${document.querySelector("select[id='selectUnidadMedida']").innerHTML}</select></td>
        <td>
            <div class="form-group">
                <input class="form-control input-sm cantidad text-right handleBurUpdateSubtotal handleBlurUpdateCantidadItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="1" name="cantidad[]"  placeholder="Cantidad">
            </div>
        </td>
        <td>
            <div class="form-group">
                <input class="form-control input-sm precio text-right handleBurUpdateSubtotal handleBlurUpdatePrecioItem handleBlurCalcularPresupuestoUtilizadoYSaldoPorPartida" type="number" min="0" name="precioUnitario[]"  placeholder="Precio U.">
            </div>
        </td>
        <td style="text-align:right;"><span class="moneda" name="simboloMoneda">${document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo}</span><span class="subtotal" name="subtotal[]">0.00</span></td>
        <td><textarea class="form-control input-sm" name="motivo[]" placeholder="Motivo de requerimiento de item (opcional)"></textarea></td>
        <td>
            <div class="btn-group" role="group">
                <input type="hidden" class="tipoItem" name="tipoItem[]" value="2">
                <input type="hidden" class="idRegister" name="idRegister[]" value="${idFila}">
                <button type="button" class="btn btn-warning btn-xs handleClickAdjuntarArchivoItem" name="btnAdjuntarArchivoItem[]"  data-id="${idFila}" title="Adjuntos" >
                    <i class="fas fa-paperclip"></i>
                    <span class="badge" name="cantidadAdjuntosItem" style="position:absolute; top:-10px; left:-10px; border: solid 0.1px;">0</span>
                </button>
                <button type="button" class="btn btn-danger btn-xs handleClickEliminarItem" name="btnEliminarItem[]" title="Eliminar" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`);


        this.updateContadorItem();
    }

    updateContadorItem() {
        let childrenTableTbody = document.querySelector("tbody[id='body_detalle_requerimiento']").children;

        for (let index = 0; index < childrenTableTbody.length; index++) {
            childrenTableTbody[index].firstElementChild.textContent = index + 1
        }
    }
    autoUpdateSubtotal() {

        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        for (let i = 0; i < tbodyChildren.length; i++) {
            this.updateSubtotal(tbodyChildren[i]);
        }
    }

    updateSubtotal(obj) {
        // console.log(obj);
        let tr = obj.closest("tr");
        let cantidad = parseFloat(tr.querySelector("input[class~='cantidad']").value);
        let precioUnitario = parseFloat(tr.querySelector("input[class~='precio']").value);
        let subtotal = (cantidad * precioUnitario);
        tr.querySelector("span[class='subtotal']").textContent = Util.formatoNumero(subtotal, 2);
        this.calcularTotal();
    }


    updatePartidaItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }
    }
    updateCentroCostoItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }
    }

    updateCantidadItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }

    }
    updatePrecioItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }

    }
    updateDescripcionItem(obj) {
        let text = obj.value;
        if (text.length > 0) {
            obj.closest("div").classList.remove('has-error');
            if (obj.closest("td").querySelector("span")) {
                obj.closest("td").querySelector("span").remove();
            }
        } else {
            obj.closest("div").classList.add('has-error');
        }

    }

    calcularTotal() {

        let TableTBody = document.querySelector("tbody[id='body_detalle_requerimiento']");
        let childrenTableTbody = TableTBody.children;
        let monto_subtotal = 0;
        for (let index = 0; index < childrenTableTbody.length; index++) {
            if (childrenTableTbody[index].dataset.estado != 7) {
                // console.log(childrenTableTbody[index]);
                let cantidad = parseFloat(childrenTableTbody[index].querySelector("input[class~='cantidad']").value ? childrenTableTbody[index].querySelector("input[class~='cantidad']").value : 0);
                let precioUnitario = parseFloat(childrenTableTbody[index].querySelector("input[class~='precio']").value ? childrenTableTbody[index].querySelector("input[class~='precio']").value : 0);
                monto_subtotal += (cantidad * precioUnitario);
            }
        }
        let monto_igv = 0;

        let incluyeIGV = document.querySelector("input[name='incluye_igv']").checked;
        if (incluyeIGV == true) {
            monto_igv = monto_subtotal * 0.18;
        }
        let monto_total = monto_subtotal + monto_igv;
        document.querySelector("label[name='monto_subtotal']").textContent = Util.formatoNumero(monto_subtotal, 2);
        document.querySelector("input[name='monto_subtotal']").value = monto_subtotal;
        document.querySelector("label[name='monto_igv']").textContent = Util.formatoNumero(monto_igv, 2);
        document.querySelector("input[name='monto_igv']").value = monto_igv;
        document.querySelector("label[name='monto_total']").textContent = Util.formatoNumero(monto_total, 2);
        document.querySelector("input[name='monto_total']").value = monto_total;
    }

    actualizarValorIncluyeIGV() {
        this.calcularTotal();
        this.calcularPresupuestoUtilizadoYSaldoPorPartida();

    }

    // partidas
    cargarModalPartidas(obj) {
        // anterior modal
        this.limpiarTabla('listaPartidas');

        tempObjectBtnPartida = obj.target;
        let id_grupo = document.querySelector("form[id='form-requerimiento'] input[name='id_grupo']").value;
        let id_proyecto = document.querySelector("form[id='form-requerimiento'] select[name='id_proyecto']").value;
        let usuarioProyectos = false;
        grupos.forEach(element => {
            if (element.id_grupo == 3) { // proyectos
                usuarioProyectos = true
            }
        });
        if (id_grupo > 0) {
            $('#modal-partidas').modal({
                show: true,
                backdrop: 'true'
            });

            if (!$("select[name='id_presupuesto_interno']").val() > 0) { //* si presupuesto interno fue seleccionado, no cargar presupuesto antiguo.

                this.listarPartidas(id_grupo, id_proyecto > 0 ? id_proyecto : null);
            }
        } else {
            Swal.fire(
                '',
                'No se puedo seleccionar el grupo al que pertence el usuario.',
                'warning'
            );
        }
    }

    listarPartidas(idGrupo, idProyecto) {
        this.limpiarTabla('listaPartidas');
        this.requerimientoCtrl.obtenerListaPartidas(idGrupo, idProyecto).then((res) => {
            this.construirListaPartidas(res);

        }).catch(function (err) {
            console.log(err)
        })
    }

    construirListaPartidas(data) {

        let html = '';
        let isVisible = '';
        data['presupuesto'].forEach(presupuesto => {
            html += `
            <div id='${presupuesto.codigo}' class="panel panel-primary" style="width:100%; overflow: auto;">
                <h5 class="panel-heading handleClickapertura" data-id-presup="${presupuesto.id_presup}" style="margin: 0; cursor: pointer;">
                <i class="fas fa-chevron-right"></i>
                    &nbsp; ${presupuesto.descripcion}
                </h5>
                <div id="pres-${presupuesto.id_presup}" class="oculto" style="width:100%;">
                    <table class="table table-bordered table-condensed partidas" id="listaPartidas" width="100%" style="font-size:0.9em">
                        <tbody>
            `;

            data['titulos'].forEach(titulo => {
                if (titulo.id_presup == presupuesto.id_presup) {
                    html += `
                    <tr id="com-${titulo.id_titulo}">
                        <td><strong>${titulo.codigo}</strong></td>
                        <td><strong>${titulo.descripcion}</strong></td>
                        <td class="right ${isVisible}"><strong>S/${Util.formatoNumero(titulo.total, 2)}</strong></td>
                    </tr> `;

                    data['partidas'].forEach(partida => {
                        if (partida.id_presup == presupuesto.id_presup) {
                            if (titulo.codigo == partida.cod_padre) {
                                html += `<tr id="par-${partida.id_partida}">
                                    <td style="width:15%; text-align:left;" name="codigo">${partida.codigo}</td>
                                    <td style="width:75%; text-align:left;" name="descripcion">${partida.descripcion}</td>
                                    <td style="width:15%; text-align:right;" name="importe_total" class="right ${isVisible}" data-presupuesto-total="${partida.importe_total}" >S/${Util.formatoNumero(partida.importe_total, 2)}</td>
                                    <td style="width:5%; text-align:center;"><button class="btn btn-success btn-xs handleClickSelectPartida" data-id-partida="${partida.id_partida}">Seleccionar</button></td>
                                </tr>`;
                            }
                        }
                    });

                }


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

    changeBtnIcon(obj) {

        if (obj.currentTarget.children[0].className == 'fas fa-chevron-right') {

            obj.currentTarget.children[0].classList.replace('fa-chevron-right', 'fa-chevron-down')
        } else {
            obj.currentTarget.children[0].classList.replace('fa-chevron-down', 'fa-chevron-right')
        }
    }

    selectPartida(idPartida) {
        // console.log(idPartida);
        let codigo = $("#par-" + idPartida + " ").find("td[name=codigo]")[0].innerHTML;
        let descripcion = $("#par-" + idPartida + " ").find("td[name=descripcion]")[0].innerHTML;
        let presupuestoTotal = $("#par-" + idPartida + " ").find("td[name=importe_total]")[0].dataset.presupuestoTotal;

        tempObjectBtnPartida.nextElementSibling.querySelector("input[class='partida']").value = idPartida;
        tempObjectBtnPartida.textContent = 'Cambiar';

        let tr = tempObjectBtnPartida.closest("tr");
        tr.querySelector("p[class='descripcion-partida']").dataset.idPartida = idPartida;
        tr.querySelector("p[class='descripcion-partida']").textContent = codigo
        tr.querySelector("p[class='descripcion-partida']").dataset.presupuestoTotal = presupuestoTotal;
        tr.querySelector("p[class='descripcion-partida']").setAttribute('title', descripcion);

        this.updatePartidaItem(tempObjectBtnPartida.nextElementSibling.querySelector("input[class='partida']"));
        $('#modal-partidas').modal('hide');
        // tempObjectBtnPartida = null;  debe estar

        this.calcularPresupuestoUtilizadoYSaldoPorPartida();
    }

    calcularPresupuestoUtilizadoYSaldoPorPartida() {
        let tempPartidasActivas = [];
        let partidaAgregadas = [];
        let subtotalItemList = [];
        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;

        let idMonedaPresupuestoUtilizado = document.querySelector("select[name='moneda']").value;
        let simboloMonedaPresupuestoUtilizado = document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo;
        let actualTipoCambioCompra = document.querySelector("span[id='tipo_cambio_compra']").textContent;



        for (let index = 0; index < tbodyChildren.length; index++) {
            if (tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida > 0) {
                if (!partidaAgregadas.includes(tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida)) {
                    partidaAgregadas.push(tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida);
                    tempPartidasActivas.push({
                        'id_partida': tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida,
                        'codigo': tbodyChildren[index].querySelector("p[class='descripcion-partida']").title,
                        'descripcion': tbodyChildren[index].querySelector("p[class='descripcion-partida']").textContent,
                        'presupuesto_total': tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.presupuestoTotal,
                        'id_moneda_presupuesto_utilizado': idMonedaPresupuestoUtilizado,
                        'simbolo_moneda_presupuesto_utilizado': simboloMonedaPresupuestoUtilizado,
                        'presupuesto_utilizado_al_cambio': 0,
                        'presupuesto_utilizado': 0,
                        'saldo': 0
                    });
                }

                let subtotal = (tbodyChildren[index].querySelector("input[class~='cantidad']").value > 0 ? tbodyChildren[index].querySelector("input[class~='cantidad']").value : 0) * (tbodyChildren[index].querySelector("input[class~='precio']").value > 0 ? tbodyChildren[index].querySelector("input[class~='precio']").value : 0);
                if (document.querySelector("input[name='incluye_igv']").checked == true) {
                    subtotal = (subtotal * 0.18) + subtotal;
                }

                subtotalItemList.push({
                    'id_partida': tbodyChildren[index].querySelector("p[class='descripcion-partida']").dataset.idPartida,
                    'subtotal': subtotal
                });

            }
        }


        for (let p = 0; p < tempPartidasActivas.length; p++) {
            for (let i = 0; i < subtotalItemList.length; i++) {
                if (tempPartidasActivas[p].id_partida == subtotalItemList[i].id_partida) {
                    tempPartidasActivas[p].presupuesto_utilizado += subtotalItemList[i].subtotal;
                }
            }
        }

        for (let p = 0; p < tempPartidasActivas.length; p++) {
            if (tempPartidasActivas[p].id_moneda_presupuesto_utilizado == 2) { // moneda dolares
                let alCambio = tempPartidasActivas[p].presupuesto_utilizado * actualTipoCambioCompra;
                tempPartidasActivas[p].presupuesto_utilizado_al_cambio = alCambio;
                tempPartidasActivas[p].saldo = tempPartidasActivas[p].presupuesto_total - (alCambio > 0 ? alCambio : 0);
            } else {
                tempPartidasActivas[p].saldo = tempPartidasActivas[p].presupuesto_total - (tempPartidasActivas[p].presupuesto_utilizado > 0 ? tempPartidasActivas[p].presupuesto_utilizado : 0);

            }
        }

        for (let p = 0; p < tempPartidasActivas.length; p++) {

        }


        this.validarPresupuestoUtilizadoYSaldoPorPartida(tempPartidasActivas);
        this.construirTablaPresupuestoUtilizadoYSaldoPorPartida(tempPartidasActivas);
        // console.log(tempPartidasActivas);
    }
    validarPresupuestoUtilizadoYSaldoPorPartida(data) {


        let mensajeAlerta = '';

        data.forEach(partida => {
            if (partida.saldo < 0) {

                mensajeAlerta += `La partida ${partida.codigo} - ${partida.descripcion} a excedido el presupuesto asignado, tiene un saldo actual de ${Util.formatoNumero(partida.saldo, 2)}. \n`
            }
        });
        if (mensajeAlerta.length > 0) {

            Lobibox.notify('info', {
                title: false,
                size: 'normal',
                width: 500,
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: mensajeAlerta
            });


        }
    }

    construirTablaPresupuestoUtilizadoYSaldoPorPartida(data) {
        this.limpiarTabla('listaPartidasActivas');
        data.forEach(element => {

            document.querySelector("tbody[id='body_partidas_activas']").insertAdjacentHTML('beforeend', `<tr style="text-align:center">
                <td>${element.codigo}</td>
                <td>${element.descripcion}</td>
                <td style="text-align:right;"><span>S/</span>${Util.formatoNumero(element.presupuesto_total, 2)}</td>
                <td style="text-align:right;"><span class="simboloMoneda">${element.simbolo_moneda_presupuesto_utilizado}</span>${element.presupuesto_utilizado_al_cambio > 0 ? (Util.formatoNumero(element.presupuesto_utilizado, 2) + ' (S/' + Util.formatoNumero(element.presupuesto_utilizado_al_cambio, 2) + ')') : (Util.formatoNumero(element.presupuesto_utilizado, 2))}</td>
                <td style="text-align:right; color:${element.saldo >= 0 ? '#333' : '#dd4b39'}"><span>S/</span>${Util.formatoNumero(element.saldo, 2)}</td>
            </tr>`);

        });

    }

    //centro de costos
    cargarModalCentroCostos(obj) {
        tempObjectBtnCentroCostos = obj.target;

        $('#modal-centro-costos').modal({
            show: true
        });
        this.listarCentroCostos();
    }

    listarCentroCostos() {
        this.limpiarTabla('listaCentroCosto');

        this.requerimientoCtrl.obtenerCentroCostos().then((res) => {
            this.construirCentroCostos(res);
        }).catch(function (err) {
            console.log(err)
        })
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
                                        // console.log(hijo4);
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
        tr.querySelector("p[class='descripcion-centro-costo']").textContent = codigo
        tr.querySelector("p[class='descripcion-centro-costo']").setAttribute('title', descripcion);
        this.updateCentroCostoItem(tempObjectBtnCentroCostos.nextElementSibling.querySelector("input"));
        $('#modal-centro-costos').modal('hide');
        tempObjectBtnCentroCostos = null;
        // componerTdItemDetalleRequerimiento();
    }

    eliminarItem(obj) {
        let tr = obj.target.closest("tr");
        tr.remove();
        this.updateContadorItem();
        this.calcularTotal();
    }

    //adjunto cabecera requerimiento




    modalAdjuntarArchivosCabecera(obj) {
        $('#modal-adjuntar-archivos-requerimiento').modal({
            show: true
        });
        $(":file").filestyle('clear');
        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] div[id='group-action-upload-file']").classList.remove("oculto");
        let idRequerimiento = document.querySelector("form[id='form-requerimiento'] input[name='id_requerimiento']").value;
        this.listarAdjuntosDeCabecera(idRequerimiento);
    }

    getAdjuntosRequerimientoCabecera(idRequerimiento) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-adjuntos-requerimiento-cabecera/${idRequerimiento}`,
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


    listarAdjuntosDeCabecera(idRequerimiento) {
        if (idRequerimiento.length > 0) {
            //     var regExp = /[a-zA-Z]/g; //expresión regular
            //     if (regExp.test(idRequerimiento) == false) {
            //             this.getAdjuntosRequerimientoCabecera(idRequerimiento).then((adjuntoList) => {
            //                 tempArchivoAdjuntoRequerimientoCabeceraList = [];
            //                 (adjuntoList).forEach(element => {
            //                     tempArchivoAdjuntoRequerimientoCabeceraList.push({
            //                         id: element.id_adjunto,
            //                         category: element.categoria_adjunto_id,
            //                         nameFile: element.archivo,
            //                         action:'',
            //                         file: []
            //                     });

            //                 });
            //             }).catch(function (err) {
            //                 console.log(err)
            //             });
            //     }

            this.getcategoriaAdjunto().then((categoriaAdjuntoList) => {
                this.construirTablaAdjuntosRequerimientoPagoCabecera(tempArchivoAdjuntoRequerimientoCabeceraList, categoriaAdjuntoList);
            }).catch(function (err) {
                console.log(err)
            });


        }
    }

    construirTablaAdjuntosRequerimientoPagoCabecera(adjuntoList, categoriaAdjuntoList, tipoModal = null) {
        this.limpiarTabla('listaArchivosRequerimiento');
        let html = '';
        let hasHiddenBtnEliminarArchivo = '';
        let hasDisabledSelectTipoArchivo = '';
        let estadoActual = document.querySelector("form[id='form-requerimiento'] input[name='estado']").value;

        if (estadoActual == 1 || estadoActual == 3 || estadoActual == '') {
            if (document.querySelector("form[id='form-requerimiento'] input[name='id_usuario_req']").value == auth_user.id_usuario) { //usuario en sesion == usuario requerimiento
                hasHiddenBtnEliminarArchivo = '';
            } else {
                hasHiddenBtnEliminarArchivo = 'oculto';
                hasDisabledSelectTipoArchivo = 'disabled';
            }
        }

        adjuntoList.forEach(element => {
            html += `<tr id="${element.id}" style="text-align:center">
        <td style="text-align:left;">${element.nameFile}</td>
        <td>
            <select class="form-control handleChangeCategoriaAdjunto" name="categoriaAdjunto" ${hasDisabledSelectTipoArchivo}>
        `;
            categoriaAdjuntoList.forEach(categoria => {
                if (element.category == categoria.id_categoria_adjunto) {
                    html += `<option value="${categoria.id_categoria_adjunto}" selected >${categoria.descripcion}</option>`

                } else {
                    html += `<option value="${categoria.id_categoria_adjunto}">${categoria.descripcion}</option>`
                }
            });
            html += `</select>
        </td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">`;
            if (Number.isInteger(element.id)) {
                html += `<button type="button" class="btn btn-info btn-md handleClickDescargarArchivoCabeceraRequerimiento" name="btnDescargarArchivoCabeceraRequerimiento" title="Descargar" data-id="${element.id}" ><i class="fas fa-paperclip"></i></button>`;
            }
            if (tipoModal != 'lectura') {
                html += `<button type="button" class="btn btn-danger btn-md handleClickEliminarArchivoCabeceraRequerimiento ${hasHiddenBtnEliminarArchivo}" name="btnEliminarArchivoRequerimiento" title="Eliminar" data-id="${element.id}" ><i class="fas fa-trash-alt"></i></button>`;
            }
            html += `</div>
        </td>
        </tr>`;
        });
        document.querySelector("tbody[id='body_archivos_requerimiento']").insertAdjacentHTML('beforeend', html);

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

    getcategoriaAdjunto() {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `mostrar-categoria-adjunto`,
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
        this.getcategoriaAdjunto().then((categoriaAdjuntoList) => {
            this.agregarRegistroEnTablaAdjuntoRequerimientoCabecera(payload, categoriaAdjuntoList);

        }).catch(function (err) {
            console.log(err)
        })
    }

    agregarRegistroEnTablaAdjuntoRequerimientoCabecera(payload, categoriaAdjuntoList) {
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
                <button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoCabeceraRequerimiento" name="btnEliminarArchivoRequerimiento" title="Eliminar" data-id="${payload.id}" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;

        document.querySelector("tbody[id='body_archivos_requerimiento']").insertAdjacentHTML('beforeend', html);
    }

    updateContadorTotalAdjuntosRequerimientoCabecera() {
        document.querySelector("span[name='cantidadAdjuntosCabeceraRequerimiento']").textContent = tempArchivoAdjuntoRequerimientoCabeceraList.filter((element, i) => element.action != 'ELIMINAR').length;


    }

    agregarAdjuntoRequerimiento(obj) {
        this.updateContadorTotalAdjuntosRequerimientoCabecera();
        if (obj.files != undefined && obj.files.length > 0) {
            Array.prototype.forEach.call(obj.files, (file) => {

                if (this.estaHabilitadoLaExtension(file) == true) {
                    let payload = {
                        id: this.makeId(),
                        category: 1, //default: otros adjuntos
                        nameFile: file.name,
                        action: 'GUARDAR',
                        file: file
                    };
                    this.addToTablaArchivosRequerimientoCabecera(payload);

                    tempArchivoAdjuntoRequerimientoCabeceraList.push(payload);
                } else {
                    Swal.fire(
                        'Este tipo de archivo no esta permitido adjuntar',
                        file.name,
                        'warning'
                    );
                }
            });

            this.updateContadorTotalAdjuntosRequerimientoCabecera();
        }
        return false;
    }

    descargarArchivoRequerimiento(obj) {
        console.log(obj);
        if (obj.dataset.id > 0) {
            if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
                tempArchivoAdjuntoRequerimientoCabeceraList.forEach(element => {
                    if (element.id == obj.dataset.id) {
                        window.open("/files/necesidades/requerimientos/bienes_servicios/cabecera/" + element.nameFile);
                    }
                });
            }
        }
    }

    eliminarArchivoRequerimientoCabecera(obj) {
        obj.closest("tr").remove();

        var regExp = /[a-zA-Z]/g; //expresión regular
        if ((regExp.test(obj.dataset.id) == true)) {

            tempArchivoAdjuntoRequerimientoCabeceraList = tempArchivoAdjuntoRequerimientoCabeceraList.filter((element, i) => element.id != obj.dataset.id);
        } else {
            if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
                let indice = tempArchivoAdjuntoRequerimientoCabeceraList.findIndex(elemnt => elemnt.id == obj.dataset.id);
                tempArchivoAdjuntoRequerimientoCabeceraList[indice].action = 'ELIMINAR';
            } else {
                Swal.fire(
                    '',
                    'Hubo un error inesperado al intentar eliminar el adjunto, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
                    'error'
                );
            }

        }

        this.updateContadorTotalAdjuntosRequerimientoCabecera();
    }

    // adjuntos detalle requerimiento

    modalAdjuntarArchivosDetalle(obj) {

        $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
        this.limpiarTabla('listaArchivos');

        $(":file").filestyle('clear');

        tempIdRegisterActive = obj.closest('td').querySelector("input[class~='idRegister']").value;

        objBotonAdjuntoRequerimientoDetalleSeleccionado = obj;
        // document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.remove('oculto');
        this.listarArchivosAdjuntosDetalle(obj.dataset.id);
        this.actualizarEstadoBotonAdjuntarNuevoDetalleRequerimiento();
    }

    descargarArchivoItem(obj) {
        if (obj.dataset.id > 0) {
            if (tempArchivoAdjuntoRequerimientoDetalleList.length > 0) {
                tempArchivoAdjuntoRequerimientoDetalleList.forEach(element => {
                    if (element.id == obj.dataset.id) {
                        window.open("/files/necesidades/requerimientos/bienes_servicios/detalle/" + element.nameFile);
                    }
                });
            }
        }
    }

    actualizarEstadoBotonAdjuntarNuevoDetalleRequerimiento() {
        // console.log(document.querySelector("input[name='estado']").value);
        switch (document.querySelector("input[name='estado']").value) {
            case '1':
                if (document.querySelector("input[name='id_usuario_req']").value == auth_user.id_usuario) { //usuario en sesion == usuario requerimiento
                    document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.remove('oculto');
                } else {
                    document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.add('oculto');
                }
                break;

            case '2':
                document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.add('oculto');
                break;

            case '3':
                if (document.querySelector("input[name='id_usuario_req']").value == auth_user.id_usuario) { //usuario en sesion == usuario requerimiento
                    document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.remove('oculto');
                } else {
                    document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.add('oculto');
                }

                break;

            case '':
                document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.remove('oculto');
                break;

            default:
                document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.add('oculto');
                break;
        }
    }


    getAdjuntosRequerimientoDetalle(idDetalleRequerimiento) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'GET',
                url: `listar-adjuntos-requerimiento-detalle/${idDetalleRequerimiento}`,
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

    construirTablaAdjuntosRequerimientoDetalle(adjuntoList, idDetalleRequerimiento = null) {
        this.limpiarTabla('listaArchivos');
        let html = '';
        let hasDisableBtnEliminarArchivo = '';
        let estadoActual = document.querySelector("form[id='form-requerimiento'] input[name='estado']").value;

        if (estadoActual == 1 || estadoActual == 3 || estadoActual == '') {
            if (document.querySelector("form[id='form-requerimiento'] input[name='id_usuario_req']").value == auth_user.id_usuario) { //usuario en sesion == usuario requerimiento
                hasDisableBtnEliminarArchivo = '';
            } else {
                hasDisableBtnEliminarArchivo = 'oculto';
            }
        }

        adjuntoList.forEach(element => {
            if (idDetalleRequerimiento.length > 0 && idDetalleRequerimiento == element.id_detalle_requerimiento) {

                html += `<tr id="${element.id}" style="text-align:center">
            <td style="text-align:left;">${element.nameFile}</td>
            <td style="text-align:center;">
                <div class="btn-group" role="group">`;
                if (Number.isInteger(element.id)) {
                    html += `<button type="button" class="btn btn-info btn-md handleClickDescargarArchivoRequerimientoDetalle" name="btnDescargarArchivoRequerimientoDetalle" title="Descargar" data-id="${element.id}" ><i class="fas fa-paperclip"></i></button>`;
                }

                html += `<button type="button" class="btn btn-danger btn-md handleClickEliminarArchivoRequerimientoDetalle" name="btnEliminarArchivoRequerimientoDetalle" title="Eliminar" data-id="${element.id}" ><i class="fas fa-trash-alt"></i></button>

                </div>
            </td>
            </tr>`;
            }
        });
        document.querySelector("tbody[id='body_archivos_item']").insertAdjacentHTML('beforeend', html);
    }


    listarArchivosAdjuntosDetalle(idDetalleRequerimiento) {

        if (idDetalleRequerimiento.length > 0) {
            //     // this.limpiarTabla('listaArchivos');

            //     var regExp = /[a-zA-Z]/g; //expresión regular
            //     if (regExp.test(idDetalleRequerimiento) == false) {
            //         let tempArchivoAdjuntoRequerimientoDetalleList=[]
            //         this.getAdjuntosRequerimientoDetalle(idDetalleRequerimiento).then((adjuntoList) => {
            //             (adjuntoList).forEach(element => {
            //                 if(element.id_estado !=7){ // omitir anulados

            //                     tempArchivoAdjuntoRequerimientoDetalleList.push({
            //                     id: element.id_adjunto,
            //                     id_detalle_requerimiento: element.id_detalle_requerimiento,
            //                     nameFile: element.archivo,
            //                     action: '',
            //                     file: []
            //                 });
            //             }
            //             });
            //         }).catch(function (err) {
            //             console.log(err)
            //         })
            //     }


            this.construirTablaAdjuntosRequerimientoDetalle(tempArchivoAdjuntoRequerimientoDetalleList, idDetalleRequerimiento);


        }
    }

    agregarAdjuntoRequerimientoPagoDetalle(obj) {
        if (obj.files != undefined && obj.files.length > 0) {
            Array.prototype.forEach.call(obj.files, (file) => {
                if (this.estaHabilitadoLaExtension(file) == true) {
                    let payload = {
                        id: this.makeId(),
                        id_detalle_requerimiento: objBotonAdjuntoRequerimientoDetalleSeleccionado.dataset.id,
                        nameFile: file.name,
                        action: 'GUARDAR',
                        file: file
                    };
                    this.agregarRegistroEnTablaAdjuntoRequerimientoDetalle(payload);
                    tempArchivoAdjuntoRequerimientoDetalleList.push(payload);
                } else {
                    Swal.fire(
                        'Este tipo de archivo no esta permitido adjuntar',
                        file.name,
                        'warning'
                    );
                }
            });
            this.updateContadorTotalAdjuntosRequerimientoDetalle();
        }
        return false;
    }
    updateContadorTotalAdjuntosRequerimientoDetalle() {
        if (typeof objBotonAdjuntoRequerimientoDetalleSeleccionado == 'object') {
            objBotonAdjuntoRequerimientoDetalleSeleccionado.querySelector("span[name='cantidadAdjuntosItem']").textContent = tempArchivoAdjuntoRequerimientoDetalleList.filter((element, i) => (element.id_detalle_requerimiento == objBotonAdjuntoRequerimientoDetalleSeleccionado.dataset.id && element.action != 'ELIMINAR')).length;
        }

    }


    agregarRegistroEnTablaAdjuntoRequerimientoDetalle(payload) {

        let html = '';
        html = `<tr id="${payload.id}" style="text-align:center">
        <td style="text-align:left;">${payload.nameFile}</td>
        <td style="text-align:center;">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoRequerimientoDetalle" name="btnEliminarArchivoRequerimientoDetalle" title="Eliminar" data-id="${payload.id}" ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;

        document.querySelector("tbody[id='body_archivos_item']").insertAdjacentHTML('beforeend', html);
    }


    eliminarArchivoRequerimientoDetalle(obj) {
        obj.closest("tr").remove();
        // tempIdArchivoAdjuntoRequerimientoPagoDetalleToDeleteList.push(obj.dataset.id);
        var regExp = /[a-zA-Z]/g; //expresión regular
        if ((regExp.test(obj.dataset.id) == true)) {

            tempArchivoAdjuntoRequerimientoDetalleList = tempArchivoAdjuntoRequerimientoDetalleList.filter((element, i) => element.id != obj.dataset.id);
        } else {
            if (tempArchivoAdjuntoRequerimientoDetalleList.length > 0) {
                let indice = tempArchivoAdjuntoRequerimientoDetalleList.findIndex(elemnt => elemnt.id == obj.dataset.id);
                tempArchivoAdjuntoRequerimientoDetalleList[indice].action = 'ELIMINAR';
            } else {
                Swal.fire(
                    '',
                    'Hubo un error inesperado al intentar eliminar el adjunto del item, puede que no el objecto este vacio, elimine adjuntos y vuelva a seleccionar',
                    'error'
                );
            }

        }
        this.updateContadorTotalAdjuntosRequerimientoDetalle();
    }
    // guardar requerimiento

    actionGuardarEditarRequerimiento() {

        let continuar = true;
        if (document.querySelector("tbody[id='body_detalle_requerimiento']").childElementCount == 0) {
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

        if (document.querySelector("select[name='empresa']").value == 0) {
            continuar = false;
            if (document.querySelector("select[name='empresa']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una empresa)';
                document.querySelector("select[name='empresa']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='empresa']").closest('div').classList.add('has-error');
            }
        }

        if (document.querySelector("select[name='sede']").value == 0) {
            continuar = false;
            if (document.querySelector("select[name='sede']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una sede)';
                document.querySelector("select[name='sede']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='sede']").closest('div').classList.add('has-error');
            }

        }

        // if (document.querySelector("input[name='fecha_entrega']").value == '') {
        //     continuar = false;
        //     if (document.querySelector("input[name='fecha_entrega']").closest('div').querySelector("span") == null) {
        //         let newSpanInfo = document.createElement("span");
        //         newSpanInfo.classList.add('text-danger');
        //         newSpanInfo.textContent = '(Seleccione una fecha de entrega)';
        //         document.querySelector("input[name='fecha_entrega']").closest('div').querySelector("h5").appendChild(newSpanInfo);
        //         document.querySelector("input[name='fecha_entrega']").closest('div').classList.add('has-error');
        //     }

        // }

        if (document.querySelector("select[name='tipo_requerimiento']").value == 0) {
            continuar = false;
            if (document.querySelector("select[name='tipo_requerimiento']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione un tipo)';
                document.querySelector("select[name='tipo_requerimiento']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='tipo_requerimiento']").closest('div').classList.add('has-error');
            }

        }
        if (document.querySelector("select[name='division']").value == 0) {
            continuar = false;
            if (document.querySelector("select[name='division']").closest('div').querySelector("span") == null) {
                let newSpanInfo = document.createElement("span");
                newSpanInfo.classList.add('text-danger');
                newSpanInfo.textContent = '(Seleccione una división)';
                document.querySelector("select[name='division']").closest('div').querySelector("h5").appendChild(newSpanInfo);
                document.querySelector("select[name='division']").closest('div').classList.add('has-error');
            }

        }

        let tbodyChildren = document.querySelector("tbody[id='body_detalle_requerimiento']").children;
        for (let index = 0; index < tbodyChildren.length; index++) {

            if(document.querySelector("input[name='id_cc']").value =='' || document.querySelector("input[name='id_cc']").value ==null ){

                if (tbodyChildren[index].querySelector("input[class~='partida']").value == '') {
                    continuar = false;
                    if (tbodyChildren[index].querySelector("input[class~='partida']").closest('td').querySelector("span") == null) {
                        let newSpanInfo = document.createElement("span");
                        newSpanInfo.classList.add('text-danger');
                        newSpanInfo.textContent = 'Ingrese una partida';
                        tbodyChildren[index].querySelector("input[class~='partida']").closest('td').appendChild(newSpanInfo);
                        tbodyChildren[index].querySelector("input[class~='partida']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                    }
    
                }
            }
                if (tbodyChildren[index].querySelector("input[class~='centroCosto']").value == '') {
                    continuar = false;
                    if (tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').querySelector("span") == null) {
                        let newSpanInfo = document.createElement("span");
                        newSpanInfo.classList.add('text-danger');
                        newSpanInfo.textContent = 'Ingrese un centro de costo';
                        tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').appendChild(newSpanInfo);
                        tbodyChildren[index].querySelector("input[class~='centroCosto']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                    }

                }

            if (tbodyChildren[index].querySelector("input[class~='cantidad']").value == '' || tbodyChildren[index].querySelector("input[class~='cantidad']").value <= 0) {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese una cantidad';
                    tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').appendChild(newSpanInfo);
                    tbodyChildren[index].querySelector("input[class~='cantidad']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                }

            }

            if (tbodyChildren[index].querySelector("input[class~='precio']").value == '' || tbodyChildren[index].querySelector("input[class~='precio']").value <= 0) {
                continuar = false;
                if (tbodyChildren[index].querySelector("input[class~='precio']").closest('td').querySelector("span") == null) {
                    let newSpanInfo = document.createElement("span");
                    newSpanInfo.classList.add('text-danger');
                    newSpanInfo.textContent = 'Ingrese un precio';
                    tbodyChildren[index].querySelector("input[class~='precio']").closest('td').appendChild(newSpanInfo);
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
                        tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').appendChild(newSpanInfo);
                        tbodyChildren[index].querySelector("textarea[class~='descripcion']").closest('td').querySelector("div[class~='form-group']").classList.add('has-error');
                    }
                }


            }
        }
        if (continuar) {
            let formData = new FormData($('#form-requerimiento')[0]);

            if (tempArchivoAdjuntoRequerimientoDetalleList.length > 0) {

                tempArchivoAdjuntoRequerimientoDetalleList.forEach(element => {
                    if (element.action == 'GUARDAR') {
                        formData.append(`archivoAdjuntoRequerimientoDetalleGuardar${element.id_detalle_requerimiento}[]`, element.file);
                    }
                });

            }

            if (tempArchivoAdjuntoRequerimientoCabeceraList.length > 0) {
                tempArchivoAdjuntoRequerimientoCabeceraList.forEach(element => {
                    if (element.action == 'GUARDAR') {
                        formData.append(`archivoAdjuntoRequerimiento${element.category}[]`, element.file);
                        formData.append(`archivoAdjuntoRequerimientoCabeceraFileGuardar${element.category}[]`, element.file);
                    }
                });

            }

            formData.append(`archivoAdjuntoRequerimientoObject`, JSON.stringify(tempArchivoAdjuntoRequerimientoCabeceraList));
            formData.append(`archivoAdjuntoRequerimientoDetalleObject`, JSON.stringify(tempArchivoAdjuntoRequerimientoDetalleList));



            let typeActionForm = document.querySelector("form[id='form-requerimiento']").getAttribute("type"); //  register | edition
            let sustento = '';

            if (typeActionForm == 'register') {
                $.ajax({
                    type: 'POST',
                    url: 'guardar-requerimiento',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    beforeSend: (data) => { // Are not working with dataType:'jsonp'

                        // $('#modal-loader').modal({backdrop: 'static', keyboard: false});
                        var customElement = $("<div>", {
                            "css": {
                                "font-size": "24px",
                                "text-align": "center",
                                "padding": "0px",
                                "margin-top": "-400px"
                            },
                            "class": "your-custom-class",
                            "text": "Guardando requerimiento..."
                        });

                        $('#wrapper-okc').LoadingOverlay("show", {
                            imageAutoResize: true,
                            progress: true,
                            custom: customElement,
                            imageColor: "#3c8dbc"
                        });
                    },
                    success: (response) => {
                        if (response.id_requerimiento > 0) {
                            $('#wrapper-okc').LoadingOverlay("hide", true);

                            Lobibox.notify('success', {
                                title: false,
                                size: 'mini',
                                rounded: true,
                                sound: false,
                                delayIndicator: false,
                                msg: response.mensaje
                            });
                            this.RestablecerFormularioRequerimiento();
                        } else {
                            $('#wrapper-okc').LoadingOverlay("hide", true);
                            console.log(response);
                            Swal.fire(
                                '',
                                response.mensaje,
                                'error'
                            );
                        }
                    },
                    fail: (jqXHR, textStatus, errorThrown) => {
                        $('#wrapper-okc').LoadingOverlay("hide", true);
                        Swal.fire(
                            '',
                            'Lo sentimos hubo un error en el servidor al intentar guardar el requerimiento, por favor vuelva a intentarlo',
                            'error'
                        );
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
            if (typeActionForm == 'edition') {

                if (parseInt(document.querySelector("form[id='form-requerimiento'] input[name='estado']").value) == 3
                    && parseInt(document.querySelector("form[id='form-requerimiento'] input[name='id_usuario_req']").value) == auth_user.id_usuario) {
                    Swal.fire({
                        title: 'Sustente la observación',
                        input: 'textarea',
                        inputAttributes: {
                            autocapitalize: 'off',
                        },
                        inputValue: '',
                        showCancelButton: true,
                        confirmButtonText: 'Registrar',

                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            sustento = (result.value).toString();
                            if ((sustento.trim()).length > 0) {
                                formData.append(`sustento`, sustento);

                                this.actualizarRequerimiento(formData);

                            } else {
                                Swal.fire(
                                    '',
                                    'Debe escribir un sustento para actualizar y levantar la observación',
                                    'warning'
                                );
                            }
                        }
                    });

                } else {
                    this.actualizarRequerimiento(formData);

                }
            }


        } else {
            Swal.fire(
                '',
                'Por favor ingrese los datos faltantes en el formulario',
                'warning'
            );
            console.log("no se va a guardar");
        }
    }

    actualizarRequerimiento(formData) {
        $.ajax({
            type: 'POST',
            url: 'actualizar-requerimiento',
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
                    "text": "Actualizando requerimiento..."
                });

                $('#wrapper-okc').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    custom: customElement,
                    imageColor: "#3c8dbc"
                });
            },
            success: (response) => {
                if (response.id_requerimiento > 0) {
                    $('#wrapper-okc').LoadingOverlay("hide", true);
                    Lobibox.notify('success', {
                        title: false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });
                    this.cargarRequerimiento(response.id_requerimiento);
                } else {
                    $('#wrapper-okc').LoadingOverlay("hide", true);
                    console.log(response.mensaje);
                    Swal.fire(
                        '',
                        response.mensaje,
                        'error'
                    );

                }
                changeStateButton('historial'); //init.js
            },
            fail: (jqXHR, textStatus, errorThrown) => {
                $('#wrapper-okc').LoadingOverlay("hide", true);
                Swal.fire(
                    '',
                    'Lo sentimos hubo un error en el servidor al intentar guardar el requerimiento, por favor vuelva a intentarlo',
                    'error'
                );
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });

    }

    anularRequerimiento(idRequerimiento) {
        if (idRequerimiento > 0) {
            $.ajax({
                type: 'PUT',
                url: 'anular-requerimiento/' + idRequerimiento,
                dataType: 'JSON',
                beforeSend: function (data) {
                    var customElement = $("<div>", {
                        "css": {
                            "font-size": "24px",
                            "text-align": "center",
                            "padding": "0px",
                            "margin-top": "-400px"
                        },
                        "class": "your-custom-class",
                        "text": "Anulando requerimiento..."
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
                    $('#wrapper-okc').LoadingOverlay("hide", true);
                    if (response.estado == 7) {
                        Lobibox.notify(response.tipo_mensaje, {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: `${response.mensaje}`
                        });
                        // location.reload();
                        this.RestablecerFormularioRequerimiento();

                    } else {
                        Lobibox.notify(response.tipo_mensaje, {
                            title: false,
                            size: 'mini',
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: `${response.mensaje}`
                        });
                    }
                },
                fail: function (jqXHR, textStatus, errorThrown) {
                    $('#wrapper-okc').LoadingOverlay("hide", true);
                    Swal.fire(
                        '',
                        'Hubo un problema al anular el requerimiento. Por favor actualice la página e intente de nuevo',
                        'error'
                    );
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }
    }


    RestablecerFormularioRequerimiento() {
        $('#form-requerimiento')[0].reset();
        document.querySelector("span[id='codigo_requerimiento']").textContent = '';
        document.querySelector("span[id='estado_doc']").textContent = '';
        document.querySelector("span[id='nro_occ_softlink']").textContent = '';
        document.querySelector("input[name='id_grupo']").value = '';
        document.querySelector("input[name='monto_subtotal']").value = '';
        document.querySelector("input[name='monto_igv']").value = '';
        document.querySelector("input[name='monto_total']").value = '';
        this.limpiarTabla('ListaDetalleRequerimiento');
        this.limpiarTabla('listaArchivosRequerimiento');
        this.limpiarTabla('listaArchivos');
        this.limpiarTabla('listaPartidasActivas');
        this.limpiarMesajesValidacion();
        tempArchivoAdjuntoRequerimientoCabeceraList = [];
        tempArchivoAdjuntoRequerimientoDetalleList = [];
        objBotonAdjuntoRequerimientoDetalleSeleccionado = '';
        tempCentroCostoSelected = null;
        tempIdRegisterActive = null
        this.restaurarTotalMonedaDefault();
        this.calcularPresupuestoUtilizadoYSaldoPorPartida();
        document.querySelector("div[id='group-historial-revisiones']").setAttribute("hidden", true);
        document.querySelector("span[name='cantidadAdjuntosCabeceraRequerimiento']").textContent = 0;
        disabledControl(document.getElementsByName("btn-imprimir-requerimento-pdf"), true);
        // this.actualizarEstadoBotonAdjuntarNuevoCabeceraRequerimiento();
        this.actualizarEstadoBotonAdjuntarNuevoDetalleRequerimiento();
    }

    cancelarRequerimiento() {
        this.RestablecerFormularioRequerimiento();

    }

    limpiarMesajesValidacion() {
        let allDivError = document.querySelectorAll("div[class='form-group has-error']");
        let allSpanDanger = document.querySelectorAll("span[class~='text-danger']");
        if (allDivError.length > 0) {
            allDivError.forEach(element => {
                element.classList.remove('has-error');
            });
        }
        if (allSpanDanger.length > 0) {
            allSpanDanger.forEach(element => {
                element.remove();
            });
        }

    }

    restaurarTotalMonedaDefault() {
        let allSelectorTotal = document.getElementsByName("monto_subtotal");
        let simboloMonedaPresupuestoUtilizado = document.querySelector("select[name='moneda']").options[document.querySelector("select[name='moneda']").selectedIndex].dataset.simbolo
        let allSelectorSimboloMoneda = document.getElementsByName("simboloMoneda");
        if (allSelectorSimboloMoneda.length > 0) {
            allSelectorSimboloMoneda.forEach(element => {
                element.textContent = simboloMonedaPresupuestoUtilizado;
            });
        }
        if (allSelectorTotal.length > 0) {
            allSelectorTotal.forEach(element => {
                element.textContent = '0.00';
            });
        }
    }

    asignarComoProductoTransformado(obj) {

        let inputConTransformacion = obj.closest('tr').querySelector("input[name='conTransformacion[]']");
        let conSinTransformacionText = obj.closest('tr').querySelector("span[class~='conSinTransformacionText']");
        if (inputConTransformacion != null && (inputConTransformacion.value == 0 || inputConTransformacion.value == '')) {
            inputConTransformacion.value = 1;
            obj.classList.replace('btn-default', 'btn-success');
            obj.setAttribute("title", "Con transformación");
            conSinTransformacionText.textContent = "Transformado";
        } else {
            inputConTransformacion.value = 0;
            obj.classList.replace('btn-success', 'btn-default');
            obj.setAttribute("title", "Sin transformación");
            conSinTransformacionText.textContent = "";

        }

    }


}
