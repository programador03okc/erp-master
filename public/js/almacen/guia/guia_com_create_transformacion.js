
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
    $('#detalleOrdenSeleccionadas tbody').html('');
    cargar_almacenes(data.id_sede, 'id_almacen');

    listar_detalle_transformacion(data.id_transformacion);
}

let series_transformacion = [];

function listar_detalle_transformacion(id) {
    oc_det_seleccionadas = [];
    series_transformacion = [];
    $('#detalleOrdenSeleccionadas tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listarDetalleTransformacion/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            response['sobrantes'].forEach(function (element) {
                series_transformacion.push({
                    'id': element.id_sobrante,//'s' + 
                    'id_detalle': element.id_sobrante,
                    'series': [],
                    'control_series': element.series,
                    'tipo': 'sobrante',
                    'cantidad': element.cantidad,
                    'id_producto': null,//element.id_producto,
                    'codigo': element.codigo,
                    'cod_prod': null,//element.cod_prod,
                    'part_number': element.part_number_sobrante,
                    'descripcion': element.descripcion_sobrante,
                    'abreviatura': null,//element.abreviatura,
                    'valor_unitario': element.valor_unitario,
                    'valor_total': element.valor_total
                });
            });
            response['transformados'].forEach(function (element) {
                series_transformacion.push({
                    'id': 't' + element.id_transformado,
                    'id_detalle': element.id_transformado,
                    'series': [],
                    'control_series': element.series,
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
            <td>${element.cod_prod !== null ? (element.cod_prod == '' ? '<label>(por crear)</label>' : element.cod_prod) : '<label class="subtitulo_red">(sin mapear)</label>'}</td>
            <td>${element.part_number !== null ? element.part_number : ''}</td>
            <td>${element.descripcion + ' <strong>' + html_ser + '</strong>'}</td>
            <td>${element.tipo == 'sobrante' ?
                `<input type="number" class="form-control cantidad" style="width:120px;" data-idprod="${element.id_producto}" step="0.001" 
                value="${element.cantidad}"/>` : element.cantidad}
            </td>
            <td>${element.abreviatura !== null ? element.abreviatura : ''}</td>
            <td><input type="number" class="form-control unitario" style="width:120px;" data-id="${element.tipo == 'sobrante' ? element.id_producto : element.id}" data-tipo="${element.tipo}" step="0.001" 
                value="${element.valor_unitario}"/></td>
            <td>${formatNumber.decimal((element.cantidad * element.valor_unitario), '', -4)}</td>
            <td>
                ${element.control_series ?
                `<input type="text" class="oculto" id="series" value="${element.series}" data-partnumber="${element.part_number}"/>
                    <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" 
                    onClick="agrega_series_transformacion(${"'" + element.id + "'"});"></i>` : ''}
                ${element.tipo == 'sobrante' ?
                `<button type="button" style="padding-left:8px;padding-right:7px;" 
                    class="asignar btn btn-xs btn-info boton" data-toggle="tooltip" 
                    data-placement="bottom" data-partnumber="${element.part_number}" 
                    data-desc="${encodeURIComponent(element.descripcion)}" data-id="${element.id}"
                    title="Asignar producto" >
                    <i class="fas fa-angle-double-right"></i>
                </button>` : ''}
            </td>
            </tr>`;
        i++;
    });
    $('#detalleOrdenSeleccionadas tbody').html(html);
}

$('#detalleOrdenSeleccionadas tbody').on("click", "button.asignar", function () {
    var partnumber = $(this).data('partnumber');
    var desc = $(this).data('desc');
    var id = $(this).data('id');
    console.log('openAsignarProducto');
    openAsignarProducto(partnumber, desc, id, 0);
});

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
