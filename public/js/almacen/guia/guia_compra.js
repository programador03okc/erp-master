function nuevo_guia_compra(){
    $('#form-general')[0].reset();
    $('[name=usuario]').val(auth_user.id_usuario).trigger('change.select2');
    $('[name=id_tp_doc_almacen]').val(1).trigger('change.select2');
    $('[name=id_operacion]').val(2).trigger('change.select2');
    $('[name=id_guia_clas]').val(1);
    
    $('#serie').text('');
    $('#numero').text('');
    $('#doc_serie').text('');
    $('#doc_numero').text('');
    $('#tp_doc_abreviatura').text('');

    $('#listaDetalle tbody').html('');
    $('#oc tbody').html('');
    limpiarCampos();
}
function limpiarCampos(){
    $('[name=id_tp_prorrateo]').val('');
    $('[name=pro_serie]').val('');
    $('[name=pro_numero]').val('');
    $('[name=doc_fecha_emision]').val(fecha_actual());
    $('[name=tipo_cambio]').val(0);
    $('[name=id_moneda]').val(0);
    $('[name=sub_total]').val(0);
    $('[name=importe]').val(0);
    $('[name=doc_razon_social]').val('');
    $('[name=doc_id_proveedor]').val('');
    $('[name=id_tp_documento]').val('').trigger('change.select2');
    $('[name=id_contrib]').val('');
}
$(function(){
    var id_guia_com = localStorage.getItem("id_guia_com");
    console.log(id_guia_com);

    if (id_guia_com !== null){
        mostrar_guia_com(id_guia_com);
        localStorage.removeItem("id_guia_com");
        changeStateButton('historial');
    }
    $('#listaProrrateos tbody').html('');

    $("#form-doc_prorrateo").on("submit", function(){
        var data = $(this).serialize();
        console.log(data);
        var id_guia = $('[name=id_guia]').val();
        var id_prov = $('[name=doc_id_proveedor]').val();
        console.log('submit prorrateo'+id_guia);
        
        if (id_prov !== ''){
            if (id_guia !== ''){
                var p = $('[name=prorrateo]').val();
                console.log('prorrateo: '+p);
                $.ajax({
                    type: 'POST',
                    url: 'guardar_prorrateo',
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        if (response > 0){
                            alert('Documento Adicional guardado con éxito');
                            limpiarCampos();
                            console.log('id_guia:'+id_guia);
                            listar_docs_prorrateo(id_guia);
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            } else {
                alert('No ha seleccionado una Guía!');
            }    
        } else {
            alert('Debe ingresar un proveedor!');
        }
        return false;
    });
    $("#form-obs").on("submit", function(e){
        console.log('submit');
        e.preventDefault();
        var data = $(this).serialize();
        console.log(data);
        guardar_observacion(data);
    });

    $("#tab-guia_compra section:first form").attr('form', 'formulario');
    $('#modal-proveedores .modal-dialog').attr('type','form-general');
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
        $('#modal-proveedores .modal-dialog').attr('type',activeForm);
        changeStateInput(activeForm, true);

        var id = $('[name=id_guia]').val();
        var id_prov = $('[name=id_proveedor]').val();
        
        clearDataTable();
        actualizar_tab(activeForm, id, id_prov);
        $(activeTab).attr('hidden', false);//inicio botones (estados)
        // resizeSide();
    });
    // resizeSide();

});
function actualizar_tab(activeForm, id, id_prov){
    if (id !== ''){
        console.log('id_guia'+id);
        if (activeForm == "form-general"){
            mostrar_guia_com(id);
            // listar_detalle(id);
            // guia_ocs(id);
        } 
        else if (activeForm == "form-detalle"){
            listar_detalle(id);
            guia_ocs(id);
            if (id_prov !== ''){
                listar_ordenes(id_prov);
            }
        }
        else if (activeForm == "form-prorrateo"){
            $('[name=id_guia]').val(id);
            listar_docs_prorrateo(id);
            $('[name=prorrateo]').val(1);
            limpiarCampos();
            $('.boton').removeClass('desactiva');
        }
    }
}
function mostrar_guia_com(id){
    console.log('id'+id);
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_guia_compra/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response[0]);
            $('[name=id_guia]').val(response[0].id_guia);
            $('[name=id_tp_doc_almacen]').val(response[0].id_tp_doc_almacen).trigger('change.select2');
            $('[name=serie]').val(response[0].serie);
            $('#tp_doc_abreviatura').text(response[0].tp_doc_abreviatura);
            $('#serie').text(response[0].serie);
            $('[name=numero]').val(response[0].numero);
            $('[name=id_doc_com]').val(response[0].id_doc_com);
            $('#numero').text(response[0].numero);
            $('#tp_doc').text(response[0].tp_doc);
            $('#doc_serie').text(response[0].doc_serie);
            $('#doc_numero').text(response[0].doc_numero);
            $('[name=id_proveedor]').val(response[0].id_proveedor);
            $('[name=prov_razon_social]').val(response[0].nro_documento+' - '+response[0].razon_social);
            $('[name=id_almacen]').val(response[0].id_almacen).trigger('change.select2');
            $('[name=id_motivo]').val(response[0].id_motivo).trigger('change.select2');
            $('[name=id_guia_clas]').val(response[0].id_guia_clas);
            $('[name=id_operacion]').val(response[0].id_operacion).trigger('change.select2');
            $('[name=fecha_emision]').val(response[0].fecha_emision);
            $('[name=fecha_almacen]').val(response[0].fecha_almacen);
            $('[name=usuario]').val(response[0].usuario).trigger('change.select2');
            $('[name=cod_estado]').val(response[0].estado);
            $('[name=transportista]').val(response[0].transportista).trigger('change.select2');
            $('[name=tra_serie]').val(response[0].serie);
            $('[name=tra_numero]').val(response[0].numero);
            $('[name=fecha_traslado]').val(response[0].fecha_traslado);
            $('[name=punto_partida]').val(response[0].punto_partida);
            $('[name=punto_llegada]').val(response[0].punto_llegada);
            $('[name=placa]').val(response[0].placa);
            $('[id=fecha_registro] label').text('');
            $('[id=fecha_registro] label').append(formatDateHour(response[0].fecha_registro));
            $('[id=registrado_por] label').text('');
            $('[id=registrado_por] label').append(response[0].nombre_corto);
            $('#des_estado').text(response[0].des_estado);
            var des='';
            if (response[0].estado == 1){
                des = 'label label-primary';
            } else if (response[0].estado == 7){
                des = 'label label-danger';
            } else if (response[0].estado == 9){
                des = 'label label-success';
            }
            $('#des_estado').removeClass();
            $('#des_estado').addClass(des);
            listar_detalle(id);
            if (response[0].id_proveedor !== null){
                listar_ordenes(response[0].id_proveedor);
            }
            $('.boton').removeClass('desactiva');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function save_guia_compra(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_guia_compra';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_guia_compra';
    }
    console.log(data);
    var msj = validaCabecera();

    if (msj.length > 0){
        alert(msj);
    } 
    else {
        $.ajax({
            type: 'POST',
            // headers: {'X-CSRF-TOKEN': token},
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response['id_guia'] > 0){
                    alert('Guía de Remisión registrada con éxito');
                    changeStateButton('guardar');
                    $('#form-general').attr('type', 'register');
                    changeStateInput('form-general', true);
                    
                    // $('[name=tipo]').val('1').trigger('change.select2');
                    listar_ordenes(response['id_proveedor']);
                    mostrar_guia_com(response['id_guia']);
                    $('.boton').removeClass('desactiva');
                    
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function validaCabecera(){
    var tp_doc = $('[name=id_tp_doc_almacen]').val();
    var serie = $('[name=serie]').val();
    var num = $('[name=numero]').val();
    var prov = $('[name=id_proveedor]').val();
    var alm = $('[name=id_almacen]').val();
    var ope = $('[name=id_operacion]').val();
    var clas = $('[name=id_guia_clas]').val();
    var usuario = $('[name=usuario]').val();
    var msj = '';

    if (tp_doc == '0'){
        msj+='\n Es necesario que elija un Tipo de Documento';
    }
    if (serie == ''){
        msj+='\n Es necesario que ingrese una Serie';
    }
    if (num == '0'){
        msj+='\n Es necesario que ingrese un Número';
    }
    if (alm == '0' || alm == ''){
        msj+='\n Es necesario que elija un Almacén';
    }
    if (ope == '0'){
        msj+='\n Es necesario que elija una Operación';
    }
    if (ope !== 21){//Si es distinto de Transferencia entre almacenes
        if (prov == '0' || prov == ''){
            msj+='\n Es necesario que elija un Proveedor';
        }
    }
    if (clas == '0'){
        msj+='\n Es necesario que elija una Clasificación';
    }
    if (usuario == '0'){
        msj+='\n Es necesario que elija un Responsable';
    }
    return msj;
}
function open_guia_com_obs(id_guia){
    $('#modal-guia_com_obs').modal({
        show: true
    });
    $('[name=id_guia_com]').val(id_guia);
}

function anular_guia_compra(ids){
    open_guia_com_obs(ids);
}

function guardar_observacion(data){
    // var formData = new FormData($('#form-obs')[0]);
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'anular_guia_compra',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.length > 0){
                alert(response);
                $('#estado label').text('Anulado');
                $('[name=cod_estado]').val('2');
                changeStateButton('anular');
                $('#modal-guia_com_obs').modal('hide');
                var id = $('[name=id_guia]').val();
                mostrar_guia_com(id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function generar_ingreso(){
    var id_guia = $('[name=id_guia]').val();
    // var id_usuario = auth_user.id_usuario;
    
    if (id_guia !== ''){
        var estado = $('[name=cod_estado]').val();
        if (estado == '1'){
            var nro_reg = $('#listaDetalle tbody tr').length;
            if (nro_reg > 0){
                var rspta = verificaItems();
                console.log(rspta);
                console.log(rspta.length);
                if (rspta.length > 0){
                    alert(rspta);
                } else {
                    var id_doc = $('[name=id_doc_com]').val();
                    var ing = false;

                    if (id_doc !== ''){
                        ing = true;
                    } else {
                        ing = confirm("¿Esta seguro que desea generar un ingreso sin factura?");
                    }
                    if (ing){
                        var ingreso = confirm("¿Esta seguro que desea generar el Ingreso a Almacén?\nEste procedimiento moverá los stocks en Almacén y ya no podrá modificar la Guía");
                        if (ingreso){
                            $.ajax({
                                type: 'GET',
                                url: 'generar_ingreso/'+id_guia,
                                dataType: 'JSON',
                                success: function(id_ingreso){
                                    console.log('id_ingreso'+id_ingreso);
                                    if (id_ingreso > 0){
                                        alert('Ingreso Almacén generado con éxito');
                                        changeStateButton('guardar');
                                        mostrar_guia_com(id_guia);
                                        var id = encode5t(id_ingreso);
                                        window.open('imprimir_ingreso/'+id);
                                    }
                                }
                            }).fail( function( jqXHR, textStatus, errorThrown ){
                                console.log(jqXHR);
                                console.log(textStatus);
                                console.log(errorThrown);
                            });
                        }
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
function abrir_ingreso(){
    var id_guia = $('[name=id_guia]').val();
    console.log(id_guia);
    if (id_guia != ''){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'id_ingreso/'+id_guia,
            dataType: 'JSON',
            success: function(id_ingreso){
                if (id_ingreso > 0){
                    console.log(id_ingreso);
                    var id = encode5t(id_ingreso);
                    window.open('imprimir_ingreso/'+id);
                } else {
                    alert('Esta guía no tiene Ingreso');
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
function generar_factura(){
    var id_guia = $('[name=id_guia]').val();
    console.log(id_guia);
    var id_operacion = $('[name=id_operacion]').val();

    if (id_operacion == 2){//Compra Nacional
        if (id_guia !== ''){
            $('#modal-doc_guia').modal({
                show: true
            });
            var id_prov = $('[name=id_proveedor]').val();
            doc_guia(id_prov);
        } else {
            alert("Debe seleccionar una Guía de Remision!");
        }
    } else {
        alert("Solo puede generar Factura para el caso de una COMPRA NACIONAL.");
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
    else if(numero == 'pro_numero'){
        var num = $('[name=pro_numero]').val();
        $('[name=pro_numero]').val(leftZero(7,num));
    }
    else if(numero == 'serie'){
        var num = $('[name=serie]').val();
        $('[name=serie]').val(leftZero(4,num));
    }
    else if(numero == 'tra_serie'){
        var num = $('[name=tra_serie]').val();
        $('[name=tra_serie]').val(leftZero(4,num));
    }
}
function agregar_adicional(){
    var id_guia = $('[name=id_guia]').val();
    
    if (id_guia !== ''){
        $('#modal-doc_create').modal({
            show: true
        });
        open_doc_create();
    } else {
        alert("Debe seleccionar una Guía de Remision!");
    }
}
function direccion(){
    var almacen = $('[name=id_almacen]').val();
    console.log('almacen'+almacen);
    if (almacen !== null){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'direccion_almacen/'+almacen,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=punto_llegada]').val(response);
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
    var tp = $('[name=id_tp_doc_almacen]').val();
    if (tp == 6){
        $('[name=id_proveedor]').val('0');
        $('[name=id_proveedor]').attr('disabled',true);
    } else {
        $('[name=id_proveedor]').attr('disabled',false);
    }
}
function abrir_doc(){
    var id_doc = $('[name=id_doc_com]').val();
    var id_guia = $('[name=id_guia]').val();
    console.log('id_doc: '+id_doc);
    if (id_guia !== ''){
        if (id_doc !== ''){
            localStorage.setItem("id_doc_com",id_doc);
            location.assign("doc_compra");
        } else {
            alert('No existe una Factura relacionada');
        }
    } else {
        alert('Es necesario que seleccione una Guia');
    }
}
