$(document).ready(function(){
    listarGuiasTransportistas();
});

function listarGuiasTransportistas(){
    var vardataTables = funcDatatables();
    $('#listaGuiasTransportistas').DataTable({
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
            // {'render': function (data, type, row){
            //         return `<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" 
            //             data-placement="bottom" title="Ver Detalle" >
            //             <i class="fas fa-list-ul"></i></button>`;
            //     }, targets: 12
            // }
        ],
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