$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaTransportistas tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaTransportistas")
                .dataTable()
                .$("tr.eventClick")
                .removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var myId = $(this)[0].firstChild.innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;

        var page = $(".page-main").attr("type");

        if (page == "transferencias") {
            $("[name=id_transportista]").val(myId);
            $("[name=transportista]").val(des);
        }
        else if (page == "listaOrdenesDespachoExterno") {
            $("[name=tr_id_transportista]").val(myId);
            $("[name=tr_razon_social]").val(des);
        }

        $("#modal-transportistas").modal("hide");
    });
});

function listarTransportistas() {
    var vardataTables = funcDatatables();

    let botones = [];
    botones.push({
        text: 'Nuevo transportista',
        action: function () {
            agregarTransportista();
        }, className: 'btn-primary'
    });

    $("#listaTransportistas").dataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        bDestroy: true,
        ajax: "mostrarTransportistas",
        columns: [
            { data: "id_contribuyente" },
            { data: "nro_documento" },
            { data: "razon_social" }
        ],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        order: [[2, "asc"]]
    });
}

function openTransportistaModal() {
    $("#modal-transportistas").modal({
        show: true
    });
    listarTransportistas();
}
