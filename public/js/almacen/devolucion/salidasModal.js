$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaSalidasVenta tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaSalidasVenta").dataTable().$("tr.eventClick").removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var data = $('#listaSalidasVenta').DataTable().row($(this)).data();
        var id_dev = $('[name=id_devolucion]').val();

        salidas.push({
            'id': 0,
            'id_devolucion': (id_dev == '' ? 0 : id_dev),
            'id_salida': data.id_mov_alm,
            'serie_numero_guia': data.serie_numero_guia,
            'serie_numero_doc': data.serie_numero_doc,
            'razon_social': data.razon_social,
            'codigo': data.codigo,
            'estado': 1,
        });
        obtenerSalida(data.id_mov_alm);
        mostrarSalidas();
        $("#modal-salidas").modal("hide");
    });
});

function abrirSalidasModal(id_almacen, id_contribuyente) {
    $('#modal-salidas').modal({
        show: true
    });
    clearDataTable();
    listarSalidasVenta(id_almacen, id_contribuyente);
}

function listarSalidasVenta(id_almacen, id_contribuyente) {
    var vardataTables = funcDatatables();

    $('#listaSalidasVenta').dataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        serverSide: true,
        destroy: true,
        ajax: 'listarSalidasVenta/' + id_almacen + '/' + id_contribuyente,
        columns: [
            { 'data': 'id_mov_alm' },
            { 'data': 'serie_numero_guia' },
            { 'data': 'serie_numero_doc' },
            { 'data': 'razon_social' },
            { 'data': 'codigo' },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });
}