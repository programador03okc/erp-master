
function listar_guia_detalle(id_guia) {
    console.log('id_guia' + id_guia);

    $.ajax({
        type: 'GET',
        url: 'listar_guia_detalle/' + id_guia,
        dataType: 'JSON',
        success: function (response) {
            console.log(response.length);
            console.log(response);

            if (response.length > 0) {
                let id = null;
                var unitario = 0;
                var valor_compra = 0;
                var fecha_emision = '';
                var precio_unitario = '';
                var moneda = '';
                var simbolo = '';
                var tipo_cambio = 0;

                response.forEach(element => {
                    id = guias_detalle.find(guia => guia.id_guia_com_det == element.id_guia_com_det);

                    if (id == undefined || id == null) {

                        fecha_emision = element.fecha_emision !== null ? element.fecha_emision : element.fecha_orden;
                        precio_unitario = element.precio_unitario !== null ? element.precio_unitario : element.unitario_orden;
                        moneda = element.fecha_emision !== null ? element.moneda : element.moneda_orden;
                        simbolo = element.fecha_emision !== null ? element.simbolo : element.simbolo_orden;
                        tipo_cambio = element.fecha_emision !== null ? element.tipo_cambio_doc : element.tipo_cambio_orden;

                        unitario = parseFloat(precio_unitario);
                        valor_compra = ((unitario * parseFloat(element.cantidad)) + parseFloat(element.unitario_adicional));

                        guias_detalle.push({
                            'id_prorrateo_det': 0,
                            'id_guia_com_det': element.id_guia_com_det,
                            'id_mov_alm_det': element.id_mov_alm_det,
                            'serie': element.serie,
                            'numero': element.numero,
                            'codigo': element.codigo,
                            'part_number': element.part_number,
                            'descripcion': element.descripcion,
                            'simbolo': simbolo,
                            'cantidad': element.cantidad,
                            'abreviatura': element.abreviatura,
                            'fecha_emision': fecha_emision,
                            'tipo_cambio': tipo_cambio,
                            'valor_compra': valor_compra,
                            'valor_compra_soles': (moneda !== 1
                                ? (valor_compra * parseFloat(tipo_cambio))
                                : (valor_compra)),
                            'adicional_valor': 0,
                            'adicional_peso': 0,
                            'total': (parseFloat(precio_unitario) * parseFloat(element.cantidad)),
                            'peso': 0,
                            'estado': 1,
                        });
                    }
                });
                mostrar_guias_detalle();
            } else {
                Lobibox.notify("error", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'El ingreso aún no tiene una factura relacionada!'
                });
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_guias_detalle() {

    $('#listaDetalleProrrateo tbody').html('');

    var html = '';
    let importe_valor = $('[name=total_comp_valor]').val();
    let importe_peso = $('[name=total_comp_peso]').val();
    console.log('importe_valor' + importe_valor);
    console.log('importe_peso' + importe_peso);

    let suma_total = 0;
    let suma_peso = 0;

    guias_detalle.forEach(element => {
        if (element.estado !== 7) {
            suma_total += parseFloat(element.valor_compra_soles);
            suma_peso += parseFloat(element.peso);
        }
    });
    let factor_valor = parseFloat(importe_valor !== '' ? importe_valor : 0) / (suma_total > 0 ? suma_total : 1);
    let factor_peso = parseFloat(importe_peso !== '' ? importe_peso : 0) / (suma_peso > 0 ? suma_peso : 1);

    let adicional_valor = 0;
    let adicional_peso = 0;
    let total = 0;

    let total_valor_compra = 0;
    let total_valor = 0;
    let total_peso = 0;
    let total_adicional_valor = 0;
    let total_adicional_peso = 0;
    let total_prorrateado = 0;
    let moneda = '';

    // let edition = ($("#form-prorrateo").attr('type') == 'edition' ? true : false);
    console.log('factor_peso: ' + factor_peso);
    console.log('factor_peso: ' + factor_valor);

    guias_detalle.forEach(element => {

        if (element.estado !== 7) {

            adicional_valor = parseFloat(element.valor_compra_soles) * parseFloat(factor_valor);
            adicional_peso = parseFloat(element.peso) * parseFloat(factor_peso);

            total = parseFloat(element.valor_compra_soles) + parseFloat(adicional_valor) + parseFloat(adicional_peso);

            element.adicional_valor = adicional_valor;
            element.adicional_peso = adicional_peso;
            element.total = total;

            total_valor_compra += parseFloat(element.valor_compra);
            total_valor += parseFloat(element.valor_compra_soles);
            total_peso += parseFloat(element.peso);
            total_adicional_valor += parseFloat(element.adicional_valor);
            total_adicional_peso += parseFloat(element.adicional_peso);
            total_prorrateado += parseFloat(element.total);
            console.log(element);

            moneda = ((element.simbolo !== null && element.simbolo !== undefined) ? element.simbolo : '');

            html += `
            <tr id="${element.id_guia_com_det}">
                <td>${element.serie + '-' + element.numero}</td>
                <td>${element.fecha_emision}</td>
                <td>${element.codigo}</td>
                <td>${element.part_number !== null ? element.part_number : ''}</td>
                <td>${element.descripcion}</td>
                <td>${element.cantidad}</td>
                <td>${element.abreviatura}</td>
                <td style="text-align: right">${moneda}</td>
                <td style="width: 110px;text-align: right">${formatDecimalDigitos(element.valor_compra, 3)}</td>
                <td style="width: 110px;text-align: right">${element.tipo_cambio}</td>
                <td style="width: 110px;text-align: right">${formatDecimalDigitos(element.valor_compra_soles, 3)}</td>
                <td style="width: 110px;text-align: right"><input type="number" class="form-control peso" style="width:70px;"
                    data-id="${element.id_guia_com_det}" value="${element.peso}"/></td>
                <td style="width: 110px;text-align: right">${formatDecimalDigitos(element.adicional_valor, 3)}</td>
                <td style="width: 110px;text-align: right">${formatDecimalDigitos(element.adicional_peso, 3)}</td>
                <td style="width: 110px;text-align: right">${formatDecimalDigitos(element.total, 3)}</td>
                <td style="display:flex;">
                    <button type="button" class="anular btn btn-danger btn-xs activation" data-toggle="tooltip" 
                        data-placement="bottom" title="Eliminar" onClick="anular_item('${element.id_guia_com_det}');"
                        >  <i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
        }
    });

    $('#listaGuiaDetalleProrrateo tbody').html(html);

    $('[name=total_ingreso]').val(formatDecimalDigitos(suma_total, 3));
    $('#moneda').text(moneda);
    $('#soles').text('S/');
    $('#total_valor_compra').text(formatDecimalDigitos(total_valor_compra, 3));
    $('#total_valor').text(formatDecimalDigitos(total_valor, 3));
    $('#total_peso').text(formatDecimalDigitos(total_peso, 3));
    $('#total_adicional_valor').text(formatDecimalDigitos(total_adicional_valor, 3));
    $('#total_adicional_peso').text(formatDecimalDigitos(total_adicional_peso, 3));
    $('#total_costo').text(formatDecimalDigitos(total_prorrateado, 3));

}

$('#listaGuiaDetalleProrrateo tbody').on("change", ".peso", function () {

    let id_guia_com_det = $(this).data('id');
    let peso = parseFloat($(this).val());
    console.log('peso: ' + peso);

    guias_detalle.forEach(element => {
        if (element.id_guia_com_det == id_guia_com_det) {
            element.peso = peso;
            console.log(element);
        }
    });
    console.log(guias_detalle);
    mostrar_guias_detalle();
});

function anular_item(id_guia_com_det) {
    let elimina = confirm("¿Esta seguro que desea eliminar éste item?");

    if (elimina) {

        let guia = guias_detalle.find(det => det.id_guia_com_det == id_guia_com_det);

        if (guia.id_prorrateo_det == 0) {
            var index = guias_detalle.findIndex(function (item, i) {
                return item.id_guia_com_det == id_guia_com_det;
            });
            guias_detalle.splice(index, 1);
        }
        else {
            guia.estado = 7;
        }
        console.log(guias_detalle);
        mostrar_guias_detalle();
    }
}
