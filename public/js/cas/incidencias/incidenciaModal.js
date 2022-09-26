$(function () {
    /* Seleccionar valor del DataTable */
    $('#listaIncidencias tbody').on('click', 'tr', function () {
        if ($(this).hasClass('eventClick')) {
            $(this).removeClass('eventClick');
        } else {
            $('#listaIncidencias').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }

        var id = $(this)[0].firstChild.innerHTML;
        var page = $('.page-main').attr('type');

        if (page == "incidencia") {
            $('[name=id_incidencia]').val(id);
            mostrarIncidencia(id);
        }
        else if (page == "devolucion") {
            var data = $('#listaIncidencias').DataTable().row($(this)).data();

            incidencias.push({
                'id': 0,
                'id_incidencia': data.id_incidencia,
                'codigo': data.codigo,
                'fecha_reporte': data.fecha_reporte,
                'razon_social': data.razon_social,
                'nombre_corto': data.nombre_corto,
                'estado_descripcion': data.estado_descripcion,
                'estado': 1,
            });
            mostrarIncidencias();
        }
        $('#modal-incidencia').modal('hide');
    });
});

function abrirIncidenciaModal() {
    $('#modal-incidencia').modal({
        show: true
    });
    clearDataTable();
    listarIncidencias();
}
function listarIncidencias() {
    var vardataTables = funcDatatables();
    $('#listaIncidencias').dataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        serverSide: true,
        destroy: true,
        ajax: 'listarIncidencias',
        'columns': [
            { 'data': 'id_incidencia' },
            { 'data': 'codigo' },
            { 'data': 'razon_social' },
            { 'data': 'fecha_reporte' },
            { 'data': 'nombre_corto' },
            { 'data': 'estado_descripcion' },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        order: [[3, "desc"]],
    });
}
