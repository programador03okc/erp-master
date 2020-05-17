let id_almacen = null;

function nuevo_guia_venta(){
    $('#form-general')[0].reset();

    $('[name=id_guia_ven]').val('');
    $('[name=usuario]').val(auth_user.id_usuario).trigger('change.select2');
    $('[name=id_tp_doc_almacen]').val(5).trigger('change.select2');
    $('#nombre_usuario label').text(auth_user.nombres);

    $('#tp_doc_almacen').text('');
    $('#serie').text('');
    $('#numero').text('');
    $('#codigo_trans').text('');

    $('#listaDetalle tbody').html('');
    $('#oc tbody').html('');
}
$(function(){
    var id_guia_ven = localStorage.getItem("id_guia_ven");
    
    if (id_guia_ven !== null){
        mostrar_guia_ven(id_guia_ven);
        localStorage.removeItem("id_guia_ven");
        changeStateButton('historial');
    }
    $("#tab-guia_venta section:first form").attr('form', 'formulario');

    /* Efecto para los tabs */
    $('ul.nav-tabs li a').click(function(){
        $('ul.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.content-tabs section').attr('hidden', true);
        $('.content-tabs section form').removeAttr('type');
        $('.content-tabs section form').removeAttr('form');

        var activeTab = $(this).attr('type');
        var activeForm = "form-"+activeTab.substring(1);

        $("#"+activeForm).attr('type', 'register');
        $("#"+activeForm).attr('form', 'formulario');
        changeStateInput(activeForm, true);

        // $("[name=usuario]").val(3);
        // $('[name=nombre_usuario]').val('Rocio Condori Palomino');
        var id = $('[name=id_guia_ven]').val();
        if (activeForm == "form-detalle" || activeForm == "form-transportista"){
            clearDataTable();
        }
        actualizar_tab(activeForm, id);
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });
    $("#form-obs").on("submit", function(e){
        console.log('submit');
        e.preventDefault();
        var data = $(this).serialize();
        console.log(data);
        guardar_observacion(data);
    });
});
function actualizar_tab(activeForm, id){
    console.log(id+'id');
    if (id !== null){
        if (activeForm == "form-general"){
            mostrar_guia_ven(id);
        } 
        else if (activeForm == "form-detalle"){
            listar_detalle(id);
        }
        else if (activeForm == "form-transportista"){
            // listar_transportista(id);
            $('[name=id_guia_ven]').val(id);
        }
    }
}
function mostrar_guia_ven(id){
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_guia_venta/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            id_almacen = response[0].id_almacen;
            $('[name=id_guia_ven]').val(response[0].id_guia_ven);
            // $('[name=id_tp_doc]').val(response[0].id_tp_doc);
            $('[name=id_tp_doc_almacen]').val(response[0].id_tp_doc_almacen).trigger('change.select2');
            $('[name=serie]').val(response[0].serie);
            $('#tp_doc_almacen').text(response[0].tp_doc_almacen);
            $('#serie').text(response[0].serie);
            $('[name=numero]').val(response[0].numero);
            $('#numero').text(response[0].numero);
            $('#codigo_trans').text(response[0].codigo_trans);
            $('[name=id_transferencia]').val(response[0].id_transferencia);
            $('[name=id_sede]').val(response[0].id_sede).trigger('change.select2');
            $('[name=id_almacen]').val(response[0].id_almacen).trigger('change.select2');
            $('[name=id_motivo]').val(response[0].id_motivo).trigger('change.select2');
            $('[name=id_operacion]').val(response[0].id_operacion).trigger('change.select2');
            $('[name=fecha_emision]').val(response[0].fecha_emision);
            $('[name=fecha_almacen]').val(response[0].fecha_almacen);
            $('[name=fecha_traslado]').val(response[0].fecha_traslado);
            $('[name=usuario]').val(response[0].usuario).trigger('change.select2');
            $('[name=id_cliente]').val(response[0].id_cliente);
            $('[name=id_contrib]').val(response[0].id_contribuyente);
            $('[name=cliente_razon_social]').val(response[0].cliente_razon_social);
            $('[name=tra_serie]').val(response[0].tra_serie);
            $('[name=tra_numero]').val(response[0].tra_numero);
            $('[name=punto_partida]').val(response[0].punto_partida);
            $('[name=punto_llegada]').val(response[0].punto_llegada);
            $('[name=transportista]').val(response[0].transportista).trigger('change.select2');
            $('[name=placa]').val(response[0].placa);
            $('[name=cod_estado]').val(response[0].estado);
            $('[name=usuario]').val(response[0].usuario);
            $('#nombre_usuario label').text(response[0].nombre_trabajador);
            $('#fecha_registro label').text('');
            $('#fecha_registro label').append(formatDateHour(response[0].fecha_registro));
            $('#estado label').text('');
            $('#estado label').append(response[0].estado_doc);
            $('#registrado_por label').text('');
            $('#registrado_por label').append(response[0].nombre_corto);
            
            $('#des_estado').text(response[0].estado_doc);
            var des='';
            if (response[0].estado == 1){
                des = 'label label-primary';
            } else if (response[0].estado == 7){
                des = 'label label-danger';
            } else if (response[0].estado == 9){
                des = 'label label-success';
            }
            $('#des_estado').addClass(des);
            // var tipo = $('[name=tipo]').val();
            // console.log(tipo+'tipo');
            // if (tipo == 1){//Guia de Compra
                if (response[0].id_almacen !== null){
                    listar_guias_almacen(response[0].id_almacen);
                }
            // }
            
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_guia_venta(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_guia_venta';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_guia_venta';
    }
    var msj = validaCabecera();
    console.log(data);

    if (msj.length > 0){
        alert(msj);
    } 
    else {
        console.log('guardar_guia_venta');
        $.ajax({
            type: 'POST',
            // headers: {'X-CSRF-TOKEN': token},
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response['id_guia_ven'] > 0){
                    alert('Guía de Remisión registrada con éxito');
                    $('[name=tipo]').val('1').trigger('change.select2');
                    if (action == 'register'){
                        console.log(response['id_guia_ven']);
                        mostrar_guia_ven(response['id_guia_ven']);
                    }    
                }
                changeStateButton('guardar');
                $('#form-general').attr('type', 'register');
                changeStateInput('form-general', true);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function open_guia_ven_obs(id_guia){
    $('#modal-guia_ven_obs').modal({
        show: true
    });
    $('[name=id_guia_ven]').val(id_guia);
}
function anular_guia_venta(ids){
    open_guia_ven_obs(ids);
}
function guardar_observacion(data){
    console.log('guardar observacion');
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'anular_guia_venta',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.length > 0){
                alert(response);
            }
            $('#estado label').text('Anulado');
            $('[name=cod_estado]').val('2');
            changeStateButton('anular');
            $('#modal-guia_ven_obs').modal('hide');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function generar_salida(){
    var id_guia = $('[name=id_guia_ven]').val();
    
    if (id_guia !== ''){
        var estado = $('[name=cod_estado]').val();
        if (estado == '1'){
            var nro_reg = $('#listaDetalle tbody tr').length;
            if (nro_reg > 0){
                var rspta = validaItems();
                console.log(rspta);
                if (rspta.length > 0){
                    alert(rspta);
                } else {
                    var salida = confirm("¿Esta seguro que desea generar el salida a Almacén?\nEste procedimiento moverá los stocks en Almacén y ya no podrá modificar la Guía");
                    if (salida){
                        $.ajax({
                            type: 'GET',
                            // headers: {'X-CSRF-TOKEN': token},
                            url: 'generar_salida_guia/'+id_guia,
                            dataType: 'JSON',
                            success: function(response){
                                console.log(response);
                                if (response['id_salida'] > 0){
                                    alert('Salida Almacén generada con éxito');
                                    changeStateButton('guardar');

                                    var op = $('[name=id_operacion]').val();
                                    var cont = $('[name=id_contrib]').val();
                                    console.log('ope: '+op);
                                    console.log('id_contrib: '+cont);

                                    if (op == 11 || (op == 1 && cont >= 1 && cont <= 5)){
                                        alert('Desea generar una transferencia entre almacenes?');
                                        open_transferencia();
                                    }
                                    mostrar_guia_ven(id_guia);
                                    // var id = encode5t(response['id_salida']);
                                    // window.open('imprimir_salida/'+id);
                                } else {
                                    alert(response['msj']);
                                }
                            }
                        }).fail( function( jqXHR, textStatus, errorThrown ){
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                        });
                    }
                }
            } else {
                alert('No se puede procesar una Guía sin Items');
            }
        } else {
            alert('La guia ya fue Procesada!');
        }
    } else {
        alert("Debe seleccionar una Guía de Remision!");
    }
}
function abrir_salida(){
    var id_guia = $('[name=id_guia_ven]').val();
    if (id_guia != ''){
        $.ajax({
            type: 'GET',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'id_salida/'+id_guia,
            dataType: 'JSON',
            success: function(id_salida){
                console.log('id_salida '+id_salida);
                if (id_salida > 0){
                    var id = encode5t(id_salida);
                    window.open('imprimir_salida/'+id);
                } else {
                    alert('Esta guía no tiene salida');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('Debe seleccionar una Guía!');
    }
}
function ceros_numero(numero){
    if (numero == 'numero'){
        var num = $('[name=numero]').val();
        $('[name=numero]').val(leftZero(7,num));
    } 
    else if(numero == 'tra_numero'){
        var num = $('[name=tra_numero]').val();
        $('[name=tra_numero]').val(leftZero(7,num));
    }
}
function direccion(){
    var almacen = $('[name=id_almacen]').val();
    console.log('almacen'+almacen);
    if (almacen !== '' && almacen !== '0'){
        // var token = $('#token').val();
        $.ajax({
            type: 'GET',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'direccion_almacen/'+almacen,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=punto_partida]').val(response);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function actualiza_titulo(){
    var tp_doc = $('select[name="id_tp_doc_almacen"] option:selected').text();
    $('#titulo').text(tp_doc);
    next_serie_numero();
}
function validaCabecera(){
    var id_tp_doc_almacen = $('[name=id_tp_doc_almacen]').val();
    var serie = $('[name=serie]').val();
    var numero = $('[name=numero]').val();
    var id_sede = $('[name=id_sede]').val();
    var id_cliente = $('[name=id_cliente]').val();
    var id_almacen = $('[name=id_almacen]').val();
    var id_operacion = $('[name=id_operacion]').val();
    var usuario = $('[name=usuario]').val();
    var msj = '';

    if (id_tp_doc_almacen == '0'){
        msj+='\n Es necesario que elija un Tipo de Documento';
    }
    if (serie == ''){
        msj+='\n Es necesario que ingrese una Serie';
    }
    if (numero == '0'){
        msj+='\n Es necesario que ingrese un Número';
    }
    if (id_sede == '0'){
        msj+='\n Es necesario que elija una Sede';
    }
    if (id_cliente == '' && id_operacion !== '11'){
        msj+='\n Es necesario que seleccione un Cliente';
    }
    if (id_almacen == '0'){
        msj+='\n Es necesario que elija un Almacén';
    }
    if (id_operacion == '0'){
        msj+='\n Es necesario que elija un Tipo de Operación';
    }
    if (usuario == '0'){
        msj+='\n Es necesario que elija un Responsable';
    }
    return msj;
}
function validaItems(){
    var pos = 0;
    var series = 0;
    var msj = '';
    $('#listaDetalle tbody tr').each(function(e){
        var posicion = $(this).find("td select[name=id_posicion]").val();
        if (posicion == "0"){
            pos++;
        }
        var tds = $(this).find("td input[name=series]").val();
        
        if (tds == 'true'){
            var des = $(this).find("td")[2].innerHTML;//descripcion
            var nro_series = $(this).find("td input[name=nro_series]").val();
            var cant = $(this).find("td input[name=cantidad]").val();
            console.log('cant '+cant);
            console.log('nro_series '+nro_series);

            if (des.indexOf('Serie(s):') == -1){
                series++;
            } else {
                if (cant !== nro_series){
                    console.log('diferentes');
                    series++;
                }    
            }
        }
    });
    if (pos > 0 || series > 0){
        msj = 'No puede realizar ésta acción:'+(pos > 0 ? 
            ('\nFalta asignar una ubicación a '+pos+' productos') : '')+ 
            (series > 0 ? ('\nEl nro de series no concuerda con la cantidad.') : '');
    }
    console.log(msj);
    return msj;
}
function cargar_almacenes(){
    var id_sede = $('[name=id_sede]').val();
    console.log(id_sede);
    if (id_sede !== ''){
        $.ajax({
            type: 'GET',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'cargar_almacenes/'+id_sede,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                var option = '';
                for (var i=0; i<response.length; i++){
                    var sel = false;
                    console.log('id_almacen '+id_almacen +'   response[i].id_almacen '+response[i].id_almacen);

                    if (response.length == 1 || (id_almacen !== null && id_almacen == response[i].id_almacen)){
                        sel = true;
                    }
                    if (sel){
                        option+='<option value="'+response[i].id_almacen+'" selected>'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                    } else {
                        option+='<option value="'+response[i].id_almacen+'">'+response[i].codigo+' - '+response[i].descripcion+'</option>';
                    }
                }
                $('[name=id_almacen]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
        next_serie_numero();
    }
}
function next_serie_numero(){
    console.log('next_serie_numero()');
    var id_sede = $('[name=id_sede]').val();
    var id_tp_doc = $('[name=id_tp_doc_almacen]').val();
    console.log('id_sede'+id_sede+' tp_doc'+id_tp_doc);
    var id = $('[name=id_guia_ven]').val();
    console.log('id: '+id);

    if (id == ''){
        $.ajax({
            type: 'GET',
            url: 'next_serie_numero_guia/'+id_sede+'/'+id_tp_doc,
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
function valida_tipo_operacion(){
    var ope = $('[name=id_operacion]').val();
    if (ope == '11'){
        $('[name=id_cliente]').val('');
        $('[name=id_contrib]').val('');
        $('[name=cliente_razon_social]').val('');
    }
}
function imprimir_guia(){
    var id_guia = $('[name=id_guia_ven]').val();
    if (id_guia != ''){
        var id = encode5t(id_guia);
        window.open('imprimir_guia_venta/'+id);
    } else {
        alert('Debe seleccionar una Guía!');
    }
}
