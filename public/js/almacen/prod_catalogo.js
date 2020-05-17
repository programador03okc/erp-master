$(function(){
    var vardataTables = funcDatatables();
    $('#listaProductoCatalogo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        // 'processing': true,
        'ajax': 'listar_productos',
        'columns': [
            {'data': 'id_producto'},
            {'data': 'id_tipo_producto'},
            {'data': 'tipo_descripcion'},
            {'data': 'cod_cat'},
            {'data': 'cat_descripcion'},
            {'data': 'cod_sub_cat'},
            {'data': 'subcat_descripcion'},
            {'data': 'id_clasificacion'},
            {'data': 'clasif_descripcion'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
});