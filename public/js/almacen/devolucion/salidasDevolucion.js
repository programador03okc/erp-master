
function verSalidasVenta() {
    var id_almacen = $('[name=id_almacen]').val();
    var id_contribuyente = $('[name=id_contribuyente]').val();

    if (id_almacen == '') {
        Lobibox.notify("warning", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Es necesario que seleccione un almacén.'
        });
    }
    if (id_contribuyente == '') {
        Lobibox.notify("warning", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Es necesario que seleccione un contribuyente.'
        });
    }
    if (id_almacen !== '' && id_contribuyente !== '') {
        abrirSalidasModal(id_almacen, id_contribuyente);
    }
}

function obtenerSalida(id_salida) {
    $.ajax({
        type: 'GET',
        url: 'obtenerSalidaDetalle/' + id_salida,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            response.forEach(sel => {
                items.push({
                    'id_detalle': 0,
                    'id_mov_alm': sel.id_mov_alm,
                    'id_salida_detalle': sel.id_mov_alm_det,
                    'id_producto': sel.id_producto,
                    'part_number': sel.part_number,
                    'codigo': sel.codigo,
                    'descripcion': sel.descripcion,
                    'unid_med': sel.abreviatura,
                    'id_moneda': sel.id_moneda,
                    'control_series': sel.series,
                    'series': [],
                    'estado': 1,
                    'cantidad': sel.cantidad,
                    'unitario': 0,
                    'total': 0,
                });
            });
            mostrarProductos();

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrarSalidas() {
    $("#listaSalidasDevolucion tbody").html('');
    var row = '';

    salidas.forEach(sel => {
        if (sel.estado == 1) {
            row += `<tr>
                <td style="text-align:center">${sel.serie_numero_guia}</td>
                <td style="text-align:center">${sel.serie_numero_doc !== null ? sel.serie_numero_doc : ''}</td>
                <td style="text-align:center">${sel.razon_social}</td>
                <td style="text-align:center">${sel.codigo}</td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton delete" data-id="${sel.id_mov_alm}"
                        data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
                </td>
            </tr>`;
        }
    })
    $("#listaSalidasDevolucion tbody").html(row);
}

// Delete row on delete button click
$('#listaSalidasDevolucion tbody').on("click", ".delete", function () {
    var anula = confirm("¿Esta seguro que desea anular ésta salida?");

    if (anula) {
        let id_mov_alm = $(this).data('id');

        if (id_mov_alm !== '') {
            salidas.forEach(sal => {
                if (sal.id_mov_alm == id_mov_alm) {
                    sal.estado = 7;
                }
            });
            console.log(salidas);

            items.forEach(element => {
                if (element.id_mov_alm == id_mov_alm) {
                    if (element.id_detalle == 0) {
                        let index = items.findIndex(function (item, i) {
                            return item.id_producto == element.id_producto &&
                                item.id_mov_alm == id_mov_alm;
                        });
                        items.splice(index, 1);
                    } else {
                        element.estado = 7;
                    }
                }
            });
        }
        $(this).parents("tr").remove();
        mostrarProductos();
        mostrarSalidas();
    }
});