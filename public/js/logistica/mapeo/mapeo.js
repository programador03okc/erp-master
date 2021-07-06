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
                {'data': 'id_requerimiento', 'searchable':false},
                {'data': 'codigo', 'className':'text-center'},
                {'data': 'tipo', 'name': 'alm_tp_req.descripcion', 'className':'text-center'},
                {'data': 'concepto'},
                {'data': 'fecha_entrega', 'className':'text-center'},
                {'data': 'sede_descripcion', 'name': 'sis_sede.descripcion', 'className':'text-center'},
                {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
                {
                    'render': function (data, type, row) {
                        return '<span class="label label-'+(row['count_pendientes']>0?'warning':'success')+'">'+(row['count_pendientes']>0?'No mapeado':'Mapeado')+'</span>'
                    }, 'searchable': false, 'className':'text-center'
                },  
                {'data': 'id_requerimiento', 'searchable':false},
            ],
            'columnDefs': [
                {'aTargets': [0, 'desc'], 'sClass': 'invisible'},
                {'render': function (data, type, row){
                    return `
                    <div class="text-center">
                        <button type="button" class="detalle btn btn-primary btn-xs boton" data-toggle="tooltip" 
                        data-placement="bottom" data-id="${row['id_requerimiento']}" title="Mapear producto" >
                        <i class="fas fa-sign-out-alt"></i></button>
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