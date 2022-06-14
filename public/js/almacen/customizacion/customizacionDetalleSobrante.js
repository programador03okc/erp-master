let sel_producto_sobrante = null;
let origen = '';

function agregarCustomizacionSobrante(sel) {
    sel_producto_sobrante = sel;
    items_sobrante.push({
        'id_sobrante': 0,
        'id_producto': sel.id_producto,
        'part_number': sel.part_number,
        'codigo': sel.codigo,
        'descripcion': sel.descripcion,
        'unid_med': sel.unid_med,
        'cantidad': sel.cantidad,
        'unitario': sel.unitario,
        'total': sel.total,
    });
    mostrarProductoSobrante();
}

function mostrarProductoSobrante() {
    $("#listaSobrantes tbody").html('');
    var row = '';
    var mon = $('[name=id_moneda]').val();
    var totalSobrantesTransformados = 0;

    items_sobrante.forEach(sel => {
        totalSobrantesTransformados += parseFloat(sel.total);
        row = `<tr>
            <td>${sel.codigo}</td>
            <td>${sel.part_number !== null ? sel.part_number : ''}</td>
            <td>${sel.descripcion}</td>
            <td><input type="number" class="form-control edition calcula" name="cantidad" id="cantidad" 
                data-id="${sel.id_producto}" value="${sel.cantidad}"></td>
            <td>${sel.unid_med}</td>
            <td>
                <div style="display:flex;">
                    <span style="font-size: 17px;">${(mon == 1 ? 'S/' : '$')}</span>
                    <input type="number" class="form-control edition calcula" name="unitario" id="unitario" 
                    data-id="${sel.id_producto}" value="${sel.unitario}">
                </div>
            </td>
            <td>
                <div style="display:flex;">
                    <span style="font-size: 17px;">${(mon == 1 ? 'S/' : '$')}</span>
                    <input type="number" class="form-control" name="total" readOnly id="total" 
                    data-id="${sel.id_producto}" value="${sel.total}">
                </div>
            </td>
            <td>
            <i class="fas fa-trash icon-tabla red boton delete" data-id="${sel.id_producto}"
                data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
            </td>
        </tr>`;
    })
    $("#listaSobrantes tbody").html(row);

    items_transformado.forEach(sel => {
        totalSobrantesTransformados += parseFloat(sel.total);
    });

    $("#totalSobrantesTransformados tbody").html(`<tr>
        <td style="text-align:right; width:80%"><span style="font-size: 17px;">Total</span></td>
        <td style="text-align:center"><span style="font-size: 17px;">${(mon == 1 ? 'S/' : '$') + formatNumber.decimal(totalSobrantesTransformados, '', -2)}</span></td>
    </tr>`);
}

function agregarProductoSobrante() {
    var id_almacen = $('[name=id_almacen]').val();

    if (id_almacen !== '') {
        origen = 'sobrante';
        $("#modal-productoCatalogo").modal({
            show: true
        });
        clearDataTable();
        listarProductosCatalogo();
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
$('#listaSobrantes tbody').on("click", ".delete", function () {
    var anula = confirm("¿Esta seguro que desea anular éste item?");

    if (anula) {
        let id_producto = $(this).data('id');
        if (id_producto !== '') {
            let index = items_sobrante.findIndex(function (item, i) {
                return item.id_producto == id_producto;
            });
            items_sobrante.splice(index, 1);
        }
        $(this).parents("tr").remove();
    }
});

// Calcula total
$('#listaSobrantes tbody').on("change", ".calcula", function () {
    var cantidad = $(this).parents("tr").find('input[name=cantidad]').val();
    var unitario = $(this).parents("tr").find('input[name=unitario]').val();
    let id_producto = $(this).data('id');

    if (cantidad !== '' && unitario !== '') {
        items_sobrante.forEach(element => {
            if (element.id_producto == id_producto) {
                element.cantidad = parseFloat(cantidad);
                element.unitario = parseFloat(unitario);
                element.total = (parseFloat(unitario) * parseFloat(cantidad));
                console.log(element);
            }
        });
        $(this).parents("tr").find('input[name=total]').val(parseFloat(cantidad) * parseFloat(unitario));
    } else {
        $(this).parents("tr").find('input[name=total]').val(0);
    }
});