function open_guia_create(data) {
    $('#modal-guia_ven_create').modal({
        show: true
    });
    $("#submit_guia").removeAttr("disabled");
    $("#mensaje").text('');
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
    $('#almacen_req').text(data.almacen_descripcion);
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
    $('[name=comentario]').val('');
    // $('#serie').text('');
    // $('#numero').text('');
    detalle = [];
    listarDetalleOrdenDespacho(data.id_requerimiento, data.id_od, (data.aplica_cambios ? 'si' : 'no'), (data.tiene_transformacion ? 'si' : 'no'));
    // cargar_almacenes(data.id_sede, 'id_almacen');
    // var tp_doc_almacen = 2;//guia venta
    // next_serie_numero(data.id_sede,tp_doc_almacen);
}

let detalle = [];

function listarDetalleOrdenDespacho(id_requerimiento, id_od, aplica_cambios, tiene_transformacion) {
    detalle = [];
    console.log('verDetalleDespacho/' + id_requerimiento + '/' + id_od + '/' + aplica_cambios + '/' + tiene_transformacion);
    $.ajax({
        type: 'GET',
        url: 'verDetalleDespacho/' + id_requerimiento + '/' + id_od + '/' + aplica_cambios + '/' + tiene_transformacion,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var cantidad_despacho = 0;
            var despacho = 0;
            var items_para_despachar = 0;
            var almacen_diferentes = 0;
            var id_almacen = (response.length > 0 ? response[0].id_almacen_reserva : null);
            var almacen_descripcion = (response.length > 0 ? response[0].almacen_reserva : null);
            var msj = '';

            response.forEach(element => {
                despacho = (element.cantidad - (element.cantidad_despachada ?? 0));

                // var cantidad_despacho = 0;
                if (parseFloat(element.stock_comprometido ?? 0) < despacho) {
                    cantidad_despacho = parseFloat(element.stock_comprometido);
                } else {
                    cantidad_despacho = despacho;
                }

                if (cantidad_despacho > 0 && parseFloat(element.stock_comprometido ?? 0) > 0) {
                    detalle.push({
                        'id_od_detalle': element.id_od_detalle,
                        'id_detalle_requerimiento': element.id_detalle_requerimiento,
                        'id_producto': element.id_producto,
                        'id_unidad_medida': element.id_unidad_medida,
                        'codigo': element.codigo,
                        'part_number': element.part_number,
                        'descripcion': element.descripcion,
                        'cantidad_despachada': element.cantidad_despachada ?? 0,
                        'cantidad_despacho': cantidad_despacho,
                        'cantidad': cantidad_despacho,
                        'abreviatura': element.abreviatura,
                        'control_series': element.control_series,
                        'suma_reservas': element.stock_comprometido ?? 0,
                        'id_almacen_reserva': element.id_almacen_reserva,
                        'almacen_reserva': element.almacen_reserva,
                        'series': []
                    });
                    console.log(element.codigo + ': ' + element.stock_comprometido + '!== ' + element.cantidad);

                    if (parseFloat(element.stock_comprometido) > 0) {
                        items_para_despachar++;
                    }
                    if (parseFloat(element.stock_comprometido) == 0 || element.stock_comprometido == null) {
                        msj = '*Aún no hay saldo de todos los productos. ';
                    }
                    if (element.id_almacen_reserva !== null && (id_almacen == null || id_almacen == '')) {
                        id_almacen = element.id_almacen_reserva;
                        almacen_descripcion = element.almacen_reserva;
                    }
                    if (element.id_almacen_reserva !== null && element.id_almacen_reserva !== id_almacen) {
                        almacen_diferentes++;
                    }
                }
            });
            if (items_para_despachar == 0) {
                $("#submit_guia").attr('disabled', 'true');
                Lobibox.notify("info", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'No hay items para despachar. Para mas información, revise el detalle de items.'
                });
            }
            if (almacen_diferentes > 0) {
                // $("#submit_guia").attr('disabled', 'true');
                msj += 'Los almacenes de reserva son diferentes.';
            } else {
                $('[name=id_almacen]').val(id_almacen);
                $('[name=almacen_descripcion]').val(almacen_descripcion);
            }
            $("#mensaje").text(msj);

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
        <td>${element.suma_reservas > 0 ? `<input type="checkbox" value="${element.id_detalle_requerimiento}" checked/>` : ''}</td>
        <td><a href="#" class="verProducto" data-id="${element.id_producto}" >${element.codigo !== null ? element.codigo : ''}</a></td>
        <td>${element.part_number !== null ? element.part_number : ''}</td>
        <td>${element.descripcion !== null ? element.descripcion : '(producto no mapeado)'}<br><strong>${html_series}</strong></td>
        <td>${element.almacen_reserva ?? ''}</td>
        <td>${element.suma_reservas !== null ? element.suma_reservas : ''}</td>
        <td>${element.cantidad_despachada}</td>
        <td><input class="right cantidad" type="number" value="${element.cantidad}" min="0.01" name="cantidad" style="width:80px;"
        step=".01" max="${element.cantidad}" data-id="${element.id_detalle_requerimiento}"/></td>
        <td>${element.abreviatura !== null ? element.abreviatura : ''}</td>
        <td>
        ${element.control_series ?
                `<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" 
        onClick="open_series(${element.id_producto},${element.id_od_detalle},${element.cantidad},${id_almacen});"></i>` : ''}
        </td>
        </tr>`;
    });
    $('#detalleGuiaVenta tbody').html(html);
}

$("#detalleGuiaVenta tbody").on("change", ".cantidad", function (e) {
    var cantidad = parseFloat($(this).val());
    var id = $(this).data('id');
    var res = detalle.find(element => element.id_detalle_requerimiento == id);
    if (res.cantidad_despacho >= cantidad) {
        res.cantidad = cantidad;
        mostrar_detalle();
    } else {
        $(this).parents("tr").find('input[name=cantidad]').val(res.cantidad_despacho);
        Lobibox.notify("warning", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'No puede ingresar una cantidad mayor a ' + res.cantidad_despacho
        });
    }
});

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
    let prod_sin_series = 0;
    let cantidad_sobregirada = 0;

    $("#detalleGuiaVenta input[type=checkbox]:checked").each(function () {
        var id = $(this).val();

        detalle.forEach(element => {

            if (id == element.id_detalle_requerimiento && element.cantidad > 0) {
                lista_detalle.push({
                    'id_od_detalle': element.id_od_detalle,
                    'id_producto': element.id_producto,
                    'cantidad': element.cantidad,
                    'id_unidad_medida': element.id_unidad_medida,
                    'id_detalle_requerimiento': element.id_detalle_requerimiento,
                    'id_guia_com_det': element.id_guia_com_det,
                    'id_almacen_reserva': element.id_almacen_reserva,
                    'series': element.series
                });

                if (element.cantidad > element.cantidad_despacho) {
                    cantidad_sobregirada++;
                }
                if (element.control_series && element.series.length == 0) {
                    prod_sin_series++;
                }
            }
        });
    });
    console.log(lista_detalle);
    var id_almacen = (lista_detalle.length > 0 ? lista_detalle[0].id_almacen_reserva : null);
    var almacen_diferentes = 0;

    lista_detalle.forEach(element => {
        if (element.id_almacen_reserva !== id_almacen) {
            almacen_diferentes++;
        }
    });

    var valida = 0;

    if (almacen_diferentes > 0) {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'No es posible realizar una salida seleccionando stock de varios almacenes.'
        });
        valida++;
    }
    if (lista_detalle.length == 0) {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'No es posible realizar una salida sin items.'
        });
        valida++;
    }
    if (prod_sin_series > 0) {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Falta agregar series a ' + prod_sin_series + ' productos.'
        });
        valida++;
    }
    if (cantidad_sobregirada > 0) {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Ingreso una cantidad mayor a la que puede despachar en ' + cantidad_sobregirada + ' productos.'
        });
        valida++;
    }
    if (valida == 0) {
        $('[name=id_almacen]').val(id_almacen);
        var ser = $(this).serialize();
        var data = ser + '&detalle=' + JSON.stringify(lista_detalle);
        console.log(data);
        guardarGuiaVenta(data);
    }
});

function guardarGuiaVenta(data) {
    $("#submit_guia").attr('disabled', 'true');

    $.ajax({
        type: 'POST',
        url: 'guardarSalidaGuiaDespacho',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });
            $('#mensaje').text('*' + response.mensaje);

            if (response.tipo == 'success') {
                $('#modal-guia_ven_create').modal('hide');
                $('#despachosPendientes').DataTable().ajax.reload(null, false);
                $('#nro_despachos').text(response.nroDespachosPendientes);
            }
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
