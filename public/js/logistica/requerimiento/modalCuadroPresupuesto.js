$('#form-requerimiento').on("click", "button.handleClickModalListaCuadroDePresupuesto", () => {
    this.modalListaCuadroDePresupuesto();
});
$('#listaCuadroPresupuesto').on("click", "button.handleClickSeleccionarCDP", (e) => {
    this.seleccionarCDP(e.currentTarget);
});

function modalListaCuadroDePresupuesto(){
    $('#modal-lista-cuadro-presupuesto').modal({
        show: true
    });
    this.listarCuadroPresupuesto();
}

function listarCuadroPresupuesto() {
    var vardataTables = funcDatatables();
    $tablaListaCuadroPresupuesto = $('#listaCuadroPresupuesto').DataTable({
        'dom': vardataTables[1],
        'buttons': [],
        'language': vardataTables[0],
        'order': [[7, 'desc']],
        'bLengthChange': false,
        'serverSide': true,
        'destroy': true,
        'ajax': {
            'url': 'lista-cuadro-presupuesto',
            'type': 'POST',
            beforeSend: data => {

                $("#listaCuadroPresupuesto").LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            },

        },
        'columns': [
            { 'data': 'codigo_oportunidad', 'name': 'cc_view.codigo_oportunidad', 'className': 'text-center' },
            { 'data': 'descripcion_oportunidad', 'name': 'cc_view.descripcion_oportunidad', 'className': 'text-left' },
            { 'data': 'fecha_creacion', 'name': 'cc_view.fecha_creacion', 'className': 'text-center' },
            { 'data': 'fecha_limite', 'name': 'cc_view.fecha_limite', 'className': 'text-center' },
            { 'data': 'nombre_entidad', 'name': 'cc_view.nombre_entidad', 'className': 'text-left' },
            { 'data': 'name', 'name': 'cc_view.name', 'className': 'text-center' },
            { 'data': 'estado_aprobacion', 'name': 'cc_view.estado_aprobacion', 'className': 'text-center' },
            { 'data': 'id', 'name': 'cc_view.id', }
        ],
        'columnDefs': [


            {
                'render': function (data, type, row) {
                    let containerOpenBrackets = '<center><div class="btn-group" role="group" style="margin-bottom: 5px;">';
                    let containerCloseBrackets = '</div></center>';
                    let btnSeleccionar = `<button type="button" class="btn btn-xs btn-success handleClickSeleccionarCDP"  data-id-cc="${row.id}"  data-codigo-oportunidad="${row.codigo_oportunidad}" title="Seleccionar">Seleccionar</button>`;
                    return containerOpenBrackets + btnSeleccionar + containerCloseBrackets;
                }, targets: 7
            },

        ],
        'initComplete': function () {
            // that.updateContadorFiltroRequerimientosElaborados();

            //Boton de busqueda
            const $filter = $('#listaCuadroPresupuesto_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscarCDP" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscarCDP').trigger('click');
                }
            });
            $('#btnBuscarCDP').on('click', (e) => {
                $tablaListaCuadroPresupuesto.search($input.val()).draw();
            })
            //Fin boton de busqueda

        },
        "drawCallback": function (settings) {
            if ($tablaListaCuadroPresupuesto.rows().data().length == 0) {
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
            $('#listaCuadroPresupuesto_filter input').prop('disabled', false);
            $('#btnBuscarCDP').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
            $('#listaCuadroPresupuesto_filter input').trigger('focus');
            //fin botón búsqueda
            $("#listaCuadroPresupuesto").LoadingOverlay("hide", true);
        }
    });
    //Desactiva el buscador del DataTable al realizar una busqueda
    $tablaListaCuadroPresupuesto.on('search.dt', function () {
        $('#tableDatos_filter input').prop('disabled', true);
        $('#btnBuscarCDP').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
    });

}


function seleccionarCDP(obj) {

    if (obj.dataset.idCc > 0) {
        document.querySelector("input[name='id_cc']").value = obj.dataset.idCc;
        document.querySelector("input[name='codigo_oportunidad']").value = obj.dataset.codigoOportunidad;
    } else {
        Swal.fire(
            '',
            'Lo sentimos hubo un error al intentar obtener el id del cuadro de presupuesto, por favor vuelva a intentarlo',
            'error'
        );
    }
    $('#modal-lista-cuadro-presupuesto').modal('hide');
}