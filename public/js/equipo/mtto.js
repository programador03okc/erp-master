function mostrar_mtto(id){
    baseUrl = 'mostrar_mtto/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            // var htmls = '<option value="0" disabled>Elija una opción</option>';
            // Object.keys(response['sedes']).forEach(function (key){
            //     htmls += '<option value="'+response['sedes'][key]['id_sede']+'">'+response['sedes'][key]['descripcion']+'</option>';
            // });
            // $('[name=id_sede]').html(htmls);

            // var htmls = '<option value="0" disabled>Elija una opción</option>';
            // Object.keys(response['grupos']).forEach(function (key){
            //     htmls += '<option value="'+response['grupos'][key]['id_grupo']+'">'+response['grupos'][key]['descripcion']+'</option>';
            // });
            // $('[name=id_grupo]').html(htmls);

            // var htmls = '<option value="0" disabled>Elija una opción</option>';
            // Object.keys(response['areas']).forEach(function (key){
            //     htmls += '<option value="'+response['areas'][key]['id_area']+'">'+response['areas'][key]['descripcion']+'</option>';
            // });
            // $('[name=id_area]').html(htmls);

            $('[name=id_mtto]').val(response.id_mtto);
            $('[name=fecha_mtto]').val(response.fecha_mtto);
            $('[name=id_proveedor]').val(response.id_proveedor);
            $('[name=id_equipo]').val(response.id_equipo);
            $('[name=codigo]').val(response.codigo);
            $('[name=kilometraje]').val(response.kilometraje);
            $('[name=costo_total]').val(response.costo_total);
            $('[name=observaciones]').val(response.observaciones);
            $('[name=id_area]').val(response.id_area);
            $('[name=nombre_area]').val(response.nombre_area);
            $('[name=razon_social]').val(response.razon_social);
            $('[name=id_grupo]').val(response.id_grupo);
            $('[name=id_sede]').val(response.id_sede);
            $('[name=id_empresa]').val(response.id_empresa);
            
            listar_mtto_pendientes(response.id_equipo);
            listar_mtto_detalle(response.id_mtto);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function save_mtto(data, action){
    console.log(action);
    if (action == 'register'){
        baseUrl = 'guardar_mtto';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_mtto';
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
                alert('Mantenimiento registrado con éxito');
                var id_equipo = $('[name=id_equipo]').val();
                console.log('id_equipo'+id_equipo);
                mostrar_mtto(response);
                listar_mtto_pendientes(id_equipo);
                
                changeStateButton('guardar');
                $('#form-mtto').attr('type', 'register');
				changeStateInput('form-mtto', true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_mtto(ids){
    baseUrl = 'anular_mtto/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Mantenimiento anulado con éxito');
                changeStateButton('anular');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_mtto_pendientes(id_equipo){
    console.log(id_equipo);
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_mtto_pendientes/'+id_equipo,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var option = '';
            for (var i=0;i<response.length;i++){
                option+='<option value="'+response[i].id_programacion+'">'+response[i].descripcion+' - '+response[i].fecha_vencimiento+'</option>';
            }
            $('[name=id_programacion]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
/*function agregar_mtto(){
    var token = $('#token').val();
    var id_mtto = $("[name=id_mtto]").val();
    var id_programacion = $("[name=id_programacion]").val();
    var descripcion = $('select[name="id_programacion"] option:selected').text();
    
    var data =  'id_mtto='+id_mtto+
                '&id_programacion='+id_programacion+
                '&descripcion='+descripcion+
                '&resultado='+
                '&tp_mantenimiento=1'+//preventivo
                '&cantidad=0'+
                '&precio_unitario=0'+
                '&precio_total=0';
    console.log(data);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_mtto_detalle',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            listar_mtto_detalle(id_mtto);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function editar_detalle(id){
    $("#det-"+id+" td").find("input[name=cantidad]").attr('disabled',false);
    $("#det-"+id+" td").find("input[name=unitario]").attr('disabled',false);
    $("#det-"+id+" td").find("i.blue").removeClass('visible');
    $("#det-"+id+" td").find("i.blue").addClass('oculto');
    $("#det-"+id+" td").find("i.green").removeClass('oculto');
    $("#det-"+id+" td").find("i.green").addClass('visible');
}
function update_detalle(id){
    var res = $("#det-"+id+" td").find("input[name=resultado]").val();
    var cant = $("#det-"+id+" td").find("input[name=cantidad]").val();
    var uni = $("#det-"+id+" td").find("input[name=unitario]").val();
    var tot = $("#det-"+id+" td").find("input[name=total]").val();
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'update_mtto_detalle',
        data: 'id_mtto_det='+id+
            '&resultado='+res+
            '&cantidad='+cant+
            '&unitario='+uni+
            '&total='+tot,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item actualizado con éxito');
                $("#det-"+id+" td").find("input").attr('disabled',true);
                $("#det-"+id+" td").find("i.blue").removeClass('oculto');
                $("#det-"+id+" td").find("i.blue").addClass('visible');
                $("#det-"+id+" td").find("i.green").removeClass('visible');
                $("#det-"+id+" td").find("i.green").addClass('oculto');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}*/
function listar_mtto_detalle(id_mtto){
    $('#detalle tbody').html('');
    console.log('id_mtto'+id_mtto);
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_mtto_detalle/'+id_mtto,
        dataType: 'JSON',
        success: function(response){
            $('#detalle tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
/*
function cambiarEmpresa(value){
    console.log('cambiarEMpresa'+value);
    baseUrl = 'mostrar_combos_emp/'+value;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(result_emp){
            var sedes = result_emp.sedes;
            var htmls = '<option value="0" disabled>Elija una opción</option>';
            Object.keys(sedes).forEach(function (key){
                htmls += '<option value="'+sedes[key]['id_sede']+'">'+sedes[key]['descripcion']+'</option>';
            });
            console.log(htmls);
            $('[name="id_sede"]').html(htmls);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function cambiarSede(value){
    console.log('sede'+value);
    baseUrl = 'mostrar_grupo_sede/'+value;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(result_se){
            var htmls = '<option value="0" disabled>Elija una opción</option>';
            Object.keys(result_se).forEach(function (key){
                htmls += '<option value="'+result_se[key]['id_grupo']+'">'+result_se[key]['descripcion']+'</option>';
            });
            console.log(htmls);
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
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(result_gr){
            var htmls = '<option value="0" disabled>Elija una opción</option>';
            Object.keys(result_gr).forEach(function (key){
                htmls += '<option value="'+result_gr[key]['id_area']+'">'+result_gr[key]['descripcion']+'</option>';
            });
            console.log(htmls);
            $('[name="id_area"]').html(htmls);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}*/
function ver_kilometraje(){
    var tp = $('select[name="id_equipo"] option:selected').text();
    var cod = parseInt(tp.substr(0,2));
    console.log('tipo'+cod);
    if (cod !== 1){
        $('#kilometraje').addClass('oculto');
    } else {
        $('#kilometraje').removeClass('oculto');
    }
}
function modal_area(){
    var id_emp = $('[name=id_empresa]').val();
    if (id_emp > 0){
        $('#modal-empresa-area').modal({
            show: true,
            backdrop: 'static'
        });
        cargarEstOrg(id_emp);
    } else {
        alert("Debe seleccionar la empresa");
        $('[name=id_empresa]').focus();
    }
}