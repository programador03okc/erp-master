$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaSeriesProductos tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaSeriesProductos").dataTable().$("tr.eventClick").removeClass("eventClick");
            $(this).addClass("eventClick");
        }

        var data = $('#listaSeriesProductos').DataTable().row($(this)).data();
        var id = $('[name=id_incidencia]').val();

        let item = listaSeriesProductos.find(element => element.id_prod_serie == data.id_prod_serie);

        if (item == undefined) {
            listaSeriesProductos.push({
                "id_incidencia_producto": 0,
                "id_incidencia": $id,
                "id_prod_serie": data.id_prod_serie,
                "serie": data.serie,
                "id_producto": data.id_producto,
                "codigo": data.codigo,
                "part_number": data.part_number,
                "descripcion": data.descripcion,
                // "id_guia_ven_det": data.id_guia_ven_det,
            });
        }

        mostrarListaSeriesProductos();

        // $("#modal-seriesProductos").modal("hide");
    });
});

function listarSeriesProductos() {
    var vardataTables = funcDatatables();
    var id_guia_ven = $('[name=id_guia_ven]').val();

    $("#listaSeriesProductos").dataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        bDestroy: true,
        ajax: "listarSeriesProductos/" + id_guia_ven,
        columns: [
            { data: "id_prod_serie" },
            { data: "serie" },
            { data: "codigo", name: 'alm_prod.codigo' },
            { data: "part_number", name: 'alm_prod.part_number' },
            { data: "descripcion", name: 'alm_prod.descripcion' },
        ],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        order: [[0, "asc"]]
    });
}

function openSeriesProductosModal() {
    $("#modal-seriesProductos").modal({
        show: true
    });
    listarSeriesProductos();
}
