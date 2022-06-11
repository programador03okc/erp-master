let sel_producto_materia = null;
//Materias Primas
function agregar_producto_materia(sel) {
    sel_producto_materia = sel;
    items_base.push({
        'id_materia': 0,
        'id_producto': sel.id_producto,
        'part_number': sel.part_number,
        'codigo': sel.codigo,
        'descripcion': sel.descripcion,
        'unid_med': sel.unid_med,
        'cantidad': sel.cantidad,
        'unitario': sel.unitario,
        'total': sel.total,
    });
    mostrarProductosBase();
}

function mostrarProductosBase() {
    var row = '';
    $("#listaMateriasPrimas tbody").html('');
    console.log(items_base);
    items_base.forEach(sel => {
        row += `<tr>
        <td>${sel.codigo}</td>
        <td>${sel.part_number !== null ? sel.part_number : ''}</td>
        <td>${sel.descripcion}</td>
        <td><input type="number" class="form-control edition calcula" name="cantidad" id="cantidad" 
            data-id="${sel.id_producto}" value="${sel.cantidad}"></td>
        <td>${sel.unid_med}</td>
        <td><input type="number" class="form-control edition calcula" name="unitario" id="unitario" 
            data-id="${sel.id_producto}" value="${sel.unitario}"></td>
        <td><input type="number" class="form-control" name="total" readOnly id="total" 
            data-id="${sel.id_producto}" value="${sel.total}"></td>
        <td>
        <i class="fas fa-trash icon-tabla red boton edition delete" data-id="${sel.id_producto}"
            data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
        </td>
    </tr>`;
    });
    $("#listaMateriasPrimas tbody").html(row);
    // $(".edition").attr('disabled', 'true');
}

function agregarProductoBase() {
    var id_almacen = $('[name=id_almacen]').val();
    var almacen_descripcion = $('select[name="id_almacen"] option:selected').text();
    console.log(almacen_descripcion);

    if (id_almacen !== '') {
        $("#modal-productosAlmacen").modal({
            show: true
        });
        $('#titulo_almacen').text(almacen_descripcion);
        listarSaldosProductoAlmacen();
    } else {
        Lobibox.notify("warning", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Debe seleccionar un almacén.'
        });
    }
}
// Delete row on delete button click
$('#listaMateriasPrimas tbody').on("click", ".delete", function () {
    var anula = confirm("¿Esta seguro que desea anular éste item?");

    if (anula) {
        let id_producto = $(this).data('id');
        if (id_producto !== '') {
            let index = items_base.findIndex(function (item, i) {
                return item.id_producto == id_producto;
            });
            items_base.splice(index, 1);
        }
        $(this).parents("tr").remove();
    }
});
// Calcula total
$('#listaMateriasPrimas tbody').on("change", ".calcula", function () {
    var cantidad = $(this).parents("tr").find('input[name=cantidad]').val();
    var unitario = $(this).parents("tr").find('input[name=unitario]').val();
    let id_producto = $(this).data('id');

    if (cantidad !== '' && unitario !== '') {
        items_base.forEach(element => {
            if (element.id_producto == id_producto) {
                element.cantidad = parseFloat(cantidad);
                element.unitario = parseFloat(unitario);
                element.total = (parseFloat(unitario) * parseFloat(cantidad));
            }
        });
        $(this).parents("tr").find('input[name=total]').val(parseFloat(cantidad) * parseFloat(unitario));
    } else {
        $(this).parents("tr").find('input[name=total]').val(0);
    }
});