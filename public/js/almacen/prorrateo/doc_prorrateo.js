
let documentos = [];
let guias_detalle = [];

function nuevo_prorrateo() {
    $('#listaProrrateos tbody').html('');
    $('#listaGuiaDetalleProrrateo tbody').html('');
    documentos = [];
    guias_detalle = [];

    $('[name=id_prorrateo]').val('');
    $('#codigo').text('');
    $('#estado_doc').text('');

    $('[name=total_ingreso]').val('');
    $('#total_valor_compra').text('');
    $('#moneda').text('');
    $('#soles').text('');
    $('#total_valor').text('');
    $('#total_peso').text('');
    $('#total_adicional_valor').text('');
    $('#total_adicional_peso').text('');
    $('#total_costo').text('');

    $('[name=total_comp_valor]').val('');
    $('[name=total_comp_peso]').val('');

}

function mostrar_prorrateo(id_prorrateo) {

    $('#listaProrrateos tbody').html('');
    $('#listaGuiaDetalleProrrateo tbody').html('');

    documentos = [];
    guias_detalle = [];

    $.ajax({
        type: 'GET',
        url: 'mostrar_prorrateo/' + id_prorrateo,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            $('#codigo').text(response['prorrateo'].codigo);
            $('#estado_doc').text((response['prorrateo'].estado == 1 ? "Activo" : "Inactivo"));

            response['documentos'].forEach(element => {

                documentos.push({
                    'id_prorrateo_doc': element.id_prorrateo_doc,
                    'id_doc_com': element.id_doc_com,
                    'id_tp_prorrateo': element.id_tp_doc_prorrateo,
                    'id_tipo_prorrateo': element.id_tipo_prorrateo,
                    'tipo_prorrateo': element.tipo_prorrateo,
                    'tp_prorrateo': element.descripcion,
                    'id_proveedor': element.id_proveedor,
                    'razon_social': element.razon_social,
                    'fecha_emision': element.fecha_emision,
                    'id_tp_documento': element.id_tp_doc,
                    'serie': element.serie,
                    'numero': element.numero,
                    'id_moneda': element.moneda,
                    'total': element.total_a_pagar,
                    'tipo_cambio': element.tipo_cambio,
                    // 'importe'           :(element.total_a_pagar * element.tipo_cambio),
                    'importe': element.importe_soles,
                    'importe_aplicado': element.importe_aplicado,
                    'estado': element.estado,
                });
            });

            mostrar_documentos();

            response['detalles'].forEach(element => {
                var unitario = parseFloat(element.precio_unitario !== null
                    ? element.precio_unitario
                    : element.unitario);

                guias_detalle.push({
                    'id_prorrateo_det': element.id_prorrateo_det,
                    'id_guia_com_det': element.id_guia_com_det,
                    'id_mov_alm_det': element.id_mov_alm_det,
                    'serie': element.serie,
                    'numero': element.numero,
                    'codigo': element.codigo,
                    'part_number': element.part_number,
                    'descripcion': element.descripcion,
                    'simbolo': element.simbolo,
                    'cantidad': element.cantidad,
                    'abreviatura': element.abreviatura,
                    'fecha_emision': element.fecha_emision,
                    'tipo_cambio': element.tipo_cambio,
                    'valor_compra': (unitario * parseFloat(element.cantidad)),
                    'valor_compra_soles': (element.moneda !== 1
                        ? (unitario * parseFloat(element.cantidad) * parseFloat(element.tipo_cambio))
                        : (unitario * parseFloat(element.cantidad))),
                    'adicional_valor': element.adicional_valor,
                    'adicional_peso': element.adicional_peso,
                    'peso': element.peso,
                    'total': (parseFloat(element.valor_compra_soles) + parseFloat(element.adicional_valor) + parseFloat(element.adicional_peso)),
                    'estado': element.estado,
                });
            });

            mostrar_guias_detalle();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_prorrateo(data, action) {

    let baseUrl = '';

    if (action == 'register') {
        baseUrl = 'guardarProrrateo';
    }
    else if (action == 'edition') {
        baseUrl = 'updateProrrateo';
    }
    var id = $('[name=id_prorrateo]').val();
    console.log(baseUrl);

    data = 'id_prorrateo=' + id +
        '&documentos=' + JSON.stringify(documentos) +
        '&guias_detalle=' + JSON.stringify(guias_detalle);
    console.log(data);
    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: {
            id_prorrateo: id,
            documentos: documentos,
            detalleGuias: guias_detalle
        },
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (baseUrl == 'guardarProrrateo') {
                Lobibox.notify(response.tipo, {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: response.mensaje
                });
            } else {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Se actualizó correctamente el prorrateo.'
                });
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_doc_prorrateo(id) {
    $.ajax({
        type: 'GET',
        url: 'anular_prorrateo/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response) {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Se anuló correctamente el prorrateo.'
                });
            } else {
                Lobibox.notify("error", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Ha ocurrido un problema. Inténtelo de nuevo.'
                });
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}