let detalle_requerimiento = [];
let detalle_ingresa = [];
let detalle_sale = [];
let tab_origen = null;

function open_despacho_create(data){
    $('#modal-orden_despacho_create').modal({
        show: true
    });
    console.log('open_despacho_create');
    console.log(data);
    $("#submit_orden_despacho").removeAttr("disabled");
    $('[name=tipo_entrega]').val('MISMA CIUDAD').trigger('change.select2');
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $('[name=tiene_transformacion]').val(data.tiene_transformacion ? 'si' : 'no');
    $('[name=direccion_destino]').val(data.direccion_entrega);
    $('[name=ubigeo]').val(data.id_ubigeo_entrega);
    $('[name=name_ubigeo]').val(data.ubigeo_descripcion);
    $('[name=tipo_cliente]').val(data.tipo_cliente);
    $('[name=id_almacen]').val((data.id_almacen !== null && data.id_almacen !== 0) ? data.id_almacen : '');
    $('[name=almacen_descripcion]').val(data.almacen_descripcion !== null ? data.almacen_descripcion : '');
    $('[name=id_sede]').val(data.sede_requerimiento !== null ? data.sede_requerimiento : '');
    $('[name=telefono_cliente]').val(data.telefono);
    $('[name=correo_cliente]').val(data.email);
    $('[name=id_cc]').val(data.id_cc);
    $('[name=part_number_transformado]').val('');
    $('[name=cantidad_transformado]').val('');
    $('[name=descripcion_transformado]').val('');
    $('[name=comentario_transformado]').val('');

    // $('#'+data.documento+'').prop('checked', true);
    if (data.tipo_cliente == 1){
        $('#Boleta').prop('checked', true);
    } 
    else if (data.tipo_cliente == 2){
        $('#Factura').prop('checked', true);
    }

    if (data.id_persona !== null){
        $('[name=id_persona]').val(data.id_persona);
        $('[name=dni_persona]').val(data.dni_persona);
        $('[name=nombre_persona]').val(data.nombre_persona);
        $('[name=dni_persona]').show();
        $('[name=nombre_persona]').show();

        $('[name=id_cliente]').val('');
        $('[name=cliente_ruc]').val('');
        $('[name=cliente_razon_social]').val('');
        $('[name=cliente_ruc]').hide();
        $('[name=cliente_razon_social]').hide();
    }
    else if (data.id_cliente !== null){
        $('[name=id_cliente]').val(data.id_cliente);
        $('[name=cliente_ruc]').val(data.cliente_ruc);
        $('[name=cliente_razon_social]').val(data.cliente_razon_social);
        $('[name=cliente_ruc]').show();
        $('[name=cliente_razon_social]').show();

        $('[name=id_persona]').val('');
        $('[name=dni_persona]').val('');
        $('[name=nombre_persona]').val('');
        $('[name=dni_persona]').hide();
        $('[name=nombre_persona]').hide();
    }
    $("#detalleItemsReq").hide();
    $("#despachoExterno").show();

    $('#detalleSale tbody').html('');
            
    $('[name=fecha_despacho]').val(fecha_actual());
    $('[name=fecha_entrega]').val(fecha_actual());
    
    detalle_requerimiento = [];
    detalle_ingresa = [];
    detalle_sale = [];

    detalleRequerimiento(data.id_requerimiento).then(function (response) {

        var html = '';
        console.log('det');
        response.forEach(element => {
            var ing = (element.suma_ingresos !== null ? parseFloat(element.suma_ingresos) : 0);
            var cant = ing - (element.suma_despachos !== null ? parseFloat(element.suma_despachos) : 0);
            
            if (cant > 0){
                html+='<tr id="'+element.id_detalle_requerimiento+'">'+
                '<td><input type="checkbox" id="detalle" value="'+element.id_detalle_requerimiento+'" onChange="changeCheckIngresa(this,'+element.id_detalle_requerimiento+');"/></td>'+
                '<td>'+(element.producto_codigo !== null ? element.producto_codigo : '')+'</td>'+
                '<td>'+(element.part_number !== null ? element.part_number : '')+'</td>'+
                '<td>'+(element.producto_descripcion !== null ? element.producto_descripcion : element.descripcion_adicional)+'</td>'+
                // '<td>'+(element.almacen_descripcion !== null ? element.almacen_descripcion : '')+'</td>'+
                '<td>'+element.cantidad+'</td>'+
                '<td>'+(element.abreviatura !== null ? element.abreviatura : '')+'</td>'+
                '<td>'+(element.suma_ingresos !== null ? element.suma_ingresos : '0')+'</td>'+
                '<td>'+(element.suma_despachos !== null ? element.suma_despachos : '0')+'</td>'+
                '<td><input type="number" id="'+element.id_detalle_requerimiento+'cantidad" value="'+cant+'" max="'+cant+'" min="0" style="width: 80px;"/></td>'+
                '<td><span class="label label-'+element.bootstrap_color+'">'+element.estado_doc+'</span></td>'+
                '<td><i class="fas fa-code-branch boton btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Agregar Instrucciones" onClick="verInstrucciones('+element.id_detalle_requerimiento+');"></i>'+
                (element.series ? '<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Ver Series" onClick="verSeries('+element.id_detalle_requerimiento+');"></i>' : '')+
                '</td></tr>';
                
            }
            detalle_requerimiento.push({
                'id_detalle_requerimiento'  : element.id_detalle_requerimiento,
                'id_producto'               : element.id_producto,
                'cantidad'                  : element.cantidad,
                'suma_ingresos'             : element.suma_ingresos,
                'suma_despachos'            : element.suma_despachos,
                'part_number_transformado'  : null,
                'descripcion_transformado'  : null,
                'comentario_transformado'   : null,
                'cantidad_transformado'     : null,
            });
            
            if (element.tiene_transformacion){
                detalle_sale.push({
                    'id_detalle_requerimiento'  : element.id_detalle_requerimiento,
                    'id_producto'               : element.id_producto,
                    'part_number'               : element.part_number,
                    'codigo'                    : element.producto_codigo,
                    'descripcion'               : element.producto_descripcion,
                    'id_unidad_medida'          : element.id_unidad_medida,
                    'abreviatura'               : element.abreviatura,
                    'cantidad'                  : element.cantidad,
                });
            }
        });

        $('#detalleRequerimientoOD tbody').html(html);
        mostrarSale();

        if (data.tiene_transformacion && 
            data.count_despachos_internos == 0){
            $('[name=aplica_cambios]').prop('checked', true);
            on();
        } else {
            $('[name=aplica_cambios]').prop('checked', false);
            off();
        }

    }).catch(function (err) {
        console.log(err)
    });

}

function detalleRequerimiento(id_requerimiento){

    return new Promise(function (resolve, reject) {

        $.ajax({
            type: 'GET',
            url: 'verDetalleRequerimiento/'+id_requerimiento,
            dataType: 'JSON',
            success(response){
                console.log('promesa');
                resolve(response); // Resolve promise and go to then()
            },
            error: function (err) {
                reject(err) // Reject the promise and go to catch()
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    });
}

function verSeries(id_detalle_requerimiento){
    if (id_detalle_requerimiento !== null){
        $.ajax({
            type: 'GET',
            url: 'verSeries/'+id_detalle_requerimiento,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('#modal-ver_series').modal({
                    show: true
                });
                var tr = '';
                var i = 1;
                response.forEach(element => {
                    tr+=`<tr id="reg-${element.serie}">
                            <td class="numero">${i}</td>
                            <td><input type="text" class="oculto" name="series" value="${element.serie}"/>${element.serie}</td>
                            <td>${element.serie_guia_com}-${element.numero_guia_com}</td>
                            <td>${element.serie_guia_ven !== null ? (element.serie_guia_ven+'-'+element.numero_guia_ven) : ''}</td>
                        </tr>`;
                    i++;
                });
                $('#listaSeries tbody').html(tr);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function verInstrucciones(id_detalle_requerimiento){
    $('#modal-od_transformacion').modal({
        show: true
    });
    $('[name=id_detalle_requerimiento]').val(id_detalle_requerimiento);
    $("#submit_od_transformacion").removeAttr("disabled");
}

$("#form-od_transformacion").on("submit", function(e){
    e.preventDefault();
    var id_detalle_requerimiento = $('[name=id_detalle_requerimiento]').val();
    var ing = detalle_ingresa.find(element => element.id_detalle_requerimiento == id_detalle_requerimiento);
    var data = $(this).serializeArray();
    console.log(data);
    // var indexed_array = {};
    $.map(data, function(n, i){
        ing[n['name']] = n['value'];
        // indexed_array[n['name']] = n['value'];
    });
    // ing.transformacion = indexed_array;
    console.log(detalle_ingresa);
    $('#modal-od_transformacion').modal('hide');
});

function openCliente(){
    var tipoCliente = $('[name=tipo_cliente]').val();
    if (tipoCliente == 1){
        modalPersona();
    } else {
        clienteModal();
    }
}

function changeTipoCliente(e){
    limpiarCampos(e.target.value);
}

function limpiarCampos(tipo){
    if (tipo == 1){
        $('[name=id_cliente]').val('');
        $('[name=cliente_ruc]').val('');
        $('[name=cliente_razon_social]').val('');
        $('[name=cliente_ruc]').hide();
        $('[name=cliente_razon_social]').hide();

        $('[name=id_persona]').val('');
        $('[name=dni_persona]').val('');
        $('[name=nombre_persona]').val('');
        $('[name=dni_persona]').show();
        $('[name=nombre_persona]').show();
    }
    else if (tipo == 2){
        $('[name=id_cliente]').val('');
        $('[name=cliente_ruc]').val('');
        $('[name=cliente_razon_social]').val('');
        $('[name=cliente_ruc]').show();
        $('[name=cliente_razon_social]').show();

        $('[name=id_persona]').val('');
        $('[name=dni_persona]').val('');
        $('[name=nombre_persona]').val('');
        $('[name=dni_persona]').hide();
        $('[name=nombre_persona]').hide();
    }
}

$("#form-orden_despacho").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var msj = validaOrdenDespacho();
    var json_detalle_ingresa = [];
    var json_detalle_sale = [];
    var validaCampos = '';

    if (msj.length > 0){
        alert(msj);
    } 
    else {
        var serial = $(this).serialize();
        var doc = $('input[name=optionsRadios]:checked').val();

        $("#detalleRequerimientoOD input[type=checkbox]:checked").each(function(){
            var id_detalle_requerimiento = $(this).val();
            var json = detalle_ingresa.find(element => element.id_detalle_requerimiento == id_detalle_requerimiento);
            
            json_detalle_ingresa.push({
                'cantidad'      : $(this).parent().parent().find('td input[id='+id_detalle_requerimiento+'cantidad]').val(),
                'id_detalle_requerimiento' : json.id_detalle_requerimiento,
                'id_producto'   : json.id_producto,
                // 'descripcion'   : json.descripcion_adicional,
                'part_number_transformado'   : json.part_number_transformado,
                'descripcion_transformado'   : json.descripcion_transformado,
                'comentario_transformado'   : json.comentario_transformado,
                'cantidad_transformado'   : json.cantidad_transformado,
            });
        });

        $("#detalleSale tbody tr").each(function(){
            var id_producto = $(this)[0].id;
            var json = detalle_sale.find(element => element.id_producto == id_producto);
            var cant = $(this).parent().parent().find('td input[type=number]').val();

            if (cant == '' || cant == null){
                validaCampos += 'El producto '+json.descripcion+' requiere que cantidad.\n'; 
            }
            json_detalle_sale.push({
                'id_detalle_requerimiento' : json.id_detalle_requerimiento,
                'cantidad' : cant,
                'id_producto' : json.id_producto
            });
        });

        console.log(json_detalle_ingresa);
        console.log(json_detalle_sale);

        if (validaCampos.length > 0){
            alert(validaCampos);
        } else {
            var ac = $('[name=aplica_cambios_valor]').val();
            var m = '';
            if (ac == 'si'){
                if (json_detalle_ingresa.length == 0){
                    m = 'No ha seleccionado items para despachar.';
                }
                if (json_detalle_sale.length == 0){
                    m += '\nNo ha ingresado items transformados.';
                }
            } else {
                if (detalle_requerimiento.length == 0){
                    m = 'No hay items para despachar.';
                }
            }
            if (m == ''){
                var data = serial+'&documento='+doc+
                                '&detalle_ingresa='+JSON.stringify(json_detalle_ingresa)+
                                '&detalle_requerimiento='+JSON.stringify(detalle_requerimiento)+
                                '&detalle_sale='+JSON.stringify(json_detalle_sale);
                console.log(data);
                guardar_orden_despacho(data);
            } else {
                alert(m);
            }
        }
    }
});

function guardar_orden_despacho(data){
    $("#submit_orden_despacho").attr('disabled','true');

    $.ajax({
        type: 'POST',
        url: 'guardar_orden_despacho',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            alert(response);
            $('#modal-orden_despacho_create').modal('hide');
            
            if (tab_origen == 'confirmados'){
                // $('#requerimientosConfirmados').DataTable().ajax.reload();
                listarRequerimientosConfirmados(permiso_temp);
                od_seleccionadas = [];
            } 
            else if (tab_origen == 'enProceso'){
                listarRequerimientosPendientes(permiso_temp);
                od_seleccionadas = [];
            }
            actualizaCantidadDespachosTabs();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("[name=aplica_cambios]").on( 'change', function() {
    if( $(this).is(':checked') ) {
        on();
    } else {
        off();
    }
});

function on(){
    $("#detalleItemsReq").show();
    $("[name=aplica_cambios_valor]").val('si');
    $('#name_title').text('Despacho Interno');
    $('#name_title').removeClass();
    $('#name_title').addClass('red');
    $("#despachoExterno").hide();

    $("[name=seleccionar_todos]").prop('checked', true);
    on_todos();
    // $("#detalleRequerimientoOD tbody tr").each(function(){
    //     // $(this).find("td input[id=detalle]").prop('checked', true);
    //     $(this).find("td input[type=checkbox]").prop('checked', true);
    // });
    
    // detalle_requerimiento.forEach(function(element){
    //     var ing = (element.suma_ingresos !== null ? parseFloat(element.suma_ingresos) : 0);
    //     var cant = ing - (element.suma_despachos !== null ? parseFloat(element.suma_despachos) : 0);
    //     if (cant > 0){
    //         detalle_ingresa.push(element);
    //     }
    // });
}

function off(){
    $("#detalleItemsReq").hide();
    $("[name=aplica_cambios_valor]").val('no');
    $('#name_title').text('Despacho Externo');
    $('#name_title').removeClass();
    $('#name_title').addClass('blue');
    $("#despachoExterno").show();

    $("[name=seleccionar_todos]").prop('checked', false);
    // detalle_ingresa = [];
    // $("#detalleRequerimientoOD tbody tr").each(function(){
    //     $(this).find("td input[type=checkbox]").prop('checked', false);
    // });
    off_todos();
}

$("[name=optionsRadios]").on( 'change', function() {
    if( $(this).is(':checked') ) {
        var tipo = null;
        if ($(this).val() == 'Factura'){
            tipo = 2;
        } else {
            tipo = 1;
        }
        $('[name=tipo_cliente]').val(tipo);
        limpiarCampos(tipo);
    }
});

$("[name=seleccionar_todos]").on( 'change', function() {
    if( $(this).is(':checked') ) {
        on_todos();
    } else {
        off_todos();
    }
});

function on_todos(){
    console.log('on_todos');
    // detalle_ingresa = detalle_requerimiento;
    detalle_requerimiento.forEach(function(element){
        var ing = (element.suma_ingresos !== null ? parseFloat(element.suma_ingresos) : 0);
        var cant = ing - (element.suma_despachos !== null ? parseFloat(element.suma_despachos) : 0);
        if (cant > 0){
            detalle_ingresa.push(element);
        }
    });
    $("#detalleRequerimientoOD tbody tr").each(function(){
        $(this).find("td input[type=checkbox]").prop('checked', true);
    });
}

function off_todos(){
    console.log('off_todos');
    detalle_ingresa = [];
    $("#detalleRequerimientoOD tbody tr").each(function(){
        $(this).find("td input[type=checkbox]").prop('checked', false);
    });
}

function changeCheckIngresa(checkbox, id_detalle_requerimiento){
    console.log(checkbox.checked+' id_detalle_requerimiento'+id_detalle_requerimiento);
    if (checkbox.checked) {
        var nuevo = detalle_requerimiento.find(element => element.id_detalle_requerimiento == id_detalle_requerimiento);
        detalle_ingresa.push(nuevo);
    } else {
        var index = detalle_ingresa.findIndex(function(item, i){
            return item.id_detalle_requerimiento == id_detalle_requerimiento;
        });
        detalle_ingresa.splice(index,1);
    }
    console.log(detalle_ingresa);
}

function validaOrdenDespacho(){
    var tpcli = $('[name=tipo_cliente]').val();
    var clie = $('[name=id_cliente]').val();
    var perso = $('[name=id_persona]').val();
    var ubig = $('[name=ubigeo]').val();
    var dir = $('[name=direccion_destino]').val();
    var telf = $('[name=telefono_cliente]').val();
    var mail = $('[name=correo_cliente]').val();
    var hora = $('[name=hora_despacho]').val();
    var msj = '';

    if (tpcli == 1){
        if (perso == ''){
            msj+='\n Es necesario que ingrese los datos del Cliente';
        }
    } else if (tpcli == 2){
        if (clie == ''){
            msj+='\n Es necesario que ingrese los datos del Cliente';
        }
    }
    if (ubig == ''){
        msj+='\n Es necesario que ingrese un Ubigeo Destino';
    }
    if (dir == ''){
        msj+='\n Es necesario que ingrese una Dirección Destino';
    }
    if (telf == ''){
        msj+='\n Es necesario que ingrese un Teléfono';
    }
    if (mail == ''){
        msj+='\n Es necesario que ingrese un Email';
    }
    if (hora == ''){
        msj+='\n Es necesario que ingrese una Hora';
    }
    return msj;
}

function mostrarSale(){
    var html = '';
    var i = 1;
    detalle_sale.forEach(element => {
        html+=`<tr id="${element.id_producto}">
        <td>${i}</td>
        <td>${(element.codigo !== null ? element.codigo : '')}</td>
        <td>${(element.part_number !== null ? element.part_number : '')}</td>
        <td>${element.descripcion}</td>
        <td><input type="number" id="" value="${element.cantidad ? element.cantidad : '1'}" style="width: 80px;"/></td>
        <td>${(element.abreviatura !== null ? element.abreviatura : '')}</td>
        <td><i class="fas fa-times icon-tabla red boton delete" data-toggle="tooltip" data-placement="bottom" 
        title="Eliminar"></i></td>
        </tr>`;
        i++;
    });
    $('#detalleSale tbody').html(html);
}

function ceros_numero(numero){
    if (numero == 'numero'){
        var num = $('[name=numero]').val();
        $('[name=numero]').val(leftZero(7,num));
    }
    else if(numero == 'serie'){
        var num = $('[name=serie]').val();
        $('[name=serie]').val(leftZero(4,num));
    }
}

$('#detalleSale tbody').on("click", ".delete", function(){
    var anula = confirm("¿Esta seguro que desea anular éste item?");
    
    if (anula){
        var idx = $(this).parents("tr")[0].id;
        var index = detalle_sale.findIndex(function(item, i){
            return parseInt(item.id_producto) == parseInt(idx);
        });
        console.log(index);
        if (index !== -1){
            detalle_sale.splice(index,1);
        }
        $(this).parents("tr").remove();
        console.log('idx'+idx);
    }
});
// function eliminarProductoSale(){
//     var idx = $(this).parents("tr")[0].id;
//     $(this).parents("tr").remove();
// }