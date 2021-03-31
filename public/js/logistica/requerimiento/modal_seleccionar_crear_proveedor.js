var temp_nombre_proveedor='';
$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaProveedorCollapse tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaProveedorCollapse').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var idCo = $(this)[0].childNodes[1].innerHTML;
        var ruc = $(this)[0].childNodes[2].innerHTML;
        var des = $(this)[0].childNodes[3].innerHTML;

        $('.modal-footer #id_proveedor').text(idTr);
        $('.modal-footer #id_contribuyente').text(idCo);
        $('.modal-footer #ruc').text(ruc);
        $('.modal-footer #select_razon_social').text(des);
    });
});

function listar_proveedores(){
    var vardataTables = funcDatatables();

    $('#listaProveedorCollapse').DataTable({
        'dom': vardataTables[1],
        'buttons': [
        ],
        'language' : vardataTables[0],
        'serverSide' : false,
        'bInfo': false,
        "bLengthChange" : false,
        'paging': true,
        'searching': true,
        'bDestroy' : true,
        'ajax': 'mostrar_proveedores',
        'columns': [
            {'data': 'id_proveedor'},
            {'data': 'id_contribuyente'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'}
        ],
        initComplete: function( settings, json ) {
            // console.log('data cargada');
            if(temp_nombre_proveedor.length >0){
                $('#listaProveedorCollapse_filter input').val(temp_nombre_proveedor);
                this.api().search(temp_nombre_proveedor).draw();
                
            }
        },

        'columnDefs': [{ 'aTargets': [0,1], 'sClass': 'invisible'}],
    });
}

 
function modalSeleccionarCrearProveedor(event,indice){
    $('#modal-seleccionar_crear_proveedor').modal({
        show: true
    });
    document.querySelector("div[id='modal-seleccionar_crear_proveedor'] label[id='indice']").textContent = indice;

    var page = $('.page-main').attr('type');

    if (page == "ordenesDespacho"){
        listar_transportistas();
    } else {
        listar_proveedores();
    }
}

function selectProveedorCollapse(){
    var indiceSelected = $('.modal-footer #indice').text();
    var myId = $('.modal-footer #id_proveedor').text();
    // var idCo = $('.modal-footer #id_contribuyente').text();
    // var des = $('.modal-footer #select_razon_social').text();
    var page = $('.page-main').attr('type');

    if (page == "requerimiento"){

        if(indice >=0){
            $('[name=id_proveedor]').val(myId);
            if(data_item.length >0){
                data_item.forEach((element, index) => {
                    if (index == indiceSelected) {
                        data_item[index].proveedor_id = parseInt(myId);
                        data_item[index].id_almacen_reserva = null;
                        data_item[index].stock_comprometido = null;
                    }
                });

                alert("Item actualizado, Se asignó un proveedor al item");
    
            }else{
                alert("Hubo un problema, no se detecto item's en el detalle requerimiento");
            }
            
        }else{
            alert("No se seleccionó ningún item");
            
        }

    }
    else {
        
        $('[name=id_proveedor]').val(myId);
        $('[name=id_contrib]').val(idCo);
        $('[name=razon_social]').val(des);
    }
    
    $('#modal-seleccionar_crear_proveedor').modal('hide');
}

function ubigeoModalProveedor(){
    $('#modal-ubigeo').modal({
        show: true
    });
    modalPage='modal-seleccionar_crear_proveedor';
    listarUbigeos();
}

function colapsarCrearProveedor(){
    let className= document.querySelector("div[id='modal-seleccionar_crear_proveedor'] i[name='angle']").className;
    if(className == "fa fa-angle-left"){
        document.querySelector("div[id='modal-seleccionar_crear_proveedor'] i[name='angle']").className="fa fa-angle-down";
    }else{
        document.querySelector("div[id='modal-seleccionar_crear_proveedor'] i[name='angle']").className="fa fa-angle-left";

    }

}

function guardarProveedorCollapse(){

    let data ={
        'id_doc_identidad':document.querySelector("div[id='modal-seleccionar_crear_proveedor'] select[name='id_doc_identidad']").value,
        'nro_documento_prov':document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='nro_documento_prov']").value,
        'razon_social':document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='razon_social']").value,
        'telefono':document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='telefono']").value,
        'direccion_fiscal':document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='direccion_fiscal']").value,
        'ubigeo':document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='ubigeo_prov']").value,
        'ubigeo_nombre':document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='name_ubigeo_prov']").value,
        'transportista':false
    };
    
    if(data.nro_documento_prov.length !=11 && data.id_doc_identidad ==2){
        alert('Debe ingresar un RUC con 11 digitos!');

    }else{
        var formData = new FormData($('#form-proveedorCollapse')[0]);


        $.ajax({
            type: 'POST',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'guardar_proveedor',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'JSON',
            success: function(response){ 
                if (response['id_proveedor'] > 0){
                    alert('Proveedor registrado con éxito');
                    temp_nombre_proveedor=response['razon_social'];
                    listar_proveedores();
                    limpiarFormProveedorCollapse();
                } else {
                    alert('Ya se encuentra registrado un Proveedor con dicho Nro de Documento!');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });   
    }

}


function limpiarFormProveedorCollapse(){
    document.querySelector("div[id='modal-seleccionar_crear_proveedor'] select[name='id_doc_identidad']").value='';
    document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='nro_documento_prov']").value='';
    document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='razon_social']").value='';
    document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='telefono']").value='';
    document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='direccion_fiscal']").value='';
    document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='ubigeo_prov']").value='';
    document.querySelector("div[id='modal-seleccionar_crear_proveedor'] input[name='name_ubigeo_prov']").value='';
}