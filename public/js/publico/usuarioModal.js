$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaUsuarios tbody").on("click", "tr", function () {
        console.log($(this));
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaUsuarios")
                .dataTable()
                .$("tr.eventClick")
                .removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var id = $(this)[0].firstChild.innerHTML;
        // var nc = $(this)[0].childNodes[1].innerHTML;
        // var name = $(this)[0].childNodes[2].innerHTML;

        // $("[name=id_usuario]").val(id);
        // $("[name=nombre_completo]").val(nc !== '' ? nc : name);

        var id_almacen = $('[name=id_almacen]').val();
        var data = 'id_almacen=' + id_almacen + '&id_usuario=' + id;
        guardar_almacen_usuario(data);

        $("#modal-usuarios").modal("hide");
    });
});

function listarUsuarios() {
    console.log("listarUsuarios");
    var vardataTables = funcDatatables();
    $("#listaUsuarios").dataTable({
        dom: vardataTables[1],
        buttons: vardataTables[2],
        language: vardataTables[0],
        bDestroy: true,
        ajax: "listarUsuarios",
        columns: [
            { data: "id_usuario" },
            { data: "nombre_corto" },
            { data: "nombre_completo" },
        ],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }],
        order: [[1, "asc"]]
    });
}

function usuarioModal() {
    $("#modal-usuarios").modal({
        show: true
    });
    listarUsuarios();
}
