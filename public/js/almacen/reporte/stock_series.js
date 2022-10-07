let $tablaListaStockSeries;

$(function(){
    listar_stock_series();
});

function listar_stock_series(){
    vista_extendida();
    var vardataTables = funcDatatables();
    $tablaListaStockSeries = $('#lista_stock_series').DataTable({
        'dom': vardataTables[1],
        'buttons': [
            {
                text: '<span class="far fa-file-excel" aria-hidden="true"></span> Descargar',
                attr: {
                    id: 'btnDescargarReporteStockSeriesExcel',
                    disabled: false

                },
                action: () => {
                    download_excel();

                },
                className: 'btn-default btn-sm'
            }
        ],
        'language': vardataTables[0],
        'order': [[7, 'desc']],
        'bLengthChange': false,
        'serverSide': true,
        'destroy': true,
        'ajax': {
            'url': 'listar_stock_series',
            'type': 'POST',
            beforeSend: data => {
                $("#lista_stock_series").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },
        },
        'columns': [
 
            { 'data': 'almacen', 'name': 'almacen' ,'className': 'text-center'},
            { 'data': 'codigo_producto', 'name': 'codigo_producto','className': 'text-center' },
            { 'data': 'part_number', 'name': 'part_number' },
            { 'data': 'serie', 'name': 'serie' },
            { 'data': 'descripcion', 'name': 'descripcion' },
            { 'data': 'unidad_medida', 'name': 'unidad_medida' },
            { 'data': 'afecto_igv', 'name': 'afecto_igv' },
            { 'data': 'fecha_ingreso', 'name': 'fecha_ingreso' },
            { 'data': 'guia_fecha_emision', 'name': 'guia_fecha_emision' },
            { 'data': 'documento_compra', 'name': 'documento_compra' }
        ],

        'columnDefs': [


        ],
        'initComplete': function () {

            //Boton de busqueda
            const $filter = $('#lista_stock_series_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscarItemOrden" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscarItemOrden').trigger('click');
                }
            });
            $('#btnBuscarItemOrden').on('click', (e) => {
                $tablaListaStockSeries.search($input.val()).draw();
            })
            //Fin boton de busqueda

        },
        "drawCallback": function (settings) {
            if ($tablaListaStockSeries.rows().data().length == 0) {
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
            $('#lista_stock_series_filter input').prop('disabled', false);
            $('#btnBuscarItemOrden').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
            $('#lista_stock_series_filter input').trigger('focus');
            //fin botón búsqueda
            $("#lista_stock_series").LoadingOverlay("hide", true);
        }
    });
    //Desactiva el buscador del DataTable al realizar una busqueda
    $tablaListaStockSeries.on('search.dt', function () {
        $('#tableDatos_filter input').prop('disabled', true);
        $('#btnBuscarItemOrden').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
    });
}

function download_excel(){
    window.open('exportar_excel');
}

function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}