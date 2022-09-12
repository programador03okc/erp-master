function agregarProducto(sel) {
    sel_producto = sel;
    items.push({
        'id_detalle': 0,
        'id_producto': sel.id_producto,
        'part_number': sel.part_number,
        'codigo': sel.codigo,
        'descripcion': sel.descripcion,
        'unid_med': sel.unid_med,
        'id_moneda': sel.id_moneda,
        'control_series': sel.control_series,
        'series': [],
        'estado': 1,
        'cantidad': 1,
        'unitario': 0,
        'total': 0,
    });
    mostrarProductos();
}

function mostrarProductos() {
    $("#listaProductosDevolucion tbody").html('');
    var row = '';

    items.forEach(sel => {
        if (sel.estado == 1) {
            row += `<tr>
                <td>${sel.codigo}</td>
                <td>${sel.part_number !== null ? sel.part_number : ''}</td>
                <td>${sel.descripcion}</td>
                <td><input type="number" class="form-control edition calcula" name="cantidad" id="cantidad" 
                    data-id="${sel.id_producto}" value="${sel.cantidad}"></td>
                <td>${sel.unid_med}</td>
                <td>
                    <i class="fas fa-trash icon-tabla red boton delete" data-id="${sel.id_producto}"
                        data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
                </td>
            </tr>`;
        }
    })
    $("#listaProductosDevolucion tbody").html(row);
}

// Delete row on delete button click
$('#listaProductosDevolucion tbody').on("click", ".delete", function () {
    var anula = confirm("¿Esta seguro que desea anular éste item?");

    if (anula) {
        let id_producto = $(this).data('id');
        if (id_producto !== '') {
            let item = items.find(element => {
                return element.id_producto == id_producto;
            });

            if (item.id_detalle == 0) {
                let index = items.findIndex(function (item, i) {
                    return item.id_producto == id_producto;
                });
                items.splice(index, 1);
            } else {
                item.estado = 7;
            }
        }
        $(this).parents("tr").remove();
        mostrarProductos();
    }
});
// Calcula total
$('#listaProductosDevolucion tbody').on("change", ".calcula", function () {
    var cantidad = $(this).parents("tr").find('input[name=cantidad]').val();
    // var unitario = $(this).parents("tr").find('input[name=unitario]').val();
    let id_producto = $(this).data('id');

    if (cantidad !== '') {
        items.forEach(element => {
            if (element.id_producto == id_producto) {
                element.cantidad = parseFloat(cantidad);
            }
        });
    }
    mostrarProductos();
});