class RequerimientoPago
{
    constructor(permisoConfirmarDenegarPago)
    {
        this.permisoConfirmarDenegarPago = permisoConfirmarDenegarPago;
        this.listarRequerimientos();
    }

    listarRequerimientos() {
        const permisoConfirmarDenegarPago=this.permisoConfirmarDenegarPago;
        var vardataTables = funcDatatables();
        $('#listaRequerimientos').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'destroy' : true,
            'serverSide' : true,
            'ajax': {
                url: 'listarRequerimientosPagos',
                type: 'POST'
            },
            'columns': [
                {'data': 'id_requerimiento'},
                {'data': 'codigo'},
                {'data': 'concepto'},
                {'data': 'fecha_requerimiento'},
                {'data': 'sede_descripcion', 'name': 'sis_sede.descripcion'},
                {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
                // {'data': 'monto'},
                {'render': 
                    function (data, type, row){
                        return (row['simbolo']+(row['monto']!==null ? row['monto'] : 0));
                    }
                },
                {'data': 'fecha_pago', 'name': 'alm_req_pago.fecha_pago'},
                {'data': 'observacion', 'name': 'alm_req_pago.observacion'},
                {'data': 'usuario_pago', 'name': 'registrado_por.nombre_corto'},
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
                        <button type="button" style="padding-left:8px;padding-right:7px;" class="adjunto btn btn-danger boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_requerimiento']}" data-cod="${row['codigo']}" title="Procesar Pago" >
                            <i class="far fa-credit-card"></i></button>
                        <button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                            data-placement="bottom" title="Ver Detalle" >
                            <i class="fas fa-list-ul"></i></button>
                    </div>
                    `;
                    
                    }, targets: 11
                }
            ],
        });
    }

}

$('#listaRequerimientos tbody').on("click","button.adjunto", function(){
    var id_requerimiento = $(this).data('id');
    var codigo = $(this).data('cod');
    $('#modal-procesarPago').modal({
        show: true
    });
    $('[name=id_requerimiento]').val(id_requerimiento);
    $('[name=codigo]').val(codigo);
    $('#submit_procesarPago').removeAttr('disabled');
});

$('#listaRequerimientos tbody').on("click","button.detalle", function(){
    var data = $('#listaRequerimientos').DataTable().row($(this).parents("tr")).data();
    console.log(data.id_requerimiento);
    open_detalle_requerimiento(data);
});

$("#form-procesarPago").on("submit", function(e){
    e.preventDefault();
    $('#submit_procesarPago').attr('disabled','true');
    procesarPago();
});

function procesarPago(){
    var formData = new FormData($('#form-procesarPago')[0]);
    $.ajax({
        type: 'POST',
        url: 'procesarPago',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#modal-procesarPago').modal('hide');
            $('#listaRequerimientos').DataTable().ajax.reload();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$('#listaRequerimientos tbody').on("click","button.detalle", function(){
    var data = $('#requerimientosConfirmados').DataTable().row($(this).parents("tr")).data();
    console.log(data.id_requerimiento);
    open_detalle_requerimiento(data);
});
