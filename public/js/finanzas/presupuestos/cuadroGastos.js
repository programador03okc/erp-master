
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
                var total = 0;

                response.req_compras.forEach(element => {
                    var sub_total = parseFloat(element.precio) * parseFloat(element.cantidad);
                    var igv = sub_total * 0.18;
                    total += parseFloat(sub_total);
                    html += `<tr>
                            <td>${element.razon_social}</td>
                            <td>${element.fecha_pago}</td>
                            <td>${element.codigo}</td>
                            <td>${element.codigo_oc}</td>
                            <td>${element.titulo_descripcion}</td>
                            <td>${element.partida_descripcion}</td>
                            <td>${element.descripcion_adicional}</td>
                            <td>${element.cantidad}</td>
                            <td>${element.abreviatura}</td>
                            <td>${element.precio}</td>
                            <td>${element.subtotal}</td>
                            <td>${igv}</td>
                            <td>${sub_total + igv}</td>
                            </tr>`;
                    // <td width="50px" style="text-align:right;">${formatter.format(sub_total)}</td>
                });

                response.req_pagos.forEach(element => {
                    var sub_total = parseFloat(element.precio_unitario) * parseFloat(element.cantidad);
                    var igv = sub_total * 0.18;
                    total += parseFloat(sub_total);
                    html += `<tr>
                            <td>${element.razon_social}</td>
                            <td>${element.fecha_pago}</td>
                            <td>${element.codigo}</td>
                            <td></td>
                            <td>${element.titulo_descripcion}</td>
                            <td>${element.partida_descripcion}</td>
                            <td>${element.descripcion}</td>
                            <td>${element.cantidad}</td>
                            <td>${element.abreviatura}</td>
                            <td>${element.precio_unitario}</td>
                            <td>${element.subtotal}</td>
                            <td>${igv}</td>
                            <td>${sub_total + igv}</td>
                            </tr>`;
                });

                html += `<tr>
                        <td colSpan="2"></td>
                        <td style="font-size: 14px;"><strong>Total Consumido</strong></td>
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