let $filaActual;
function open_guia_create(data, $fila) {
    console.log(data);
    $('#modal-guia_create').modal({
        show: true
    });
    $filaActual = $fila;
    $("#submit_guia").removeAttr("disabled");
    $('[name=id_operacion]').val(2).trigger('change.select2');
    $('[name=id_guia_clas]').val(1);
    $('[name=id_proveedor]').val(data.id_proveedor);
    $('[name=razon_social_proveedor]').val(data.razon_social);
    $('[name=id_sede]').val(data.id_sede);
    $('[name=id_orden_compra]').val(data.id_orden_compra);
    $('[name=id_od]').val('');
    $('[name=id_requerimiento]').val('');
    $('[name=id_transformacion]').val('');
    $('[name=serie]').val('');
    $('[name=numero]').val('');
    $('[name=fecha_emision]').val(fecha_actual());
    $('[name=fecha_almacen]').val(fecha_actual());
    $('#detalleOrdenSeleccionadas tbody').html('');
    $('.agregarSobrante').hide();

    $('#serie').text('');
    $('#numero').text('');
    cargar_almacenes(data.id_sede, 'id_almacen');
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

            $('#serie').text('');
            $('#numero').text('');
            cargar_almacenes(sede, 'id_almacen');
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

function open_transformacion_guia_create(data) {
    console.log(data);
    $('#modal-guia_create').modal({
        show: true
    });
    $("#submit_guia").removeAttr("disabled");
    $('[name=id_operacion]').val(26).trigger('change.select2');
    $('[name=id_guia_clas]').val(1);
    $('[name=id_proveedor]').val(data.id_proveedor);
    $('[name=id_sede]').val(data.id_sede);
    $('[name=id_transformacion]').val(data.id_transformacion);
    $('[name=id_orden_compra]').val('');
    $('[name=id_od]').val(data.id_od);
    $('[name=id_requerimiento]').val(data.id_requerimiento);
    $('[name=serie]').val(data.serie);
    $('[name=numero]').val(data.numero);
    $('[name=fecha_emision]').val(fecha_actual());
    $('[name=fecha_almacen]').val(fecha_actual());

    $('.agregarSobrante').show();
    cargar_almacenes(data.id_sede, 'id_almacen');
    // var data = 'oc_seleccionadas='+JSON.stringify([data.id_orden_compra]);
    listar_detalle_transformacion(data.id_transformacion);
}

let series_transformacion = [];

function listar_detalle_transformacion(id) {
    oc_det_seleccionadas = [];
    series_transformacion = [];

    $.ajax({
        type: 'GET',
        url: 'listarDetalleTransformacion/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            response['sobrantes'].forEach(function (element) {
                series_transformacion.push({
                    'id': 's' + element.id_sobrante,
                    'id_detalle': element.id_sobrante,
                    'series': [],
                    'tipo': 'sobrante',
                    'cantidad': element.cantidad,
                    'id_producto': element.id_producto,
                    'codigo': element.codigo,
                    'cod_prod': element.cod_prod,
                    'part_number': element.part_number,
                    'descripcion': element.descripcion,
                    'abreviatura': element.abreviatura,
                    'valor_unitario': element.valor_unitario,
                    'valor_total': element.valor_total
                });
            });
            response['transformados'].forEach(function (element) {
                series_transformacion.push({
                    'id': 't' + element.id_transformado,
                    'id_detalle': element.id_transformado,
                    'series': [],
                    'tipo': 'transformado',
                    'cantidad': element.cantidad,
                    'id_producto': element.id_producto,
                    'codigo': element.codigo,
                    'cod_prod': element.cod_prod,
                    'part_number': element.part_number,
                    'descripcion': element.descripcion,
                    'abreviatura': element.abreviatura,
                    'valor_unitario': (element.suma_materia / element.cantidad),
                    'valor_total': element.suma_materia
                });
            });
            mostrar_detalle_transformacion();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_detalle_transformacion() {
    var html = '';
    var html_ser = '';
    var i = 1;

    series_transformacion.forEach(function (element) {
        html_ser = '';
        element.series.forEach(function (serie) {
            html_ser += '<br>' + serie;
        });
        html += `<tr>
            <td>${i}</td>
            <td>${element.codigo}</td>
            <td>${element.cod_prod}</td>
            <td>${element.part_number !== null ? element.part_number : ''}</td>
            <td>${element.descripcion + ' <strong>' + html_ser + '</strong>'}</td>
            <td>${element.tipo == 'sobrante' ?
                `<input type="number" class="form-control cantidad" style="width:120px;" data-idprod="${element.id_producto}" step="0.001" 
                value="${element.cantidad}"/>` : element.cantidad}
            </td>
            <td>${element.abreviatura}</td>
            <td><input type="number" class="form-control unitario" style="width:120px;" data-id="${element.tipo == 'sobrante' ? element.id_producto : element.id}" data-tipo="${element.tipo}" step="0.001" 
                value="${element.valor_unitario}"/></td>
            <td>${formatNumber.decimal((element.cantidad * element.valor_unitario), '', -4)}</td>
            ${`<td><input type="text" class="oculto" id="series" value="${element.series}" data-partnumber="${element.part_number}"/>
                <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" 
                onClick="agrega_series_transformacion(${"'" + element.id + "'"});"></i></td>`}
            </tr>`;
        i++;
    });
    $('#detalleOrdenSeleccionadas tbody').html(html);
}

$('#detalleOrdenSeleccionadas tbody').on("change", ".unitario", function () {

    let tipo = $(this).data('tipo');
    let id = $(this).data('id');
    let unitario = parseFloat($(this).val());
    console.log('unitario: ' + unitario);

    series_transformacion.forEach(element => {
        if (tipo == 'sobrante') {
            if (element.id_producto == id) {
                element.valor_unitario = unitario;
                element.valor_total = (unitario * parseFloat(element.cantidad));
            }
        } else {
            if (element.id == id) {
                element.valor_unitario = unitario;
                element.valor_total = (unitario * parseFloat(element.cantidad));
            }
        }
    });
    console.log(series_transformacion);
    mostrar_detalle_transformacion();
});

$('#detalleOrdenSeleccionadas tbody').on("change", ".cantidad", function () {

    let idprod = $(this).data('idprod');
    let cantidad = parseFloat($(this).val());
    console.log('cantidad: ' + cantidad);

    series_transformacion.forEach(element => {
        if (element.id_producto == idprod) {
            element.cantidad = cantidad;
            element.valor_total = (element.valor_unitario * parseFloat(element.cantidad));
        }
    });
    console.log(series_transformacion);
    mostrar_detalle_transformacion();
});

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
            <td><input class="right" type="number" id="${element.id_oc_det !== null ? element.id_oc_det : 'p' + element.id_producto}cantidad" value="${element.cantidad}" 
                min="1" ${element.id_oc_det !== null ? `max="${element.cantidad}"` : ''} style="width:80px;"/></td>
            <td>${element.abreviatura}</td>
            <td class="text-right">${formatNumber.decimal(element.precio, element.simbolo + ' ', -4)}</td>
            <td class="text-right">${formatNumber.decimal((element.cantidad * element.precio), element.simbolo + ' ', -4)}</td>
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

    var data = $(this).serialize();
    var detalle = [];
    var validaCampos = '';
    var ope = $('[name=id_operacion]').val();

    if (ope == 26) {
        series_transformacion.forEach(function (element) {
            detalle.push({
                'id': element.id_detalle,
                'tipo': element.tipo,
                'id_producto': element.id_producto,
                'cantidad': element.cantidad,
                'unitario': element.valor_unitario,
                'series': element.series
            });
        });
    } else {
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
    }

    if (validaCampos.length > 0) {
        Swal.fire(validaCampos, "", "warning");
    } else {
        Swal.fire({
            title: "¿Está seguro que desea guardar ésta Guía?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00a65a", //"#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sí, Guardar"
        }).then(result => {
            if (result.isConfirmed) {
                data += '&detalle=' + JSON.stringify(detalle);
                console.log(data);
                guardar_guia_create(data);
            }
        });
    }
});

function guardar_guia_create(data) {
    $("#submit_guia").attr('disabled', 'true');
    $.ajax({
        type: 'POST',
        url: 'guardar_guia_com_oc',
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
                if (tra !== '') {
                    listarTransformaciones();
                } else {
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
                $('#modal-guia_create').modal('hide');
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

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
}

function abrirProducto(id_producto) {
    console.log('abrirProducto' + id_producto);
    localStorage.setItem("id_producto", id_producto);
    var win = window.open("/almacen/catalogos/productos/index", '_blank');
    win.focus();
}

function actualizarDetalle() {
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

function ceros_numero(numero) {
    if (numero == "numero") {
        var num = $("[name=numero]").val();
        $("[name=numero]").val(leftZero(7, num));
    } else if (numero == "serie") {
        var num = $("[name=serie]").val();
        $("[name=serie]").val(leftZero(4, num));
    }
}