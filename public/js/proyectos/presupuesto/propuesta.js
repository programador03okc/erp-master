function nuevo_propuesta(){
    $('#form-propuesta')[0].reset();
    $('[name=id_empresa]').val(auth_user.id_empresa);
    $('[name=elaborado_por]').val(auth_user.id_usuario);
    $('#codigo').text('');
    $('#cod_presint').text('');
    $('#des_estado').text('');
    $('#listaPresupuesto tbody').html('');
    open_opcion_modal();
}

$(function(){
    vista_extendida();
    var id_pres_cli = localStorage.getItem("id_pres_cli");
    console.log('id_pres_cli'+id_pres_cli);
    if (id_pres_cli !== null){
        mostrar_propuesta(id_pres_cli);
        localStorage.removeItem("id_pres_cli");
        $('[name=id_presup]').val(id_pres_cli);
    }

    $("#tab-propuesta section:first form").attr('form', 'formulario');
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
    });
});

function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}


function mostrar_propuesta(id){
    console.log('id_presup '+id);
    $.ajax({
        type: 'GET',
        url: 'mostrar_propuesta/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_presup]').val(response['propuesta'].id_presup);
            $('[name=id_empresa]').val(response['propuesta'].id_empresa);
            $('[name=id_grupo]').val(response['propuesta'].id_grupo);
            $('[name=id_op_com]').val(response['propuesta'].id_op_com);
            $('[name=nombre_opcion]').val(response['propuesta'].descripcion);
            $('[name=unid_program]').val(response['propuesta'].unid_program);
            $('[name=cantidad]').val(response['propuesta'].cantidad);
            $('[name=simbolo]').text(response['propuesta'].simbolo);
            $('[name=responsable]').val(response['propuesta'].responsable).trigger('change.select2');
            $('[name=moneda]').val(response['propuesta'].moneda);
            $('[name=fecha_emision]').val(response['propuesta'].fecha_emision);
            $('[name=sub_total_presint]').val(formatDecimal(response['presint'].sub_total));
            $('[name=id_presupuesto]').val(response['presint'].id_presupuesto);

            $('[name=sub_total]').val(formatDecimal(response['totales'].sub_total));
            $('[name=porcen_utilidad]').val(formatDecimal(response['totales'].porcen_utilidad));
            $('[name=impor_utilidad]').val(formatDecimal(response['totales'].importe_utilidad));
            $('[name=total]').val(formatDecimal(parseFloat(response['totales'].sub_total) + parseFloat(response['totales'].importe_utilidad)));
            $('[name=porcen_igv]').val(formatDecimal(response['totales'].porcen_igv));
            $('[name=importe_igv]').val(formatDecimal(response['totales'].importe_igv));
            $('[name=total_propuesta]').val(formatDecimal(response['totales'].total_propuesta));
            
            $('#codigo').text(response['propuesta'].codigo);
            $('#cod_presint').text(response['presint'].codigo);
            $('[name=cod_presint]').text(response['presint'].codigo);

            $('#des_estado').text(response['propuesta'].des_estado);
            $('#estado').text(response['propuesta'].estado);
            
            var des='';
            if (response['propuesta'].estado == 1){
                des = 'label label-primary';
            } else if (response['propuesta'].estado == 7){
                des = 'label label-danger';
            } else if (response['propuesta'].estado == 8){
                des = 'label label-success';
            }
            $('#des_estado').removeClass();
            $('#des_estado').addClass(des);

            document.getElementById("cronograma").style.visibility = (response['propuesta'].cronograma ? "visible" : "hidden"); 
            document.getElementById("cronoval").style.visibility = (response['propuesta'].cronoval ? "visible" : "hidden"); 
            
            listar_partidas_propuesta(id);
            $('#modal-propuesta').modal('hide');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function refresh_partidas(){
    var id = $('[name=id_presup]').val();
    
    if (id !== ''){
        listar_partidas_propuesta(id);
    } else {
        alert('Aun no existe una propuesta');
    }
}

function listar_partidas_propuesta(id_pres){
    console.log('id_presup:'+id_pres);
    $.ajax({
        type: 'GET',
        url: 'listar_partidas_propuesta/'+id_pres,
        dataType: 'JSON',
        success: function(response){
            $('#listaPresupuesto tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function totales(){
    var id_presup = $('[name=id_presup]').val();
    console.log('totales id_presup: '+id_presup);
    if (id_presup !== ''){
        $.ajax({
            type: 'GET',
            url: 'totales_propuesta/'+id_presup,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=sub_total]').val(formatDecimal(response.sub_total));
                $('[name=porcen_utilidad]').val(formatDecimal(response.porcen_utilidad));
                $('[name=impor_utilidad]').val(formatDecimal(response.importe_utilidad));
                $('[name=total]').val(formatDecimal(parseFloat(response.sub_total) + parseFloat(response.importe_utilidad)));
                $('[name=porcen_igv]').val(formatDecimal(response.porcen_igv));
                $('[name=importe_igv]').val(formatDecimal(response.importe_igv));
                $('[name=total_propuesta]').val(formatDecimal(response.total_propuesta));
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function mostrar_total_presint(id_op_com){
    if (id_op_com !== ''){
        $.ajax({
            type: 'GET',
            url: 'mostrar_total_presint/'+id_op_com,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=sub_total_presint]').val(formatDecimal(response.sub_total));
                $('[name=id_presupuesto]').val(response.id_presupuesto);
                $('[name=moneda]').val(response.moneda);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function change_importe_utilidad(){
    $('[name=porcen_utilidad]').val(0);
    change_utilidad();
}

function change_utilidad(){
    var porc = parseFloat($('[name=porcen_utilidad]').val());
    var st = parseFloat($('[name=sub_total]').val());
    var igv = parseFloat($('[name=porcen_igv]').val());
    console.log('porcen:'+porc+' subtotal:'+st+' igv:'+igv);
    var uti = 0;

    if (porc !== '' && porc > 0){
        uti = st * porc / 100;
        $('[name=impor_utilidad]').val(formatDecimal(uti));
    } else {
        uti = parseFloat($('[name=impor_utilidad]').val());
    }

    var total = uti + st;
    var imp_igv = 0;

    if (igv > 0){
        imp_igv = total * igv / 100;
    } else {
        imp_igv = parseFloat($('[name=importe_igv]').val());
    }
    var total_prop = total + imp_igv;

    $('[name=total]').val(formatDecimal(total));
    $('[name=importe_igv]').val(formatDecimal(imp_igv));
    $('[name=total_propuesta]').val(formatDecimal(total_prop));
}

function exportar_propuesta(){
    var id_presup = $('[name=id_presup]').val();
    console.log('download_propuesta id_presup: '+id_presup);
    if (id_presup !== ''){
        window.open('download_propuesta/'+id_presup);
    }
}

function copiar_partidas_presint(){
    var id_presupuesto = $('[name=id_presupuesto]').val();
    var id_presup = $('[name=id_presup]').val();
    
    if (id_presupuesto !== '' && id_presup !== ''){
        var filas = document.querySelectorAll('#listaPresupuesto tbody tr');
        
        if (filas.length > 0){
            alert('No es posible copiar. Ya existen partidas creadas.');
        } 
        else {
            var rspta = confirm('¿Está seguro que desea copiar las partidas del Presupuesto Interno relacionado?');
            if (rspta){
                $.ajax({
                    type: 'GET',
                    url: 'copiar_partidas_presint/'+id_presupuesto+'/'+id_presup,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        mostrar_propuesta(id_presup);
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        }
    } else {
        alert('Debe ingresar la propuesta.');
    }
}

function save_propuesta(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_presup';
    } else if (action == 'edition'){
        baseUrl = 'update_presup';
    }
    console.log(action);
    console.log(data);
    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Propuesta registrada con éxito');
                changeStateButton('guardar');
                document.getElementById('btnCopiar').removeAttribute("disabled");
                mostrar_propuesta(response);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_propuesta(ids){
    if (ids !== ''){
        $.ajax({
            type: 'GET',
            url: 'anular_propuesta/'+ids,
            dataType: 'JSON',
            success: function(response){
                if (response > 0){
                    alert('Se anuló correctamente.');
                    mostrar_propuesta(ids);
                } else {
                    alert('No es posible anular!');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
