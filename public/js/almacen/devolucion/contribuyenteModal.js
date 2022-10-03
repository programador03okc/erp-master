$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaContribuyentes tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaContribuyentes")
                .dataTable()
                .$("tr.eventClick")
                .removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var myId = $(this)[0].firstChild.innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;

        var page = $(".page-main").attr("type");

        if (page == "devolucion") {
            console.log(page);
            $("[name=id_contribuyente]").val(myId);
            $("[name=contribuyente]").val(des.trim());
        }
        $("#modal-contribuyente").modal("hide");
    });
});

function listarContribuyentes() {
    var vardataTables = funcDatatables();

    let botones = [];
    // botones.push({
    //     text: 'Nuevo transportista',
    //     action: function () {
    //         agregarTransportista();
    //     }, className: 'btn-primary'
    // });

    $("#listaContribuyentes").dataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        bDestroy: true,
        serverSide: true,
        ajax: {
            url: "mostrarContribuyentes",
            type: "POST"
        },
        columns: [
            { data: "id_contribuyente" },
            { data: "nro_documento" },
            { data: "razon_social" }
        ],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        order: [[2, "asc"]]
    });
}

function openContribuyenteModal() {
    $("#modal-contribuyente").modal({
        show: true
    });
    listarContribuyentes();
}
