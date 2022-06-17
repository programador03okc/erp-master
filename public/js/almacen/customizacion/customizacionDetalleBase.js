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
        'id_moneda': sel.id_moneda,
        'control_series': sel.control_series,
        'series': [],
        'costo_promedio': 0,
        'cantidad': 1,
        'unitario': 0,
        'total': 0,
        'estado': 1,
    });
    mostrarProductosBase();
}

function mostrarProductosBase() {
    var row = '';
    var mon = $('[name=id_moneda]').val();
    var id_almacen = $('[name=id_almacen]').val();
    var total = 0;
    $("#listaMateriasPrimas tbody").html('');
    console.log(items_base);

    items_base.forEach(sel => {
        if (sel.estado == 1) {
            html_ser = '';
            if (sel.series !== undefined) {
                sel.series.forEach(ser => {
                    if (ser.estado == 1) {
                        html_ser += (html_ser == '' ? '' : ', ') + ser.serie;
                    }
                });
            }
            total += parseFloat(sel.total);
            row += `<tr>
                <td>${sel.codigo}</td>
                <td>${sel.part_number !== null ? sel.part_number : ''}</td>
                <td>${sel.descripcion + ' <br><strong>' + html_ser + '</strong>'}</td>
                <td><input type="number" class="form-control edition calcula" name="cantidad" id="cantidad" 
                    data-id="${sel.id_producto}" value="${sel.cantidad}"></td>
                <td>${sel.unid_med}</td>
                <td><span style="font-size: 17px;text-align:right;">${(sel.id_moneda == 1 ? 'S/' : '$') + sel.costo_promedio}</span></td>
                <td>
                    <div style="display:flex;">
                        <span style="font-size: 17px;">${(mon == 1 ? 'S/' : '$')}</span>
                        <input type="number" class="form-control edition calcula" name="unitario" id="unitario" step="0.0001"
                        data-id="${sel.id_producto}" value="${formatDecimalDigitos(sel.unitario, 4)}">
                    </div>
                </td>
                <td>
                    <div style="display:flex;">
                        <span style="font-size: 17px;">${(mon == 1 ? 'S/' : '$')}</span>
                        <input type="number" class="form-control" name="total" readOnly id="total" step="0.0001"
                        data-id="${sel.id_producto}" value="${formatDecimalDigitos(sel.total, 4)}">
                    </div>
                </td>
                <td>
                <i class="fas fa-trash icon-tabla red boton edition delete" data-id="${sel.id_producto}"
                    data-toggle="tooltip" data-placement="bottom" title="Eliminar" ></i>
                    ${sel.control_series ?
                    `<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" 
                            onClick="open_series_base(${sel.id_producto}, ${sel.cantidad}, ${id_almacen})"></i>`
                    : ''}
                </td>
            </tr>`;
        }
    });
    $("#listaMateriasPrimas tbody").html(row);

    var foot = `<tr>
            <td colspan="7" style="text-align:right;"><span style="font-size: 17px;">Total</span></td>
            <td colspan="2" style="text-align:center;"><span style="font-size: 17px;">${(mon == 1 ? 'S/' : '$')
        + formatNumber.decimal(total, '', -2)}</span></td>
        </tr>`;
    $("#listaMateriasPrimas tfoot").html(foot);
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

            let item = items_base.find(element => {
                return element.id_producto == id_producto;
            });

            if (item.id_materia == 0) {
                let index = items_base.findIndex(function (item, i) {
                    return item.id_producto == id_producto;
                });
                items_base.splice(index, 1);
            } else {
                item.estado = 7;
            }
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
    mostrarProductosBase();
});