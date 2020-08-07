$(function(){
    $("[name=id_moneda]").val(1);
    $("#tab-producto section:first form").attr('form', 'formulario');
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
        console.log('activeForm: '+activeForm);

        var id = $('[name=id_producto]').val();
        console.log('id:'+id);

        if (activeForm == "form-ubicacion" && id !== ""){
            clearDataTable();
            listar_ubicaciones(id);
            var abr = $('[name=abr_id_unidad_medida]').text();
            console.log('abr'+abr);
            $('[name=id_producto]').val(id);
            $('[name=abreviatura]').text(abr);
        }
        else if (activeForm == "form-promocion" && id !== ""){
            clearDataTable();
            listar_promociones(id);
            $('[name=id_producto]').val(id);
        }
        else if (activeForm == "form-serie" && id !== ""){
            clearDataTable();
            listar_series(id);
            $('[name=id_producto]').val(id);
        }

        //inicio botones (estados)
        $(activeTab).attr('hidden', false);
        // changeStateButton('cancelar');
        // clearForm(activeForm);
    });

    $('[name=afecto_igv]').on('ifChecked ifUnchecked', function(event){
        if (event.type.replace('if','').toLowerCase()=='checked'){
            $(this).val('1');
        } else if (event.type.replace('if','').toLowerCase()=='unchecked'){
            $(this).val('0');
        }
    });
    $('[name=series]').on('ifChecked ifUnchecked', function(event){
        if (event.type.replace('if','').toLowerCase()=='checked'){
            $(this).val('1');
        } else if (event.type.replace('if','').toLowerCase()=='unchecked'){
            $(this).val('0');
        }
    });
    $('#imagen').change(function(e) {
        console.log(e);
        guardar_imagen();
    });
});

function mostrar_producto(id){
    $(":file").filestyle('disabled', false);
    baseUrl = 'mostrar_producto/'+id;
    console.log(baseUrl);
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response['producto'][0]);
            console.log(response);
            $('[name=id_producto]').val(response['producto'][0].id_producto);
            $('#codigo').text(response['producto'][0].codigo);
            $('[name=codigo_anexo]').val(response['producto'][0].codigo_anexo);
            $('[name=part_number]').val(response['producto'][0].part_number);
            $('[name=descripcion]').val(response['producto'][0].descripcion);
            $('[name=id_unidad_medida]').val(response['producto'][0].id_unidad_medida);
            $('[name=id_subcategoria]').val(response['producto'][0].id_subcategoria).trigger('change.select2');
            $('[name=id_categoria]').val(response['producto'][0].id_categoria).trigger('change.select2');
            $('[name=id_clasif]').val(response['producto'][0].id_clasif).trigger('change.select2');
            $('#tipo_descripcion').text(response['producto'][0].tipo_descripcion);
            $('#cat_descripcion').text(response['producto'][0].cat_descripcion);
            $('#subcat_descripcion').text(response['producto'][0].subcat_descripcion);
            $('[name=subcat_descripcion]').val(response['producto'][0].subcat_descripcion);
            $('[name=id_unid_equi]').val(response['producto'][0].id_unid_equi);
            $('[name=cant_pres]').val(response['producto'][0].cant_pres);
            $('[name=afecto_igv]').iCheck((response['producto'][0].afecto_igv)?'check':'uncheck');
            $('[name=afecto_igv]').val((response['producto'][0].afecto_igv)?'1':'0');
            $('[name=series]').iCheck((response['producto'][0].series)?'check':'uncheck');
            $('[name=series]').val((response['producto'][0].series)?'1':'0');
            $('[name=estado]').val(response['producto'][0].estado);
            $('[name=notas]').val(response['producto'][0].notas);
            $('[name=id_moneda]').val(response['producto'][0].id_moneda);
            // if (response['producto'][0].imagen !== "" &&
            //     response['producto'][0].imagen !== null){
            //     $('#img').attr('src','files/productos/'+response['producto'][0].imagen);
            // } else {
            //     $('#img').attr('src','img/product-default.png');
            // }
            $('[id=fecha_registro] label').text('');
            $('[id=fecha_registro] label').append(formatDateHour(response['producto'][0].fecha_registro));

            /* Antiguos */
            var antiguos = response['antiguos'];
            console.log(antiguos);
            var htmls = '';
            for (x=0; x<antiguos.length; x++){
                htmls += '<tr><td>'+antiguos[x].cod_antiguo+
                '</td><td>'+((antiguos[x].estado == 1) ? 'Activo' : 'Inactivo' )+'</td></tr>';
            }
            $('#antiguos tbody').html(htmls);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_producto(data, action){
    console.log(data);
    var msj = validaProducto();
    if (msj.length > 0){
        alert(msj);
    } else {
        if (action == 'register'){
            baseUrl = 'guardar_producto';
        } else if (action == 'edition'){
            baseUrl = 'actualizar_producto';
        }
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                console.log('id_producto:'+response['id_producto']);
                if (response['msj'].length > 0){
                    alert(response['msj']);
                } else {
                    alert('Producto registrado con exito');
                    changeStateButton('guardar');
                    $('#form-general').attr('type', 'register');
                    changeStateInput('form-general', true);
                    
                    console.log('id_producto:'+response['id_producto']);
                    mostrar_producto(response['id_producto']);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function anular_producto(ids){
    baseUrl = 'anular_producto/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Producto anulado con exito');
                $('#listaProducto').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-producto');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guardar_imagen(){
    // alert('guardar_imagen');
    baseUrl = 'guardar_imagen';
    let timestamp = Math.floor( Date.now() );
    console.log('Antes del ajax: ' + $('#img').attr('src') );
    var formData = new FormData($('#form-general')[0]);
    console.log(formData);
    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response.status > 0){
                alert('Imagen cargada con exito');
                console.log($('#img')[0]);
                setTimeout(function(){
                    $('#img').attr('src', 'files/productos/'+response.imagen+'?ver=' + timestamp);
                    console.log('Después del ajax: ' + $('#img').attr('src') );
                }, 500); 
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function posicion(){
    $id_posicion = $('[name=id_posicion]').val();
    console.log($id_posicion);
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'almacen_posicion/'+$id_posicion,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=alm_descripcion]').val(response[0].alm_descripcion);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function unid_abrev($id_name){
    console.log($id_name);
    $unidad = $('select[name="'+$id_name+'"] option:selected').text();
    console.log($unidad);
    $abreviatura = $unidad.split(" - ");
    if ($abreviatura.length > 0){
        console.log($abreviatura[1]);
        $('[name=abr_'+$id_name+']').text($abreviatura[1]);
    } else {
        $('[name=abr_'+$id_name+']').text("");
    }
}

function validaProducto(){
    var id_categoria = $('[name=id_categoria]').val();
    var id_subcategoria = $('[name=id_subcategoria]').val();
    var id_clasif = $('[name=id_clasif]').val();
    var descripcion = $('[name=descripcion]').val();
    var part_number = $('[name=part_number]').val();
    // var id_moneda = $('[name=id_moneda]').val();
    var id_unidad_medida = $('[name=id_unidad_medida]').val();
    var msj = '';

    if (id_categoria == '0'){
        msj+='\n Es necesario que elija una Categoría';
    }
    if (id_subcategoria == '0'){
        msj+='\n Es necesario que elija una SubCategoría';
    }
    if (id_clasif == '0'){
        msj+='\n Es necesario que alija una Clasificación';
    }
    if (descripcion == ''){
        msj+='\n Es necesario que ingrese una Descripción';
    }
    if (part_number == ''){
        msj+='\n Es necesario que ingrese una Part Number';
    }
    if (id_unidad_medida == '0'){
        msj+='\n Es necesario que seleccione una Unidad de Medida';
    }
    return msj;
}

function listar_promociones(id_producto){
    var vardataTables = funcDatatables();
    $('#listaPromocion').dataTable({
        'dom': vardataTables[1],
        // 'buttons': vardataTables[2],
        'buttons': [
            {
                text: "Agregar Producto Promocionado",
                className: 'btn btn-success',
                action: function(){
                    accion_origen = 'crear_promocion';
                    productoModal();
                }
            }
        ],
        'language' : vardataTables[0],
        'bDestroy' : true,
        'ajax': 'listar_promociones/'+id_producto,
        'columns': [
            {'data': 'id_promocion'},
            {'data': 'descripcion_producto'},
            {'data': 'descripcion_producto_promocion'},
            {'data': 'fecha_registro'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            },
            {'data': 'nombre_corto'}
        ],
        'columnDefs': [
            {'aTargets': [0], 'sClass': 'invisible'},
            {'render': function (data, type, row){
                    return '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Dar de Baja" data-id="'+row['id_promocion']+'">'+
                    '<i class="fas fa-trash"></i></button>';
                }, targets: 6
            }
        ]
    });
}

$('#listaPromocion tbody').on("click","button.anular", function(){
    var id = $(this).data('id');
    anular_promocion(id);
});

function anular_promocion(id_promocion){
    $.ajax({
        type: 'GET',
        url: 'anular_promocion/'+id_promocion,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Promoción anulada con éxito!');
                var id = $('[name=id_producto]').val();
                listar_promociones(id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function crear_promocion(id_seleccionado){
    var id = $('[name=id_producto]').val();
    var data = 'id_producto='+id+
               '&id_producto_promocion='+id_seleccionado;
    $.ajax({
        type: 'POST',
        url: 'crear_promocion',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Promoción registrada con éxito!');
                // $('#listaPromocion').DataTable().ajax.reload();
                listar_promociones(id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}