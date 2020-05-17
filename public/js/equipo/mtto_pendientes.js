$(function(){
    listar_mtto_pendientes();
});
function listar_mtto_pendientes(){
    var vardataTables = funcDatatables();
    var actual = fecha_actual();
    console.log(actual);

    $('#listaMttoPendientes').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        ajax:{url:"listar_todas_programaciones",dataSrc:""},
        // ajax:{url:"listar_mtto_pendientes",dataSrc:""},
        'columns': [
            {'data': 'id_programacion'},
            {'render': 
                function (data, type, row){
                    return ('<i class="fas fa-exclamation-triangle '+row['warning']+'"></i>');
                }
            },
            {'data': 'cod_equipo'},
            {'data': 'des_equipo'},
            {'data': 'descripcion'},
            {'data': 'kilometraje_inicial'},
            {'data': 'kilometraje_rango'},
            {'data': 'kilometraje_vencimiento'},
            {'data': 'fecha_inicial'},
            {'render':
                function (data, type, row){
                    return ((row['tiempo'] !== null)?(row['tiempo'] +' '+ row['des_unid_program']):'');
                }
            },
            {'data': 'fecha_vencimiento'},
            {'data': 'estado_doc'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    // botones('#listaMttoPendientes tbody',tabla);
}
// function botones(tbody, tabla){
//     console.log("editar");
//     $(tbody).on("click","button.editar", function(){
//         var data = tabla.row($(this).parents("tr")).data();
//         equipo_create(data);
//     });
//     $(tbody).on("click","button.anular", function(){
//         var data = tabla.row($(this).parents("tr")).data();
//         anular_equipo(data.id_equipo);
//     });
//     $(tbody).on("click","button.seguro", function(){
//         var data = tabla.row($(this).parents("tr")).data();
//         open_seguro(data);
//     });
//     $(tbody).on("click","button.programacion", function(){
//         var data = tabla.row($(this).parents("tr")).data();
//         open_programacion(data);
//     });
// }
