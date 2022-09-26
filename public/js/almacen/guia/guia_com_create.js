let $filaActual;
function open_guia_create(data, $fila) {
    console.log(data);
    $('#modal-guia_create').modal({
        show: true
    });
    $filaActual = $fila;
    $("#submit_guia").removeAttr("disabled");
    $('[name=id_operacion]').val(2);
    $('[name=nombre_operacion]').val('COMPRA NACIONAL');
    $('[name=id_guia_clas]').val(1);
    $('[name=id_proveedor]').val(data.id_proveedor);
    $('[name=razon_social_proveedor]').val(data.razon_social);
    $('[name=id_sede]').val(data.id_sede);
    $('[name=id_orden_compra]').val(data.id_orden_compra);
    $('[name=id_od]').val('');
    $('[name=id_requerimiento]').val('');
    $('[name=id_transformacion]').val('');
    $('[name=id_devolucion]').val('');
    $('[name=serie]').val('');
    $('[name=numero]').val('');
    $('[name=fecha_emision]').val(fecha_actual());
    $('[name=fecha_almacen]').val(fecha_actual());
    $('#detalleOrdenSeleccionadas tbody').html('');
    $('.agregarSobrante').hide();
    $('[name=comentario]').val('');

    if (data.id_tp_documento == 12) { // orden de importación
        $('#titulo').text('Ingresar Hoja de Importación');
        $('[name=id_operacion]').val(18);
        $('[name=nombre_operacion]').val('IMPORTACIÓN');
    } else
        if (data.id_tp_documento == 13) { // orden de devolución
            $('#titulo').text('Ingresar Hoja de Devolución');
            $('[name=id_operacion]').val(24);// INGRESO POR DEVOLUCIÓN DEL CLIENTE
            $('[name=nombre_operacion]').val('INGRESO POR DEVOLUCIÓN DEL CLIENTE');
        } else {
            $('#titulo').text('Ingresar Guía de Compra');
        }

    $('#serie').text('');
    $('#numero').text('');
    cargar_almacenes(data.id_sede, 0);
    $("#id_almacen").removeAttr("disabled");

    $(".orden_transformacion").html('');
    $(".transformacion").hide();
    $(".devolucion").hide();
    $(".compra").show();

    var data = 'oc_seleccionadas=' + JSON.stringify([data.id_orden_compra]);
    listar_detalle_ordenes_seleccionadas(data);
}

function open_guia_create_seleccionadas() {
    var id_prov = null;
    var proveedor = null;
    var sede = null;
    var dif_prov = 0;
    var dif_sede = 0;
    var id_oc_seleccionadas = [];

    if (oc_seleccionadas.length > 1) {

        oc_seleccionadas.forEach(element => {
            id_oc_seleccionadas.push(element.id_orden_compra);

            if (id_prov == null) {
                id_prov = element.id_proveedor;
                proveedor = element.razon_social;
            }
            else if (element.id_proveedor !== id_prov) {
                dif_prov++;
            }
            if (sede == null) {
                sede = element.id_sede;
            }
            else if (element.id_sede !== sede) {
                dif_sede++;
            }
        });

        var text = '';
        if (dif_prov > 0) text += 'Debe seleccionar OCs del mismo proveedor\n';
        if (dif_sede > 0) text += 'Debe seleccionar OCs de la misma sede';

        if ((dif_sede + dif_prov) > 0) {
            // alert(text);
            Swal.fire({
                title: text,
                icon: "warning",
            });
        } else {
            $('#modal-guia_create').modal({
                show: true
            });
            $("#submit_guia").removeAttr("disabled");
            $('[name=id_operacion]').val(2).trigger('change.select2');
            $('[name=id_guia_clas]').val(1);
            $('[name=id_proveedor]').val(id_prov);
            $('[name=razon_social_proveedor]').val(proveedor);
            $('[name=id_sede]').val(sede);
            $('[name=id_transformacion]').val('');
            $('[name=id_requerimiento]').val('');
            $('[name=id_od]').val('');
            $('[name=serie]').val('');
            $('[name=numero]').val('');
            $('[name=id_orden_compra]').val('');
            $('[name=fecha_emision]').val(fecha_actual());
            $('[name=fecha_almacen]').val(fecha_actual());

            $('#detalleOrdenSeleccionadas tbody').html('');
            $('.agregarSobrante').hide();
            $('[name=comentario]').val('');

            // if (data.id_tp_documento == 12) {
            //     $('#titulo').text('Ingresar Hoja de Importación');
            //     $('[name=id_operacion]').val(18).trigger('change.select2');
            // } else {
            $('#titulo').text('Ingresar Guía de Compra');
            // }

            $('#serie').text('');
            $('#numero').text('');
            cargar_almacenes(sede, 0);
            $("#id_almacen").removeAttr("disabled");

            var data = 'oc_seleccionadas=' + JSON.stringify(id_oc_seleccionadas);
            listar_detalle_ordenes_seleccionadas(data);
        }
    } else {
        Swal.fire({
            title: "Debe seleccionar varias ordenes",
            icon: "warning",
        });
    }
}

function listar_detalle_ordenes_seleccionadas(data) {
    console.log(oc_seleccionadas);
    console.log(data);
    oc_det_seleccionadas = [];
    $('#detalleOrdenSeleccionadas tbody').html('');

    $.ajax({
        type: 'POST',
        url: 'detalleOrdenesSeleccionadas',
        data: data,
        dataType: 'JSON',
        success: function (response) {
            var cant = 0;
            console.log(response);
            response.forEach(function (element) {
                cant = parseFloat(element.cantidad) - parseFloat(element.suma_cantidad_guias !== null ? element.suma_cantidad_guias : 0);
                oc_det_seleccionadas.push({
                    'id_oc_det': element.id_detalle_orden,
                    'id_producto': element.id_producto,
                    'id_categoria': element.id_categoria,
                    'codigo_oc': element.codigo_oc,
                    'codigo': element.codigo,
                    'part_number': element.part_number,
                    'descripcion': element.descripcion,
                    'cantidad': cant,
                    'id_unid_med': element.id_unidad_medida,
                    'abreviatura': element.abreviatura,
                    'simbolo': element.simbolo,
                    'precio': element.precio,
                    'subtotal': (element.cantidad * element.precio),
                    'control_series': element.series,
                    'series': []
                });
            });
            mostrar_ordenes_seleccionadas();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_ordenes_seleccionadas() {
    var html = '';
    var html_ser = '';
    var i = 1;
    $('#detalleOrdenSeleccionadas tbody').html('');

    oc_det_seleccionadas.forEach(function (element) {
        html_ser = '';
        element.series.forEach(function (serie) {
            html_ser += (html_ser == '' ? '' : ', ') + serie;
        });
        html += `<tr>
            <td><input type="checkbox" data-tipo="${element.id_oc_det !== null ? 'orden' : 'producto'}" 
                value="${element.id_oc_det !== null ? element.id_oc_det : element.id_producto}" checked/></td>
            <td>${element.codigo_oc !== null ? element.codigo_oc : ''}</td>
            <td><a href="#" class="verProducto" data-id="${element.id_producto}" >${element.codigo}</a></td>
            <td>${element.part_number !== null ? element.part_number : ''}</td>
            <td>${(element.id_categoria == 117 ?
                `<i class="fas fa-exclamation-triangle orange" style="cursor:pointer;" onClick="abrirProducto(${element.id_producto});" 
                title="El producto fue creado con Categoría = Por definir"></i>`: '')
            + element.descripcion + ' <br><strong>' + html_ser + '</strong>'}
            </td>
            <td><input class="right" type="number" id="${element.id_oc_det !== null ? element.id_oc_det : 'p' + element.id_producto}cantidad" step="0.01" value="${element.cantidad}" 
                min="0.01" ${element.id_oc_det !== null ? `max="${element.cantidad}"` : ''} style="width:80px;"/></td>
            <td>${element.abreviatura}</td>
            <td class="text-right">${formatNumber.decimal(element.precio, element.simbolo, -4)}</td>
            <td class="text-right">${formatNumber.decimal((element.cantidad * element.precio), element.simbolo, -4)}</td>
            <td>
                <input type="text" class="oculto" id="series" value="${element.series}" 
                data-partnumber="${element.part_number !== null ? element.part_number : element.codigo}"/>
                ${element.control_series ?
                `<i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" 
                    onClick="${element.id_oc_det !== null ? `agrega_series(${element.id_oc_det});` : `agrega_series_producto(${element.id_producto});`}"></i>`
                : ''}
                
            </td>
        </tr>`;
        i++;
    });
    $('#detalleOrdenSeleccionadas tbody').html(html);
}

$("#detalleOrdenSeleccionadas tbody").on("click", "a.verProducto", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    abrirProducto(id);
});

$("#form-guia_create").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();

    var detalle = [];
    var validaCampos = '';
    var ope = $('[name=id_operacion]').val();

    if (ope == 26) {
        series_transformacion.forEach(function (element) {
            if (element.control_series) {
                // var part_number = $(this).parent().parent().find('td input[id=series]').data('partnumber');
                if (element.series.length == 0) {
                    validaCampos += 'Es necesario que agregue series al producto ' + (element.cod_prod) + '.\n';
                }
                else if (element.series.length > 0 && element.series.length < parseFloat(element.cantidad)) {
                    var dif = parseFloat(element.cantidad) - element.series.length;
                    validaCampos += 'El producto ' + (element.codigo) + ' requiere que se agreguen ' + dif + ' series.\n';
                }
            }

            if (element.id_producto == null && element.id_categoria == null && element.id_subcategoria == null) {
                validaCampos += 'Falta mapear el producto ' + (element.codigo) + '.\n';
            }
            detalle.push({
                'id': element.id_detalle,
                'tipo': element.tipo,
                'id_producto': element.id_producto,
                'part_number': (element.id_producto !== null ? '' : element.part_number),
                'descripcion': (element.id_producto !== null ? '' : element.descripcion),
                'id_categoria': element.id_categoria,
                'id_clasif': element.id_clasif,
                'id_subcategoria': element.id_subcategoria,
                'id_unidad_medida': element.id_unidad_medida,
                'control_series': element.control_series,
                'cantidad': element.cantidad,
                'id_moneda': element.id_moneda,
                'unitario': element.valor_unitario,
                'series': element.series
            });
        });
    } else if (ope == 2 || ope == 18) {
        $("#detalleOrdenSeleccionadas input[type=checkbox]:checked").each(function () {
            var id = $(this).val();
            var tipo = $(this).data('tipo');
            var json = null;

            if (tipo == 'orden') {
                json = oc_det_seleccionadas.find(element => element.id_oc_det == id);
            }
            else if (tipo == 'producto') {
                json = oc_det_seleccionadas.find(element => element.id_producto == id);
            }
            var series = (json !== null ? json.series : []);
            var cantidad = $(this).parent().parent().find('td input[id=' + (tipo == 'producto' ? 'p' : '') + id + 'cantidad]').val();

            if (json.control_series) {
                var part_number = $(this).parent().parent().find('td input[id=series]').data('partnumber');
                if (series.length == 0) {
                    validaCampos += 'Es necesario que agregue series al producto ' + part_number + '.\n';
                }
                else if (series.length > 0 && series.length < parseFloat(cantidad)) {
                    var dif = parseFloat(cantidad) - series.length;
                    validaCampos += 'El producto ' + part_number + ' requiere que se agreguen ' + dif + ' series.\n';
                }
            }
            detalle.push({
                'id_detalle_orden': (tipo == 'orden' ? id : null),
                'cantidad': cantidad,
                'id_producto': (tipo == 'producto' ? id : null),
                'id_unid_med': json.id_unid_med,
                'series': series
            });
        });
    } else {
        detalle_devolucion.forEach(function (element) {
            if (element.control_series) {
                if (element.series.length == 0) {
                    validaCampos += 'Es necesario que agregue series al producto ' + (element.cod_prod) + '.\n';
                }
                else if (element.series.length > 0 && element.series.length < parseFloat(element.cantidad)) {
                    var dif = parseFloat(element.cantidad) - element.series.length;
                    validaCampos += 'El producto ' + (element.cod_prod) + ' requiere que se agreguen ' + dif + ' series.\n';
                }
            }

            detalle.push({
                'id': element.id_detalle,
                'id_producto': element.id_producto,
                'id_unidad_medida': element.id_unidad_medida,
                'control_series': element.control_series,
                'cantidad': element.cantidad,
                'id_moneda': element.id_moneda,
                'unitario': element.valor_unitario,
                'series': element.series
            });
        });
    }
    console.log(detalle);
    if (validaCampos.length > 0) {
        Swal.fire(validaCampos, "", "warning");
    } else {
        var tra = $('[name=id_transformacion]').val();
        var dev = $('[name=id_devolucion]').val();
        var no_procede = 0;

        if (tra !== '') {
            var mnd = $('[name=moneda_transformacion]').val();
            var tipo = $('[name=tipo_cambio_transformacion]').val();

            if (mnd == '') {
                Lobibox.notify("warning", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Debe ingresar una moneda para el cálculo.'
                });
                no_procede++;
            }
            if (tipo == '') {
                Lobibox.notify("warning", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Debe ingresar un tipo de cambio para el cálculo.'
                });
                no_procede++;
            }
        }

        if (dev !== '') {
            var mnd = $('[name=moneda_devolucion]').val();
            var tipo = $('[name=tipo_cambio_devolucion]').val();

            if (mnd == '') {
                Lobibox.notify("warning", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Debe ingresar una moneda para el cálculo.'
                });
                no_procede++;
            }
            if (tipo == '') {
                Lobibox.notify("warning", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Debe ingresar un tipo de cambio para el cálculo.'
                });
                no_procede++;
            }
        }

        if (no_procede == 0) {
            Swal.fire({
                title: "¿Está seguro que desea guardar ésta Guía?",
                // text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00a65a", //"#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: "Cancelar",
                confirmButtonText: "Sí, Guardar"
            }).then(result => {
                if (result.isConfirmed) {
                    $("#id_almacen").removeAttr("disabled");

                    var data = $(this).serialize();
                    data += '&detalle=' + JSON.stringify(detalle);
                    console.log(data);
                    guardar_guia_create(data);
                }
            });
        }
    }
});

function guardar_guia_create(data) {

    $("#submit_guia").attr('disabled', 'true');

    $.ajax({
        type: 'POST',
        url: 'guardar_guia_com_oc',// para ordenes y transformaciones
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response['id_ingreso'] == null) {
                Swal.fire({
                    title: "Ya existe la serie-número de Guía!",
                    text: "Verifique en ingresos procesados.",
                    icon: "error",
                }).then(result => {
                    $("#submit_guia").removeAttr("disabled");
                });
            } else {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'Ingreso Almacén generado con éxito.'
                });
                var tra = $('[name=id_transformacion]').val();
                var dev = $('[name=id_devolucion]').val();
                if (tra !== '') {
                    listarTransformaciones();
                }
                else if (dev !== '') {
                    listarDevoluciones();
                }
                else {
                    Swal.fire({
                        title: "¿Desea ingresar ahora el documento de compra?",
                        icon: "info",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6", //"#00a65a", //
                        cancelButtonColor: "#d33",
                        cancelButtonText: "Aún No.",
                        confirmButtonText: "Si, Ingresar"
                    }).then(result => {
                        if (result.isConfirmed) {
                            open_doc_create(response['id_guia'], 'oc');
                        }
                    });
                    // listarOrdenesPendientes();
                    $("#ordenesPendientes").DataTable().ajax.reload(null, false);
                }
                $('#nro_ordenes').text(response.nroOrdenesPendientes);
                $('#nro_transformaciones').text(response.nroTransformacionesPendientes);
                $('#nro_devoluciones').text(response.nroDevolucionesPendientes);
                $('#modal-guia_create').modal('hide');
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

}
/*
function agregarProducto(producto) {
    // oc_det_seleccionadas.push({ 
    //     'id_oc_det'    : null,
    //     'id_producto'  : parseInt(producto.id_producto),
    //     'codigo_oc'    : null,
    //     'codigo'       : producto.codigo,
    //     'part_number'  : producto.part_number,
    //     'descripcion'  : producto.descripcion,
    //     'cantidad'     : 1,
    //     'id_unid_med'  : producto.id_unidad_medida,
    //     'abreviatura'  : producto.abreviatura,
    //     'precio'       : 0.01,
    //     'subtotal'     : 0.01,
    //     'series'       : []
    // });
    // mostrar_ordenes_seleccionadas();
    if (series_transformacion.length > 0) {
        var cod = series_transformacion[0].codigo;
        series_transformacion.push({
            'id': null,
            'id_detalle': null,
            'series': [],
            'tipo': 'sobrante',
            'cantidad': 1,
            'id_producto': parseInt(producto.id_producto),
            'codigo': cod,
            'cod_prod': producto.codigo,
            'part_number': producto.part_number,
            'descripcion': producto.descripcion,
            'abreviatura': producto.abreviatura,
            'control_series': producto.control_series,
            'valor_unitario': 0.01,
            'valor_total': 0.01
        });
        mostrar_detalle_transformacion();
    }
}*/

function abrirProducto(id_producto) {
    console.log('abrirProducto' + id_producto);
    localStorage.setItem("id_producto", id_producto);
    var win = window.open("/almacen/catalogos/productos/index", '_blank');
    win.focus();
}

function actualizarDetalle() {
    var id_tr = $('[name=id_transformacion]').val();
    var id_dev = $('[name=id_devolucion]').val();

    if (id_tr !== '') {
        listar_detalle_transformacion(id_tr);
    } else if (id_dev !== '') {
        listar_detalle_devolucion(id_dev);
    } else {
        var id_oc = $('[name=id_orden_compra]').val();
        if (id_oc == '') {
            var id_oc_seleccionadas = [];
            oc_seleccionadas.forEach(element => {
                id_oc_seleccionadas.push(element.id_orden_compra);
            });
            var data = 'oc_seleccionadas=' + JSON.stringify(id_oc_seleccionadas);
            listar_detalle_ordenes_seleccionadas(data);
        } else {
            var data = 'oc_seleccionadas=' + JSON.stringify([id_oc]);
            listar_detalle_ordenes_seleccionadas(data);
        }
    }
}

function ceros_numero(numero) {
    if (numero == "numero") {
        var num = $("[name=numero]").val();
        if (num !== '') {
            $("[name=numero]").val(leftZero(7, num));
        }
    } else if (numero == "serie") {
        var num = $("[name=serie]").val();
        if (num !== '') {
            $("[name=serie]").val(leftZero(4, num));
        }
    }
}