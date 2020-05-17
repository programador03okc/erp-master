function nuevo_preseje(){
    $('#form-preseje')[0].reset();
    $('#form-totales')[0].reset();
    $('#codigo').text('');
    $('#listaCD tbody').html('');
    $('#listaCI tbody').html('');
    $('#listaGG tbody').html('');
    $('#listaAcusCD tbody').html('');
    $('#total_acus_cd').text('');
    $('#listaEstructura tbody').html('');
    proyectoModal();
}
$(function(){
    $('[name=fecha_emision]').val(fecha_actual());
    $('[name=id_empresa]').val(auth_user.id_empresa);
    $('[name=elaborado_por]').val(auth_user.id_usuario);
    $('#listaCD tbody').html('');
    $('#listaCI tbody').html('');
    $('#listaGG tbody').html('');
    $('#listaEstructura tbody').html('');

    $("#tab-preseje section:first form").attr('form', 'formulario');
    
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

        var id = $('[name=id_presupuesto]').val();
        console.log('activeForm'+activeForm+' id'+id);
        clearDataTable();
        actualizar_tab(activeForm, id);
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });

});

function actualizar_tab(activeForm, id){
    if (id !== ''){
        if (activeForm == "form-par"){
            listarAcusCD(id);
        }
        else if (activeForm == "form-cd"){
            $('#listaCD tbody').html('');
            listar_cd(id);
        } 
        else if (activeForm == "form-ci"){
            $('#listaCI tbody').html('');
            listar_ci(id);
        }
        else if (activeForm == "form-gg"){
            $('#listaGG tbody').html('');
            listar_gg(id);
        }
        else if (activeForm == "form-est"){
            $('#listaEstructura tbody').html('');
            var id_pres = $('[name=id_presup]').val();
            listar_estructura(id_pres);
        }
    }
}

function mostrar_preseje(id){
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_presint/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_presupuesto]').val(response.id_presupuesto);
            $('[name=id_presup]').val(response.id_presup);
            $('[name=id_op_com]').val(response.id_op_com);
            $('[name=descripcion_proy]').val(response.descripcion_proy);
            $('[name=simbolo]').text(response.simbolo);
            // $('[name=importe]').val(response.total_presupuestado);
            $('[name=tipo_cambio]').val(response.tipo_cambio);
            $('[name=moneda]').val(response.moneda);
            $('[name=fecha_emision]').val(response.fecha_emision);

            $('[name=total_costo_directo]').val(formatDecimal(response.total_costo_directo));
            $('[name=porcentaje_ci]').val(formatDecimal(response.porcentaje_ci));
            $('[name=total_ci]').val(formatDecimal(response.total_ci));
            $('[name=porcentage_gg]').val(formatDecimal(response.porcentage_gg));
            $('[name=total_gg]').val(formatDecimal(response.total_gg));
            $('[name=sub_total]').val(formatDecimal(response.sub_total));
            $('[name=porcentaje_igv]').val(formatDecimal(response.porcentaje_igv));
            $('[name=total_igv]').val(formatDecimal(response.total_igv));
            $('[name=total_presupuestado]').val(formatDecimal(response.total_presupuestado));
        
            $('#codigo').text(response.codigo);
            $('#version').text('Versión N° '+response.version);
            $('#des_estado').text(response.des_estado);
			$('#estado').text(response.estado);
            var des='';
            if (response.estado == 1){
                des = 'label label-primary';
            } else if (response.estado == 7){
                des = 'label label-danger';
            } else if (response.estado == 8){
                des = 'label label-success';
            }
            $('#des_estado').removeClass();
            $('#des_estado').addClass(des);

            document.getElementById("cronograma").style.visibility = (response.cronograma ? "visible" : "hidden"); 
            document.getElementById("cronoval").style.visibility = (response.cronoval ? "visible" : "hidden"); 

            var activeTab = $("#tab-preseje #myTab li.active a").attr('type');
            var activeForm = "form-"+activeTab.substring(1);
            actualizar_tab(activeForm, response.id_presupuesto);

            $('#modal-preseje').modal('hide');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function validaCabecera(){
    var id_proyecto = $('[name=id_proyecto]').val();
    var moneda = $('[name=moneda]').val();
    var fecha_emision = $('[name=fecha_emision]').val();
    var tipo_cambio = $('[name=tipo_cambio]').val();
    var msj = '';

    if (id_proyecto == ''){
        msj+='\n Es necesario que seleccione un Proyecto';
    }
    if (moneda == '' || moneda == '0'){
        msj+='\n Es necesario que seleccione una Moneda';
    }
    if (fecha_emision == ''){
        msj+='\n Es necesario que ingrese una Fecha Emisión';
    }
    if (tipo_cambio == ''){
        msj+='\n Es necesario que ingrese un Tipo de Cambio';
    }
    return msj;
}

function save_preseje(data, action){
    console.log(action);
    console.log(data);
    if (action == 'register'){
        baseUrl = 'guardar_preseje';
    } else if (action == 'edition'){
        baseUrl = 'update_preseje';
    }
    var msj = validaCabecera();

    if (msj.length > 0){
        alert(msj);
    } 
    else {
        $.ajax({
            type: 'POST',
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response['msj'].length > 0){
                    alert(response['msj']);
                } 
                if (response['id_pres'] > 0){
                    $('[name=id_presupuesto]').val(response['id_pres']);
                    changeStateButton('guardar');
                    $('#form-preseje').attr('type', 'register');
                    changeStateInput('form-preseje', true);
                } else {
                    $('[name=id_presupuesto]').val('');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function sim_moneda(){
    console.log('sim_moneda');
    var mnd = $('select[name="moneda"] option:selected').text();
    var sim = mnd.split(' - ');
    console.log(mnd)
    $('[name=simbolo]').text(sim[1]);
}

function anular_preseje(ids){
    console.log('anular_preseje() id_pres: '+ids);
    if (ids !== ''){
        $.ajax({
            type: 'GET',
            url: 'anular_presint/'+ids,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                console.log(response['update']);
                alert(response['msj']);
                if (response['update'] > 0){
                    mostrar_preseje(ids);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function generar_estructura(){
    var id_pres = $('[name=id_presupuesto]').val();
    var id_presup = $('[name=id_presup]').val();
    var tp_presup = 4;//Estructura Preseje

    if (id_presup == ''){
        if (id_pres !== ''){
            console.log('id_presup:'+id_presup+' id_presupuesto:'+id_pres);
            $.ajax({
                type: 'GET',
                url: 'generar_estructura/'+id_pres+'/'+tp_presup,
                dataType: 'JSON',
                success: function(response){
                    console.log('id_presup: '+response);
                    $('[name=id_presup]').val(response);
                    mostrar_preseje(id_pres);
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        } else {
            alert('Debe seleccionar un presupuesto!');
        }
    } else {
        alert('Ya existe un presupuesto generado. Debe anularlo!');
    }
}

function listar_estructura(id_presup){
    if (id_presup !== ''){
        $.ajax({
            type: 'GET',
            url: 'listar_presupuesto_proyecto/'+id_presup,
            dataType: 'JSON',
            success: function(response){
                $('#listaEstructura tbody').html(response);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function anular_estructura(){
    var id_pres = $('[name=id_presupuesto]').val();
    if (id_pres !== ''){
        $.ajax({
            type: 'GET',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'anular_estructura/'+id_pres,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    $('#listaEstructura tbody').html('');
                    mostrar_preseje(id_pres);
                } else {
                    alert('No se puede anular el presupuesto. Ya está relacionado con Requerimientos!');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function totales(id_presup){
    if (id_presup !== ''){
        $.ajax({
            type: 'GET',
            url: 'totales/'+id_presup,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=total_costo_directo]').val(formatDecimal(response.total_costo_directo));
                $('[name=porcentaje_ci]').val(formatDecimal(response.porcentaje_ci));
                $('[name=total_ci]').val(formatDecimal(response.total_ci));
                $('[name=porcentage_gg]').val(formatDecimal(response.porcentage_gg));
                $('[name=total_gg]').val(formatDecimal(response.total_gg));
                $('[name=sub_total]').val(formatDecimal(response.sub_total));
                $('[name=porcentaje_igv]').val(formatDecimal(response.porcentaje_igv));
                $('[name=total_igv]').val(formatDecimal(response.total_igv));
                $('[name=total_presupuestado]').val(formatDecimal(response.total_presupuestado));
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function exportar_presupuesto(){
    var id_presup = $('[name=id_presup]').val();
    console.log('download_presupuesto id_presup: '+id_presup);
    if (id_presup !== ''){
        window.open('download_presupuesto/'+id_presup);
    }
}

function generar_preseje(id_proyecto){
    if (id_proyecto !== ''){
        $.ajax({
            type: 'GET',
            // headers: {'X-CSRF-TOKEN': token},
            url: 'generar_preseje/'+id_proyecto,
            dataType: 'JSON',
            success: function(id_preseje){
                console.log('id_preseje'+id_preseje);
                if (id_preseje > 0){
                    alert('Presupuesto de Ejecución generado con éxito!');
                    mostrar_preseje(id_preseje);
                    changeStateButton('guardar');
                    $('#form-preseje').attr('type', 'register');
                    changeStateInput('form-preseje', true);
                } else {
                    alert('No se pudo generar el Presupuesto!');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert("Debe seleccionar una Opción Comercial!");
    }
}

function actualiza_moneda(){
    var id_presup = $('[name=id_presupuesto]').val();
    console.log(' id_presup: '+id_presup);
    if (id_presup !== ''){
        $.ajax({
            type: 'GET',
            url: 'actualiza_moneda/'+id_presup,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    mostrar_preseje(id_presup);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('Debe seleccionar un presupuesto!');
    }
}