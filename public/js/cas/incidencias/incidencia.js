let listaSeriesProductos = [];
let ubigeoOrigen = '';

$(function () {
    $(".edition").attr('disabled', 'true');
    $(".guardar-incidencia").hide();
    $(".edit-incidencia").show();

    var id_incidencia = localStorage.getItem("id_incidencia");

    if (id_incidencia !== null && id_incidencia !== undefined) {
        mostrarIncidencia(id_incidencia);
        localStorage.removeItem("id_incidencia");
    }
});

function mostrarIncidencia(id) {
    $.ajax({
        type: 'GET',
        url: 'mostrarIncidencia/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            $("[name=id_incidencia]").val(response.incidencia.id_incidencia);
            $("#codigo_ficha").text(response.incidencia.codigo);
            $("[name=factura]").val(response.incidencia.factura);
            $("[name=id_responsable]").val(response.incidencia.id_responsable);
            $("[name=id_tipo_falla]").val(response.incidencia.id_tipo_falla);
            $("[name=id_tipo_servicio]").val(response.incidencia.id_tipo_servicio);
            $("[name=id_modo]").val(response.incidencia.id_modo);
            $("[name=id_tipo_garantia]").val(response.incidencia.id_tipo_garantia);
            $("[name=id_atiende]").val(response.incidencia.id_atiende);
            $("[name=numero_caso]").val(response.incidencia.numero_caso);
            $("[name=sede_cliente]").val(response.incidencia.sede_cliente);
            $("[name=usuario_final]").val(response.incidencia.usuario_final);
            $("[name=importe_gastado]").val(response.incidencia.importe_gastado);
            $("[name=comentarios_cierre]").val(response.incidencia.comentarios_cierre);
            $("[name=parte_reemplazada]").val(response.incidencia.parte_reemplazada);

            $("[name=id_mov_alm]").val(response.incidencia.id_salida);
            $("[name=id_guia_ven]").val(response.incidencia.id_guia_ven);
            $("[name=id_requerimiento]").val(response.incidencia.id_requerimiento);
            $("[name=id_contribuyente]").val(response.incidencia.id_contribuyente);
            $("[name=id_empresa]").val(response.incidencia.id_empresa);
            $("[name=id_entidad]").val(response.incidencia.id_entidad);
            $("[name=id_contacto]").val(response.incidencia.id_contacto);

            $("[name=falla_reportada]").val(response.incidencia.falla_reportada);
            $("[name=fecha_reporte]").val(response.incidencia.fecha_reporte);
            $("[name=id_division]").val(response.incidencia.id_division);
            $("[name=id_medio]").val(response.incidencia.id_medio);
            $("[name=conformidad]").val(response.incidencia.conformidad);

            $('[name=equipo_operativo]').prop('checked', (response.incidencia.equipo_operativo ? true : false));
            // $(".guia_venta").text(response.incidencia.serie + '-' + response.incidencia.numero);
            $("[name=cliente_razon_social]").val(response.incidencia.razon_social);
            $(".codigo_oportunidad").text(response.incidencia.codigo_oportunidad);
            $("[name=nro_orden]").val(response.incidencia.nro_orden);
            $(".fecha_registro").text(response.incidencia.fecha_registro);

            $("[name=nombre_contacto]").val(response.incidencia.nombre_contacto);
            $("[name=cargo_contacto]").val(response.incidencia.cargo_contacto);
            $("[name=telefono_contacto]").val(response.incidencia.telefono_contacto);
            $("[name=direccion_contacto]").val(response.incidencia.direccion_contacto);
            $("[name=id_ubigeo_contacto]").val(response.incidencia.id_ubigeo_contacto);
            $("[name=ubigeo_contacto]").val(response.incidencia.ubigeo_descripcion);
            $(".horario_contacto").text(response.incidencia.horario);
            $(".email_contacto").text(response.incidencia.email);

            $("[name=serie]").val(response.incidencia.serie);
            $("[name=producto]").val(response.incidencia.producto);
            $("[name=marca]").val(response.incidencia.marca);
            $("[name=modelo]").val(response.incidencia.modelo);
            $("[name=id_tipo]").val(response.incidencia.id_tipo);
            // response.productos.forEach(function (element) {

            //     listaSeriesProductos.push({
            //         "id_incidencia_producto": element.id_incidencia_producto,
            //         "id_incidencia": element.id_incidencia,
            //         "id_prod_serie": element.id_prod_serie ?? null,
            //         "serie": element.serie,
            //         "id_producto": element.id_producto ?? null,
            //         "descripcion": element.producto !== null ? element.producto : '',
            //         "id_tipo": element.id_tipo,
            //         "marca": element.marca,
            //         "modelo": element.modelo,
            //     });
            // });
            // mostrarListaSeriesProductos();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function openContacto() {
    var id_requerimiento = $("[name=id_requerimiento]").val();
    var id_contribuyente = $("[name=id_contribuyente]").val();
    var id_entidad = $("[name=id_entidad]").val();
    var id_contacto = $("[name=id_contacto]").val();
    var codigo = $("[name=codigo_oportunidad]").val() + ' - ' + $(".codigo_requerimiento").text();

    if (id_contribuyente !== null) {
        openDespachoContactoIncidencia(id_requerimiento, id_contribuyente, id_entidad, id_contacto, codigo);
    } else {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'No existe un contribuyente.'
        });
    }
}

$(".nueva-incidencia").on('click', function () {

    $(".edition").removeAttr("disabled");
    $(".guardar-incidencia").show();
    $(".cancelar").show();
    $(".nueva-incidencia").hide();
    $(".anular-incidencia").hide();
    $(".edit-incidencia").hide();
    $(".buscar-incidencia").hide();

    $("#codigo_ficha").text('');
    $(".limpiarIncidencia").val("");
    $(".limpiarTexto").text("");
    $("#seriesProductos tbody").html("");

    $("[name=modo]").val("edicion");
    $("[name=id_incidencia]").val("");
    $("[name=fecha_reporte]").val(fecha_actual());


});

$(".cancelar").on('click', function () {

    $(".edition").attr('disabled', 'true');
    $(".guardar-incidencia").hide();
    $(".cancelar").hide();
    $(".nueva-incidencia").show();
    $(".anular-incidencia").show();
    $(".edit-incidencia").show();
    $(".buscar-incidencia").show();

    $("[name=modo]").val("");
    $("#codigo_ficha").text('');

    $("#submit_incidencia").attr('disabled', false);

    $(".limpiarIncidencia").val("");
    $(".limpiarTexto").text("");
    $("[name=id_incidencia]").val("");
    $("#seriesProductos tbody").html("");

});

$(".edit-incidencia").on('click', function () {
    var id = $('[name=id_incidencia]').val();

    if (id !== '') {
        $(".edition").removeAttr("disabled");
        $(".guardar-incidencia").show();
        $(".cancelar").show();
        $(".nueva-incidencia").hide();
        $(".anular-incidencia").hide();
        $(".edit-incidencia").hide();
        $(".buscar-incidencia").hide();

        $("[name=modo]").val("edicion");

    } else {
        Lobibox.notify('warning', {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Debe seleccionar una incidencia.'
        });
    }
});

$("#form-incidencia").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();

    var data = $(this).serialize();

    Swal.fire({
        title: "¿Está seguro que desea guardar la incidencia?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {

        if (result.isConfirmed) {
            let detalle = [];

            listaSeriesProductos.forEach(function (element) {
                detalle.push({
                    'id_incidencia_producto': element.id_incidencia_producto,
                    'id_incidencia': element.id_incidencia,
                    'id_producto': element.id_producto,
                    'id_prod_serie': element.id_prod_serie,
                    'serie': element.serie,
                    'producto': element.descripcion,
                    'marca': element.marca,
                    'modelo': element.modelo,
                    'id_tipo': element.id_tipo,
                });
            })
            data += '&detalle=' + JSON.stringify(detalle);
            console.log(data);
            guardarIncidencia(data);
        }
    });
});

function guardarIncidencia(data) {
    $("#submit_incidencia").attr('disabled', 'true');
    var id = $('[name=id_incidencia]').val();
    var url = '';

    if (id !== '') {
        url = 'actualizarIncidencia';
    } else {
        url = 'guardarIncidencia';
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
            $(".guardar-incidencia").hide();
            $(".cancelar").hide();
            $(".nueva-incidencia").show();
            $(".anular-incidencia").show();
            $(".edit-incidencia").show();
            $(".buscar-incidencia").show();

            $("[name=modo]").val("");
            $("[name=id_incidencia]").val(response.incidencia.id_incidencia);
            $("#codigo_ficha").text(response.incidencia.codigo);

            $("#submit_incidencia").attr('disabled', false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// function mostrarListaSeriesProductos() {
//     var html = '';
//     $('#seriesProductos tbody').html(html);
//     listaSeriesProductos.forEach(function (element) {
//         html += `<tr>
//         <td style="text-align:center">${element.serie}</td>
//         <td style="text-align:center">${element.descripcion ?? ''}</td>
//         <td style="text-align:center">${element.marca ?? ''}</td>
//         <td style="text-align:center">${element.modelo ?? ''}</td>
//         </tr>`;
//     });
//     $('#seriesProductos tbody').html(html);
// }

function anularIncidencia() {

    Swal.fire({
        title: "¿Está seguro que desea anular ésta incidencia?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",//"#00a65a"
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Anular"
    }).then(result => {

        if (result.isConfirmed) {

            var id = $('[name=id_incidencia]').val();
            $.ajax({
                type: 'GET',
                url: 'anularIncidencia/' + id,
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
                    $(".edition").removeAttr("disabled");
                    $("#codigo_ficha").text('');

                    $(".limpiarIncidencia").val("");
                    $(".limpiarTexto").text("");
                    $("[name=id_incidencia]").val("");
                    $("#seriesProductos tbody").html("");
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
}

function imprimirIncidencia() {
    var id_incidencia = $('[name=id_incidencia]').val();
    if (id_incidencia !== null && id_incidencia !== '') {
        window.open("imprimirIncidencia/" + id_incidencia);
    }
}

function imprimirFichaAtencionBlanco() {
    var id_incidencia = $('[name=id_incidencia]').val();
    if (id_incidencia !== null && id_incidencia !== '') {
        window.open("imprimirFichaAtencionBlanco/" + id_incidencia);
    }
}

function abrirUbigeoModal(origen) {
    ubigeoOrigen = origen;
    console.log(ubigeoOrigen);
    ubigeoModal();
}