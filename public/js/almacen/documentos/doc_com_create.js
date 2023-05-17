let listaItems = [];
let totales = {};
let origenDoc = null;

function open_doc_create(id_guia, oc_ing) {
    console.log('open_doc_create');
    origenDoc = oc_ing;

    $('#modal-doc_create').modal({
        show: true
    });
    var id_tp_doc = 2;
    $('[name=id_tp_doc]').val(id_tp_doc).trigger('change.select2');
    $('[name=fecha_emision_doc]').val(fecha_actual());
    $('[name=serie_doc]').val("");
    $('[name=numero_doc]').val("");
    $('[name=moneda]').val(1);
    $('[name=simbolo]').val("S/");

    totales.simbolo = "S/";
    obtenerGuía(id_guia);
}

function obtenerGuía(id) {
    $.ajax({
        type: 'GET',
        url: 'obtenerGuia/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            if (response['guia'] !== null) {
                $('[name=id_proveedor]').val(response['guia'].id_proveedor);
                $('[name=proveedor_razon_social]').val(response['guia'].razon_social);
                $('[name=id_guia]').val(response['guia'].id_guia);
                $('[name=serie_guia]').val(response['guia'].serie);
                $('[name=numero_guia]').val(response['guia'].numero);
                $('[name=id_almacen_doc]').val(response['guia'].id_almacen);
            }

            if (response['detalle'].length > 0) {
                listaItems = response['detalle'];
                $('[name=id_condicion]').val(listaItems[0].id_condicion);
                $('[name=credito_dias]').val(listaItems[0].plazo_dias);
                $('[name=id_sede]').val(listaItems[0].id_sede);
                $('[name=moneda]').val(listaItems[0].id_moneda);
                $('[name=simbolo]').val(listaItems[0].simbolo);
                $('[name=id_cta_principal]').val(listaItems[0].id_cta_principal);

                totales = { 'porcentaje_igv': parseFloat(response['igv']) };

                totales.simbolo = listaItems[0].simbolo;
                mostrarListaItems();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function open_doc_create_seleccionadas() {
    console.log(ingresos_seleccionados);
    origenDoc = 'ing';
    var id_ingresos_seleccionadas = [];
    var id_prov = null;
    var prov = null;
    var emp = null;
    var dif_prov = 0;
    var dif_emp = 0;

    if (ingresos_seleccionados.length > 1) {

        ingresos_seleccionados.forEach(element => {
            id_ingresos_seleccionadas.push(element.id_guia_com);

            if (prov == null) {
                prov = element.razon_social;
                id_prov = element.id_proveedor;
            }
            else if (element.razon_social !== prov) {
                dif_prov++;
            }
            if (emp == null) {
                emp = element.id_empresa;
            }
            else if (element.id_empresa !== emp) {
                dif_emp++;
            }
        });

        var text = '';
        if (dif_prov > 0) text += 'Debe seleccionar Guías del mismo proveedor\n';
        if (dif_emp > 0) text += 'Debe seleccionar Guías emitidas para la misma empresa';

        if ((dif_prov + dif_emp) > 0) {
            // alert(text);
            Swal.fire({
                title: text,
                icon: "warning",
            });
        } else {

            $('#modal-doc_create').modal({
                show: true
            });
            var id_tp_doc = 2;
            $('[name=id_tp_doc]').val(id_tp_doc).trigger('change.select2');
            $('[name=fecha_emision_doc]').val(fecha_actual());
            $('[name=serie_doc]').val("");
            $('[name=numero_doc]').val("");
            $('[name=moneda]').val(1);
            $('[name=simbolo]').val("S/");

            totales.simbolo = "S/";
            obtenerGuíaSeleccionadas(id_ingresos_seleccionadas, prov, id_prov);
        }
    } else {
        Swal.fire({
            title: "Debe seleccionar varias guías",
            icon: "warning",
        });
    }
}

function obtenerGuíaSeleccionadas(id_ingresos_seleccionadas, prov, id_prov) {
    var data = 'id_ingresos_seleccionados=' + JSON.stringify(id_ingresos_seleccionadas);
    $.ajax({
        type: 'POST',
        url: 'obtenerGuiaSeleccionadas',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            // if (response['guia'] !== null){
            $('[name=id_proveedor]').val(id_prov);
            $('[name=proveedor_razon_social]').val(prov);
            $('[name=id_guia]').val('');
            $('[name=serie_guia]').val('');
            $('[name=numero_guia]').val('');
            // }

            if (response['detalle'].length > 0) {
                listaItems = response['detalle'];
                $('[name=id_condicion]').val(listaItems[0].id_condicion);
                $('[name=credito_dias]').val(listaItems[0].plazo_dias);
                $('[name=id_sede]').val(listaItems[0].id_sede);
                $('[name=moneda]').val(listaItems[0].id_moneda);
                $('[name=simbolo]').val(listaItems[0].simbolo);
                $('[name=id_almacen_doc]').val(listaItems[0].id_almacen);
                $('[name=id_cta_principal]').val(listaItems[0].id_cta_principal);

                totales.simbolo = listaItems[0].simbolo;

                totales = { 'porcentaje_igv': parseFloat(response['igv']) };
                mostrarListaItems();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrarListaItems() {
    var html = ''
    var i = 1;
    var sub_total = 0;

    listaItems.forEach(element => {
        // total_item = parseFloat(element.cantidad * element.precio);
        element.porcentaje_dscto = (element.porcentaje_dscto !== undefined ? element.porcentaje_dscto : 0);
        element.total_dscto = (element.total_dscto !== undefined ? element.total_dscto : 0);
        element.precio = (element.precio !== null ? element.precio : 0.01);
        element.sub_total = (parseFloat(element.cantidad) * parseFloat(element.precio));
        element.total = (element.sub_total - element.total_dscto);
        sub_total += element.total;

        html += `<tr>
        <td>${i}</td>
        <td>${(element.serie !== undefined ? (element.serie + '-' + element.numero) : (element.cod_orden !== null ? element.cod_orden : ''))}</td>
        <td>${element.codigo !== null ? element.codigo : ''}</td>
        <td>${element.part_number !== null ? element.part_number : ''}</td>
        <td>${element.id_producto == null
                ? `<input type="text" class="form-control descripcion" value="${element.descripcion}" data-id="${element.id_guia_com_det}"/>`
                : element.descripcion}</td>
        <td>${element.cantidad}</td>
        <td>${element.abreviatura}</td>
        <td>
            <input type="number"  style="text-align:right" class="form-control  unitario" value="${formatDecimalDigitos(element.precio, 4)}" 
            data-id="${element.id_guia_com_det}" min="0" step="0.0001"/>
        </td>
        <td style="text-align:right">${formatNumber.decimal(element.sub_total, '', -4)}</td>
        <td>
            <input type="number"  style="text-align:right" class="form-control  porcentaje_dscto" value="${element.porcentaje_dscto}" 
            data-id="${element.id_guia_com_det}" min="0" step="0.0001"/>
        </td>
        <td>
            <input type="number"  style="text-align:right" class="form-control  total_dscto" value="${element.total_dscto}" 
            data-id="${element.id_guia_com_det}" min="0" step="0.0001"/>
        </td>
        <td style="text-align:right">${formatNumber.decimal(element.total, '', -4)}</td>
        <td>
            ${element.id_producto == null ?
                `<button type="button" class="quitar btn btn-danger btn-xs" data-toggle="tooltip" 
                data-placement="bottom" title="Quitar item" data-id="${element.id_guia_com_det}">
                <i class="fas fa-minus"></i></button>`: ''}
        </td>
        </tr>`;
        i++;
    });

    $('#detalleItems tbody').html(html);

    totales.sub_total = sub_total;
    totales.igv = (totales.porcentaje_igv * sub_total / 100);
    totales.total_icbper = 0;
    totales.total = sub_total + totales.igv + totales.total_icbper;
    totales.simbolo = $('select[name="moneda"] option:selected').data('sim');

    var html_foot = `<tr>
        <th colSpan="11" style="text-align:right">Sub Total <label name="sim">${totales.simbolo}</label></th>
        <th style="text-align:right">${formatNumber.decimal(totales.sub_total, '', -2)}</th>
    </tr>
    <tr>
        <th colSpan="11" style="text-align:right">IGV ${totales.porcentaje_igv}% <label name="sim">${totales.simbolo}</label></th>
        <th style="text-align:right">${formatNumber.decimal(totales.igv, '', -2)}</th>
    </tr>
    <tr>
        <th colSpan="11" style="text-align:right">ICBPER <label name="sim">${totales.simbolo}</label></th>
        <th style="text-align:right">${formatNumber.decimal(totales.total_icbper, '', -2)}</th>
    </tr>
    <tr>
        <th colSpan="11" style="text-align:right"> Total <label name="sim">${totales.simbolo}</label></th>
        <th style="text-align:right">${formatNumber.decimal(totales.total, '', -2)}</th>
    </tr>
    `;
    $('#detalleItems tfoot').html(html_foot);
    $('[name=importe]').val(formatNumber.decimal(totales.total, '', -2));
}

$("#detalleItems tbody").on("click", ".quitar", function () {
    let id = $(this).data("id");
    console.log(id);
    let index = listaItems.findIndex(function (item, i) {
        return item.id_guia_com_det == id;
    });
    listaItems.splice(index, 1);
    mostrarListaItems();
});

$('#detalleItems tbody').on("change", ".descripcion", function () {

    let id_guia_com_det = $(this).data('id');
    let descripcion = $(this).val();
    console.log('descripcion: ' + descripcion);
    listaItems.forEach(element => {
        if (element.id_guia_com_det == id_guia_com_det) {
            element.descripcion = descripcion.trim();
            console.log(element);
        }
    });
    mostrarListaItems();
});

$('#detalleItems tbody').on("change", ".unitario", function () {

    let id_guia_com_det = $(this).data('id');
    let unitario = parseFloat($(this).val() !== '' ? $(this).val() : 0);
    console.log('unitario: ' + unitario);
    // let item = listaItems.find(element => element.id_guia_com_det == id_guia_com_det);
    listaItems.forEach(element => {
        if (element.id_guia_com_det == id_guia_com_det) {
            element.precio = unitario;
            element.sub_total = (unitario * parseFloat(element.cantidad));
            element.total = (element.sub_total - element.total_dscto);
            console.log(element);
        }
    });
    mostrarListaItems();
});

$('#detalleItems tbody').on("change", ".porcentaje_dscto", function () {

    let id_guia_com_det = $(this).data('id');
    let porcentaje_dscto = parseFloat($(this).val() !== '' ? $(this).val() : 0);
    let unitario = 0;
    console.log('porcentaje_dscto: ' + porcentaje_dscto);
    // let item = listaItems.find(element => element.id_guia_com_det == id_guia_com_det);
    listaItems.forEach(element => {
        if (element.id_guia_com_det == id_guia_com_det) {

            element.porcentaje_dscto = porcentaje_dscto;
            element.total_dscto = (porcentaje_dscto * element.sub_total / 100);
            element.total = (element.sub_total - element.total_dscto);
            console.log(element);
        }
    });
    mostrarListaItems();
});

$('#detalleItems tbody').on("change", ".total_dscto", function () {

    let id_guia_com_det = $(this).data('id');
    let total_dscto = parseFloat($(this).val() !== '' ? $(this).val() : 0);
    console.log('total_dscto: ' + total_dscto);
    // let item = listaItems.find(element => element.id_guia_com_det == id_guia_com_det);
    listaItems.forEach(element => {
        if (element.id_guia_com_det == id_guia_com_det) {
            element.porcentaje_dscto = 0;
            element.total_dscto = total_dscto;
            element.total = (element.sub_total - total_dscto);
            console.log(element);
        }
    });
    mostrarListaItems();
});

$("#form-doc_create").on("submit", function (e) {
    e.preventDefault();
    var valida = '';

    listaItems.forEach(element => {
        valida += (element.id_producto == null && element.descripcion == '' ? 'Debe ingresar un servicio!\n' : '');
        valida += (element.total > 0 ? '' : 'Debe ingresar un precio unitario mayor a cero\n');
    });

    if (valida !== '') {
        Swal.fire(valida, "", "warning");
    } else {
        Swal.fire({
            title: "¿Está seguro que desea guardar éste Documento de compra?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00a65a", //"#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Si, Guardar"
        }).then(result => {
            if (result.isConfirmed) {

                var id_doc_com = $('[name=id_doc_com]').val();
                var serial = $(this).serialize();
                var listaItemsDetalle = [];
                var nuevo = null;

                listaItems.forEach(element => {
                    nuevo = {
                        'id_guia_com_det': element.id_guia_com_det,
                        'id_producto': element.id_producto,
                        'descripcion': (element.id_producto == null ? element.descripcion : ''),
                        'cantidad': element.cantidad,
                        'id_unid_med': element.id_unid_med,
                        'id_moneda_producto': element.id_moneda_producto,
                        'precio': element.precio,
                        'sub_total': element.sub_total,
                        'porcentaje_dscto': element.porcentaje_dscto,
                        'total_dscto': element.total_dscto,
                        'total': element.total,
                        'id_oc_det': element.id_oc_det,
                    }
                    listaItemsDetalle.push(nuevo);
                });

                var data = serial +
                    '&sub_total=' + totales.sub_total +
                    '&porcentaje_igv=' + totales.porcentaje_igv +
                    '&igv=' + totales.igv +
                    '&total=' + totales.total +
                    '&detalle_items=' + JSON.stringify(listaItemsDetalle);
                console.log(data);
                guardar_doc_create(data);
            }
        });
    }
});

function guardar_doc_create(data) {
    $.ajax({
        type: 'POST',
        url: 'guardar_doc_compra',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response.id_doc > 0) {
                // alert('Comprobante registrado con éxito');
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Comprobante registrado con éxito.'
                });
                $('#modal-doc_create').modal('hide');
                if (origenDoc == 'ing') {
                    // listarIngresos();
                    $("#listaIngresosAlmacen").DataTable().ajax.reload(null, false);
                }
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function ceros_numero_doc() {
    var num = $('[name=numero_doc]').val();
    if (num !== '') {
        $('[name=numero_doc]').val(leftZero(6, num));
    }
}

function changeMoneda() {
    var simbolo = $('select[name="moneda"] option:selected').data('sim');
    if (simbolo.length > 0) {
        console.log(simbolo);
        $('[name=simbolo]').val(simbolo);
        $('[name=sim]').text(simbolo);
    } else {
        $('[name=simbolo]').val("");
        $('[name=sim]').text("");
    }
}

function agregarServicio() {
    let count = $('#detalleItems tbody tr').length + 1;
    console.log(count);

    nuevo = {
        'abreviatura': "SER",
        'cantidad': "1",
        'cod_orden': "",
        'codigo': "",
        'descripcion': "",
        'estado': 1,
        'id_guia_com': null,
        'id_guia_com_det': count,
        'id_producto': null,
        'id_unid_med': 29,//Servicio
        'part_number': "",
        'porcentaje_dscto': 0,
        'precio': 0,
        'sub_total': 0,
        'id_oc_det':null,
        'total': 0,
        'total_dscto': 0
    }

    listaItems.push(nuevo);
    mostrarListaItems();
}