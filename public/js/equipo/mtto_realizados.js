$(function(){
    var fecha = new Date();
    var anio = fecha.getFullYear();
    $('[name=fecha_inicio]').val(anio+'-01-01');
    $('[name=fecha_fin]').val(fecha_actual());

    var id_equipo = $('[name=id_equipo]').val();
    var fini = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();

    listar_mtto_realizados(id_equipo,fini,ffin);
});
function actualizar_reporte(){
    var id_equipo = $('[name=id_equipo]').val();
    var fini = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();
    listar_mtto_realizados(id_equipo,fini,ffin);
}
function listar_mtto_realizados(id_equipo,fini,ffin){
    var vardataTables = funcDatatables();
    $('#listaMttoRealizados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        ajax:{url:"listar_mttos_detalle/"+id_equipo+'/'+fini+'/'+ffin,dataSrc:""},
        'columns': [
            {'data': 'id_mtto'},
            {'render':
                function (data, type, row){
                    return ((row['id_programacion'] !== null) ? '<i class="fas fa-tasks purple"></i>' : '');
                }
            },
            {'data': 'fecha_mtto'},
            {'data': 'razon_social'},
            {'data': 'cod_equipo'},
            {'data': 'des_equipo'},
            {'render':
                function (data, type, row){
                    return ((row['tp_mantenimiento'] == 1) ? 'Mtto. Preventivo' : 'Mtto. Correctivo');
                }
            },
            {'data': 'descripcion'},
            {'data': 'precio_total'},
            {'data': 'resultado'}
            // {'data': 'estado_doc'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    vista_extendida();
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
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}