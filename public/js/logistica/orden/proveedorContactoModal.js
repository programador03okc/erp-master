$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaContactosProveedor tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaContactosProveedor').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var nom = $(this)[0].childNodes[1].innerHTML;
        var car = $(this)[0].childNodes[2].innerHTML;
        var tel = $(this)[0].childNodes[3].innerHTML;
        var em = $(this)[0].childNodes[4].innerHTML;
        var dir = $(this)[0].childNodes[5].innerHTML;
  
        $('.modal-footer #select_id_contacto').text(idTr);
        $('.modal-footer #select_nombre_contacto').text(nom);
        $('.modal-footer #select_cargo_contacto').text(car);
        $('.modal-footer #select_telefono_contacto').text(tel);
        $('.modal-footer #select_email_contacto').text(em);
        $('.modal-footer #select_direccion_contacto').text(dir);
      });
});

function contactoModal(){
    var page = $('.page-main').attr('type');
    // console.log(page);
    if(page =='crear-orden-requerimiento'){
        let id_proveedor = document.querySelector("input[name='id_proveedor']").value;
        if(id_proveedor>0){
            $('#modal-contacto-proveedor').modal({
                show: true
            });

            listarContactosProveedor(id_proveedor);

        }else{
            alert("Antes debe seleccione un proveedor");
        }

    }
    
}

function listarContactosProveedor(id_proveedor){
    var vardataTables = funcDatatables();
    $('#listaContactosProveedor').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'ajax': 'lista_contactos_proveedor/'+id_proveedor,
        'columns': [
            {'data': 'id_contacto'},
            {'data': 'nombre_contacto'},
            {'data': 'cargo_contacto'},
            {'data': 'telefono_contacto'},
            {'data': 'email_contacto'},
            {'data': 'direccion_contacto'}
      
      
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function selectContactoProveedor(){
    var id = $('.modal-footer #select_id_contacto').text();
    var nom = $('.modal-footer #select_nombre_contacto').text();
    var car = $('.modal-footer #select_cargo_contacto').text();
    var tel = $('.modal-footer #select_telefono_contacto').text();
    var ema = $('.modal-footer #select_email_contacto').text();
    var dir = $('.modal-footer #select_direccion_contacto').text();
    var page = $('.page-main').attr('type');
    // console.log('page: '+page);
    
    var page = $('.page-main').attr('type');
    // console.log(page);
    if(page =='crear-orden-requerimiento'){

        $('[name=id_contacto_proveedor]').val(id);
        $('[name=contacto_proveedor_nombre]').val(nom+' - '+ car);
        $('[name=contacto_proveedor_telefono]').val(tel);   
    }

    else {
        
        $('[name=id_contacto_proveedor]').val(id);
        $('[name=contacto_proveedor_nombre]').val(nom);
        $('[name=contacto_proveedor_cargo]').val(car);   
        $('[name=contacto_proveedor_telefono]').val(tel);   
        $('[name=contacto_proveedor_email]').val(ema); 
        $('[name=contacto_proveedor_direccion]').val(dir);  
    }
    
    $('#modal-contacto-proveedor').modal('hide');
}