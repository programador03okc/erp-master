$(function(){
    listar_docs();
});
function listar_docs(){
    var vardataTables = funcDatatables();

    $('#listaDocs').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        ajax:{url:"listar_docs",dataSrc:""},
        'columns': [
            {'data': 'id_seguro'},
            {'render': 
                function (data, type, row){
                    return ('<i class="fas fa-exclamation-triangle '+row['warning']+'"></i>');
                }
            },
            {'data': 'cod_equipo'},
            {'data': 'des_equipo'},
            {'data': 'tipo_seguro'},
            {'data': 'nro_poliza'},
            {'data': 'razon_social'},
            {'data': 'fecha_inicio'},
            {'data': 'fecha_fin'},
            {'data': 'importe'},
            {'render':
                function (data, type, row){
                    return ((row['archivo_adjunto'] !== null) ? ('<a href="'+row['file']+'" target="_blank">'+row['archivo_adjunto']+'</a>') : '');
                }
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    // botones('#listaMttoPendientes tbody',tabla);
}
