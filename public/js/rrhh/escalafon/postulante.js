$(function(){
    /* Efecto para los tabs */
    $('ul.nav-tabs li a').click(function(){
        $('ul.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.content-tabs section').hide();
        $('.content-tabs section form').removeAttr('type');
        $('.content-tabs section form').removeAttr('form');

        var activeTab = $(this).attr('type');
        var activeForm = "form-"+activeTab.substring(1);
        
        $("#"+activeForm).attr('type', 'register');
        $("#"+activeForm).attr('form', 'formulario');
        changeStateInput(activeForm, true);
        changeStateButton('inicio');
        $(activeTab).show();
        resizeSide();

        var id = $('[name=id_postulante]').val();
        if (activeForm == 'form-informacion' && id !== ''){
            actualizarForm(activeForm, id);
        }else if (activeForm == 'form-formacion' && id !== ''){
            actualizarForm(activeForm, id);
        }else if (activeForm == 'form-experiencia' && id !== ''){
            actualizarForm(activeForm, id);
        }else if (activeForm == 'form-extras' && id !== ''){
            actualizarForm(activeForm, id);
        }else if (activeForm == 'form-observacion' && id !== ''){
            actualizarForm(activeForm, id);
        }
    });

    $('#listaPostulante tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer label').text(idTr);
    });

    $('#ListaFormacionAcad tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        cargarValores('formacion', miTr);
    });
    $('#ListaExperienciaLab tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        cargarValores('experiencia', miTr);
    });
    $('#ListaObs tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        cargarValores('observacion', miTr);
    });

});

function cargarValores(type, id){
    var baseUrl;
    if (type == 'formacion'){
        baseUrl = 'cargar_formacion_click/'+id;
    }else if(type == 'experiencia'){
        baseUrl = 'cargar_experiencia_click/'+id;
    }else if(type == 'observacion'){
        baseUrl = 'cargar_observacion_click/'+id;
    }
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            var data = response[0];
            if (type == 'formacion'){
                $('[name=id_formacion]').val(data.id_formacion);
                $('[name=id_nivel_estudio]').val(data.id_nivel_estudio);
                $('[name=fecha_inicio]').val(data.fecha_inicio);
                $('[name=fecha_fin]').val(data.fecha_fin);
                $('[name=id_carrera]').val(data.id_carrera);
                $('[name=nombre_institucion]').val(data.nombre_institucion);
            }else if(type == 'experiencia'){
                $('[name=id_experiencia_laboral]').val(data.id_experiencia_laboral);
                $('[name=nombre_empresa]').val(data.nombre_empresa);
                $('[name=cargo_ocupado]').val(data.cargo_ocupado);
                $('[name=fecha_ingreso]').val(data.fecha_ingreso);
                $('[name=fecha_cese]').val(data.fecha_cese);
                $('[name=funciones]').val(data.funciones);
                $('[name=datos_contacto]').val(data.datos_contacto);
                $('[name=relacion_trab_contacto]').val(data.relacion_trab_contacto);
                $('[name=telefono_contacto]').val(data.telefono_contacto);
            }else if(type == 'observacion'){
                $('[name=id_observacion]').val(data.id_observacion);
                $('[name=observacion]').val(data.observacion);
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function modalPostulante(){
    $('#modal-postulante').modal({
        show: true,
        backdrop: 'static'
    });
    listarPostulante();
}

function listarPostulante() {
    var vardataTables = funcDatatables();
    $('#listaPostulante').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': 'listar_postulantes',
        'columns': [
            {'data': 'id_postulante'},
            {'data': 'nro_documento'},
            {'render':
                function (data, type, row){
                    return (row['apellido_paterno'] + ' ' + row['apellido_materno'] + ' ' + row['nombres']);
                }
            },
            {'data': 'direccion'},
            {'data': 'telefono'},
            {'data': 'correo'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [2, 'asc']
        ]
    });
}

function selectValue(){
    var myId = $('.modal-footer label').text();
    $('[name=id_postulante]').val(myId);
    $('#modal-postulante').modal('hide');

    var activeTab = $('#tab-postulante ul li.active a').attr('type');
    var activeForm = "form-"+activeTab.substring(1);
    actualizarForm(activeForm, myId);
}

function actualizarForm(form, id){
    $('form').trigger('reset');
    changeStateButton('historial');
    if (form == 'form-informacion'){
        mostrar_postulante(id, 2);
    }else if(form == 'form-formacion'){
        mostrar_formacion(id);
    }else if(form == 'form-experiencia'){
        mostrar_experiencia(id);
    }else if(form == 'form-extras'){
        mostrar_extras(id);
    }else if(form == 'form-observacion'){
        mostrar_observacion(id);
    }
}

function buscarPersona(){
    var dni = $('[name=nro_documento]').val();
    mostrar_postulante(dni, 1);
}

function mostrar_postulante(val, type){
    if (type == 1){
        baseUrl = 'cargar_postulante_dni/'+val; 
    }else{
        baseUrl = 'cargar_postulante/'+val;
    }
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (type == 1){
                if (response[0].id_postulante > 0){
                    alert('Postulante encontrado en el sistema');
                    $('[name=id_postulante]').val(response[0].data[0].id_postulante);
                    $('[name=id_persona]').val(response[0].data[0].id_persona);
                    $('[name=datos_persona]').val(response[0].data[0].datos_persona);
                    $('[name=telefono]').val(response[0].data[0].telefono);
                    $('[name=direccion]').val(response[0].data[0].direccion);
                    $('[name=brevette]').val(response[0].data[0].brevette);
                    $('[name=correo]').val(response[0].data[0].correo);
                    $('[name=id_pais]').val(response[0].data[0].id_pais);
                    $('[name=ubigeo]').val(response[0].data[0].ubigeo);
                    changeStateButton('historial');
                }else{
                    if (response[0].id_persona > 0){
                        alert('Persona registrada, mas no como postulante');
                        $('[name=id_postulante]').val('');
                        $('[name=id_persona]').val(response[0].data[0].id_persona);
                        $('[name=datos_persona]').val(response[0].data[0].datos_persona);
                        $('[name=telefono]').val('');
                        $('[name=direccion]').val('');
                        $('[name=brevette]').val('');
                        $('[name=correo]').val('');
                        $('[name=id_pais]').val(170);
                        $('[name=ubigeo]').val('');
                    }else{
                        alert('Persona no encontrada en el sistema');
                        $('[name=id_postulante]').val('');
                        $('[name=id_persona]').val('');
                        $('[name=datos_persona]').val('');
                        $('[name=telefono]').val('');
                        $('[name=direccion]').val('');
                        $('[name=brevette]').val('');
                        $('[name=correo]').val('');
                        $('[name=id_pais]').val(170);
                        $('[name=ubigeo]').val('');
                    }
                }
            }else{
                $('[name=nro_documento]').val(response[0].nro_documento);
                $('[name=id_persona]').val(response[0].id_persona);
                $('[name=datos_persona]').val(response[0].datos_persona);
                $('[name=telefono]').val(response[0].telefono);
                $('[name=direccion]').val(response[0].direccion);
                $('[name=brevette]').val(response[0].brevette);
                $('[name=correo]').val(response[0].correo);
                $('[name=id_pais]').val(response[0].id_pais);
                $('[name=ubigeo]').val(response[0].ubigeo);
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_formacion(id){
    $('#postu-fa tr').empty();
    baseUrl = 'listar_formacion_acad/'+ id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#postu-fa').append(response);
            resizeSide();
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function mostrar_experiencia(id){
    $('#postu-el tr').empty();
    baseUrl = 'listar_experiencia_lab/'+ id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#postu-el').append(response);
            resizeSide();
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_extras(id){
    $('#postu-de tr').empty();
    baseUrl = 'listar_datos_extras/'+ id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#postu-de').append(response);
            resizeSide();
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_observacion(id){
    $('#postu-obs tr').empty();
    baseUrl = 'listar_observaciones/'+ id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#postu-obs').append(response);
            resizeSide();
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_postulante(data, action, frm_active){
    var id_post = $('[name=id_postulante]').val();
    var msj;
    if (frm_active == 'form-informacion'){
        if (action == 'register'){
            baseUrl = 'guardar_informacion_postulante';
            msj = 'Información del postulante registrada con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_informacion_postulante';
            data = data + '&id_postulante=' + id_post;
            msj = 'Información del postulante editada con exito';
        }
    }else if (frm_active == 'form-formacion'){
        var id_form = $('[name=id_formacion]').val();
        if (action == 'register'){
            baseUrl = 'guardar_formacion_academica';
            data = data + '&id_postulante=' + id_post;
            msj = 'Formación académica del postulante registrada con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_formacion_academica';
            data = data + '&id_postulante=' + id_post + '&id_formacion=' + id_form;
            msj = 'Formación académica del postulante editada con exito';
        }
    }else if (frm_active == 'form-experiencia'){
        var id_form = $('[name=id_experiencia_laboral]').val();
        if (action == 'register'){
            baseUrl = 'guardar_experiencia_laboral';
            data = data + '&id_postulante=' + id_post;
            msj = 'Experiencia laboral del postulante registrada con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_experiencia_laboral';
            data = data + '&id_postulante=' + id_post + '&id_experiencia_laboral=' + id_form;
            msj = 'Experiencia laboral del postulante editada con exito';
        }
    }else if (frm_active == 'form-extras'){
        baseUrl = 'guardar_datos_extras';
        msj = 'Datos extras del postulante registrado con exito';
        save_frm_files(baseUrl, msj, id_post);
    }else if (frm_active == 'form-observacion'){
        var id_form = $('[name=id_observacion]').val();
        if (action == 'register'){
            baseUrl = 'guardar_observacion';
            data = data + '&id_postulante=' + id_post;
            msj = 'Observaciones del postulante registrada con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_observacion';
            data = data + '&id_postulante=' + id_post + '&id_observacion=' + id_form;
            msj = 'Observaciones del postulante editada con exito';
        }
    }

    if (id_post > 0){
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                if (frm_active == 'form-informacion'){
                    alert(msj);
                }else if (frm_active == 'form-formacion'){
                    alert(msj);
                    if (response > 0){
                        mostrar_formacion(response);
                    }
                }else if (frm_active == 'form-experiencia'){
                    alert(msj);
                    if (response > 0){
                        mostrar_experiencia(response);
                    }
                }else if (frm_active == 'form-extras'){
                    alert(msj);
                    if (response > 0){
                        mostrar_extras(response);
                    }
                }else if (frm_active == 'form-observacion'){
                    alert(msj);
                    if (response > 0){
                        mostrar_observacion(response);
                    }
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }else{
        if (frm_active == 'form-informacion'){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: baseUrl,
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0) {
                        alert(msj);
                        $('[name=id_postulante]').val(response);
                    }else{
                        alert('Error, Intentelo más tarde');
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            alert('Seleccione un postulante');
        }
    }
}

function save_frm_files(url, msj, postu){
    var formData = new FormData($('#form-extras')[0]);
    formData.append('id_postulante', postu);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: url,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert(msj);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_postulante(frm_active){
    var id, perm;
    if (frm_active == 'form-informacion'){
        id = $('[name=id_postulante]').val();
        perm = 0;
    }else if (frm_active == 'form-formacion'){
        id = $('[name=id_formacion]').val();
        perm = 1;
    }else if (frm_active == 'form-experiencia'){
        id = $('[name=id_experiencia_laboral]').val();
        perm = 1;
    }else if (frm_active == 'form-extras'){
        id = $('[name=id_datos_extras]').val();
        perm = 1;
    }else if (frm_active == 'form-observacion'){
        id = $('[name=id_observacion]').val();  
        perm = 1;      
    }
    
    if (id > 0){
        alert(frm_active+' / '+id);
    }else{
        alert(frm_active+' / sin id');
    }
}

function cargarUbigeo(){
    $('#modal-ubigeo').modal({
        show: true,
        backdrop: 'static'
    });
    cargarDep();
}

function enviarUbigeo(){
    var activeTab = $('#tab-postulante ul li.active a').attr('type');
    var activeForm = "form-"+activeTab.substring(1);
    // alert(activeForm);
    var id_dist = $('#distri').val();
    baseUrl = 'traer_ubigeo/'+ id_dist;

    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#'+ activeForm + ' [name=ubigeo]').val(response);
            $('#modal-ubigeo').modal('hide');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}