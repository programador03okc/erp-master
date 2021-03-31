class RequerimientoPago
{
    constructor(permisoConfirmarDenegarPago)
    {
        this.permisoConfirmarDenegarPago = permisoConfirmarDenegarPago;
        this.listarRequerimientos();
        this.listarComprobantes();
    }

    listarRequerimientos() {
        const permisoConfirmarDenegarPago=this.permisoConfirmarDenegarPago;
        var vardataTables = funcDatatables();
        tablePagos = $('#listaRequerimientos').DataTable({
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
                        ${row['estado'] == 8 ?
                        `<button type="button" style="padding-left:8px;padding-right:7px;" class="adjunto btn btn-danger boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_requerimiento']}" data-cod="${row['codigo']}" title="Procesar Pago" >
                            <i class="far fa-credit-card"></i></button>`:''}
                        <button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_requerimiento']}" title="Ver Detalle" >
                            <i class="fas fa-chevron-down"></i></button>
                    </div>
                    `;
                    
                    }, targets: 11
                }
            ],
        });
    }

    listarComprobantes(){
        var vardataTables = funcDatatables();
        $('#listaComprobantes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'destroy': true,
            'serverSide' : true,
            'ajax': {
                url: 'listarComprobantesPagos',
                type: 'POST'
            },
            'columns': [
                {'data': 'id_doc_com'},
                {'data': 'tipo_documento', 'name': 'cont_tp_doc.descripcion'},
                {'data': 'serie'},
                {'data': 'numero'},
                {'data': 'razon_social', 'name': 'adm_contri.razon_social'},
                {'data': 'fecha_emision'},
                {'data': 'condicion_pago', 'name': 'log_cdn_pago.descripcion'},
                {'data': 'fecha_vcmto'},
                {'data': 'simbolo', 'name': 'sis_moneda.simbolo'},
                {'data': 'total_a_pagar'},
                {'data': 'fecha_pago'},
                {'data': 'observacion'},
                {'data': 'usuario_pago', 'name':'registrado_por.nombre_corto'},
                {'render': function (data, type, row){
                    return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                    }
                },
                {'render':
                    function (data, type, row){
                    return `<div class="btn-group" role="group">
                    ${row['estado'] == 8 ?
                            `<button type="button" style="padding-left:8px;padding-right:7px;" class="adjunto btn btn-danger boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_doc_com']}" data-cod="${row['serie']+'-'+row['numero']}" title="Procesar Pago" >
                                <i class="far fa-credit-card"></i></button>`:''}
                            <button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_doc_com']}" title="Ver Detalle" >
                                <i class="fas fa-chevron-down"></i></button>
                        </div>`;
                    }
                },
            ],
            
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
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
    $('[name=id_doc_com]').val('');
    $('[name=codigo]').val(codigo);
    $('#submit_procesarPago').removeAttr('disabled');
});

$('#listaComprobantes tbody').on("click","button.adjunto", function(){
    var id_doc_com = $(this).data('id');
    var codigo = $(this).data('cod');
    $('#modal-procesarPago').modal({
        show: true
    });
    $('[name=id_doc_com]').val(id_doc_com);
    $('[name=id_requerimiento]').val('');
    $('[name=codigo]').val(codigo);
    $('#submit_procesarPago').removeAttr('disabled');
});

$("#form-procesarPago").on("submit", function(e){
    e.preventDefault();
    $('#submit_procesarPago').attr('disabled','true');
    procesarPago();
});

function procesarPago(){
    var formData = new FormData($('#form-procesarPago')[0]);
    var id_requerimiento = $('[name=id_requerimiento]').val();
    var id_doc_com = $('[name=id_doc_com]').val();
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
            
            if (id_requerimiento!==''){
                $('#listaRequerimientos').DataTable().ajax.reload();
            } else if (id_doc_com!==''){
                $('#listaComprobantes').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

var iTableCounter=1;
var oInnerTable;
var tablePagos;

$('#listaRequerimientos tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tablePagos.row( tr );
    var id = $(this).data('id');
    
    if ( row.child.isShown() ) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatDetalle(iTableCounter, id, row);
        tr.addClass('shown');
        oInnerTable = $('#listaRequerimientos_' + iTableCounter).dataTable({
            //    data: sections, 
            autoWidth: true, 
            deferRender: true, 
            info: false, 
            lengthChange: false, 
            ordering: false, 
            paging: false, 
            scrollX: false, 
            scrollY: false, 
            searching: false, 
            columns:[ ]
        });
        iTableCounter = iTableCounter + 1;
    }
});

function formatDetalle(table_id, id, row)
{
    $.ajax({
        type: 'GET',
        url: 'detalleRequerimiento/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            
            if (response.length > 0){
                response.forEach(element => {
                    html+='<tr '+(element.tiene_transformacion ? ' style="background-color: gainsboro;" ' : '')+' id="'+element.id_detalle_requerimiento+'">'+
                    '<td style="border: none;">'+i+'</td>'+
                    '<td style="border: none;">'+(element.producto_codigo !== null ? element.producto_codigo : '')+(element.tiene_transformacion ? ' <span class="badge badge-secondary">Transformado</span> ' : '')+'</td>'+
                    '<td style="border: none;">'+(element.part_number !== null ? element.part_number : '')+'</td>'+
                    '<td style="border: none;">'+(element.producto_descripcion !== null ? element.producto_descripcion : element.descripcion_adicional)+'</td>'+
                    '<td style="border: none;">'+element.cantidad+'</td>'+
                    '<td style="border: none;">'+(element.abreviatura !== null ? element.abreviatura : '')+'</td>'+
                    '<td style="border: none;">'+(element.precio_referencial!==null?element.precio_referencial:'0')+'</td>'+
                    '<td style="border: none;"><span class="label label-'+element.bootstrap_color+'">'+element.estado_doc+'</span></td>'+
                    '</tr>';
                    i++;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Código</th>
                        <th style="border: none;">PartNumber</th>
                        <th style="border: none;">Descripción</th>
                        <th style="border: none;">Cantidad</th>
                        <th style="border: none;">Unid.</th>
                        <th style="border: none;">Precio</th>
                        <th style="border: none;">Estado</th>
                    </tr>
                </thead>
                <tbody>${html}</tbody>
                </table>`;
            }
            else {
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <tbody>
                    <tr><td>No hay registros para mostrar</td></tr>
                </tbody>
                </table>`;
            }
            row.child( tabla ).show();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
