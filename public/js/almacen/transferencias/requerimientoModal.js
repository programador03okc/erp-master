$(function() {
    $("#listaRequerimientos tbody").on("click", "tr", function() {
        console.log($(this));
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaRequerimientos")
                .dataTable()
                .$("tr.eventClick")
                .removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var id = $(this)[0].firstChild.innerHTML;

        if (id !== null) {
            ver_requerimiento(id);
            $("#modal-requerimiento").modal("hide");
        }
        // var idPr = $(this)[0].childNodes[5].innerHTML;
        // $(".modal-footer #mid_requerimiento").text(id);
        // $('.modal-footer #mid_doc_prov').text(idPr);
    });
});

function listarRequerimientos() {
    var vardataTables = funcDatatables();
    $("#listaRequerimientos").dataTable({
        dom: vardataTables[1],
        buttons: vardataTables[2],
        language: vardataTables[0],
        destroy: true,
        ajax: "listarRequerimientos",
        columns: [
            { data: "id_requerimiento" },
            { data: "codigo" },
            { data: "concepto" },
            {
                render: function(data, type, row) {
                    return formatDate(row["fecha_entrega"]);
                }
            },
            { data: "estado_doc" }
        ],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }]
    });
}

function openRequerimientoModal() {
    $("#modal-requerimiento").modal({
        show: true
    });
    clearDataTable();
    listarRequerimientos();
}

// function selectRequerimiento() {
//     var myId = $(".modal-footer #mid_requerimiento").text();
//     var page = $(".page-main").attr("type");
//     // var form = $('.page-main form[type=register]').attr('id');

//     // if (page == "transferencias"){
//     // var activeTab = $("#tab-doc_compra #myTab li.active a").attr('type');
//     // var activeForm = "form-"+activeTab.substring(1);
//     // actualizar_tab(activeForm, myId);
//     ver_requerimiento(myId);
//     // }
//     $("#modal-requerimiento").modal("hide");
// }
