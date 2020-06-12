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
});
function mostrar_doc_compra(id_doc_com){
    if (id_doc_com !== null){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: '/mostrar_doc_com/'+id_doc_com,
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

                listar_guias_prov(response[0].id_proveedor);
                // console.log(response[0].doc_com_det);
                
                if(response[0].doc_com_det.length > 0){
                    listar_doc_com_orden(response[0].id_doc_com)
                }else{
                    listar_doc_guias(response[0].id_doc_com);
                    listar_doc_items(response[0].id_doc_com);
                }
                
                localStorage.removeItem("id_doc_com");
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });   
    }
}
function listar_doc_guias(id_doc){
    $('#guias tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: '/listar_doc_guias/'+id_doc,
        dataType: 'JSON',
        success: function(response){
            $('#guias tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_doc_items(id_doc){
    $('#listaDetalle tbody').html('');
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: '/listar_doc_items/'+id_doc,
        dataType: 'JSON',
        success: function(response){
            $('#listaDetalle tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function save_doc_compra(data, action){
    // console.log(data);
    if (action == 'register'){
        baseUrl = '/guardar_doc_compra';
    } else if (action == 'edition'){
        baseUrl = '/actualizar_doc_compra';
    }
    var s = $('[name=total_a_pagar]').val();
    // console.log('total a pagar:'+s);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response['id_doc'] > 0){
                alert('Documento registrado con éxito');
                listar_guias_prov(response['id_proveedor']);
                $('[name=id_doc_com]').val(response['id_doc']);
                
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

function listar_guias_prov(id_proveedor){
    // console.log('id_proveedor'+id_proveedor);
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: '/listar_guias_prov/'+id_proveedor,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            var option = '';
            for (var i=0;i<response.length;i++){
                option +='<option value="'+response[i].id_guia+'">'+
                    response[i].guia+' - '+response[i].razon_social+' - '+
                    response[i].estado_doc+'</option>';
            }
            $('[name=id_guia]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function agrega_guia(id_guia){
    // var id_guia = $('[name=id_guia]').val();
    // var id_proveedor = $('[name=id_proveedor]').val();
    var id_doc_com = $('[name=id_doc_com]').val();
    // console.log('id_guia'+id_guia+' id_doc_com'+id_doc_com);
    
    if (id_guia !== null){
        var rspta = confirm('¿Esta seguro que desea agregar los items de ésta guía?');
        if (rspta){
            $.ajax({
                type: 'GET',
                url: '/guardar_doc_items_guia/'+id_guia+'/'+id_doc_com,
                dataType: 'JSON',
                success: function(response){
                    // console.log('response'+response);
                    if (response > 0){
                        alert('Items registrados con éxito');
                        listar_doc_items(id_doc_com);
                        listar_doc_guias(id_doc_com);
                        // listar_guias_prov(id_proveedor);
                        // $('[name=id_guia]').val('0').trigger('change.select2');
                        actualiza_totales();
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    } else {
        alert('Debe seleccionar una Guía');
    }
}

function anular_doc_compra(ids){
    baseUrl = '/anular_doc_compra/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response.length > 0){
                alert('No es posible anular. '+response);
            } else {
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

function anular_guia(id_guia,id_doc_com_guia){
    var id_doc = $('[name=id_doc_com]').val();
    // console.log('id_guia'+id_guia+'id_doc'+id_doc);
    var anula = confirm("¿Esta seguro que desea anular ésta OC?\nSe quitará también la relación de sus Items");
    if (anula){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: '/anular_guia/'+id_doc+'/'+id_guia,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Guía anulada con éxito');
                    $("#doc-"+id_doc_com_guia).remove();
                    listar_doc_items(id_doc);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
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
function actualiza_totales(){
    var por = $('[name=porcen_descuento]').val();
    var id = $('[name=id_doc_com]').val();
    var fecha = $('[name=fecha_emision]').val();
    $.ajax({
        type: 'GET',
        url: '/actualiza_totales_doc/'+por+'/'+id+'/'+fecha,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if (response > 0){
                mostrar_doc_compra(id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    // var sub_total = 0;
    // $('#listaDetalle tbody tr').each(function(e){
    //     var tds = parseFloat($(this).find("td input[name=precio_total]").val());
    //     sub_total += tds;
    // });
    // var dscto = parseFloat($('[name=total_descuento]').val());
    // $('[name=porcen_igv]').val(18);
    // var total = sub_total + dscto;
    // var total_igv = total * 18/100;

    // $('[name=sub_total]').val(sub_total);
    // $('[name=total]').val(total);
    // $('[name=total_igv]').val(total_igv);
    // $('[name=total_a_pagar]').val(total + total_igv);

}