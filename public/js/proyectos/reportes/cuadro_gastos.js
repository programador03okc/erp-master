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
                var total_sin_igv_req = 0;
                var total_req = 0;
                
                var total_ord = 0;
                var total_sin_igv_ord = 0;

                response.req_compras.forEach(element => {
                    //req
                    var unitario_req = (element.precio_requerimiento != null && element.precio_requerimiento >0 ? element.precio_requerimiento : 0);
                    var sub_total_req = parseFloat(unitario_req) * parseFloat(element.cantidad >0 ?element.cantidad:0);
                    var igv_req = sub_total_req >0? (sub_total_req * 0.18):0;

                    //orden
                    var unitario_orden = (element.precio_orden != null && element.precio_orden >0 ? element.precio_orden : 0);
                    var sub_total_orden = parseFloat(unitario_orden) * parseFloat(element.cantidad_orden >0 ? element.cantidad_orden:0);
                    var igv_orden = sub_total_orden>0?(sub_total_orden * 0.18):0;

                    //totales
                    total_sin_igv_req += parseFloat(sub_total_req>0?sub_total_req:0);
                    total_req += (sub_total_req + igv_req);

                    total_sin_igv_ord += parseFloat(sub_total_orden >0 ?sub_total_orden:0);
                    total_ord += (sub_total_orden + igv_orden);

                    html += `<tr>
                            <td>Logístico</td>
                            <td>${element.fecha_requerimiento}</td>
                            <td>${element.codigo}</td>
                            <td>${element.titulo_descripcion}</td>
                            <td>${element.partida_descripcion}</td>
                            <td>${element.tipo_comprobante ?? ''}</td>
                            <td>${element.serie_numero ?? ''}</td>
                            <td>${element.fecha_emision_comprobante ?? ''}</td>
                            <td>${element.proveedor_razon_social ?? ''}</td>
                            <td>${element.descripcion}</td>
                            <td>${element.cantidad}</td>
                            <td>${element.abreviatura}</td>
                            <td>${element.simbolo_moneda_requerimiento ?? ''}</td>
                            <td style="text-align:right">${formatNumber.decimal(unitario_req, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(sub_total_req, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(igv_req, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal((sub_total_req + igv_req), '', -2)}</td>
                            <td  style="text-align:center">${element.estado_pago ?? ''}</td>
                            <td>${element.fecha_orden ?? ''}</td>
                            <td>${element.codigo_orden??''}</td>
                            <td style="text-align:center">${element.cantidad_orden??''}</td>
                            <td style="text-align:center">${element.unidad_orden??''}</td>
                            <td style="text-align:center">${element.simbolo_moneda_orden?? ''}</td>
                            <td style="text-align:right">${element.precio_orden?? ''}</td>
                            <td style="text-align:right">${element.subtotal_orden?? ''}</td>
                            <td style="text-align:right">${igv_orden !=null ? (formatNumber.decimal(igv_orden, '', -2)):''}</td>
                            <td style="text-align:right">${ sub_total_orden!=null && igv_orden !=null ? (formatNumber.decimal((sub_total_orden + igv_orden), '', -2)):''}</td>
                            </tr>`;
                    // <td width="50px" style="text-align:right;">${formatter.format(sub_total)}</td>
                });

                response.req_pagos.forEach(element => {
                    var sub_total = parseFloat(element.precio_unitario) * parseFloat(element.cantidad);
                    var igv = sub_total * 0.18;
                    
                    total_sin_igv_req += parseFloat(sub_total);
                    total_req += (sub_total + igv);

                    html += `<tr>
                            <td>Pago</td>
                            <td>${formatDate(element.fecha_registro)}</td>
                            <td>${element.codigo ?? ''}</td>
                            <td>${element.titulo_descripcion ?? ''}</td>
                            <td>${element.partida_descripcion ?? ''}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${element.apellido_paterno !== null ? (element.apellido_paterno + ' ' + element.apellido_materno + ' ' + element.nombres) : ''}</td>
                            <td>${element.descripcion ?? ''}</td>
                            <td  style="text-align:center">${element.cantidad ?? ''}</td>
                            <td  style="text-align:center">${element.abreviatura ?? ''}</td>
                            <td  style="text-align:center">${element.simbolo_requerimiento ?? ''}</td>
                            <td style="text-align:right">${formatNumber.decimal(element.precio_unitario ?? '', '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(sub_total ?? '', '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(0, '', -2)}</td>
                            <td style="text-align:right">${formatNumber.decimal(sub_total, '', -2)}</td>
                            <td  style="text-align:center">${element.estado_pago ?? ''}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            </tr>`;
                });

                html += `<tr>
                        <td style="font-size: 14px; text-align:right;" colSpan="12"><strong>Total Consumido</strong></td>
                        <td style="font-size: 14px; text-align:right;"></td>
                        <td style="font-size: 14px; text-align:right;"><strong>${formatNumber.decimal(total_sin_igv_req, '', -2)}</strong></td>
                        <td style="font-size: 14px; text-align:right;"></td>
                        <td style="font-size: 14px; text-align:right;"><strong>${formatNumber.decimal(total_req, '', -2)}</strong></td>
                        <td style="font-size: 14px; text-align:right;" colSpan="6"><strong>Total Consumido</strong></td>
                        <td style="font-size: 14px; text-align:right;"><strong>${formatNumber.decimal(total_sin_igv_ord, '', -2)}</strong></td>
                        <td style="font-size: 14px; text-align:right;"></td>
                        <td style="font-size: 14px; text-align:right;"><strong>${formatNumber.decimal(total_ord, '', -2)}</strong></td>

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