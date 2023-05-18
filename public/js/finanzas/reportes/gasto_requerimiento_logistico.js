

$(document).ready(function () {
    lista();
});

let $tablaListaGastoRequerimientoLogistico;

function lista() {
    var vardataTables = funcDatatables();
    const btnDescargar = {
            text: '<span class="far fa-file-excel" aria-hidden="true"></span> Descargar',
            attr: {
                id: 'btnDescargarReporteGastoRequerimientoLogisticoExcel'
            },
            action: () => {
                descargarReporteGastoRequerimientoLogistico();

            },
            className: 'btn-default btn-sm'
        };
    $tablaListaGastoRequerimientoLogistico = $('#listaGastoRequerimientoLogistico').DataTable({
        'dom': vardataTables[1],
        'buttons': [btnDescargar],
        'language': vardataTables[0],
        'order': [[42, 'desc']],
        'bLengthChange': false,
        'serverSide': true,
        'destroy': true,
        'ajax': {
            'url': 'lista-requerimiento-logistico',
            'type': 'POST',
            'data': {},
            beforeSend: data => {

                $("#listaGastoRequerimientoLogistico").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            }
        },
        'columns': [
            { 'data': 'prioridad', 'name': 'prioridad', 'className': 'text-center','searchable':false},
            { 'data': 'codigo', 'name': 'alm_req.codigo', 'className': 'text-center' },
            { 'data': 'codigo_oportunidad', 'name': 'codigo_oportunidad', 'className': 'text-center','searchable':false },
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
            { 'data': 'padre_centro_costo', 'name': 'padre_centro_costo', 'className': 'text-left' ,'searchable':false},
            { 'data': 'padre_descripcion_centro_costo', 'name': 'padre_descripcion_centro_costo', 'className': 'text-left' ,'searchable':false},
            { 'data': 'centro_costo', 'name': 'centro_costo', 'className': 'text-left' ,'searchable':false},
            { 'data': 'descripcion_centro_costo', 'name': 'descripcion_centro_costo', 'className': 'text-left' ,'searchable':false},
            { 'data': 'descripcion_proyecto', 'name': 'descripcion_proyecto', 'className': 'text-left' ,'searchable':false},
            { 'data': 'motivo', 'name': 'motivo', 'className': 'text-left' ,'searchable':false},
            { 'data': 'concepto', 'name': 'concepto', 'className': 'text-left' ,'searchable':false},
            { 'data': 'descripcion_producto', 'name': 'alm_prod.descripcion', 'className': 'text-left','render': function (data, type, row) {
                if(row.descripcion_producto !='' ){
                    return row.descripcion_producto;
                }else if(row.descripcion_detalle_requerimiento !=''){
                    return row.descripcion_detalle_requerimiento;
                    
                }else{
                    return '';
                }
            },'searchable':false},
            { 'data': 'tipo_requerimiento', 'name': 'alm_tp_req.descripcion', 'className': 'text-center' ,'searchable':false},
            { 'data': 'empresa_razon_social', 'name': 'adm_contri.razon_social', 'className': 'text-left' ,'searchable':false},
            { 'data': 'sede', 'name': 'sis_sede.codigo', 'className': 'text-center' ,'searchable':false},
            { 'data': 'grupo', 'name': 'sis_grupo.descripcion', 'className': 'text-center' ,'searchable':false},
            { 'data': 'division', 'name': 'division.descripcion', 'className': 'text-center' ,'searchable':false},
            { 'data': 'cantidad', 'name': 'alm_det_req.cantidad', 'className': 'text-center' ,'searchable':false},
            { 'data': 'precio_unitario', 'name': 'alm_det_req.precio_unitario', 'className': 'text-center' ,'searchable':false},
            { 'data': 'subtotal', 'name': 'alm_det_req.subtotal', 'className': 'text-center' ,'searchable':false},
            { 'data': 'simbolo_moneda', 'name': 'sis_moneda.simbolo', 'className': 'text-center' ,'searchable':false},
            { 'data': 'nro_orden', 'name': 'log_ord_compra.codigo', 'className': 'text-center' ,'searchable':false},
            { 'data': 'codigo_producto', 'name': 'alm_prod.codigo', 'className': 'text-center' ,'searchable':false},
            { 'data': 'cantidad_orden', 'name': 'cantidad_orden', 'className': 'text-center' ,'searchable':false},
            { 'data': 'precio_orden', 'name': 'log_det_ord_compra.precio', 'className': 'text-center' ,'searchable':false},
            { 'data': 'subtotal_orden', 'name': 'subtotal_orden', 'className': 'text-center' ,'searchable':false},
            { 'data': 'simbolo_moneda_orden', 'name': 'moneda_orden.simbolo', 'className': 'text-center' ,'searchable':false},
            { 'data': 'subtotal_orden_considera_igv', 'name': 'subtotal_orden_considera_igv', 'className': 'text-center' ,'searchable':false},
            { 'data': 'estado_orden', 'name': 'estado_compra.descripcion', 'className': 'text-center' ,'searchable':false},
            { 'data': 'estado_pago', 'name': 'requerimiento_pago_estado.descripcion', 'className': 'text-center' ,'searchable':false},
            { 'data': 'estado_despacho', 'name': 'estado_despacho.estado_doc', 'className': 'text-center','searchable':false },
            { 'data': 'nro_salida_int', 'name': 'nro_salida_int', 'className': 'text-center','searchable':false },
            { 'data': 'nro_salida_ext', 'name': 'nro_salida_ext', 'className': 'text-center','searchable':false },
            { 'data': 'almacen_salida', 'name': 'almacen_salida', 'className': 'text-center','searchable':false },
            { 'data': 'fecha_salida',   'name': 'fecha_salida', 'className': 'text-center','searchable':false },
            { 'data': 'codigo_producto_salida', 'name': 'codigo_producto_salida', 'className': 'text-center','searchable':false },
            { 'data': 'cantidad_salida', 'name': 'cantidad_salida', 'className': 'text-center','searchable':false },
            { 'data': 'moneda_producto_salida', 'name': 'moneda_producto_salida', 'className': 'text-center','searchable':false },
            { 'data': 'costo_unitario_salida', 'name': 'costo_unitario_salida', 'className': 'text-center','searchable':false },
            { 'data': 'costo_total_salida', 'name': 'costo_total_salida', 'className': 'text-center','searchable':false },
            { 'data': 'tipo_cambio', 'name': 'tipo_cambio', 'className': 'text-center' ,'searchable':false},
            { 'data': 'observacion', 'name': 'alm_req.observacion', 'className': 'text-left' ,'searchable':false},
            { 'data': 'fecha_requerimiento', 'name': 'alm_req.fecha_requerimiento', 'className': 'text-center' ,'searchable':false},
            { 'data': 'fecha_registro', 'name': 'alm_det_req.fecha_registro', 'className': 'text-center' ,'searchable':false},
            { 'data': 'hora_registro', 'name': 'hora_registro', 'className': 'text-center' ,'searchable':false},
            { 'data': 'estado_requerimiento', 'name': 'adm_estado_doc.estado_doc', 'className': 'text-center','searchable':false }
        ],
        'columnDefs': [],
        'initComplete': function () {
            //Boton de busqueda
            const $filter = $('#listaGastoRequerimientoLogistico_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscar').trigger('click');
                }
            });
            $('#btnBuscar').on('click', (e) => {
                $tablaListaGastoRequerimientoLogistico.search($input.val()).draw();
            })
            //Fin boton de busqueda

        },
        "drawCallback": function (settings) {
            if ($tablaListaGastoRequerimientoLogistico.rows().data().length == 0) {
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
            $('#listaGastoRequerimientoLogistico_filter input').prop('disabled', false);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
            $('#listaGastoRequerimientoLogistico_filter input').trigger('focus');
            //fin botón búsqueda
            $("#listaGastoRequerimientoLogistico").LoadingOverlay("hide", true);
        }
    });
    //Desactiva el buscador del DataTable al realizar una busqueda
    $tablaListaGastoRequerimientoLogistico.on('search.dt', function () {
        $('#tableDatos_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
    });

    $('#listaGastoRequerimientoLogistico').DataTable().on("draw", function () {
        resizeSide();
    });
}

function descargarReporteGastoRequerimientoLogistico(){
    window.open(`exportar-requerimiento-logistico-excel`);
}