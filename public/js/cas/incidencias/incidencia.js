let listaSeriesProductos = [];

$(function () {
    $(".edition").attr('disabled', 'true');
    $(".guardar-incidencia").hide();
    $(".edit-incidencia").show();

    var id_incidencia = localStorage.getItem("id_incidencia");

    if (id_incidencia !== null && id_incidencia !== undefined) {
        // mostrarFicha(id_incidencia);
        localStorage.removeItem("id_incidencia");
    }
});

function openContacto() {
    var id_requerimiento = $("[name=id_requerimiento]").val();
    var id_contribuyente = $("[name=id_contribuyente]").val();
    var id_entidad = $("[name=id_entidad]").val();
    var id_contacto = $("[name=id_contacto]").val();
    var codigo = $("[name=codigo_oportunidad]").val() + ' - ' + $(".codigo_requerimiento").text();

    openDespachoContactoIncidencia(id_requerimiento, id_contribuyente, id_entidad, id_contacto, codigo);
}

$(".nueva-incidencia").on('click', function () {

    $(".edition").removeAttr("disabled");
    $(".guardar-incidencia").show();
    $(".nueva-incidencia").hide();
    $(".anular-incidencia").hide();
    $(".edit-incidencia").hide();
    $(".buscar-incidencia").hide();
    $("[name=modo]").val("edicion");

    $(".limpiarIncidencia").val("");
    $("[name=id_incidencia]").val("");

});

function mostrarListaSeriesProductos() {
    var html = '';
    listaSeriesProductos.forEach(function (element) {
        html += `<tr>
        <td>${element.serie}</td>
        <td>${element.codigo}</td>
        <td>${element.part_number}</td>
        <td>${element.descripcion}</td>
        </tr>`;
    });
    $('#seriesProductos tbody').html(html);
}

$("#form-incidencia").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    Swal.fire({
        title: "¿Está seguro que desea guardar la incidencia?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {
        if (result.isConfirmed) {
            let detalle = [];
            listaSeriesProductos.forEach(function (element) {
                detalle.push({
                    'id_producto': element.id_producto,
                    'id_prod_serie': element.id_prod_serie,
                    'serie': element.serie
                });
            })
            data += '&detalle=' + JSON.stringify(detalle);
            console.log(data);
            guardarIncidencia(data);
        }
    });
});


function guardarIncidencia(data) {
    $("#submit_incidencia").attr('disabled', 'true');
    $.ajax({
        type: 'POST',
        url: 'guardarIncidencia',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}