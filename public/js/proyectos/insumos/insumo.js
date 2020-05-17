$(function(){
    listarInsumos();
});
function listarInsumos(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaInsumo').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        // 'processing': true,
        'ajax': 'listar_insumos',
        'columns': [
            {'data': 'id_insumo'},
            {'data': 'cat_descripcion'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'cod_tp_insumo'},
            {'data': 'abreviatura'},
            {'render': 
                function (data, type, row){
                    if (row['precio_insumo'] !== null){
                        return row['precio_insumo'];
                    } else {
                        return row['precio'];
                    }
                }, className: 'text-right'
            },
            // {'data': 'precio_insumo', className: 'text-right'},
            {'data': 'flete'},
            {'data': 'peso_unitario'},
            {'data': 'iu_descripcion'},
            {'defaultContent': 
            '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Editar" >'+
                '<i class="fas fa-edit"></i></button>'+
            '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Anular" >'+
                '<i class="fas fa-trash"></i></button>'+
            '<button type="button" class="precios btn btn-warning boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver precios" >'+
                '<i class="fas fa-coins"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaInsumo tbody',tabla)
}
function botones(tbody, tabla){
    console.log("editar");
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_insumo_create(data);
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        anular_insumo(data.id_insumo);
    });
    $(tbody).on("click","button.precios", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_precio_modal(data.id_insumo);
    });
}
function anular_insumo(id){
    console.log(id);
    var anula = confirm('¿Esta seguro que desea Anular éste Insumo?');
    
    if (anula){
        $.ajax({
            type: 'GET',
            url: 'anular_insumo/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Insumo anulado con exito');
                    $('#listaInsumo').DataTable().ajax.reload();
                    changeStateButton('anular');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}