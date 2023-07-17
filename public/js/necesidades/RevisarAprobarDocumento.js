
let $tablaDocumentosPorRevisarAprobar;
let $tablaDocumentosRevisados;
var tempArchivoAdjuntoRequerimientoPagoCabeceraList = [];
var tempArchivoAdjuntoRequerimientoPagoDetalleList = [];
var objBotonAdjuntoRequerimientoPagoDetalleSeleccionado = [];
var tempArchivoAdjuntoItemList = [];
var tempArchivoAdjuntoRequerimientoList = [];

class RevisarAprobarDocumentoView {

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


        $('#lista_documentos_para_revisar_aprobar').on("click", "li.handleClickTabDocumentosPendientesRevisar", (e) => {
            this.listarDocumentosPendientesParaRevisarAprobar();
        });
        $('#lista_documentos_para_revisar_aprobar').on("click", "li.handleClickTabDocumentosAprobados", (e) => {
            this.construirTablaListarDocumentosAprobados();
        });
        $('#listaDocumetosParaRevisarAprobar').on("click", "button.handleClickVerEnVistaRapidaDocumento", (e) => {
            this.verEnVistaRapidaDocumento(e.currentTarget);
        });
        $('#listaDocumetosParaRevisarAprobar').on("click", "button.handleClickAprobarDocumento", (e) => {
            this.aprobarDocumento(e.currentTarget);
        });
        $('#listaDocumetosParaRevisarAprobar').on("click", "button.handleClickObservarDocumento", (e) => {
            this.observarDocumento(e.currentTarget);
        });
        $('#listaDocumetosParaRevisarAprobar').on("click", "button.handleClickRechazarDocumento", (e) => {
            this.rechazarDocumento(e.currentTarget);
        });

        $('#listaDocumetosRevisados').on("click", "button.handleClickObservarDocumento", (e) => {
            this.observarDocumento(e.currentTarget);
        });
        $('#listaDocumetosRevisados').on("click", "button.handleClickRechazarDocumento", (e) => {
            this.rechazarDocumento(e.currentTarget);
        });
        $('#listaDocumetosRevisados').on("click", "button.handleClickVerEnVistaRapidaDocumento", (e) => {
            this.verEnVistaRapidaDocumento(e.currentTarget);
        });
        // $('#listaDocumetosParaRevisarAprobar').on("click", "button.handleClickVerEnVistaRapidaRequerimientoPago", (e) => {
        //     this.verEnVistaRapidaRequerimientoPago(e.currentTarget);
        // });
        $('#modal-vista-rapida-requerimiento-pago').on("click", "a.handleClickAdjuntarArchivoCabecera", (e) => {
            this.modalAdjuntarArchivosCabecera(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-requerimiento-pago').on("click", "button.handleClickDescargarArchivoCabeceraRequerimientoPago", (e) => {
            this.descargarArchivoRequerimientoPagoCabecera (e.currentTarget);
        });
        $('#modal-vista-rapida-requerimiento-pago').on("click", "a.handleClickAdjuntarArchivoDetalle", (e) => {
            this.modalAdjuntarArchivosDetalle(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-requerimiento-pago-detalle').on("click", "button.handleClickDescargarArchivoRequerimientoPagoDetalle", (e) => {
            this.descargarArchivoRequerimientoPagoDetalle (e.currentTarget);
        });

        
        
        $('#modal-requerimiento').on("click", "a.handleClickVerAdjuntosRequerimiento", (e) => {
            this.verAdjuntosRequerimiento(e.currentTarget);
        });
        $('#modal-requerimiento').on("click", "a.handleClickVerAdjuntosItem", (e) => {
            this.verAdjuntosItem(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-requerimiento').on("click", "button.descargarArchivoRequerimiento", (e) => {
            this.descargarArchivoRequerimiento(e.currentTarget);
        });
        $('#modal-adjuntar-archivos-detalle-requerimiento').on("click", "button.descargarArchivoItem", (e) => {
            this.descargarArchivoItem(e.currentTarget);
        });
    }


    getListarDocumentosPendientesParaRevisarAprobar(idEmpresa, idSede, idGrupo, idPrioridad) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                url: `documentos-pendientes`,
                dataType: 'JSON',
                data: { 'idEmpresa': idEmpresa, 'idSede': idSede, 'idGrupo': idGrupo, 'idPrioridad': idPrioridad },
                beforeSend: data => {

                    $("#listaDocumetosParaRevisarAprobar").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                success(response) {
                    $("#listaDocumetosParaRevisarAprobar").LoadingOverlay("hide", true);
                    resolve(response);

                },
                error: function (err) {
                    $("#listaDocumetosParaRevisarAprobar").LoadingOverlay("hide", true);
                    reject(err)
                }
            });
        });
    }

    listarDocumentosPendientesParaRevisarAprobar(idEmpresa = null, idSede = null, idGrupo = null, idPrioridad = null) {
        this.getListarDocumentosPendientesParaRevisarAprobar(idEmpresa, idSede, idGrupo, idPrioridad).then((res) => {
            this.limpiarTabla('listaDocumetosParaRevisarAprobar');
            this.construirTablaListarDocumentosPendientesParaRevisarAprobar(res['data']);
            // console.log(res);
            if (res['mensaje'].length > 0) {
                console.warn(res['mensaje']);
                Lobibox.notify('warning', {
                    title: false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: res['mensaje'].toString()
                });
            }

        }).catch(function (err) {
            console.log(err)
        })

    }

    construirTablaListarDocumentosPendientesParaRevisarAprobar(data) {
        let that = this;
        vista_extendida();
        var vardataTables = funcDatatables();
        $tablaDocumentosPorRevisarAprobar = $('#listaDocumetosParaRevisarAprobar').DataTable({
            'dom': 'Bfrtip',
            'buttons': [
                // {
                //     text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros : 0',
                //     attr: {
                //         id: 'btnFiltrosListaRequerimientosElaborados',
                //         disabled: true
                //     },
                //     action: () => {
                //         // this.abrirModalFiltrosRequerimientosElaborados();

                //     },
                //     className: 'btn-default btn-sm'
                // }
            ],
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'bLengthChange': false,
            // 'serverSide': false,
            'destroy': true,
            'data': data,
            'columns': [
                { 'data': 'id_doc_aprob', 'name': 'id_doc_aprob', 'visible': false },
                { 'data': 'prioridad_descripcion', 'name': 'prioridad_descripcion', 'className': 'text-center' },
                { 'data': 'tipo_documento_descripcion', 'name': 'tipo_documento_descripcion', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
                { 'data': 'concepto', 'name': 'concepto' },
                { 'data': 'tipo_requerimiento', 'name': 'tipo_requerimiento' },
                { 'data': 'fecha_registro', 'name': 'fecha_registro', 'className': 'text-center' },
                { 'data': 'empresa_razon_social', 'name': 'empresa_razon_social', 'className': 'text-center' },
                { 'data': 'sede_descripcion', 'name': 'sede_descripcion', 'className': 'text-center' },
                { 'data': 'grupo_descripcion', 'name': 'grupo_descripcion', 'className': 'text-center' },
                { 'data': 'division_descripcion', 'name': 'division_descripcion', 'className': 'text-center' },
                { 'data': 'monto_total', 'name': 'monto_total', 'defaultContent': '', 'className': 'text-right' },
                { 'data': 'usuario_nombre_corto', 'name': 'usuario_nombre_corto' },
                { 'data': 'estado_descripcion', 'name': 'estado_descripcion' },
                { 'data': 'id_doc_aprob', 'name': 'id_doc_aprob' }
            ],
            'columnDefs': [
                {
                    'render': function (data, type, row) {
                        switch (parseInt(row['id_prioridad'])) {
                            case 1:
                                return '<div class="text-center"> <i class="fas fa-thermometer-empty green"  data-toggle="tooltip" data-placement="right" title="Normal"></i> </div>';
                                break;

                            case 2:
                                return '<div class="text-center"> <i class="fas fa-thermometer-half orange"  data-toggle="tooltip" data-placement="right" title="Alta"></i> </div>';
                                break;

                            case 3:
                                return '<div class="text-center"> <i class="fas fa-thermometer-full red"  data-toggle="tooltip" data-placement="right" title="Crítica"></i> </div>';
                                break;

                            default:
                                return '';
                                break;
                        }
                        return '';
                    }, targets: 1
                },
                {
                    'render': function (data, type, row) {
                        return row['moneda_simbolo'].concat(' ', $.number(row['monto_total'], 2));
                    }, targets: 11
                },
                {
                    'render': function (data, type, row) {
                        switch (row['id_estado']) {
                            case 1:
                                return '<span class="labelEstado label label-default" title="Estado de documento">' + row['estado_descripcion'] + '</span>' + '<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] + '</span>';
                                break;
                            case 2:
                                return '<span class="labelEstado label label-success" title="Estado de documento">' + row['estado_descripcion'] + '</span>' + '<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] + '</span>';
                                break;
                            case 3:
                                return '<span class="labelEstado label label-warning" title="Estado de documento">' + row['estado_descripcion'] + '</span>' + '<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] + '</span>';
                                break;
                            case 5:
                                return '<span class="labelEstado label label-primary" title="Estado de documento">' + row['estado_descripcion'] + '</span>' + '<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] + '</span>';
                                break;
                            case 7:
                                return '<span class="labelEstado label label-danger" title="Estado de documento">' + row['estado_descripcion'] + '</span>' + '<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] + '</span>';
                                break;
                            default:
                                return '<span class="labelEstado label label-default" title="Estado de documento">' + row['estado_descripcion'] + '</span>' + '<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] + '</span>';
                                break;

                        }
                    }, targets: 13, className: 'text-center'
                },
                {
                    'render': function (data, type, row) {

                        let dataset = `data-id-documento="${row.id_doc_aprob ?? ''}" 
                                    data-id-tipo-documento="${row.id_tp_documento ?? ''}" 
                                    data-tipo-documento="${row.tipo_documento_descripcion ?? ''}" 
                                    data-id-requerimiento="${row.id_requerimiento ?? ''}" 
                                    data-id-requerimiento-pago="${row.id_requerimiento_pago ?? ''}" 
                                    data-codigo="${row.codigo ?? ''}" 
                                    data-id-operacion="${row.id_operacion ?? ''}" 
                                    data-id-flujo="${row.id_flujo ?? ''}" 
                                    data-id-rol-aprobante="${row.id_rol_aprobante ?? ''}" 
                                    data-aprobacion-final-o-pendiente="${row.aprobacion_final_o_pendiente ?? ''}" 
                                    data-tiene-rol-con-siguiente-aprobacion="${row.tiene_rol_con_siguiente_aprobacion ?? ''}" 
                                    data-aprobar-sin-importar-orden="${row.aprobar_sin_importar_orden ?? ''}" 
                                    data-id-usuario-propietario-documento="${row.id_usuario ?? ''}"
                                    data-id-usuario-aprobante="${row.id_usuario_aprobante ?? ''}"
                                    `;
                        let containerOpenBrackets = '<center><div style="display:flex;" >';
                        let containerCloseBrackets = '</div></center>';
                        let btnVerEnModal = '<button type="button" role="button" class="btn btn-flat btn-xs btn-info handleClickVerEnVistaRapidaDocumento" name="btnVerEnVistaRapidaDocumento" ' + dataset + ' title="Vista rápida"><i class="fas fa-eye"></i></button>';
                        let btnAprobar = '<button type="button" role="button" class="btn btn-flat btn-xs btn-success handleClickAprobarDocumento" name="btnAprobarDocumento" ' + dataset + ' title="Aprobar"><i class="fas fa-check"></i></button>';
                        let btnObservar = '<button type="button" role="button" class="btn btn-flat btn-xs btn-warning handleClickObservarDocumento" name="btnObservarDocumento" ' + dataset + ' title="Observar"><i class="fas fa-exclamation-circle"></i></button>';
                        let btnAnular = '<button type="button" role="button" class="btn btn-flat btn-xs btn-danger handleClickRechazarDocumento" name="btnRechazarDocumento" ' + dataset + ' title="Rechazar"><i class="fas fa-ban"></i></button>';

                        return containerOpenBrackets + btnVerEnModal + btnAprobar + btnObservar + btnAnular + containerCloseBrackets;
                    }, targets: 14
                },

            ],
            'initComplete': function () {
                // //Boton de busqueda
                // const $filter = $('#listaDocumetosParaRevisarAprobar_filter');
                // const $input = $filter.find('input');
                // $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                // $input.off();
                // $input.on('keyup', (e) => {
                //     if (e.key == 'Enter') {
                //         $('#btnBuscar').trigger('click');
                //     }
                // });
                // $('#btnBuscar').on('click', (e) => {
                //     $tablaDocumentosPorRevisarAprobar.search($input.val()).draw();
                // })
                // //Fin boton de busqueda

            },
            "drawCallback": function (settings) {
                if (data.length == 0) {
                    Lobibox.notify('info', {
                        title: false,
                        size: 'mini',
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: `No se encontro data disponible para mostrar`
                    });
                }
                // //Botón de búsqueda
                // $('#listaDocumetosParaRevisarAprobar_filter input').prop('disabled', false);
                // $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                // $('#listaDocumetosParaRevisarAprobar_filter input').trigger('focus');
                // //fin botón búsqueda
                // $("#listaDocumetosParaRevisarAprobar").LoadingOverlay("hide", true);
            }
        });
        //Desactiva el buscador del DataTable al realizar una busqueda
        // $tablaDocumentosPorRevisarAprobar.on('search.dt', function () {
        //     $('#tableDatos_filter input').prop('disabled', true);
        //     $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        // });
    }

    construirTablaListarDocumentosAprobados() {
        let that = this;
        vista_extendida();
        var vardataTables = funcDatatables();
        $tablaDocumentosRevisados = $('#listaDocumetosRevisados').DataTable({
            'dom': 'Bfrtip',
            'buttons': [
            ],
            'language': vardataTables[0],
            'order': [[12, 'asc']],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'documentos-aprobados',
                'type': 'POST',
                beforeSend: data => {
                    $("#listaDocumetosRevisados").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                }

            },
                'columns': [
                    {
                        'render': function (data, type, row) {
                            switch (parseInt(row['id_prioridad'])) {
                                case 1:
                                    return '<div class="text-center"> <i class="fas fa-thermometer-empty green"  data-toggle="tooltip" data-placement="right" title="Normal"></i> </div>';
                                    break;
    
                                case 2:
                                    return '<div class="text-center"> <i class="fas fa-thermometer-half orange"  data-toggle="tooltip" data-placement="right" title="Alta"></i> </div>';
                                    break;
    
                                case 3:
                                    return '<div class="text-center"> <i class="fas fa-thermometer-full red"  data-toggle="tooltip" data-placement="right" title="Crítica"></i> </div>';
                                    break;
    
                                default:
                                    return '';
                                    break;
                            }
                            return '';
                        }
                    },
                { 'data': 'tipo_documento', 'name': 'tipo_documento', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'codigo', 'className': 'text-center' },
                { 'data': 'concepto', 'name': 'concepto', 'className': 'text-left' },
                { 'data': 'tipo_requerimiento', 'name': 'tipo_requerimiento', 'className': 'text-center' },
                { 'data': 'fecha_registro', 'name': 'fecha_registro', 'className': 'text-center' },
                { 'data': 'empresa', 'name': 'empresa', 'className': 'text-center' },
                { 'data': 'sede', 'name': 'sede', 'className': 'text-center' },
                { 'data': 'grupo', 'name': 'grupo', 'className': 'text-center' },
                { 'data': 'division', 'name': 'division', 'className': 'text-center' },
                {
                    'data': 'monto_total','className': 'text-center', 
                    render: function (data, type, row) {
                        return row.simbolo_moneda + $.number(row.monto_total,2);
                    }
                },
                { 'data': 'creado_por', 'name': 'creado_por', 'className': 'text-center' },
                {
                    'data': 'estado','className': 'text-center', 
                    render: function (data, type, row) {
                        let estado = `<span class="labelEstado label label-${row.bootstrap_color}" title="Estado de documento">${row.estado}</span>`;
                            estado+= `<br><span class="labelEstado label label-default" title="Estado de documento">${(row.cantidad_ordenes>0 && row.cantidad_reservas>0)?'En atención logística, con reserva':(row.cantidad_ordenes>0 && row.cantidad_reservas==0)?'En atención logística':(row.cantidad_ordenes==0 && row.cantidad_reservas>0)?'Con reserva':(row.cantidad_ordenes==0 && row.cantidad_reservas==0)?'Aun sin atención':''}</span>`;
                        return estado;
                    }
                },
                {
                    'data': 'id','className': 'text-center', 
                    render: function (data, type, row) {

                        let dataset = `data-id-documento="${row.id ?? ''}" 
                                    data-id-tipo-documento="${row.id_tp_documento ?? ''}" 
                                    data-tipo-documento="${row.tipo_documento ?? ''}" 
                                    data-id-requerimiento="${row.id_requerimiento_logistico ?? ''}" 
                                    data-id-requerimiento-pago="${row.id_requerimiento_pago ?? ''}" 
                                    data-codigo="${row.codigo ?? ''}" 
                                    data-id-rol-aprobante="${row.ultimo_rol_aprobador ?? ''}" 
                                    data-id-usuario-aprobante="${auth_user.id_usuario ?? ''}"
                                    data-id-usuario-propietario-documento="${row.id_usuario ?? ''}"
                                    data-id-flujo="${row.id_flujo ?? ''}" 
                                    `;
                        let containerOpenBrackets = '<center><div style="display:flex;" >';
                        let containerCloseBrackets = '</div></center>';
                        let btnVerEnModal = '<button type="button" role="button" class="btn btn-flat btn-xs btn-info handleClickVerEnVistaRapidaDocumento" name="btnVerEnVistaRapidaDocumento" ' + dataset + ' title="Vista rápida"><i class="fas fa-eye"></i></button>';
                        let btnObservar = '<button type="button" role="button" class="btn btn-flat btn-xs btn-warning handleClickObservarDocumento" name="btnObservarDocumento" ' + dataset + ' title="Observar"><i class="fas fa-exclamation-circle"></i></button>';
                        let btnAnular = '<button type="button" role="button" class="btn btn-flat btn-xs btn-danger handleClickRechazarDocumento" name="btnRechazarDocumento" ' + dataset + ' title="Rechazar"><i class="fas fa-ban"></i></button>';

                        return containerOpenBrackets + btnVerEnModal + (row.id_estado ==2 && (row.pago_autorizado==false && row.pagado==false)?(btnObservar + btnAnular):'') + containerCloseBrackets;
                    }
                },
            ],
            'columnDefs': [
   

            ],
            'initComplete': function () {

            },
            "drawCallback": function (settings) {
                $("#listaDocumetosRevisados").LoadingOverlay("hide", true);

            }
        });
    }

    // inicio adjunto cabecera requerimiento de pago 
    modalAdjuntarArchivosCabecera(obj) { // TODO pasar al btn el id y no usar de un input para ambos casos de mostrar solo lectura y mostrar con carga
        $('#modal-adjuntar-archivos-requerimiento-pago').modal({
            show: true
        });
        this.limpiarTabla('listaArchivosRequerimientoPagoCabecera');

        let idRequerimientoPago = null;
        if (obj.dataset.tipoModal == "lectura") {
            idRequerimientoPago = document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_requerimiento_pago']") != null ? document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_requerimiento_pago']").value : null;
            this.listarArchivosAdjuntosCabecera(idRequerimientoPago);
            document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago'] div[id='group-action-upload-file']").classList.add("oculto");
        } else {
            idRequerimientoPago = document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']") != null ? document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value : null
            this.listarArchivosAdjuntosCabecera(idRequerimientoPago);

            document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago'] div[id='group-action-upload-file']").classList.remove("oculto");
        }
    }

    listarArchivosAdjuntosCabecera(idRequerimientoPago) {
        // let idRequerimientoPago = document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value.length > 0 ? document.querySelector("div[id='modal-requerimiento-pago'] input[name='id_requerimiento_pago']").value : document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_requerimiento_pago']").value;
        if (idRequerimientoPago.length > 0) {

            var regExp = /[a-zA-Z]/g; //expresión regular

            if (regExp.test(idRequerimientoPago) == false) {
                this.getcategoriaAdjunto().then((categoriaAdjuntoList) => {
                    this.getAdjuntosRequerimientoPagoCabecera(idRequerimientoPago).then((adjuntoList) => {
                        tempArchivoAdjuntoRequerimientoPagoCabeceraList = [];
                        (adjuntoList).forEach(element => {
                            tempArchivoAdjuntoRequerimientoPagoCabeceraList.push({
                                id: element.id_requerimiento_pago_adjunto,
                                category: element.id_tp_doc,
                                nameFile: element.archivo,
                                serie: element.serie,
                                numero: element.numero,
                                fecha_emision: element.fecha_emision,
                                id_moneda: element.id_moneda,
                                monto_total: element.monto_total,
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
        // this.limpiarTabla('listaArchivosRequerimientoPagoCabecera');

        let html = '';
        let hasHiddenBtnEliminarArchivo = '';
        let hasDisabledSelectTipoArchivo = '';
        let estadoActual = document.querySelector("input[name='id_estado']").value;

        if (estadoActual == 1 || estadoActual == 3 || estadoActual == '') {
            if (document.querySelector("input[name='id_usuario']").value == auth_user.id_usuario) { //usuario en sesion == usuario requerimiento
                hasHiddenBtnEliminarArchivo = '';
            } else {
                hasHiddenBtnEliminarArchivo = 'oculto';
                hasDisabledSelectTipoArchivo = 'disabled';
            }
        }

        adjuntoList.forEach(element => {
            html += `<tr id="${element.id}" style="text-align:center">
    <td style="text-align:left;">${element.nameFile}</td>
    <td style="text-align:left;">${element.fecha_emision}</td>
    <td style="text-align:left;"> ${element.serie??''}-${element.numero??''}</td>
    <td>
        <select class="form-control handleChangeCategoriaAdjunto" name="categoriaAdjunto" ${hasDisabledSelectTipoArchivo}>
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
    <td style="text-align:left;">${element.id_moneda!=null ? (element.id_moneda ==1?'S/':(element.id_moneda==2?'$':'')):''} ${element.monto_total!=null? ($.number(element.monto_total,2,".",",")):''}</td>

    <td style="text-align:center;">
        <div class="btn-group" role="group">`;
            if (Number.isInteger(element.id)) {
                html += `<button type="button" class="btn btn-info btn-xs handleClickDescargarArchivoCabeceraRequerimientoPago" name="btnDescargarArchivoCabeceraRequerimientoPago" title="Descargar" data-id="${element.id}" ><i class="fas fa-paperclip"></i></button>`;
            }
            html += `<button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoCabeceraRequerimientoPago ${hasHiddenBtnEliminarArchivo}" name="btnEliminarArchivoRequerimientoPago" title="Eliminar" data-id="${element.id}" ><i class="fas fa-trash-alt"></i></button>
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
    // fin adjunto cabecera requerimiento de pago

    // inicio adjunto detalle requerimiento de pago
    modalAdjuntarArchivosDetalle(obj) {
        $('#modal-adjuntar-archivos-requerimiento-pago-detalle').modal({
            show: true
        });
        this.limpiarTabla('listaArchivosRequerimientoPagoDetalle');

        objBotonAdjuntoRequerimientoPagoDetalleSeleccionado = obj;
        let textoDescripcion = '';
        if (obj.dataset.tipoModal == "lectura") {
            document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago-detalle'] div[id='group-action-upload-file']").classList.add("oculto");
            textoDescripcion = (obj.closest('tr').querySelector("td[name='descripcion_servicio']")) ? ((obj.closest('tr').querySelector("td[name='descripcion_servicio']").textContent).length > 0 ? obj.closest('tr').querySelector("td[name='descripcion_servicio']").textContent : '') : '';
        } else {
            document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago-detalle'] div[id='group-action-upload-file']").classList.remove("oculto");
            textoDescripcion = (obj.closest('tr').querySelector("textarea[name='descripcion[]']")) ? ((obj.closest('tr').querySelector("textarea[name='descripcion[]']").value).length > 0 ? obj.closest('tr').querySelector("textarea[name='descripcion[]']").value : '') : '';
        }
        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento-pago-detalle'] span[id='descripcion']").textContent = textoDescripcion.length > 0 ? textoDescripcion : '';
        this.listarArchivosAdjuntosDetalle(obj.dataset.id);


    }

    listarArchivosAdjuntosDetalle(idRequerimientoPagoDetalle) {

        if (idRequerimientoPagoDetalle.length > 0) {

            var regExp = /[a-zA-Z]/g; //expresión regular

            if (regExp.test(idRequerimientoPagoDetalle) == false) {
                this.getAdjuntosRequerimientoPagoDetalle(idRequerimientoPagoDetalle).then((adjuntoList) => {
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
                hasDisableBtnEliminarArchivo = 'oculto';
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
            html += `<button type="button" class="btn btn-danger btn-xs handleClickEliminarArchivoRequerimientoPagoDetalle ${hasDisableBtnEliminarArchivo}" name="btnEliminarArchivoRequerimientoPagoDetalle" title="Eliminar" data-id="${element.id}"  ><i class="fas fa-trash-alt"></i></button>
            </div>
        </td>
        </tr>`;
        });
        document.querySelector("tbody[id='body_archivos_requerimiento_pago_detalle']").insertAdjacentHTML('beforeend', html);
    }

    descargarArchivoRequerimientoPagoDetalle(obj){
        if (tempArchivoAdjuntoRequerimientoPagoDetalleList.length > 0) {
            tempArchivoAdjuntoRequerimientoPagoDetalleList.forEach(element => {
                if (element.id == obj.dataset.id) {
                    window.open("/files/necesidades/requerimientos/pago/detalle/" + element.nameFile);
                }
            });
        }
    }

    // fin adjunto detalle requerimiento de pago
    limpiarVistaRapidaRequerimientoPago(){
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_requerimiento_pago']").value = '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_estado']").value = '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_usuario']").value = '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='codigo']").textContent = '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='concepto']").textContent = '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='razon_social_empresa']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='grupo_division']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='tipo_requerimiento']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='prioridad']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='fecha_registro']").textContent = '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='creado_por']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='solicitado_por']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='periodo']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='tipo_destinatario']").textContent ='';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='destinatario']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='banco']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='tipo_cuenta']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='moneda']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='nro_cuenta']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='nro_cci']").textContent =  '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='comentario']").textContent = '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='tipo_impuesto']").textContent = '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] span[name='simboloMoneda']").textContent = '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='listaDetalleRequerimientoPago'] span[name='simbolo_moneda']").textContent = '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='listaDetalleRequerimientoPago'] label[name='total']").textContent = '';
        
        document.querySelector("td[id='adjuntosRequerimientoPago']").innerHTML='';
        this.limpiarTabla('listaDetalleRequerimientoPago');
        this.limpiarTabla('listaHistorialRevision');

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
    mostrarDataEnVistaRapidaRequerimientoPago(data) {
        // console.log(data);
        
        // ### ==================== cabecera ====================== ###
        var destinatario,tipo_documento_destinatario,nro_documento_destinatario, banco,tipo_cuenta, tipo_cuenta, moneda, nro_cuenta, nro_cci = '';
        if(data.id_tipo_destinatario ==1 || data.id_persona >0){
            destinatario = data.persona !=null ? ((data.persona.nombres).concat(' ',data.persona.apellido_paterno).concat(' ', data.persona.apellido_materno)):'';
            tipo_documento_destinatario = data.persona != null ? (data.persona.tipo_documento_identidad !=null?data.persona.tipo_documento_identidad.descripcion:''): '';
            nro_documento_destinatario = data.persona != null ? data.persona.nro_documento : '';
            banco = data.cuenta_persona !=null ? (data.cuenta_persona.banco !=null && data.cuenta_persona.banco.contribuyente !=null ? data.cuenta_persona.banco.contribuyente.razon_social: '') :'';
            tipo_cuenta =data.cuenta_persona !=null ? (data.cuenta_persona.tipo_cuenta !=null ? data.cuenta_persona.tipo_cuenta.descripcion: '') :''; 
            moneda = data.cuenta_persona !=null ? (data.cuenta_persona.moneda !=null ? data.cuenta_persona.moneda.descripcion: '') :'';
            nro_cuenta = data.cuenta_persona !=null ? data.cuenta_persona.nro_cuenta  :'';
            nro_cci = data.cuenta_persona !=null ? data.cuenta_persona.nro_cci  :'';
        }else if(data.id_tipo_destinatario ==2 || data.id_contribuyente >0){
            destinatario = data.contribuyente !=null ? data.contribuyente.razon_social:'';
            tipo_documento_destinatario = data.contribuyente != null ? (data.contribuyente.tipo_documento_identidad !=null?data.contribuyente.tipo_documento_identidad.descripcion:''): '';
            nro_documento_destinatario = data.contribuyente != null ? data.contribuyente.nro_documento : '';
            banco = data.cuenta_contribuyente != null ? (data.cuenta_contribuyente.banco !=null && data.cuenta_contribuyente.banco.contribuyente !=null ? data.cuenta_contribuyente.banco.contribuyente.razon_social: ''): '';
            tipo_cuenta =data.cuenta_contribuyente !=null ? (data.cuenta_contribuyente.tipo_cuenta !=null ? data.cuenta_contribuyente.tipo_cuenta.descripcion: '') :''; 
            moneda = data.cuenta_contribuyente !=null ? (data.cuenta_contribuyente.moneda !=null ? data.cuenta_contribuyente.moneda.descripcion: '') :'';;
            nro_cuenta = data.cuenta_contribuyente !=null ? data.cuenta_contribuyente.nro_cuenta  :'';
            nro_cci = data.cuenta_contribuyente !=null ? data.cuenta_contribuyente.nro_cuenta_interbancaria  :'';
        }
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_requerimiento_pago']").value = data.id_requerimiento_pago;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_estado']").value = data.id_estado;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] input[name='id_usuario']").value = data.id_usuario;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='codigo']").textContent = data.codigo;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='concepto']").textContent = data.concepto;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='razon_social_empresa']").textContent = data.sede != null ? data.sede.descripcion : '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='grupo_division']").textContent = (data.grupo != null && data.grupo.descripcion != undefined ? data.grupo.descripcion : '') + (data.grupo != null && data.division != null ? '/' : '') + (data.division != null && data.division.descripcion != undefined ? data.division.descripcion : '');
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='tipo_requerimiento']").textContent = data.tipo_requerimiento_pago != null && data.tipo_requerimiento_pago.descripcion != undefined ? data.tipo_requerimiento_pago.descripcion : '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='prioridad']").textContent = data.prioridad != null && data.prioridad.descripcion != undefined ? data.prioridad.descripcion : '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='fecha_registro']").textContent = data.fecha_registro;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='creado_por']").textContent = data.creado_por != null && data.creado_por.nombre_corto != undefined ? data.creado_por.nombre_corto : '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='solicitado_por']").textContent = data.nombre_trabajador != null && data.nombre_trabajador != undefined ? data.nombre_trabajador : '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='periodo']").textContent = data.periodo != null && data.periodo.descripcion != undefined ? data.periodo.descripcion : '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='tipo_destinatario']").textContent = data.tipo_destinatario !=null?data.tipo_destinatario.descripcion :'';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='destinatario']").textContent =  destinatario;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='tipo_documento_destinatario']").textContent = tipo_documento_destinatario;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='nro_documento_destinatario']").textContent = nro_documento_destinatario;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='banco']").textContent =  banco;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='tipo_cuenta']").textContent =  tipo_cuenta;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='moneda']").textContent =  data.moneda != null && data.moneda.descripcion != undefined ? data.moneda.descripcion : '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='nro_cuenta']").textContent =  nro_cuenta;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosDestinatario'] td[id='nro_cci']").textContent =  nro_cci;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='comentario']").textContent = data.comentario;
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='tipo_impuesto']").textContent = data.tipo_impuesto==1?'Detracción':data.tipo_impuesto ==2?'Renta':'No aplica';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] span[name='simboloMoneda']").textContent = data.moneda != null && data.moneda.simbolo != undefined ? data.moneda.simbolo : '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='listaDetalleRequerimientoPago'] span[name='simbolo_moneda']").textContent = data.moneda != null && data.moneda.simbolo != undefined ? data.moneda.simbolo : '';
        document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='listaDetalleRequerimientoPago'] label[name='total']").textContent = data.monto_total;
// console.log(data);
        if (data.id_presupuesto_interno > 0) {
            document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='presupuesto_interno']").textContent = (data.presupuesto_interno !=null ?data.presupuesto_interno.codigo: '')+' - '+(data.presupuesto_interno !=null ? data.presupuesto_interno.descripcion: '');
            document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_presupuesto_interno']").classList.remove("oculto");
        } else {
            document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_presupuesto_interno']").classList.add("oculto");

        }

    
        if(data.id_cc>0){
            document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='codigo_cdp']").textContent = data.cuadro_presupuesto != null ? data.cuadro_presupuesto.codigo_oportunidad:'';;
            document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_cdp']").classList.remove("oculto");
        }else{
            document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_cdp']").classList.add("oculto");

        }
        if(data.id_proyecto>0){
            document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] td[id='proyecto_presupuesto']").textContent = data.proyecto.descripcion??'';
            document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_proyecto']").classList.remove("oculto");
        }else{
            document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] table[id='tablaDatosGenerales'] tr[id='contenedor_proyecto']").classList.add("oculto");

        }

        if (data.adjunto.length > 0) {
            document.querySelector("td[id='adjuntosRequerimientoPago']").innerHTML = `<a title="Ver archivos adjuntos de requerimiento pago" style="cursor:pointer;" data-tipo-modal="lectura" class="handleClickAdjuntarArchivoCabecera"  data-id-requerimiento-pago="">
            Ver (<span>${data.adjunto.length}</span>)
            </a>`;
        }

        // ### ==================== botonera ====================== ###
        // limpiando botonera
        let parent = document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] div[id='botonera-accion']");
        while (parent.firstChild) {
            parent.removeChild(parent.firstChild);
        }
        // si el usuario en sessión es el propiertario del documento y el estado es elaborado o observado se mostrara los botones
        if (data.id_usuario == auth_user.id_usuario && (data.id_estado == 1 || data.id_estado == 3)) {
            document.querySelector("div[id='modal-vista-rapida-requerimiento-pago'] div[id='botonera-accion']").insertAdjacentHTML('beforeend', `
            <button type="button" name="btnEditarRequerimientoPago" class="btn btn-warning btn-sm handleClickEditarRequerimientoPago" ><i class="fas fa-edit"></i> Editar</button>
            <button type="button" name="btnAnularRequerimientoPago" class="btn btn-danger btn-sm handleClickAnularRequerimientoPago"  ><i class="fas fa-ban"></i> Anular</button>   
            `)
        }

        // ### ==================== Detalle ====================== ###

        this.limpiarTabla('listaDetalleRequerimientoPago');
        // console.log(data);
        if (data.detalle.length > 0) {
            for (let i = 0; i < data.detalle.length; i++) {
                let cantidadAdjuntosItem = 0;
                cantidadAdjuntosItem = data.detalle[i].adjunto.length;

                document.querySelector("tbody[id='body_requerimiento_pago_detalle']").insertAdjacentHTML('beforeend', `<tr style="background-color:${data.detalle[i].id_estado == '7' ? '#f1d7d7' : ''}">
                <td>${i + 1}</td>
                <td title="${data.detalle[i].id_partida >0 ?(data.detalle[i].partida.descripcion).toUpperCase() :(data.detalle[i].id_partida_pi >0?(data.detalle[i].presupuesto_interno_detalle.descripcion).toUpperCase() : '')}" >${data.detalle[i].id_partida >0 ?data.detalle[i].partida.codigo :(data.detalle[i].id_partida_pi >0?data.detalle[i].presupuesto_interno_detalle.partida : '')}</td>
                <td title="${data.detalle[i].id_centro_costo>0?(data.detalle[i].centro_costo.descripcion).toUpperCase():''}">${data.detalle[i].centro_costo !=null ? data.detalle[i].centro_costo.codigo : ''}</td>
                <td name="descripcion_servicio">${data.detalle[i].descripcion != null ? data.detalle[i].descripcion : ''} </td>
                <td>${data.detalle[i].unidad_medida != null ? data.detalle[i].unidad_medida.descripcion : ''}</td>
                <td style="text-align:center;">${data.detalle[i].cantidad >= 0 ? data.detalle[i].cantidad : ''}</td>
                <td style="text-align:right;">${data.moneda != null && data.moneda.simbolo != undefined ? data.moneda.simbolo : ''}${Util.formatoNumero(data.detalle[i].precio_unitario, 2)}</td>
                <td style="text-align:right;">${data.moneda != null && data.moneda.simbolo != undefined ? data.moneda.simbolo : ''}${(data.detalle[i].subtotal ? Util.formatoNumero(data.detalle[i].subtotal, 2) : (Util.formatoNumero((data.detalle[i].cantidad * data.detalle[i].precio_unitario), 2)))}</td>
                <td style="text-align:center;">${data.detalle[i].estado != null ? data.detalle[i].estado.estado_doc : ''}</td>
                <td style="text-align: center;"> 
                    ${cantidadAdjuntosItem > 0 ? '<a title="Ver archivos adjuntos de item" style="cursor:pointer;" class="handleClickAdjuntarArchivoDetalle" data-tipo-modal="lectura" data-id="' + data.detalle[i].id_requerimiento_pago_detalle + '" >Ver (<span>' + cantidadAdjuntosItem + '</span>)</a>' : '-'}
                </td>
            </tr>`);



            }


        }

    }
    cargarDataRequerimientoPago(idRequerimientoPago) {
        
        if (idRequerimientoPago > 0) {
            $('#modal-vista-rapida-requerimiento-pago .modal-content').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc"
            });
            this.obtenerRequerimientoPago(idRequerimientoPago).then((res) => {
                $('#modal-vista-rapida-requerimiento-pago .modal-content').LoadingOverlay("hide", true);

                this.mostrarDataEnVistaRapidaRequerimientoPago(res)

            }).catch(function (err) {
                $('#modal-vista-rapida-requerimiento-pago .modal-content').LoadingOverlay("hide", true);
                Swal.fire(
                    '',
                    'Hubo un error al tratar de obtener la data',
                    'error'
                ); 
                console.log(err)
            });
        } else {
            Swal.fire(
                '',
                'Lo sentimos no se encontro un ID valido para cargar el requerimiento de pago seleccionado, por favor vuelva a intentarlo',
                'error'
            );
        }
    }
    //  inicio modal ver requerimiento B/S 
    getRequerimiento(idRequerimiento){
        return  $.ajax({
                type: 'GET',
                url:`mostrar-requerimiento/${idRequerimiento}/null`,
                dataType: 'JSON',
                });
    }

    construirSeccionDatosGenerales(data) {
        // console.log(data);
        document.querySelector("div[id='modal-requerimiento'] input[name='id_requerimiento']").value = data.id_requerimiento;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='codigo']").textContent = data.codigo;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='concepto']").textContent = data.concepto;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='razon_social_empresa']").textContent = data.razon_social_empresa +' ('+data.codigo_sede_empresa+')' ;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='division']").textContent = data.division;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='tipo_requerimiento']").textContent = data.tipo_requerimiento;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='prioridad']").textContent = data.prioridad;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='fecha_entrega']").textContent = data.fecha_entrega;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='solicitado_por']").textContent = (data.para_stock_almacen == true ? 'Para stock almacén' : (data.nombre_trabajador ? data.nombre_trabajador : '-'));
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='periodo']").textContent = data.periodo;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='creado_por']").textContent = data.persona;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='observacion']").textContent = data.observacion;
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='tipo_impuesto']").textContent = data.tipo_impuesto==1?'Detracción':data.tipo_impuesto ==2?'Renta':'No aplica';
        document.querySelector("div[id='modal-requerimiento'] span[name='simboloMoneda']").textContent = data.simbolo_moneda;
        document.querySelector("div[id='modal-requerimiento'] table[id='listaDetalleRequerimientoModal'] span[name='simbolo_moneda']").textContent = data.simbolo_moneda;
        document.querySelector("div[id='modal-requerimiento'] table[id='listaDetalleRequerimientoModal'] label[name='monto_subtotal']").textContent =$.number(data.monto_subtotal,2);
        document.querySelector("div[id='modal-requerimiento'] table[id='listaDetalleRequerimientoModal'] label[name='monto_igv']").textContent = $.number(data.monto_igv,2);
        document.querySelector("div[id='modal-requerimiento'] table[id='listaDetalleRequerimientoModal'] label[name='monto_total']").textContent = $.number(data.monto_total,2);

        if (data.id_presupuesto_interno > 0) {
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='presupuesto_interno']").textContent = (data.codigo_presupuesto_interno ?? '')+' - '+(data.descripcion_presupuesto_interno ??'');
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] tr[id='contenedor_presupuesto_interno']").classList.remove("oculto");
        } else {
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] tr[id='contenedor_presupuesto_interno']").classList.add("oculto");

        }
        if (data.id_presupuesto > 0) {
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='presupuesto_old']").textContent = (data.codigo_presupuesto_old ?? '')+' - '+(data.descripcion_presupuesto_old ??'');
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] tr[id='contenedor_presupuesto_old']").classList.remove("oculto");
        } else {
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] tr[id='contenedor_presupuesto_old']").classList.add("oculto");

        }

        if(data.id_incidencia>0){
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='incidencia']").textContent = data.codigo_incidencia;
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] tr[id='contenedor_incidencia']").classList.remove("oculto");
        }else{
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] tr[id='contenedor_incidencia']").classList.add("oculto");

        }
        if(data.id_cc>0){
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='codigo_cdp']").textContent = data.codigo_oportunidad??'';
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] tr[id='contenedor_cdp']").classList.remove("oculto");
        }else{
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] tr[id='contenedor_cdp']").classList.add("oculto");

        }
        if(data.id_proyecto>0){
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='proyecto_presupuesto']").textContent = data.descripcion_proyecto??'';
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] tr[id='contenedor_proyecto']").classList.remove("oculto");
        }else{
            document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] tr[id='contenedor_proyecto']").classList.add("oculto");

        }
        
        tempArchivoAdjuntoRequerimientoList = [];
        if (data.adjuntos.length > 0) {
            document.querySelector("td[id='adjuntosRequerimiento']").innerHTML = `<a title="Ver archivos adjuntos de requerimiento" style="cursor:pointer;"  class="handleClickVerAdjuntosRequerimiento" >
            Ver (<span name="cantidadAdjuntosRequerimiento">${data.adjuntos.length}</span>)
            </a>`;
            (data.adjuntos).forEach(element => {
                tempArchivoAdjuntoRequerimientoList.push({
                    'id': element.id_adjunto,
                    'id_requerimiento': element.id_requerimiento,
                    'archivo': element.archivo,
                    'nameFile': element.archivo,
                    'categoria_adjunto_id': element.categoria_adjunto_id,
                    'categoria_adjunto': element.categoria_adjunto,
                    'fecha_registro': element.fecha_registro,
                    'estado': element.estado
                });

            });

            // document.querySelector("a[class~='handleClickVerAdjuntosRequerimiento']") ? (document.querySelector("a[class~='handleClickVerAdjuntosRequerimiento']").addEventListener("click", this.verAdjuntosRequerimiento.bind(this), false)) : false;

        }

        let tamañoSelectAccion = document.querySelector("div[id='modal-requerimiento'] select[id='accion']").length;
        if (data.estado == 3) {
            for (let i = 0; i < tamañoSelectAccion; i++) {
                if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].value == 1) {
                    document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].setAttribute('disabled', true)
                }
            }
        } else {
            for (let i = 0; i < tamañoSelectAccion; i++) {
                if (document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].value == 1) {
                    document.querySelector("div[id='modal-requerimiento'] select[id='accion']").options[i].removeAttribute('disabled')
                }
            }
        }
    }

    construirSeccionItemsDeRequerimiento(data, simboloMoneda) {
        this.limpiarTabla('listaDetalleRequerimientoModal');
        tempArchivoAdjuntoItemList = [];
        let html = '';
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                if(data[i].estado !=7){

                let cantidadAdjuntosItem = 0;
                cantidadAdjuntosItem = data[i].adjuntos.length;
                if (cantidadAdjuntosItem > 0) {
                    (data[i].adjuntos).forEach(element => {
                        if (element.estado == 1) {
                            tempArchivoAdjuntoItemList.push(
                                {
                                    id: element.id_adjunto,
                                    idRegister: element.id_detalle_requerimiento,
                                    nameFile: element.archivo,
                                    dateFile: element.fecha_registro,
                                    estado: element.estado
                                }
                            );
                        }

                    });
                }
                document.querySelector("tbody[id='body_item_requerimiento']").insertAdjacentHTML('beforeend', `<tr>
                <td>${i + 1}</td>
                <td title="${data[i].id_partida >0 ?data[i].descripcion_partida.toUpperCase() :(data[i].id_partida_pi >0?data[i].descripcion_partida_presupuesto_interno.toUpperCase() : '')}" >${data[i].id_partida >0 ?data[i].codigo_partida :(data[i].id_partida_pi >0?data[i].codigo_partida_presupuesto_interno : '')}</td>
                <td title="${data[i].id_centro_costo>0?data[i].descripcion_centro_costo.toUpperCase():''}">${data[i].codigo_centro_costo ? data[i].codigo_centro_costo : ''}</td>
                <td>${data[i].id_tipo_item == 1 ? (data[i].producto_part_number ? data[i].producto_part_number : data[i].part_number) : '(Servicio)'}${data[i].tiene_transformacion==true?'<br><span class="label label-default">Transformado</span>':''} </td>
                <td>${data[i].producto_descripcion !=null ? data[i].producto_descripcion : (data[i].descripcion ? data[i].descripcion : '')} </td>
                <td>${data[i].unidad_medida !=null ?data[i].unidad_medida:''}</td>
                <td style="text-align:center;">${data[i].cantidad>=0?data[i].cantidad:''}</td>
                <td style="text-align:right;">${simboloMoneda}${Util.formatoNumero(data[i].precio_unitario, 2)}</td>
                <td style="text-align:right;">${simboloMoneda}${(data[i].subtotal ? Util.formatoNumero(data[i].subtotal, 2) : (Util.formatoNumero((data[i].cantidad * data[i].precio_unitario), 2)))}</td>
                <td>${data[i].motivo !=null ? data[i].motivo : ''}</td>
                <td>${data[i].estado_doc !=null ? data[i].estado_doc : ''}</td>
                <td style="text-align: center;"> 
                    ${cantidadAdjuntosItem > 0 ? '<a title="Ver archivos adjuntos de item" style="cursor:pointer;" class="handleClickVerAdjuntosItem"  data-id-detalle-requerimiento="'+data[i].id_detalle_requerimiento+'" >Ver (<span name="cantidadAdjuntosItem">' + cantidadAdjuntosItem + '</span>)</a>' : '-'}
                </td>
            </tr>`);

                // document.querySelector("a[class='handleClickVerAdjuntosItem" + i + "']") ? document.querySelector("a[class~='handleClickVerAdjuntosItem" + i + "']").addEventListener("click", this.verAdjuntosItem.bind(this, data[i].id_detalle_requerimiento), false) : false;
            }

            }


        }


    }

    construirSeccionHistorialAprobacion(data) {
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

    verAdjuntosRequerimiento() {

        this.limpiarTabla('listaArchivosRequerimiento');
        $('#modal-adjuntar-archivos-requerimiento').modal({
            show: true
        });

        document.querySelector("div[id='modal-adjuntar-archivos-requerimiento'] div[id='group-action-upload-file']").classList.add('oculto');

        let html = '';
        if (tempArchivoAdjuntoRequerimientoList.length > 0) {
            tempArchivoAdjuntoRequerimientoList.forEach(element => {
                if (element.estado == 1) {
                    html += `<tr>
                    <td style="text-align:left;">${element.archivo}</td>
                    <td style="text-align:left;">${element.categoria_adjunto}</td>
                    <td style="text-align:center;">
                        <div class="btn-group" role="group">`;
                    html += `<button type="button" class="btn btn-info btn-md descargarArchivoRequerimiento" name="btnDescargarArchivoItem" title="Descargar" data-id="${element.id}" ><i class="fas fa-paperclip"></i></button>`;
                    html += `</div>
                    </td>
                    </tr>`;

                }
            });
        }
        document.querySelector("tbody[id='body_archivos_requerimiento']").insertAdjacentHTML('beforeend', html)

    }

    verAdjuntosItem(obj) {
        
        $('#modal-adjuntar-archivos-detalle-requerimiento').modal({
            show: true,
            backdrop: 'true'
        });
        this.limpiarTabla('listaArchivos');
        document.querySelector("div[id='modal-adjuntar-archivos-detalle-requerimiento'] div[id='group-action-upload-file']").classList.add('oculto');
        let html = '';
        tempArchivoAdjuntoItemList.forEach(element => {
            if (element.idRegister ==  obj.dataset.idDetalleRequerimiento) {
                html += `<tr>
                <td style="text-align:left;">${element.nameFile}</td>
                <td style="text-align:center;">
                    <div class="btn-group" role="group">`;
                if (Number.isInteger(element.id)) {
                    html += `<button type="button" class="btn btn-info btn-md descargarArchivoItem" name="btnDescargarArchivoItem" title="Descargar" data-id="${element.id}" ><i class="fas fa-paperclip"></i></button>`;
                }
                html += `</div>
                </td>
                </tr>`;
            }
        });
        document.querySelector("tbody[id='body_archivos_item']").insertAdjacentHTML('beforeend', html);


    }

    descargarArchivoRequerimiento(obj) {

        if (tempArchivoAdjuntoRequerimientoList.length > 0) {
            tempArchivoAdjuntoRequerimientoList.forEach(element => {
                if (element.id == obj.dataset.id) {
                    window.open("/files/necesidades/requerimientos/bienes_servicios/cabecera/" + element.nameFile);
                }
            });
        }
    }

    descargarArchivoItem(obj) {
        if (tempArchivoAdjuntoItemList.length > 0) {
            tempArchivoAdjuntoItemList.forEach(element => {
                if (element.id == obj.dataset.id) {
                    window.open("/files/necesidades/requerimientos/bienes_servicios/detalle/" + element.nameFile);
                }
            });
        }
    }

    limpiarVistaRapidaRequerimientoBienesServicios(){
        document.querySelector("div[id='modal-requerimiento'] input[name='id_requerimiento']").value = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='codigo']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='concepto']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='razon_social_empresa']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='division']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='tipo_requerimiento']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='prioridad']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='fecha_entrega']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='solicitado_por']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='periodo']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='creado_por']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='tablaDatosGenerales'] td[id='observacion']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] span[name='simboloMoneda']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='listaDetalleRequerimientoModal'] span[name='simbolo_moneda']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='listaDetalleRequerimientoModal'] label[name='monto_subtotal']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='listaDetalleRequerimientoModal'] label[name='monto_igv']").textContent = '';
        document.querySelector("div[id='modal-requerimiento'] table[id='listaDetalleRequerimientoModal'] label[name='monto_total']").textContent = '';
        this.limpiarTabla('listaDetalleRequerimientoModal');
        this.limpiarTabla('listaHistorialRevision');

    }

    //  fin modal ver requerimiento B/S 

    verEnVistaRapidaDocumento(obj) {
        let idDocumento = obj.dataset.idDocumento;
        let idTipoDocumento = obj.dataset.idTipoDocumento;
        let codigoDocumento = obj.dataset.codigoDocumento;

        if (idTipoDocumento == 11) {
            $('#modal-vista-rapida-requerimiento-pago').modal({
                show: true
            });
            this.limpiarVistaRapidaRequerimientoPago();
            this.cargarDataRequerimientoPago(obj.dataset.idRequerimientoPago);

        }else if(idTipoDocumento ==1){

            $('#modal-requerimiento').modal({
                show: true,
                backdrop: 'true'
            });
            this.limpiarVistaRapidaRequerimientoBienesServicios();

            document.querySelector("div[id='modal-requerimiento'] fieldset[id='group-acciones']").classList.add("oculto");
            document.querySelector("div[id='modal-requerimiento'] button[id='btnRegistrarRespuesta']").classList.add("oculto");
    
            if(obj.dataset.idRequerimiento > 0){

                $('#modal-requerimiento .modal-content').LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
                this.getRequerimiento(obj.dataset.idRequerimiento).then((res) => {
                    $('#modal-requerimiento .modal-content').LoadingOverlay("hide", true);

                    this.construirSeccionDatosGenerales(res['requerimiento'][0]);
                    this.construirSeccionItemsDeRequerimiento(res['det_req'], res['requerimiento'][0]['simbolo_moneda']);
                    this.construirSeccionHistorialAprobacion(res['historial_aprobacion']);
                    $('#modal-requerimiento div.modal-body').LoadingOverlay("hide", true);
        
                }).catch(function (err) {
                    $('#modal-requerimiento .modal-content').LoadingOverlay("hide", true);

                    console.log(err)
                })
            }
        }
    }


    obtenerCargaUtil(obj) {
        let payload = {
            'accion': '',
            'sustento': '',
            'idDocumento': obj.dataset.idDocumento,
            'idTipoDocumento': obj.dataset.idTipoDocumento,
            'tipoDocumento': obj.dataset.tipoDocumento,
            'idRequerimiento': obj.dataset.idRequerimiento,
            'idRequerimientoPago': obj.dataset.idRequerimientoPago,
            'codigo': obj.dataset.codigo,
            'idOperacion': obj.dataset.idOperacion,
            'idFlujo': obj.dataset.idFlujo,
            'idRolAprobante': obj.dataset.idRolAprobante,
            'aprobacionFinalOPendiente': obj.dataset.aprobacionFinalOPendiente,
            'tieneRolConSiguienteAprobacion': obj.dataset.tieneRolConSiguienteAprobacion,
            'idUsuarioPropietarioDocumento': obj.dataset.idUsuarioPropietarioDocumento,
            'idUsuarioAprobante': obj.dataset.idUsuarioAprobante,
            'aprobarSinImportarOrden': obj.dataset.aprobarSinImportarOrden
        };
        return payload;
    }

    validarCargaUtil(payload) {
        let mensaje = [];

        if (!payload.idDocumento.length > 0) {
            mensaje.push("No se encontro un ID de documento valido");
        }
        if (!payload.idFlujo.length > 0) {
            mensaje.push("No se encontro un ID de flujo valido");
        }
        // if (!payload.idOperacion.length > 0) {
        //     mensaje.push("No se encontro un ID de operación valido");
        // }
        if (!payload.idRolAprobante.length > 0) {
            mensaje.push("No se encontro un ID rol del aprobante valido");
        }
        // if (!payload.aprobacionFinalOPendiente.length > 0) {
        //     mensaje.push("no se sabe si es una aprobación final o pendiente");
        // }
        // if (!payload.tieneRolConSiguienteAprobacion.length > 0) {
        //     mensaje.push("No se sabe si el rol del usuario actual tiene una siguiente aprobación");
        // }

        return mensaje;
    }

    guardarRespuesta(payload) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: 'POST',
                url: 'guardar-respuesta',
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

    aprobarDocumento(obj) {
        // let idDocumento = obj.dataset.idDocumento;
        // let codigoDocumento = obj.dataset.codigoDocumento;
        let payload = this.obtenerCargaUtil(obj);
        payload.accion = 1;
        let validarCargaUtil = this.validarCargaUtil(payload);
        if (validarCargaUtil.length == 0) {
            Swal.fire({
                title: `Esta seguro que desea aprobar el ${payload.tipoDocumento}: ${payload.codigo}`,
                text: "No podra revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, Aprobar'

            }).then((result) => {
                if (result.isConfirmed) {
                    this.guardarRespuesta(payload).then((res) => {
                        console.log(res);
                        if (res.id_aprobacion > 0) {
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
                                'error'
                            );
                        }
                        this.listarDocumentosPendientesParaRevisarAprobar();
                    }).catch(function (err) {
                        console.log(err)
                    })
                }
            })
        } else {
            Swal.fire(
                'Error en validación',
                validarCargaUtil.toString(),
                'error'
            );
        }
    }

    obtenerTabActivo(){
        let allTab= document.querySelector("ul[class='nav nav-tabs']").children;
        for (let index = 0; index < allTab.length; index++) {
            if(allTab[index].classList.contains("active")==true){
                return allTab[index].classList[0];
            }  
        }
    }
    observarDocumento(obj) {

        let payload = this.obtenerCargaUtil(obj);
        payload.accion = 3;
        let validarCargaUtil = this.validarCargaUtil(payload);
        console.log(validarCargaUtil);
        if (validarCargaUtil.length == 0) {
            Swal.fire({
                title: `Esta seguro que desea observar el ${payload.tipoDocumento}: ${payload.codigo}`,
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
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Registrar',

                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            payload.sustento = result.value;
                            this.guardarRespuesta(payload).then((res) => {
                                if (res.id_aprobacion > 0) {
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
                                        'error'
                                    );
                                }
                                if(this.obtenerTabActivo()=='handleClickTabDocumentosPendientesRevisar'){
                                    this.listarDocumentosPendientesParaRevisarAprobar();
                                    
                                }else if(this.obtenerTabActivo()=='handleClickTabDocumentosAprobados'){
                                    this.construirTablaListarDocumentosAprobados();
                                    
                                }
                            });
                        }
                    })
                    // fin susntento
                }
            })
        } else {
            Swal.fire(
                'Error en validación',
                validarCargaUtil.toString(),
                'error'
            );
        }
    }
    rechazarDocumento(obj) {
        let payload = this.obtenerCargaUtil(obj);
        payload.accion = 2;
        let validarCargaUtil = this.validarCargaUtil(payload);
        if (validarCargaUtil.length == 0) {
            Swal.fire({
                title: `Esta seguro que desea rechazar el ${payload.tipoDocumento}: ${payload.codigo}`,
                text: "No podra revertir esta acción, Se solicitará un sustente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, rechazar'

            }).then((result) => {
                if (result.isConfirmed) {
                    // inicio  sustento
                    let sustentoAnularOrden = '';
                    Swal.fire({
                        title: 'Sustente el motivo del rechazo',
                        input: 'textarea',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Registrar',

                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            payload.sustento = result.value;

                            this.guardarRespuesta(payload).then((res) => {
                                if (res.id_aprobacion > 0) {
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
                                        'error'
                                    );
                                }
                                if(this.obtenerTabActivo()=='handleClickTabDocumentosPendientesRevisar'){
                                    this.listarDocumentosPendientesParaRevisarAprobar();
                                    
                                }else if(this.obtenerTabActivo()=='handleClickTabDocumentosAprobados'){
                                    this.construirTablaListarDocumentosAprobados();
                                    
                                }
                            });
                        }
                    })
                    // fin susntento
                }
            })
        } else {
            Swal.fire(
                'Error en validación',
                validarCargaUtil.toString(),
                'error'
            );
        }
    }





}