function open_guia_create(data){
    console.log(data);
    $('#modal-guia_create').modal({
        show: true
    });
    $("#submit_guia").removeAttr("disabled");
    $('[name=id_operacion]').val(2).trigger('change.select2');
    $('[name=id_guia_clas]').val(1);
    $('[name=id_proveedor]').val(data.id_proveedor);
    $('[name=id_sede]').val(data.id_sede);
    $('[name=id_orden_compra]').val(data.id_orden_compra);
    $('[name=id_transformacion]').val('');
    $('#serie').text('');
    $('#numero').text('');
    cargar_almacenes(data.id_sede, 'id_almacen');
    var data = 'oc_seleccionadas='+JSON.stringify([data.id_orden_compra]);
    listar_detalle_ordenes_seleccionadas(data);
}

function open_guia_create_seleccionadas(){
    var id_prov = null;
    var sede = null;
    var dif_prov = 0;
    var dif_sede = 0;
    var id_oc_seleccionadas = [];

    oc_seleccionadas.forEach(element => {
        id_oc_seleccionadas.push(element.id_orden_compra);

        if (id_prov == null){
            id_prov = element.id_proveedor;
        } 
        else if (element.id_proveedor !== id_prov){
            dif_prov++;
        }
        if (sede == null){
            sede = element.id_sede;
        } 
        else if (element.id_sede !== sede){
            dif_sede++;
        }
    });

    var text = '';
    if (dif_prov > 0) text+='Debe seleccionar OCs del mismo proveedor\n';
    if (dif_sede > 0) text+='Debe seleccionar OCs de la misma sede';

    if ((dif_sede + dif_prov) > 0){
        alert(text);
    } else {
        $('#modal-guia_create').modal({
            show: true
        });
        $("#submit_guia").removeAttr("disabled");
        $('[name=id_operacion]').val(2).trigger('change.select2');
        $('[name=id_guia_clas]').val(1);
        $('[name=id_proveedor]').val(id_prov);
        $('[name=id_sede]').val(sede);
        $('[name=id_transformacion]').val('');
        $('#serie').text('');
        $('#numero').text('');
        cargar_almacenes(sede, 'id_almacen');
        var data = 'oc_seleccionadas='+JSON.stringify(id_oc_seleccionadas);
        listar_detalle_ordenes_seleccionadas(data);
    }
}

function open_transformacion_guia_create(data){
    console.log(data);
    $('#modal-guia_create').modal({
        show: true
    });
    $("#submit_guia").removeAttr("disabled");
    $('[name=id_operacion]').val(26).trigger('change.select2');
    $('[name=id_guia_clas]').val(1);
    $('[name=id_proveedor]').val(data.id_proveedor);
    $('[name=id_sede]').val(data.id_sede);
    $('[name=id_transformacion]').val(data.id_transformacion);
    $('[name=id_orden_compra]').val('');
    $('[name=serie]').val(data.serie);
    $('[name=numero]').val(data.numero);
    cargar_almacenes(data.id_sede, 'id_almacen');
    // var data = 'oc_seleccionadas='+JSON.stringify([data.id_orden_compra]);
    listar_detalle_transformacion(data.id_transformacion);
}

let series_transformacion = [];

function listar_detalle_transformacion(id){
    oc_det_seleccionadas = [];
    series_transformacion = [];
    $.ajax({
        type: 'GET',
        url: 'listarDetalleTransformacion/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var i = 1;
            response['sobrantes'].forEach(function(element){
                html+=`<tr id="${element.id_sobrante}" >
                <td>${i}</td>
                <td></td>
                <td><input style="display:none" id="producto" data-tipo="sobrante" value="${element.id_producto}"/>${element.cod_prod}</td>
                <td>${element.part_number}</td>
                <td>${element.descripcion}</td>
                <td>${element.cantidad}</td>
                <td>${element.abreviatura}</td>
                <td>${formatNumber.decimal(element.valor_unitario,'',2)}</td>
                <td>${formatNumber.decimal(element.valor_total,'',2)}</td>
                ${( element.series
                    ? '<td><input type="text" class="oculto" id="series" value="'+element.series+'" data-partnumber="'+element.part_number+'"/><i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" onClick="agrega_series_transformacion('+"'s"+element.id_sobrante+"'"+');"></i></td>' 
                    : '<td></td>')}
                </tr>`;
                i++;
                series_transformacion.push({
                    'id'        : 's'+element.id_sobrante,
                    'series'    : [],
                    'cantidad'  : element.cantidad
                });
            });
            response['transformados'].forEach(function(element){
                html+=`<tr id="${element.id_transformado}" >
                <td>${i}</td>
                <td></td>
                <td><input style="display:none" id="producto" data-tipo="transformado" value="${element.id_producto}"/>${element.cod_prod}</td>
                <td>${element.part_number}</td>
                <td>${element.descripcion}</td>
                <td>${element.cantidad}</td>
                <td>${element.abreviatura}</td>
                <td>${formatNumber.decimal(element.valor_unitario,'',2)}</td>
                <td>${formatNumber.decimal(element.valor_total,'',2)}</td>
                ${( element.series
                    ? '<td><input type="text" class="oculto" id="series" value="'+element.series+'" data-partnumber="'+element.part_number+'"/><i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" onClick="agrega_series_transformacion('+"'t"+element.id_transformado+"'"+');"></i></td>' 
                    : '<td></td>')}
                </tr>`;
                i++;
                series_transformacion.push({
                    'id'        : 't'+element.id_transformado,
                    'series'    : [],
                    'cantidad'  : element.cantidad
                });
            });
            $('#detalleOrdenSeleccionadas tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_detalle_ordenes_seleccionadas(data){
    console.log(oc_seleccionadas);
    console.log(data);
    oc_det_seleccionadas = [];
    $.ajax({
        type: 'POST',
        url: 'detalleOrdenesSeleccionadas',
        data: data,
        dataType: 'JSON',
        success: function(response){
            $('#detalleOrdenSeleccionadas tbody').html(response['html']);
            oc_det_seleccionadas = response['ids_detalle'];
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#form-guia_create").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    var detalle = [];
    var validaCampos = '';
    var ope = $('[name=id_operacion]').val();

    if (ope == 26){
        $("#detalleOrdenSeleccionadas tbody tr").each(function(){
            var id = $(this)[0].id;
            var id_producto = $(this).find('td input[id=producto]').val();
            var tipo = $(this).find('td input[id=producto]').data('tipo');
            var abr = (tipo == 'sobrante' ? 's' : 't');

            var json = series_transformacion.find(element => element.id == abr+id);
            console.log(json.series);
            var series = (json !== null ? json.series : []);
            var requiereSeries = $(this).find('td input[id=series]').val();
            var part_number = $(this).find('td input[id=series]').data('partnumber');
            
            if (requiereSeries == 'true' && series.length == 0){
                validaCampos += 'El producto con Part Number '+part_number+' requiere que ingrese Series.\n'; 
            }
            
            var cant = $(this)[0].childNodes[11].innerHTML;
            var unit = $(this)[0].childNodes[15].innerHTML;
            detalle.push({ 
                'id'  : id,
                'tipo'  : tipo,
                'id_producto'  : id_producto,
                'cantidad'     : cant,
                'unitario'     : unit,
                'series'       : series
            });
        });
    } else {
        $("#detalleOrdenSeleccionadas input[type=checkbox]:checked").each(function(){
            var id_oc_det = $(this).val();
            var json = oc_det_seleccionadas.find(element => element.id_oc_det == id_oc_det);
            var series = (json !== null ? json.series : []);
            var requiereSeries = $(this).parent().parent().find('td input[id=series]').val();
            var part_number = $(this).parent().parent().find('td input[id=series]').data('partnumber');
            
            if (requiereSeries == '1' && series.length == 0){
                validaCampos += 'El producto con Part Number '+part_number+' requiere que ingrese Series.\n'; 
            }
    
            detalle.push({ 
                'id_detalle_orden'  : id_oc_det,
                'cantidad'          : $(this).parent().parent().find('td input[id='+id_oc_det+'cantidad]').val(),
                'series'            : series
            });
        });

    }    
    if (validaCampos.length > 0){
        alert(validaCampos);
    } else {
        data+='&detalle='+JSON.stringify(detalle);
        console.log(data);
        guardar_guia_create(data);
    }
});

function guardar_guia_create(data){
    $("#submit_guia").attr('disabled','true');
    $.ajax({
        type: 'POST',
        url: 'guardar_guia_com_oc',
        data: data,
        dataType: 'JSON',
        success: function(id_ingreso){
            console.log(id_ingreso);
            if (id_ingreso > 0){
                alert('Ingreso Almacén generado con éxito');
                $('#modal-guia_create').modal('hide');
                
                var tra = $('[name=id_transformacion]').val();
                if (tra!==''){
                    listarTransformaciones();
                } else {
                    listarOrdenesPendientes();
                }
                // var id = encode5t(id_ingreso);
                // window.open('imprimir_ingreso/'+id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
