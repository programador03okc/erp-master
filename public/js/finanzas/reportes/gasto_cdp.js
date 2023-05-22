

$(document).ready(function () {
    lista();
});

let $tablaListaGastoCDP;

function lista() {
    var vardataTables = funcDatatables();
    const btnDescargar = {
            text: '<span class="far fa-file-excel" aria-hidden="true"></span> Descargar',
            attr: {
                id: 'btnDescargarReporteGastoCDPExcel'
            },
            action: () => {
                descargarReporteGastoCDP();

            },
            className: 'btn-default btn-sm'
        };
    $tablaListaGastoCDP = $('#listaGastoCDP').DataTable({
        'dom': vardataTables[1],
        'buttons': [btnDescargar],
        'language': vardataTables[0],
        'order': [[4, 'desc']],
        'bLengthChange': false,
        'serverSide': true,
        'destroy': true,
        'ajax': {
            'url': 'lista-cdp',
            'type': 'POST',
            beforeSend: data => {
                $("#listaGastoCDP").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            }
        },
        'columns': [
            { 'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad', 'className': 'text-center'},
            { 'data': 'oportunidad', 'name': 'oportunidad', 'className': 'text-center','searchable':false},
            { 'data': 'tipo_negocio', 'name': 'tipo_negocio', 'className': 'text-center','searchable':false},
            { 'data': 'importe_oportunidad', 'name': 'importe_oportunidad', 'className': 'text-center','searchable':false, 'render': function (data, type, row) {
                let simboloMoneda = (row.moneda_oportunidad == 's') ? 'S/' : (row.moneda_oportunidad == 'd') ? '$' : row.moneda_oportunidad;
                return `${simboloMoneda}${row['importe_oportunidad'] ? $.number(row['importe_oportunidad'], 2) : ''}`;
            }},
            { 'data': 'fecha_registro_oportunidad', 'name': 'fecha_registro_oportunidad', 'className': 'text-center','searchable':false},
            { 'data': 'estado_oportunidad', 'name': 'estado_oportunidad', 'className': 'text-center','searchable':false},
            { 'data': 'part_no', 'name': 'cc_am_filas.part_no', 'className': 'text-center','searchable':false},
            { 'data': 'descripcion', 'name': 'descripcion', 'className': 'text-center','searchable':false },
            { 'data': 'pvu_oc', 'name': 'pvu_oc', 'className': 'text-center','searchable':false },
            { 'data': 'flete_oc', 'name': 'flete_oc', 'className': 'text-center','searchable':false },
            { 'data': 'cantidad', 'name': 'cantidad', 'className': 'text-center','searchable':false },
            { 'data': 'garantia', 'name': 'garantia', 'className': 'text-center','searchable':false },
            { 'data': 'origen_costo', 'name': 'origen_costo', 'className': 'text-center','searchable':false },
            { 'data': 'razon_social_proveedor', 'name': 'razon_social_proveedor', 'className': 'text-center','searchable':false },
            { 'data': 'costo_unitario_proveedor', 'name': 'costo_unitario_proveedor', 'className': 'text-center','searchable':false, 'render': function (data, type, row) {
                let simboloMoneda = (row.moneda_costo_unitario_proveedor == 's') ? 'S/' : (row.moneda_costo_unitario_proveedor == 'd') ? '$' : row.moneda_costo_unitario_proveedor;
                return `${simboloMoneda}${row['costo_unitario_proveedor'] ? $.number(row['costo_unitario_proveedor'], 2) : ''}`;
            } },

            { 'data': 'plazo_proveedor', 'name': 'plazo_proveedor', 'className': 'text-center','searchable':false },
            { 'data': 'flete_proveedor', 'name': 'flete_proveedor', 'className': 'text-center','searchable':false },
            { 'data': 'fondo_proveedor', 'name': 'fondo_proveedor', 'className': 'text-center','searchable':false },
            { 'data': 'costo_unitario_proveedor', 'name': 'costo_unitario_proveedor', 'className': 'text-center','searchable':false,'render': function (data, type, row) {
                let simboloMoneda = (row.moneda_costo_unitario_proveedor == 's') ? 'S/' : (row.moneda_costo_unitario_proveedor == 'd') ? '$' : row.moneda_costo_unitario_proveedor;
                let costoUnitario = $.number((row.cantidad * row.costo_unitario_proveedor), 2);
                return `${simboloMoneda}${costoUnitario}`;
            }},
            { 'data': 'costo_unitario_proveedor', 'name': 'costo_unitario_proveedor', 'className': 'text-center','searchable':false, 'render': function (data, type, row) {
                let costoUnitario = row.cantidad * row.costo_unitario_proveedor;
                let tipoCambio = row.tipo_cambio;
                let costoUnitarioSoles = costoUnitario * tipoCambio;
                return `S/${$.number(costoUnitarioSoles, 2)}`;
            } },
            { 'data': 'flete_proveedor', 'name': 'flete_proveedor', 'className': 'text-center','searchable':false,'render': function (data, type, row) {
                let totalFleteProveedor = $.number((row.cantidad * row.flete_proveedor), 2);
                return `S/${(totalFleteProveedor)}`;
            } },
            { 'data': 'costo_unitario_proveedor', 'name': 'costo_unitario_proveedor', 'className': 'text-center','searchable':false, 'render': function (data, type, row) {
                let totalFleteProveedor = row.cantidad * row.flete_proveedor;
                let costoUnitario = row.cantidad * row.costo_unitario_proveedor;
                let tipoCambio = row.tipo_cambio;
                let costoUnitarioSoles = costoUnitario * tipoCambio;
                let costoCompraMasFlete = costoUnitarioSoles + totalFleteProveedor;
                return `S/${$.number(costoCompraMasFlete, 2)}`;
            } },
            { 'data': 'nombre_autor', 'name': 'nombre_autor', 'className': 'text-center','searchable':false },
            { 'data': 'created_at', 'name': 'created_at', 'className': 'text-center','searchable':false }
        ],
        'initComplete': function () {
            //Boton de busqueda
            const $filter = $('#listaGastoCDP_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscar').trigger('click');
                }
            });
            $('#btnBuscar').on('click', (e) => {
                $tablaListaGastoCDP.search($input.val()).draw();
            })
            //Fin boton de busqueda

        },
        "drawCallback": function (settings) {
            if ($tablaListaGastoCDP.rows().data().length == 0) {
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
            $('#listaGastoCDP_filter input').prop('disabled', false);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
            $('#listaGastoCDP_filter input').trigger('focus');
            //fin botón búsqueda
            $("#listaGastoCDP").LoadingOverlay("hide", true);
        }
    });
    //Desactiva el buscador del DataTable al realizar una busqueda
    $tablaListaGastoCDP.on('search.dt', function () {
        $('#tableDatos_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
    });

    $('#listaGastoCDP').DataTable().on("draw", function () {
        resizeSide();
    });
}

function descargarReporteGastoCDP(){
    window.open(`exportar-cdp-excel`);
}