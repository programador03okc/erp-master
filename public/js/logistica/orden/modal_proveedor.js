$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaProveedor tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaProveedor').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var idCo = $(this)[0].childNodes[1].innerHTML;
        var ruc = $(this)[0].childNodes[2].innerHTML;
        var des = $(this)[0].childNodes[3].innerHTML;
        var dir = $(this)[0].childNodes[4].innerHTML;
        var tel = $(this)[0].childNodes[5].innerHTML;
        var ubi = $(this)[0].childNodes[6].innerHTML;
        var ubi_des = $(this)[0].childNodes[7].innerHTML;

        $('.modal-footer #id_proveedor').text(idTr);
        $('.modal-footer #id_contribuyente').text(idCo);
        $('.modal-footer #ruc').text(ruc);
        $('.modal-footer #select_razon_social').text(des);
        $('.modal-footer #select_direccion_fiscal').text(dir);
        $('.modal-footer #select_telefono').text(tel);
        $('.modal-footer #select_ubigeo').text(ubi);
        $('.modal-footer #select_ubigeo_descripcion').text(ubi_des);
    });
});

function listar_proveedores(){
    var vardataTables = funcDatatables();
    $('#listaProveedor').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'ajax': 'mostrar_proveedores',
        'columns': [
            {'data': 'id_proveedor'},
            {'data': 'id_contribuyente'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'direccion_fiscal'},
            {'data': 'telefono'},
            {'data': 'ubigeo'},
            {'data': 'ubigeo_descripcion'},
        ],
        'columnDefs': [{ 'aTargets': [0,1,4,5,6,7], 'sClass': 'invisible'}],
    });
}

function listar_transportistas(){
    var vardataTables = funcDatatables();
    $('#listaProveedor').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'ajax': 'mostrar_transportistas',
        'columns': [
            {'data': 'id_proveedor'},
            {'data': 'id_contribuyente'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            // {'data': 'telefono'}
        ],
        'columnDefs': [{ 'aTargets': [0,1], 'sClass': 'invisible'}],
    });
}

function proveedorModal(){
    $('#modal-proveedor').modal({
        show: true
    });
    var page = $('.page-main').attr('type');

    if (page == "ordenesDespacho"){
        listar_transportistas();
    } else {
        listar_proveedores();
    }
}

function selectProveedor(){
    var myId = $('.modal-footer #id_proveedor').text();
    var idCo = $('.modal-footer #id_contribuyente').text();
    var des = $('.modal-footer #select_razon_social').text();
    var dir = $('.modal-footer #select_direccion_fiscal').text();
    var tel = $('.modal-footer #select_telefono').text();
    var ubi = $('.modal-footer #select_ubigeo').text();
    var ubi_des = $('.modal-footer #select_ubigeo_descripcion').text();
    var ruc = $('.modal-footer #ruc').text();
    var page = $('.page-main').attr('type');
    // console.log('page: '+page);
    
    if (page == "cotizacion"){
        
        change_proveedor(myId);
        $('[name=id_proveedor]').val(myId);
        $('[name=id_contrib]').val(idCo);
        $('[name=razon_social]').val(des); 
        
        let classModalEditarCotizacion = document.getElementById('modal-editar-cotizacion').getAttribute('class');
        if(classModalEditarCotizacion ==  "modal fade in"){
            onChangeProveedorSave();
        }

    }
    else if (page == "guia_compra"){
        var tab = $("#modal-proveedores .modal-dialog").attr('type');
        // console.log('form:'+tab);
        
        if (tab == "form-general"){
            $('[name=id_proveedor]').val(myId);
            $('[name=id_contrib]').val(idCo);
            $('[name=prov_razon_social]').val(ruc+' - '+des);
        } 
        else if (tab == "form-prorrateo"){
            $('[name=doc_id_proveedor]').val(myId);
            $('[name=doc_id_contrib]').val(idCo);
            $('[name=doc_razon_social]').val(des);
        }
    }
    else if (page == "doc_compra"){
        $('[name=id_proveedor]').val(myId);
        $('[name=id_contrib]').val(idCo);
        $('[name=prov_razon_social]').val(des);
    }
    else if (page == "ordenesDespacho"){

        if (origen_tr == 'grupoDespacho'){
            $('[name=gd_id_proveedor]').val(myId);
            $('[name=gd_razon_social]').val(des);
        } 
        else if (origen_tr == 'transportista'){
            $('[name=tr_id_proveedor]').val(myId);
            $('[name=tr_razon_social]').val(des);
        }
    }
    else {
        
        $('[name=id_proveedor]').val(myId);
        $('[name=id_contrib]').val(idCo);
        $('[name=razon_social]').val(des);   
        $('[name=direccion_proveedor]').val(dir); 
        $('[name=telefono_proveedor]').val(tel);  
        $('[name=ubigeo_proveedor]').val(ubi);  
        $('[name=ubigeo_proveedor_descripcion]').val(ubi_des);  
    }
    
    $('#modal-proveedor').modal('hide');
}


function onChangeProveedorSave(){
    var id_proveedor =  document.querySelector('form[id="form-editar-cotizacion"] input[name="id_proveedor"]').value;
    var id_cotizacion =  document.querySelector('form[id="form-editar-cotizacion"] input[name="id_cotizacion"]').value;
    let payload = {'id_proveedor': id_proveedor, 'id_cotizacion':id_cotizacion};
    console.log('cambiando prove data',payload);
    $.ajax({
        type: 'PUT',
        url: '/actulizar-proveedor-cotizacion',
        dataType: 'JSON',
        data: {data:payload},
        success: function(response){
            console.log(response);
            if(response.status == 'success'){
                mostrar_cotizacion(id_cotizacion);
                alert('Proveedor Actualizado');
                document.querySelector('form[id="form-editar-cotizacion"] select[name="id_contacto"]').parentNode.setAttribute('class','form-group has-warning');
            }else{
                alert(response.message);
            }                        
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}