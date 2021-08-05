let listaItems = [];
let totales = {};
let origen = "";

function open_doc_ven_create(id_guia) {
    console.log("open_doc_ven_create");
    origen = "guia";

    $("#modal-doc_ven_create").modal({
        show: true
    });

    $("#detalleItems tbody").html("");
    listaItems = [];
    var id_tp_doc = 2;

    $("[name=id_tp_doc]")
        .val(id_tp_doc)
        .trigger("change.select2");
    $("[name=fecha_emision_doc]").val(fecha_actual());
    $("[name=serie_doc]").val("");
    $("[name=numero_doc]").val("");
    $("[name=moneda]").val(1);
    $("[name=simbolo]").val("S/");

    $(".guia").show();
    $(".ocam").hide();
    // totales.simbolo = "S/";
    obtenerGuía(id_guia);
}

function open_doc_ven_requerimiento_create(id_requerimiento) {
    console.log("open_doc_ven_create");
    origen = "requerimiento";

    $("#modal-doc_ven_create").modal({
        show: true
    });

    $("#detalleItems tbody").html("");
    listaItems = [];

    var id_tp_doc = 2;
    $("[name=id_tp_doc]")
        .val(id_tp_doc)
        .trigger("change.select2");
    $("[name=fecha_emision_doc]").val(fecha_actual());
    $("[name=serie_doc]").val("");
    $("[name=numero_doc]").val("");
    $("[name=moneda]").val(1);
    $("[name=simbolo]").val("S/");
    $("[name=id_requerimiento]").val(id_requerimiento);

    $(".guia").hide();
    $(".ocam").show();
    // totales.simbolo = "S/";
    obtenerRequerimiento(id_requerimiento);
}

function obtenerGuía(id) {
    $.ajax({
        type: "GET",
        url: "obtenerGuiaVenta/" + id,
        dataType: "JSON",
        success: function(response) {
            console.log(response);
            let simbolo = "";

            if (response["guia"] !== null) {
                $("[name=id_cliente]").val(response["guia"].id_cliente);
                $("[name=cliente_razon_social]").val(
                    response["guia"].razon_social
                );
                $("[name=cliente_ruc]").val(
                    response["guia"].nro_documento !== undefined
                        ? response["guia"].nro_documento
                        : ""
                );
                $("[name=id_guia]").val(response["guia"].id_guia_ven);
                $("[name=serie_guia]").val(response["guia"].serie);
                $("[name=numero_guia]").val(response["guia"].numero);
                $("[name=id_sede]").val(response["guia"].id_sede);
            }

            if (response["detalle"].length > 0) {
                listaItems = response["detalle"];
                simbolo = listaItems[0].moneda_oc == "s" ? "S/" : "$";

                $("[name=moneda]").val(listaItems[0].moneda_oc == "s" ? 1 : 2);
                $("[name=simbolo]").val(simbolo);
                $("[name=importe_oc]").val(listaItems[0].monto_total);

                totales = { porcentaje_igv: parseFloat(response["igv"]) };

                totales.simbolo = simbolo;
                mostrarListaItems();
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function obtenerRequerimiento(id) {
    $.ajax({
        type: "GET",
        url: "obtenerRequerimiento/" + id,
        dataType: "JSON",
        success: function(response) {
            console.log(response);
            let simbolo = "";

            if (response["req"] !== null) {
                $("[name=id_cliente]").val(response["req"].id_cliente);
                $("[name=cliente_razon_social]").val(
                    response["req"].razon_social
                );
                $("[name=cliente_ruc]").val(response["req"].nro_documento);
                $("[name=id_requerimiento]").val(
                    response["req"].id_requerimiento
                );
                // $("[name=serie_guia]").val(response["req"].serie);
                // $("[name=numero_guia]").val(response["req"].numero);
                $("[name=id_sede]").val(response["req"].id_sede);
            }

            if (response["detalle"].length > 0) {
                listaItems = response["detalle"];
                simbolo = listaItems[0].moneda_oc == "s" ? "S/" : "$";

                $("[name=moneda]").val(listaItems[0].moneda_oc == "s" ? 1 : 2);
                $("[name=simbolo]").val(simbolo);
                $("[name=importe_oc]").val(listaItems[0].monto_total);

                totales = { porcentaje_igv: parseFloat(response["igv"]) };

                totales.simbolo = simbolo;
                mostrarListaItems();
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrarListaItems() {
    var html = "";
    var i = 1;
    var sub_total = 0;

    listaItems.forEach(element => {
        element.porcentaje_dscto =
            element.porcentaje_dscto !== undefined
                ? element.porcentaje_dscto
                : 0;
        element.total_dscto =
            element.total_dscto !== undefined ? element.total_dscto : 0;
        element.precio = element.precio !== undefined ? element.precio : 0.01;
        element.sub_total =
            parseFloat(element.cantidad) * parseFloat(element.precio);
        element.total = element.sub_total - element.total_dscto;
        sub_total += element.total;

        html += `<tr>
        <td>${i}</td>
        <td>${
            element.cod_req !== undefined
                ? element.cod_req
                : element.serie !== undefined
                ? element.serie + "-" + element.numero
                : ""
        }</td>
        <td>${element.codigo !== null ? element.codigo : ""}</td>
        <td>${element.part_number !== null ? element.part_number : ""}</td>
        <td>${
            element.id_producto == null
                ? `<input type="text" class="form-control descripcion" value="${element.descripcion}" data-id="${element.id_detalle_requerimiento}"/>`
                : element.descripcion
        }</td>
        <td>${element.cantidad}</td>
        <td>${element.abreviatura}</td>
        <td>
            <input type="number" class="form-control right unitario" value="${
                element.precio
            }" 
            data-id="${
                element.id_detalle_requerimiento !== undefined
                    ? element.id_detalle_requerimiento
                    : element.id_guia_ven_det
            }" min="0" step="0.0001"/>
        </td>
        <td class="right">${formatNumber.decimal(
            element.sub_total,
            "",
            -4
        )}</td>
        <td>
            <input type="number" class="form-control right porcentaje_dscto" value="${
                element.porcentaje_dscto
            }" 
            data-id="${
                element.id_detalle_requerimiento !== undefined
                    ? element.id_detalle_requerimiento
                    : element.id_guia_ven_det
            }" min="0" step="0.0001"/>
        </td>
        <td>
            <input type="number" class="form-control right total_dscto" value="${
                element.total_dscto
            }" 
            data-id="${
                element.id_detalle_requerimiento !== undefined
                    ? element.id_detalle_requerimiento
                    : element.id_guia_ven_det
            }" min="0" step="0.0001"/>
        </td>
        <td class="right">${formatNumber.decimal(element.total, "", -4)}</td>
        <td>
        <button type="button" class="quitar btn btn-danger btn-xs" data-toggle="tooltip" 
            data-placement="bottom" title="Quitar item" 
            data-id="${
                element.id_detalle_requerimiento !== undefined
                    ? element.id_detalle_requerimiento
                    : element.id_guia_ven_det
            }">
            <i class="fas fa-minus"></i></button>
        </td>
        </tr>`;
        i++;
    });

    $("#detalleItems tbody").html(html);

    totales.sub_total = sub_total;
    totales.igv = (totales.porcentaje_igv * sub_total) / 100;
    totales.total = sub_total + totales.igv;
    totales.simbolo = $('select[name="moneda"] option:selected').data("sim");

    var html_foot = `<tr>
        <th colSpan="11" class="text-right">Sub Total <label name="sim">${
            totales.simbolo
        }</label></th>
        <th class="text-right">${formatNumber.decimal(
            totales.sub_total,
            "",
            -2
        )}</th>
    </tr>
    <tr>
        <th colSpan="11" class="text-right">IGV ${
            totales.porcentaje_igv
        }% <label name="sim">${totales.simbolo}</label></th>
        <th class="text-right">${formatNumber.decimal(totales.igv, "", -2)}</th>
    </tr>
    <tr>
        <th colSpan="11" class="text-right"> Total <label name="sim">${
            totales.simbolo
        }</label></th>
        <th class="text-right">${formatNumber.decimal(
            totales.total,
            "",
            -2
        )}</th>
    </tr>
    `;
    $("#detalleItems tfoot").html(html_foot);
    $("[name=importe]").val(formatNumber.decimal(totales.total, "", -2));
}

$("#detalleItems tbody").on("change", ".unitario", function() {
    let id = $(this).data("id");
    let unitario = parseFloat($(this).val());
    console.log("unitario: " + unitario);

    listaItems.forEach(element => {
        if (
            element.id_guia_ven_det == id ||
            element.id_detalle_requerimiento == id
        ) {
            element.precio = unitario;
            element.sub_total = unitario * parseFloat(element.cantidad);
            element.total = element.sub_total - element.total_dscto;
            console.log(element);
        }
    });
    mostrarListaItems();
});

$("#detalleItems tbody").on("change", ".porcentaje_dscto", function() {
    let id = $(this).data("id");
    let porcentaje_dscto = parseFloat($(this).val());
    let unitario = 0;
    console.log("porcentaje_dscto: " + porcentaje_dscto);

    listaItems.forEach(element => {
        if (
            element.id_guia_ven_det == id ||
            element.id_detalle_requerimiento == id
        ) {
            element.porcentaje_dscto = porcentaje_dscto;
            element.total_dscto = (porcentaje_dscto * element.sub_total) / 100;
            element.total = element.sub_total - element.total_dscto;
            console.log(element);
        }
    });
    mostrarListaItems();
});

$("#detalleItems tbody").on("change", ".total_dscto", function() {
    let id = $(this).data("id");
    let total_dscto = parseFloat($(this).val());
    console.log("total_dscto: " + total_dscto);

    listaItems.forEach(element => {
        if (
            element.id_guia_ven_det == id ||
            element.id_detalle_requerimiento == id
        ) {
            element.porcentaje_dscto = 0;
            element.total_dscto = total_dscto;
            element.total = element.sub_total - total_dscto;
            console.log(element);
        }
    });
    mostrarListaItems();
});

$("#detalleItems tbody").on("click", ".quitar", function() {
    let id = $(this).data("id");
    console.log(id);
    let index = listaItems.findIndex(function(item, i) {
        return (
            item.id_detalle_requerimiento == id || item.id_guia_ven_det == id
        );
    });
    listaItems.splice(index, 1);
    mostrarListaItems();
});

function ceros_numero_doc() {
    var num = $("[name=numero_doc]").val();
    $("[name=numero_doc]").val(leftZero(6, num));
}

function changeMoneda() {
    var simbolo = $('select[name="moneda"] option:selected').data("sim");
    if (simbolo.length > 0) {
        console.log(simbolo);
        $("[name=simbolo]").val(simbolo);
        $("[name=sim]").text(simbolo);
    } else {
        $("[name=simbolo]").val("");
        $("[name=sim]").text("");
    }
}

$("#form-doc_create").on("submit", function(e) {
    e.preventDefault();
    // var id_doc_ven = $('[name=id_doc_ven]').val();
    var serial = $(this).serialize();
    var listaItemsDetalle = [];
    var nuevo = null;

    listaItems.forEach(element => {
        nuevo = {
            id_guia_ven_det:
                element.id_guia_ven_det !== undefined
                    ? element.id_guia_ven_det
                    : null,
            id_detalle_requerimiento:
                element.id_detalle_requerimiento !== undefined
                    ? element.id_detalle_requerimiento
                    : null,
            id_producto: element.id_producto,
            descripcion: element.id_producto == null ? element.descripcion : "",
            cantidad: element.cantidad,
            id_unid_med: element.id_unid_med,
            precio: element.precio,
            sub_total: element.sub_total,
            porcentaje_dscto: element.porcentaje_dscto,
            total_dscto: element.total_dscto,
            total: element.total
        };
        listaItemsDetalle.push(nuevo);
    });

    var data =
        serial +
        "&sub_total=" +
        totales.sub_total +
        "&porcentaje_igv=" +
        totales.porcentaje_igv +
        "&igv=" +
        totales.igv +
        "&total=" +
        totales.total +
        "&detalle_items=" +
        JSON.stringify(listaItemsDetalle);
    console.log(data);
    guardar_doc_create(data);
});

function guardar_doc_create(data) {
    $.ajax({
        type: "POST",
        url: "guardar_doc_venta",
        data: data,
        dataType: "JSON",
        success: function(response) {
            console.log("response" + response);
            if (response > 0) {
                alert("Comprobante registrado con éxito");
                $("#modal-doc_ven_create").modal("hide");
                let facturacion = new Facturacion();

                if (origen == "guia") {
                    facturacion.listarGuias();
                } else if (origen == "requerimiento") {
                    facturacion.listarRequerimientos();
                }
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
