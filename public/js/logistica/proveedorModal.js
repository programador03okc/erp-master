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
        $('.modal-footer #id_proveedor').text(idTr);
        $('.modal-footer #id_contribuyente').text(idCo);
        $('.modal-footer #ruc').text(ruc);
        $('.modal-footer #razon_social').text(des);
    });
});

function listar_proveedores(){
    var vardataTables = funcDatatables();
    $('#listaProveedor').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'ajax': '/mostrar_proveedores',
        'columns': [
            {'data': 'id_proveedor'},
            {'data': 'id_contribuyente'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
        ],
        'columnDefs': [{ 'aTargets': [0,1], 'sClass': 'invisible'}],
    });
}

function proveedorModal(){
    $('#modal-proveedores').modal({
        show: true
    });
    // clearDataTable();
    listar_proveedores();
}

function selectProveedor(){
    var myId = $('.modal-footer #id_proveedor').text();
    var idCo = $('.modal-footer #id_contribuyente').text();
    var des = $('.modal-footer #razon_social').text();
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
    else if (page == "requerimientosPendientes"){
        $('[name=gd_id_proveedor]').val(myId);
        $('[name=gd_razon_social]').val(des);            
    }
    else {
        $('[name=id_proveedor]').val(myId);
        $('[name=id_contrib]').val(idCo);
        $('[name=razon_social]').val(des);    
    }
    
    $('#modal-proveedores').modal('hide');
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