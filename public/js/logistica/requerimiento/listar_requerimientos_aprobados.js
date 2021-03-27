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
                    <button type="button" style="padding-left:8px;padding-right:7px;" class="adjunto btn btn-warning boton" data-toggle="tooltip" 
                        data-placement="bottom" data-id="${row['id_requerimiento']}" title="Mandar A Pago" >
                        <i class="far fa-credit-card"></i></button>
                    <button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                        data-placement="bottom" title="Ver Detalle" >
                        <i class="fas fa-list-ul"></i></button>
                        </div>
                        `;
                
                }, targets: 10
            }
        ],
    });
}
