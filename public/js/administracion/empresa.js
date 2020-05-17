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

        var id = $('[name=id_contribuyente]').val();
        if (activeForm == 'form-informacion' && id !== ''){
            actualizarForm(activeForm, id);
        }else if (activeForm == 'form-contacto' && id !== ''){
            actualizarForm(activeForm, id);
        }else if (activeForm == 'form-cuentas' && id !== ''){
            actualizarForm(activeForm, id);
        }
    });

    $('#listaEmpresas tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer label').text(idTr);
    });

    $('#ListaContacto tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        cargarValores('contacto', miTr);
    });
    $('#ListaCuentas tbody').on('click', 'tr', function(){
        var miTr = $(this).find("td").eq(0).html();
        changeStateButton('historial');
        cargarValores('cuentas', miTr);
    });

});

function consultaSunat(){
    var ruc = $('[name=nro_documento]').val();
    if (ruc.length > 0) {
        $('.loading').removeClass('invisible');
        $('#panel_consulta_sunat').removeClass('invisible');
        $.ajax({
            type: 'POST',
            url: 'consulta_sunat',
            data: 'ruc='+ ruc,
            dataType: 'JSON',
            beforeSend: function(){
                $(document.body).append('<span class="loading"><div></div></span>');
            },
            success: function(response){
                console.log(response);
                $('.loading').remove();
                $('[name=id_tipo_contribuyente]').val(response.id_tipo);
                $('[name=razon_social]').val(response.razon_social);
                $('[name=direccion]').val(response.direccion);
            }     
        });
    }else{
        alert('Ingrese el número de RUC');
    }
}

function selectValue(){
    var myId = $('.modal-footer label').text();
    $('[name=id_contribuyente]').val(myId);
    $('#modal-empresas').modal('hide');
    changeStateButton('historial');

    var activeTab = $('#tab-empresa ul li.active a').attr('type');
    var activeForm = "form-"+activeTab.substring(1);
    actualizarForm(activeForm, myId);
}

function actualizarForm(form, id){
    $('form').trigger('reset');
    if (form == 'form-informacion'){
        mostrar_informacion(id);
    }else if(form == 'form-contacto'){
        mostrar_contacto(id);
    }else if(form == 'form-cuentas'){
        mostrar_cuentas(id);
    }
}


/////////////////////////////
function mostrar_informacion(val){
    baseUrl = 'cargar_empresa/' + val;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_empresa]').val(response[0].id_empresa);
            $('[name=codigo]').val(response[0].codigo);
            $('[name=id_tipo_contribuyente]').val(response[0].id_tipo_contribuyente);
            $('[name=nro_documento]').val(response[0].nro_documento);
            $('[name=razon_social]').val(response[0].razon_social);
            $('[name=direccion_fiscal]').val(response[0].direccion_fiscal);
            $('[name=telefono]').val(response[0].telefono);
            $('[name=id_pais]').val(response[0].id_pais);
            $('[name=ubigeo]').val(response[0].ubigeo);
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_contacto(id){
    $('#empre-contact tr').empty();
    baseUrl = 'listar_contacto_empresa/'+ id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('#empre-contact').append(response);
            resizeSide();
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_cuentas(id){
    $('#empre-cta tr').empty();
    baseUrl = 'listar_cuentas_empresa/'+ id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#empre-cta').append(response);
            resizeSide();
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

////////////////////////////
function cargarUbigeo(){
    $('#modal-ubigeo').modal({
        show: true,
        backdrop: 'static'
    });
    cargarDep();
}
function modalEmpresa(){
    $('#modal-empresas').modal({
        show: true,
        backdrop: 'static'
    });
    listarEmpresa();
}
function listarEmpresa() {
    var vardataTables = funcDatatables();
    $('#listaEmpresas').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': 'listar_empresa',
        'columns': [
            {'data': 'id_contribuyente'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'direccion_fiscal'},
            {'data': 'telefono'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}]
    });
}

/////////////////////////
function save_empresa(data, action, frm_active){
    var id_contri = $('[name=id_contribuyente]').val();
    var msj;
    if (frm_active == 'form-informacion'){
        if (action == 'register'){
            baseUrl = 'guardar_empresa_contri';
            msj = 'Información de la Empresa registrada con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_empresa_contri';
            data = data + '&id_contribuyente=' + id_contri;
            msj = 'Información de la Empresa editada con exito';
        }
    }else if (frm_active == 'form-contacto'){
        data = data + '&id_contribuyente=' + id_contri;
        if (action == 'register'){
            baseUrl = 'guardar_contacto_empresa';
            msj = 'Datos de contacto registrado con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_contacto_empresa';
            msj = 'Datos de contacto editado con exito';
        }
    }else if (frm_active == 'form-cuentas'){
        if (action == 'register'){
            baseUrl = 'guardar_cuentas_empresa';
            data = data + '&id_contribuyente=' + id_contri;
            msj = 'Cuenta bancaria de la Empresa registrada con exito';
        }else if(action == 'edition'){
            baseUrl = 'editar_cuentas_empresa';
            data = data + '&id_contribuyente=' + id_contri;
            msj = 'Cuenta bancaria de la Empresa editada con exito';
        }
    }

    if (id_contri > 0){
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (frm_active == 'form-informacion'){
                    alert(msj);
                }else if (frm_active == 'form-contacto'){
                    alert(msj);
                    if (response > 0){
                        mostrar_contacto(response);
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
        if (frm_active == 'form-informacion'){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: baseUrl,
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
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