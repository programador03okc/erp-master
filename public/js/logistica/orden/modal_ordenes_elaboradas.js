var detalleOrdenList=[];
function ordenesElaboradasModal(){
    $('#modal-ordenes-elaboradas').modal({
        show: true,
        backdrop: 'true'
    });
    listarOrdenesElaboradas();
    
}

function listarOrdenesElaboradas(){
        var vardataTables = funcDatatables();
        var tabla = $('#listaOrdenesElaboradas').DataTable({
            'processing':true,
            'destroy':true,
            'dom': vardataTables[1],
            'buttons': vardataTables[2],
            'language' : vardataTables[0],
            'ajax': 'listar-historial-ordenes-elaboradas',
            // "dataSrc":'',
            'order': [[1,'desc']],
            'scrollX': false,
            'columns': [
                {'data': 'id_orden_compra'},
                {'data': 'fecha'},
                {'data': 'codigo'},
                {'data': 'nro_documento'},
                {'data': 'razon_social'},
                {'data': 'moneda_simbolo'},
                {'data': 'condicion'},
                {'data': 'plazo_entrega'},
                {'data': 'descripcion_sede_empresa'},
                {'data': 'direccion_destino'},
                {'data': 'ubigeo_destino'},
                {'data': 'estado_doc'}
                
            ],
            'columnDefs': [{ className: "text-right", 'aTargets': [0], 'sClass': 'invisible'}]
        });
    
}

$('#listaOrdenesElaboradas tbody').on('click', 'tr', function(){
    if ($(this).hasClass('eventClick')){
        $(this).removeClass('eventClick');
    } else {
        $('#listaOrdenesElaboradas').dataTable().$('tr.eventClick').removeClass('eventClick');
        $(this).addClass('eventClick');
    }
    var idTr = $(this)[0].firstChild.innerHTML;
    $('.modal-footer #id_orden').text(idTr);
    
});

function selectOrden(){
    let idOrdenSelected= document.querySelector("div[id='modal-ordenes-elaboradas'] div[class='modal-footer'] label[id='id_orden']").textContent;
    mostrarOrden(idOrdenSelected);
    changeStateInput('form-crear-orden-requerimiento', true);
    $('#modal-ordenes-elaboradas').modal('hide');
}

function mostrarOrden(id){
    $.ajax({
        type: 'GET',
        url: 'mostrar-orden/'+id,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            loadHeadOrden(response.head);
            ordenView.listar_detalle_orden_requerimiento(response.detalle);
            detalleOrdenList= response.detalle;
            
            
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function fechaHoy(){
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='fecha_emision']").value = now.toISOString().slice(0, -1);
};

function loadHeadOrden(data){
    console.log(data);
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_orden']").value=data.id_orden_compra?data.id_orden_compra:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_tp_documento']").value=data.id_tp_documento?data.id_tp_documento:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_moneda']").value=data.id_moneda?data.id_moneda:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] span[name='codigo_orden_interno']").textContent=data.codigo_orden?data.codigo_orden:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='codigo_orden']").value=data.codigo_softlink?data.codigo_softlink:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='fecha_emision']").value=data.fecha?data.fecha.replace(" ","T"):'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_sede']").value=data.id_sede?data.id_sede:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] img[id='logo_empresa']").setAttribute("src",data.logo_empresa);
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='incluye_igv']").checked=data.incluye_igv;
    
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_proveedor']").value=data.id_proveedor?data.id_proveedor:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contrib']").value=data.id_contribuyente?data.id_contribuyente:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='razon_social']").value=data.razon_social?data.razon_social:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_proveedor']").value=data.direccion_fiscal?data.direccion_fiscal:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor']").value=data.ubigeo?data.ubigeo:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_proveedor_descripcion']").value=data.ubigeo_proveedor?data.ubigeo_proveedor:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_contacto_proveedor']").value=data.id_contacto?data.id_contacto:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_nombre']").value=data.nombre_contacto?data.nombre_contacto:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='contacto_proveedor_telefono']").value=data.telefono_contacto?data.telefono_contacto:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_cuenta_principal_proveedor']").value=data.id_cta_principal?data.id_cta_principal:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nro_cuenta_principal_proveedor']").value=data.nro_cuenta?data.nro_cuenta:'';
    
    document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_condicion']").value=data.id_condicion?data.id_condicion:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='plazo_dias']").value=data.plazo_dias?data.plazo_dias:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='plazo_entrega']").value=data.plazo_entrega?data.plazo_entrega:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='cdc_req']").value=data.codigo_cc?data.codigo_cc:data.codigo_requerimiento;
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ejecutivo_responsable']").value=data.nombre_responsable_cc?data.nombre_responsable_cc:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] select[name='id_tp_doc']").value=data.id_tp_doc?data.id_tp_doc:'';

    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='direccion_destino']").value=data.direccion_destino?data.direccion_destino:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='id_ubigeo_destino']").value=data.ubigeo_destino_id?data.ubigeo_destino_id:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='ubigeo_destino']").value=data.ubigeo_destino?data.ubigeo_destino:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_1']").value=data.personal_autorizado_1?data.personal_autorizado_1:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='personal_autorizado_2']").value=data.personal_autorizado_2?data.personal_autorizado_2:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_1']").value=data.nombre_personal_autorizado_1?data.nombre_personal_autorizado_1:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] input[name='nombre_persona_autorizado_2']").value=data.nombre_personal_autorizado_2?data.nombre_personal_autorizado_2:'';
    document.querySelector("form[id='form-crear-orden-requerimiento'] textarea[name='observacion']").value=data.observacion?data.observacion:'';
}