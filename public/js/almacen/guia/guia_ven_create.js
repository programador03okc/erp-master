function open_guia_create(data){
    $('#modal-guia_ven_create').modal({
        show: true
    });
    $("#submit_guia").removeAttr("disabled");
    if (data.aplica_cambios){
        $('[name=id_operacion]').val(27).trigger('change.select2');
    } else {
        $('[name=id_operacion]').val(1).trigger('change.select2');
    }
    $('[name=id_guia_clas]').val(1);
    $('[name=id_od]').val(data.id_od);
    $('[name=id_almacen]').val(data.id_almacen);
    $('[name=id_sede]').val(data.id_sede);
    $('[name=id_cliente]').val(data.id_cliente);
    $('[name=id_persona]').val(data.id_persona);
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $('[name=almacen_descripcion]').val(data.almacen_descripcion);
    $('#serie').text('');
    $('#numero').text('');
    detalle = [];
    listarDetalleOrdenDespacho(data.id_od);
    // cargar_almacenes(data.id_sede, 'id_almacen');
    // var tp_doc_almacen = 2;//guia venta
    // next_serie_numero(data.id_sede,tp_doc_almacen);
}

let detalle = [];

function listarDetalleOrdenDespacho(id_od){
    $.ajax({
        type: 'GET',
        url: 'verDetalleDespacho/'+id_od,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var html = '';
            var html_series = '';
            var i = 1;
            // detalle = response;

            response.forEach(element => {
                html_series = '';
                detalle.push({ 
                    'id_od_detalle'             : element.id_od_detalle,
                    'id_producto'               : element.id_producto,
                    'cantidad'                  : element.cantidad,
                    'id_unidad_medida'          : element.id_unidad_medida,
                    'id_detalle_requerimiento'  : element.id_detalle_requerimiento,
                    // 'codigo'                    : element.codigo,
                    // 'part_number'               : encodeURIComponent(element.part_number),
                    // 'descripcion'               : encodeURIComponent(element.descripcion),
                    // 'abreviatura'               : element.abreviatura,
                    'series'                    : element.series
                });
                element.series.forEach(ser => {
                    if (html_series==''){
                        html_series+=ser.serie;
                    } else {
                        html_series+='<br>'+ser.serie;
                    }
                });
                html += `<tr>
                <td>${i}</td>
                <td>${element.codigo}</td>
                <td>${element.part_number!==null ? element.part_number:''}</td>
                <td>${element.descripcion}</td>
                <td>${element.cantidad}</td>
                <td>${element.abreviatura}</td>
                <td><strong>${html_series}</strong></td>
                </tr>`;
            });
            $('#detalleGuiaVenta tbody').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function next_serie_numero(id_sede,id_tp_doc){
//     if (id_sede !== null && id_tp_doc !== null){
//         $.ajax({
//             type: 'GET',
//             url: 'next_serie_numero_guia/'+id_sede+'/'+id_tp_doc,
//             dataType: 'JSON',
//             success: function(response){
//                 console.log(response);
//                 if (response !== ''){
//                     $('[name=serie]').val(response.serie);
//                     $('[name=numero]').val(response.numero);
//                     $('[name=id_serie_numero]').val(response.id_serie_numero);
//                 } else {
//                     $('[name=serie]').val('');
//                     $('[name=numero]').val('');
//                     $('[name=id_serie_numero]').val('');
//                 }
//             }
//         }).fail( function( jqXHR, textStatus, errorThrown ){
//             console.log(jqXHR);
//             console.log(textStatus);
//             console.log(errorThrown);
//         });
//     }
// }

$("#form-guia_ven_create").on("submit", function(e){
    console.log('submit');
    e.preventDefault();
    var ser = $(this).serialize();
    var data = ser+'&detalle='+JSON.stringify(detalle);
    console.log(data);
    guardar_guia_create(data);
});

function guardar_guia_create(data){
    $("#submit_guia").attr('disabled','true');

    $.ajax({
        type: 'POST',
        url: 'guardar_guia_despacho',
        data: data,
        dataType: 'JSON',
        success: function(id_salida){
            console.log(id_salida);

                alert('Salida de Almacén generada con éxito');
                $('#modal-guia_ven_create').modal('hide');
                $('#despachosPendientes').DataTable().ajax.reload();
                // var id = encode5t(id_salida);
                // window.open('imprimir_salida/'+id);                
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function ceros_numero_ven(numero){
    if (numero == 'numero'){
        var num = $('[name=numero]').val();
        $('[name=numero]').val(leftZero(7,num));
    }
    else if(numero == 'serie'){
        var num = $('[name=serie]').val();
        $('[name=serie]').val(leftZero(4,num));
    }
}
