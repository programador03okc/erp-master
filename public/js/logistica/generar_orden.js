$(function(){

    var idOrden = localStorage.getItem('idOrden');
    if (idOrden != null){    
        $('[name=id_orden_compra]').val(idOrden);
        mostrar_orden(idOrden);
        listar_detalle_orden(idOrden);
        localStorage.removeItem('idOrden');
    }
    $('#listaItems tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaItems').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idItem = $(this)[0].children[0].innerHTML;
        var idProd = $(this)[0].children[1].innerHTML;
        var idServ = $(this)[0].children[2].innerHTML;
        var idEqui = $(this)[0].children[3].innerHTML;
        var codigo = $(this)[0].children[4].innerHTML;
        var descri = $(this)[0].children[5].innerHTML;
        $('.modal-footer #id_item').text(idItem);
        $('.modal-footer #codigo').text(codigo);
        $('.modal-footer #descripcion').text(descri);
        $('.modal-footer #id_producto').text(idProd);
        $('.modal-footer #id_servicio').text(idServ);
        $('.modal-footer #id_equipo').text(idEqui);
    });

    $('#ListaBuenasPro tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#ListaBuenasPro').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        let idValorizacionCotizacion = $(this)[0].firstChild.innerHTML;
        let idCotizacion = $(this)[0].childNodes[1].innerHTML;
        $('.modal-footer #idValorizacionCotizacion').text(idValorizacionCotizacion);
        $('.modal-footer #idCotizacion').text(idCotizacion);
    });
});

function handlechangeCondicion(event){
    let condicion= document.getElementsByName('id_condicion')[0];
    let text_condicion = condicion.options[condicion.selectedIndex].text;
    if(text_condicion == 'CONTADO CA'){
        document.getElementsByName('plazo_dias')[0].value = null;
        document.getElementsByName('plazo_dias')[0].setAttribute('disabled','true');
    }else if(text_condicion =='CREDITO'){
        document.getElementsByName('plazo_dias')[0].removeAttribute('disabled');

    }

}
function nueva_orden(){
    $('[name=razon_social]').val('');
    $('#listaDetalleOrden tbody').html('');
    $('[name=monto_subtotal]').val('0');
    $('[name=igv_porcentaje]').val('0');
    $('[name=monto_igv]').val('0');
    $('[name=monto_total]').val('0');
    $('#codigo').val('');
    $('#fecha').val('');
}
function detalle_cotizacion(id_cotizacion){
    

    $.ajax({
        type: 'GET',
        url: '/detalle_cotizacion/'+id_cotizacion,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            
            $('[name=id_grupo_cotizacion]').val(response.cotizacion.id_grupo_cotizacion);
            $('[name=id_cotizacion]').val(response.cotizacion.id_cotizacion);
            $('[name=id_contrib]').val(response.cotizacion.id_contribuyente);
            $('[name=id_proveedor]').val(response.cotizacion.id_proveedor);
            $('[name=razon_social]').val(response.cotizacion.razon_social);
            $('[name=id_condicion]').val(response.cotizacion.id_condicion_pago);
            $('[name=plazo_dias]').val(response.cotizacion.plazo_dias);

            $('[name=contacto_responsable]').val(response.cotizacion.id_contacto).trigger('change.select2');

            // $('[name=plazo_entrega]').val(response.valorizacion_cotizacion.plazo_entrega);

            if(response.cotizacion.plazo_dias==null){
                document.getElementsByName('plazo_dias')[0].setAttribute("disabled","true");    
            }


            $('[name=id_cta_principal]').html(response.html_cuenta);
            $('[name=id_cta_alternativa]').html(response.html_cuenta);
            $('[name=id_cta_detraccion]').html(response.html_cuenta_detra);

            $('#listaDetalleOrden tbody').html(response['html_item_valorizacion']);
            $('[name=monto_total]').val(formatDecimal(response['sub_total']));
            $('[name=igv_porcentaje]').val(formatDecimal(response['igv']));
            actualiza_totales();

            let sizeBtnEditarDespacho = document.getElementsByName('btnEditarDespacho').length;
            for (i = 0; i < sizeBtnEditarDespacho; i++) { 
                document.getElementsByName('btnEditarDespacho')[i].setAttribute("disabled","true");    
            }

            checkStatusActualizarCodigo();

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function checkStatusActualizarCodigo(){    
            // var btnActualizarcodigo =  document.getElementsByTagName('button')["btnActualizarCodigoItem"];
        var btnActualizarcodigo = document.getElementsByName('btnActualizarCodigoItem');
        let id_oc = document.querySelector("form[id='form-orden'] input[name='id_orden_compra']").value;
        if(btnActualizarcodigo){
            if(id_oc.length > 0 && parseInt(id_oc) > 0){
                for (let i = 0; i < btnActualizarcodigo.length; i++) {
                    btnActualizarcodigo.item(i).removeAttribute('disabled');
                    btnActualizarcodigo.item(i).setAttribute('title','Actualizar Código');
                }

            }else{
                for (let i = 0; i < btnActualizarcodigo.length; i++) {
                    btnActualizarcodigo.item(i).setAttribute('disabled',true);
                    btnActualizarcodigo.item(i).setAttribute('title','Para actualizar, Primero debe generarse la orden!');

                }

            }
 
    }
}


function save_orden_compra(data, action){
    let id_cotizacion =$('[name=id_cotizacion]').val();
    let id_grupo_cotizacion =$('[name=id_grupo_cotizacion]').val();
    let payload ={};
    if (action == 'register'){
        console.log(action);
        
        baseUrl = '/guardar_orden_compra';

    } else if (action == 'edition'){
        baseUrl = '/update_orden_compra';
    }
    var id_val = [];
    var id_item = [];
    var i = 0;
    $('#listaDetalleOrden tbody tr').each(function(e){
        id_val[i] = $(this).find("td input[name=id_valorizacion_cotizacion]").val();
        id_item[i] = $(this).find("td input[name=id_item]").val();
        i++;
    });
    // console.log('id_val'+id_val+' id_item'+id_item);
    payload =data+'&id_val='+id_val+'&id_item='+id_item+'&id_grupo_cotizacion='+id_grupo_cotizacion+'&id_cotizacion='+id_cotizacion;
    let idRequerimiento = document.querySelector("form[id='form-orden'] input[name='id_requerimiento']").value;
    // if(parseInt(idRequerimiento) > 0 ){
    //     // console.log('guardar orden en base a requerimiento');
    //     payload = data+'&detalle_requerimiento='+JSON.stringify(detalleRequerimientoSelected);
    //     baseUrl ='/guardar_orden_por_requerimiento';
        
    // }
    // else{
    //     console.log('el objeto no tiene data: sin propiedad det_req  y/o det_req');
    // }
    // console.log(payload);
    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: payload,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Orden de registrada con éxito');
                changeStateButton('guardar');
                mostrar_orden(response);
                listar_detalle_orden(response);
                $('[name=id_orden_compra]').val(response);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_cuentas_bco(){
    var id_contri = $('[name=id_contrib]').val();
    // console.log('id_contri'+id_contri);
    $.ajax({
        type: 'GET',
        url: '/mostrar_cuentas_bco/'+id_contri,
        dataType: 'JSON',
        success: function(response){
            // console.log('response mostrar_cuentas_bco');
            // console.log(response);
            // console.log(response.length);
            var option = '';
            var detra = '';
            for (var i=0;i<response.length;i++){
                if (response[i].id_tipo_cuenta !== 2){
                    option+='<option value="'+response[i].id_cuenta_contribuyente+'">'+response[i].nro_cuenta+' - '+response[i].banco+'</option>';
                } else {
                    detra+='<option value="'+response[i].id_cuenta_contribuyente+'">'+response[i].nro_cuenta+' - '+response[i].banco+'</option>';
                }
            }
            $('[name=id_cta_principal]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            $('[name=id_cta_alternativa]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            $('[name=id_cta_detraccion]').html('<option value="0" disabled selected>Elija una opción</option>'+detra);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function imprimir_orden(){
    var id_orden = $('[name=id_orden_compra]').val();
    if (id_orden != ''){
        // var id = encode5t(id_orden);
        var id = id_orden;
        window.open('/generar_orden_pdf/'+id);
    } else {
        alert('Debe seleccionar una Orden de Compra!');
    }
}
function mostrar_orden(id_orden){
    $.ajax({
        type: 'GET',
        url: '/mostrar_orden/'+id_orden,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            // console.log('contri'+re  sponse['orden']['id_contribuyente']);
            $('[name=id_orden_compra]').val(response['orden']['id_orden_compra']);
            $('[name=id_proveedor]').val(response['orden']['id_proveedor']);
            $('[name=id_contrib]').val(response['orden']['id_contribuyente']);
            $('#codigo').val(response['orden']['codigo']);
            $('[name=id_tipo_doc]').val(response['orden']['id_tp_documento']).trigger('change.select2');
            $('#fecha').val(response['orden']['fecha']);
            $('[name=razon_social]').val(response['orden']['razon_social']);
            $('[name=id_condicion]').val(response['orden']['id_condicion']);
            $('[name=id_moneda]').val(response['orden']['id_moneda']).trigger('change.select2');
            $('[name=plazo_entrega]').val(response['orden']['plazo_entrega']);
            $('[name=plazo_dias]').val(response['orden']['plazo_dias']);
            $('[name=id_tp_documento]').val(response['orden']['id_tp_documento']).trigger('change.select2');
            $('[name=id_grupo_cotizacion]').val(response['orden']['id_grupo_cotizacion']);
            $('[name=id_cotizacion]').val(response['orden']['id_cotizacion']);
            $('[name=monto_subtotal]').val(response['orden']['monto_subtotal']);
            $('[name=igv_porcentaje]').val(response['orden']['igv_porcentaje']);
            $('[name=monto_igv]').val(response['orden']['monto_igv']);
            $('[name=monto_total]').val(response['orden']['monto_total']);
            $('[name=id_cta_principal]').html('<option value="0" disabled selected>Elija una opción</option>'+response['html']);
            $('[name=id_cta_alternativa]').html('<option value="0" disabled selected>Elija una opción</option>'+response['html']);
            $('[name=id_cta_detraccion]').html('<option value="0" disabled selected>Elija una opción</option>'+response['detra']);
            $('[name=id_cta_principal]').val(response['orden']['id_cta_principal']);
            $('[name=id_cta_alternativa]').val(response['orden']['id_cta_alternativa']);
            $('[name=id_cta_detraccion]').val(response['orden']['id_cta_detraccion']);
            $('#estado label').text(response['orden']['estado_doc']);
            $('[name=cod_estado]').val(response['orden']['estado']);
            $('[name=personal_responsable]').val(response['orden']['personal_responsable']).trigger('change.select2');

            checkStatusActualizarCodigo();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_detalle_orden(id_orden){

    
    $.ajax({
        type: 'GET',
        url: '/listar_detalle_orden/'+id_orden,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            $('#listaDetalleOrden tbody').html(response);
            // $('[name=monto_subtotal]').val(formatDecimal(response['sub_total']));
            // actualiza_totales();
            checkStatusActualizarCodigo();

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

    
}
function anular_orden_compra(ids){
    baseUrl = '/anular_orden_compra/'+ids;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Orden de Compra anulada con éxito');
                changeStateButton('anular');
                $('#estado label').text('Anulado');
                $('[name=cod_estado]').val('2');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function actualiza_totales(){
    // var sub_total = parseFloat($('[name=monto_subtotal]').val());
    // var pigv = parseFloat($('[name=igv_porcentaje]').val());
    // var igv = sub_total * parseFloat(pigv) / 100;
    // $('[name=monto_igv]').val(formatDecimal(igv));
    // var total_a_pagar = sub_total + igv;
    // $('[name=monto_total]').val(formatDecimal(total_a_pagar));

        var pigv = parseFloat($('[name=igv_porcentaje]').val());

        var monto_total = parseFloat($('[name=monto_total]').val());
        var monto_subtotal = monto_total/(1+(pigv/100));
        $('[name=monto_subtotal]').val(formatDecimal(monto_subtotal));
        var monto_igv = monto_subtotal*(pigv/100);
        $('[name=monto_igv]').val(formatDecimal(monto_igv));

    


}

function modalActualizarCodigoItem(id_detalle_orden, id_valorizacion_cotizacion){
// open modal
$('#modal-actualizar-item-sin-codigo').modal({
    show: true
});
// console.log(id_detalle_orden);
// console.log(id_valorizacion_cotizacion);

 $('.modal-footer #id_detalle_orden').text(id_detalle_orden);
 $('.modal-footer #id_val_cot').text(id_valorizacion_cotizacion);

// select item de catalogo
// update detalle_requerimiento
// update orden_det

}

function ingresarCodigoCatalogoItems(){
    $('#modal-catalogo-items').modal({
        show: true,
        backdrop: 'static'
    });
    listarItems();
    
}


function listarItems() {
    var vardataTables = funcDatatables();
    $('#listaItems').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'ajax': '/logistica/mostrar_items',
        'columns': [
            {'data': 'id_item'},
            {'data': 'id_producto'},
            {'data': 'id_servicio'},
            {'data': 'id_equipo'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'unidad_medida_descripcion'},
            {'data': 'stock'}
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},
            { 'aTargets': [1], 'sClass': 'invisible'},
            { 'aTargets': [2], 'sClass': 'invisible'},
            { 'aTargets': [3], 'sClass': 'invisible'}
                    ],
        'order': [
            [2, 'asc']
        ]
    });
}


function selectItem(){
    var page = $('.page-main').attr('type');
    if(page == 'orden'){
        var id_item = $('.modal-footer #id_item').text();
        var codigo = $('.modal-footer #codigo').text();
        var descripcion = $('.modal-footer #descripcion').text();
        // console.log(id_item);
        // console.log(codigo);
        // console.log(descripcion);
        $('[name=codigo_item]').val(codigo);
        $('[name=descripcion_item]').val(descripcion);
        $('#new_id_item').text(id_item);
    
    
        $('#modal-catalogo-items').modal('hide');
    }



}

function actualizarCodigoItem(){

    var ask = confirm('Se actualizará el codigo del ítem?');
    if (ask == true){
        let id_detalle_orden=document.getElementById('id_detalle_orden').innerText;
        let id_valorizacion_cotizacion=document.getElementById('id_val_cot').innerText;
        let codigo_item=document.getElementsByName('codigo_item')[0].value;
        let descripcion_item=document.getElementsByName('descripcion_item')[0].value;
        let id_item=document.getElementById('new_id_item').innerText;
        // console.log(id_detalle_orden);
        // console.log(id_valorizacion_cotizacion);
        // console.log(id_item);
        // console.log(codigo_item);

       let payload = {'id_item':id_item, 'descripcion_item':descripcion_item};
        // console.log(payload);

        if(parseInt(id_item) > 0){
            baseUrl = '/actualizar_item_sin_codigo/'+id_detalle_orden+'/'+id_valorizacion_cotizacion;
            $.ajax({
                type: 'PUT',
                url: baseUrl,
                data:payload,
                dataType: 'JSON',
                success: function(response){
                    // console.log(response);                  
                    if (response.status ==200 ){
                        // console.log("Actualizacion de codigo de item de requerimiento: "+response.update_det_req+", item de orden: "+response.update_det_orden);
                        if(response.id_orden_compra >0){
                            mostrar_orden(response.id_orden_compra);                            
                            listar_detalle_orden(response.id_orden_compra);
                            $('#modal-actualizar-item-sin-codigo').modal('hide');
                            alert('El ítem se actualizo con éxito');
                            
                        }else{
                            alert("error en registro");
                        } 
                    }else if(response.status == 204){
                        alert("Antes de actualizar el código de item, primero debe guardar la Orden");
                    }else if(response.status == 400){
                        alert("hubo un problema al intentar actualizar");
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            alert("debe seleccionar un ítem del catalogo");
            return false;
        }
        
    }else{
        return false;
    }
}

function editarDespacho(e,id_valorizacion_cotizacion){


    $('#modal-despacho').modal({
        show: true,
        backdrop: 'static'
    });
    

    // Get data personal autorizado id, destino

    $.ajax({
        type: 'GET',
        url: '/logistica/cuadro_comparativos/valorizacion/item/'+id_valorizacion_cotizacion,
        dataType: 'JSON',
        success: function(response){   
         
            $('[name=personal_autorizado]').val(response['personal_autorizado_orden']);
            $('[name=lugar_despacho_valorizacion]').val(response['lugar_despacho_valorizacion']);
            $('[name=lugar_despacho_orden]').val(response['lugar_despacho_orden']);
            $('[name=id_valorizacion_cotizacion]').val(response['id_valorizacion_cotizacion']);
            $('[name=lugar_entrega_requerimiento]').val(response['lugar_entrega_requerimiento']);
            $('[name=descripcion_adicional_item_requerimiento]').val(response['descripcion_adicional_detalle_requerimiento']?response['descripcion_adicional_detalle_requerimiento']:'SIN DESCRIPCIÓN');
            $('[name=descripcion_adicional_item_orden]').val(response['descripcion_adicional_detalle_orden']);
                
            setTimeout(function () {
                var el = document.getElementsByName("descripcion_adicional_item_orden")[0];
                el.focus(); 
            }, 100);
   

        }
        
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });


}

$('#form-descripcion_adicional').on('submit', function(){
    var ask = confirm('¿Desea guardar este registro?');
    if (ask == true){
        
        $.ajax({
            type: 'PUT',
            url: '/logistica/descripcion_adicional_detalle_orden',
            datatype: "JSON",
            data: {
                'id_valorizacion_cotizacion': $('[name=id_valorizacion_cotizacion]').val(),
                'descripcion_adicional': $('[name=descripcion_adicional_item_orden]').val()
             },
            success: function(response){
                // console.log(response);
                
                if(response == 'ACTUALIZADO'){
                    alert('Datos de Descripción Adicional Actualizado!');
                 
                    listar_detalle_orden($('[name=id_orden_compra]').val());
                    // id_orden_compra

                }else if(response == 'NO_ACTUALIZADO'){
                    alert('NO se puedo actualizar');

                }else{
                    alert('ERROR al intentar actualizar');

                }
            }
        });
        $('#modal-despacho').modal('hide');

        return false;
    }else{
        return false;
    }
});

$('#form-despacho').on('submit', function(){
    // var data = $(this).serialize();
    var ask = confirm('¿Desea guardar este registro?');
    if (ask == true){
        
        $.ajax({
            type: 'PUT',
            url: '/logistica/despacho',
            datatype: "JSON",
            data: {
                'id_valorizacion_cotizacion': $('[name=id_valorizacion_cotizacion]').val(),
                'personal_autorizado': $('[name=personal_autorizado]').val(),
                'lugar_despacho' : $('[name=lugar_despacho_orden]').val()
            },
            success: function(response){
                if(response == 'ACTUALIZADO'){
                    alert('Datos de despacho Actualizado!');
                 
                    listar_detalle_orden($('[name=id_orden_compra]').val());
                    // id_orden_compra

                }else if(response == 'NO_ACTUALIZADO'){
                    alert('NO se puedo actualizar');

                }else{
                    alert('ERROR al intentar actualizar');

                }
            }
        });
        $('#modal-despacho').modal('hide');

        return false;
    }else{
        return false;
    }
});


function obtenerCuadroComparativoModal(){
    $('#modal-obtener-cuadro-comparativo').modal({
        show: true,
        backdrop: 'static'
    });

    // clearDataTable();
    lista_buenas_pro();
    document.getElementById('btnNuevo').setAttribute("disabled","true");
}


function lista_buenas_pro(){
    var vardataTables = funcDatatables();
    $('#ListaBuenasPro').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'ajax': '/data_buenas_pro',
        'columns': [
            {'data': 'id_valorizacion_cotizacion'},
            {'data': 'id_cotizacion'},
            {'data': 'codigo_grupo'},
            {'data': 'codigo_cotizacion'},
            {'render':
            function ( data, type, row ) {
                return ( row.razon_social+' [' +row.nombre_doc_identidad + ' - ' + row.nro_documento+ ']');
                
            }
            },
            {'render':
                function (data, type, row){
                    var req = '';
                    for (i=0;i<row['requerimiento'].length;i++){
                        if (req !== ''){
                            req += ', '+row['requerimiento'][0].codigo_requerimiento;
                        } else {
                            req += row['requerimiento'][0].codigo_requerimiento;
                        }
                    }
                    return (req);
                }
            },
            {'render':
            function ( data, type, row ) {
                return ( row.razon_social_empresa+' [' +row.nombre_doc_idendidad_empresa + ' - ' + row.nro_documento_empresa+ ']');
                
            }
            },


        //     {'render':
        //     function (data, type, row){
        //         var id_req = '';
        //         for (i=0;i<row['requerimiento'].length;i++){
        //             if (id_req !== ''){
        //                 id_req += ', '+row['requerimiento'][0].id_requerimiento;
        //             } else {
        //                 id_req += row['requerimiento'][0].id_requerimiento;
        //             }
        //         }
        //         return (id_req);
        //     }
        // },
        {'data': 'fecha_registro'},


         ],
        'columnDefs': [{ 'aTargets': [0,1], 'sClass': 'invisible'}],
    });
}

function selectBuenaPro(){
    let idValorizacionCotizacion = $('.modal-footer #idValorizacionCotizacion').text();  
    let id_cotizacion = $('.modal-footer #idCotizacion').text();  
    detalle_cotizacion(id_cotizacion);
    // console.log(id_cotizacion);
    // controlHiddenInputGroupOCRapida('mostrar',['condicion', 'plazo_entrega', 'moneda', 'tipo_documento', 'cuentas', 'responsable', 'totales']);
    // controlHiddenInputGroupOCRapida('ocultar',['requerimiento_seleccionado','sede','codigo_orden_externo']);
    $('#modal-obtener-cuadro-comparativo').modal('hide');


    
}

function  handleKeyPrecio(e,t){
        let index = t.getAttribute('index');
        updateMontoItem(index);
        let id_detalle_requerimiento =e.target.dataset.idDetalleRequerimiento;
        let valor =e.target.value;

        detalleRequerimientoSelected.forEach(element => {
            if(element.id_detalle_requerimiento == id_detalle_requerimiento){
                element.precio_referencial=valor;
            }
        });
        console.log(detalleRequerimientoSelected);    
}

 

function updateMontoItem(index){
    let cantidad = document.querySelectorAll("form[id='form-orden'] input[name='cantidad_item']")[index].value;
    let precio = document.querySelectorAll("form[id='form-orden'] input[name='precio_item']")[index].value;
    let total = cantidad * precio;
    document.querySelectorAll("form[id='form-orden'] input[name='monto_total_item']")[index].value = total.toFixed(2);
    
    calcularTotales();
}

function calcularTotales(){
    let inputTotales = document.querySelectorAll("form[id='form-orden'] input[name='monto_total_item']");
    let monto_subtotal = 0;
    inputTotales.forEach(element => {
        if(isNaN(element.value)==false){
            if(element.value - Math.floor(element.value) == 0){
                monto_subtotal+=Math.floor(element.value);
            }
        }

    document.querySelector("form[id='form-orden'] input[name='monto_subtotal']").value = monto_subtotal;

    var pigv = parseFloat($('[name=igv_porcentaje]').val());
    var igv = monto_subtotal * parseFloat(pigv) / 100;
    $('[name=monto_igv]').val(formatDecimal(igv));
    var total_a_pagar = monto_subtotal + igv;
    $('[name=monto_total]').val(formatDecimal(total_a_pagar));

        
    });
    
}
