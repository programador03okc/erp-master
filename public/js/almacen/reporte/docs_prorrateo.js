$(function(){
    listarSaldos();
});
function listarSaldos(){
    // var almacen = $('[name=almacen]').val();
    // var fecha = $('[name=fecha]').val();
    
    var vardataTables = funcDatatables();
    $('#listaDocsProrrateo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_documentos_prorrateo',
        'destroy':true,
        'columns': [
            {'data': 'id_prorrateo'},
            {'data': 'des_tp_prorrateo'},
            {'render':
                function (data, type, row){
                    return (row['tp_doc_guia']+' '+row['serie_guia']+' - '+row['numero_guia']);
                }
            },
            {'render':
                function (data, type, row){
                    return (row['tp_doc_descripcion']+' '+row['serie'] + ' - '+ row['numero']);
                }
            },
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'fecha_emision'},
            {'data': 'tipo_cambio'},
            {'data': 'simbolo'},
            {'data': 'sub_total'},
            {'data': 'total_descuento'},
            {'data': 'total'},
            {'data': 'total_igv'},
            {'data': 'total_a_pagar'},
            {'data': 'id_doc_com'},
        ],
        'columnDefs': [{ 'aTargets': [0,14], 'sClass': 'invisible'}],
        "order": [[6, "desc"]]
    });
    vista_extendida();
    // tipo_cambio(fecha);
}
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}