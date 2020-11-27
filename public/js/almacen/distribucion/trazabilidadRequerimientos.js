$(document).ready(function(){
    listarTrazabilidadRequerimientos();
});

function listarTrazabilidadRequerimientos(){
    var vardataTables = funcDatatables();
    $('#listaRequerimientosTrazabilidad').DataTable({
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
            {'data': 'tipo_req', 'name': 'alm_tp_req.descripcion'},
            {'data': 'sede_descripcion_req', 'name': 'sede_req.descripcion'},
            // {'data': 'codigo'},
            {'render': function (data, type, row){
                return (row['codigo'] !== null ? 
                        ('<label class="lbl-codigo" title="Abrir Requerimiento" onClick="abrir_requerimiento('+row['id_requerimiento']+')">'+row['codigo']+'</label>')
                        : '');
                }
            },
            {'data': 'concepto'},
            {'render': function (data, type, row){
                var tipo = '';
                switch (row['tipo_cliente']){
                    case 1 : tipo ='Persona Natural'; break;
                    case 2 : tipo ='Persona Jurídica'; break;
                    case 3 : tipo ='Uso Almacén'; break;
                    case 4 : tipo ='Uso Administrativo'; break;
                    default: break; 
                }
                return (tipo);
                }
            },
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
            {'data': 'fecha_requerimiento'},
            {'render': function (data, type, row){
                return (row['ubigeo_descripcion'] !== null ? row['ubigeo_descripcion'] : '');
                }
            },
            {'data': 'direccion_entrega'},
            // {'data': 'grupo', 'name': 'adm_grupo.descripcion'},
            {'data': 'responsable', 'name': 'sis_usua.nombre_corto'},
            // {'data': 'estado_doc', 'name': 'adm_estado_doc.estado_doc'},
            {'render': function (data, type, row){
                return '<span class="label label-'+row['bootstrap_color']+'">'+row['estado_doc']+'</span>'
                }
            },
            {'render': function (data, type, row){
                return (row['codigo_orden'] !== null ? row['codigo_orden'] : '')
                }
            },
            {'render': function (data, type, row){
                return (row['sede_descripcion_orden'] !== null ? row['sede_descripcion_orden'] : '')
                }
            }, 
            {'render': function (data, type, row){
                return (row['codigo_transferencia'] !== null ? row['codigo_transferencia'] : '')
                }
            },
            {'render': function (data, type, row){
                return (row['codigo_od'] !== null ? row['codigo_od'] : '')
                }
            },
            {'data': 'guias_adicionales'},
            {'data': 'importe_flete'}
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                return '<button type="button" class="ver btn btn-info boton" data-toggle="tooltip" data-placement="bottom" '+
                'data-id="'+row['id_requerimiento']+'" title="Ver Trazabilidad" >'+
                '<i class="fas fa-search"></i></button>'+
                '<button type="button" class="detalle btn btn-primary boton " data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Detalle" >'+
                    '<i class="fas fa-list-ul"></i></button>'+
                (row['id_od'] !== null ? 
                `<button type="button" class="adjuntar btn btn-warning boton" data-toggle="tooltip" 
                        data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Adjuntar Boleta/Factura" >
                        <i class="fas fa-paperclip"></i></button>`: '')+
                (row['id_od_grupo'] !== null ? `<button type="button" class="imprimir btn btn-success boton" data-toggle="tooltip" 
                    data-placement="bottom" data-id-grupo="${row['id_od_grupo']}" title="Ver Despacho" >
                    <i class="fas fa-file-alt"></i></button>` : '')
                }, targets: 18
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

$('#listaRequerimientosTrazabilidad tbody').on("click","button.detalle", function(){
    var data = $('#listaRequerimientosTrazabilidad').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    open_detalle_requerimiento(data);
});

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

function abrir_requerimiento(id_requerimiento){
    // Abrir nuevo tab
    localStorage.setItem("id_requerimiento",id_requerimiento);
    let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    // Cambiar el foco al nuevo tab (punto opcional)
    win.focus();
}