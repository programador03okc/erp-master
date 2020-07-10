$(document).ready(function(){
    $('[name=almacen]').val(1);
    var fecha = new Date();
    var yyyy = fecha.getFullYear();
    $('[name=fecha_inicio]').val(yyyy+'-01-01');
    $('[name=fecha_fin]').val(yyyy+'-12-31');
    listarKardexGeneral(1,yyyy+'-01-01',yyyy+'-12-31');
    // $('[name=id_empresa]').multiselect();
});
function actualizarKardex(){
    var alm = $('[name=almacen]').val();
    var fini = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();
    console.log('almacenes'+alm);
    console.log('fechas'+fini+'-'+ffin);
    console.log(alm);
    listarKardexGeneral(alm,fini,ffin);
}
function listarKardexGeneral(almacenes, fini, ffin){
    var vardataTables = funcDatatables();
    var tabla = $('#kardexGeneral').DataTable({
        'destroy':true,
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        "scrollX": true,
        'ajax': {
            url:'kardex_general/'+almacenes+'/'+fini+'/'+ffin,
            dataSrc:''
        },
        'columns': [
            {'data': 'id_mov_alm_det'},
            {'data': 'prod_codigo'},
            {'data': 'prod_part_number'},
            {'data': 'categoria'},
            {'data': 'subcategoria'},
            {'data': 'prod_descripcion'},
            {'data': 'fecha_emision'},
            {'render': 
                function(data, type, row){
                    return ((row['posicion']!==null) ? row['posicion'] : row['almacen_descripcion']);
                    // return row['almacen_descripcion'];
                }
            },
            // {'data': 'posicion'},
            {'data': 'abreviatura'},
            {'render': 
                function(data, type, row){
                    return ((row['tipo']==1 || row['tipo']==0) ? row['cantidad'] : '0');
                }
            },
            {'render': 
                function(data, type, row){
                    return ((row['tipo']==2) ? row['cantidad'] : '0');
                }
            },
            {'data': 'saldo'},
            {'render': 
                function(data, type, row){
                    return ((row['tipo']==1 || row['tipo']==0) ? row['valorizacion'] : '0');
                }
            },
            {'render': 
                function(data, type, row){
                    return ((row['tipo']==2) ? row['valorizacion'] : '0');
                }
            },
            {'data': 'saldo_valor'},
            {'data': 'codigo'},
            {'render': 
                function(data, type, row){
                    return ((row['tipo']==1) ? row['cod_sunat_com'] : row['cod_sunat_ven']);
                }
            },
            {'render': 
                function(data, type, row){
                    return ((row['tipo']==1) ? row['tp_com_descripcion'] : row['tp_ven_descripcion']);
                }
            },
            {'render': 
                function(data, type, row){
                    if (row['id_guia_com'] !== null)
                        return row['guia_com'];
                    else if(row['id_guia_ven'] !== null)
                        return row['guia_ven'];
                    else
                        return '';
                }
            },
            {'render': 
                function(data, type, row){
                    if (row['cod_transformacion'] !== null)
                        return row['cod_transformacion'];
                    else if (row['cod_transferencia'] !== null)
                        return row['cod_transferencia'];
                    // else if (row['id_doc_com'] !== null)
                    //     return row['doc_com'];
                    // else if(row['id_doc_ven'] !== null)
                    //     return row['doc_ven'];
                    else
                        return '';
                }
            },
            {'data': 'orden'},
            {'data': 'req'},
            // {'defaultContent':'<input type="checkbox"/>'},
            // {'data': 'codigo'},
            // {'render':
            //     function (data, type, row){
            //         return (formatDate(row['fecha_requerimiento']));
            //     }
            // },
            // {'data': 'responsable'},
            // {'data': 'des_grupo'},
            // {'data': 'concepto'},
            // {'render':
            //     function (data, type, row){
            //         return ((row['id_proyecto'] !== null) ? row['proy_descripcion'] : row['area_descripcion']);
            //     }
            // },
            // {'defaultContent': 
            //     '<button type="button" class="ver btn btn-primary boton" data-toggle="tooltip" '+
            //         'data-placement="bottom" title="Ver Documento" >'+
            //         '<i class="fas fa-search-plus"></i></button>'+
            //     '<button type="button" class="atender btn btn-success boton" data-toggle="tooltip" '+
            //         'data-placement="bottom" title="Atender" >'+
            //         '<i class="fas fa-share"></i></button>'+
            //     '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
            //         'data-placement="bottom" title="Anular" >'+
            //         '<i class="fas fa-trash"></i></button>'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible', 'width': '1200px'}],
        "order": [[1, "asc"],[3, "asc"]]
        // "bAutoWidth": false
    });
    vista_extendida();
    // ver("#kardexGeneral tbody", tabla);
    // atender("#kardexGeneral tbody", tabla);
    // anular("#kardexGeneral tbody", tabla);
}
function ver(tbody, tabla){
    console.log("ver");
    $(tbody).on("click","button.ver", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
    });
}
function atender(tbody, tabla){
    console.log("atender");
    $(tbody).on("click","button.atender", function(){
        var data = tabla.row($(this).parents("tr")).data();
        req_atencionModal(data);
    });
}
function anular(tbody, tabla){
    console.log("anular");
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
    });
}
function downloadKardexSunat(){
    var alm = $('[name=almacen]').val();
    var fini = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();
    window.open('kardex_sunat/'+alm+'/'+fini+'/'+ffin);
}
function open_filtros(){
    console.log('open_filtros');
    $('#modal-kardex_filtro').modal({
        show:true
    });
}
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}