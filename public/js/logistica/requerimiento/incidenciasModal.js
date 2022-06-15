$(function () {
    $("#listaIncidencias tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaIncidencias").dataTable().$("tr.eventClick").removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var data = $('#listaIncidencias').DataTable().row($(this)).data();
        // console.log(data);
        $("[name=id_incidencia]").val(data.id_incidencia ?? 0);
        $("[name=codigo_incidencia]").val(data.codigo !=null? data.codigo:'');
        $("[name=cliente_incidencia]").val(data.cliente !=null? data.cliente:'');
        $("#modal-listaIncidencias").modal("hide");
    });
});

function listarIncidencias() {
    var vardataTables = funcDatatables();

    $("#listaIncidencias").dataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        bDestroy: true,
        ajax: {
            url: "listarIncidencias",
            type: "POST"
        },
        columns: [
            { data: "id_incidencia" },
            { data: "codigo" },
            { data: "cliente", name: 'contribuyente.razon_social' },
            { data: "factura" },
            { data: "falla_reportada" },
            { data: 'estado.descripcion' }
        ],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        order: [[0, "desc"]]
    });
}

function openIncidenciaModal() {
    $("#modal-listaIncidencias").modal({
        show: true
    });
    listarIncidencias();
}
