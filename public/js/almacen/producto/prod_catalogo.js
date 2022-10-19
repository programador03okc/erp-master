$(function () {
    var vardataTables = funcDatatables();
    var button_copiar = (array_accesos.find(element => element === 37) ? vardataTables[2][0] : []),
        button_excel = (array_accesos.find(element => element === 39) ? vardataTables[2][1] : []),
        button_pdf = (array_accesos.find(element => element === 38) ? vardataTables[2][2] : []),
        button_imprimir = (array_accesos.find(element => element === 40) ? vardataTables[2][3] : []);

    let botones = [];
    botones.push({
        text: ' Exportar Excel',
        action: function () {
            exportarProductos();
        }, className: 'btn-success btnExportarProductos'
    });

    $('#listaProductoCatalogo').dataTable({
        'dom': vardataTables[1],
        // 'buttons': [button_copiar, button_excel, button_pdf, button_imprimir],
        'language': vardataTables[0],
        buttons: botones,
        'ajax': 'listar_productos',
        'columns': [
            { 'data': 'id_producto' },
            { 'data': 'part_number' },
            {
                data: 'codigo',
                'render': function (data, type, row) {
                    return `<a href="#" class="verProducto" data-id="${row['id_producto']}" >${row['codigo']}</a>`
                }
            },
            { 'data': 'cod_softlink' },
            { 'data': 'descripcion' },
            { 'data': 'notas' },
            { 'data': 'simbolo' },
            { 'data': 'series' },
            { 'data': 'abreviatura' },
            { 'data': 'marca' },
            { 'data': 'subcategoria' },
            { 'data': 'categoria' },
            { 'data': 'clasificacion' },
            { 'data': 'fecha_registro' },
            { 'data': 'nombre_corto' },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
    });

    $("#listaProductoCatalogo tbody").on("click", "a.verProducto", function (e) {
        $(e.preventDefault());
        var id_producto = $(this).data("id");
        localStorage.setItem("id_producto", id_producto);
        var win = window.open("/almacen/catalogos/productos/index", '_blank');
        win.focus();
    });

    vista_extendida();
});
function exportarProductos() {
    $('#formProductosExcel').trigger('submit');
}