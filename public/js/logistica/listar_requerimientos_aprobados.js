function listarRequerimientosAprobados() {
    
    var vardataTables = funcDatatables();
    $('#ListaRequerimientosAprobados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarRequerimientosAprobados',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'tipo_req'},
            {'data': 'codigo'},
            {'data': 'sede_descripcion', 'name': 'sis_sede.descripcion'},
            {'data': 'fecha_requerimiento'},
            {'data': 'concepto'},
            {'data': 'observacion'},
            {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
            {'render': function (data, type, row){
                return (row['simbolo']!==null?row['simbolo']:'')+(row['monto']!==null?row['monto']:0);
                }
            },
            {'render': function (data, type, row){
                return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                }
            }
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                return `
                    <div>
                    <button type="button" style="padding-left:8px;padding-right:7px;" class="pago btn btn-warning boton" data-toggle="tooltip" 
                        data-placement="bottom" data-id="${row['id_requerimiento']}" data-cod="${row['codigo']}" title="Mandar A Pago" >
                        <i class="fas fa-hand-holding-usd"></i></button>
                    <button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                        data-placement="bottom" title="Ver Detalle" >
                        <i class="fas fa-list-ul"></i></button>
                        </div>
                        `;
                
                }, targets: 10
            }
        ],
    });

    $('#ListaRequerimientosAprobados tbody').on("click","button.pago", function(){
        var id = $(this).data('id');
        var cod = $(this).data('cod');
        var rspta = confirm('¿Está seguro que desea mandar a Pago el '+cod+'?');
        
        if (rspta){
            requerimientoAPago(id);
        }
    });
}


function requerimientoAPago(id)
{
    $.ajax({
        type: 'GET',
        url: 'requerimientoAPago/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Se envió correctamente a Pago');
                $('#ListaRequerimientosAprobados').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}