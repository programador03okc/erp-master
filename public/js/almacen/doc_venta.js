function nuevo_doc_venta(){
    $('#form-doc_venta')[0].reset();
    $('[name=usuario]').val(auth_user.id_usuario);
    $('[name=id_tp_doc]').val(2).trigger('change.select2');
    $('[name=credito_dias]').attr('disabled',true);
    $('[name=id_guia_clas]').val(1);
    $('[name=moneda]').val(1);
    $('#nombre_usuario label').text(auth_user.nombres);
	$('#listaDetalle tbody').html('');
    $('#guias tbody').html('');
}
function mostrar_doc_venta(id_doc_ven){
    console.log(id_doc_ven);
    if (id_doc_ven !== null){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'mostrar_doc_venta/'+id_doc_ven,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=id_doc_ven]').val(response['doc'][0].id_doc_ven);
                $('[name=serie]').val(response['doc'][0].serie);
                $('#serie').text(response['doc'][0].serie);
                $('[name=numero]').val(response['doc'][0].numero);
                $('#numero').text(response['doc'][0].numero);
                $('[name=id_tp_doc]').val(response['doc'][0].id_tp_doc).trigger('change.select2');
                $('[name=fecha_emision]').val(response['doc'][0].fecha_emision);
                $('[name=fecha_vcmto]').val(response['doc'][0].fecha_vcmto);
                $('[name=cliente_razon_social]').val(response['doc'][0].razon_social);
                $('[name=id_cliente]').val(response['doc'][0].id_cliente);
                $('[name=id_contrib]').val(response['doc'][0].id_contribuyente);
                $('[name=credito_dias]').val(response['doc'][0].credito_dias);
                $('[name=id_condicion]').val(response['doc'][0].id_condicion).trigger('change.select2');
                $('[name=id_sede]').val(response['doc'][0].id_sede).trigger('change.select2');
                $('[name=moneda]').val(response['doc'][0].moneda).trigger('change.select2');
                $('[name=cod_estado]').val(response['doc'][0].estado);
                $('#estado label').text(response['doc'][0].estado_doc);
                $('#nombre_usuario label').text(response['doc'][0].nombre_corto);
                $('#fecha_registro label').text(response['doc'][0].fecha_registro);
                
                $('[name=sub_total]').val(response['doc'][0].sub_total);
                $('[name=total_igv]').val(response['doc'][0].total_igv);
                $('[name=total]').val(response['doc'][0].total);
                $('[name=porcen_descuento]').val(response['doc'][0].porcen_descuento);
                $('[name=total_descuento]').val(response['doc'][0].total_descuento);
                $('[name=total]').val(response['doc'][0].total);
                $('[name=total_ant_igv]').val(response['doc'][0].total_ant_igv);
                $('[name=total_a_pagar]').val(response['doc'][0].total_a_pagar);
                // listar_guias_emp(response['doc'][0].id_empresa);
                // listar_docven_guias(response['doc'][0].id_doc_ven);
                listar_detalle(response['doc'][0].id_doc_ven);

                localStorage.removeItem("id_doc_ven");
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });   
    }
}

function listar_docven_guias(id_doc){
    $('#guias tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_docven_guias/'+id_doc,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#guias tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_detalle(id_doc){
    $('#listaDetalle tbody').html('');
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'listar_docven_items/'+id_doc,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#listaDetalle tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function save_doc_venta(data, action){
    console.log(data);
    if (action == 'register'){
        baseUrl = 'guardar_doc_venta';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_doc_venta';
    }
    var msj = verificaCabecera();
    console.log(data);

    if (msj.length > 0){
        alert(msj);
    } else {
        $.ajax({
            type: 'POST',
            // headers: {'X-CSRF-TOKEN': token},
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Documento registrado con éxito');
                    // listar_guias_prov(response['id_proveedor']);
                    
                    $('[name=id_doc_ven]').val(response);
                    
                    if (action == 'register'){
                        $('[name=cod_estado]').val('1');
                        $('#estado label').text('Elaborado');
                    }
                    changeStateButton('guardar');
                    $('#form-doc_venta').attr('type', 'register');
                    changeStateInput('form-doc_venta', true);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });    
    }
}

function listar_guias_emp(id_empresa){
    console.log('id_empresa'+id_empresa);
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_guias_emp/'+id_empresa,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var option = '';
            for (var i=0;i<response.length;i++){
                option +='<option value="'+response[i].id_guia_ven+'">'+'GR-'+
                    response[i].serie+'-'+response[i].numero+' - '+response[i].razon_social+' - '+
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

function agrega_guia(){
    var id_guia = $('[name=id_guia]').val();
    var id_doc_ven = $('[name=id_doc_ven]').val();
    var id_sede = $('[name=id_sede]').val();
    console.log('id_guia'+id_guia+' id_doc_ven'+id_doc_ven+' id_sede'+id_sede);
    
    if (id_guia !== null){
        var rspta = confirm('¿Esta seguro que desea agregar los items de ésta guía?');
        if (rspta){
            var token = $('#token').val();
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: 'guardar_docven_items_guia/'+id_guia+'/'+id_doc_ven,
                dataType: 'JSON',
                success: function(response){
                    console.log('response'+response);
                    if (response > 0){
                        alert('Items registrados con éxito');
                        listar_detalle(id_doc_ven);
                        listar_docven_guias(id_doc_ven);
                        // listar_guias_prov(id_empresa);
                        $('[name=id_guia]').val('0').trigger('change.select2');
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

function anular_doc_venta(ids){
    baseUrl = 'anular_doc_venta/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Comprobante anulado con éxito');
                changeStateButton('anular');
                $('#estado label').text('Anulado');
                $('[name=cod_estado]').val('7');
                // clearForm('form-doc_venta');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_guia(id_guia,id_doc_ven_guia){
    var id_doc = $('[name=id_doc_ven]').val();
    console.log('id_guia'+id_guia+'id_doc'+id_doc);
    var anula = confirm("¿Esta seguro que desea anular ésta OC?\nSe quitará también la relación de sus Items");
    if (anula){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'anular_guiaven/'+id_doc+'/'+id_guia,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Guía anulada con éxito');
                    $("#doc-"+id_doc_ven_guia).remove();
                    listar_detalle(id_doc);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function ceros_numero(){
    var num = $('[name=numero]').val();
    $('[name=numero]').val(leftZero(7,num));
}

function next_serie_numero(){
    var id_sede = $('[name=id_sede]').val();
    var id_tp_doc = $('[name=id_tp_doc]').val();
    console.log('id_sede:'+id_sede+' id_tp_doc:'+id_tp_doc);
    var id = $('[name=id_doc_ven]').val();
    console.log('id: '+id);

    if (id == ''){
        $.ajax({
            type: 'GET',
            url: 'next_serie_numero_doc/'+id_sede+'/'+id_tp_doc,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response !== ''){
                    $('[name=serie]').val(response.serie);
                    $('[name=numero]').val(response.numero);
                    $('[name=id_serie_numero]').val(response.id_serie_numero);
                } else {
                    $('[name=serie]').val('');
                    $('[name=numero]').val('');
                    $('[name=id_serie_numero]').val('');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });    
    }
}

function copiar_items_occ_doc(id_occ){
    var id_doc = $('[name=id_doc_ven]').val();
    console.log('id_occ:'+id_occ+' id_doc:'+id_doc);
    $.ajax({
        type: 'GET',
        url: 'copiar_items_occ_doc/'+id_occ+'/'+id_doc,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            // if (response !== ''){
                $('#listaDetalle tbody').html(response);
            // }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function evalua_condicion(){
    var cond = $('[name=id_condicion]').val();
    if (cond == '1'){//contado
        $('[name=credito_dias]').val('');
        $('[name=credito_dias]').attr('disabled',true);
    } else {
        $('[name=credito_dias]').attr('disabled',false);
    }
}
function verificaCabecera(){
    var id_tp_doc = $('[name=id_tp_doc]').val();
    var serie = $('[name=serie]').val();
    var numero = $('[name=numero]').val();
    var id_sede = $('[name=id_sede]').val();
    var id_cliente = $('[name=id_cliente]').val();
    var id_condicion = $('[name=id_condicion]').val();
    var id_guia_clas = $('[name=id_guia_clas]').val();
    var moneda = $('[name=moneda]').val();
    var msj = '';

    if (id_tp_doc == '0'){
        msj+='\n Es necesario que elija un Tipo de Documento';
    }
    if (serie == ''){
        msj+='\n Es necesario que ingrese una Serie';
    }
    if (numero == ''){
        msj+='\n Es necesario que ingrese un Número';
    }
    if (id_sede == '0'){
        msj+='\n Es necesario que elija una Sede';
    }
    if (id_cliente == ''){
        msj+='\n Es necesario que seleccione un Cliente';
    }
    if (id_condicion == '0'){
        msj+='\n Es necesario que elija una Condición';
    }
    if (id_guia_clas == '0'){
        msj+='\n Es necesario que elija un Tipo de Clasificación';
    }
    if (moneda == '0'){
        msj+='\n Es necesario que elija una Moneda';
    }
    return msj;
}

function actualiza_totales(){
    var id_doc = $('[name=id_doc_ven]').val();
    console.log(' id_doc:'+id_doc);
    $.ajax({
        type: 'GET',
        url: 'actualiza_totales_doc_ven/'+id_doc,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            mostrar_doc_venta(id_doc);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}