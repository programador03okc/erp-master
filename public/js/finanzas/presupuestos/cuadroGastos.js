
function mostrarCuadroGastos(id) {
    // var id_presupuesto = $('[name=id_presup]').val();

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
                    var sub_total = parseFloat(element.precio) * parseFloat(element.cantidad);
                    var igv = sub_total * 0.18;
                    total_sin_igv += parseFloat(sub_total);
                    total += (sub_total + igv);
                    html += `<tr>
                            <td>${element.razon_social ?? ''}</td>
                            <td>${element.fecha_pago !== null ? formatDate(element.fecha_pago) : ''}</td>
                            <td>${element.codigo}</td>
                            <td>${element.titulo_descripcion}</td>
                            <td>${element.partida_descripcion}</td>
                            <td>${element.descripcion_adicional}</td>
                            <td>${element.cantidad}</td>
                            <td>${element.abreviatura}</td>
                            <td style="text-align:right">${formatNumber.decimal(element.precio, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(element.subtotal, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(igv, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal((sub_total + igv), '', -2)}</td>
                            </tr>`;
                    // <td width="50px" style="text-align:right;">${formatter.format(sub_total)}</td>
                });

                response.req_pagos.forEach(element => {
                    var sub_total = parseFloat(element.precio_unitario) * parseFloat(element.cantidad);
                    var igv = sub_total * 0.18;
                    total_sin_igv += parseFloat(sub_total);
                    total += (sub_total + igv);
                    html += `<tr>
                            <td>${element.razon_social ?? ''}</td>
                            <td>${element.fecha_pago !== null ? formatDate(element.fecha_pago) : ''}</td>
                            <td>${element.codigo ?? ''}</td>
                            <td>${element.titulo_descripcion ?? ''}</td>
                            <td>${element.partida_descripcion ?? ''}</td>
                            <td>${element.descripcion ?? ''}</td>
                            <td>${element.cantidad ?? ''}</td>
                            <td>${element.abreviatura ?? ''}</td>
                            <td style="text-align:right">${formatNumber.decimal(element.precio_unitario ?? '', '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(element.subtotal ?? '', '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(igv, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(sub_total + igv, '', -2)}</td>
                            </tr>`;
                });

                html += `<tr>
                        <td colSpan="8"></td>
                        <td style="font-size: 14px;"><strong>Total Consumido</strong></td>
                        <td style="font-size: 14px; text-align:right;"><strong>${formatter.format(total_sin_igv)}</strong></td>
                        <td style="font-size: 14px; text-align:right;"></td>
                        <td style="font-size: 14px; text-align:right;"><strong>${formatter.format(total)}</strong></td>
                    </tr>`;

                $('#listaGastosPartidas tbody').html(html);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function exportarCuadroCostos() {
    var id_presup = $('[name=id_presup]').val();
    var form = $(`<form action="cuadroGastosExcel" method="post" target="_blank">
        <input type="hidden" name="_token" value="${csrf_token}"/>
        <input type="hidden" name="id_presupuesto" value="${id_presup}"/>
        </form>`);
    $('body').append(form);
    form.trigger('submit');
}