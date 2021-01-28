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
        'total_descuento' : document.querySelector("div[type='doc_compra'] input[name='total_descuento']").value,
        'porcen_descuento' : document.querySelector("div[type='doc_compra'] input[name='porcen_descuento']").value,
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



function nuevo_doc_compra(){
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
                console.log(response);
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
                $('[name=total_descuento]').val(formatDecimal(response[0].total_descuento));
                $('[name=porcen_igv]').val(formatDecimal(response[0].porcen_igv));
                $('[name=porcen_descuento]').val(formatDecimal(response[0].porcen_descuento));
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

function save_doc_compra(data, action){

    let doc_com= get_data_cabecera_comprobante_compra();
    let doc_com_detalle= listaDetalleComprobanteCompra;
   
    if (action == 'register'){
        baseUrl = 'guardar_doc_compra';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_doc_compra';
    }

    console.log({'doc_com':doc_com, 'doc_com_detalle':doc_com_detalle});
    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: {'doc_com':doc_com, 'doc_com_detalle':doc_com_detalle},
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if (response['id_doc'] > 0){
                alert('Documento registrado con éxito');
                
                if (action == 'register'){
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
    var vardataTables = funcDatatables();
    $('#ListaGuiaRemision').DataTable({
        'info': false,
        'searching': false,
        'paging':   false,
        'language' : vardataTables[0],
        'bDestroy': true,
        'data':data,
        'columns': [
            {'data': 'nro_guia'},
            {'data': 'fecha_emision'},
            {'data': 'razon_social'},
            {'data': 'tipo_operacion'},
            {'render':
            function (data, type, row){
            return '';
            }
            },
        ]
        // 'columnDefs': [{ 'aTargets': [0,5], 'sClass': 'invisible'}],
    });
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
            listaDetalleComprobanteCompra[index].unitario = valor;
            let sub_total = (parseInt(listaDetalleComprobanteCompra[index].cantidad)*parseFloat(listaDetalleComprobanteCompra[index].unitario));
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
            listaDetalleComprobanteCompra[index].porcentaje_descuento = valor;
            let total = (parseInt(listaDetalleComprobanteCompra[index].cantidad)*parseFloat(listaDetalleComprobanteCompra[index].unitario));
            let montoDescuento=(parseFloat(total)*parseFloat(valor))/100;
            tr.querySelector("input[name='total_descuento']").value=montoDescuento;
            let newTotal = (parseFloat(total)-parseFloat(montoDescuento));
            tr.querySelector("span[name='total']").textContent=newTotal;
            listaDetalleComprobanteCompra[index].total_descuento = montoDescuento;
            listaDetalleComprobanteCompra[index].total = newTotal;
        


        }
    });
    CalcSubTotal(listaDetalleComprobanteCompra);

    // console.log(listaDetalleComprobanteCompra);
}

function resetPorcentajeDescuento(e){
    let tr= e.currentTarget.parentElement.parentElement;
    let id_guia_com_det= e.target.dataset.id;

    tr.querySelector("input[name='porcentaje_descuento']").value=0;
    listaDetalleComprobanteCompra.forEach((element, index) => {
        if (element.id == id_guia_com_det) {
            listaDetalleComprobanteCompra[index].porcentaje_descuento = 0;
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
            listaDetalleComprobanteCompra[index].total_descuento = valor;
            let newTotal= parseFloat(listaDetalleComprobanteCompra[index].cantidad * listaDetalleComprobanteCompra[index].unitario)-parseFloat(valor)
            tr.querySelector("span[name='total']").textContent=newTotal;
            listaDetalleComprobanteCompra[index].total = newTotal;

        }
    });
    CalcSubTotal(listaDetalleComprobanteCompra);

}

function llenarTablaListaDetalleGuiaCompra(data){
    var vardataTables = funcDatatables();
    $('#listaDetalleComprobanteCompra').DataTable({
        'info': false,
        'searching': false,
        'paging':   false,
        'language' : vardataTables[0],
        'bDestroy': true,
        'data':data,
        'columns': [
            {'data': 'nro_guia'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'cantidad'},
            {'data': 'unidad_medida'},
            {'render':
            function (data, type, row){
                return  `<input type="text" class="form-control" name="unitario" data-id="${row.id}" onkeyup ="updateUnitario(event);" value="${row.unitario?row.unitario:''}" style="
                width: 80px;">`;
            }
            },
            {'render':
            function (data, type, row){
                return  `<input type="text" class="form-control" name="porcentaje_descuento" data-id="${row.id}" onkeyup ="updatePorcentajeDescuento(event);" value="${row.porcentaje_descuento?row.porcentaje_descuento:''}" style="
                width: 40px;">`;
            }
            },
            {'render':
            function (data, type, row){
                return  `<input type="text" class="form-control" name="total_descuento" data-id="${row.id}" onkeyup ="updateTotalDescuento(event);" value="${row.total_descuento?row.total_descuento:''}" style="
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
    console.log(data);
    if(data.guia.length > 0){
        data.guia.forEach(element => {
            listaGuiaRemision.push(
                {
                    'nro_guia':'GR-'+element.serie+'-'+element.numero,
                    'id_guia':element.id_guia_com,
                    'id_operacion':element.id_operacion,
                    'tipo_operacion':element.tipo_operacion, 
                    'id_proveedor':element.id_proveedor,
                    'razon_social':element.razon_social,
                    'fecha_emision':element.fecha_emision,
                    'subtotal':null,
                    'total':null,
                    'porcentaje_descuento':0,
                    'total_descuento':0,
                    'importe_total':null
                }
            )
        });
    }
    if(data.guia_detalle.length > 0){
        data.guia_detalle.forEach(element => {
            listaDetalleComprobanteCompra.push(
                {
                    'id':element.id_guia_com_det,
                    'id_item':element.id_item,
                    'id_guia':element.id_guia_com,
                    'nro_guia':'GR-'+data.guia[0].serie+'-'+data.guia[0].numero,
                    'codigo':element.codigo,
                    'descripcion':element.descripcion,
                    'cantidad':element.cantidad,
                    'unitario':element.unitario,
                    'sub_total':(parseInt(element.cantidad) * parseFloat(element.unitario)),
                    'id_unid_med':element.id_unid_med,
                    'unidad_medida':element.unidad_medida,
                    'porcentaje_descuento':0,
                    'total_descuento':0,
                    // 'precio_total':'',
                    'total':element.total,
                    
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
    data.forEach(element => {
        subtotal+=parseFloat(element.total);
    });
    listaGuiaRemision[0]['subtotal']=subtotal;
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='sub_total']").value=subtotal;

    CalcTotal();
}

function calcTotalPorcentajeDescuento(event){
    let porcentaje_descuento = event.target.value;
    let subtotal = document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='sub_total']").value;
    let total_descuento = (subtotal*porcentaje_descuento)/100;
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_descuento']").value=total_descuento;
    listaGuiaRemision[0]['porcentaje_descuento']=porcentaje_descuento;
    listaGuiaRemision[0]['total_descuento']=total_descuento;

    CalcTotal();
}

function CalcTotal(){
    let subtotal = document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='sub_total']").value;
    let total_descuento =document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_descuento']").value;
    let total = subtotal - parseFloat(total_descuento);
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total']").value=total;
    listaGuiaRemision[0]['total']=total;

    calcIGV();
    
}

function calcIGV(){
    let porcen_igv =document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='porcen_igv']").value;
    let total = document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total']").value;
    let total_igv= (parseFloat(total) * parseInt(porcen_igv))/ 100;
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_igv']").value= total_igv;
    listaGuiaRemision[0]['porcen_igv']=porcen_igv;
    listaGuiaRemision[0]['total_igv']=total_igv;

    calcImporteTotal();
}

function calcImporteTotal(){
    let total =document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total']").value;
    let total_igv =document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_igv']").value;
    let importe_total = (parseFloat(total)+parseFloat(total_igv)).toFixed(2);
    document.querySelector("table[id='TablaDetalleComprobanteCompra'] input[name='total_a_pagar']").value= importe_total;
    listaGuiaRemision[0]['importe_total']=importe_total;
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

// function anular_doc_compra(ids){
//     baseUrl = '/anular_doc_compra/'+ids;
//     $.ajax({
//         type: 'GET',
//         headers: {'X-CSRF-TOKEN': token},
//         url: baseUrl,
//         dataType: 'JSON',
//         success: function(response){
//             if (response.length > 0){
//                 alert('No es posible anular. '+response);
//             } else {
//                 changeStateButton('anular');
//                 // $('#estado label').text('Anulado');
//                 // $('[name=cod_estado]').val('7');
//                 mostrar_doc_com(ids);
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

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
//     var por = $('[name=porcen_descuento]').val();
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
//     // var dscto = parseFloat($('[name=total_descuento]').val());
//     // $('[name=porcen_igv]').val(18);
//     // var total = sub_total + dscto;
//     // var total_igv = total * 18/100;

//     // $('[name=sub_total]').val(sub_total);
//     // $('[name=total]').val(total);
//     // $('[name=total_igv]').val(total_igv);
//     // $('[name=total_a_pagar]').val(total + total_igv);

// }