let items = [];
let salidas = [];
let ingresos = [];
let incidencias = [];
let usuarioSession = '';
let usuarioNombreSession = '';

$(function () {
    $(".edition").attr('disabled', 'true');
    $(".guardar-devolucion").hide();
    $(".edit-devolucion").show();
    $('.imprimir-ingreso').hide();
    $('.imprimir-salida').hide();

    var id_devolucion = localStorage.getItem("id_devolucion");

    if (id_devolucion !== null && id_devolucion !== undefined) {
        mostrarDevolucion(id_devolucion);
        localStorage.removeItem("id_devolucion");
    }
});

$("[name=id_tipo]").on('change', function () {
    console.log($(this).val());
    if ($(this).val() == 1) {
        $('.divSalida').show();
        $('.divIngreso').hide();
    }
    else if ($(this).val() == 2) {
        $('.divSalida').hide();
        $('.divIngreso').show();
    }
});

$(".nueva-devolucion").on('click', function () {

    $(".edition").removeAttr("disabled");
    $(".guardar-devolucion").show();
    $(".cancelar").show();
    $(".nueva-devolucion").hide();
    $(".anular-devolucion").hide();
    $(".edit-devolucion").hide();
    $(".procesar-devolucion").hide();
    $(".buscar-devolucion").hide();
    $('.imprimir-ingreso').hide();
    $('.imprimir-salida').hide();

    $("#codigo").text('');
    $(".limpiardevolucion").val("");
    $(".limpiarTexto").text("");

    $("#listaProductosDevolucion tbody").html("");
    $("#listaSalidasDevolucion tbody").html("");
    $("#listaIngresosDevolucion tbody").html("");
    $("#listaIncidenciasDevolucion tbody").html("");

    items = [];
    incidencias = [];
    salidas = [];
<<<<<<< HEAD
=======
    ingresos = [];
>>>>>>> develop

    $("[name=modo]").val("edicion");
    $("[name=id_devolucion]").val("");
    $("[name=id_tipo]").val(1);

    $("[name=id_usuario]").val(usuarioSession);
    $("#nombre_registrado_por").text(usuarioNombreSession);

});

$(".cancelar").on('click', function () {

    $(".edition").attr('disabled', 'true');
    $(".guardar-devolucion").hide();
    $(".cancelar").hide();
    $(".nueva-devolucion").show();
    $(".anular-devolucion").show();
    $(".edit-devolucion").show();
    $(".procesar-devolucion").show();
    $(".buscar-devolucion").show();
    $('.imprimir-ingreso').hide();
    $('.imprimir-salida').hide();

    $("#codigo").text('');
    $(".limpiardevolucion").val("");
    $(".limpiarTexto").text("");

    $("#listaProductosDevolucion tbody").html("");
    $("#listaSalidasDevolucion tbody").html("");
    $("#listaIngresosDevolucion tbody").html("");
    $("#listaIncidenciasDevolucion tbody").html("");

    items = [];
    incidencias = [];
    salidas = [];
<<<<<<< HEAD
=======
    ingresos = [];
>>>>>>> develop

    $("[name=modo]").val("");
    $("[name=id_devolucion]").val("");

    $("#submit_devolucion").attr('disabled', false);
});

$(".edit-devolucion").on('click', function () {
    var id = $('[name=id_devolucion]').val();

    if (id !== '') {
        $.ajax({
            type: 'GET',
            url: 'validarEdicion/' + id,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                if (response.tipo == 'success') {

                    $(".edition").removeAttr("disabled");
                    $(".guardar-devolucion").show();
                    $(".cancelar").show();
                    $(".nueva-devolucion").hide();
                    $(".anular-devolucion").hide();
                    $(".edit-devolucion").hide();
                    $(".procesar-devolucion").hide();
                    $(".buscar-devolucion").hide();

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
            msg: 'Debe seleccionar una devolucion.'
        });
    }
});

$("#form-devolucion").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();

    var data = $(this).serialize();

    Swal.fire({
        title: "¿Está seguro que desea guardar la devolución?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {

        if (result.isConfirmed) {
            let detalle = [];
            items.forEach(function (element) {
                detalle.push({
                    'id_detalle': element.id_detalle,
                    'id_salida_detalle': (element.id_salida_detalle !== undefined ? element.id_salida_detalle : null),
                    'id_ingreso_detalle': (element.id_ingreso_detalle !== undefined ? element.id_ingreso_detalle : null),
                    'id_producto': element.id_producto,
                    'cantidad': element.cantidad,
                    'estado': element.estado,
                });
            });
            let salidas_venta = [];
            salidas.forEach(function (element) {
                salidas_venta.push({
                    'id': element.id,
                    'id_devolucion': element.id_devolucion,
                    'id_salida': element.id_salida,
                    'estado': element.estado,
                });
            });
<<<<<<< HEAD
            data += '&items=' + JSON.stringify(detalle) +
                '&incidencias=' + JSON.stringify(incidencias) +
                '&salidas=' + JSON.stringify(salidas_venta);
=======
            let ingresos_compra = [];
            ingresos.forEach(function (element) {
                ingresos_compra.push({
                    'id': element.id,
                    'id_devolucion': element.id_devolucion,
                    'id_ingreso': element.id_ingreso,
                    'estado': element.estado,
                });
            });
            data += '&items=' + JSON.stringify(detalle) +
                '&incidencias=' + JSON.stringify(incidencias) +
                '&salidas=' + JSON.stringify(salidas_venta) +
                '&ingresos=' + JSON.stringify(ingresos_compra);
>>>>>>> develop
            console.log(data);
            guardarDevolucion(data);
        }
    });
});

let origen;

function abrirProductos() {
    origen = 'devolucion';
    $("#modal-productoCatalogo").modal({
        show: true
    });
    clearDataTable();
    listarProductosCatalogo();
}

function guardarDevolucion(data) {
    $("#submit_devolucion").attr('disabled', 'true');
    var id = $('[name=id_devolucion]').val();
    var url = '';

    if (id !== '') {
        url = 'actualizarDevolucion';
    } else {
        url = 'guardarDevolucion';
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
            $(".guardar-devolucion").hide();
            $(".cancelar").hide();
            $(".nueva-devolucion").show();
            $(".anular-devolucion").show();
            $(".edit-devolucion").show();
            $(".procesar-devolucion").show();
            $(".buscar-devolucion").show();
            $('.imprimir-ingreso').hide();
            $('.imprimir-salida').hide();

            $("[name=modo]").val("");
            $("[name=id_devolucion]").val(response.devolucion.id_devolucion);
            $('#codigo').text(response.devolucion.codigo);
            $('#estado').text(response.devolucion.estado_descripcion);
            $('#estado').removeClass();
            $('#estado').addClass('label label-' + response.devolucion.bootstrap_color);
            $('#nombre_registrado_por').text(response.devolucion.nombre_corto);
            $('#fecha_registro').text(response.devolucion.fecha_registro);

            mostrarDevolucion(response.devolucion.id_devolucion)

            $("#submit_devolucion").attr('disabled', false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
//obs: si se cambia el almacen debe borrarse los items
function mostrarDevolucion(id) {

    $("#listaProductosDevolucion tbody").html("");
    $("#listaSalidasDevolucion tbody").html("");
    $("#listaIncidenciasDevolucion tbody").html("");

    items = [];
    incidencias = [];
    salidas = [];

    $.ajax({
        type: 'GET',
        url: 'mostrarDevolucion/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $('[name=id_devolucion]').val(response.devolucion.id_devolucion);
            $('[name=id_almacen]').val(response.devolucion.id_almacen);
            $('[name=id_usuario]').val(response.devolucion.registrado_por);
            $('[name=observacion]').val(response.devolucion.observacion);
            $('[name=id_proveedor]').val(response.devolucion.id_proveedor);
            $('[name=id_cliente]').val(response.devolucion.id_cliente);
            $('[name=id_contribuyente]').val(response.devolucion.id_contribuyente);
<<<<<<< HEAD
            $('[name=contribuyente]').val(response.devolucion.proveedor_razon_social);
=======
            // $('[name=contribuyente]').val(response.devolucion.proveedor_razon_social);
            $('[name=contribuyente]').val(response.devolucion.proveedor_razon_social !== null ?
                response.devolucion.proveedor_razon_social : response.devolucion.cliente_razon_social);
>>>>>>> develop
            $('[name=id_tipo]').val(response.devolucion.id_tipo);

            $('#codigo').text(response.devolucion.codigo);
            $('#estado').text(response.devolucion.estado_descripcion);
            $('#estado').removeClass();
            $('#estado').addClass('label label-' + response.devolucion.bootstrap_color);
            $('#nombre_registrado_por').text(response.devolucion.nombre_corto);
            $('#fecha_registro').text(response.devolucion.fecha_registro);
            $('#nombre_revisado_por').text(response.devolucion.nombre_revisado);
            $('#comentario_revision').text(response.devolucion.comentario_revision);

            items = response.detalle;
            salidas = response.salidas;
            ingresos = response.ingresos;
            incidencias = response.incidencias;

            mostrarProductos();
            mostrarSalidas();
            mostrarIngresos();
            mostrarIncidencias();

            $(".edition").attr('disabled', 'true');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularDevolucion() {

    Swal.fire({
        title: "¿Está seguro que desea anular la devolución?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Anular"
    }).then(result => {

        if (result.isConfirmed) {

            let ids = $("[name=id_devolucion]").val();
            $.ajax({
                type: 'GET',
                url: 'anularDevolucion/' + ids,
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
                        $(".limpiardevolucion").val("");
                        $(".limpiarTexto").text("");

                        $("#listaProductosDevolucion tbody").html("");
                        $("#listaSalidasDevolucion tbody").html("");
                        $("#listaIncidenciasDevolucion tbody").html("");

                        $("[name=modo]").val("");
                        $("[name=id_devolucion]").val("");
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
