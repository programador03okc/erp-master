function open_guia_create(data) {
    $('#modal-guia_ven_create').modal({
        show: true
    });
    $("#submit_guia").removeAttr("disabled");
    if (data.aplica_cambios) {
        $('[name=id_operacion]').val(27).trigger('change.select2');
        $('#name_title').text('Despacho Interno');
        $('#name_title').removeClass();
        $('#name_title').addClass('red');
    } else {
        $('[name=id_operacion]').val(1).trigger('change.select2');
        $('#name_title').text('Despacho Externo');
        $('#name_title').removeClass();
        $('#name_title').addClass('blue');
    }
    console.log(data);
    $('#codigo_req').text(data.codigo_req);
    $('[name=id_guia_clas]').val(1);
    $('[name=id_od]').val(data.id_od);
    $('[name=id_almacen]').val(data.id_almacen);
    $('[name=id_sede]').val(data.id_sede);
    $('[name=id_cliente]').val(data.id_cliente);
    $('[name=id_persona]').val(data.id_persona);
    $('[name=razon_social_cliente]').val(data.razon_social);
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $('[name=almacen_descripcion]').val(data.almacen_descripcion);
    $('[name=serie]').val('');
    $('[name=numero]').val('');
    // $('#serie').text('');
    // $('#numero').text('');
    detalle = [];
    listarDetalleOrdenDespacho(data.id_od, data.tiene_transformacion);
    // cargar_almacenes(data.id_sede, 'id_almacen');
    // var tp_doc_almacen = 2;//guia venta
    // next_serie_numero(data.id_sede,tp_doc_almacen);
}

let detalle = [];

function listarDetalleOrdenDespacho(id_od, tiene_transformacion) {
    detalle = [];
    console.log('verDetalleDespacho/' + id_od + '/' + tiene_transformacion);
    $.ajax({
        type: 'GET',
        url: 'verDetalleDespacho/' + id_od + '/' + tiene_transformacion,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            response.forEach(element => {
                detalle.push({
                    'id_od_detalle': element.id_od_detalle,
                    'id_detalle_requerimiento': element.id_detalle_requerimiento,
                    'id_producto': element.id_producto,
                    'id_unidad_medida': element.id_unidad_medida,
                    'codigo': element.codigo,
                    'part_number': element.part_number,
                    'descripcion': element.descripcion,
                    'cantidad': element.cantidad,
                    'abreviatura': element.abreviatura,
                    'control_series': element.control_series,
                    'series': []
                })
            });
            // detalle = response;
            mostrar_detalle();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_detalle() {
    var html = '';
    var html_series = '';
    var i = 1;
    var id_almacen = parseInt($('[name=id_almacen]').val());

    detalle.forEach(element => {
        html_series = '';
        element.series.forEach(ser => {
            if (ser.estado == 1) {
                if (html_series == '') {
                    html_series += ser.serie;
                } else {
                    html_series += ', ' + ser.serie;
                }
            }
        });
        html += `<tr>
        <td>${i}</td>
        <td><a href="#" class="verProducto" data-id="${element.id_producto}" >${element.codigo}</a></td>
        <td>${element.part_number !== null ? element.part_number : ''}</td>
        <td>${element.descripcion}<br><strong>${html_series}</strong></td>
        <td>${element.cantidad}</td>
        <td>${element.abreviatura}</td>
        <td>
        ${element.control_series ? `<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" 
        onClick="open_series(${element.id_producto},${element.id_od_detalle},${element.cantidad},${id_almacen});"></i>` : ''}
        </td>
        </tr>`;
        i++;
    });
    $('#detalleGuiaVenta tbody').html(html);
}

$("#detalleGuiaVenta tbody").on("click", "a.verProducto", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    abrirProducto(id);
});

function abrirProducto(id_producto) {
    console.log('abrirProducto' + id_producto);
    localStorage.setItem("id_producto", id_producto);
    var win = window.open("/almacen/catalogos/productos/index", '_blank');
    win.focus();
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

$("#form-guia_ven_create").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();
    var lista_detalle = [];
    detalle.forEach(element => {
        lista_detalle.push({
            'id_od_detalle': element.id_od_detalle,
            'id_producto': element.id_producto,
            'cantidad': element.cantidad,
            'id_unidad_medida': element.id_unidad_medida,
            'id_detalle_requerimiento': element.id_detalle_requerimiento,
            'id_guia_com_det': element.id_guia_com_det,
            // 'codigo'                    : element.codigo,
            // 'part_number'               : element.part_number,
            // 'descripcion'               : element.descripcion,
            // 'abreviatura'               : element.abreviatura,
            // 'descripcion'               : encodeURIComponent(element.descripcion),
            'series': element.series
        });
    });
    var ser = $(this).serialize();
    var data = ser + '&detalle=' + JSON.stringify(lista_detalle);
    console.log(data);
    guardar_guia_create(data);
});

function guardar_guia_create(data) {
    $("#submit_guia").attr('disabled', 'true');

    $.ajax({
        type: 'POST',
        url: 'guardar_guia_despacho',
        data: data,
        dataType: 'JSON',
        success: function (id_salida) {
            console.log(id_salida);
            Lobibox.notify("success", {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: 'Salida de Almacén generada con éxito.'
            });
            $('#modal-guia_ven_create').modal('hide');
            $('#despachosPendientes').DataTable().ajax.reload();
            // var id = encode5t(id_salida);
            // window.open('imprimir_salida/'+id);                
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function ceros_numero_ven(numero) {
    if (numero == 'numero') {
        var num = $('[name=numero]').val();
        $('[name=numero]').val(leftZero(7, num));
    }
    else if (numero == 'serie') {
        var num = $('[name=serie]').val();
        $('[name=serie]').val(leftZero(4, num));
    }
}
