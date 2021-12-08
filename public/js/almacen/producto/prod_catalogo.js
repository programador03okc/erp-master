$(function () {
    var vardataTables = funcDatatables();
    $('#listaProductoCatalogo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        // 'processing': true,
        'ajax': 'listar_productos',
        'columns': [
            { 'data': 'id_producto' },
            { 'data': 'clasif_descripcion' },
            { 'data': 'cod_softlink' },
            { 'data': 'tipo_descripcion' },
            { 'data': 'cod_cat' },
            { 'data': 'cat_descripcion' },
            { 'data': 'cod_sub_cat' },
            { 'data': 'subcat_descripcion' },
            // { 'data': 'codigo' },
            {
                data: 'codigo',
                'render': function (data, type, row) {
                    return `<a href="#" class="verProducto" data-id="${row['id_producto']}" >${row['codigo']}</a>`
                }
            },
            { 'data': 'part_number' },
            { 'data': 'descripcion' },
            { 'data': 'abreviatura' },
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