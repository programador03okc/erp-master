
let $tablaRevisarAprobarDocumento;

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

    }


    getListarDocumentosPendientesParaRevisarAprobar(idEmpresa, idSede, idGrupo, idPrioridad){
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'POST',
                url:`documentos-pendientes`,
                dataType: 'JSON',
                data:{'idEmpresa':idEmpresa,'idSede':idSede,'idGrupo':idGrupo,'idPrioridad':idPrioridad},
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
                error: function(err) {
                    $("#listaDocumetosParaRevisarAprobar").LoadingOverlay("hide", true);
                    reject(err) 
                }
                });
            });
    }

    listarDocumentosPendientesParaRevisarAprobar(idEmpresa =null, idSede=null, idGrupo=null, idPrioridad=null) {
        this.getListarDocumentosPendientesParaRevisarAprobar(idEmpresa, idSede, idGrupo, idPrioridad).then((res) =>{
            this.construirTablaListarDocumentosPendientesParaRevisarAprobar(res['data']);
            // console.log(res);
            if(res['mensaje'].length>0){
                console.warn(res['mensaje']);
                    Lobibox.notify('warning', {
                    title:false,
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
        $tablaRevisarAprobarDocumento = $('#listaDocumetosParaRevisarAprobar').DataTable({
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
            // 'destroy': true,
            'data':  data,
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
                                return '<span class="labelEstado label label-default" title="Estado de documento">' + row['estado_descripcion'] +'</span>'+'<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] +'</span>';
                                break;
                            case 2:
                                return '<span class="labelEstado label label-success" title="Estado de documento">' + row['estado_descripcion'] + '</span>'+'<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] +'</span>';
                                break;
                            case 3:
                                return '<span class="labelEstado label label-warning" title="Estado de documento">' + row['estado_descripcion'] + '</span>'+'<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] +'</span>';
                                break;
                            case 5:
                                return '<span class="labelEstado label label-primary" title="Estado de documento">' + row['estado_descripcion'] + '</span>'+'<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] +'</span>';
                                break;
                            case 7:
                                return '<span class="labelEstado label label-danger" title="Estado de documento">' + row['estado_descripcion'] + '</span>'+'<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] +'</span>';
                                break;
                            default:
                                return '<span class="labelEstado label label-default" title="Estado de documento">' + row['estado_descripcion'] + '</span>'+'<br> <span class="labelEstado label label-default" title="Aprobaciones realizadas / Aprobaciones pendientes">' + row['cantidad_aprobados_total_flujo'] +'</span>';
                                break;

                        }
                    }, targets: 13, className: 'text-center'
                },
                {
                    'render': function (data, type, row) {
                        let containerOpenBrackets = '<center><div class="btn-group btn-group-justified" role="group" >';
                        let containerCloseBrackets = '</div></center>';
                    
                        let btnVerEnModal = '<div class="btn-group" role="group"><button type="button" role="button" class="btn btn-xs btn-info handleClickVerEnVistaRapidaDocumento" name="btnVerEnVistaRapidaDocumento" data-id-documento="' + row.id_doc_aprob + '" data-codigo-documento="' + row.codigo + '" title="Vista rápida"><i class="fas fa-eye fa-xs"></i></button></div>';
                        let btnAprobar = '<div class="btn-group" role="group"><button type="button" role="button" class="btn btn-xs btn-success handleClickAprobarDocumento" name="btnAprobarDocumento" data-id-documento="' + row.id_doc_aprob + '" data-codigo-documento="' + row.codigo + '" title="Aprobar"><i class="fas fa-check fa-xs"></i></button></div>';
                        let btnObservar = '<div class="btn-group" role="group"><button type="button" role="button" class="btn btn-xs btn-warning handleClickObservarDocumento" name="btnObservarDocumento" data-id-documento="' + row.id_doc_aprob + '" data-codigo-documento="' + row.codigo + '" title="Observar"><i class="fas fa-exclamation fa-xs"></i></button></div>';
                        let btnAnular = '<div class="btn-group" role="group"><button type="button"  role="button" class="btn btn-xs btn-danger handleClickRechazarDocumento" name="btnRechazarDocumento" data-id-documento="' + row.id_doc_aprob + '" data-codigo-documento="' + row.codigo + '" title="Rechazar"><i class="fas fa-ban fa-xs"></i></button></div>';
                        
                        // let btnVerDetalle = `<button type="button" class="btn btn-xs btn-primary desplegar-detalle handleClickVerDetalleDocumento" data-toggle="tooltip" data-placement="bottom" title="Desplegar detalle" data-id-documento="${row.id_requerimiento_pago}">
                        // <i class="fas fa-chevron-down"></i>
                        // </button>`;


                        return containerOpenBrackets + btnVerEnModal + btnAprobar +btnObservar+ btnAnular + containerCloseBrackets;
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
                //     $tablaRevisarAprobarDocumento.search($input.val()).draw();
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
        // $tablaRevisarAprobarDocumento.on('search.dt', function () {
        //     $('#tableDatos_filter input').prop('disabled', true);
        //     $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        // });
    }

    verEnVistaRapidaDocumento(obj){
        let idDocumento = obj.dataset.idDocumento;
        let codigoDocumento = obj.dataset.codigoDocumento;
        console.log(idDocumento,codigoDocumento);

    }
    aprobarDocumento(obj){
        let idDocumento = obj.dataset.idDocumento;
        let codigoDocumento = obj.dataset.codigoDocumento;
        console.log(idDocumento,codigoDocumento);
    }
    observarDocumento(obj){
        let idDocumento = obj.dataset.idDocumento;
        let codigoDocumento = obj.dataset.codigoDocumento;
        console.log(idDocumento,codigoDocumento);
    }
    rechazarDocumento(obj){
        let idDocumento = obj.dataset.idDocumento;
        let codigoDocumento = obj.dataset.codigoDocumento;
        console.log(idDocumento,codigoDocumento);
    }

}