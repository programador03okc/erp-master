$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaUbigeos tbody').on('click', 'tr', function(){
        console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaUbigeos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        var ubig = $(this)[0].childNodes[2].innerHTML;


 
        var page = $('.page-main').attr('type');
        // console.log(page);
        if(page =='crear-orden-requerimiento'){
            $('[name=ubigeo_proveedor]').val(myId);    
            $('[name=ubigeo_proveedor_descripcion]').val(ubig);  
          
        }else{
            $('[name=ubigeo]').val(myId);    
            $('[name=name_ubigeo]').val(ubig);    
        }
        $('#modal-ubigeo').modal('hide');
    });
});

function listarUbigeos(){
    var vardataTables = funcDatatables();
    $('#listaUbigeos').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'listar_ubigeos',
        'columns': [
            {'data': 'id_dis'},
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return (row['descripcion']+' - '+row['provincia']+' - '+row['departamento']);
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function ubigeoModal(){
    $('#modal-ubigeo').modal({
        show: true
    });
    listarUbigeos();
}
