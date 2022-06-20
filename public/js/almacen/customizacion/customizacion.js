let items_base = [];
let items_sobrante = [];
let items_transformado = [];
let usuarioSession = '';
let usuarioNombreSession = '';

$(function () {
    $(".edition").attr('disabled', 'true');
    $(".guardar-customizacion").hide();
    $(".edit-customizacion").show();
    $('.imprimir-ingreso').hide();
    $('.imprimir-salida').hide();

    var id_customizacion = localStorage.getItem("id_customizacion");

    if (id_customizacion !== null && id_customizacion !== undefined) {
        mostrarCustomizacion(id_customizacion);
        localStorage.removeItem("id_customizacion");
    }
});

$(".nueva-customizacion").on('click', function () {

    $(".edition").removeAttr("disabled");
    $(".guardar-customizacion").show();
    $(".cancelar").show();
    $(".nueva-customizacion").hide();
    $(".anular-customizacion").hide();
    $(".edit-customizacion").hide();
    $(".procesar-customizacion").hide();
    $(".buscar-customizacion").hide();
    $('.imprimir-ingreso').hide();
    $('.imprimir-salida').hide();

    $("#codigo").text('');
    $(".limpiarCustomizacion").val("");
    $(".limpiarTexto").text("");

    $("#listaMateriasPrimas tbody").html("");
    $("#listaSobrantes tbody").html("");
    $("#listaProductoTransformado tbody").html("");

    items_base = [];
    items_sobrante = [];
    items_transformado = [];

    $("[name=modo]").val("edicion");
    $("[name=id_customizacion]").val("");

    var fecha = fecha_actual();

    $("[name=fecha_proceso]").val(fecha);
    $("[name=id_usuario]").val(usuarioSession);
    $("#nombre_registrado_por").text(usuarioNombreSession);

    obtenerTipoCambio(fecha);
});

$(".cancelar").on('click', function () {

    $(".edition").attr('disabled', 'true');
    $(".guardar-customizacion").hide();
    $(".cancelar").hide();
    $(".nueva-customizacion").show();
    $(".anular-customizacion").show();
    $(".edit-customizacion").show();
    $(".procesar-customizacion").show();
    $(".buscar-customizacion").show();
    $('.imprimir-ingreso').hide();
    $('.imprimir-salida').hide();

    $("#codigo").text('');
    $(".limpiarCustomizacion").val("");
    $(".limpiarTexto").text("");

    $("#listaMateriasPrimas tbody").html("");
    $("#listaSobrantes tbody").html("");
    $("#listaProductoTransformado tbody").html("");

    items_base = [];
    items_sobrante = [];
    items_transformado = [];

    $("[name=modo]").val("");
    $("[name=id_customizacion]").val("");

    $("#submit_customizacion").attr('disabled', false);
});

$(".edit-customizacion").on('click', function () {
    var id = $('[name=id_customizacion]').val();

    if (id !== '') {
        $.ajax({
            type: 'GET',
            url: 'validarEdicion/' + id,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                if (response.tipo == 'success') {

                    $(".edition").removeAttr("disabled");
                    $(".guardar-customizacion").show();
                    $(".cancelar").show();
                    $(".nueva-customizacion").hide();
                    $(".anular-customizacion").hide();
                    $(".edit-customizacion").hide();
                    $(".procesar-customizacion").hide();
                    $(".buscar-customizacion").hide();
                    $('.imprimir-ingreso').hide();
                    $('.imprimir-salida').hide();

                    $("[name=modo]").val("edicion");
                } else {
                    Lobibox.notify(response.tipo, {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });

    } else {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Debe seleccionar una customizacion.'
        });
    }
});

$("#form-customizacion").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();

    var data = $(this).serialize();

    Swal.fire({
        title: "¿Está seguro que desea guardar la customización?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {

        if (result.isConfirmed) {
            let base = [];
            let transformado = [];
            let sobrante = [];
            let falta_series = 0;
            let count_series = 0;

            items_base.forEach(function (element) {

                if (element.control_series) {
                    count_series = 0;
                    element.series.forEach(function (base) {
                        if (base.estado == 1) {
                            count_series++;
                        }
                    });
                    if (parseInt(element.cantidad) == count_series) {
                        base.push({
                            'id_materia': element.id_materia,
                            'id_producto': element.id_producto,
                            'cantidad': element.cantidad,
                            'costo_promedio': element.costo_promedio,
                            'unitario': element.unitario,
                            'total': element.total,
                            'estado': element.estado,
                            'series': element.series,
                        });
                    } else {
                        falta_series++;
                    }
                } else {
                    base.push({
                        'id_materia': element.id_materia,
                        'id_producto': element.id_producto,
                        'cantidad': element.cantidad,
                        'costo_promedio': element.costo_promedio,
                        'unitario': element.unitario,
                        'total': element.total,
                        'estado': element.estado,
                        'series': element.series,
                    });
                }
            });

            items_transformado.forEach(function (element) {

                if (element.control_series) {
                    count_series = 0;
                    element.series.forEach(function (base) {
                        if (base.estado == undefined) {
                            count_series++;
                        } else {
                            if (base.estado == 1) {
                                count_series++;
                            }
                        }
                    });
                    if (parseInt(element.cantidad) == count_series) {
                        transformado.push({
                            'id_transformado': element.id_transformado,
                            'id_producto': element.id_producto,
                            'cantidad': element.cantidad,
                            'id_moneda': element.id_moneda,
                            'unitario': element.unitario,
                            'total': element.total,
                            'estado': element.estado,
                            'series': element.series,
                        });
                    } else {
                        falta_series++;
                    }
                } else {
                    transformado.push({
                        'id_transformado': element.id_transformado,
                        'id_producto': element.id_producto,
                        'cantidad': element.cantidad,
                        'id_moneda': element.id_moneda,
                        'unitario': element.unitario,
                        'total': element.total,
                        'estado': element.estado,
                        'series': element.series,
                    });
                }
            });

            items_sobrante.forEach(function (element) {

                if (element.control_series) {
                    count_series = 0;
                    element.series.forEach(function (base) {
                        if (base.estado == undefined) {
                            count_series++;
                        } else {
                            if (base.estado == 1) {
                                count_series++;
                            }
                        }
                    });
                    if (parseInt(element.cantidad) == count_series) {
                        sobrante.push({
                            'id_sobrante': element.id_sobrante,
                            'id_producto': element.id_producto,
                            'id_moneda': element.id_moneda,
                            'cantidad': element.cantidad,
                            'unitario': element.unitario,
                            'total': element.total,
                            'estado': element.estado,
                            'series': element.series,
                        });
                    } else {
                        falta_series++;
                    }
                } else {
                    sobrante.push({
                        'id_sobrante': element.id_sobrante,
                        'id_producto': element.id_producto,
                        'id_moneda': element.id_moneda,
                        'cantidad': element.cantidad,
                        'unitario': element.unitario,
                        'total': element.total,
                        'estado': element.estado,
                        'series': element.series,
                    });
                }
            });

            if (falta_series > 0) {
                Swal.fire("Aún le falta agregar series a " + falta_series + " producto(s).", "", "warning");
            } else {
                data += '&items_base=' + JSON.stringify(base) +
                    '&items_sobrante=' + JSON.stringify(sobrante) +
                    '&items_transformado=' + JSON.stringify(transformado);
                console.log(data);
                guardarCustomizacion(data);
            }
        }
    });
});

function guardarCustomizacion(data) {
    $("#submit_customizacion").attr('disabled', 'true');
    var id = $('[name=id_customizacion]').val();
    var url = '';

    if (id !== '') {
        url = 'actualizarCustomizacion';
    } else {
        url = 'guardarCustomizacion';
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            Lobibox.notify(response.tipo, {
                title: false,
                size: "mini",
                rounded: true,
                sound: false,
                delayIndicator: false,
                msg: response.mensaje
            });

            $(".edition").attr('disabled', 'true');
            $(".guardar-customizacion").hide();
            $(".cancelar").hide();
            $(".nueva-customizacion").show();
            $(".anular-customizacion").show();
            $(".edit-customizacion").show();
            $(".procesar-customizacion").show();
            $(".buscar-customizacion").show();
            $('.imprimir-ingreso').hide();
            $('.imprimir-salida').hide();

            $("[name=modo]").val("");
            $("[name=id_customizacion]").val(response.customizacion.id_transformacion);
            $("#codigo").text(response.customizacion.codigo);

            $("#submit_customizacion").attr('disabled', false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
//obs: si se cambia el almacen debe borrarse los items
function mostrarCustomizacion(id) {
    $.ajax({
        type: 'GET',
        url: 'mostrarCustomizacion/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $('[name=id_customizacion]').val(response.customizacion.id_transformacion);
            $('[name=id_almacen]').val(response.customizacion.id_almacen);
            $('[name=id_moneda]').val(response.customizacion.id_moneda);
            $('[name=tipo_cambio]').val(response.customizacion.tipo_cambio);
            $('[name=id_usuario]').val(response.customizacion.responsable);
            $('[name=fecha_proceso]').val(moment(response.customizacion.fecha_transformacion).format("YYYY-MM-DD"));
            $('[name=observacion]').val(response.customizacion.observacion);
            $('[name=id_ingreso]').val(response.id_ingreso);
            $('[name=id_salida]').val(response.id_salida);

            $('#codigo').text(response.customizacion.codigo);
            $('#nombre_registrado_por').text(response.customizacion.registrado_por_nombre);
            $('#fecha_registro').text(response.customizacion.fecha_registro);

            if (response.id_ingreso == 0) {
                $('.imprimir-ingreso').hide();
            } else {
                $('.imprimir-ingreso').show();
            }

            if (response.id_salida == 0) {
                $('.imprimir-salida').hide();
            } else {
                $('.imprimir-salida').show();
            }

            items_base = response.bases;
            mostrarProductosBase();
            items_transformado = response.transformados;
            mostrarProductoTransformado();
            items_sobrante = response.sobrantes;
            mostrarProductoSobrante();
            $(".edition").attr('disabled', 'true');
            // $('[name=id_estado]').val(response.estado);
            // $('#estado_doc').text(response.estado_doc);
            // $('#estado_doc').removeClass();
            // $('#estado_doc').addClass("label label-" + response.bootstrap_color);

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularCustomizacion() {

    Swal.fire({
        title: "¿Está seguro que desea anular la customización?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Anular"
    }).then(result => {

        if (result.isConfirmed) {

            let ids = $("[name=id_customizacion]").val();
            $.ajax({
                type: 'GET',
                url: 'anularCustomizacion/' + ids,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    Lobibox.notify(response.tipo, {
                        title: false,
                        size: "mini",
                        rounded: true,
                        sound: false,
                        delayIndicator: false,
                        msg: response.mensaje
                    });

                    if (response.tipo == 'success') {
                        $("#codigo").text('');
                        $(".limpiarCustomizacion").val("");
                        $(".limpiarTexto").text("");
                        $('.imprimir-ingreso').hide();
                        $('.imprimir-salida').hide();

                        $("#listaMateriasPrimas tbody").html("");
                        $("#listaSobrantes tbody").html("");
                        $("#listaProductoTransformado tbody").html("");

                        items_base = [];
                        items_sobrante = [];
                        items_transformado = [];

                        $("[name=modo]").val("");
                        $("[name=id_customizacion]").val("");
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
}

$("[name=fecha_proceso]").on('change', function () {
    var fecha = $(this).val();
    obtenerTipoCambio(fecha);
});

$("[name=id_moneda]").on('change', function () {
    console.log($('[name=id_moneda]').val());
    mostrarProductosBase();
    mostrarProductoSobrante();
    mostrarProductoTransformado();
});

function actualizarCostosBase() {
    let base = [];
    let id_almacen = $('[name=id_almacen]').val();
    let id_moneda = $('[name=id_moneda]').val();
    let tipo_cambio = $('[name=tipo_cambio]').val();
    let msj = 0;

    if (id_moneda == '') {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Debe seleccionar una moneda.'
        });
        msj++;
    }
    if (tipo_cambio == '' || tipo_cambio == 0) {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Debe ingresar un tipo de cambio válido.'
        });
        msj++;
    }

    if (msj == 0) {
        items_base.forEach(function (element) {
            base.push({
                'id_almacen': id_almacen,
                'id_producto': element.id_producto,
            });
        });

        var data = 'items_base=' + JSON.stringify(base);

        $.ajax({
            type: 'POST',
            url: 'actualizarCostosBase',
            data: data,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                let costo_promedio = 0;
                response.items_base.forEach(res => {
                    items_base.forEach(function (element) {
                        costo_promedio = formatDecimalDigitos(parseFloat(res.costo_promedio), 4);

                        if (res.id_producto == element.id_producto) {
                            element.costo_promedio = costo_promedio;

                            if (id_moneda == element.id_moneda) {
                                element.unitario = costo_promedio;
                                element.total = element.cantidad * costo_promedio;
                            } else {
                                if (id_moneda == 1) {
                                    element.unitario = costo_promedio * parseFloat(tipo_cambio);
                                } else {
                                    element.unitario = costo_promedio / parseFloat(tipo_cambio);
                                }
                                element.total = element.cantidad * element.unitario;
                            }
                        }
                    });
                });
                mostrarProductosBase();
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}

function procesarCustomizacion() {

    let ids = $("[name=id_customizacion]").val();

    if (ids == '') {
        Lobibox.notify("warning", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Debe seleccionar una customización.'
        });
    } else {
        Swal.fire({
            title: "¿Está seguro que desea procesar la customización?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00a65a",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sí, Procesar"
        }).then(result => {

            if (result.isConfirmed) {

                $.ajax({
                    type: 'GET',
                    url: 'procesarCustomizacion/' + ids,
                    dataType: 'JSON',
                    success: function (response) {
                        console.log(response);
                        Lobibox.notify(response.tipo, {
                            title: false,
                            size: "mini",
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: response.mensaje
                        });

                        if (response.tipo == 'success') {
                            $('.imprimir-ingreso').show();
                            $('.imprimir-salida').show();

                            $('[name=id_ingreso]').val(response.id_ingreso);
                            $('[name=id_salida]').val(response.id_salida);
                            imprimirIngreso();
                            imprimirSalida();
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        });
    }
}

function obtenerTipoCambio(fecha) {
    $.ajax({
        type: 'GET',
        url: 'obtenerTipoCambio/' + fecha + '/' + 2,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $("[name=tipo_cambio]").val(response.venta);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function imprimirIngreso() {
    var id = $('[name=id_ingreso]').val();

    if (id !== null && id !== '') {
        window.open("imprimir_ingreso/" + id);
    } else {
        Swal.fire({
            title: "No existe un ingreso activo relacionado!",
            icon: "error",
        });
    }
}

function imprimirSalida() {
    var id = $('[name=id_salida]').val();

    if (id !== null && id !== '') {
        window.open("imprimir_salida/" + id);
    } else {
        Swal.fire({
            title: "No existe una salida activa relacionada!",
            icon: "error",
        });
    }
}