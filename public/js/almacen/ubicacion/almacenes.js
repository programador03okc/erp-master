$(function() {
    listar_almacenes();
    var form = $(".page-main form[type=register]").attr("id");
    $(".group-table .mytable tbody").on("click", "tr", function() {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaAlmacen")
                .dataTable()
                .$("tr.eventClick")
                .removeClass("eventClick");
            $(this).addClass("eventClick");
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        mostrar_almacen(id);
        changeStateButton("historial");
    });
});
function listar_almacenes() {
    var vardataTables = funcDatatables();
    $("#listaAlmacen").dataTable({
        dom: vardataTables[1],
        buttons: vardataTables[2],
        language: vardataTables[0],
        bDestroy: true,
        ajax: "listar_almacenes",
        columns: [
            { data: "id_almacen" },
            { data: "sede_descripcion" },
            { data: "codigo" },
            { data: "descripcion" },
            { data: "tp_almacen" }
            // {'render':
            //     function (data, type, row){
            //         return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
            //     }
            // }
        ],
        order: [[2, "asc"]],
        columnDefs: [{ aTargets: [0], sClass: "invisible" }]
    });
}
function mostrar_almacen(id) {
    baseUrl = "mostrar_almacen/" + id;
    $("#listaAlmacenUsuarios tbody").html("");
    $.ajax({
        type: "GET",
        url: baseUrl,
        dataType: "JSON",
        success: function(response) {
            $("[name=id_almacen]").val(response[0].id_almacen);
            $("[name=id_sede]").val(response[0].id_sede);
            $("[name=id_tipo_almacen]").val(response[0].id_tipo_almacen);
            $("[name=codigo]").val(response[0].codigo);
            $("[name=descripcion]").val(response[0].descripcion);
            $("[name=ubicacion]").val(response[0].ubicacion);
            if (response[0].ubigeo !== null) {
                $("[name=name_ubigeo]").val(response[0].name_ubigeo);
            } else {
                $("[name=name_ubigeo]").val("");
            }

            obtenerUsuarios(id);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_almacen(data, action) {
    var msj;
    if (action == "register") {
        baseUrl = "guardar_almacen";
        msj = "Almacén registrado con exito";
    } else if (action == "edition") {
        baseUrl = "editar_almacen";
        msj = "Almacén editado con exito";
    }
    $.ajax({
        type: "POST",
        headers: { "X-CSRF-TOKEN": token },
        url: baseUrl,
        data: data,
        dataType: "JSON",
        success: function(response) {
            console.log(response);
            if (response > 0) {
                alert(msj);
                $("#listaAlmacen")
                    .DataTable()
                    .ajax.reload();
                // listar_almacenes();
                changeStateButton("guardar");
                $("#form-almacenes").attr("type", "register");
                changeStateInput("form-almacenes", true);
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_almacen(ids) {
    baseUrl = "anular_almacen/" + ids;
    $.ajax({
        type: "GET",
        headers: { "X-CSRF-TOKEN": token },
        url: baseUrl,
        dataType: "JSON",
        success: function(response) {
            console.log(response);
            if (response > 0) {
                alert("Almacén anulado con exito");
                $("#listaAlmacen")
                    .DataTable()
                    .ajax.reload();
                changeStateButton("anular");
                clearForm("form-almacenes");
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
