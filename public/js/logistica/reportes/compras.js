class Compras {
    constructor(token, permisos, idioma) {
        this.token = token;
        this.permisos = permisos;
        this.idioma = idioma;
    }

    actualizarFiltros = () => {
        let actualizar = false;
        $('#modal-filtros').find('input[type=checkbox]').change(function () {
            actualizar = true;
        });

        $("#modal-filtros").on("hidden.bs.modal", () => {
            if (actualizar) {
                actualizar = false;
                this.listar(true);
            }
        });
    }

    listar = () => {
        let buttons = [];

        if ((this.permisos).includes(276)) {
            buttons.push(
                {
                    text: '<i class="fas fa-filter"></i> Filtros : <span id="spanCantFiltros">0</span>',
                    action: () => {
                        this.abrirModalFiltros();
    
                    }, className: 'btn-default btn-sm'
                }
            );
        }

        if ((this.permisos).includes(277)) {
            buttons.push(
                {
                    text: '<i class="far fa-file-excel"></i> Descargar',
                    action: () => {
                        this.DescargarReporte();

                    }, className: 'btn-default btn-sm'
                }
            );
        }

        const $tablaListaCompras = $('#listaCompras').DataTable({
            dom: 'Bfrtip',
            pageLength: 25,
            language: this.idioma,
            destroy: true,
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#listaCompras_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><i class="fas fa-search"></i></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaListaCompras.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#listaCompras_filter input').prop('disabled', false);
                $('#btnBuscar').html('<i class="fas fa-search"></i>').prop('disabled', false);
                $('#listaCompras_filter input').trigger('focus');
                $('#spanCantFiltros').html($('#modal-filtros').find('input[type=checkbox]:checked').length);
            },
            order: [[10, 'desc']],
            ajax: {
                url: 'lista-compras',
                data: function (params) {
                    return Object.assign(params, Util.objectifyForm($('#formulario-filtros').serializeArray()))
                },
                method: 'POST',
                headers: {'X-CSRF-TOKEN': this.token}
            },
            columns: [
                {
                    render: function (data, type, row) {
                        return `<a href="/logistica/gestion-logistica/compras/ordenes/listado/generar-orden-pdf/`+ row.id_orden_compra +`" target="_blank">`+ row.codigo + `</a>`;
                    }, className: 'text-center'
                },
                {
                    render: function (data, type, row) {
                        return `<a href="/necesidades/requerimiento/elaboracion/imprimir-requerimiento-pdf/`+ row.id_requerimiento +`/0" target="_blank">`+ row.codigo_requerimiento + `</a>`;
                    }, className: 'text-center'
                },
                {data: 'codigo_producto', className: 'text-center'},
                {data: 'descripcion', className: 'text-center'},
                // {data: 'rubro_contribuyente', className: 'text-center'},
                {data: 'razon_social_contribuyente', className: 'text-center'},
                {data: 'nro_documento_contribuyente', className: 'text-center'},
                // {data: 'direccion_contribuyente', className: 'text-center'},
                // {data: 'ubigeo_contribuyente', className: 'text-center'},
                {
                    render: function (data, type, row) {
                        return `<label class="lbl-codigo" data-id-orden="` + row.id_orden_compra +`">`+ ((row.fecha_emision_comprobante_contribuyente != null) ? moment(row.fecha_emision_comprobante_contribuyente).format("DD-MM-YYYY") : '') +`</label>`;
                    }, className: 'text-center'
                },
                {
                    render: function (data, type, row) {
                        return `<label class="lbl-codigo" data-id-orden="`+ row.id_orden_compra +`">`+ ((row.fecha_pago != null) ? row.fecha_pago : '') +`</label>`;
                    }, className: 'text-center'
                },
                {data: 'tiempo_cancelacion', className: 'text-center'},
                {data: 'cantidad', className: 'text-center'},
                {data: 'moneda_orden', className: 'text-center'},
                {data: 'total_precio_soles_item', className: 'text-center'},
                {data: 'total_precio_dolares_item', className: 'text-center'},
                {data: 'total_a_pagar_soles', className: 'text-center'},
                {data: 'total_a_pagar_dolares', className: 'text-center'},
                {data: 'tipo_doc_com', className: 'text-center'},
                {data: 'nro_comprobante', className: 'text-center'},
                {data: 'descripcion_sede_empresa', className: 'text-center'},
                {data: 'descripcion_grupo', className: 'text-center'},
                {data: 'descripcion_proyecto', className: 'text-left'},
            ],
            buttons: buttons
        });
        $tablaListaCompras.on('search.dt', function() {
            $('#listaCompras_filter input').attr('disabled', true);
            $('#btnBuscar').html('<i class="fas fa-clock" aria-hidden="true"></i>').prop('disabled', true);
        });
        $tablaListaCompras.on('init.dt', function(e, settings, processing) {
            $('#listaCompras tbody').LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
        });
        $tablaListaCompras.on('processing.dt', function(e, settings, processing) {
            if (processing) {
                $('#listaCompras tbody').LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
            } else {
                $('#listaCompras tbody').LoadingOverlay("hide", true);
            }
        });
    }

    abrirModalFiltros = () => {
        $('#modal-filtros').modal({ show: true,  backdrop: 'true' });
    }

    DescargarReporte = () =>  {
        window.open('reporte-compras-locales-excel', '_blank');
    }
}