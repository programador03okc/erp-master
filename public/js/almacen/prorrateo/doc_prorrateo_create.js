function open_doc_prorrateo() {
    $('#modal-doc_prorrateo').modal({
        show: true
    });
    limpiarCampos();
}

function limpiarCampos() {
    $('[name=id_prorrateo_doc]').val('');
    $('[name=id_tp_prorrateo]').val('');
    $('[name=pro_serie]').val('');
    $('[name=pro_numero]').val('');
    $('[name=doc_fecha_emision]').val(fecha_actual());
    $('[name=tipo_cambio]').val(0);
    $('[name=doc_id_moneda]').val(0);
    $('[name=sub_total]').val(0);
    $('[name=importe]').val(0);
    $('[name=importe_aplicado]').val(0);
    $('[name=doc_razon_social]').val('');
    $('[name=doc_id_proveedor]').val('');
    $('[name=id_tp_documento]').val('').trigger('change.select2');
    $('[name=id_contrib]').val('');
}

$("#form-doc_prorrateo").on("submit", function () {
    var data = JSON.stringify($(this).serializeArray());
    console.log(data);
    guardar_doc_prorrateo();
    // mostrar_guias_detalle();

});

function guardar_doc_prorrateo() {
    let id = ($('[name=id_prorrateo_doc]').val() !== '' ? $('[name=id_prorrateo_doc]').val() : 'n' + (documentos.length + 1));

    let doc = documentos.find(doc => doc.id_prorrateo_doc == id);

    if (doc == undefined || doc == null) {
        let nuevo = {
            'id_prorrateo_doc': id,
            // 'id_doc_com': id,
            'id_tp_prorrateo': $('[name=id_tp_prorrateo]').val(),
            'id_tipo_prorrateo': $('[name=id_tipo_prorrateo]').val(),
            'tipo_prorrateo': $('select[name="id_tipo_prorrateo"] option:selected').text(),
            'tp_prorrateo': $('select[name="id_tp_prorrateo"] option:selected').text(),
            'id_proveedor': $('[name=doc_id_proveedor]').val(),
            'razon_social': $('[name=doc_razon_social]').val(),
            'fecha_emision': $('[name=doc_fecha_emision]').val(),
            'id_tp_documento': $('[name=id_tp_documento]').val(),
            'serie': $('[name=pro_serie]').val(),
            'numero': $('[name=pro_numero]').val(),
            'id_moneda': $('[name=doc_id_moneda]').val(),
            'total': $('[name=sub_total]').val(),
            'tipo_cambio': $('[name=tipo_cambio]').val(),
            'importe': $('[name=importe]').val(),
            'importe_aplicado': $('[name=importe_aplicado]').val(),
            'estado': 1,
        }
        documentos.push(nuevo);

    } else {
        doc.id_tp_prorrateo = $('[name=id_tp_prorrateo]').val();
        doc.tp_prorrateo = $('select[name="id_tp_prorrateo"] option:selected').text();
        doc.id_tipo_prorrateo = $('[name=id_tipo_prorrateo]').val();
        doc.tipo_prorrateo = $('select[name="id_tipo_prorrateo"] option:selected').text();
        doc.id_proveedor = $('[name=doc_id_proveedor]').val();
        doc.razon_social = $('[name=doc_razon_social]').val();
        doc.fecha_emision = $('[name=doc_fecha_emision]').val();
        doc.id_tp_documento = $('[name=id_tp_documento]').val();
        doc.serie = $('[name=pro_serie]').val();
        doc.numero = $('[name=pro_numero]').val();
        doc.id_moneda = $('[name=doc_id_moneda]').val();
        doc.total = $('[name=sub_total]').val();
        doc.tipo_cambio = $('[name=tipo_cambio]').val();
        doc.importe = $('[name=importe]').val();
        doc.importe_aplicado = $('[name=importe_aplicado]').val();
    }
    mostrar_documentos();

    $('#modal-doc_prorrateo').modal('hide');
}

$('[name=doc_id_moneda]').on("change", function () {
    console.log('changeMoneda');
    getTipoCambio();
});

$('[name=sub_total]').on("change", function () {
    console.log('changeSubTotal');
    calculaImporte();
});

$('[name=tipo_cambio]').on("change", function () {
    console.log('changeTipoCambio');
    calculaImporte();
});

$('[name=doc_fecha_emision]').on("change", function () {
    console.log('changeFechaEmision');
    getTipoCambio();
});

function calculaImporte() {
    var tcambio = $('[name=tipo_cambio]').val();
    var sub_total = $('[name=sub_total]').val();
    var id_moneda_global = $('[name=id_moneda_global]').val();
    var id_moneda_doc = $('[name=doc_id_moneda]').val();

    if (id_moneda_global == id_moneda_doc) {
        if (tcambio !== '' && tcambio !== '0' && tcambio !== 1) {
            var imp = formatDecimal(parseFloat(sub_total));
            $('[name=importe]').val(imp);
            $('[name=importe_aplicado]').val(imp);
        } else {
            $('[name=importe]').val(sub_total);
            $('[name=importe_aplicado]').val(sub_total);
        }
    } else {
        if (id_moneda_global == 1) {//soles
            if (tcambio !== '' && tcambio !== '0' && tcambio !== 1) {
                var imp = formatDecimal(parseFloat(sub_total) * parseFloat(tcambio));
                $('[name=importe]').val(imp);
                $('[name=importe_aplicado]').val(imp);
            } else {
                $('[name=importe]').val(sub_total);
                $('[name=importe_aplicado]').val(sub_total);
            }
        } else {//dolares
            if (tcambio !== '' && tcambio !== '0' && tcambio !== 1) {
                var imp = formatDecimal(parseFloat(sub_total) / parseFloat(tcambio));
                $('[name=importe]').val(imp);
                $('[name=importe_aplicado]').val(imp);
            } else {
                $('[name=importe]').val(sub_total);
                $('[name=importe_aplicado]').val(sub_total);
            }
        }
    }
}

function editar_documento(id_prorrateo_doc) {
    console.log(id_prorrateo_doc);
    $('#modal-doc_prorrateo').modal({
        show: true
    });
    let doc = documentos.find(doc => doc.id_prorrateo_doc == id_prorrateo_doc);

    $('[name=id_prorrateo_doc]').val(doc.id_prorrateo_doc);
    $('[name=id_tp_prorrateo]').val(doc.id_tp_prorrateo);
    $('[name=id_tipo_prorrateo]').val(doc.id_tipo_prorrateo);
    $('[name=pro_serie]').val(doc.serie);
    $('[name=pro_numero]').val(doc.numero);
    $('[name=doc_fecha_emision]').val(doc.fecha_emision);
    $('[name=tipo_cambio]').val(doc.tipo_cambio);
    $('[name=doc_id_moneda]').val(doc.id_moneda);
    $('[name=sub_total]').val(doc.total);
    $('[name=importe]').val(doc.importe);
    $('[name=importe_aplicado]').val(doc.importe_aplicado);
    $('[name=doc_razon_social]').val(doc.razon_social);
    $('[name=doc_id_proveedor]').val(doc.id_proveedor);
    $('[name=id_tp_documento]').val(doc.id_tp_documento).trigger('change.select2');
    $('[name=id_contrib]').val('');
}

function mostrar_documentos() {
    let tr = '';
    let i = 0;
    let total_aplicado_valor = 0;
    let total_aplicado_peso = 0;

    // let edition = ($("#form-prorrateo").attr('type') == 'edition' ? true : false);

    documentos.forEach(element => {

        if (element.estado !== 7) {
            i++;

            if (element.id_tipo_prorrateo == 1) {
                total_aplicado_valor += parseFloat(element.importe_aplicado);
            }
            else if (element.id_tipo_prorrateo == 2) {
                total_aplicado_peso += parseFloat(element.importe_aplicado);
            }
            tr += `<tr>
                <td>${i}</td>
                <td>${element.tp_prorrateo}</td>
                <td>${element.serie + '-' + element.numero}</td>
                <td>${element.razon_social}</td>
                <td>${element.fecha_emision}</td>
                <td style="text-align: right">${element.id_moneda == 1 ? 'S/' : '$'}</td>
                <td style="text-align: right">${element.total}</td>
                <td style="text-align: right">${element.tipo_cambio}</td>
                <td style="text-align: right">${element.importe}</td>
                <td style="text-align: right">${element.importe_aplicado}</td>
                <td style="text-align: right">${element.tipo_prorrateo}</td>
                <td style="display:flex;">
                    <button type="button" class="editar btn btn-primary btn-xs activation" data-toggle="tooltip" 
                        data-placement="bottom" title="Editar" onClick="editar_documento('${element.id_prorrateo_doc}');"
                        >  <i class="fas fa-pen"></i></button>
                    <button type="button" class="anular btn btn-danger btn-xs activation" data-toggle="tooltip" 
                        data-placement="bottom" title="Eliminar" onClick="anular_documento('${element.id_prorrateo_doc}');"
                        >  <i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
        }
    });
    // ${edition ? '' : 'disabled="true"'}
    // <i class="fas fa-pen-square icon-tabla blue visible boton" data-toggle="tooltip" data-placement="bottom" 
    // title="Editar" onClick="editar_documento('${element.id_doc_com}');"></i>
    // <i class="fas fa-trash icon-tabla red boton" data-toggle="tooltip" data-placement="bottom" 
    // title="Anular" onClick="anular_documento('${element.id_doc_com}');"></i>

    $('[name=total_comp_valor]').val(total_aplicado_valor);
    $('[name=total_comp_peso]').val(total_aplicado_peso);

    $('#listaProrrateos tbody').html(tr);

    mostrar_guias_detalle();
}

function anular_documento(id_doc_com) {
    let elimina = confirm("¿Esta seguro que desea eliminar éste documento?");

    if (elimina) {
        let doc = documentos.find(doc => doc.id_doc_com == id_doc_com);

        if (doc.id_prorrateo_doc == 0) {
            var index = documentos.findIndex(function (item, i) {
                return item.id_doc_com == id_doc_com;
            });
            documentos.splice(index, 1);
        }
        else {
            doc.estado = 7;
        }
        console.log(documentos);
        mostrar_documentos();
    }

}

function getTipoCambio() {
    var fecha = $('[name=doc_fecha_emision]').val();
    var sub_total = $('[name=sub_total]').val();

    if (fecha !== null && fecha !== '') {
        $.ajax({
            type: 'GET',
            url: 'obtenerTipoCambio/' + fecha + '/' + 2,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                $("[name=tipo_cambio]").val(response.venta);

                var tcambio = parseFloat(response.venta);
                var imp = formatDecimal(parseFloat(sub_total) * tcambio);

                $('[name=importe]').val(imp);
                $('[name=importe_aplicado]').val(imp);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        $('[name=tipo_cambio]').val(0);
        $('[name=importe]').val(sub_total);
        $('[name=importe_aplicado]').val(sub_total);
    }
}

function agregar_tipo() {
    var nombre = prompt('Ingrese el Nombre del nuevo tipo', '');
    console.log(nombre);
    if (nombre !== null) {
        var rspta = confirm("¿Está seguro que desea agregar éste tipo: " + nombre + "?");
        if (rspta) {
            $.ajax({
                type: 'GET',
                url: 'guardar_tipo_prorrateo/' + nombre,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    $('[name=id_tp_prorrateo]').html('');
                    var html = '<option value="0" disabled>Elija una opción</option>' + response;
                    $('[name=id_tp_prorrateo]').html(html);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    }
}

function ceros_numero(numero) {
    if (numero == 'pro_numero') {
        var num = $('[name=pro_numero]').val();
        $('[name=pro_numero]').val(leftZero(7, num));
    }
}