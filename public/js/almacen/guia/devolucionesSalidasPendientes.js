function listarDevoluciones() {
    var vardataTables = funcDatatables();
    let botones = [];

    tableDevoluciones = $('#listaDevoluciones').DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        serverSide: true,
        ajax: 'listarDevolucionesSalidas',
        // ajax: {
        //     url: "listarDevoluciones",
        //     type: "POST",
        //     data: function (params) {
        //         return Object.assign(params, objectifyForm($('#formFiltrosIncidencias').serializeArray()))
        //     }
        // },
        columns: [
            { 'data': 'id_devolucion' },
            {
                'data': 'codigo',
                render: function (data, type, row) {
                    return (
                        `<a href="#" class="ver-devolucion" data-id="${row["id_devolucion"]}">${row["codigo"]}</a>`
                    );
                }
            },
            {
                'data': 'estado_doc', name: 'devolucion_estado.descripcion',
                'render': function (data, type, row) {
                    return `<span class="label label-${row['bootstrap_color']}">${row['estado_doc']}</span>`;
                }, className: "text-center"
            },
            {
                data: 'fecha_registro',
                'render': function (data, type, row) {
                    return (row['fecha_registro'] !== undefined ? formatDate(row['fecha_registro']) : '');
                }
            },
            { 'data': 'tipo_descripcion', name: 'devolucion_tipo.descripcion' },
            { 'data': 'razon_social', name: 'adm_contri.razon_social' },
            { 'data': 'almacen_descripcion', name: 'alm_almacen.descripcion' },
            { 'data': 'observacion' },
            // {
            //     'render': function (data, type, row) {
            //         if (row["count_fichas"] > 0) {
            //             return `<a href="#" onClick="verFichasTecnicasAdjuntas(${row["id_devolucion"]});">${row["count_fichas"]} archivos adjuntos </a>`;
            //         } else {
            //             return ''
            //         }
            //     }, className: "text-center"
            // },
            { 'data': 'nombre_corto', name: 'sis_usua.nombre_corto' },
            // {
            //     'data': 'usuario_conformidad', name: 'usuario_conforme.nombre_corto',
            //     'render': function (data, type, row) {
            //         if (row["estado"] !== 1) {
            //             return `${row["usuario_conformidad"]} el ${formatDateHour(row["fecha_revision"])}`;
            //         } else {
            //             return '';
            //         }
            //     }, className: "text-center"
            // },
            {
                'render':
                    function (data, type, row) {
                        return `
                        <div class="btn-group" role="group">
                            <button type="button" class="guia btn btn-warning btn-flat boton" data-toggle="tooltip"
                            data-placement="bottom" title="Generar Guía"
                            ${(row['estado_requerimiento'] == 39 || row['estado_requerimiento'] == 38) ? 'disabled' : ''} >
                            <i class="fas fa-sign-in-alt"></i></button>
                        </div>`;
                    }, className: "text-center"
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        order: [[0, "desc"]],
    });
}

$('#listaDevoluciones tbody').on("click", "button.guia", function () {
    var data = $('#listaDevoluciones').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    open_guia_create(data);
});

$("#listaDevoluciones tbody").on("click", "a.ver-devolucion", function () {
    var id = $(this).data("id");
    console.log('id_devolucion ' + id);
    abrirDevolucion(id);
});

function abrirDevolucion(id_devolucion) {
    console.log('abrirDevolucion' + id_devolucion);
    localStorage.setItem("id_devolucion", id_devolucion);
    // location.assign("/logistica/almacen/customizacion/hoja-transformacion/index");
    var win = window.open("/cas/garantias/devolucionCas/index", '_blank');
    win.focus();
}