let listaAlmacenUsuarios = [];

function openAlmacenUsuario() {
    let id = $("[name=id_almacen]").val();

    if (id !== "") {
        $("#modal-almacen_usuario").modal({
            show: true
        });
        $("[name=id_usuario]").val("");
        $("[name=nombre_completo]").val("");
        $("[name=crear_editar]").prop("checked", false);
        $("[name=ver]").prop("checked", false);
    } else {
        alert("Es necesario que seleccione un almacén!");
    }
}

$("#form-almacen_usuario").on("submit", function(e) {
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    guardar_almacen_usuario(data);
});

function guardar_almacen_usuario(data) {
    $.ajax({
        type: "POST",
        url: "guardarAlmacenUsuario",
        data: data,
        dataType: "JSON",
        success: function(response) {
            console.log("response" + response);
            if (response > 0) {
                alert("Usuario registrado con éxito");
                $("#modal-almacen_usuario").modal("hide");
                let id = $("[name=id_almacen]").val();
                obtenerUsuarios(id);
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function obtenerUsuarios(id) {
    console.log(id + "id");
    listaAlmacenUsuarios = [];
    $.ajax({
        type: "GET",
        url: "listarAlmacenUsuarios/" + id,
        dataType: "JSON",
        success: function(response) {
            console.log(response);
            listaAlmacenUsuarios = response;
            mostrarListaAlmacenUsuarios();
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrarListaAlmacenUsuarios() {
    var html = "";
    var i = 1;

    listaAlmacenUsuarios.forEach(element => {
        html += `<tr>
        <td>${i}</td>
        <td>${element.nombre_completo}</td>
        <td>${element.editar ? "Si" : "No"}</td>
        <td>${element.ver ? "Si" : "No"}</td>
        <td></td>
        </tr>`;
        i++;
    });

    $("#listaAlmacenUsuarios tbody").html(html);
}
