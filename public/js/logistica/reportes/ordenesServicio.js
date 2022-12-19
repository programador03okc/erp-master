
// ============== View =========================
var vardataTables = funcDatatables();
var $tablaListaOrdenesServicio;
var iTableCounter = 1;
var oInnerTable;
var actionPage = null;

class OrdenesServicio {
    constructor() {
        this.ActualParametroEmpresa= 'SIN_FILTRO';
        this.ActualParametroSede= 'SIN_FILTRO';
        this.ActualParametroFechaDesde= 'SIN_FILTRO';
        this.ActualParametroFechaHasta= 'SIN_FILTRO';
    }

    initializeEventHandler() {

    }

    descargarListaOrdenesServicio(){
        window.open(`reporte-ordenes-servicio-excel/${this.ActualParametroEmpresa}/${this.ActualParametroSede}/${this.ActualParametroFechaDesde}/${this.ActualParametroFechaHasta}`);

    }


    mostrar(idEmpresa = 'SIN_FILTRO', idSede = 'SIN_FILTRO', fechaRegistroDesde='SIN_FILTRO',fechaRegistroHasta='SIN_FILTRO') {
        let that = this;
        vista_extendida();
<<<<<<< HEAD
        var vardataTables = funcDatatables(),
            button_descargar = (array_accesos.find(element => element === 275)?{
=======
        var vardataTables = funcDatatables();
        const button_descargar_excel=(array_accesos.find(element => element === 275)?{
>>>>>>> develop
                text: '<i class="far fa-file-excel"></i> Descargar',
                attr: {
                    id: 'btnDescargarListaOrdenesServicio'
                },
                action: () => {
                    this.descargarListaOrdenesServicio();

                },
                className: 'btn-default btn-sm'
            }:[]);
        $tablaListaOrdenesServicio= $('#listaOrdenesServicio').DataTable({
            'dom': vardataTables[1],
<<<<<<< HEAD
            'buttons': [button_descargar],
=======
            'buttons': [button_descargar_excel],
>>>>>>> develop
            'language': vardataTables[0],
            'order': [[0, 'desc']],
            'bLengthChange': false,
            'serverSide': true,
            'destroy': true,
            'ajax': {
                'url': 'lista-ordenes-servicio',
                'type': 'POST',
                'data':{'idEmpresa':idEmpresa,'idSede':idSede,'fechaRegistroDesde':fechaRegistroDesde,'fechaRegistroHasta':fechaRegistroHasta},

                beforeSend: data => {

                    $("#listaOrdenesServicio").LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                },
                // data: function (params) {
                //     return Object.assign(params, Util.objectifyForm($('#form-requerimientosElaborados').serializeArray()))
                // }

            },
            'columns': [
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'codigo', 'name': 'log_ord_compra.codigo', 'className': 'text-center' },
                { 'data': 'codigo_softlink', 'name': 'log_ord_compra.codigo_softlink', 'className': 'text-center' },
                { 'data': 'sede.descripcion', 'name': 'sede.descripcion',  'defaultContent':'' ,'className': 'text-center' },
                { 'data': 'estado.descripcion', 'name': 'estado.descripcion', 'className': 'text-center' },
                { 'data': 'fecha', 'name': 'fecha', 'className': 'text-center' },
                { 'data': 'id_orden_compra', 'name': 'id_orden_compra', 'className': 'text-center' },
                { 'data': 'observacion', 'name': 'log_ord_compra.observacion', 'className': 'text-left' },

            ],
            'columnDefs': [

                {
                    'render': function (data, type, row) {
                        // console.log((row.cuadro_costo));
                        return (row.requerimientos)!=null && ((row.requerimientos)).length >0 ?(row.requerimientos).map(e => e.codigo).join(","):'';
                    }, targets: 0
                },

                {
                    'render': function (data, type, row) {
                        return moment(row['fecha_formato'], "DD-MM-YYYY").format("DD-MM-YYYY").toString();
                    }, targets: 5
                },

                {
                    'render': function (data, type, row) {
                        let fechaPlazoEntrega = moment(row['fecha_formato'], "DD-MM-YYYY").add(row['plazo_entrega'], 'days').format("DD-MM-YYYY").toString();
                        return fechaPlazoEntrega;
                    }, targets: 6
                },



            ],
            'initComplete': function () {
                // that.updateContadorFiltro();
                //Boton de busqueda
                const $filter = $('#listaOrdenesServicio_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tablaListaOrdenesServicio.search($input.val()).draw();
                })
                //Fin boton de busqueda

            },
            "drawCallback": function( settings ) {

                //Botón de búsqueda
                $('#listaOrdenesServicio_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#listaOrdenesServicio_filter input').trigger('focus');
                //fin botón búsqueda
                $("#listaOrdenesServicio").LoadingOverlay("hide", true);
            }
        });
        //Desactiva el buscador del DataTable al realizar una busqueda
        $tablaListaOrdenesServicio.on('search.dt', function () {
            $('#tableDatos_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

    }

}
