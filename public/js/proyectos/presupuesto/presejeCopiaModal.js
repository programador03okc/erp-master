$(function(){
    $('#listaPresejeCopia tbody').on("click","tr", function(){
        var id = $(this)[0].firstChild.innerHTML;
        console.log(id);
        generar_partidas_presupuesto(id);
        $('#modal-preseje_copia').modal('hide');
    });
});
function listarPresejeCopia(){
    var id_pres = $('[name=id_presupuesto]').val();
    var vardataTables = funcDatatables();
    $('#listaPresejeCopia').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_presupuestos_copia/'+2+'/'+id_pres,//2 Presupuesto Ejecución
        'columns': [
            {'data': 'id_presupuesto'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'simbolo'},
            {'render':
                function (data, type, row){
                    return (formatNumber.decimal(row['total_presupuestado'],'',-2));
                }, 'class':'right'
            },
            // {'data': 'total_presupuestado'},
            {'data': 'moneda'}
        ],
        'columnDefs': [{ 'aTargets': [0,6], 'sClass': 'invisible'}],
    });
}
function presejeCopiaModal(){
    var id = $('[name=id_presupuesto]').val();
    if (id !== '' && id !== null){
        $('#modal-preseje_copia').modal({
            show: true
        });
        clearDataTable();
        listarPresejeCopia();
    } else {
        alert('Debe seleccionar un Presupuesto');
    }
    
}
function generar_partidas_presupuesto(id){
    var id_pres_actual = $('[name=id_presupuesto]').val();
    $.ajax({
        type: 'GET',
        url: 'generar_partidas_presupuesto/'+id+'/'+id_pres_actual,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Partidas copiadas con éxito!');
                mostrar_preseje(id_pres_actual);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}