$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaCategoria tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaCategoria').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var tdes = $(this)[0].childNodes[2].innerHTML;
        var cdes = $(this)[0].childNodes[3].innerHTML;
        
        $('.modal-footer #id').text(idTr);
        $('.modal-footer #tipo').text(tdes);
        $('.modal-footer #cat_des').text(cdes);
    });
});

function listarCategorias(){
    var vardataTables = funcDatatables();
    $('#listaCategoria').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_categorias',
        'columns': [
            {'data': 'id_categoria'},
            {'data': 'codigo'},
            {'data': 'tipo_descripcion'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            },
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_registro']));
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function categoriaModal(){
    $('#modal-categoria').modal({
        show: true
    });
    clearDataTable();
    listarCategorias();
}

function selectCategoria(){
    var myId = $('.modal-footer #id').text();
    var tdes = $('.modal-footer #tipo').text();
    var cdes = $('.modal-footer #cat_des').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "subcategoria"){
        $('[name=id_categoria]').val(myId);
        $('[name=tipo_descripcion]').val(tdes);
        $('[name=cat_descripcion]').val(cdes);
    }
    else if (page == "categoria"){
        clearForm(form);
        mostrar_categoria(myId);
        changeStateButton('historial');
    }
    $('#modal-categoria').modal('hide');
}