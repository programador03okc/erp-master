$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaClientes tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaClientes")
                .dataTable()
                .$("tr.eventClick")
                .removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var myId = $(this)[0].firstChild.innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;

        $("[name=id_cliente]").val(myId);
        $("[name=razon_social_cliente]").val(des);


        $("#modal-clientes").modal("hide");
    });
});

function listarClientes() {
    var vardataTables = funcDatatables();

    let botones = [];
    botones.push({
        text: 'Nuevo Cliente',
        action: function () {
            agregarCliente();
        }, className: 'btn-primary'
    });

    $("#listaClientes").dataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        bDestroy: true,
        ajax: "mostrarClientes",
        columns: [
            { data: "id_cliente" },
            { data: "nro_documento" },
            { data: "razon_social" }
        ],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        order: [[2, "asc"]]
    });
}

function openClienteModal() {
    $("#modal-clientes").modal({
        show: true
    });
    listarClientes();
}
