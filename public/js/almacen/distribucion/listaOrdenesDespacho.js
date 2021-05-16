var table;

function listarRequerimientosPendientes(){
    var vardataTables = funcDatatables();
    table = $('#requerimientosEnProceso').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarRequerimientosEnProceso',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_requerimiento'},
            {'render': function (data, type, row){
                return (row['orden_am'] !== null ? row['orden_am']+`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                <span class="label label-success">Ver O.E.</span></a>
                <a href="${row['url_oc_fisica']}">
                <span class="label label-warning">Ver O.F.</span></a>` : '');
                }
            },
            {'data': 'codigo_oportunidad', 'name': 'oportunidades.codigo_oportunidad'},
            {'render': function (data, type, row){
                    return formatNumber.decimal(row['monto_total'],'S/',2);
                }
            },
            {'data': 'nombre', 'name': 'entidades.nombre'},
            {'render': function (data, type, row){
                return (row['fecha_entrega'] !== null ? formatDate(row['fecha_entrega']) : '');
                }
            },
            // {'data': 'codigo'},
            {'render': function (data, type, row){
                    return ('<label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento('+row['id_requerimiento']+')">'+row['codigo']+'</label>'+
                    ' <strong>'+row['sede_descripcion_req']+'</strong>'+(row['tiene_transformacion'] ? '<br><i class="fas fa-random red"></i>' : ''));
                }
            },
            {'render': function (data, type, row){
                return (row['fecha_requerimiento'] !== null ? formatDate(row['fecha_requerimiento']) : '');
                }
            },
            {'data': 'user_name', 'name': 'users.name'},
            {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
            {'render': function (data, type, row){
                return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                }
            },
            {'render': function (data, type, row){
                return ((row['count_transferencia'] > 0 ? 
                '<button type="button" class="detalle_trans btn btn-success boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle de Transferencias" data-id="'+row['id_requerimiento']+'">'+
                    '<i class="fas fa-exchange-alt"></i></button>' : ''))
                }
            },
            // {'render': function (data, type, row){
            //     return  (row['count_despachos_internos'] > 0 ? ('<span class="label label-danger">'+row['count_despachos_internos']+' </span>') : '')+
            //             (row['codigo_od'] !== null ? ('<span class="label label-primary">'+row['codigo_od']+'</span>') : '');
            //     }
            // },
            {'render': function (data, type, row){
                    if (row['estado'] == 17){
                        return 'Pendiente de que <strong>Almacén</strong> recepcione la Transferencia';
                    } 
                    else if (row['estado'] == 19 && row['count_transferencia'] > 0 && 
                             row['count_transferencia'] !== row['count_transferencia_recibida']){
                        return 'Pendiente de que <strong>Almacén</strong> envíe la Transferencia';
                    }
                    else if (row['estado'] == 19 && row['id_od'] == null && 
                             row['count_transferencia'] == row['count_transferencia_recibida']){
                        return 'Pendiente de que <strong>Distribución</strong> genere la OD';
                    }
                    else if (row['estado'] == 19 && row['id_od'] !== null){
                        return 'Pendiente de que <strong>Almacén</strong> genere la Salida';
                    }
                    else if (row['id_tipo_requerimiento'] !== 1 && row['estado'] == 19 && row['id_od'] == null){
                        return 'Pendiente de que <strong>Distribución</strong> genere la OD';
                    }
                    else if (row['estado'] == 22){
                        return 'Pendiente de que <strong>Customización</strong> realice la transformación';
                    }
                    else if (row['estado'] == 10){
                        return 'Pendiente de que <strong>Distribución</strong> realice el Despacho Externo';
                    }
                    else if (row['estado'] == 27){
                        return 'Pendiente de que <strong>Almacén</strong> complete los ingresos';
                    }
                    else if (row['estado'] == 28){
                        return 'Pendiente de que <strong>Distribución</strong> genere la Orden de Despacho';
                    }
                    else if (row['estado'] == 29){
                        return 'Pendiente de que <strong>Almacén</strong> genere la Salida';
                    }
                }
            }
        ],
        'order': [[ 5, "asc" ]],
        // "createdRow": function( row, data, dataIndex){
        //     if(data.estado == 28){
        //         $(row).css('background-color', '#FACABF');
        //             // $(row.childNodes[1]).css('font-weight', 'bold');
        //     }else if(data.estado == 27){
        //         $(row).css('background-color', '#FCF699');

        //     }
        // },
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                // if (permiso == '1') {
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_requerimiento']+'">'+
                    '<i class="fas fa-chevron-down"></i></button>'+
                    ((row['estado'] == 19 && row['id_tipo_requerimiento'] == 2 && row['id_od'] == null && row['confirmacion_pago'] == false) ? 
                        '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                        'data-placement="bottom" data-id="'+row['id_requerimiento']+'" data-cod="'+row['codigo']+'" title="Anular Requerimiento" >'+
                        '<i class="fas fa-trash"></i></button>' : '')+
                    ((
                        //     (row['estado'] == 19 && row['id_tipo_requerimiento'] == 1 && row['sede_requerimiento'] == row['sede_orden'] && row['id_od'] == null) || //compra 
                        // (row['estado'] == 19 && row['id_tipo_requerimiento'] == 1 && row['sede_requerimiento'] !== row['sede_orden'] && row['id_transferencia'] !== null && row['id_od'] == null) || //compra con transferencia
                        (row['estado'] == 19 && row['confirmacion_pago'] == true && /*row['id_od'] == null &&*/ row['count_transferencia'] == 0) || //venta directa
                        (row['estado'] == 10) || (row['estado'] == 22) ||
                        (row['estado'] == 28) || (row['estado'] == 27) ||
                        (row['estado'] == 19 && row['id_tipo_requerimiento'] !== 1) ||
                        (row['estado'] == 19 && row['confirmacion_pago'] == true && /*row['id_od'] == null &&*/ row['count_transferencia'] > 0 && row['count_transferencia'] == row['count_transferencia_recibida'])) ? //venta directa con transferencia
                            ('<button type="button" class="despacho btn btn-success boton" data-toggle="tooltip" '+
                            'data-placement="bottom" title="Generar Orden de Despacho" >'+
                            '<i class="fas fa-sign-in-alt"></i></button>') : 
                        ( row['id_od'] !== null && row['estado_od'] == 1) ?
                        `<button type="button" class="adjuntar btn btn-${row['count_despacho_adjuntos']>0 ? 'warning' : 'default' } boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Adjuntar Boleta/Factura" >
                            <i class="fas fa-paperclip"></i></button>
                        <button type="button" class="anular_od btn btn-danger boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Anular Orden Despacho" >
                            <i class="fas fa-trash"></i></button>` : '' )+
                        (row['estado'] == 9 ? 
                        `<button type="button" class="adjuntar btn btn-${row['count_despacho_adjuntos']>0 ? 'warning' : 'default' } boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Adjuntar Boleta/Factura" >
                            <i class="fas fa-paperclip"></i></button>` : '')
                // } else {
                //     return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                //     'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_requerimiento']+'">'+
                //     '<i class="fas fa-chevron-down"></i></button>'
                // }
                }, targets: 13
            }
        ],
    });
   
}

$('#requerimientosEnProceso tbody').on("click","button.detalle_trans", function(){
    var id = $(this).data('id');
    open_detalle_transferencia(id);
});

$('#requerimientosEnProceso tbody').on("click","button.adjuntar", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    $('#modal-despachoAdjuntos').modal({
        show: true
    });
    listarAdjuntos(id);
    $('[name=id_od]').val(id);
    $('[name=codigo_od]').val(cod);
    $('[name=descripcion]').val('');
    $('[name=archivo_adjunto]').val('');
    $('[name=proviene_de]').val('enProceso');
});

$('#requerimientosEnProceso tbody').on("click","button.anular", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    var origen = 'despacho';
    openRequerimientoObs(id, cod, origen);
});

$('#requerimientosEnProceso tbody').on("click","button.despacho", function(){
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    tab_origen = 'enProceso';
    open_despacho_create(data);
});

$('#requerimientosEnProceso tbody').on("click","button.anular_od", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    var msj = confirm('¿Está seguro que desea anular la '+cod+' ?');
    if (msj){
        anularOrdenDespacho(id, 'enProceso');
    }
});

function anularOrdenDespacho(id, proviene){
    $.ajax({
        type: 'GET',
        url: 'anular_orden_despacho/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                if (proviene == 'enProceso'){
                    $('#requerimientosEnProceso').DataTable().ajax.reload();
                }
                // else if (proviene == 'enTransformacion'){
                //     $('#requerimientosEnTransformacion').DataTable().ajax.reload();
                // }
                // actualizaCantidadDespachosTabs();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function open_detalle_transferencia(id){
    $('#modal-detalleTransferencia').modal({
        show: true
    });
    $.ajax({
        type: 'GET',
        url: 'listarDetalleTransferencias/'+id,
        dataType: 'JSON',
        success: function(response){
            $('#detalleTransferencias tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

var iTableCounter=1;
var oInnerTable;

$('#requerimientosEnProceso tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = table.row( tr );
    var id = $(this).data('id');
    
    if ( row.child.isShown() ) {
        //  This row is already open - close it
       row.child.hide();
       tr.removeClass('shown');
    }
    else {
       // Open this row
    //    row.child( format(iTableCounter, id) ).show();
       format(iTableCounter, id, row);
       tr.addClass('shown');
       // try datatable stuff
       oInnerTable = $('#requerimientosEnProceso_' + iTableCounter).dataTable({
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
           columns:[ 
            //   { data:'refCount' },
            //   { data:'section.codeRange.sNumber.sectionNumber' }, 
            //   { data:'section.title' }
            ]
       });
       iTableCounter = iTableCounter + 1;
   }
});

/*function listarOrdenesDespacho(){
    var vardataTables = funcDatatables();
    $('#listaOrdenesDespacho').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarOrdenesDespacho',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_od'},
            {'render': 
                function (data, type, row){
                    if (row['aplica_cambios']){
                        return '<span class="label label-danger">'+row['codigo']+'</span>';
                    } else {
                        return '<span class="label label-primary">'+row['codigo']+'</span>';
                    }
                }
            },
            {'data': 'fecha_despacho'},
            // {'data': 'hora_despacho'},
            {'render': 
                function (data, type, row){
                    if (row['razon_social'] !== null){
                        return row['razon_social'];
                    } else if (row['nombre_persona'] !== null){
                        return row['nombre_persona'];
                    }
                }
            },
            // {'data': 'razon_social', 'name': 'adm_contri.razon_social'},
            // {'data': 'codigo_req', 'name': 'alm_req.codigo'},
            {'render': 
                function (data, type, row){
                    return '<label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento('+row['id_requerimiento']+')">'+row['codigo_req']+'</label>';
                }
            },
            {'data': 'concepto', 'name': 'alm_req.concepto'},
            {'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion'},
            // {'data': 'fecha_despacho'},
            {'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto'},
            {'render': 
                function (data, type, row){
                    return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>';
                }
            }
        ],
        'order': [[ 2, "desc" ],[ 3, "desc" ]],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" >'+
                    '<i class="fas fa-list-ul"></i></button>'
                }, targets: 9
            }
        ],
    });
}*/

function abrir_requerimiento(id_requerimiento){
    localStorage.setItem("id_requerimiento",id_requerimiento);
    let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    win.focus();
}