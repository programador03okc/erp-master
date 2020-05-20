$(function(){
    listar_cus();
});
function listar_cus(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaCu').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy': true,
        'retrieve': true,
        'ajax': 'listar_cus',
        'columns': [
            {'data': 'id_cu'},
            {'data': 'cat_descripcion'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            // {'data': 'fecha_registro'},
            {'render':
                function (data, type, row){
                    return ('<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>');
                }
            },
            {'defaultContent': 
            '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Editar" >'+
                '<i class="fas fa-edit"></i></button>'+
            '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Anular" >'+
                '<i class="fas fa-trash"></i></button>'+
            '<button type="button" class="partidas btn btn-warning boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver partidas enlazadas" >'+
                '<i class="fas fa-file-alt"></i></button>'}
        ],
        'columnDefs': [ { 'aTargets': [0], 'sClass': 'invisible'} ],
        'order': [[ 2, "asc" ]],
        'initComplete': function () {
            $('#listaCu_filter label input').focus();
        }
    });
    botones('#listaCu tbody',tabla);
}

function botones(tbody, tabla){
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        if (data !== undefined){
            edit_acu_create(data);
        }
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        anular_cu(data.id_cu);
    });
    $(tbody).on("click","button.partidas", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        if (data !== undefined){
            open_acuPartidas(data);
        }
    });
}

function anular_cu(ids){
    if (ids !== ''){
        var rspta = confirm("¿Está seguro que desea anular éste Nombre de A.C.U?");
        if (rspta){
            $.ajax({
                type: 'GET',
                url: 'anular_cu/'+ids,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Costo Unitario anulado con éxito');
                        $('#listaCu').DataTable().ajax.reload();
                    } else {
                        alert('El nombre esta relacionado con una Partida!');
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });        
        }
    }
}

function open_acuPartidas(data){
    console.log('open_acuPartidas');
    $('#modal-ver_partida_cu').modal({
        show:true
    });
    $('#nombre_cu').text(data.codigo+' - '+data.descripcion);
    listar_partidas(data.id_cu);
}

function listar_partidas(id_cu){
    $.ajax({
        type: 'GET',
        url: 'listar_partidas_cu/'+id_cu,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response.forEach(element => {
                html+='<tr>'+
                '<td>'+i+'</td>'+
                '<td>'+(element.codigo !== null ? element.codigo : '')+'</td>'+
                '<td>'+(element.descripcion !== null ? element.descripcion : '')+'</td>'+
                '<td>'+element.rendimiento+' '+element.abreviatura+'/jornada</td>'+
                // '<td>'+element.abreviatura+'</td>'+
                '<td>'+(element.cantidad !== null ? element.cantidad : '')+'</td>'+
                '<td>'+element.total+'</td>'+
                '<td>'+(element.importe_parcial !== null ? element.importe_parcial : '')+'</td>'+
                '<td>'+element.fecha_registro+'</td>'+
                '</tr>';
                i++;
            });
            $('#VerPartidaCu tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}