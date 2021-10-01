function open_detalle_movimiento(data) {
    $('#modal-movAlmDetalle').modal({
        show: true
    });
    console.log(data);
    $('#cabecera').text(data.codigo);
    $('[name=id_guia_com_detalle]').val(data.id_guia_com);
    $('[name=id_mov_alm]').val(data.id_mov_alm);
    $('#guia_com').text(data.serie + '-' + data.numero);
    $('[name=ingreso_serie]').val(data.serie);
    $('[name=ingreso_numero]').val(data.numero);
    $('#almacen_descripcion').val(data.almacen_descripcion);
    $('#prov_razon_social').val(data.razon_social);
    $('#operacion_descripcion').val(data.operacion_descripcion);
    $('[name=fecha_emision]').val(moment(data.fecha_emision_guia).format("YYYY-MM-DD"));
    $('[name=fecha_almacen]').val(moment(data.fecha_emision).format("YYYY-MM-DD"));
    $('#ordenes_compra').val(data.ordenes_compra);
    $('#responsable_nombre').text(data.nombre_corto);
    $('#requerimientos').val(data.requerimientos);
    $('#ordenes_soft_link').text(data.ordenes_soft_link);

    listar_detalle_movimiento(data.id_guia_com);
}

function abrirIngreso() {
    var id = encode5t($('[name=id_mov_alm]').val());
    window.open("imprimir_ingreso/" + id);
}
let guia_detalle = [];

function listar_detalle_movimiento(id_guia_com_detalle) {
    console.log('id_guia_com_detalle', id_guia_com_detalle);
    $.ajax({
        type: 'GET',
        url: 'detalleMovimiento/' + id_guia_com_detalle,
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
                <td>${element.serie !== null ? (element.serie + '-' + element.numero) : ''}</td>
                <td>${element.codigo_orden !== null ? element.codigo_orden :
                        (element.codigo_transfor !== null ? element.codigo_transfor :
                            (element.codigo_trans !== null ? element.codigo_trans : ''))}</td>
                <td>${element.codigo_req !== null ? element.codigo_req : ''}</td>
                <td><strong>${element.sede_req !== null ? element.sede_req : ''}</strong></td>
                <td>${html_ser == '' ? ''
                        : `<i class="fas fa-edit icon-tabla boton blue" data-toggle="tooltip" data-placement="bottom" 
                    title="Editar Series" onClick="open_guia_series_edit(${element.id_guia_com_det});"></i>
                    <i class="fas fa-file-excel icon-tabla boton green" data-toggle="tooltip" data-placement="bottom" 
                    title="Exportar a Excel Series" onClick="exportarSeries(${element.id_guia_com_det});"></i>`}
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

function exportarSeries(id_guia_com_det) {
    window.location.href = 'seriesExcel/' + id_guia_com_det;
}