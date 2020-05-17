$(function(){
    listarSubCategorias();
    /* Seleccionar valor del DataTable */
    $('#listaSubCategoria tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaSubCategoria').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        // var tdes = $(this)[0].childNodes[2].innerHTML;
        // var cdes = $(this)[0].childNodes[3].innerHTML;
        var scdes = $(this)[0].childNodes[2].innerHTML;
        $('.modal-footer #id_subcat').text(idTr);
        // $('.modal-footer #tp_des').text(tdes);
        // $('.modal-footer #cat_des').text(cdes);
        $('.modal-footer #subcat_des').text(scdes);
    });    
});

function listarSubCategorias(){
    var vardataTables = funcDatatables();
    $('#listaSubCategoria').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_subcategorias',
        'columns': [
            {'data': 'id_subcategoria'},
            {'data': 'codigo'},
            // {'data': 'tipo_descripcion'},
            // {'data': 'cat_descripcion'},
            {'data': 'descripcion'}
            // {'render':
            //     function (data, type, row){
            //         return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
            //     }
            // }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function subCategoriaModal(){
    $('#modal-subcategoria').modal({
        show: true
    });
    // $('#listaSubCategoria').DataTable().ajax.reload();
    clearDataTable();
    listarSubCategorias();
}

function selectSubCategoria(){
    var myId = $('.modal-footer #id_subcat').text();
    // var tdes = $('.modal-footer #tp_des').text();
    // var cdes = $('.modal-footer #cat_des').text();
    var scdes = $('.modal-footer #subcat_des').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
    
    if (page == "producto"){
        $('[name=id_subcategoria]').val(myId);
        $('[name=subcat_descripcion]').val(scdes);
        // $('#tipo_descripcion').text(tdes);
        // $('#cat_descripcion').text(cdes);
        $('#subcat_descripcion').text(scdes);
    }
    else if (page == "subcategoria"){
        clearForm(form);
        mostrar_subcategoria(myId);
        changeStateButton('historial');
    }
    $('#modal-subcategoria').modal('hide');

}