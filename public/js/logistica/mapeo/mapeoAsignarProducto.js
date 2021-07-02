function listarProductosCatalogo(){
    var vardataTables = funcDatatables();
    $('#productosCatalogo').dataTable({
        // 'dom': vardataTables[1],
        'language' : vardataTables[0],
        // 'processing': true,
        'bDestroy' : true,
        'ajax': 'mostrar_prods',
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'descripcion'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function listarProductosSugeridos(part_number){
    var vardataTables = funcDatatables();
    $('#productosSugeridos').dataTable({
        // 'dom': vardataTables[1],
        'language' : vardataTables[0],
        // 'processing': true,
        'bDestroy' : true,
        'ajax': 'mostrar_prods_sugeridos/'+part_number,
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'part_number'},
            {'data': 'descripcion'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}