let items_base = [];
let items_sobrante = [];
let items_transformado = [];
let usuarioSession = '';
let usuarioNombreSession = '';

$(function () {
    $(".edition").attr('disabled', 'true');
    $(".guardar-customizacion").hide();
    $(".edit-customizacion").show();

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
    $(".buscar-customizacion").hide();

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

    $("[name=fecha_proceso]").val(fecha_actual());
    $("[name=id_usuario]").val(usuarioSession);
    $("#nombre_registrado_por").text(usuarioNombreSession);

});

$(".cancelar").on('click', function () {

    $(".edition").attr('disabled', 'true');
    $(".guardar-customizacion").hide();
    $(".cancelar").hide();
    $(".nueva-customizacion").show();
    $(".anular-customizacion").show();
    $(".edit-customizacion").show();
    $(".buscar-customizacion").show();

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
        $(".edition").removeAttr("disabled");
        $(".guardar-customizacion").show();
        $(".cancelar").show();
        $(".nueva-customizacion").hide();
        $(".anular-customizacion").hide();
        $(".edit-customizacion").hide();
        $(".buscar-customizacion").hide();

        $("[name=modo]").val("edicion");

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

            items_base.forEach(function (element) {
                base.push({
                    'id_materia': element.id_materia,
                    'id_producto': element.id_producto,
                    'cantidad': element.cantidad,
                    'unitario': element.unitario,
                    'total': element.total,
                    // 'series': element.series,
                });
            });

            items_transformado.forEach(function (element) {
                transformado.push({
                    'id_transformado': element.id_transformado,
                    'id_producto': element.id_producto,
                    'cantidad': element.cantidad,
                    'unitario': element.unitario,
                    'total': element.total,
                    // 'series': element.series,
                });
            });

            items_sobrante.forEach(function (element) {
                sobrante.push({
                    'id_sobrante': element.id_sobrante,
                    'id_producto': element.id_producto,
                    'cantidad': element.cantidad,
                    'unitario': element.unitario,
                    'total': element.total,
                    // 'series': element.series,
                });
            });

            data += '&items_base=' + JSON.stringify(base) +
                '&items_sobrante=' + JSON.stringify(sobrante) +
                '&items_transformado=' + JSON.stringify(transformado);
            console.log(data);
            guardarCustomizacion(data);
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
            $(".buscar-customizacion").show();

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

function mostrarCustomizacion(id) {
    $.ajax({
        type: 'GET',
        url: 'mostrarCustomizacion/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            $('[name=id_customizacion]').val(response.customizacion.id_transformacion);
            $('[name=id_almacen]').val(response.customizacion.id_almacen);
            $('[name=id_usuario]').val(response.customizacion.responsable);
            $('[name=fecha_proceso]').val(moment(response.customizacion.fecha_transformacion).format("YYYY-MM-DD"));
            $('[name=observacion]').val(response.customizacion.observacion);

            $('#codigo').text(response.customizacion.codigo);
            $('#nombre_registrado_por').text(response.customizacion.registrado_por_nombre);
            // $('#serie-numero').text(response.serie !== null ? (response.serie + '-' + response.numero) : '');
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

            // listar_materias(response.id_transformacion);
            // listar_directos(response.id_transformacion);
            // listar_indirectos(response.id_transformacion);
            // listar_sobrantes(response.id_transformacion);
            // listar_transformados(response.id_transformacion);

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
