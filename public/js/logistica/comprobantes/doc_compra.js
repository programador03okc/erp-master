var listaGuiaRemision=[];
var listaDetalleComprobanteCompra=[];
function get_data_cabecera_comprobante_compra(){
    var comprobanteCompra={
        'id_doc_com': document.querySelector("div[type='doc_compra'] input[name='id_doc_com']").value,
        'id_guia_com': document.querySelector("div[type='doc_compra'] input[name='id_guia_com']").value,
        'serie' : document.querySelector("div[type='doc_compra'] input[name='serie']").value,
        'numero' : document.querySelector("div[type='doc_compra'] input[name='numero']").value,
        'id_tp_doc' : document.querySelector("div[type='doc_compra'] select[name='id_tp_doc']").value,
        'id_proveedor' : document.querySelector("div[type='doc_compra'] input[name='id_proveedor']").value,
        'id_contrib' : document.querySelector("div[type='doc_compra'] input[name='id_contrib']").value,
        'fecha_emision' : document.querySelector("div[type='doc_compra'] input[name='fecha_emision']").value,
        'fecha_vcmto' : document.querySelector("div[type='doc_compra'] input[name='fecha_vcmto']").value,
        'id_condicion' : document.querySelector("div[type='doc_compra'] select[name='id_condicion']").value,
        'credito_dias' : document.querySelector("div[type='doc_compra'] input[name='credito_dias']").value,
        'moneda' : document.querySelector("div[type='doc_compra'] select[name='moneda']").value,
        'tipo_cambio' : document.querySelector("div[type='doc_compra'] input[name='tipo_cambio']").value,
        'sub_total' : document.querySelector("div[type='doc_compra'] input[name='sub_total']").value,
        'total_dscto' : document.querySelector("div[type='doc_compra'] input[name='total_dscto']").value,
        'porcen_dscto' : document.querySelector("div[type='doc_compra'] input[name='porcen_dscto']").value,
        'total' : document.querySelector("div[type='doc_compra'] input[name='total']").value,
        'total_igv' : document.querySelector("div[type='doc_compra'] input[name='total_igv']").value,
        'total_ant_igv' :'',
        'porcen_igv' : document.querySelector("div[type='doc_compra'] input[name='porcen_igv']").value,
        'porcen_anticipo' : '',
        'total_otros' : '',
        'total_a_pagar' : document.querySelector("div[type='doc_compra'] input[name='total_a_pagar']").value,
        'usuario' : document.querySelector("form[id='form-doc_compra'] select[name='usuario']").value,
        'registrado_por' : '',
        'estado' : 1,
        'estado_descripcion' : '',
        'detalle_comprobante':[]
    };
    return comprobanteCompra;
}

function editar_doc_compra(){
    document.querySelector("button[name='btnAgregarGuia']").removeAttribute("disabled");
    document.querySelector("button[name='btnAgregarOrden']").removeAttribute("disabled");
}

function nuevo_doc_compra(){
    listaGuiaRemision=[];
    listaDetalleComprobanteCompra=[];
    document.querySelector("button[name='btnAgregarGuia']").removeAttribute("disabled");
    document.querySelector("button[name='btnAgregarOrden']").removeAttribute("disabled");
    // console.log(auth_user);
    $('#form-doc_compra')[0].reset();
    $('[name=usuario]').val(auth_user.id_usuario);
    $('[name=id_tp_doc]').val(2).trigger('change.select2');
    $('#nombre_usuario label').text(auth_user.nombres);
	$('#listaDetalle tbody').html('');
    $('#guias tbody').html('');
}
$(function(){
    var id_doc_com = localStorage.getItem("id_doc_com");
    if (id_doc_com !== null){
        mostrar_doc_compra(id_doc_com);
    }
    tipo_cambio();
});

function mostrar_doc_compra(id_doc_com){
    if (id_doc_com !== null){
        $.ajax({
            type: 'GET',
            url: 'mostrar_doc_com/'+id_doc_com,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                $('[name=id_doc_com]').val(response[0].id_doc_com);
                $('[name=serie]').val(response[0].serie);
                $('#serie').text(response[0].serie);
                $('[name=numero]').val(response[0].numero);
                $('#numero').text(response[0].numero);
                $('[name=id_tp_doc]').val(response[0].id_tp_doc).trigger('change.select2');
                $('[name=fecha_emision]').val(response[0].fecha_emision);
                $('[name=fecha_vcmto]').val(response[0].fecha_vcmto);
                $('[name=id_condicion]').val(response[0].id_condicion);
                $('[name=credito_dias]').val(response[0].credito_dias);
                $('[name=id_proveedor]').val(response[0].id_proveedor);
                $('[name=prov_razon_social]').val(response[0].nro_documento + ' - ' + response[0].razon_social);
                $('[name=moneda]').val(response[0].moneda);
                $('[name=usuario]').val(response[0].usuario).trigger('change.select2');
                $('[name=sub_total]').val(formatDecimal(response[0].sub_total));
                $('[name=total_dscto]').val(formatDecimal(response[0].total_dscto));
                $('[name=porcen_igv]').val(formatDecimal(response[0].porcen_igv));
                $('[name=porcen_dscto]').val(formatDecimal(response[0].porcen_dscto?response[0].porcen_dscto:0));
                $('[name=total]').val(formatDecimal(response[0].total));
                $('[name=total_igv]').val(formatDecimal(response[0].total_igv));
                $('[name=total_ant_igv]').val(formatDecimal(response[0].total_ant_igv));
                $('[name=total_a_pagar]').val(formatDecimal(response[0].total_a_pagar));
                $('[name=cod_estado]').val(response[0].estado);
                $('#estado label').text('');
                $('#estado label').text(response[0].estado_doc);
                $('#fecha_registro label').text('');
                $('#fecha_registro label').text(response[0].fecha_registro);
                $('#registrado_por label').text('');
                $('#registrado_por label').text(response[0].nombre_corto);
                $('[name=simbolo_moneda]').text(response[0].simbolo)

                // listar_guias_prov(response[0].id_proveedor);
                // console.log(response[0].doc_com_det);
                if(response[0].guias.length >0){
                    agregarObjGuia(response[0].guias);
                    llenarTablaListaGuiaRemision(listaGuiaRemision);
                }
                if(response[0].doc_com_det.length >0){
                    agregarObjDetalleGuiaCompra(response[0].doc_com_det);
                    llenarTablaListaDetalleGuiaCompra(listaDetalleComprobanteCompra);
                }
                // if(response[0].doc_com_det.length > 0){
                //     listar_doc_com_orden(response[0].id_doc_com)
                // }else{
                //     listar_doc_guias(response[0].id_doc_com);
                //     listar_doc_items(response[0].id_doc_com);
                // }
                
                localStorage.removeItem("id_doc_com");
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });   
    }
}

function agregarObjGuia(data){
    // console.log(data);
    data.forEach(element => {
        listaGuiaRemision.push(
            {
                'id_doc_com_guia':element.id_doc_com_guia?element.id_doc_com_guia:null,
                'nro_guia':element.nro_guia,
                'id_guia':element.id_guia,
                'id_operacion':element.id_operacion,
                'tipo_operacion':element.tipo_operacion, 
                'id_proveedor':element.id_proveedor,
                'razon_social':element.razon_social,
                'fecha_emision':element.fecha_emision,
                'subtotal':null,
                'total':null,
                'porcen_dscto':0,
                'total_dscto':0,
                'importe_total':null,
                'estado':1
            }
        )

    });

}
 
function agregarObjDetalleGuiaCompra(data){
    // console.log(data);
    data.forEach(element => {
        listaDetalleComprobanteCompra.push(
            {
                'id':element.id_guia_com_det,
                'id_doc_det':element.id_doc_det,
                'id_item':element.id_item,
                'id_guia':element.id_guia,
                'nro_guia':element.nro_guia,
                'codigo':element.codigo,
                'descripcion':element.descripcion,
                'cantidad':element.cantidad,
                'precio_unitario':element.precio_unitario,
                'id_unid_med':element.id_unid_med,
                'unidad_medida':element.unidad_medida,
                'porcen_dscto':element.porcen_dscto,
                'total_dscto':element.total_dscto,
                'sub_total':(parseInt(element.cantidad) * parseFloat(element.precio_unitario)),
                'total':(parseInt(element.cantidad) * parseFloat(element.precio_unitario))-parseFloat(element.total_dscto),
                'estado':1
                
            }
        );
    });
}

function save_doc_compra(data, action){

    let doc_com= get_data_cabecera_comprobante_compra();
    let doc_com_detalle= listaDetalleComprobanteCompra;
    let guia_remision= listaGuiaRemision;
   
    if (action == 'register'){
        baseUrl = 'guardar_doc_compra';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_doc_compra';
    }
    console.log({'doc_com':doc_com, 'guia_remision':guia_remision,'doc_com_detalle':doc_com_detalle});

    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: {'doc_com':doc_com, 'guia_remision':guia_remision, 'doc_com_detalle':doc_com_detalle},
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if (response['id_doc'] > 0){
                if (action == 'register'){
                    alert('Documento registrado con éxito');
                }                
                if (action == 'edition'){
                    alert('Documento actualizado con éxito');

                    $('[name=cod_estado]').val('1');
                    $('#estado label').text('Elaborado');
                }
                $('[name=credito_dias]').attr('disabled',true);
                changeStateButton('guardar');
                $('#form-doc_compra').attr('type', 'register');
				changeStateInput('form-doc_compra', true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function listar_guias_prov(id_proveedor){
//     // console.log('id_proveedor'+id_proveedor);
//     $.ajax({
//         type: 'GET',
//         headers: {'X-CSRF-TOKEN': token},
//         url: 'listar_guias_prov/'+id_proveedor,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
       
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }




function llenarTablaListaGuiaRemision(data){

    var newData =  data.filter(element => element.estado != 7); 

    var vardataTables = funcDatatables();
    $('#ListaGuiaRemision').DataTable({
        'info': false,
        'searching': false,
        'paging':   false,
        'language' : vardataTables[0],
        'bDestroy': true,
        'data':newData,
        'columns': [
            {'data': 'nro_guia'},
            {'data': 'fecha_emision'},
            {'data': 'razon_social'},
            {'data': 'tipo_operacion'},
            {'render':
            function (data, type, row){
            return `<div class="btn-group" role="group">
                        <button type="button" class="btn btn-danger btn-xs" name="btnEliminarGuiayDetalle" title="Eliminar Guía y Detalle" data-id-guia="${row.id_guia}" onclick="eliminarGuiayDetalleGuua(this);">
                            <i class="fas fa-trash fa-sm"></i>
                        </button>
                    </div>`;
            }
            },
        ]
        // 'columnDefs': [{ 'aTargets': [0,5], 'sClass': 'invisible'}],
    });
}
function eliminarGuiayDetalleGuua(obj){
    let id_guia = obj.dataset.idGuia;
    listaGuiaRemision.forEach((element,index) => {
        if(element.id_guia == id_guia){
            if(element.estado == null){
                listaGuiaRemision.splice( index, 1 );
            }else{
                listaGuiaRemision[index].estado =7;
            }
        }
    });
    listaDetalleComprobanteCompra.forEach((element,index) => {
        if(element.id_guia == id_guia){
            if(element.estado == null){
                listaDetalleComprobanteCompra.splice( index, 1 );
            }else{
                listaDetalleComprobanteCompra[index].estado =7;
            }

        }
    });
    llenarTablaListaGuiaRemision(listaGuiaRemision);
    llenarTablaListaDetalleGuiaCompra(listaDetalleComprobanteCompra);
    CalcSubTotal(listaDetalleComprobanteCompra);



}
function updateUnitario(e){
    let id_guia_com_det= e.target.dataset.id;
    let valor = e.target.value;
    let tr= e.currentTarget.parentElement.parentElement;
    if(valor<=0 || valor==undefined){
        valor =0;
    }
    listaDetalleComprobanteCompra.forEach((element, index) => {
        if (element.id == id_guia_com_det) {
            listaDetalleComprobanteCompra[index].precio_unitario = valor;
            let sub_total = (parseInt(listaDetalleComprobanteCompra[index].cantidad)*parseFloat(listaDetalleComprobanteCompra[index].precio_unitario));
            listaDetalleComprobanteCompra[index].sub_total = sub_total;
            listaDetalleComprobanteCompra[index].total = sub_total;
            tr.querySelector("span[name='total']").textContent=sub_total;

        }
    });
    // console.log(listaDetalleComprobanteCompra);
    CalcSubTotal(listaDetalleComprobanteCompra);

}

function updatePorcentajeDescuento(e){
    let id_guia_com_det= e.target.dataset.id;
    let valor = e.target.value;
    let tr= e.currentTarget.parentElement.parentElement;
    if(valor<=0 || valor==undefined){
        valor =0;
    }

    listaDetalleComprobanteCompra.forEach((element, index) => {
        if (element.id == id_guia_com_det) {
            listaDetalleComprobanteCompra[index].porcen_dscto = valor;
            let total = (parseInt(listaDetalleComprobanteCompra[index].cantidad)*parseFloat(listaDetalleComprobanteCompra[index].precio_unitario));
            let montoDescuento=(parseFloat(total)*parseFloat(valor))/100;
            tr.querySelector("input[name='total_dscto']").value=montoDescuento;
            let newTotal = (parseFloat(total)-parseFloat(montoDescuento));
            tr.querySelector("span[name='total']").textContent=newTotal;
            listaDetalleComprobanteCompra[index].total_dscto = montoDescuento;
            listaDetalleComprobanteCompra[index].total = newTotal;
        


        }
    });
    CalcSubTotal(listaDetalleComprobanteCompra);

    // console.log(listaDetalleComprobanteCompra);
}

function resetPorcentajeDescuento(e){
    let tr= e.currentTarget.parentElement.parentElement;
    let id_guia_com_det= e.target.dataset.id;

    tr.querySelector("input[name='porcen_dscto']").value=0;
    listaDetalleComprobanteCompra.forEach((element, index) => {
        if (element.id == id_guia_com_det) {
            listaDetalleComprobanteCompra[index].porcen_dscto = 0;
        }
    });
}

function updateTotalDescuento(e){
    
    resetPorcentajeDescuento(e);
    let tr= e.currentTarget.parentElement.parentElement;
    let id_guia_com_det= e.target.dataset.id;
    let valor = e.target.value;
    if(valor<=0 || valor==undefined){
        valor =0;
    }
    listaDetalleComprobanteCompra.forEach((element, index) => {
        if (element.id == id_guia_com_det) {
            listaDetalleComprobanteCompra[index].total_dscto = valor;
            let newTotal= parseFloat(listaDetalleComprobanteCompra[index].cantidad * listaDetalleComprobanteCompra[index].precio_unitario)-parseFloat(valor)
            tr.querySelector("span[name='total']").textContent=newTotal;
            listaDetalleComprobanteCompra[index].total = newTotal;

        }
    });
    CalcSubTotal(listaDetalleComprobanteCompra);

}

function llenarTablaListaDetalleGuiaCompra(data){
    var newData =  data.filter(element => element.estado != 7); 

    var vardataTables = funcDatatables();
    $('#listaDetalleComprobanteCompra').DataTable({
        'info': false,
        'searching': false,
        'paging':   false,
        'language' : vardataTables[0],
        'bDestroy': true,
        'data':newData,
        'columns': [
            {'data': 'nro_guia'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'cantidad'},
            {'data': 'unidad_medida'},
            {'render':
            function (data, type, row){
                return  `<input type="text" class="form-control" name="precio_unitario" data-id="${row.id}" onkeyup ="updateUnitario(event);" value="${row.precio_unitario?row.precio_unitario:''}" style="
                width: 80px;">`;
            }
            },
            {'render':
            function (data, type, row){
                return  `<input type="text" class="form-control" name="porcen_dscto" data-id="${row.id}" onkeyup ="updatePorcentajeDescuento(event);" value="${row.porcen_dscto?row.porcen_dscto:''}" style="
                width: 40px;">`;
            }
            },
            {'render':
            function (data, type, row){
                return  `<input type="text" class="form-control" name="total_dscto" data-id="${row.id}" onkeyup ="updateTotalDescuento(event);" value="${row.total_dscto?row.total_dscto:''}" style="
                width: 80px;">`;
            }
            },
            {'render':
            function (data, type, row){
                return  `<span name="total">${row.total}</span`;
            }
            }
        ],
        // 'columnDefs': [{ 'aTargets': [0,5], 'sClass': 'invisible'}],
    });
}

function agregarAListaGuias(data){
    // console.log(data);
    if(data.guia.length > 0){
        data.guia.forEach(element => {
            listaGuiaRemision.push(
                {
                    'id_doc_com_guia':element.id_doc_com_guia?element.id_doc_com_guia:null,
                    'nro_guia':'GR-'+element.serie+'-'+element.numero,
                    'id_guia':element.id_guia,
                    'id_operacion':element.id_operacion,
                    'tipo_operacion':element.tipo_operacion, 
                    'id_proveedor':element.id_proveedor,
                    'razon_social':element.razon_social,
                    'fecha_emision':element.fecha_emision,
                    'subtotal':null,
                    'total':null,
                    'porcen_dscto':0,
                    'total_dscto':0,
                    'importe_total':null,
                    'estado':null

                }
            )
        });
    }
    if(data.guia_detalle.length > 0){
        data.guia_detalle.forEach(element => {
            listaDetalleComprobanteCompra.push(
                {
                    'id':element.id_guia_com_det,
                    'id_doc_det':null,
                    'id_item':element.id_item,
                    'id_guia':element.id_guia,
                    'nro_guia':element.nro_guia,
                    'codigo':element.codigo,
                    'descripcion':element.descripcion,
                    'cantidad':element.cantidad,
                    'precio_unitario':element.unitario,
                    'sub_total':((parseInt(element.cantidad)) * (parseFloat(element.unitario))),
                    'id_unid_med':element.id_unid_med,
                    'unidad_medida':element.unidad_medida,
                    'porcen_dscto':0,
                    'total_dscto':0,
                    // 'precio_total':'',
                    'total':element.total,
                    'estado':null

                    
                }
            );
        });

        // console.log(listaGuiaRemision);
        // console.log(listaDetalleComprobanteCompra);
        llenarTablaListaGuiaRemision(listaGuiaRemision);
        llenarTablaListaDetalleGuiaCompra(listaDetalleComprobanteCompra);
        CalcSubTotal(listaDetalleComprobanteCompra);
    }else{
        alert('La guía seleccionada no tiene detalle');
    }
}

function CalcSubTotal(data){
    var subtotal=0;
    if(data.length > 0){
        data.forEach(element => {
            if(element.estado != 7){
                subtotal+=parseFloat(element.total);
            }
        });
    }
    if(listaGuiaRemision.length >0){
        listaGuiaRemision[0]['subtotal']=subtotal;
    }    
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='sub_total']").value=subtotal;
    CalcTotal();
}

function calcTotalPorcentajeDescuento(event){
    let porcen_dscto = event.target.value;
    let subtotal = document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='sub_total']").value;
    let total_dscto = (subtotal*porcen_dscto)/100;
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_dscto']").value=total_dscto;
    listaGuiaRemision[0]['porcen_dscto']=porcen_dscto;
    listaGuiaRemision[0]['total_dscto']=total_dscto;

    CalcTotal();
}

function CalcTotal(){
    
    let subtotal = document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='sub_total']").value;
    let total_dscto =document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_dscto']").value?document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_dscto']").value:0;
    let total = subtotal - parseFloat(total_dscto);
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total']").value=total;
    if(listaGuiaRemision.length >0){
        listaGuiaRemision[0]['total']=total;
    }

    calcIGV();
    
}

function calcIGV(){
    let porcen_igv =document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='porcen_igv']").value;
    let total = document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total']").value;
    let total_igv= (parseFloat(total) * parseInt(porcen_igv))/ 100;
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_igv']").value= total_igv;
    if(listaGuiaRemision.length >0){
        listaGuiaRemision[0]['porcen_igv']=porcen_igv;
        listaGuiaRemision[0]['total_igv']=total_igv;
    }


    calcImporteTotal();
}

function calcImporteTotal(){
    let total =document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total']").value;
    let total_igv =document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_igv']").value;
    let importe_total = (parseFloat(total)+parseFloat(total_igv)).toFixed(2);
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_a_pagar']").value= importe_total;
    if(listaGuiaRemision.length >0){
        listaGuiaRemision[0]['importe_total']=importe_total;
    }
}


function agrega_guia(id_guia){
    document.querySelector("div[type='doc_compra'] input[name='id_guia_com']").value= id_guia;
    $.ajax({
        type: 'GET',
        url:  `listar_detalle_guia_compra/${id_guia}`,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            agregarAListaGuias(response);
            // llenarTablaListaDetalleGuiaCompra(response.data)
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

// function listar_doc_guias(id_doc){
//     $('#guias tbody').html('');
//     $.ajax({
//         type: 'GET',
//         headers: {'X-CSRF-TOKEN': token},
//         url: '/listar_doc_guias/'+id_doc,
//         dataType: 'JSON',
//         success: function(response){
//             $('#guias tbody').html(response);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
// function listar_doc_items(id_doc){
//     $('#listaDetalle tbody').html('');
//     $.ajax({
//         type: 'GET',
//         // headers: {'X-CSRF-TOKEN': token},
//         url: '/listar_doc_items/'+id_doc,
//         dataType: 'JSON',
//         success: function(response){
//             $('#listaDetalle tbody').html(response);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// } 

    // var id_guia = $('[name=id_guia]').val();
    // var id_proveedor = $('[name=id_proveedor]').val();
    // var id_doc_com = $('[name=id_doc_com]').val();
    // console.log('id_guia'+id_guia+' id_doc_com'+id_doc_com);
    
    // if (id_guia !== null){
    //     var rspta = confirm('¿Esta seguro que desea agregar los items de ésta guía?');
    //     if (rspta){
    //         $.ajax({
    //             type: 'GET',
    //             url: 'guardar_doc_items_guia/'+id_guia+'/'+id_doc_com,
    //             dataType: 'JSON',
    //             success: function(response){
    //                 // console.log('response'+response);
    //                 if (response > 0){
    //                     alert('Items registrados con éxito');
    //                     listar_doc_items(id_doc_com);
    //                     listar_doc_guias(id_doc_com);
    //                     // listar_guias_prov(id_proveedor);
    //                     // $('[name=id_guia]').val('0').trigger('change.select2');
    //                     actualiza_totales();
    //                 }
    //             }
    //         }).fail( function( jqXHR, textStatus, errorThrown ){
    //             console.log(jqXHR);
    //             console.log(textStatus);
    //             console.log(errorThrown);
    //         });
    //     }
    // } else {
    //     alert('Debe seleccionar una Guía');
    // }
}

function anular_doc_compra(ids){
    baseUrl = 'anular_doc_compra/'+ids;
    console.log(baseUrl);
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response.length > 0){
                alert('No es posible anular. '+response);
            } else {
                alert("Documento Anulado");
                changeStateButton('anular');
                // $('#estado label').text('Anulado');
                // $('[name=cod_estado]').val('7');
                mostrar_doc_com(ids);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function anular_guia(id_guia,id_doc_com_guia){
//     var id_doc = $('[name=id_doc_com]').val();
//     // console.log('id_guia'+id_guia+'id_doc'+id_doc);
//     var anula = confirm("¿Esta seguro que desea anular ésta OC?\nSe quitará también la relación de sus Items");
//     if (anula){
//         $.ajax({
//             type: 'GET',
//             headers: {'X-CSRF-TOKEN': token},
//             url: '/anular_guia/'+id_doc+'/'+id_guia,
//             dataType: 'JSON',
//             success: function(response){
//                 console.log(response);
//                 if (response > 0){
//                     alert('Guía anulada con éxito');
//                     $("#doc-"+id_doc_com_guia).remove();
//                     listar_doc_items(id_doc);
//                 }
//             }
//         }).fail( function( jqXHR, textStatus, errorThrown ){
//             console.log(jqXHR);
//             console.log(textStatus);
//             console.log(errorThrown);
//         });
//     }
// }

function tipo_cambio(){
    $.ajax({
        type: 'GET',
        url: 'tipo_cambio_compra/'+fecha_actual(),
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            $('[name=tipo_cambio]').val(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// function getTipoCambio(){
//     var fecha = $('[name=fecha_emision]').val();
//     console.log(fecha);

//     var proxy = 'https://cors-anywhere.herokuapp.com/';
//     var url = 'https://api.sunat.cloud/cambio/';
//     var peticion = new Request(proxy + url + fecha, 
//         {cache: 'no-cache'});
//     fetch( peticion )
//         .then(response => response.json())
//         .then((respuesta)=>{
//             console.log(respuesta);
//             console.log(respuesta[fecha].compra);
//             console.log(respuesta[fecha].venta);
//             $('[name=tipo_cambio]').val(respuesta[fecha].compra);
//         })
//     .catch(e => console.error('Algo salio mal...'));

// }
function ceros_numero(){
    var num = $('[name=numero]').val();
    $('[name=numero]').val(leftZero(7,num));
}
function change_dias(){
    var condicion = $('[name=id_condicion]').val();
    var edi = $('[name=id_condicion]').attr('disabled');
    // console.log('edi'+edi);
    if (condicion == 2){
        $('[name=credito_dias]').attr('disabled',false);
    } else {
        $('[name=credito_dias]').attr('disabled',true);
    }
}
// function actualiza_totales(){
//     var por = $('[name=porcen_dscto]').val();
//     var id = $('[name=id_doc_com]').val();
//     var fecha = $('[name=fecha_emision]').val();
//     $.ajax({
//         type: 'GET',
//         url: '/actualiza_totales_doc/'+por+'/'+id+'/'+fecha,
//         dataType: 'JSON',
//         success: function(response){
//             // console.log(response);
//             if (response > 0){
//                 mostrar_doc_compra(id);
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
//     // var sub_total = 0;
//     // $('#listaDetalle tbody tr').each(function(e){
//     //     var tds = parseFloat($(this).find("td input[name=precio_total]").val());
//     //     sub_total += tds;
//     // });
//     // var dscto = parseFloat($('[name=total_dscto]').val());
//     // $('[name=porcen_igv]').val(18);
//     // var total = sub_total + dscto;
//     // var total_igv = total * 18/100;

//     // $('[name=sub_total]').val(sub_total);
//     // $('[name=total]').val(total);
//     // $('[name=total_igv]').val(total_igv);
//     // $('[name=total_a_pagar]').val(total + total_igv);

// }