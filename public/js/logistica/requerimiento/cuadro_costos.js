
function getCabeceraCuadroCostos(id){
    return new Promise(function(resolve, reject) {
    $.ajax({
        type: 'GET',
        url:rutaCuadroCostos +'/'+id,
        dataType: 'JSON',
        success(response) {
            resolve(response) // Resolve promise and go to then() 
        },
        error: function(err) {
        reject(err) // Reject the promise and go to catch()
        }
        });
    });
}


function getDataCuadroCostos(cc){
    getCabeceraCuadroCostos(cc.id_cc).then(function(res) {
        // Run this when your request was successful
        // console.log(res)
        if(res.status ==200){
            llenarCabeceraCuadroCostos(res.data);
        }
    }).catch(function(err) {
        // Run this when promise was rejected via reject()
        console.log(err)
    })
    geDetalleCuadroCostos(cc.id_cc).then(function(res) {
        // Run this when your request was successful
        // console.log(res)
        let cantidadTransformaciones=0;
 
        if(res.status ==200){
            tempDetalleItemsCC= res.data;
            detalleItemsCC= res.data;
            if(res.data.length >0){
                tempDetalleItemsCC.forEach(element => {
                    if(element['descripcion_producto_transformado'] != null  || element['descripcion_producto_transformado' != '']){
                        cantidadTransformaciones+=1;
                        itemsConTransformacionList.push({
                            'id':element.id,
                            'part_no_producto_transformado':element.part_no_producto_transformado,
                            'descripcion_producto_transformado':(element.descripcion_producto_transformado.replace("nbsp;","").replace("&nbsp;","").replace("&amp;","")).trim(),
                            'comentario_producto_transformado':element.comentario_producto_transformado,
                            'cantidad':element.cantidad
    
                        });
                    }
                });
                // console.log(itemsConTransformacionList);
                if(cantidadTransformaciones >0){
                    document.querySelector("form[id='form-requerimiento'] input[name='tiene_transformacion']").value= true;
                    document.querySelector("fieldset[id='group-detalle-items-transformados']").removeAttribute('hidden');
                    llenarItemsTransformados(itemsConTransformacionList)
                }else{
                    document.querySelector("form[id='form-requerimiento'] input[name='tiene_transformacion']").value= false;
                    document.querySelector("fieldset[id='group-detalle-cuadro-costos']").removeAttribute('hidden');
                    llenarDetalleCuadroCostos(tempDetalleItemsCC);
                }
            }else{
                alert("Hubo un problema al intentar encontrar el detalle del cuadro de costos");
            }


        }
    }).catch(function(err) {
        // Run this when promise was rejected via reject()
        console.log(err)
    })
}

function geDetalleCuadroCostos(id){
    return new Promise(function(resolve, reject) {
    $.ajax({
        type: 'GET',
        url:rutaDetalleCuadroCostos +'/'+id,
        dataType: 'JSON',
        success(response) {
            resolve(response) // Resolve promise and go to then() 
        },
        error: function(err) {
        reject(err) // Reject the promise and go to catch()
        }
        });
    });
}
function getOrBuildCustomer(razon_social,ruc,telefono,direccion,correo,ubigeo_entidad){
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: 'POST',
            url:rutaObtenerCostruirCliente,
            data:{'razon_social':razon_social ,'ruc':ruc,'telefono':telefono,'direccion':direccion,'correo':correo,'ubigeo':ubigeo_entidad},
            dataType: 'JSON',
            success(response) {
                resolve(response); // Resolve promise and go to then() 
            },
            error: function(err) {
            reject(err); // Reject the promise and go to catch()
            }
            });
        });
}

function llenarCabeceraCuadroCostos(data){
// console.log( 'llenarCabeceraCuadroCostos');
// console.log(data);
    changeStateInput('form-requerimiento', false);
    changeStateButton('nuevo');
    nuevo_req();
    document.querySelector("input[name='fecha_creacion_cc']").value =data.fecha_creacion_cc;
    document.querySelector("input[name='id_cc']").value =data.id_cc;
    document.querySelector("input[name='tipo_cuadro']").value =data.tipo_cuadro;
    document.querySelector("select[name='tipo_requerimiento']").value =1;
    document.querySelector("input[name='confirmacion_pago']").value =true;
    document.querySelector("input[name='concepto']").value =data.orden_am;
    document.querySelector("select[name='prioridad']").value =1;
    document.querySelector("select[id='empresa']").value =data.id_empresa;
    getDataSelectSedeSinUbigeo(data.id_empresa);
    // document.querySelector("select[name='sede']").value ='';
    document.querySelector("select[name='moneda']").value =1;
    // document.querySelector("input[name='name_ubigeo']").value ='';
    document.querySelector("input[name='monto']").value =data.monto_total;
    document.querySelector("input[name='fecha_entrega']").value =data.fecha_entrega;
    document.querySelector("select[name='tipo_cliente']").value =2;
    
    // document.querySelector("input[name='id_cliente']").value =data.id_cliente?data.id_cliente:null;
    document.querySelector("input[name='nombre_contacto']").value =data.contact_nombre?data.contact_nombre:'';
    document.querySelector("input[name='cargo_contacto']").value =data.contact_cargo?data.contact_cargo:'';
    document.querySelector("input[name='email_contacto']").value =data.contact_email?data.contact_email:'';
    document.querySelector("input[name='telefono_contacto']").value =data.contact_telefono?data.contact_telefono:'';
    document.querySelector("input[name='direccion_contacto']").value =data.contact_direccion?data.contact_direccion:'';
    document.querySelector("input[name='horario_contacto']").value =data.contact_horario?data.contact_horario:'';
    changeTipoCliente(event,2); //cambiar input para tipo cliente

    
    document.querySelector("h6[name='titulo_tabla_detalle_cc']").textContent = `Detalle de Cuadro de Costos ${data.codigo_oportunidad} ( ${data.estado_aprobacion_cc} )`;

    getOrBuildCustomer(data.nombre_entidad,data.ruc_entidad,data.telefono,data.direccion_entidad,data.correo,data.ubigeo_entidad).then(function(res) {
        // Run this when your request was successful
        // console.log(res);
        if(res.status ==200){
            document.querySelector("input[name='id_cliente']").value =res.data.id_cliente?res.data.id_cliente:'';
            document.querySelector("input[name='cliente_ruc']").value =res.data.ruc?res.data.ruc:'';
            document.querySelector("input[name='cliente_razon_social']").value =res.data.razon_social?res.data.razon_social:'';
            document.querySelector("textarea[name='observacion']").value = 'Ubigeo Cliente: '+data.ubigeo_entidad?data.ubigeo_entidad:'';
            // document.querySelector("input[name='responsable']").value =res.data.responsable;
            document.querySelector("input[name='direccion_entrega']").value =res.data.direccion?res.data.direccion:'';
            document.querySelector("input[name='telefono_cliente']").value =res.data.telefono?res.data.telefono:'';
            document.querySelector("input[name='email_cliente']").value =res.data.correo?res.data.correo:'';
            document.querySelector("input[name='ubigeo']").value =res.data.id_ubigeo?res.data.id_ubigeo:'';
            document.querySelector("input[name='name_ubigeo']").value =res.data.descripcion_ubigeo?res.data.descripcion_ubigeo:'';
            // console.log(res.mensaje);
        }else{
            console.log(res.status);
            console.log(res.mensaje);
        }
    }).catch(function(err) {
        // Run this when promise was rejected via reject()
        console.log(err);
    })

}

function llenarItemsTransformados(data){

    var dataTableListaDetalleItemstransformado =  $('#ListaDetalleItemstransformado').DataTable({
        'processing': false,
        'serverSide': false,
        'bDestroy': true,
        'bInfo':     false,
        'dom': 'Bfrtip',
        'paging':   false,
        'searching': false,
        'data':data,
        'columns':[
            {'data':'part_no_producto_transformado'},
            {'data':'descripcion_producto_transformado'},
            {'data':'cantidad'},
            {'data':'comentario_producto_transformado'},
            {'render': function (data, type, row){
                return `<button class="btn btn-xs btn-default" data-key="${row['id']}" onclick="procesarItemDetalleCuadroCostos(${row['id']},'ITEM_CON_TRANSFORMACION');" title="Agregar Item" style="background-color:#a7904f; color:white;"><i class="fas fa-plus"></i></button>`;
                }
            }
        ]
    });

    document.querySelector("table[id='ListaDetalleItemstransformado']").tHead.style.fontSize = '11px',
    document.querySelector("table[id='ListaDetalleItemstransformado']").tBodies[0].style.fontSize = '11px';
    dataTableListaDetalleItemstransformado.buttons().destroy();
    document.querySelector("table[id='ListaDetalleItemstransformado'] thead").style.backgroundColor ="#968a30";
    $('#ListaDetalleItemstransformado tr').css('cursor','default');
}

function llenarDetalleCuadroCostos(data){


    var dataTableListaDetalleCuadroCostos =  $('#ListaDetalleCuadroCostos').DataTable({
        'processing': false,
        'serverSide': false,
        'bDestroy': true,
        'bInfo':     false,
        'dom': 'Bfrtip',
        'paging':   false,
        'searching': false,
        'data':data,
        'columns':[
            {'data':'part_no'},
            {'render': function (data, type, row){
                return `${row['descripcion']}`;
                }
            },
            {'data':'pvu_oc'},
            {'data':'flete_oc'},
            {'data':'cantidad'},
            {'data':'garantia'},
            {'data':'razon_social_proveedor'},
            {'data':'nombre_autor'},
            {'data':'fecha_creacion'}, 
            {'render': function (data, type, row){
                return `<button class="btn btn-xs btn-default" data-key="${row['id']}" onclick="procesarItemDetalleCuadroCostos(${row['id']},'ITEM_SIN_TRANSFORMACION');" title="Agregar Item" style="background-color:#714fa7; color:white;"><i class="fas fa-plus"></i></button>`;
                }
            }
        ]
    });

    document.querySelector("table[id='ListaDetalleCuadroCostos']").tHead.style.fontSize = '11px',
    document.querySelector("table[id='ListaDetalleCuadroCostos']").tBodies[0].style.fontSize = '11px';
    dataTableListaDetalleCuadroCostos.buttons().destroy();
    document.querySelector("table[id='ListaDetalleCuadroCostos'] thead").style.backgroundColor ="#5d4d6d";
    $('#ListaDetalleCuadroCostos tr').css('cursor','default');


}
 
function eliminarVinculoCC(){
    document.querySelector("form[id='form-requerimiento'] input[name='id_cc']").value='';
    tempDetalleItemCCSelect={};
    tempDetalleItemsCC=[];
    sessionStorage.removeItem('ordenP_Cuadroc')
    $('#text-info-cc-vinculado').attr('hidden',true);
    $('#text-info-item-vinculado').attr('hidden',true);
    document.querySelector("fieldset[id='group-detalle-cuadro-costos']").setAttribute('hidden',true);
    alert("Se elimino el vinculo al Cuadro de Costos");

}
function eliminarVinculoItemCC(){
    tempDetalleItemCCSelect={};
    $('#text-info-item-vinculado').attr('hidden',true);
    alert("Se elimino el vinculo del item seleccionado del Cuadro de Costos");

}

function procesarItemDetalleCuadroCostos(id_detalle_cc,tipo_item){
    // console.log(id_detalle_cc);
    
    let detalle_cc_selected=null;
    let id_cc_am_filas=null;
    let id_cc_venta_filas=null;
    tempDetalleItemsCC.forEach(element => {
        if(element.id == id_detalle_cc){
            detalle_cc_selected= element;
        }
    });

    if( detalle_cc_selected.hasOwnProperty('id_cc_am')){
        id_cc_am_filas = detalle_cc_selected.id;
        id_cc_venta_filas = null;
    }else if(detalle_cc_selected.hasOwnProperty('id_cc_venta')){
        id_cc_am_filas = null;
        id_cc_venta_filas = detalle_cc_selected.id;
    }
    if(tipo_item =='ITEM_SIN_TRANSFORMACION'){
        let descripcionParseText = ((detalle_cc_selected.descripcion).replace("&lt;","<").replace("nbsp;","").replace("&nbsp;","").replace("&amp;","")).trim();
        let partNumberParseText = detalle_cc_selected.part_no? detalle_cc_selected.part_no:'';
        let precioUnitarioOC = detalle_cc_selected.pvu_oc? detalle_cc_selected.pvu_oc:0;
        let cantidadParseText = detalle_cc_selected.cantidad;
        tempDetalleItemCCSelect={
            'part_number':document.querySelector("div[id='modal-crear-nuevo-producto'] input[name='part_number']").value= partNumberParseText,
            'descripcion':document.querySelector("div[id='modal-crear-nuevo-producto'] textarea[name='descripcion']").value= descripcionParseText,
            'cantidad':cantidadParseText,
            'precio_unitario': precioUnitarioOC,
            'id_cc_am_filas':id_cc_am_filas,
            'id_cc_venta_filas':id_cc_venta_filas
            }
    }else if(tipo_item =='ITEM_CON_TRANSFORMACION'){
        let descripcionParseText = ((detalle_cc_selected.descripcion_producto_transformado).replace("&lt;","<").replace("nbsp;","").replace("&nbsp;","").replace("&amp;","")).trim();
        let partNumberParseText = detalle_cc_selected.part_no_producto_transformado? detalle_cc_selected.part_no_producto_transformado:'';
        let cantidadParseText = detalle_cc_selected.cantidad;
        let precioUnitarioOC = detalle_cc_selected.pvu_oc? detalle_cc_selected.pvu_oc:0;

        // console.log(cantidadParseText);

        tempDetalleItemCCSelect={
            'part_number':document.querySelector("div[id='modal-crear-nuevo-producto'] input[name='part_number']").value= partNumberParseText,
            'descripcion':document.querySelector("div[id='modal-crear-nuevo-producto'] textarea[name='descripcion']").value= descripcionParseText,
            'cantidad':cantidadParseText,
            'precio_unitario': precioUnitarioOC,
            'id_cc_am_filas':id_cc_am_filas,
            'id_cc_venta_filas':id_cc_venta_filas
        }
    }

        catalogoItemsModal();

}