

$(document).ready(function () {
    lista();
});

let $tablaListaGastoRequerimientoPago;

function lista() {
    var vardataTables = funcDatatables();
    const btnDescargar = {
            text: '<span class="far fa-file-excel" aria-hidden="true"></span> Descargar',
            attr: {
                id: 'btnDescargarReporteGastoRequerimientoPagoExcel'
            },
            action: () => {
                descargarReporteGastoRequerimientoPago();

            },
            className: 'btn-default btn-sm'
        };
    $tablaListaGastoRequerimientoPago = $('#listaGastoRequerimientoPago').DataTable({
        'dom': vardataTables[1],
        'buttons': [btnDescargar],
        'language': vardataTables[0],
        'order': [[32, 'desc']],
        'bLengthChange': false,
        'serverSide': true,
        'destroy': true,
        'ajax': {
            'url': 'lista-requerimiento-pago',
            'type': 'POST',
            'data': {},
            beforeSend: data => {

                $("#listaGastoRequerimientoPago").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            }
        },
        'columns': [
            { 'data': 'prioridad', 'name': 'adm_prioridad.descripcion', 'className': 'text-center','searchable':false},
            { 'data': 'codigo', 'name': 'requerimiento_pago.codigo', 'className': 'text-center' },
            { 'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad', 'className': 'text-center','searchable':false },
            { 'data': 'codigo_presupuesto_old', 'name': 'presup.codigo', 'className': 'text-center','searchable':false },
            { 'data': 'descripcion_presupuesto_old', 'name': 'presup.descripcion', 'className': 'text-left','searchable':false },
            { 'data': 'descripcion_partida_padre', 'name': 'presup_titu.descripcion', 'className': 'text-left' ,'searchable':false},
            { 'data': 'partida', 'name': 'presup_par.codigo', 'className': 'text-left' ,'searchable':false},
            { 'data': 'descripcion_partida', 'name': 'presup_par.descripcion', 'className': 'text-left' ,'searchable':false},
            { 'data': 'codigo_presupuesto_interno', 'name': 'presupuesto_interno.codigo', 'className': 'text-left' ,'searchable':false},
            { 'data': 'descripcion_presupuesto_interno', 'name': 'presupuesto_interno.descripcion', 'className': 'text-left' ,'searchable':false},
            { 'data': 'descripcion_partida_presupuesto_interno', 'name': 'descripcion_partida_presupuesto_interno', 'className': 'text-left' ,'searchable':false},
            { 'data': 'codigo_sub_partida_presupuesto_interno', 'name': 'codigo_sub_partida_presupuesto_interno', 'className': 'text-left' ,'searchable':false},
            { 'data': 'descripcion_sub_partida_presupuesto_interno', 'name': 'descripcion_sub_partida_presupuesto_interno', 'className': 'text-left' ,'searchable':false},
            { 'data': 'padre_centro_costo', 'name': 'padre_centro_costo.codigo', 'className': 'text-left' ,'searchable':false},
            { 'data': 'padre_descripcion_centro_costo', 'name': 'padre_centro_costo.descripcion', 'className': 'text-left' ,'searchable':false},
            { 'data': 'centro_costo', 'name': 'centro_costo.codigo', 'className': 'text-left' ,'searchable':false},
            { 'data': 'descripcion_centro_costo', 'name': 'centro_costo.descripcion', 'className': 'text-left' ,'searchable':false},
            { 'data': 'descripcion_proyecto', 'name': 'proy_proyecto.descripcion', 'className': 'text-left' ,'searchable':false},
            { 'data': 'motivo', 'name': 'requerimiento_pago_detalle.motivo', 'className': 'text-left' ,'searchable':false},
            { 'data': 'concepto', 'name': 'requerimiento_pago.concepto', 'className': 'text-left' ,'searchable':false},
            { 'data': 'descripcion', 'name': 'requerimiento_pago_detalle.descripcion', 'className': 'text-left','searchable':false},
            { 'data': 'tipo_requerimiento', 'name': 'requerimiento_pago_tipo.descripcion', 'className': 'text-center' ,'searchable':false},
            { 'data': 'empresa_razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-left' ,'searchable':false},
            { 'data': 'sede', 'name': 'sis_sede.codigo', 'className': 'text-center' ,'searchable':false},
            { 'data': 'grupo', 'name': 'sis_grupo.descripcion', 'className': 'text-center' ,'searchable':false},
            { 'data': 'division', 'name': 'division.descripcion', 'className': 'text-center' ,'searchable':false},
            { 'data': 'cantidad', 'name': 'requerimiento_pago_detalle.cantidad', 'className': 'text-center' ,'searchable':false},
            { 'data': 'precio_unitario', 'name': 'requerimiento_pago_detalle.precio_unitario', 'className': 'text-center' ,'searchable':false},
            { 'data': 'subtotal', 'name': 'requerimiento_pago_detalle.subtotal', 'className': 'text-center' ,'searchable':false},
            { 'data': 'simbolo_moneda', 'name': 'sis_moneda.simbolo', 'className': 'text-center' ,'searchable':false},
            { 'data': 'tipo_cambio', 'name': 'tipo_cambio', 'className': 'text-center' ,'searchable':false},
            { 'data': 'fecha_aprobacion', 'name': 'fecha_aprobacion', 'className': 'text-center' ,'searchable':false},
            { 'data': 'usuario_aprobador', 'name': 'usuario_aprobador', 'className': 'text-center' ,'searchable':false},
            { 'data': 'subtotal_soles', 'name': 'subtotal_soles', 'className': 'text-center' ,'searchable':false},
            { 'data': 'comentario', 'name': 'requerimiento_pago.comentario', 'className': 'text-left' ,'searchable':false},
            { 'data': 'fecha_registro', 'name': 'requerimiento_pago.fecha_registro', 'className': 'text-center' ,'searchable':false},
            { 'data': 'hora_registro', 'name': 'hora_registro', 'className': 'text-center' ,'searchable':false},
            { 'data': 'estado_requerimiento', 'name': 'requerimiento_pago_estado.descripcion', 'className': 'text-center','searchable':false }
        ],
        'columnDefs': [],
        'initComplete': function () {
            //Boton de busqueda
            const $filter = $('#listaGastoRequerimientoPago_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscar').trigger('click');
                }
            });
            $('#btnBuscar').on('click', (e) => {
                $tablaListaGastoRequerimientoPago.search($input.val()).draw();
            })
            //Fin boton de busqueda

        },
        "drawCallback": function (settings) {
            if ($tablaListaGastoRequerimientoPago.rows().data().length == 0) {
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
            $('#listaGastoRequerimientoPago_filter input').prop('disabled', false);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
            $('#listaGastoRequerimientoPago_filter input').trigger('focus');
            //fin botón búsqueda
            $("#listaGastoRequerimientoPago").LoadingOverlay("hide", true);
        }
    });
    //Desactiva el buscador del DataTable al realizar una busqueda
    $tablaListaGastoRequerimientoPago.on('search.dt', function () {
        $('#tableDatos_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
    });

    $('#listaGastoRequerimientoPago').DataTable().on("draw", function () {
        resizeSide();
    });
}

function descargarReporteGastoRequerimientoPago(){
    window.open(`exportar-requerimiento-pago-excel`);
}