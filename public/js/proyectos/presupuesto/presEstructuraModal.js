$(function(){
    $('#listaPresEstructura tbody').on("click","tr", function(){
        var id = $(this)[0].firstChild.innerHTML;
        console.log('id:'+id);
        mostrar_presEstructura(id);
    });
});

function listarPresEstructura(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPresEstructura').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_pres_estructura',
        'columns': [
            {'data': 'id_presup'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

}
function presEstructuraModal(){
    $('#modal-presEstructura').modal({
        show: true
    });
    listarPresEstructura();
}
function mostrar_presEstructura(id){
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_pres_estructura/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_presup]').val(response.id_presup);
            $('[name=descripcion]').val(response.descripcion);
            $('[name=id_sede]').val(response.id_sede).trigger('change.select2');
            $('[name=id_grupo]').val(response.id_grupo);
            $('[name=fecha_emision]').val(response.fecha_emision);
            $('#codigo').text(response.codigo);
            $('#fecha_registro').text(response.fecha_registro);
            $('#responsable').text(response.nombre_corto);
            
            listar_presupuesto(response.id_presup);
            $('#modal-presEstructura').modal('hide');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}