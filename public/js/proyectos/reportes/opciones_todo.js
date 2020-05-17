$(function(){
    vista_extendida();
    var vardataTables = funcDatatables();
    var tabla = $('#listaOpcionesTodo').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_opciones_todo',
        'columns': [
            {'data': 'id_op_com'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'fecha_emision'},
            {'data': 'cod_presint'},
            {'data': 'cod_propuesta'},
            {'data': 'cod_proyecto'},
            {'data': 'cod_preseje'},
            {'render': 
                function (data, type, row){
                    return (formatNumber.decimal(row['sub_total'],'',-2));
                }, className: 'text-right'
            },
            {'render': 
                function (data, type, row){
                    return (formatNumber.decimal(row['total_igv'],'',-2));
                }, className: 'text-right'
            },
            {'render': 
                function (data, type, row){
                    return (formatNumber.decimal(row['total_presupuestado'],'',-2));
                }, className: 'text-right'
            },
            {'render': 
                function (data, type, row){
                    return (formatNumber.decimal(row['total_req'],'',-2));
                }, className: 'text-right'
            },
            {'render': 
                function (data, type, row){
                    return (formatNumber.decimal(row['total_oc_sin_igv'],'',-2));
                }, className: 'text-right'
            },
            // {'defaultContent': 
            // '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Editar" >'+
            //     '<i class="fas fa-edit"></i></button>'+
            // '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Anular" >'+
            //     '<i class="fas fa-trash"></i></button>'+
            // '<button type="button" class="contrato btn btn-warning boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Ver Contratos" >'+
            //     '<i class="fas fa-file-upload"></i></button>'
            // ''}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaOpcionesTodo tbody',tabla);
});
function botones(tbody, tabla){
    console.log("editar");
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        // open_proyecto_create(data);
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        // anular_proyecto(data.id_proyecto);
    });
    $(tbody).on("click","button.contrato", function(){
        var data = tabla.row($(this).parents("tr")).data();
        // open_proyecto_contrato(data);
    });
}

function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}