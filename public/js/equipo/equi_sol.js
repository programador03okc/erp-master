$(function(){
    var id_solicitud = localStorage.getItem("id_solicitud");
    console.log(id_solicitud);
    if (id_solicitud !== null){
        mostrar_solicitud(id_solicitud);
        localStorage.removeItem("id_solicitud");
        changeStateButton('historial');
    }
});
function nuevo_equi_sol(){
    var id_usuario = auth_user.id_usuario;
    $('#form-equi_sol')[0].reset();
    mostrar_trabajador(id_usuario);
    $('#listaSolFlujos tbody').html('');
}
function edit_equi_sol(){
    var estado = $('[name=estado]').val();
    var estado_doc = $('#estado_doc').text();
    console.log(estado);
    if (estado == 1 || estado == 3){
        changeStateInput("form-equi_sol", false);
        changeStateButton('editar');
        $('#form-equi_sol').attr('type', 'edition');
    } 
    else {
        alert("Ésta solicitud está con "+estado_doc+". No es posible modificar!");
    }
}
function mostrar_solicitud(id){
    baseUrl = 'mostrar_solicitud/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.id_proyecto !== 0 && response.id_proyecto !== null){
                $('.proyecto').removeClass('oculto');
                $('.proyecto').addClass('visible');
            } else {
                $('.proyecto').removeClass('visible');
                $('.proyecto').addClass('oculto');
            }
            $('[name=id_solicitud]').val(response.id_solicitud);
            $('[name=codigo]').val(response.codigo);
            $('[name=id_trabajador]').val(response.id_trabajador).trigger('change.select2');
            $('[name=id_categoria]').val(response.id_categoria);
            $('[name=id_proyecto]').val(response.id_proyecto).trigger('change.select2');
            $('[name=fecha_solicitud]').val(response.fecha_solicitud);
            $('[name=fecha_inicio]').val(response.fecha_inicio);
            $('[name=fecha_fin]').val(response.fecha_fin);
            $('[name=cantidad]').val(response.cantidad);
            $('[name=observaciones]').val(response.observaciones);
            $('[name=estado]').val(response.estado);
            $('#estado_doc label').text('');
            $('#estado_doc label').append(response.estado_doc);
            $('#fecha_registro label').text('');
            $('#fecha_registro label').append(formatDateHour(response.fecha_registro));
            $('[name=empresa]').val(response.id_empresa);
            // $('[name=id_sede]').val(response.id_sede);
            // $('[name=id_grupo]').val(response.id_grupo);
            $('[name=id_area]').val(response.id_area);
            $('[name=nombre_area]').val(response.nombre_area);
            $('#registrado_por label').text(response.nombre_usuario);
            console.log(response.nombre_area);
            $('[name=id_doc_aprob]').val(response.id_doc_aprob);

            if (response.id_doc_aprob !== undefined && response.id_solicitud !== undefined){
                listar_flujos(  response.id_doc_aprob, 
                                response.id_solicitud  );
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function save_equi_sol(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_equi_sol';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_equi_sol';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Solicitud registrada con éxito');
                // $('#listaSolicitudes').DataTable().ajax.reload();
                changeStateButton('guardar');
                
                var estado = $('[name=estado]').val();
                if (estado == 3){//3 = Observado
                    open_obs('Ingrese sustento:','true','Es necesario que ingrese un Sustento para guardar los cambios!');
                    
                } else {
                    mostrar_solicitud(response);
                }
                changeStateButton('guardar');
				$('#form-equi_sol').attr('type', 'register');
				changeStateInput('form-equi_sol', true);           
            }
        }
    }).fail( function( jqXHR, textStatus, erro ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(erro);
    });

}
function guardar_sustento(codigo,id_solicitud,sus){
    var data = 'codigo='+codigo+
        '&id_solicitud='+id_solicitud+
        // '&id_doc_aprob='+id_doc_aprob+
        '&id_vobo='+4+//Sustentado
        '&id_usuario='+auth_user.id_usuario+
        '&id_area='+auth_user.id_area+
        '&detalle_observacion='+sus+
        '&id_rol='+auth_user.id_rol;
    console.log(data);

    var token = $('#token').val();
    
    $.ajax({
    type: 'POST',
    headers: {'X-CSRF-TOKEN': token},
    url: 'guardar_sustento',
    data: data,
    dataType: 'JSON',
    success: function(response){
            console.log(response);
            if (response > 0){
                alert('Sustento registrado con éxito');
                // $('#listaSolicitudes').DataTable().ajax.reload();
                changeStateButton('guardar');
                mostrar_solicitud(id_solicitud);
            }
        }
    }).fail( function( jqXHR, textStatus, erro ){
    console.log(jqXHR);
    console.log(textStatus);
    console.log(erro);
    });

}
function anular_equi_sol(ids){
    baseUrl = 'anular_equi_sol/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Solicitud anulada con exito');
                $('#listaSolicitud').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-equi_sol');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}
function cambiarArea(){
    var area = $('[name=nombre_area]').val();
    console.log('cambiarArea');
    console.log(area);
    if (area == 'PROYECTOS'){
        $('.proyecto').addClass('visible');
        $('.proyecto').removeClass('oculto');
    } else {
        $('.proyecto').removeClass('visible');
        $('.proyecto').addClass('oculto');
    }
}
function listar_flujos(id_doc_aprob,id_solicitud){
    $('#listaSolFlujos tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'solicitud_flujos/'+id_doc_aprob+'/'+id_solicitud,
        dataType: 'JSON',
        success: function(response){
            $('#listaSolFlujos tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function mostrar_trabajador(id_usuario){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'getTrabajador/'+id_usuario,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_trabajador]').val(response[0].id_trabajador).trigger('change.select2');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function valida_fecha(value){
    var fecha_inicio = $('[name=fecha_inicio]').val();
    if (value < fecha_inicio){
        alert('Debe ingresar una fecha posterior a la fecha de inicio');
        $('[name=fecha_fin]').val('');
        // $('[name=fecha_fin]').focus();
    }
}
function modal_area(){
    var id_emp = $('[name=empresa]').val();
    if (id_emp > 0){
        $('#modal-empresa-area').modal({
            show: true,
            backdrop: 'static'
        });
        cargarEstOrg(id_emp);
    } else {
        alert("Debe seleccionar la empresa");
        $('[name=empresa]').focus();
    }
}
function change_empresa(){
    $('[name=nombre_area]').val('');
    $('[name=id_area]').val('');
    $('[name=id_grupo]').val('');
}