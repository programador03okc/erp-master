
let documentos = [];
let guias_detalle = [];

function nuevo_prorrateo(){
    $('#listaProrrateos tbody').html('');
    $('#listaGuiaDetalleProrrateo tbody').html('');
    documentos = [];
    guias_detalle = [];

    $('[name=id_prorrateo]').val('');
    $('[name=total_suma]').val('');
    $('[name=total_valor]').val('');
    $('[name=total_items]').val('');
    $('[name=total_adicional]').val('');
    $('[name=total_costo]').val('');
}
//PRORRATEO
function open_doc_prorrateo(){
    $('#modal-doc_prorrateo').modal({
        show: true
    });
    limpiarCampos();
}

function limpiarCampos(){
    $('[name=id_doc_com]').val('');
    $('[name=id_tp_prorrateo]').val('');
    $('[name=pro_serie]').val('');
    $('[name=pro_numero]').val('');
    $('[name=doc_fecha_emision]').val(fecha_actual());
    $('[name=tipo_cambio]').val(0);
    $('[name=id_moneda]').val(0);
    $('[name=sub_total]').val(0);
    $('[name=importe]').val(0);
    $('[name=importe_aplicado]').val(0);
    $('[name=doc_razon_social]').val('');
    $('[name=doc_id_proveedor]').val('');
    $('[name=id_tp_documento]').val('').trigger('change.select2');
    $('[name=id_contrib]').val('');
}

$("#form-doc_prorrateo").on("submit", function(){
    var data = JSON.stringify($(this).serializeArray());
    console.log(data);
    guardar_doc_prorrateo();
    // mostrar_guias_detalle();

});

function guardar_doc_prorrateo(){
    let id = ($('[name=id_doc_com]').val() !== '' ? $('[name=id_doc_com]').val() : (documentos.length + 1));

    let doc = documentos.find(doc => doc.id_doc_com == id);

    if (doc == undefined || doc == null){
        let nuevo = {
            'id_doc_com':id,
            'id_tp_prorrateo':$('[name=id_tp_prorrateo]').val(),
            'tp_prorrateo':$('select[name="id_tp_prorrateo"] option:selected').text(),
            'id_proveedor':$('[name=doc_id_proveedor]').val(),
            'razon_social':$('[name=doc_razon_social]').val(),
            'fecha_emision':$('[name=doc_fecha_emision]').val(),
            'id_tp_documento':$('[name=id_tp_documento]').val(),
            'serie':$('[name=pro_serie]').val(),
            'numero':$('[name=pro_numero]').val(),
            'id_moneda':$('[name=id_moneda]').val(),
            'total':$('[name=sub_total]').val(),
            'tipo_cambio':$('[name=tipo_cambio]').val(),
            'importe':$('[name=importe]').val(),
            'importe_aplicado':$('[name=importe_aplicado]').val(),
            'id_tipo_prorrateo':$('[name=id_tipo_prorrateo]').val(),
            'tipo_prorrateo':$('select[name="id_tipo_prorrateo"] option:selected').text(),
        }
        documentos.push(nuevo);
        
    } else {
            doc.id_tp_prorrateo = $('[name=id_tp_prorrateo]').val();
            doc.tp_prorrateo = $('select[name="id_tp_prorrateo"] option:selected').text();
            doc.id_proveedor = $('[name=doc_id_proveedor]').val();
            doc.razon_social = $('[name=doc_razon_social]').val();
            doc.fecha_emision = $('[name=doc_fecha_emision]').val();
            doc.id_tp_documento = $('[name=id_tp_documento]').val();
            doc.serie = $('[name=pro_serie]').val();
            doc.numero = $('[name=pro_numero]').val();
            doc.id_moneda = $('[name=id_moneda]').val();
            doc.total = $('[name=sub_total]').val();
            doc.tipo_cambio = $('[name=tipo_cambio]').val();
            doc.importe = $('[name=importe]').val();
            doc.importe_aplicado = $('[name=importe_aplicado]').val();
            doc.id_tipo_prorrateo = $('[name=id_tipo_prorrateo]').val();
            doc.tipo_prorrateo = $('select[name="id_tipo_prorrateo"] option:selected').text();
    }
    mostrar_documentos();

    $('#modal-doc_prorrateo').modal('hide');
}

function changeMoneda(){
    getTipoCambio();
    calculaImporte();
}

function calculaImporte(){
    var moneda = $('[name=id_moneda]').val();
    var sub_total = $('[name=sub_total]').val();
    if (moneda == 2){
        var tcambio = $('[name=tipo_cambio]').val();
        if (tcambio == null || tcambio == '' || tcambio == '0'){
            getTipoCambio();
            tcambio = $('[name=tipo_cambio]').val();
        }
        var imp = formatDecimal(sub_total * tcambio);
        $('[name=importe]').val(imp);
        $('[name=importe_aplicado]').val(imp);
    } else {
        $('[name=importe]').val(sub_total);
        $('[name=importe_aplicado]').val(sub_total);
    }
}

function editar_documento(id_doc_com){
    $('#modal-doc_prorrateo').modal({
        show: true
    });
    let doc = documentos.find(doc => doc.id_doc_com == id_doc_com);

    $('[name=id_doc_com]').val(doc.id_doc_com);
    $('[name=id_tp_prorrateo]').val(doc.id_tp_prorrateo);
    $('[name=pro_serie]').val(doc.serie);
    $('[name=pro_numero]').val(doc.numero);
    $('[name=doc_fecha_emision]').val(doc.fecha_emision);
    $('[name=tipo_cambio]').val(doc.tipo_cambio);
    $('[name=id_moneda]').val(doc.id_moneda);
    $('[name=sub_total]').val(doc.total);
    $('[name=importe]').val(doc.importe);
    $('[name=importe_aplicado]').val(doc.importe_aplicado);
    $('[name=doc_razon_social]').val(doc.razon_social);
    $('[name=doc_id_proveedor]').val(doc.id_proveedor);
    $('[name=id_tp_documento]').val(doc.id_tp_documento).trigger('change.select2');
    $('[name=id_contrib]').val('');
}

function mostrar_prorrateo(id_prorrateo){
    
    $('#listaProrrateos tbody').html('');
    $('#listaGuiaDetalleProrrateo tbody').html('');
    
    documentos = [];
    guias_detalle = [];

    $.ajax({
        type: 'GET',
        url: 'mostrar_prorrateo/'+id_prorrateo,
        dataType: 'JSON',
        success: function(response){
            console.log(response);

            $('[name=codigo]').text(response['prorrateo'].codigo);
            $('#estado_doc').text((response['prorrateo'].estado==1 ? "Activo" : "Inactivo"));

            response['documentos'].forEach(element => {
                
                documentos.push({
                    'id_doc_com'        :element.id_doc_com,
                    'id_tp_prorrateo'   :element.id_tp_prorrateo,
                    'tp_prorrateo'      :element.descripcion,
                    'id_proveedor'      :element.id_proveedor,
                    'razon_social'      :element.razon_social,
                    'fecha_emision'     :element.fecha_emision,
                    'id_tp_documento'   :element.id_tp_doc,
                    'serie'             :element.serie,
                    'numero'            :element.numero,
                    'id_moneda'         :element.moneda,
                    'total'             :element.total_a_pagar,
                    'tipo_cambio'       :element.tipo_cambio,
                    'importe'           :(element.total_a_pagar * element.tipo_cambio),
                    'importe_aplicado'  :element.importe_aplicado,
                });
            });

            mostrar_documentos();

            response['detalles'].forEach(element => {
                
                guias_detalle.push({
                    'id_guia_com_det'   :element.id_guia_com_det,
                    'serie'             :element.serie,
                    'numero'            :element.numero,
                    'codigo'            :element.codigo,
                    'part_number'       :element.part_number,
                    'descripcion'       :element.descripcion,
                    'cantidad'          :element.cantidad,
                    'abreviatura'       :element.abreviatura,
                    'valorizacion'      :element.valorizacion,
                    'adicional'         :element.importe,
                    'total'             :(parseFloat(element.valorizacion) + parseFloat(element.importe)),
                });
            });

            mostrar_guias_detalle();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_documentos(){
    let tr = '';
    let i = 0;
    let total_aplicado_valor = 0;
    let total_aplicado_peso = 0;

    // let edition = ($("#form-prorrateo").attr('type') == 'edition' ? true : false);
    
    documentos.forEach(element => {
        i++;
        
        if (element.id_tipo_prorrateo == 1){
            total_aplicado_valor += parseFloat(element.importe_aplicado);
        }
        else if (element.id_tipo_prorrateo == 2){
            total_aplicado_peso += parseFloat(element.importe_aplicado);
        }
        tr += `<tr>
            <td>${i}</td>
            <td>${element.tp_prorrateo}</td>
            <td>${element.serie+'-'+element.numero}</td>
            <td>${element.fecha_emision}</td>
            <td class="right">${element.id_moneda==1 ? 'S/' : '$'}</td>
            <td class="right">${element.total}</td>
            <td class="right">${element.tipo_cambio}</td>
            <td class="right">${element.importe}</td>
            <td class="right">${element.importe_aplicado}</td>
            <td class="right">${element.tipo_prorrateo}</td>
            <td style="display:flex;">
                <button type="button" class="editar btn btn-primary btn-xs activation" data-toggle="tooltip" 
                    data-placement="bottom" title="Editar" onClick="editar_documento(${element.id_doc_com});"
                    >  <i class="fas fa-pen"></i></button>
                <button type="button" class="anular btn btn-danger btn-xs activation" data-toggle="tooltip" 
                    data-placement="bottom" title="Eliminar" onClick="anular_documento('${element.id_doc_com}');"
                    >  <i class="fas fa-trash"></i></button>
            </td>
        </tr>`;
    });
    // ${edition ? '' : 'disabled="true"'}
    // <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
    // title="Editar" onClick="editar_documento('${element.id_doc_com}');"></i>
    // <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
    // title="Anular" onClick="anular_documento('${element.id_doc_com}');"></i>
    
    $('[name=total_valor]').val(total_aplicado_valor);
    $('[name=total_peso]').val(total_aplicado_peso);

    $('#listaProrrateos tbody').html(tr);
    
    mostrar_guias_detalle();
}

function anular_documento(id_doc_com){
    let elimina = confirm("¿Esta seguro que desea eliminar éste documento?");
    
    if (elimina){
        var index = documentos.findIndex(function(item, i){
            return item.id_doc_com == id_doc_com;
        });
        documentos.splice(index,1);
        console.log(documentos);
        mostrar_documentos();
    }
    
}

function listar_guia_detalle(id_guia){
    console.log('id_guia'+id_guia);
    
    $.ajax({
        type: 'GET',
        url: 'listar_guia_detalle/'+id_guia,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            let id = null;
            response.forEach(element => {
                id = guias_detalle.find(guia => guia.id_guia_com_det == element.id_guia_com_det);
                
                if (id == undefined || id == null){
                    // guias_detalle.push(element);
                    guias_detalle.push({
                        'id_guia_com_det'   :element.id_guia_com_det,
                        'serie'             :element.serie,
                        'numero'            :element.numero,
                        'codigo'            :element.codigo,
                        'part_number'       :element.part_number,
                        'descripcion'       :element.descripcion,
                        'cantidad'          :element.cantidad,
                        'abreviatura'       :element.abreviatura,
                        'valorizacion'      :(parseFloat(element.precio_unitario!==null ? element.precio_unitario : element.unitario) * parseFloat(element.cantidad)),
                        'adicional'         :0,
                        'total'             :(parseFloat(element.precio_unitario!==null ? element.precio_unitario : element.unitario) * parseFloat(element.cantidad)),
                    });
                }
            });
            mostrar_guias_detalle();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_guias_detalle(){
    
    $('#listaDetalleProrrateo tbody').html('');

    var html = '';
    let importe_doc = $('[name=total_valor]').val();
    console.log('importe_doc'+importe_doc);
    // let tipo_cambio = $('[name=tipo_cambio]').val();
    let suma_total = 0;

    guias_detalle.forEach(element => {
        suma_total += parseFloat(element.valorizacion);
    });
    let valor = parseFloat(importe_doc!=='' ? importe_doc : 0) / (suma_total > 0 ? suma_total : 1);
    let adicional = 0;
    let total = 0;

    let total_valorra = 0;
    let total_adicional = 0;
    let total_prorrateado = 0;

    // let edition = ($("#form-prorrateo").attr('type') == 'edition' ? true : false);

    guias_detalle.forEach(element => {

        // if (element.adicional == undefined || element.adicional == null){
        adicional = parseFloat(element.valorizacion) * parseFloat(valor);
        total = parseFloat(element.valorizacion) + parseFloat(adicional);

        element.adicional = adicional;
        element.total = total;
        // }
        total_valorra += parseFloat(element.valorizacion);
        total_adicional += parseFloat(element.adicional);
        total_prorrateado += parseFloat(element.total);
        console.log(element);
        
        html += `
        <tr id="${element.id_guia_com_det}">
            <td>${element.serie+'-'+element.numero}</td>
            <td>${element.codigo}</td>
            <td>${element.part_number!==null ? element.part_number : ''}</td>
            <td>${element.descripcion}</td>
            <td>${element.cantidad}</td>
            <td>${element.abreviatura}</td>
            <td class="right" style="width: 110px;">${formatDecimalDigitos(element.valorizacion,3)}</td>
            <td class="right" style="width: 110px;">${formatDecimalDigitos(element.adicional,3)}</td>
            <td class="right" style="width: 110px;">${formatDecimalDigitos(element.total,3)}</td>
            <td style="display:flex;">
                <button type="button" class="anular btn btn-danger btn-xs activation" data-toggle="tooltip" 
                    data-placement="bottom" title="Eliminar" onClick="anular_item('${element.id_guia_com_det}');"
                    >  <i class="fas fa-trash"></i></button>
            </td>
        </tr>`;
    });
                // <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
                //     title="Anular" onClick="anular_item('${element.id_guia_com_det}');"></i>
                
                // ${edition ? '' : 'disabled="true"'}

            // <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="subtotal" 
            // onChange="calcula_importe('${element.id_guia_com_det}');" value="${formatDecimalDigitos(element.valorizacion,3)}" /></td>
            // <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="adicional" 
            // onChange="calcula_importe('${element.id_guia_com_det}');" value="${formatDecimalDigitos(element.adicional,3)}" /></td>
            // <td style="width: 110px;"><input type="number" style="width:100px;" class="right" name="total" 
            // value="${formatDecimalDigitos(element.total,3)}" /></td>
    
    $('#listaGuiaDetalleProrrateo tbody').html(html);

    $('[name=total_suma]').val(formatDecimalDigitos(total_valorra,3));
    $('[name=total_adicional]').val(formatDecimalDigitos(total_adicional,3));
    $('[name=total_costo]').val(formatDecimalDigitos(total_prorrateado,3));

}

function anular_item(id_guia_com_det){
    let elimina = confirm("¿Esta seguro que desea eliminar éste item?");
    
    if (elimina){
        var index = guias_detalle.findIndex(function(item, i){
            return item.id_guia_com_det == id_guia_com_det;
        });
        guias_detalle.splice(index,1);
        console.log(guias_detalle);
        mostrar_guias_detalle();
    }
}

// function listar_docs_prorrateo(id_guia){
//     $.ajax({
//         type: 'GET',
//         url: 'listar_docs_prorrateo/'+id_guia,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             $('#listaProrrateos tbody').html(response['html']);
//             $('[name=total_valor]').val(response['total_valor']);
//             $('[name=total_items]').val(response['total_items']);

//             if (response['moneda'] !== null){
//                 console.log(response['moneda']);
//                 console.log(response['moneda'].descripcion+' '+response['moneda'].simbolo);
//                 $('#moneda').text(response['moneda'].descripcion+' '+response['moneda'].simbolo);
//             }
//             console.log('total_valor:'+response['total_valor']);
//             listar_detalle_prorrateo(id_guia, response['total_valor']);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

// function listar_detalle_prorrateo(guia, total_valor){
//     $('#listaDetalleProrrateo tbody').html('');
//     console.log('id_guia'+guia);
//     console.log('total_valor'+total_valor);
//     console.log();
//     var baseUrl = 'listar_guia_detalle_prorrateo/'+guia+'/'+total_valor;
//     $.ajax({
//         type: 'GET',
//         url: baseUrl,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response['sumas']);
//             $('#listaDetalleProrrateo tbody').html(response['html']);
//             $('[name=total_suma]').val(response['sumas'][0].suma_total);
//             $('[name=total_adicional]').val(response['sumas'][0].suma_adicional);
//             $('[name=total_costo]').val(response['sumas'][0].suma_costo);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

// function editar_adicional(id){
//     $("#"+id+" td").find("input[name=subtotal]").attr('disabled',false);
//     $("#"+id+" td").find("input[name=tipocambio]").attr('disabled',false);
//     // $("#"+id+" td").find("input[name=importe]").attr('disabled',false);
//     $("#"+id+" td").find("i.blue").removeClass('visible');
//     $("#"+id+" td").find("i.blue").addClass('oculto');
//     $("#"+id+" td").find("i.green").removeClass('oculto');
//     $("#"+id+" td").find("i.green").addClass('visible');
// }

// function calcula_importe(id){
//     var subtotal = $('#'+id+' input[name=subtotal]').val();
//     var adicional = $('#'+id+' input[name=adicional]').val();

//     if (subtotal !== '' && adicional !== ''){
//         let guia_det = guias_detalle.find(element => element.id_guia_com_det == id);
//         guia_det.
        // $('#'+id+' input[name=total]').val(formatDecimal(subtotal * adicional));
    // } else {
        // $('#'+id+' input[name=total]').val(0);
//     }
// }

// function anular_adicional(id,id_doc){
//     var anula = confirm("¿Esta seguro que desea anular éste adicional?");
//     if (anula){
//         $.ajax({
//             type: 'GET',
//             headers: {'X-CSRF-TOKEN': token},
//             url: 'eliminar_doc_prorrateo/'+id+'/'+id_doc,
//             dataType: 'JSON',
//             success: function(response){
//                 console.log(response);
//                 if (response > 0){
//                     alert('Adicional anulado con éxito');
//                     // $("#det-"+id).remove();
//                     var id = $('[name=id_guia]').val();
//                     console.log('id:'+id);
//                     listar_docs_prorrateo(id);
//                 }
//             }
//         }).fail( function( jqXHR, textStatus, errorThrown ){
//             console.log(jqXHR);
//             console.log(textStatus);
//             console.log(errorThrown);
//         });
//     }
// }

// function update_adicional(id,id_doc){
//     var subtotal = $("#det-"+id+" td").find("input[name=subtotal]").val();
//     var tipocambio = $("#det-"+id+" td").find("input[name=tipocambio]").val();
//     var importe = $("#det-"+id+" td").find("input[name=importedet]").val();
//     var data =  'id_prorrateo='+id+
//                 '&id_doc='+id_doc+
//                 '&sub_total='+subtotal+
//                 '&tipo_cambio='+tipocambio+
//                 '&importe='+importe;
//     console.log(data);
//     $.ajax({
//         type: 'POST',
//         // headers: {'X-CSRF-TOKEN': token},
//         url: 'update_doc_prorrateo',
//         data: data,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             if (response > 0){
//                 alert('Adicional actualizado con éxito');
//                 $("#det-"+id+" td").find("input[name=subtotal]").attr('disabled',true);
//                 $("#det-"+id+" td").find("input[name=tipocambio]").attr('disabled',true);
//                 // $("#det-"+id+" td").find("input[name=importe]").attr('disabled',false);
//                 $("#det-"+id+" td").find("i.blue").removeClass('oculto');
//                 $("#det-"+id+" td").find("i.blue").addClass('visible');
//                 $("#det-"+id+" td").find("i.green").removeClass('visible');
//                 $("#det-"+id+" td").find("i.green").addClass('oculto');
//                 var id = $('[name=id_guia]').val();
//                 console.log('despues id_guia:'+id);
//                 listar_docs_prorrateo(id);
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function getTipoCambio(){
    var fecha = $('[name=doc_fecha_emision]').val();
    if (fecha !== null && fecha !== ''){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'tipo_cambio_compra/'+fecha,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=tipo_cambio]').val(response);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function save_prorrateo(data, action){
    console.log(data);
    console.log(action);

    if (action == 'register'){
        baseUrl = 'guardarProrrateo';
    } 
    else if (action == 'edition'){
        baseUrl = 'actualizarProrrateo';
    }
    
    data =  'documentos='+JSON.stringify(documentos)+
            '&guias_detalle='+JSON.stringify(guias_detalle);
    $.ajax({
        type: 'POST',
        url: 'guardarProrrateo',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function copiar_unitario(){
//     $('[name=prorrateo]').prop('checked',true);
//     var p = $('[name=prorrateo]').val();
//     console.log(p);
//     var id_guia = $('[name=id_guia]').val();
//     var id = [];
//     var uni = [];
//     var r = 0;
    
//     $('#listaDetalleProrrateo tbody tr').each(function(e){
//         var pro = $(this)[0].id.split("-");
//         var tds = parseFloat($(this).find("td input[name=unit]").val());
//         console.log('unitario:'+tds);
//         console.log('id_guia_com_det:'+pro[1]);
//         id[r] = pro[1];
//         uni[r] = tds;
//         r++;
//     });
//     var data =  'id_guia='+id_guia+
//                 '&id_guia_com_det='+id+
//                 '&unitario='+uni;
//     console.log(data);
//     $.ajax({
//         type: 'POST',
//         url: 'update_guia_detalle_adic',
//         data: data,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             if (response > 0){
//                 alert('Item guardado con éxito');
//                 listar_detalle(id_guia);
//             }
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function agregar_tipo(){
    var nombre = prompt('Ingrese el Nombre del nuevo tipo','');
    console.log(nombre);
    if (nombre !== null){
        var rspta = confirm("¿Está seguro que desea agregar éste tipo: "+nombre+"?");
        if (rspta){
            $.ajax({
                type: 'GET',
                url: 'guardar_tipo_prorrateo/'+nombre,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    $('[name=id_tp_prorrateo]').html('');
                    var html = '<option value="0" disabled>Elija una opción</option>'+response;
                    $('[name=id_tp_prorrateo]').html(html);
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });        
        }
    }
}

//PRORRATEO ITEMS
// function prorrateo_items(id_prorrateo,importe){
//     $('#modal-guia_com_det').modal({
//         show: true
//     });
//     listar_guia_detalle(id_prorrateo);
//     $('[name=id_prorrateo]').val(id_prorrateo);
//     $('[name=importe_prorrateo]').val(importe);
// }
// function listar_guia_detalle(id_prorrateo){
//     var id = $('[name=id_guia]').val();
//     console.log('id'+id);
//     $.ajax({
//         type: 'GET',
//         url: 'mostrar_guia_detalle/'+id+'/'+id_prorrateo,
//         dataType: 'JSON',
//         success: function(response){
//             console.log(response);
//             $('#listaGuiaDetalle tbody').html(response);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }
function guardar_prorrateo_detalle(){
    console.log('guardar_prorrateo_detalle');
    var id_guia_com_det = [];
    var total_det = [];
    var id_prorrateo = $('[name=id_prorrateo]').val();
    var imp_comp = $('[name=importe_prorrateo]').val();
    var r = 0;
    var suma_total = 0;

    $("input[type=checkbox]:checked").each(function(){
        id_guia_com_det[r] = $(this)[0].parentElement.parentElement.id;
        var columnas = $(this)[0].parentElement.parentElement.querySelectorAll("td");
        var imp = parseFloat(columnas[6].innerHTML);
        total_det[r] = imp;
        console.log(imp);
        suma_total += imp;
        ++r;
    });
    var data =  'id_guia_com_det='+id_guia_com_det+
                '&total_det='+total_det+
                '&id_prorrateo='+id_prorrateo+
                '&importe_comp='+imp_comp+
                '&suma_total='+suma_total;
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'guardar_prorrateo_detalle',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#modal-guia_com_det').modal('hide');
            $('[name=total_items]').val(imp_comp);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function ceros_numero(numero){
    if (numero == 'pro_numero'){
        var num = $('[name=pro_numero]').val();
        $('[name=pro_numero]').val(leftZero(7,num));
    }
}