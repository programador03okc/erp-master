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
        $('[name=id_incidencia]').val(id);
        mostrarIncidencia(id);

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
        ajax: {
            url: "listarIncidencias",
            type: "POST"
        },
        'columns': [
            { 'data': 'id_incidencia' },
            { 'data': 'codigo' },
            { 'data': 'contribuyente.razon_social' },
            { 'data': 'fecha_reporte' },
            { 'data': 'responsable.nombre_corto' },
            { 'data': 'estado.estado_doc' },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });
}
