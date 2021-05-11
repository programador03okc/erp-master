
function listarOrdenesDespacho(){
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
}

function abrir_requerimiento(id_requerimiento){
    localStorage.setItem("id_requerimiento",id_requerimiento);
    let url ="/logistica/gestion-logistica/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    win.focus();
}