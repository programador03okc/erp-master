let json_series_ven = [];

function open_series(id_producto, id_od_detalle, cantidad, id_almacen) {
    $("#modal-guia_ven_series").modal({
        show: true
    });
    listarSeries(id_producto, id_almacen);
    json_series_ven = [];
    $("[name=id_od_detalle]").val(id_od_detalle);
    $("[name=id_trans_detalle]").val("");
    $("[name=id_producto]").val(id_producto);
    $("[name=cant_items]").val(cantidad);
    $("[name=seleccionar_todos]").prop("checked", false);
}

function open_series_transferencia(id_trans_detalle, id_producto, cantidad, id_almacen) {
    $("#modal-guia_ven_series").modal({
        show: true
    });

    let item = listaDetalle.find(element => element.id_trans_detalle == id_trans_detalle);
    if (item !== undefined) {
        json_series_ven = item.series;
    }
    listarSeries(id_producto, id_almacen);

    $("[name=id_od_detalle]").val("");
    $("[name=id_trans_detalle]").val(id_trans_detalle);
    $("[name=id_producto]").val(id_producto);
    $("[name=cant_items]").val(cantidad);
    $("[name=seleccionar_todos]").prop("checked", false);
}

function open_series_base(id_producto, cantidad, id_almacen) {
    $("#modal-guia_ven_series").modal({
        show: true
    });

    let item = items_base.find(element => element.id_producto == id_producto);
    if (item !== undefined) {
        json_series_ven = item.series;
    }
    listarSeries(id_producto, id_almacen);

    $("[name=id_od_detalle]").val("");
    $("[name=id_trans_detalle]").val('');
    $("[name=id_producto]").val('');
    $("[name=id_producto_base]").val(id_producto);
    $("[name=cant_items]").val(cantidad);
    $("[name=seleccionar_todos]").prop("checked", false);
}

function listarSeries(id_producto, id_almacen) {
    console.log("id_producto" + id_producto);
    $.ajax({
        type: "GET",
        url: "listarSeriesGuiaVen/" + id_producto + "/" + id_almacen,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            var tr = "";
            var i = 1;
            var value = "";

            response.forEach(element => {
                value = json_series_ven.find(
                    item => item.serie == element.serie && item.estado == 1
                );

                tr += `<tr>
                <td>
                    <input type="checkbox" data-serie="${element.serie
                    }" value="${element.id_prod_serie}" 
                    ${value !== undefined ? "checked" : ""}/></td>
                <td class="numero">${i}</td>
                <td class="serie">${element.serie}</td>
                <td>${element.guia_com ?? 'STOCK INICIAL'}</td>
                </tr>`;

                i++;
            });
            $("#listaSeriesVen tbody").html(tr);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardar_series() {
    let serie = null;
    let id_prod_serie = null;
    let series_chk = [];

    let value = null;
    let obj = "";

    $("#listaSeriesVen input[type=checkbox]:checked").each(function () {
        id_prod_serie = $(this).val();
        serie = $(this).data("serie");
        obj = { serie: serie, id_prod_serie: id_prod_serie, estado: 1 };

        series_chk.push(obj);
        value = json_series_ven.find(item => item.serie == obj.serie);
        //agrego las series nuevas
        if (value == undefined) {
            json_series_ven.push(obj);
        }
    });

    let val = "";

    json_series_ven.forEach(element => {
        val = series_chk.find(item => item.serie == element.serie);
        //anulo las que se deschekearon
        val == undefined ? (element.estado = 7) : (element.estado = 1);
    });

    var id_od_detalle = $("[name=id_od_detalle]").val();
    var id_trans_detalle = $("[name=id_trans_detalle]").val();
    var id_base = $("[name=id_producto_base]").val();
    var cant = $("[name=cant_items]").val();

    var rspta = false;
    var count_series = 0;

    json_series_ven.forEach(item => {
        if (item.estado == 1)
            count_series++
    });

    if (count_series == 0) {

        Swal.fire({
            title: "¿Está seguro que desea quitar las series?",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#00a65a", //"#3085d6", //
            cancelButtonColor: "#d33",
            cancelButtonText: "No",
            confirmButtonText: "Si"
        }).then(result => {
            rspta = result.isConfirmed;
        });

    } else if (parseInt(cant) == count_series) {
        rspta = true;
    } else if (parseInt(cant) > count_series) {
        Swal.fire({
            title: `Se espera ${cant} series, aún le falta seleccionar ${parseInt(cant) - count_series} serie(s).`,
            text: "Seleccione las series.",
            icon: "error",
        });
    } else if (parseInt(cant) < count_series) {
        Swal.fire({
            title: `Se espera ${cant} series, ud. ha seleccionado ${count_series - parseInt(cant)} serie(s) adicionales.`,
            text: "Quite las series restantes.",
            icon: "error",
        });
    }

    if (rspta && id_od_detalle !== "") {
        var json = detalle.find(
            element => element.id_od_detalle == id_od_detalle
        );

        if (json !== null) {
            json.series = json_series_ven;
        }
        console.log(json);
        console.log(detalle);
        mostrar_detalle();
        $("#modal-guia_ven_series").modal("hide");
    } else if (rspta && id_trans_detalle !== "") {
        var json = listaDetalle.find(
            element => element.id_trans_detalle == id_trans_detalle
        );

        if (json !== null) {
            json.series = json_series_ven;
        }
        mostrarDetalleTransferencia();
        $("#modal-guia_ven_series").modal("hide");
    }
    else if (rspta && id_base !== "") {
        var json = items_base.find(element => element.id_producto == id_base);

        if (json !== null) {
            json.series = json_series_ven;
        }
        mostrarProductosBase();
        $("#modal-guia_ven_series").modal("hide");
    }
}

$("[name=seleccionar_todos]").on("change", function () {
    if ($(this).is(":checked")) {
        $("#listaSeriesVen tbody tr").each(function () {
            $(this)
                .find("td input[type=checkbox]")
                .prop("checked", true);
        });
    } else {
        $("#listaSeriesVen tbody tr").each(function () {
            $(this)
                .find("td input[type=checkbox]")
                .prop("checked", false);
        });
    }
});
