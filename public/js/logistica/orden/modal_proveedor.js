 

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
            {'render':
                function (data, type, row){
                    let action = `
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-success btn-sm" name="btnSeleccionarProveedor" title="Seleccionar proveedor" 
                            data-id-proveedor="${row.id_proveedor}"
                            data-id-contribuyente="${row.id_contribuyente}"
                            data-razon-social="${row.razon_social?row.razon_social:''}"
                            data-ruc="${row.nro_documento?row.nro_documento:''}"
                            data-direccion-fiscal="${row.direccion_fiscal?row.direccion_fiscal:''}"
                            data-telefono="${row.telefono?row.telefono:''}"
                            data-ubigeo-descripcion="${row.ubigeo_descripcion?row.ubigeo_descripcion:''}"
                            data-ubigeo="${row.ubigeo}"
                            onclick="selectProveedor(this);">
                            <i class="fas fa-check"></i>
                            </button>
                        </div>
                        `;
            
                    return action;
                }
            }
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
    $('#modal-proveedores').modal({
        show: true
    });
    var page = $('.page-main').attr('type');

    if (page == "ordenesDespacho"){
        listar_transportistas();
    } else {
        listar_proveedores();
    }
}

function selectProveedor(obj){

    let idProveedor= obj.dataset.idProveedor;
    let idContribuyente= obj.dataset.idContribuyente;
    let razonSocial= obj.dataset.razonSocial? obj.dataset.razonSocial:"";
    // let ruc= obj.dataset.ruc;
    let direccionFiscal= obj.dataset.direccionFiscal?obj.dataset.direccionFiscal:"";
    let telefono= obj.dataset.telefono?obj.dataset.telefono:"";
    let ubigeoDescripcion= obj.dataset.ubigeoDescripcion?obj.dataset.ubigeoDescripcion:"";
    let ubigeo= obj.dataset.ubigeo?obj.dataset.ubigeo:"";

    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_proveedor']").value =idProveedor;
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contrib']").value =idContribuyente;
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='razon_social']").value =razonSocial;
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_proveedor']").value =direccionFiscal;
    // document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='telefono_proveedor']").value =telefono;
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor']").value =ubigeo;
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor_descripcion']").value =ubigeoDescripcion;
 

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