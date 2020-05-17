var listCheckOrden = [];
var detalle_orden=[];

$( document ).ready(function() {
    let id_empresa = document.getElementById('id_empresa_select_req').value;

    listar_ordenes(id_empresa,'all');
    listar_comprobante_compra(id_empresa,'all');

    document.getElementById('menu_tab_crear_factura').childNodes[3].children[0].setAttribute('data-toggle', 'notab');
    document.getElementById('menu_tab_crear_factura').childNodes[3].className ='disabled';
    document.getElementById('menu_tab_crear_factura').childNodes[1].className ='active';
    document.getElementById('contenido_tab_crear_factura').childNodes[3].className = 'tab-pane';
    document.getElementById('contenido_tab_crear_factura').childNodes[1].className = 'active';

    get_user_session();
});

function inputPorcentajeDescKeyPress(event){
    let porcentajeDescuento = event.target.value;
    let montoTotal = document.querySelector("input[name='monto_total_fijo']").value;
    let montoDecuento = montoTotal * (porcentajeDescuento/100);
    document.querySelector("input[name='monto_descuento']").value = montoDecuento;

    document.querySelector("input[name='monto_total']").value =(montoTotal-montoDecuento);

}
function inputMontoDescKeyPress(event){
    let montoDecuento = event.target.value;
    let montoTotal = document.querySelector("input[name='monto_total_fijo']").value;
    let porcentajeDescuento =  (montoDecuento*100)/montoTotal;

    document.querySelector("input[name='porcentaje_descuento']").value = porcentajeDescuento;
    document.querySelector("input[name='monto_total']").value =(montoTotal-montoDecuento);

}

var get_orden = function(id_empresa=null, id_orden=null) {
    return new Promise(function(resolve, reject) {
        var baseUrl ='/ordenes_sin_facturar/' + id_empresa+'/'+id_orden;
        $.ajax({
            type: 'GET',
            url: baseUrl,
            dataType: 'JSON',
            success(response) {
                if(response.length >0){
                    if(response[0]['detalle_orden'].length > 0){
                        detalle_orden = response[0]['detalle_orden'];
                        // console.log(detalle_orden);
                        

                    }
                }
                
                resolve(response) // Resolve promise and go to then() 
            },
            error: function(err) {
            reject(err) // Reject the promise and go to catch()
            }
    });
    });
}



function listar_ordenes(id_empresa, id_orden){
    get_orden(id_empresa, id_orden).then(function(data) {

        var vardataTables = funcDatatables()
        $('#ListaOrdenes').dataTable({
            bDestroy: true,
            order: [[0, 'asc']],
            language: vardataTables[0],
            processing: true,
            bDestroy: true,
            data:data,
            columns: [
                { data: 'id_orden_compra' },
                { data: 'fecha' },
                { data: 'codigo' },
                { data: 'proveedor' },
                { data: 'moneda' },
                { data: 'monto_subtotal' },
                { data: 'monto_igv' },
                { data: 'monto_total' },
                { data: 'condicion' },
                { data: 'plazo_entrega' },
                {
                    render: function (data, type, row) {
                        let btn =
                        '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
                            '<button class="btn btn-primary btn-sm" name="btnCotizacionRelacionada" title=" Ir a Paso 2" onclick="gotToSecondStep(event,'+row.id_orden_compra+');">'+
                                '<i class="far fa-arrow-alt-circle-right"></i>'+
                            '</button>'+
                        '</div>';
                        return btn;
                    },
                }
            ],
            columnDefs: [{ aTargets: [0], sClass: 'invisible' }],
        })
    
        let tablelistaitem = document.getElementById(
            'ListaOrdenes_wrapper'
        )
        tablelistaitem.childNodes[0].childNodes[0].hidden = true

    });
}


var get_comprobanteCompra = function(id_empresa=null, id_factura=null) {
    return new Promise(function(resolve, reject) {
        var baseUrl ='/lista_comprobante_compra/' + id_empresa+'/'+id_factura;
        $.ajax({
            type: 'GET',
            url: baseUrl,
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

function listar_comprobante_compra(id_empresa, id_factura){
    get_comprobanteCompra(id_empresa, id_factura).then(function(data) {
        var vardataTables = funcDatatables()
        $('#listaComprobanteCompra').dataTable({
            bDestroy: true,
            order: [[0, 'asc']],
            language: vardataTables[0],
            processing: true,
            bDestroy: true,
            data:data,
            columns: [
                { data: 'id_doc_com' },
                {'render':
                    function (data, type, row, meta){
                        return meta.row +1;
                    }
                },
                { data: 'fecha_emision' },
                { data: 'codigo' },
                { data: 'proveedor' },
                { data: 'empresa' },
                { data: 'sede' },
                { data: 'fecha_vcmto' },
                { data: 'condicion' },
                { data: 'moneda' },
                { data: 'tipo_cambio' },
                { data: 'porcen_descuento' },
                { data: 'total_descuento' },
                { data: 'sub_total' },
                { data: 'total_igv' },
                { data: 'total' },
                { data: 'fecha_registro' },
                {
                    render: function (data, type, row) {
                        let btn =
                        '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
                            '<button class="btn btn-primary btn-sm" name="btnEditarComprobanteCompra" title="Editar" onclick="editarComprobanteCompra(event,'+row.id_doc_com+');">'+
                                '<i class="far fa-edit"></i>'+
                            '</button>'+
                        '</div>';
                        return btn;
                    },
                }
            ],
            columnDefs: [{ aTargets: [0], sClass: 'invisible' }],
        })
    
        let tablelistaitem = document.getElementById(
            'listaComprobanteCompra_wrapper'
        )
        tablelistaitem.childNodes[0].childNodes[0].hidden = true

    });
}


var get_comprobante_compra = function(id_sede=null,id_doc_com=null) {
    return new Promise(function(resolve, reject) {
        var baseUrl ='/lista_comprobante_compra/' + id_sede+'/'+id_doc_com;
        $.ajax({
            type: 'GET',
            url: baseUrl,
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

function llenar_formulario_editar_comproante_compra(data){
    
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='id_orden']").value = data[0].id_orden_compra;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='numero']").value = data[0].numero;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='serie']").value = data[0].serie;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='proveedor']").value = data[0].proveedor;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='id_proveedor']").value = data[0].id_proveedor;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='fecha_emision']").value = data[0].fecha_emision;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='fecha_vencimiento']").value = data[0].fecha_vcmto;
    document.querySelector("form[id='form_editar_comprobante_compra'] select[name='id_condicion']").value = data[0].id_condicion;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='plazo_dias']").value = data[0].credito_dias;
    document.querySelector("form[id='form_editar_comprobante_compra'] select[name='id_moneda']").value = data[0].id_moneda;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='id_sede']").value = data[0].id_sede;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='tipo_cambio']").value = data[0].tipo_cambio;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='porcentaje_descuento']").value = data[0].porcen_descuento;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='monto_descuento']").value = data[0].total_descuento;

    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='monto_subtotal']").value = data[0].sub_total;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='monto_igv']").value = data[0].total_igv;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='monto_total']").value = data[0].total;
    document.querySelector("form[id='form_editar_comprobante_compra'] input[name='monto_total_fijo']").value = data[0].total;
}

function editarComprobanteCompra(e,id_doc_com){
    $('#modal-editar_comprobante_compra').modal({
        show: true,
        backdrop: 'static',
    })
    get_comprobante_compra(null,id_doc_com).then(function(data) {
        llenar_formulario_editar_comproante_compra(data);
        
    });
}

function actualizarComprobanteCompra(){
    console.log('actualizar');
    
}
// function setFechaRegistro(){
//     var date = new Date();
//     var day = date.getDate();
//     var month = date.getMonth() + 1;
//     var year = date.getFullYear();
//     if (month < 10) month = "0" + month;
//     if (day < 10) day = "0" + day;
//     var today = year + "-" + month + "-" + day;       
//     document.getElementsByName("fecha_registro")[0].value = today;
// }

function llenar_formulario_crear_factura(data){
    if(data.length >0){
        var formattedDate='';
        if(data[0].fecha){
            let fecha_registro =  data[0].fecha;
            let fecha = new Date(fecha_registro);
            formattedDate =   fecha.getFullYear() + '-' + ("0" + (fecha.getMonth() + 1)).slice(-2) + '-' + ("0" + fecha.getDate()).slice(-2);    
        }
        
        
        document.querySelector("form[id='form_crear_comprobante_compra'] input[name='id_orden']").value = data[0].id_orden_compra;
        document.querySelector("form[id='form_crear_comprobante_compra'] input[name='proveedor']").value = data[0].proveedor;
        document.querySelector("form[id='form_crear_comprobante_compra'] input[name='id_proveedor']").value = data[0].id_proveedor;
        document.querySelector("form[id='form_crear_comprobante_compra'] input[name='fecha_emision']").value = formattedDate;
        document.querySelector("form[id='form_crear_comprobante_compra'] select[name='id_condicion']").value = data[0].id_condicion;
        document.querySelector("form[id='form_crear_comprobante_compra'] input[name='plazo_dias']").value = data[0].plazo_dias;
        document.querySelector("form[id='form_crear_comprobante_compra'] select[name='id_moneda']").value = data[0].id_moneda;
        document.querySelector("form[id='form_crear_comprobante_compra'] input[name='id_sede']").value = data[0].id_sede;
    
        document.querySelector("form[id='form_crear_comprobante_compra'] input[name='monto_subtotal']").value = data[0].monto_subtotal;
        document.querySelector("form[id='form_crear_comprobante_compra'] input[name='monto_igv']").value = data[0].monto_igv;
        document.querySelector("form[id='form_crear_comprobante_compra'] input[name='monto_total']").value = data[0].monto_total;
        document.querySelector("form[id='form_crear_comprobante_compra'] input[name='monto_total_fijo']").value = data[0].monto_total;

        if(data[0]['detalle_orden'].length >0){
            llenar_tabla_detalle_orden(data[0]['detalle_orden']);
        }
    }

}

function llenar_tabla_detalle_orden(data){
    var vardataTables = funcDatatables()
    $('#ListaDetalleOrden').dataTable({
        bDestroy: true,
        order: [[0, 'asc']],
        language: vardataTables[0],
        processing: true,
        bDestroy: true,
        data:data,
        columns: [
            { data: 'id_item' },
            { data: 'codigo' },
            { data: 'descripcion_item' },
            { data: 'cantidad_cotizada' },
            { data: 'unidad_medida' },
            { data: 'precio_cotizado' },
            { data: 'incluye_igv' },
            { data: 'igv' },
            { data: 'precio_sin_igv' },
            { data: 'subtotal' },
            { data: 'monto_descuento' },
            { data: 'porcentaje_descuento' },
            {render:
            function (data, type, row){
                var monto_descuento = row.monto_descuento?row.monto_descuento:0;
                var total = (row.subtotal)-monto_descuento;
                return (total);
            }
        },

        ],
        columnDefs: [{ aTargets: [0], sClass: 'invisible' }],
    })

    let tablelistaitem = document.getElementById(
        'ListaDetalleOrden_wrapper'
    )
    tablelistaitem.childNodes[0].childNodes[0].hidden = true
}


function gotToSecondStep(e,id_orden) {

    get_orden(null, id_orden).then(function(data) {
        llenar_formulario_crear_factura(data);
        
    });
    
    e.preventDefault();
    // setFechaRegistro();

    document.getElementById('menu_tab_crear_factura').childNodes[1].children[0].setAttribute('data-toggle', 'notab');
    document.getElementById('menu_tab_crear_factura').childNodes[1].className ='disabled';
    document.getElementById('menu_tab_crear_factura').childNodes[3].className ='active';
    document.getElementById('contenido_tab_crear_factura').childNodes[1].className = 'tab-pane';
    document.getElementById('contenido_tab_crear_factura').childNodes[3].className = 'active';
}

function gotToSecondToFirstTab(e){
    e.preventDefault();
	document.getElementById('menu_tab_crear_factura').childNodes[3].children[0].setAttribute('data-toggle', 'notab');
	document.getElementById('menu_tab_crear_factura').childNodes[3].className ='disabled';
	document.getElementById('menu_tab_crear_factura').childNodes[1].className ='active';
	document.getElementById('contenido_tab_crear_factura').childNodes[3].className = 'tab-pane';
	document.getElementById('contenido_tab_crear_factura').childNodes[1].className = 'active';
}

function get_user_session(){
    $.ajax({
        type: 'GET',
        url: '/session-rol-aprob',
        success: function(response){
            // console.log(response); 
            // userSession=response;
            document.getElementsByName('id_usuario_session')[0].value= response.id_usuario;
        }
    });
}

function get_data_form_factura_compra(){
    let id_orden = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='id_orden']").value;
    let id_sede = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='id_sede']").value;
    let id_usuario = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='id_usuario_session']").value;
    let proveedor = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='proveedor']").value;
    let id_proveedor = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='id_proveedor']").value;
    let serie = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='serie']").value;
    let fecha_vencimiento = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='fecha_vencimiento']").value;
    let fecha_emision = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='fecha_emision']").value;
    let id_condicion = document.querySelector("form[id='form_crear_comprobante_compra'] select[name='id_condicion']").value;
    let plazo_dias = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='plazo_dias']").value;
    let id_moneda = document.querySelector("form[id='form_crear_comprobante_compra'] select[name='id_moneda']").value;
    let tipo_cambio = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='tipo_cambio']").value;
    let porcentaje_descuento = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='porcentaje_descuento']").value;
    let monto_descuento = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='monto_descuento']").value;

    let monto_subtotal = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='monto_subtotal']").value;
    let monto_igv = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='monto_igv']").value;
    let monto_total = document.querySelector("form[id='form_crear_comprobante_compra'] input[name='monto_total']").value;
 

    return {
        id_orden,
        id_usuario,
        id_sede,
        proveedor,
        id_proveedor,
        serie,
        fecha_vencimiento,
        fecha_emision,
        id_condicion,
        plazo_dias,
        id_moneda,
        tipo_cambio,
        porcentaje_descuento,
        monto_descuento,
        monto_subtotal,
        monto_igv,
        monto_total,
        detalle_orden:detalle_orden
    }
}

function generar_comprobante_compra(event){
    let data = get_data_form_factura_compra();
    console.log(data);
    
    let baseUrl = '/guardar_comprobante_compra';
    if(data.id_orden > 0){
        $.ajax({
            type: 'POST',
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                if (response.status == 200){
                    alert('Comprobante Registrado con éxito');
                }else{
                    alert('Lo siento, hubo un problema al guardar el comprobante.')
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
    
    
}