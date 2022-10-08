$(function () {
    var vardataTables = funcDatatables();
    const button_copiar = (array_accesos.find(element => element === 37)?vardataTables[2][0]:[]),
        button_descargar_excel = (array_accesos.find(element => element === 38)?vardataTables[2][1]:[]),
        button_descargar_pdf = (array_accesos.find(element => element === 39)?vardataTables[2][2]:[]),
        button_imprimir = (array_accesos.find(element => element === 40)?vardataTables[2][3]:[]);

    console.log(vardataTables[2]);
    $('#listaProductoCatalogo').dataTable({
        'dom': vardataTables[1],
        'buttons': [button_copiar,button_descargar_excel,button_descargar_pdf,button_imprimir],
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
