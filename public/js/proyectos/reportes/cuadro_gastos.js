$(function () {
    vista_extendida();
});

function exportarCuadroCostos() {
    var id_presup = $('[name=id_presup]').val();
    var form = $(`<form action="cuadroGastosExcel" method="post" target="_blank">
        <input type="hidden" name="_token" value="${csrf_token}"/>
        <input type="hidden" name="id_presupuesto" value="${id_presup}"/>
        </form>`);
    $('body').append(form);
    form.trigger('submit');
}

$("[name=id_presup]").on('change', function () {
    // var id = $('[name=id_presup]').val();
    var id = $(this).val();
    if (id !== '') {
        $.ajax({
            type: 'GET',
            url: "mostrarGastosPorPresupuesto/" + id,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                var html = '';
                var total_sin_igv = 0;
                var total = 0;

                response.req_compras.forEach(element => {
                    var unitario = (element.precio !== null ? element.precio : element.precio_requerimiento);
                    var sub_total = parseFloat(unitario) * parseFloat(element.cantidad);
                    var igv = sub_total * 0.18;
                    total_sin_igv += parseFloat(sub_total);
                    total += (sub_total + igv);
                    html += `<tr>
                            <td>${element.fecha_requerimiento}</td>
                            <td>${element.codigo}</td>
                            <td>${element.titulo_descripcion}</td>
                            <td>${element.partida_descripcion}</td>
                            <td>${element.tipo_comprobante ?? ''}</td>
                            <td>${element.serie_numero ?? ''}</td>
                            <td>${element.fecha_emision_comprobante ?? ''}</td>
                            <td>${element.proveedor_razon_social ?? ''}</td>
                            <td>${element.cantidad}</td>
                            <td>${element.abreviatura}</td>
                            <td>${element.descripcion}</td>
                            <td>${element.simbolo ?? ''}</td>
                            <td style="text-align:right">${formatNumber.decimal(unitario, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(sub_total, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(igv, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal((sub_total + igv), '', -2)}</td>
                            <td>${element.estado_pago ?? ''}</td>
                            </tr>`;
                    // <td width="50px" style="text-align:right;">${formatter.format(sub_total)}</td>
                });

                response.req_pagos.forEach(element => {
                    var sub_total = parseFloat(element.precio_unitario) * parseFloat(element.cantidad);
                    var igv = sub_total * 0.18;
                    total_sin_igv += parseFloat(sub_total);
                    total += (sub_total + igv);
                    html += `<tr>
                            <td>${formatDate(element.fecha_registro)}</td>
                            <td>${element.codigo ?? ''}</td>
                            <td>${element.titulo_descripcion ?? ''}</td>
                            <td>${element.partida_descripcion ?? ''}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${element.apellido_paterno !== null ? (element.apellido_paterno + ' ' + element.apellido_materno + ' ' + element.nombres) : ''}</td>
                            <td>${element.cantidad ?? ''}</td>
                            <td>${element.abreviatura ?? ''}</td>
                            <td>${element.descripcion ?? ''}</td>
                            <td>${element.simbolo ?? ''}</td>
                            <td style="text-align:right">${formatNumber.decimal(element.precio_unitario ?? '', '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(sub_total ?? '', '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(0, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(sub_total, '', -2)}</td>
                            <td>${element.estado_pago ?? ''}</td>
                            </tr>`;
                });

                html += `<tr>
                        <td style="font-size: 14px; text-align:right;" colSpan="12"><strong>Total Consumido</strong></td>
                        <td style="font-size: 14px; text-align:right;"></td>
                        <td style="font-size: 14px; text-align:right;"><strong>${formatNumber.decimal(total_sin_igv, '', -2)}</strong></td>
                        <td style="font-size: 14px; text-align:right;"></td>
                        <td style="font-size: 14px; text-align:right;"><strong>${formatNumber.decimal(total, '', -2)}</strong></td>
                    </tr>`;

                $('#listaEstructura tbody').html(html);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
});