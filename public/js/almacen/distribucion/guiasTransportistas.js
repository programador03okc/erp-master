$(document).ready(function(){
    listarGuiasTransportistas();
});

var table;

function listarGuiasTransportistas(){
    var vardataTables = funcDatatables();
    table = $('#listaGuiasTransportistas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        // 'serverSide' : true,
        // "scrollX": true,
        'ajax': {
            url: 'listarGuiasTransportistas',
            type: 'GET'
        },
        'columns': [
            {'data': 'id_od'},
            {'render': function (data, type, row){
                    return 'GT-'+row['serie']+'-'+row['numero'];
                }
            },
            {'data': 'razon_social'},
            {'render': function (data, type, row){
                    return formatDate(row['fecha_transportista']);
                }
            },
            {'data': 'codigo_envio'},
            // {'data': 'importe_flete'},
            {'render': function (data, type, row){
                    return 'S/'+formatDecimal(row['importe_flete']);
                }
            },
            {'render': function (data, type, row){
                return (row['orden_am'] !== null ? row['orden_am']+`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                    <span class="label label-success">Ver O.E.</span></a>
                <a href="${row['url_oc_fisica']}">
                    <span class="label label-warning">Ver O.F.</span></a>` : '');
                }
            },
            {'render': function (data, type, row){
                return (row['cod_req'] !== null ? 
                        ('<label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento('+row['id_requerimiento']+')">'+row['cod_req']+'</label>')
                        : '');
                }
            },
            {'data': 'codigo'},
            {'data': 'nombre'},
            {'render': function (data, type, row){
                return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                }
            }
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_requerimiento']+'">'+
                    '<i class="fas fa-chevron-down"></i></button>';
                }, targets: 11
            }
        ],
    });
}

var iTableCounter=1;
var oInnerTable;

$('#listaGuiasTransportistas tbody').on('click', 'td button.detalle', function () {
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
       oInnerTable = $('#listaGuiasTransportistas_' + iTableCounter).dataTable({
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

function format ( table_id, id, row ) {
    $.ajax({
        type: 'GET',
        url: 'verDetalleRequerimiento/'+id,
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
                    // '<td style="border: none;">'+(element.suma_transferencias!==null?element.suma_transferencias:'')+'</td>'+
                    '<td style="border: none;">'+(element.suma_ingresos!==null?element.suma_ingresos:'0')+'</td>'+
                    '<td style="border: none;">'+(element.stock_comprometido!==null?element.stock_comprometido:'0')+'</td>'+
                    '<td style="border: none;">'+(element.suma_despachos_internos!==null?element.suma_despachos_internos:'0')+'</td>'+
                    '<td style="border: none;">'+(element.suma_despachos_externos!==null?element.suma_despachos_externos:'0')+'</td>'+
                    '<td style="border: none;">'+(element.abreviatura !== null ? element.abreviatura : '')+'</td>'+
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
                        <th style="border: none;">Ingresado</th>
                        <th style="border: none;">Stock Alm.</th>
                        <th style="border: none;">Transformación</th>
                        <th style="border: none;">Despachado</th>
                        <th style="border: none;">Unid.</th>
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
            console.log(tabla);
            row.child( tabla ).show();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function abrir_requerimiento(id_requerimiento){
    // Abrir nuevo tab
    localStorage.setItem("id_requerimiento",id_requerimiento);
    let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}