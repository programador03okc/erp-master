var modalPage='';
$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaUbigeos tbody').on('click', 'tr', function(){
        // console.log($(this));
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaUbigeos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var myId = $(this)[0].firstChild.innerHTML;
        var ubig = $(this)[0].childNodes[2].innerHTML;

        let page = document.getElementsByClassName('page-main')[0].getAttribute('type');

        // console.log(page);
        // console.log(modalPage);
        if(page =='crear-orden-requerimiento'){
            if(modalPage=='modal-proveedor'){
                $('[name=ubigeo]').val(myId);    
                $('[name=name_ubigeo]').val(ubig);   
            }else{
                $('[name=id_ubigeo_destino]').val(myId);    
                $('[name=ubigeo_destino]').val(ubig);
            } 
            
        }else if(modalPage =='modal-seleccionar_crear_proveedor'){
            $('[name=ubigeo_prov]').val(myId);    
            $('[name=name_ubigeo_prov]').val(ubig);  
        }else{
            $('[name=ubigeo]').val(myId);    
            $('[name=name_ubigeo]').val(ubig);    
        }
        modalPage='';
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
