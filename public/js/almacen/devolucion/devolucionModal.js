$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaDevoluciones tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaDevoluciones").dataTable().$("tr.eventClick").removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var data = $('#listaDevoluciones').DataTable().row($(this)).data();
        mostrarDevolucion(data.id_devolucion);
        $("#modal-devolucion").modal("hide");
    });
});

function abrirDevolucionModal() {
    $('#modal-devolucion').modal({
        show: true
    });
    clearDataTable();
    listarDevoluciones();
}

function listarDevoluciones() {
    var vardataTables = funcDatatables();
    var page = $('.page-main').attr('type');

    $('#listaDevoluciones').dataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        serverSide: true,
        destroy: true,
        ajax: 'listarDevoluciones',
        columns: [
            { 'data': 'id_devolucion' },
            { 'data': 'codigo' },
            { 'data': 'observacion' },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });
}