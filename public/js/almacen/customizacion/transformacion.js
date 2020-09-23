function nuevo_transformacion(){
    $('#form-transformacion')[0].reset();
    console.log('nuevo_transformacion:'+auth_user.id_usuario);
    limpiarCampos();
}
function limpiarCampos(){
    $('[name=id_transformacion]').val('');
    $('[name=id_empresa]').val(0).trigger('change.select2');
    $('[name=serie]').val('');
    $('[name=numero]').val('');
    $('[name=id_almacen]').val(0).trigger('change.select2');
    $('[name=responsable]').val(auth_user.id_usuario).trigger('change.select2');
    $('[name=fecha_transformacion]').val(fecha_actual());

    $('#listaMateriasPrimas tbody').html('');
    $('#listaServiciosDirectos tbody').html('');
    $('#listaCostosIndirectos tbody').html('');
    $('#listaSobrantes tbody').html('');
    $('#listaProductoTransformado tbody').html('');

}
$(function(){
    var id_transformacion = localStorage.getItem("id_transformacion");
    if (id_transformacion !== null){
        mostrar_transformacion(id_transformacion);
        localStorage.removeItem("id_transformacion");
        changeStateButton('historial');
    }
});
function mostrar_transformacion(id){
    $.ajax({
        type: 'GET',
        url: 'mostrar_transformacion/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response[0]);
            $('[name=id_transformacion]').val(response[0].id_transformacion);
            $('[name=codigo_oportunidad]').val(response[0].codigo_oportunidad);
            $('[name=id_empresa]').val(response[0].id_empresa).trigger('change.select2');
            $('[name=serie]').val(response[0].serie);
            $('[name=numero]').val(response[0].numero);
            $('[name=fecha_transformacion]').val(response[0].fecha_transformacion);
            $('[name=id_almacen]').val(response[0].id_almacen).trigger('change.select2');
            $('[name=responsable]').val(response[0].responsable).trigger('change.select2');
            $('[name=total_materias]').val(response[0].total_materias);
            $('[name=total_directos]').val(response[0].total_directos);
            $('[name=costo_primo]').val(response[0].costo_primo);
            $('[name=total_indirectos]').val(response[0].total_indirectos);
            $('[name=total_sobrantes]').val(response[0].total_sobrantes);
            $('[name=costo_transformacion]').val(response[0].costo_transformacion);
            $('[name=cod_estado]').val(response[0].estado);
            $('[name=observacion]').val(response[0].observacion);
            $('[name=codigo]').val(response[0].codigo);
            $('#fecha_registro label').text('');
            $('#fecha_registro label').text(formatDateHour(response[0].fecha_registro));
            $('#estado label').text('');
            $('#estado label').text(response[0].estado_doc);
            $('#registrado_por label').text('');
            $('#registrado_por label').text(response[0].nombre_corto);
            listar_materias(response[0].id_transformacion);
            listar_directos(response[0].id_transformacion);
            listar_indirectos(response[0].id_transformacion);
            listar_sobrantes(response[0].id_transformacion);
            listar_transformados(response[0].id_transformacion);
            // calcula_totales();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function save_transformacion(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_transformacion';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_transformacion';
    }
    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response['id_transformacion'] > 0){
                alert('Transformación registrada con éxito');

                changeStateButton('guardar');
                $('#form-transformacion').attr('type', 'register');
                changeStateInput('form-transformacion', true);

                mostrar_transformacion(response['id_transformacion']);
                $('.boton').removeClass('desactiva');    
                // var id = $('[name=id_transformacion]').val();
                // listar_materias(id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function ceros_numero(numero){
    if (numero == 'numero'){
        var num = $('[name=numero]').val();
        $('[name=numero]').val(leftZero(7,num));
    }
}
function calcula_totales(){
    var tot_materias = $('[name=total_materias]').val();
    var tot_directos = $('[name=total_directos]').val();
    var tot_indirectos = $('[name=total_indirectos]').val();
    var tot_sobrantes = $('[name=total_sobrantes]').val();
    var tot_transformados = $('[name=total_transformados]').val();
    var costo_primo = 0;

    if (tot_materias !== '' && tot_directos !== ''){
        costo_primo = parseFloat(tot_materias) + parseFloat(tot_directos);
        console.log('costo_primo:'+costo_primo);
        $('[name=costo_primo]').val(costo_primo);
    }
    if (tot_indirectos !== '' && 
        tot_sobrantes !== '' && 
        tot_transformados !== ''){

    }
    console.log('total_materias: '+tot_materias+' - total_directos: '+tot_directos);
}
function procesar_transformacion(){
    var id_trans = $('[name=id_transformacion]').val();

    if (id_trans !== ''){
        var est = $('[name=cod_estado]').val();
        if (est == '9'){
            alert('La transformación ya fue procesada.');
        } 
        else if (est == '7'){
            alert('La transformación esta Anulada.');
        } 
        else {
            $.ajax({
                type: 'GET',
                url: 'procesar_transformacion/'+id_trans,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    alert('Transformación procesada con éxito');
                    if (response['id_ingreso'] > 0){
                        var id = encode5t(response['id_ingreso']);
                        window.open('imprimir_ingreso/'+id);
                    }
                    else if (response['id_salida'] > 0){
                        var id = encode5t(response['id_salida']);
                        window.open('imprimir_salida/'+id);
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
function abrir_salida(){
    var id_transformacion = $('[name=id_transformacion]').val();
    console.log(id_transformacion);
    if (id_transformacion != ''){
        $.ajax({
            type: 'GET',
            url: 'id_salida_transformacion/'+id_transformacion,
            dataType: 'JSON',
            success: function(id_salida){
                if (id_salida > 0){
                    console.log(id_salida);
                    var id = encode5t(id_salida);
                    window.open('imprimir_salida/'+id);
                } else {
                    alert('Esta Transformación no tiene Salida');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('Debe seleccionar una Transformación!');
    }
}
function abrir_ingreso(){
    var id_transformacion = $('[name=id_transformacion]').val();
    console.log(id_transformacion);
    if (id_transformacion != ''){
        $.ajax({
            type: 'GET',
            url: 'id_ingreso_transformacion/'+id_transformacion,
            dataType: 'JSON',
            success: function(id_ingreso){
                if (id_ingreso > 0){
                    console.log(id_ingreso);
                    var id = encode5t(id_ingreso);
                    window.open('imprimir_ingreso/'+id);
                } else {
                    alert('Esta Transformación no tiene Ingreso');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('Debe seleccionar una Transformación!');
    }
}
function anular_transformacion(ids){
    $.ajax({
        type: 'GET',
        url: 'anular_transformacion/'+ids,
        dataType: 'JSON',
        success: function(response){
            if (response.length > 0){
                alert(response);
                changeStateButton('anular');
                mostrar_transformacion(ids);
                // clearForm('form-guia_compra');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}