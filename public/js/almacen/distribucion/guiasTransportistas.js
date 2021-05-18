$(document).ready(function(){
    listarGuiasTransportistas();
    vista_extendida();
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
                return (row['orden_am'] !== null ? row['orden_am']+`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                    <span class="label label-success">Ver O.E.</span></a>
                <a href="${row['url_oc_fisica']}">
                    <span class="label label-warning">Ver O.F.</span></a>` : '');
                }
            },
            {'render': function (data, type, row){
                return ('<label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento('+row['id_requerimiento']+')">'+row['cod_req']+'</label>'+
                    ' <strong>'+row['sede_descripcion_req']+'</strong>'+(row['tiene_transformacion'] ? '<br><i class="fas fa-random red" title="Tiene Transformación"></i>' : ''));
                }
            },
            // {'data': 'codigo'},
            {'render': 
                function (data, type, row){
                    return row['id_od_grupo']!==null ? ('<label class="lbl-codigo" title="Abrir Despacho" onClick="openDespacho('+row['id_od_grupo']+')">'+row['codigo']+'</label>'):row['codigo'];
                }
            },
            {'render': function (data, type, row){
                    return (row['fecha_despacho']!==null ? formatDate(row['fecha_despacho']):'');
                }
            },
            {'render': function (data, type, row){
                    return (row['fecha_entrega']!==null ? formatDate(row['fecha_entrega']):'');
                }
            },
            {'data': 'nombre'},
            {'render': function (data, type, row){
                    return (row['serie']!==null ? ('GT-'+row['serie']+'-'+row['numero']) : '');
                }
            },
            // {'data': 'razon_social'},
            {'render': function (data, type, row){
                    return (row['serie']!==null ? (row['razon_social']!==null ? row['razon_social'] : 'Movilidad Propia') : '');
                }
            },
            {'render': function (data, type, row){
                    return (row['fecha_transportista']!==null ? formatDate(row['fecha_transportista']):'');
                }
            },
            {'data': 'codigo_envio'},
            {'render': function (data, type, row){
                    return (row['importe_flete']!==null ? ('S/'+formatDecimal(row['importe_flete'])) : '');
                }
            },
            {'render': function (data, type, row){
                    return row['extras']>0 ? ('S/'+formatDecimal(row['extras'])) : '';
                }
            },
            {'render': function (data, type, row){
                    return (row['credito'] ? '<span class="label label-danger">Si</span>' : '<span class="label label-primary">No</span>');
                }
            },
            {'render': function (data, type, row){
                return `<span class="label label-${(row['estado']==1||row['estado']==9||row['estado']==10)?'default':
                ((row['estado']>=2&&row['estado']<=6) ? 'info' : 
                (row['estado']==7?'danger':
                (row['estado']==8?'success':'warning')))}">${row['estado_doc']}</span>`
                }
            },
            {'render': function (data, type, row){
                    return (row['plazo_excedido']!==undefined && row['plazo_excedido']!==null) ? (!row['plazo_excedido'] ? '<span class="label label-success">Si</span>' : '<span class="label label-danger">No</span>'):'';
                }
            },
            {'render': function (data, type, row){
                    return (row['observacion']!==undefined && row['observacion']!==null) ? row['observacion']:'';
                }
            }
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                    return '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_requerimiento']+'">'+
                    '<i class="fas fa-chevron-down"></i></button>';
                }, targets: 17
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

function openDespacho(id_od_grupo){
    var id = encode5t(id_od_grupo);
    window.open('imprimir_despacho/'+id);
}

function abrir_requerimiento(id_requerimiento){
    // Abrir nuevo tab
    localStorage.setItem("id_requerimiento",id_requerimiento);
    let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}