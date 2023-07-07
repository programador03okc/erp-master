 

function listar_proveedores(){
    var vardataTables = funcDatatables();
    $('#listaProveedor').dataTable({
        'dom': vardataTables[1],
        'buttons': [],
        'language' : vardataTables[0],
        'serverSide': true,
        'destroy': true,
        'ajax': {
            'url': 'mostrar-proveedores',
            'type': 'POST',
            'data': {idMoneda:document.querySelector("select[name='id_moneda']").value??''},
            // beforeSend: data => {

            //     $("#listaProveedores").LoadingOverlay("show", {
            //         imageAutoResize: true,
            //         progress: true,
            //         imageColor: "#3c8dbc"
            //     });
            // },

        },
        'columns': [
            { 'data': 'nro_documento', 'name': 'contribuyente.nro_documento', 'className': 'text-center'},
            { 'data': 'razon_social', 'name': 'contribuyente.razon_social', 'className': 'text-left'},
            { 'data': 'id_proveedor', 'name': 'id_proveedor', 'className': 'text-center' ,'searchable': false, 'orderable': false }

        ],
        'columnDefs': [
            {'render':
            function (data, type, row){
                // console.log(row.contribuyente);
                let action = `
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-success btn-sm" name="btnSeleccionarProveedor" title="Seleccionar proveedor" 
                        data-id-proveedor="${row.id_proveedor}"
                        data-id-contribuyente="${row.id_contribuyente}"
                        data-tipo-documento-identidad="${row.contribuyente && row.contribuyente.tipo_documento_identidad !=null && row.contribuyente.tipo_documento_identidad.descripcion !=null ?row.contribuyente.tipo_documento_identidad.descripcion:''}"
                        data-numero-documento="${row.contribuyente && row.contribuyente.nro_documento !=null ?row.contribuyente.nro_documento:''}"
                        data-razon-social="${row.contribuyente && row.contribuyente.razon_social!=null?row.contribuyente.razon_social:''}"
                        data-direccion-fiscal="${row.contribuyente && row.contribuyente.direccion_fiscal!=null?row.contribuyente.direccion_fiscal:''}"
                        data-telefono="${row.contribuyente && row.contribuyente.telefono!=null?row.contribuyente.telefono:''}"
                        data-ubigeo-descripcion="${row.contribuyente && row.contribuyente.ubigeo_completo!=null?row.contribuyente.ubigeo_completo:''}"
                        data-ubigeo="${row.contribuyente && row.contribuyente.ubigeo!=null?row.contribuyente.ubigeo:''}"
                        data-id-moneda-cuenta-principal="${row.cuenta_contribuyente.length>0?row.cuenta_contribuyente[0].id_moneda:''}"
                        data-cantidad-cuentas-con-misma-moneda="${row.cuenta_contribuyente.length}"
                        data-id-cuenta-principal="${row.cuenta_contribuyente.length>0?row.cuenta_contribuyente[0].id_cuenta_contribuyente:''}"
                        data-cuenta-principal="${row.cuenta_contribuyente.length>0?row.cuenta_contribuyente[0].nro_cuenta:''}"
                        onclick="selectProveedor(this);">
                        <i class="fas fa-check"></i>
                        </button>
                    </div>
                    `;
        
                return action;
            },targets: 2 
        }
    ],
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
    // console.log('modal lista proveedor');

    var page = $('.page-main').attr('type');

    if (page == "ordenesDespacho"){
        listar_transportistas();
    } else {
        listar_proveedores();
        if($('.page-main').attr('type')=='lista_requerimiento_pago'){
            document.querySelector("div[id='modal-proveedores'] h3[class='modal-title']")? document.querySelector("div[id='modal-proveedores'] h3[class='modal-title']").textContent= "Lista de Destinatarios":false;
        }else{
            document.querySelector("div[id='modal-proveedores'] h3[class='modal-title']")? document.querySelector("div[id='modal-proveedores'] h3[class='modal-title']").textContent= "Lista de Proveedores":false;

        }
    }
}

function selectProveedor(obj){
    console.log(obj.dataset.cantidadCuentasConMismaMoneda);
    let idProveedor= obj.dataset.idProveedor? obj.dataset.idProveedor: "";
    let idContribuyente= obj.dataset.idContribuyente !=null ?obj.dataset.idContribuyente: "";
    let tipoDocumentoIdentidad= obj.dataset.tipoDocumentoIdentidad !=null ? obj.dataset.tipoDocumentoIdentidad:"";
    let numeroDocumento= obj.dataset.numeroDocumento !=null ? obj.dataset.numeroDocumento:"";
    let razonSocial= obj.dataset.razonSocial !=null ? obj.dataset.razonSocial:"";
    // let ruc= obj.dataset.ruc;
    let direccionFiscal= obj.dataset.direccionFiscal !=null ?obj.dataset.direccionFiscal:"";
    let telefono= obj.dataset.telefono?obj.dataset.telefono:"";
    let ubigeoDescripcion= obj.dataset.ubigeoDescripcion !=null ?obj.dataset.ubigeoDescripcion:"";
    let ubigeo= obj.dataset.ubigeo !=null?obj.dataset.ubigeo:"";
    let cuentaPrincipal= obj.dataset.cuentaPrincipal !=null?obj.dataset.cuentaPrincipal:"";
    let idCuentaPrincipal= obj.dataset.idCuentaPrincipal !=null?obj.dataset.idCuentaPrincipal:"";
    let idMonedaCuentaPrincipal= obj.dataset.idMonedaCuentaPrincipal !=null?obj.dataset.idMonedaCuentaPrincipal:"";

    if(obj.dataset.cantidadCuentasConMismaMoneda >1){
        Lobibox.notify('warning', {
            title:false,
            size: 'mini',
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: `Proveedor seleccionado tiene mas de una cuenta en ${obj.dataset.idMonedaCuentaPrincipal==1?'soles':'moneda extranjera'}.`,
        });

    }

    document.querySelector("input[name='id_proveedor']").value =idProveedor;
    document.querySelector("input[name='id_contrib']").value =idContribuyente;
    if( document.querySelector("input[name='tipo_documento_identidad']")!=null){
        document.querySelector("input[name='tipo_documento_identidad']").value =tipoDocumentoIdentidad;
    }
    if( document.querySelector("input[name='nro_documento']")!=null){
        document.querySelector("input[name='nro_documento']").value =numeroDocumento;
    }
    document.querySelector("input[name='razon_social']").value =razonSocial;
    document.querySelector("input[name='moneda_cuenta_principal_proveedor']").value =idMonedaCuentaPrincipal ==1?'S/':(idMonedaCuentaPrincipal ==2?'$':'');
    document.querySelector("input[name='id_cuenta_principal_proveedor']").value =idCuentaPrincipal;
    document.querySelector("input[name='nro_cuenta_principal_proveedor']").value =cuentaPrincipal;
    
    if(document.querySelector("form[id='form-crear-orden-requerimiento']")){
        document.querySelector("input[name='direccion_proveedor']").value =direccionFiscal;
        document.querySelector("input[name='ubigeo_proveedor']").value =ubigeo;
        document.querySelector("input[name='ubigeo_proveedor_descripcion']").value =ubigeoDescripcion;
        obtenerContactoPorDefecto(idProveedor)
    }

    if (idProveedor == 4) {
        $('[name=compra_local]').iCheck('check');
    } else {
        $('[name=compra_local]').iCheck('uncheck');
    }

    $('#modal-proveedores').modal('hide');
}

function obtenerContactoPorDefecto(idProveedor){
    $.ajax({
        type: 'GET',
        url: 'contacto-proveedor/'+idProveedor,
        dataType: 'JSON',
        success: function(response){
            if(response.length >0){                    
                document.querySelector("input[name='id_contacto_proveedor']").value =response[0].id_datos_contacto;
                document.querySelector("input[name='contacto_proveedor_nombre']").value =response[0].nombre;
                document.querySelector("input[name='contacto_proveedor_telefono']").value =response[0].telefono;


            }else{
                document.querySelector("input[name='id_contacto_proveedor']").value ="";
                document.querySelector("input[name='contacto_proveedor_nombre']").value ="";
                document.querySelector("input[name='contacto_proveedor_telefono']").value ="";


                Lobibox.notify('info', {
                    title:false,
                    size: 'mini',
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: `Proveedor seleccionado sin contactos, para agregarlo ingrese al módulo de proveedores.`,
                });
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){

        Swal.fire(
            '',
            'Hubo un problema al intentar  obtener el contacto del proveedor seleccionado, por favor vuelva a intentarlo',
            'error'
        );
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
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