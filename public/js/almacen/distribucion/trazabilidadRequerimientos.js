$(document).ready(function(){
    listarTrazabilidadRequerimientos();
});

function listarTrazabilidadRequerimientos(){
    var vardataTables = funcDatatables();
    $('#listaRequerimientosTrazabilidad').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarRequerimientosTrazabilidad',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'tipo_req', 'name': 'alm_tp_req.descripcion'},
            {'data': 'codigo'},
            {'data': 'sede_descripcion_req', 'name': 'sede_req.descripcion'},
            {'data': 'concepto'},
            {'data': 'fecha_requerimiento'},
            {'render': function (data, type, row){
                return (row['ubigeo_descripcion'] !== null ? row['ubigeo_descripcion'] : '');
                }
            },
            {'data': 'direccion_entrega'},
            // {'data': 'grupo', 'name': 'adm_grupo.descripcion'},
            {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
            // {'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc'},
            {'render': function (data, type, row){
                return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                }
            },
            {'render': function (data, type, row){
                return (row['codigo_orden'] !== null ? row['codigo_orden'] : '')
                }
            },
            {'render': function (data, type, row){
                return (row['sede_descripcion_orden'] !== null ? row['sede_descripcion_orden'] : '')
                }
            },            
            {'render': function (data, type, row){
                return (row['serie'] !== null ? row['serie']+'-'+row['numero'] : '')
                }
            },
            {'render': function (data, type, row){
                return (row['codigo_transferencia'] !== null ? row['codigo_transferencia'] : '')
                }
            },
            {'render': function (data, type, row){
                return (row['codigo_od'] !== null ? row['codigo_od'] : '')
                }
            }
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                return '<button type="button" class="ver btn btn-info boton" data-toggle="tooltip" data-placement="bottom" '+
                'data-id="'+row['id_requerimiento']+'" title="Ver Trazabilidad" >'+
                '<i class="fas fa-search"></i></button>'
                }, targets: 15
            }
        ],
    });
   
}

$('#listaRequerimientosTrazabilidad tbody').on("click","button.ver", function(){
    var id = $(this).data('id');
    $('#modal-verTrazabilidadRequerimiento').modal({
        show: true
    });
    verTrazabilidadRequerimiento(id);
});

function verTrazabilidadRequerimiento(id_requerimiento){
    $.ajax({
        type: 'GET',
        url: 'verTrazabilidadRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response.forEach(element => {
                html += '<tr>'+
                    '<td>'+i+'</td>'+
                    '<td>'+element.accion+'</td>'+
                    '<td>'+element.descripcion+'</td>'+
                    '<td>'+element.nombre_corto+'</td>'+
                    '<td>'+element.fecha_registro+'</td>'+
                    '</tr>';
                    i++;
            });
            $('#listaAccionesRequerimiento tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
