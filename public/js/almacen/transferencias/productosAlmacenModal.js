$(function () {
    /* Seleccionar valor del DataTable */
    $("#listaProductoSaldos tbody").on("click", "tr", function () {
        if ($(this).hasClass("eventClick")) {
            $(this).removeClass("eventClick");
        } else {
            $("#listaProductoSaldos").dataTable().$("tr.eventClick").removeClass("eventClick");
            $(this).addClass("eventClick");
        }

        var data = $('#listaProductoSaldos').DataTable().row($(this)).data();
        var id = $('[name=id_transferencia_nuevo]').val();

        let item = itemsTransferencia.find(element => element.id_producto == data.id_producto);

        if (item == undefined) {
            itemsTransferencia.push({
                "id_detalle_transferencia": 0,
                "id_transferencia": id,
                "id_producto": data.id_producto,
                "codigo": data.codigo,
                "cod_softlink": data.cod_softlink,
                "part_number": data.part_number,
                "descripcion": data.descripcion,
                "abreviatura": data.abreviatura,
                "cantidad": 0,
                "stock_disponible": parseFloat(data.stock) - parseFloat(data.stock_comprometido ?? 0),
            });
        } else {
            Lobibox.notify("warning", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Éste producto ya fue agregado.'
            });
        }

        mostrarItemsTransferencia();

        $("#modal-productosAlmacen").modal("hide");
    });
});


function listarSaldosProductoAlmacen() {
    var vardataTables = funcDatatables();

    $('#listaProductoSaldos').dataTable({
        dom: vardataTables[1],
        buttons: [],
        language: vardataTables[0],
        serverSide: true,
        destroy: true,
        ajax: {
            url: "listarProductosAlmacen",
            type: "POST",
            data: function (params) {
                return Object.assign(params, objectifyForm($('#form-nuevaTransferencia').serializeArray()))
            }
        },
        columns: [
            { 'data': 'id_producto' },
            { 'data': 'codigo', name: 'alm_prod.codigo' },
            { 'data': 'cod_softlink', name: 'alm_prod.cod_softlink' },
            { 'data': 'part_number', name: 'alm_prod.part_number' },
            { 'data': 'descripcion', name: 'alm_prod.descripcion' },
            // { 'data': 'stock' },
            {
                'render': function (data, type, row) {
                    return parseFloat(row['suma_ingresos'] ?? 0) - parseFloat(row['suma_salidas'] ?? 0);
                }, orderable: false, searchable: false
            },
            { 'data': 'stock_comprometido', orderable: false, searchable: false },
            {
                'render': function (data, type, row) {
                    return (parseFloat(row['suma_ingresos'] ?? 0) - parseFloat(row['suma_salidas'] ?? 0)) - (parseFloat(row['stock_comprometido'] ?? 0));
                }, orderable: false, searchable: false
            },
            { 'data': 'abreviatura', name: 'alm_und_medida.abreviatura' },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });
}