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
            console.log(response);
            $('[name=id_transformacion]').val(response.id_transformacion);
            $('[name=codigo_oportunidad]').val(response.codigo_oportunidad);
            // $('[name=id_empresa]').val(response.id_empresa).trigger('change.select2');
            // $('[name=serie]').val(response.serie);
            // $('[name=numero]').val(response.numero);
            $('[name=almacen_descripcion]').val(response.almacen_descripcion);
            $('[name=total_materias]').val(response.total_materias);
            $('[name=total_directos]').val(response.total_directos);
            $('[name=costo_primo]').val(response.costo_primo);
            $('[name=total_indirectos]').val(response.total_indirectos);
            $('[name=total_sobrantes]').val(response.total_sobrantes);
            $('[name=costo_transformacion]').val(response.costo_transformacion);
            // $('[name=cod_estado]').val(response.estado);
            $('[name=codigo]').val(response.codigo);
            $('[name=codigo_od]').val(response.cod_od);
            $('[name=serie-numero]').val(response.serie+'-'+response.numero);
            // $('#fecha_registro label').text('');
            $('#fecha_transformacion').text(formatDateHour(response.fecha_transformacion));
            $('#fecha_registro').text(formatDateHour(response.fecha_registro));
            $('#nombre_responsable').text(response.nombre_corto);
            $('#observacion').text(response.observacion);
            
            if (response.estado == 1){
                $('#addCostoIndirecto').show();
                $('#addServicio').show();
                $('#addTransformado').show();
                $('#addSobrante').show();
                $('#addMateriaPrima').show();
            } else {
                $('#addCostoIndirecto').hide();
                $('#addServicio').hide();
                $('#addTransformado').hide();
                $('#addSobrante').hide();
                $('#addMateriaPrima').hide();
            }
            
            $('[name=id_estado]').val(response.estado);
            $('#estado_doc').text(response.estado_doc);
            $('#estado_doc').addClass("label label-"+response.bootstrap_color);
            
            listar_materias(response.id_transformacion);
            listar_directos(response.id_transformacion);
            listar_indirectos(response.id_transformacion);
            listar_sobrantes(response.id_transformacion);
            listar_transformados(response.id_transformacion);
            
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
function openProcesar(){
    var id_trans = $('[name=id_transformacion]').val();

    if (id_trans !== ''){
        // var est = $('[name=id_estado]').val();
        // if (est == '9'){
        //     alert('La transformación ya fue procesada.');
        // } 
        // else if (est == '7'){
        //     alert('La transformación esta Anulada.');
        // } 
        // else {
            $('#modal-procesarTransformacion').modal({
                show: true
            });
        // }
    } else {
        alert('No ha seleccionado una Hoja de Transformación');
    }
}

$("#form-procesarTransformacion").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    procesar_transformacion(data);
});

function procesar_transformacion(data){
    $.ajax({
        type: 'POST',
        url: 'procesar_transformacion',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            alert('Transformación procesada con éxito');
            $('#modal-procesarTransformacion').hide();
            var id_trans = $('[name=id_transformacion]').val();
            mostrar_transformacion(id_trans);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
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

let origen = null;
function openProductoMateriaModal(){
    origen = 'materia';
    productoModal();
}
function openProductoTransformadoModal(){
    origen = 'transformado';
    productoModal();
}
function openProductoSobranteModal(){
    origen = 'sobrante';
    productoModal();
}
// Calcula total
function actualizaTotales(){
    var total_materias = parseFloat($('[name=total_materias]').text());
    var total_directos = parseFloat($('[name=total_directos]').text());
    var total_indirectos = parseFloat($('[name=total_indirectos]').text());
    var total_sobrantes = parseFloat($('[name=total_sobrantes]').text());
    console.log('actualiza');
    $('[name=costo_primo]').text(formatDecimalDigitos((total_materias + total_directos),2));
    $('[name=costo_transformacion]').text(formatDecimalDigitos((total_materias + total_directos + total_indirectos - total_sobrantes),2));

}