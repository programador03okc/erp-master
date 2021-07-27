class RequerimientoPago
{
    constructor(permisoConfirmarDenegarPago)
    {
        this.permisoConfirmarDenegarPago = permisoConfirmarDenegarPago;
        this.listarComprobantes();
        this.listarOrdenes();
    }

    listarComprobantes(){
        var vardataTables = funcDatatables();
        console.time();
        tableComprobantes = $('#listaComprobantes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'destroy': true,
            'serverSide' : true,
            'ajax': {
                url: 'listarComprobantesPagos',
                type: 'GET',
                // complete: function(){
                //     console.timeEnd();
                // }
            },
            'columns': [
                {'data': 'id_doc_com', 'name': 'doc_com.id_doc_com'},
                {'data': 'tipo_documento', 'name': 'cont_tp_doc.descripcion'},
                {'data': 'serie', 'name': 'doc_com.serie'},
                {'data': 'numero', 'name': 'doc_com.numero'},
                {'data': 'razon_social', 'name': 'adm_contri.razon_social'},
                {'data': 'fecha_emision', 'name': 'doc_com.fecha_emision', 'className': 'text-center'},
                {'data': 'condicion_pago', 'name': 'log_cdn_pago.descripcion', 'className': 'text-center'},
                {'data': 'fecha_vcmto', 'name': 'doc_com.fecha_vcmto', 'className': 'text-center'},
                {'data': 'nro_cuenta', 'name': 'adm_cta_contri.nro_cuenta'},
                // {'render': function (data, type, row){
                //     return (row['fecha_emision']!==null ? formatDate(row['fecha_emision']) : '');
                //     }, 'className': 'text-center'
                // },
                // {'data': 'condicion_pago', 'name': 'log_cdn_pago.descripcion',
                // 'render': function (data, type, row){
                //     return (row['condicion_pago']!==null ? (row['condicion_pago']+' '+row['credito_dias']+' días') : '');
                //     }, 'className': 'text-center'
                // },
                // {'render': function (data, type, row){
                //     return (row['fecha_vcmto']!==null ? formatDate(row['fecha_vcmto']) : '');
                //     }, 'className': 'text-center'
                // },
                {'data': 'simbolo', 'name': 'sis_moneda.simbolo'},
                {'data': 'total_a_pagar_format', 'className': 'text-right'},
                {'render': function (data, type, row){
                        return (row['suma_pagado']!==null ? formatDecimal(row['total_a_pagar'] - row['suma_pagado']) : '0.00');
                    }, 'className': 'text-right'
                },
                {'data': 'span_estado', 'searchable': false},
                // {'render': function (data, type, row){
                //     return (row['total_a_pagar']!==null ? formatDecimal(row['total_a_pagar']) : '');
                //     }, 'className': 'text-right'
                // },
                // {'render': function (data, type, row){
                //     return (row['fecha_pago']!==null ? formatDate(row['fecha_pago']) : '');
                //     }, 'className': 'text-center'
                // },
                // {'data': 'observacion', 'name':'req_pagos.observacion'},
                // {'data': 'usuario_pago', 'name':'registrado_por.nombre_corto'},
                // {'data': 'total_pago', 'name':'req_pagos.total_pago'},
                // {'render': function (data, type, row){
                //         if (row['adjunto']!==null){
                //             return '<a href="/files/tesoreria/pagos/'+row['adjunto']+'" target="_blank">'+row['adjunto']+'</a>';
                //         } else {
                //             return '';
                //         }
                //     }
                // },
                // {'render': function (data, type, row){
                //     return '<span class="label label-'+row['bootstrap_color']+'">'+(row['estado']==9?'Pagada':row['estado_doc'])+'</span>'
                //     }
                // },
                {'render':
                    function (data, type, row){
                    return `<div class="btn-group" role="group">
                        ${row['estado'] == 1 ?
                            `<button type="button" style="padding-left:8px;padding-right:7px;" class="pago btn btn-danger boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_doc_com']}" data-cod="${row['serie']+'-'+row['numero']}" 
                                data-total="${row['total_a_pagar']}" data-pago="${row['suma_pagado']}" 
                                title="Procesar Pago"> <i class="far fa-credit-card"></i> </button>`:''}
                        ${row['suma_pagado'] > 0 ?
                            `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_doc_com']}" title="Ver Detalle" >
                                <i class="fas fa-chevron-down"></i></button>`:''}
                        </div>`;
                    }, 'searchable':false
                },
            ],
            
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
    
    }

    listarOrdenes(){
        var vardataTables = funcDatatables();
        tableOrdenes = $('#listaOrdenes').DataTable({
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'destroy': true,
            'serverSide' : true,
            'ajax': {
                url: 'listarOrdenesCompra',
                type: 'POST'
            },
            'columns': [
                {'data': 'id_orden_compra'},
                {'data': 'sede_descripcion', 'name': 'sis_sede.descripcion'},
                {'data': 'codigo'},
                {'data': 'codigo_softlink'},
                {'data': 'razon_social', 'name': 'adm_contri.razon_social'},
                {'render': function (data, type, row){
                        return (row['fecha']!==null ? formatDate(row['fecha']) : '');
                    }, 'className': 'text-center', 'searchable': false
                },
                {'data': 'condicion_pago', 'name': 'log_cdn_pago.descripcion'},
                {'data': 'nro_cuenta', 'name': 'adm_cta_contri.nro_cuenta'},
                {'data': 'simbolo', 'name': 'sis_moneda.simbolo'},
                {'render': function (data, type, row){
                        return (row['suma_total']!==null ? formatDecimal(row['suma_total']) : '');
                    }, 'className': 'text-right'
                },
                {'render': function (data, type, row){
                        return (row['suma_pagado']!==null ? formatDecimal(row['suma_total'] - row['suma_pagado']) : '0.00');
                    }, 'className': 'text-right'
                },
                // {'render': function (data, type, row){
                //     return (row['fecha_pago']!==null ? formatDate(row['fecha_pago']) : '');
                //     }, 'className': 'text-center'
                // },
                // {'data': 'observacion', 'name': 'req_pagos.observacion', 'searchable': false},
                // {'data': 'usuario_pago', 'name':'registrado_por.nombre_corto', 'searchable': false},
                // {'data': 'total_pago', 'name': 'req_pagos.total_pago', 'searchable': false},
                // {'render': function (data, type, row){
                //         if (row['adjunto']!==null){
                //             return '<a href="/files/tesoreria/pagos/'+row['adjunto']+'" target="_blank">'+row['adjunto']+'</a>';
                //         } else {
                //             return '';
                //         }
                //     }
                // },
                {'render': function (data, type, row){
                    return '<span class="label label-'+(row['estado']==9?'primary':'default')+'">'+row['estado_doc']+'</span>'
                    }
                },
                {'render':
                    function (data, type, row){
                    return `<div class="btn-group" role="group">
                    ${row['estado'] !== 9 ?
                        `<button type="button" style="padding-left:8px;padding-right:7px;" class="pago btn btn-danger boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_orden_compra']}" data-cod="${row['codigo']}" 
                                data-total="${row['suma_total']}" data-pago="${row['suma_pagado']}" 
                                title="Procesar Pago" >
                                <i class="far fa-credit-card"></i></button>`:''}
                    ${row['suma_pagado'] > 0 ?
                        `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                                data-placement="bottom" data-id="${row['id_orden_compra']}" title="Ver Detalle" >
                                <i class="fas fa-chevron-down"></i></button>`:''}
                        </div>`;
                    }, 'searchable': false
                },
            ],
            'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        });
    
    }
}

$('#listaOrdenes tbody').on("click","button.pago", function(){
    var id_oc = $(this).data('id');
    var codigo = $(this).data('cod');
    var total = $(this).data('total');
    var pago = $(this).data('pago');
    
    var total_pago = parseFloat(total) - (pago!==null ? parseFloat(pago) : 0);
    console.log(total_pago);

    $('#modal-procesarPago').modal({
        show: true
    });

    $('[name=id_oc]').val(id_oc);
    $('[name=id_doc_com]').val('');
    $('[name=codigo]').val(codigo);
    $('[name=total_pago]').val(total_pago);
    $('[name=total]').val(total_pago);
    $('[name=observacion]').val('');

    $('#submit_procesarPago').removeAttr('disabled');
});

$('#listaComprobantes tbody').on("click","button.pago", function(){
    var id_doc_com = $(this).data('id');
    var codigo = $(this).data('cod');
    var total = $(this).data('total');
    var pago = $(this).data('pago');

    var total_pago = parseFloat(total) - (pago!==null ? parseFloat(pago) : 0);
    console.log(total_pago);

    $('#modal-procesarPago').modal({
        show: true
    });
    
    $('[name=id_doc_com]').val(id_doc_com);
    $('[name=id_oc]').val('');
    $('[name=codigo]').val(codigo);
    $('[name=total_pago]').val(total_pago);
    $('[name=total]').val(total_pago);
    $('[name=observacion]').val('');

    $('#submit_procesarPago').removeAttr('disabled');
});

$("#form-procesarPago").on("submit", function(e){
    e.preventDefault();
    $('#submit_procesarPago').attr('disabled','true');
    procesarPago();
});

function procesarPago(){
    var formData = new FormData($('#form-procesarPago')[0]);
    var id_oc = $('[name=id_oc]').val();
    var id_doc_com = $('[name=id_doc_com]').val();
    console.log(formData);

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
            
            if (id_oc!==''){
                $('#listaOrdenes').DataTable().ajax.reload();
            } 
            else if (id_doc_com!==''){
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
var tableOrdenes;

$('#listaOrdenes tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableOrdenes.row( tr );
    var id = $(this).data('id');
    
    if ( row.child.isShown() ) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatPagosOrdenes(iTableCounter, id, row);
        tr.addClass('shown');
        oInnerTable = $('#listaOrdenes_' + iTableCounter).dataTable({
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

var iTableCounterComp=1;
var oInnerTableComp;
var tableComprobantes;

$('#listaComprobantes tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = tableComprobantes.row( tr );
    var id = $(this).data('id');
    
    if ( row.child.isShown() ) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        formatPagosComprobante(iTableCounterComp, id, row);
        tr.addClass('shown');
        oInnerTableComp = $('#listaComprobantes_' + iTableCounterComp).dataTable({
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
        iTableCounterComp = iTableCounterComp + 1;
    }
});

function formatPagosOrdenes(table_id, id, row)
{
    $.ajax({
        type: 'GET',
        url: 'pagosOrdenes/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            
            if (response.length > 0){
                response.forEach(element => {
                    html+='<tr id="'+element.id_pago+'">'+
                    '<td style="border: none;">'+i+'</td>'+
                    '<td style="border: none; text-align: center">'+(element.fecha_pago !== null ? formatDate(element.fecha_pago) : '')+'</td>'+
                    '<td style="border: none; text-align: center">'+element.observacion+'</td>'+
                    '<td style="border: none; text-align: right">'+element.simbolo+'</td>'+
                    '<td style="border: none; text-align: right">'+formatDecimal(element.total_pago)+'</td>'+
                    '<td style="border: none; text-align: center"><a href="/files/tesoreria/pagos/'+element.adjunto+'" target="_blank">'+(element.adjunto!==null?element.adjunto:'')+'</a></td>'+
                    '<td style="border: none; text-align: center">'+element.nombre_corto+'</td>'+
                    '<td style="border: none; text-align: center">'+element.fecha_registro+'</td>'+
                    '</tr>';
                    i++;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Fecha Pago</th>
                        <th style="border: none;">Motivo</th>
                        <th style="border: none;">Moneda</th>
                        <th style="border: none;">Total Pago</th>
                        <th style="border: none;">Adjunto</th>
                        <th style="border: none;">Registrado por</th>
                        <th style="border: none;">Fecha Registro</th>
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

function formatPagosComprobante(table_id, id, row)
{
    $.ajax({
        type: 'GET',
        url: 'pagosComprobante/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            
            if (response.length > 0){
                response.forEach(element => {
                    html+='<tr id="'+element.id_pago+'">'+
                    '<td style="border: none;">'+i+'</td>'+
                    '<td style="border: none; text-align: center">'+(element.fecha_pago !== null ? formatDate(element.fecha_pago) : '')+'</td>'+
                    '<td style="border: none; text-align: center">'+element.observacion+'</td>'+
                    '<td style="border: none; text-align: right">'+element.simbolo+'</td>'+
                    '<td style="border: none; text-align: right">'+formatDecimal(element.total_pago)+'</td>'+
                    '<td style="border: none; text-align: center"><a href="/files/tesoreria/pagos/'+element.adjunto+'" target="_blank">'+(element.adjunto!==null?element.adjunto:'')+'</a></td>'+
                    '<td style="border: none; text-align: center">'+element.nombre_corto+'</td>'+
                    '<td style="border: none; text-align: center">'+element.fecha_registro+'</td>'+
                    '</tr>';
                    i++;
                });
                var tabla = `<table class="table table-sm" style="border: none;" 
                id="detalle_${table_id}">
                <thead style="color: black;background-color: #c7cacc;">
                    <tr>
                        <th style="border: none;">#</th>
                        <th style="border: none;">Fecha Pago</th>
                        <th style="border: none;">Motivo</th>
                        <th style="border: none;">Moneda</th>
                        <th style="border: none;">Total Pago</th>
                        <th style="border: none;">Adjunto</th>
                        <th style="border: none;">Registrado por</th>
                        <th style="border: none;">Fecha Registro</th>
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