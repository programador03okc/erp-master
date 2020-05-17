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

        var id = $('[name=id_trabajador]').val();
        if (activeForm == 'form-alta' && id !== ''){
            actualizarForm(activeForm, id);
        }else if (activeForm == 'form-contrato' && id !== ''){
            actualizarForm(activeForm, id);
        }else if (activeForm == 'form-rol' && id !== ''){
            actualizarForm(activeForm, id);
        }else if (activeForm == 'form-cuentas' && id !== ''){
            actualizarForm(activeForm, id);
        }
    });

    $('#listaTrabajador tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer label').text(idTr);
    });

    $('#ListaRolTrab tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        cargarValores('rol', miTr);
    });
    $('#ListaContratoTrab tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        cargarValores('contrato', miTr);
    });
    $('#ListaCtasTrab tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        cargarValores('cuentas', miTr);
    });

});

function cargarValores(type, id){
    var baseUrl;
    if (type == 'contrato'){
        baseUrl = 'cargar_contrato_click/'+id;
    }else if(type == 'rol'){
        baseUrl = 'cargar_rol_click/'+id;
    }else if(type == 'cuentas'){
        baseUrl = 'cargar_cuenta_click/'+id;
    }
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            var data = response[0];
            if (type == 'contrato'){
                $('[name=id_contrato]').val(data.id_contrato);
                $('[name=motivo]').val(data.motivo);
                $('[name=id_tipo_contrato]').val(data.id_tipo_contrato);
                $('[name=id_modalidad]').val(data.id_modalidad);
                $('[name=fecha_inicio]').val(data.fecha_inicio);
                $('[name=fecha_fin]').val(data.fecha_fin);
                $('[name=id_horario]').val(data.id_horario);
                $('[name=id_centro_costo]').val(data.id_centro_costo).trigger('change.select2');
                $('[name=tipo_centro_costo]').val(data.tipo_centro_costo);
            }else if(type == 'rol'){
                $('[name=id_rol]').val(data.id_rol);
                $('[name=rol_id_tipo_planilla]').val(data.id_tipo_planilla);
                $('[name=id_empresa]').val(data.id_empresa);
                $('[name=id_sede]').val(data.id_sede);
                $('[name=id_grupo]').val(data.id_grupo);
                $('[name=id_area]').val(data.id_area);
                $('[name=id_cargo]').val(data.id_cargo).trigger('change.select2');
                $('[name=id_rol_concepto]').val(data.id_rol_concepto).trigger('change.select2');
                $('[name=nombre_area]').val(data.nombre_area);
                $('[name=fecha_ingreso]').val(data.fecha_inicio);
                $('[name=fecha_cese]').val(data.fecha_fin);
                $('[name=salario]').val(data.salario);
                var respo =  (data.responsabilidad == true) ? 1 : 2;
                $('[name=responsabilidad]').val(respo);
                var sctrs =  (data.sctr == true) ? 1 : 2;
                $('[name=sctr]').val(sctrs);
                $('[name=id_proyecto]').val(data.id_proyecto);
            }else if(type == 'cuentas'){
                $('[name=id_cuenta_bancaria]').val(data.id_cuenta_bancaria);
                $('[name=id_banco]').val(data.id_banco);
                $('[name=id_tipo_cuenta]').val(data.id_tipo_cuenta);
                $('[name=nro_cci]').val(data.nro_cci);
                $('[name=nro_cuenta]').val(data.nro_cuenta);
                $('[name=id_moneda]').val(data.id_moneda);
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function modalTrabajador(){
    $('#modal-trabajador').modal({
        show: true,
        backdrop: 'static'
    });
    listarTrabajador();
}

function listarTrabajador() {
    var vardataTables = funcDatatables();
    $('#listaTrabajador').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': 'listar_trabajador',
        'columns': [
            {'data': 'id_trabajador'},
            {'data': 'nro_documento'},
            {'data': 'datos_trabajador'},
            {'data': 'empresa'},
            {'data': 'sede'},
            {'data': 'grupo'},
            {'data': 'rol'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [2, 'asc']
        ]
    });
}

function selectValue(){
    var myId = $('#footer-lista label').text();
    $('[name=id_trabajador]').val(myId);
    $('#modal-trabajador').modal('hide');

    var activeTab = $('#tab-trabajador ul li.active a').attr('type');
    var activeForm = "form-"+activeTab.substring(1);
    actualizarForm(activeForm, myId);
}

function actualizarForm(form, id){
    $('form').trigger('reset');
    changeStateButton('historial');
    if (form == 'form-alta'){
        mostrar_trabajador(id, 2);
    }else if(form == 'form-contrato'){
        mostrar_contrato(id);
    }else if(form == 'form-rol'){
        mostrar_rol(id);
    }else if(form == 'form-cuentas'){
        mostrar_cuentas(id);
    }
}

function buscarPostulante(){
    var dni = $('[name=nro_documento]').val();
    mostrar_trabajador(dni, 1);
}

function mostrar_trabajador(val, type){
    if (type == 1){
        baseUrl = 'cargar_trabajador_dni/'+val; 
    }else{
        baseUrl = 'cargar_trabajador/'+val;
    }
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            var conf;
            var marj;
            if (type == 1){
                if (response[0].id_trabajador > 0){
                    alert('Trabajador encontrado en el sistema');
                    $('[name=id_trabajador]').val(response[0].data[0].id_trabajador);
                    $('[name=id_postulante]').val(response[0].data[0].id_postulante);
                    $('[name=datos_postulante]').val(response[0].data[0].datos_postulante);
                    $('[name=id_tipo_planilla]').val(response[0].data[0].id_tipo_planilla);
                    $('[name=id_tipo_trabajador]').val(response[0].data[0].id_tipo_trabajador);
                    $('[name=id_categoria_ocupacional]').val(response[0].data[0].id_categoria_ocupacional);
                    $('[name=id_pension]').val(response[0].data[0].id_pension);
                    $('[name=cuspp]').val(response[0].data[0].cuspp);
                    $('[name=hijos]').val(response[0].data[0].hijos);
                    $('[name=seguro]').val(response[0].data[0].seguro);
                    conf = (response[0].confianza === true) ? 1 : 2;
                    $('[name=confianza]').val(conf);
                    marj = (response[0].marcaje === 1) ? 1 : 2;
                    $('[name=marcaje]').val(marj);
                    changeStateButton('historial');
                }else{
                    if (response[0].id_postulante > 0){
                        alert('Postulante registrado, mas no como trabajador');
                        $('[name=id_trabajador]').val('');
                        $('[name=id_postulante]').val(response[0].data[0].id_postulante);
                        $('[name=datos_postulante]').val(response[0].data[0].datos_postulante);
                        $('[name=id_tipo_planilla]').val(0);
                        $('[name=id_tipo_trabajador]').val(0);
                        $('[name=id_categoria_ocupacional]').val(0);
                        $('[name=id_pension]').val(0);
                        $('[name=cuspp]').val('');
                        $('[name=hijos]').val(0);
                        $('[name=seguro]').val(0);
                        $('[name=confianza]').val(0);
                        $('[name=marcaje]').val(0);
                    }else{
                        alert('Postulante no encontrado en el sistema');
                        $('[name=id_trabajador]').val('');
                        $('[name=id_postulante]').val('');
                        $('[name=datos_postulante]').val('');
                        $('[name=id_tipo_planilla]').val(0);
                        $('[name=id_tipo_trabajador]').val(0);
                        $('[name=id_categoria_ocupacional]').val(0);
                        $('[name=id_pension]').val(0);
                        $('[name=cuspp]').val('');
                        $('[name=hijos]').val(0);
                        $('[name=seguro]').val(0);
                        $('[name=confianza]').val(0);
                        $('[name=marcaje]').val(0);
                    }
                }
            }else{
                $('[name=nro_documento]').val(response[0].nro_documento);
                $('[name=id_postulante]').val(response[0].id_postulante);
                $('[name=datos_postulante]').val(response[0].datos_postulante);
                $('[name=id_tipo_planilla]').val(response[0].id_tipo_planilla);
                $('[name=id_tipo_trabajador]').val(response[0].id_tipo_trabajador);
                $('[name=id_categoria_ocupacional]').val(response[0].id_categoria_ocupacional);
                $('[name=id_pension]').val(response[0].id_pension);
                $('[name=cuspp]').val(response[0].cuspp);
                $('[name=hijos]').val(response[0].hijos);
                $('[name=seguro]').val(response[0].seguro);
                conf = (response[0].confianza === true) ? 1 : 2;
                $('[name=confianza]').val(conf);
                marj = (response[0].marcaje === 1) ? 1 : 2;
                $('[name=marcaje]').val(marj);
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_contrato(id){
    $('#trab-ctt tr').empty();
    baseUrl = 'listar_contrato_trab/'+ id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#trab-ctt').append(response);
            resizeSide();
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_rol(id){
    $('#trab-rol tr').empty();
    baseUrl = 'listar_rol_trab/'+ id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#trab-rol').append(response);
            resizeSide();
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_cuentas(id){
    $('#trab-cta tr').empty();
    baseUrl = 'listar_cuentas_trab/'+ id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#trab-cta').append(response);
            resizeSide();
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_trabajador(data, action, frm_active){
    var id_trab = $('[name=id_trabajador]').val();
    var msj;
    if (frm_active == 'form-alta'){
        if (action == 'register'){
            baseUrl = 'guardar_alta_trabajador';
            msj = 'Alta del trabajador registrada con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_alta_trabajador';
            data = data + '&id_trabajador=' + id_trab;
            msj = 'Alta del trabajador editada con exito';
        }
    }else if (frm_active == 'form-contrato'){
        data = data + '&id_trabajador=' + id_trab;
        if (action == 'register'){
            baseUrl = 'guardar_contrato_trabajador';
            msj = 'Contrato del trabajador registrado con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_contrato_trabajador';
            msj = 'Contrato del trabajador editado con exito';
        }
    }else if (frm_active == 'form-rol'){
        if (action == 'register'){
            baseUrl = 'guardar_rol_trabajador';
            data = data + '&id_trabajador=' + id_trab;
            msj = 'Rol del trabajador registrado con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_rol_trabajador';
            data = data + '&id_trabajador=' + id_trab;
            msj = 'Rol del trabajador editado con exito';
        }
    }else if (frm_active == 'form-cuentas'){
        if (action == 'register'){
            baseUrl = 'guardar_cuentas_trabajador';
            data = data + '&id_trabajador=' + id_trab;
            msj = 'Cuenta bancaria del trabajador registrada con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_cuentas_trabajador';
            data = data + '&id_trabajador=' + id_trab;
            msj = 'Cuenta bancaria del trabajador editada con exito';
        }
    }

    if (id_trab > 0){
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                if (frm_active == 'form-alta'){
                    alert(msj);
                }else if (frm_active == 'form-contrato'){
                    alert(msj);
                    if (response > 0){
                        mostrar_contrato(response);
                    }
                }else if (frm_active == 'form-rol'){
                    if (response > 0){
                        alert(msj);
                        mostrar_rol(response);
                    }
                }else if (frm_active == 'form-cuentas'){
                    alert(msj);
                    if (response > 0){
                        mostrar_cuentas(response);
                    }
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }else{
        if (frm_active == 'form-alta'){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: baseUrl,
                data: data,
                dataType: 'JSON',
                success: function(response){
                    $('[name=id_trabajador]').val(response);
                    alert(msj);
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            alert('Seleccione un trabajador');
        }
    }
}

function cambiarSede(value){
    baseUrl = 'mostrar_grupo_sede/'+value;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            var htmls = '<option value="0" selected disabled>Elija una opción</option>';
            Object.keys(response).forEach(function (key){
                htmls += '<option value="'+response[key]['id_grupo']+'">'+response[key]['descripcion']+'</option>';
            })
            $('[name="id_grupo"]').html(htmls);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function cambiarGrupo(value){
    baseUrl = 'mostrar_area_grupo/'+value;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            var htmls = '<option value="0" selected disabled>Elija una opción</option>';
            Object.keys(response).forEach(function (key){
                htmls += '<option value="'+response[key]['id_area']+'">'+response[key]['descripcion']+'</option>';
            })
            $('[name="id_area"]').html(htmls);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function modal_area(){
    var id_emp = $('[name=id_empresa]').val();

    if (id_emp > 0){
        $('#modal-empresa-area').modal({
            show: true,
            backdrop: 'static'
        });
        cargarEstOrg(id_emp);
    }else{
        alert('Debe seleccionar la empresa');
        $('[name=id_empresa]').focus();
    }
}

function closeRole(id, trab){
    $('#tipo_termino').val('rol');
    $('#id_termino').val(id);
    $('#id_trab_termino').val(trab);
    $('#modal-terminos').modal({
        show: true,
        backdrop: 'static'
    });
}

function save_close(){
    var tipo = $('#tipo_termino').val();
    var termino = $('#id_termino').val();
    var trab = $('#id_trab_termino').val();
    var fecha = $('#fecha_termino').val();
    var baseUrl;
    if (tipo == 'rol'){
        baseUrl = 'actualizar_cierre_rol/' + termino + '/' + fecha;
    }else{
        baseUrl = 'actualizar_cierre_contrato/'+termino + '/' + fecha;
    }
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                if (tipo == 'rol'){
                    mostrar_rol(trab);
                }
                $('#modal-terminos').modal('hide');
            }else{
                alert('No se realizó la operación.. Intentelo más tarde');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}