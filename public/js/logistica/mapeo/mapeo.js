class MapeoProductos
{
    constructor()
    {
        // this.permisoMapeoProductos = permisoMapeoProductos;
        this.listarRequerimientos();
    }

    listarRequerimientos() {

        // const permisoMapeoProductos=this.permisoMapeoProductos;
        var vardataTables = funcDatatables();

        $('#listaRequerimientos').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'destroy' : true,
            'serverSide' : true,
            'ajax': {
                url: 'listarRequerimientos',
                type: 'POST'
            },
            'columns': [
                {'data': 'id_requerimiento'},
                {'data': 'codigo'},
                {'data': 'concepto'},
                {'data': 'fecha_requerimiento'},
                {'data': 'sede_descripcion', 'name': 'sis_sede.descripcion'},
                {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
                {'render': 
                    function (data, type, row){
                        return (row['simbolo']+(row['monto']!==null ? row['monto'] : 0));
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
                        <button type="button" class="detalle btn btn-${row['count_pendientes']>0?'success':'info'} btn-xs boton" data-toggle="tooltip" 
                        data-placement="bottom" data-id="${row['id_requerimiento']}" title="Ver Detalle" >
                        Mapear</button>
                    </div>
                    `;
                    
                    }, targets: 8
                }
            ],
        });
    }

}

$('#listaRequerimientos tbody').on("click","button.detalle", function(){
    var id_requerimiento = $(this).data('id');
    
    $('#modal-mapeoItemsRequerimiento').modal({
        show: true
    });
    $('[name=id_requerimiento]').val(id_requerimiento);
    itemsRequerimiento(id_requerimiento);
    
    $('#submit_mapeoItemsRequerimiento').removeAttr('disabled');
});