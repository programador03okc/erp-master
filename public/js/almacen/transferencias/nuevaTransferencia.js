let itemsTransferencia = [];

function openNuevaTransferencia() {
    $("#modal-nuevaTransferencia").modal({
        show: true
    });
    $('[name=id_almacen_origen_nueva]').val('');
    $('[name=id_almacen_destino_nueva]').val('');
    $('[name=concepto]').val('');

    itemsTransferencia = [];
    $('#detalleTransferencia tbody').html('');
}

$("[name=id_almacen_origen_nueva]").on('change', function () {
    var id_almacen_origen = $(this).val();
    console.log(id_almacen_origen);
    $('[name=id_almacen_destino_nueva]').html('');

    if (id_almacen_origen !== undefined && id_almacen_origen !== null && id_almacen_origen !== '') {
        listarAlmacenesSegunOrigen(id_almacen_origen);
    }
});

function listarAlmacenesSegunOrigen(id) {
    $.ajax({
        type: "GET",
        url: "getAlmacenesPorEmpresa/" + id,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            var option = '<option value="">Elija una opción</option>';
            response.forEach(element => {
                if (response.length == 1) {
                    option +=
                        '<option value="' + element.id_almacen + '" selected>' + element.descripcion + "</option>";
                } else {
                    option +=
                        '<option value="' + element.id_almacen + '">' + element.descripcion + "</option>";
                }
            });
            $('[name=id_almacen_destino_nueva]').html(option);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function agregarProducto() {
    var id_almacen = $('[name=id_almacen_origen_nueva]').val();

    if (id_almacen !== '') {
        $("#modal-productosAlmacen").modal({
            show: true
        });
        listarSaldosProductoAlmacen();
    } else {
        Lobibox.notify("warning", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Debe seleccionar un almacén de origen.'
        });
    }
}

function mostrarItemsTransferencia() {
    var html = '';
    $('#detalleTransferencia tbody').html('');

    itemsTransferencia.forEach(function (element) {

        html += `<tr>
            <td><a href="#" class="verProducto" data-id="${element.id_producto}" >${element.codigo}</a></td>
            <td>${element.cod_softlink ?? ''}</td>
            <td>${element.part_number ?? ''}</td>
            <td>${element.descripcion}</td>
            <td>${element.stock_disponible}</td>
            <td><input class="right cantidad" type="number" data-id="${element.id_producto}" value="${element.cantidad}" 
            min="1" max="${element.stock_disponible}" step="0.001" style="width:80px;"/></td>
            <td>${element.abreviatura}</td>
            <td>
                <button type="button" class="quitar btn btn-danger btn-xs" data-toggle="tooltip" 
                        data-placement="bottom" title="Quitar item" 
                        data-id="${element.id_producto}">
                        <i class="fas fa-minus"></i></button>
            </td>
        </tr>`;
    });
    $('#detalleTransferencia tbody').html(html);
}

$('#detalleTransferencia tbody').on("change", ".cantidad", function () {

    let idprod = $(this).data('id');
    let cantidad = parseFloat($(this).val());
    console.log('cantidad: ' + cantidad);

    itemsTransferencia.forEach(element => {
        if (element.id_producto == idprod) {
            element.cantidad = cantidad;
        }
    });
    console.log(itemsTransferencia);
    mostrarItemsTransferencia();
});

$("#detalleTransferencia tbody").on("click", "a.verProducto", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    localStorage.setItem("id_producto", id);
    var win = window.open("/almacen/catalogos/productos/index", '_blank');
    win.focus();
});

$("#detalleTransferencia tbody").on("click", ".quitar", function () {
    let id = $(this).data("id");
    console.log(id);
    let index = itemsTransferencia.findIndex(function (item, i) {
        return item.id_producto == id;
    });
    itemsTransferencia.splice(index, 1);
    mostrarItemsTransferencia();
});

$("#form-nuevaTransferencia").on("submit", function (e) {
    e.preventDefault();
    let error_stock = 0;
    let cero = 0;
    let listaItems = [];

    itemsTransferencia.forEach(function (element) {
        if (element.stock_disponible < element.cantidad) {
            error_stock++
        }
        if (element.cantidad <= 0) {
            cero++
        }
        listaItems.push({
            "id_transferencia": element.id_transferencia,
            "id_producto": element.id_producto,
            "cantidad": element.cantidad,
        });
    });

    if (error_stock > 0) {
        Lobibox.notify("warning", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Ha superado el stock disponible.'
        });
    }
    if (cero > 0) {
        Lobibox.notify("warning", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Debe ingresar cantidades mayores que 0.'
        });
    }

    if (error_stock == 0 && cero == 0) {
        var id_almacen_origen = $("[name=id_almacen_origen_nueva]").val();
        var id_almacen_destino = $("[name=id_almacen_destino_nueva]").val();
        var concepto = $("[name=concepto]").val();

        var data = {
            id_almacen_origen: id_almacen_origen,
            id_almacen_destino: id_almacen_destino,
            concepto: concepto,
            detalle: listaItems
        };
        // data += '&detalle=' + JSON.stringify(listaItems);
        console.log(data);

        $.ajax({
            type: 'POST',
            url: 'nuevaTransferencia',
            data: data,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                $("#modal-nuevaTransferencia").modal("hide");
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Se guardó correctamente la transferencia.'
                });
                // $("#listaRequerimientos").DataTable().ajax.reload(null, false);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
});