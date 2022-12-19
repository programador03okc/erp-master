$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaIngresosCompra tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaIngresosCompra").dataTable().$("tr.eventClick").removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var data = $('#listaIngresosCompra').DataTable().row($(this)).data();
        var id_dev = $('[name=id_devolucion]').val();

        ingresos.push({
            'id': 0,
            'id_devolucion': (id_dev == '' ? 0 : id_dev),
            'id_ingreso': data.id_mov_alm,
            'serie_numero_guia': data.serie_numero_guia,
            'serie_numero_doc': data.serie_numero_doc,
            'razon_social': data.razon_social,
            'codigo': data.codigo,
            'estado': 1,
        });
        obtenerIngreso(data.id_mov_alm);
        mostrarIngresos();
        $("#modal-ingresos").modal("hide");
    });
});

function abrirIngresosModal(id_almacen, id_contribuyente) {
    $('#modal-ingresos').modal({
        show: true
    });
    clearDataTable();
    listarIngresos(id_almacen, id_contribuyente);
}

function listarIngresos(id_almacen, id_contribuyente) {
    var vardataTables = funcDatatables();

    $('#listaIngresosCompra').dataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        serverSide: true,
        destroy: true,
        ajax: 'listarIngresos/' + id_almacen + '/' + id_contribuyente,
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