$(function(){
    listar_sol_todas();
});
function listar_sol_todas(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaSolTodas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        ajax:{
            url:"listar_todas_solicitudes",
            dataSrc:""
        },
        'columns': [
            {'data': 'id_solicitud'},
            {'data': 'codigo'},
            {'data': 'fecha_solicitud'},
            {'data': 'nombre_trabajador'},
            {'data': 'des_area'},
            {'data': 'des_categoria'},
            {'data': 'equi_asignado'},
            {'data': 'fecha_asignacion'},
            {'data': 'estado_doc'},
            {'defaultContent': 
            '<button type="button" class="flujos btn btn-warning boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Flujos" >'+
                '<i class="fas fa-stream"></i></button>'+
            '<button type="button" class="ver btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver" >'+
                '<i class="fas fa-search-plus"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaSolTodas tbody',tabla);
}
function botones(tbody, tabla){
    console.log("aprobar");
    $(tbody).on("click","button.flujos", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        open_flujos(data.id_doc_aprob, data.id_solicitud);
    });
    $(tbody).on("click","button.ver", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        open_ver(data.id_solicitud);
    });
}
function open_flujos(id_doc_aprob,id_solicitud){
    $('#modal-aprob_flujos').modal({
        show: true
    });
    listar_flujos(id_doc_aprob,id_solicitud);
}
function open_ver(id_solicitud){
    if (id_solicitud !== ''){
        var id = encode5t(id_solicitud);
        window.open('imprimir_solicitud/'+id);
    } else {
        alert('Debe seleccionar una solicitud.');
    }
}
function listar_flujos(id_doc_aprob,id_solicitud){
    $('#listaSolFlujos tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'solicitud_flujos/'+id_doc_aprob+'/'+id_solicitud,
        dataType: 'JSON',
        success: function(response){
            $('#listaSolFlujos tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}