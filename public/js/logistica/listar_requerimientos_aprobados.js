function listarRequerimientosAprobados() {
    
    var vardataTables = funcDatatables();
    var tableAprobados = $('#ListaRequerimientosAprobados').DataTable({
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
                        ${row['estado'] == 2 ? 
                        `<button type="button" style="padding-left:8px;padding-right:7px;" class="pago btn btn-warning boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_requerimiento']}" data-cod="${row['codigo']}" title="Mandar A Pago" >
                            <i class="fas fa-hand-holding-usd"></i></button>`:''}

                        <button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
                            data-placement="bottom" data-id="${row['id_requerimiento']}" title="Ver Detalle" >
                            <i class="fas fa-chevron-down"></i></button>
                    </div>`;
                
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

    var iTableCounter=1;
    var oInnerTable;

    $('#ListaRequerimientosAprobados tbody').on('click', 'td button.detalle', function () {
        var tr = $(this).closest('tr');
        var row = tableAprobados.row( tr );
        console.log($(this).data('id'));
        var id = $(this).data('id');
        console.log(id);
        
        if ( row.child.isShown() ) {
        row.child.hide();
        tr.removeClass('shown');
        }
        else {
        formatDetalle(iTableCounter, id, row);
        tr.addClass('shown');
        oInnerTable = $('#ListaRequerimientosAprobados_' + iTableCounter).dataTable({
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

}

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