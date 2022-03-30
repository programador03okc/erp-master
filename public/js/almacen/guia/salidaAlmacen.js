function abrirSalidaAlmacen(data) {
    $('#modal-salidaAlmacen').modal({
        show: true
    });
    console.log(data);
    console.log(moment(data.fecha_emision).format("YYYY-MM-DD"));

    $('#codigo_salida').text(data.codigo);
    $('#guia_ven').text(data.serie + '-' + data.numero);

    $('[name=id_guia_ven]').val(data.id_guia_ven);
    $('[name=id_mov_alm]').val(data.id_mov_alm);
    $('[name=salida_serie]').val(data.serie);
    $('[name=salida_numero]').val(data.numero);
    $('[name=salida_punto_partida]').val(data.punto_partida);
    $('[name=salida_punto_llegada]').val(data.punto_llegada);
    $('[name=salida_fecha_emision]').val(moment(data.fecha_emision_guia).format("YYYY-MM-DD"));
    $('[name=salida_fecha_almacen]').val(moment(data.fecha_emision).format("YYYY-MM-DD"));

    $('#almacen_descripcion').val(data.almacen_descripcion);
    $('#cliente_razon_social').val(data.razon_social);
    $('#operacion_descripcion').val(data.operacion);
    $('#orden_despacho').text(data.codigo_od);
    $('#requerimientos').text(data.codigo_requerimiento);
    $('#responsable_nombre').text(data.nombre_corto);
    $('#fecha_registro').text(data.fecha_registro);

    $('[name=id_motivo_cambio]').val('');
    $('[name=observacion]').val('');

    $("#submit_salidaAlmacen").removeAttr("disabled");

    listar_detalle_movimiento(data.id_guia_ven);
}

function listar_detalle_movimiento(id_guia_ven) {
    console.log('id_guia_ven', id_guia_ven);
    $.ajax({
        type: 'GET',
        url: 'detalleMovimientoSalida/' + id_guia_ven,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            var html = '';
            var html_ser = '';
            var i = 1;
            guia_detalle = response;

            response.forEach(element => {
                html_ser = '';
                element.series.forEach(function (item) {
                    if (html_ser == '') {
                        html_ser += '<br>' + item.serie;
                    } else {
                        html_ser += ',  ' + item.serie;
                    }
                });
                html += `<tr>
                <td>${i}</td>
                <td>${element.codigo}</td>
                <td>${element.part_number !== null ? element.part_number : ''}</td>
                <td>${element.descripcion + '<strong>' + html_ser + '</strong>'}</td>
                <td>${element.cantidad}</td>
                <td>${element.abreviatura}</td>
                <td>${html_ser == '' ? ''
                        : `<i class="fas fa-edit icon-tabla boton blue" data-toggle="tooltip" data-placement="bottom" 
                    title="Editar Series" onClick="open_guia_series_edit(${element.id_guia_ven_det});"></i>
                    <i class="fas fa-file-excel icon-tabla boton green" data-toggle="tooltip" data-placement="bottom" 
                    title="Exportar a Excel Series" onClick="exportarSeries(${element.id_guia_ven_det});"></i>`}
                </td>
                </tr>`;
                i++;
            });
            $('#detalleMovimiento tbody').html(html);
            // <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" 
            // title="Agregar Series" onClick="agrega_series_guia(${element.id_guia_com_det},${element.cantidad},${element.id_producto},${element.id_almacen});"></i>
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$("#form-salidaAlmacen").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();

    Swal.fire({
        title: "¿Está seguro que desea actualizar ésta Salida?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Si, Actualizar"
    }).then(result => {
        if (result.isConfirmed) {

            var data = $(this).serialize();
            console.log(data);
            $("#submit_salidaAlmacen").attr('disabled', 'true');

            $.ajax({
                type: 'POST',
                url: 'actualizarSalida',
                data: data,
                dataType: 'JSON',
                success: function (response) {
                    console.log(response);
                    if (response == 'ok') {
                        Lobibox.notify("success", {
                            title: false,
                            size: "mini",
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: 'Salida Almacén actualizada con éxito.'
                        });
                        $("#despachosEntregados").DataTable().ajax.reload(null, false);
                        $('#modal-salidaAlmacen').modal('hide');
                    } else {
                        Swal.fire({
                            title: response,
                            icon: "error",
                        }).then(result => {
                            $("#submit_salidaAlmacen").removeAttr("disabled");
                        });
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    });
});

function exportarSeries(id_guia_ven_det) {
    window.location.href = 'seriesExcel/' + id_guia_ven_det;
}

function salida_ceros_numero(numero) {
    if (numero == "numero") {
        var num = $("[name=salida_numero]").val();
        $("[name=salida_numero]").val(leftZero(7, num));
    } else if (numero == "serie") {
        var num = $("[name=salida_serie]").val();
        $("[name=salida_serie]").val(leftZero(4, num));
    }
}