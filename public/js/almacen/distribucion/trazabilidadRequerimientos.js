$(document).ready(function(){
    listarTrazabilidadRequerimientos();
});

var table;

function listarTrazabilidadRequerimientos(){
    var vardataTables = funcDatatables();
    table = $('#listaRequerimientosTrazabilidad').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'serverSide' : true,
        'ajax': {
            url: 'listarRequerimientosTrazabilidad',
            type: 'POST'
        },
        'columns': [
            {'data': 'id_requerimiento'},
            {'render': function (data, type, row){
                return (row['codigo'] !== null ? 
                        ('<label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento('+row['id_requerimiento']+')">'+row['codigo']+'</label>')
                        : '');
                }
            },
            // {'data': 'tipo_req', 'name': 'alm_tp_req.descripcion'},
            {'render': function (data, type, row){
                return (row['orden_am'] !== null ? (`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row['id_oc_propia']}&ImprimirCompleto=1">
                    <span class="label label-success">Ver O.E.</span></a>
                    <a href="${row['url_oc_fisica']}">
                    <span class="label label-warning">Ver O.F.</span></a> `+row['orden_am']) : row['concepto']);
                }
            },
            // {'data': 'concepto'},
            {'data': 'sede_descripcion_req', 'name': 'sede_req.descripcion'},
            // {'render': function (data, type, row){
            //     var tipo = '';
            //     switch (row['tipo_cliente']){
            //         case 1 : tipo ='Persona Natural'; break;
            //         case 2 : tipo ='Persona Jurídica'; break;
            //         case 3 : tipo ='Uso Almacén'; break;
            //         case 4 : tipo ='Uso Administrativo'; break;
            //         default: break; 
            //     }
            //     return (tipo);
            //     }
            // },
            {'render': function (data, type, row){
                var cliente = '';
                switch (row['tipo_cliente']){
                    case 1 : cliente = (row['nombre_persona'] !== null ? row['nombre_persona'] : ''); break;
                    case 2 : cliente = (row['cliente_razon_social'] !== null ? row['cliente_razon_social'] : ''); break;
                    case 3 : cliente = (row['almacen_descripcion'] !== null ? row['almacen_descripcion'] : ''); break;
                    case 4 : cliente = 'Uso Administrativo'; break;
                    default: break; 
                }
                return (cliente);
                }
            },
            {'render': function (data, type, row){
                return formatDate(row['fecha_requerimiento']);
                }
            },
            {'render': function (data, type, row){
                return (row['ubigeo_descripcion'] !== null ? row['ubigeo_descripcion'] : '');
                }
            },
            {'data': 'direccion_entrega'},
            // {'data': 'grupo', 'name': 'adm_grupo.descripcion'},
            // {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
            {'render': function (data, type, row){
                if (row['name']!==null)
                    return row['name'];
                else
                    return row['responsable'];
                }
            },
            {'render': function (data, type, row){
                return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                }
            },
            // {'render': function (data, type, row){
            //     return (row['codigo_orden'] !== null ? row['codigo_orden'] : '')
            //     }
            // },
            // {'render': function (data, type, row){
            //     return (row['sede_descripcion_orden'] !== null ? row['sede_descripcion_orden'] : '')
            //     }
            // }, 
            // {'render': function (data, type, row){
            //     return (row['codigo_transferencia'] !== null ? row['codigo_transferencia'] : '')
            //     }
            // },
            {'render': function (data, type, row){
                return (row['codigo_od'] !== null ? row['codigo_od'] : '')
                }
            },
            {'data': 'guia_transportista'},
            {'render': function (data, type, row){
                return (row['importe_flete'] !== null ? 'S/ '+row['importe_flete'] : '')
                }
            },
            // {'data': 'importe_flete'}
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                return '<button type="button" class="ver btn btn-info boton" data-toggle="tooltip" data-placement="bottom" '+
                    'data-id="'+row['id_requerimiento']+'" title="Ver Trazabilidad" >'+
                    '<i class="fas fa-search"></i></button>'+
                // '<button type="button" class="detalle btn btn-primary boton " data-toggle="tooltip" '+
                //     'data-placement="bottom" title="Ver Detalle" >'+
                //     '<i class="fas fa-list-ul"></i></button>'+
                '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" data-id="'+row['id_requerimiento']+'">'+
                    '<i class="fas fa-chevron-down"></i></button>'+
                (row['id_od'] !== null ? 
                `<button type="button" class="adjuntar btn btn-warning boton" data-toggle="tooltip" 
                        data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Adjuntar Boleta/Factura" >
                        <i class="fas fa-paperclip"></i></button>`: '')+
                (row['id_od_grupo'] !== null ? `<button type="button" class="imprimir btn btn-success boton" data-toggle="tooltip" 
                    data-placement="bottom" data-id-grupo="${row['id_od_grupo']}" title="Ver Despacho" >
                    <i class="fas fa-file-alt"></i></button>` : '')
                }, targets: 13
            }
        ],
    });
   
}

$('#listaRequerimientosTrazabilidad tbody').on("click","button.ver", function(){
    var id = $(this).data('id');
    $('#modal-verTrazabilidadRequerimiento').modal({
        show: true
    });
    verTrazabilidadRequerimiento(id);
});

// $('#listaRequerimientosTrazabilidad tbody').on("click","button.detalle", function(){
//     var data = $('#listaRequerimientosTrazabilidad').DataTable().row($(this).parents("tr")).data();
//     console.log(data);
//     open_detalle_requerimiento(data);
// });

$('#listaRequerimientosTrazabilidad tbody').on("click","button.adjuntar", function(){
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    $('#modal-despachoAdjuntos').modal({
        show: true
    });
    listarAdjuntos(id);
    $('[name=id_od]').val(id);
    $('[name=codigo_od]').val(cod);
});

$('#listaRequerimientosTrazabilidad tbody').on("click","button.imprimir", function(){
    var id_od_grupo = $(this).data('idGrupo');
    var id = encode5t(id_od_grupo);
    console.log(id_od_grupo);
    window.open('imprimir_despacho/'+id);
});

function verTrazabilidadRequerimiento(id_requerimiento){
    $.ajax({
        type: 'GET',
        url: 'verTrazabilidadRequerimiento/'+id_requerimiento,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response.forEach(element => {
                html += '<tr>'+
                    '<td>'+i+'</td>'+
                    '<td>'+element.accion+'</td>'+
                    '<td>'+element.descripcion+'</td>'+
                    '<td>'+element.nombre_corto+'</td>'+
                    '<td>'+element.fecha_registro+'</td>'+
                    '</tr>';
                    i++;
            });
            $('#listaAccionesRequerimiento tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}

var iTableCounter=1;
var oInnerTable;

$('#listaRequerimientosTrazabilidad tbody').on('click', 'td button.detalle', function () {
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
       oInnerTable = $('#listaRequerimientosTrazabilidad_' + iTableCounter).dataTable({
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

function abrir_requerimiento(id_requerimiento){
    // Abrir nuevo tab
    localStorage.setItem("id_requerimiento",id_requerimiento);
    let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}