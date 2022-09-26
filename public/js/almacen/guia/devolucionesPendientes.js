function listarDevoluciones() {
    var vardataTables = funcDatatables();
    let botones = [];

    tableDevoluciones = $('#listaDevoluciones').DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        bDestroy: true,
        serverSide: true,
        ajax: 'listarDevolucionesRevisadas',
        columns: [
            { 'data': 'id_devolucion' },
            {
                'data': 'codigo',
                render: function (data, type, row) {
                    return (
                        `<a href="#" class="devolucion" data-id="${row["id_devolucion"]}">${row["codigo"]}</a>`
                    );
                }
            },
            {
                data: 'fecha_registro',
                'render': function (data, type, row) {
                    return (row['fecha_registro'] !== undefined ? formatDate(row['fecha_registro']) : '');
                }
            },
            { 'data': 'observacion' },
            {
                'render': function (data, type, row) {
                    return `<a href="#" onClick="verFichasTecnicasAdjuntas(${row["id_devolucion"]});">${row["count_fichas"]} archivos adjuntos </a>`;
                }, className: "text-center"
            },
            { 'data': 'nombre_corto', name: 'sis_usua.nombre_corto' },
            {
                'data': 'usuario_conformidad', name: 'usuario_conforme.nombre_corto',
                'render': function (data, type, row) {
                    return `${row["usuario_conformidad"]} el ${formatDateHour(row["fecha_revision"])}`;
                }, className: "text-center"
            },
            {
                'render':
                    function (data, type, row) {
                        if (row['estado'] == 1 || row['estado'] == 2) {
                            return `
                            <div class="btn-group" role="group">
                                <button type="button" class="guiadev btn btn-info boton" data-toggle="tooltip"
                                data-placement="bottom" data-id="${row['id_devolucion']}" title="Ingresar Guía" >
                                <i class="fas fa-sign-in-alt"></i></button>
                            </div>`;
                        } else {
                            return '';
                        }
                    }, className: "text-center"
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible' }],
        order: [[0, "desc"]],
    });
}

$("#listaDevoluciones tbody").on("click", "button.guiadev", function () {
    var data = $("#listaDevoluciones").DataTable().row($(this).parents("tr")).data();
    open_devolucion_guia_create(data);
});

$("#listaDevoluciones tbody").on("click", "a.devolucion", function () {
    var id = $(this).data("id");
    abrirDevolucion(id);
});

function open_devolucion_guia_create(data) {
    console.log(data.id_devolucion);
    var fecha = fecha_actual();
    $('#modal-guia_create').modal({
        show: true
    });
    $("#submit_guia").removeAttr("disabled");
    $('[name=id_operacion]').val(24);
    $('[name=nombre_operacion]').val('INGRESO POR DEVOLUCIÓN DEL CLIENTE');
    $('[name=id_guia_clas]').val(1);
    $('[name=id_proveedor]').val(data.id_proveedor);
    $('[name=razon_social_proveedor]').val(data.razon_social);
    $('[name=id_sede]').val(data.id_sede);
    $('[name=id_devolucion]').val(data.id_devolucion);
    $('[name=id_orden_compra]').val('');
    $('[name=id_transformacion]').val('');
    $('[name=id_od]').val('');
    $('[name=id_requerimiento]').val('');
    $('[name=serie]').val('');
    $('[name=numero]').val('');
    $('[name=fecha_emision]').val(fecha);
    $('[name=fecha_almacen]').val(fecha);

    $('#detalleOrdenSeleccionadas tbody').html('');
    cargar_almacenes(data.id_sede, data.id_almacen);
    $("#id_almacen").attr('disabled', 'true');
    $('[name=comentario]').val('');

    $(".orden_transformacion").html(`<h5></h5>
    <div style="display:flex;">
    <label class="lbl-codigo" title="Abrir Devolución" onClick="abrir_devolucion(${data.id_devolucion})">
    ${data.codigo}</label>
    </div>`);

    $(".transformacion").hide();
    $(".compra").hide();
    $(".devolucion").show();

    $('[name=moneda_devolucion]').val('');
    $('[name=tipo_cambio_devolucion]').val('');

    $.ajax({
        type: 'GET',
        url: 'getTipoCambioVenta/' + fecha,
        dataType: 'JSON',
        success: function (response) {
            $('[name=tipo_cambio_devolucion]').val(response);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });

    listar_detalle_devolucion(data.id_devolucion);
}

let detalle_devolucion = [];

function listar_detalle_devolucion(id) {
    oc_det_seleccionadas = [];
    series_transformacion = [];
    detalle_devolucion = [];
    $('#detalleOrdenSeleccionadas tbody').html('');
    $.ajax({
        type: 'GET',
        url: 'listarDetalleDevolucion/' + id,
        dataType: 'JSON',
        success: function (response) {
            console.log(response);

            response.forEach(function (element) {
                detalle_devolucion.push({
                    'id_detalle': element.id_detalle,
                    'series': [],
                    'control_series': element.series,
                    'cantidad': element.cantidad,
                    'id_producto': element.id_producto,
                    'codigo': element.codigo_devolucion,
                    'cod_prod': element.codigo,//element.cod_prod,
                    'part_number': element.part_number,
                    'descripcion': element.descripcion,
                    'abreviatura': element.abreviatura,
                    'id_moneda': element.id_moneda,
                    'id_unidad_medida': element.id_unidad_medida,
                    'valor_unitario': 0,
                    'valor_total': 0
                });
            });
            // $('[name=tipo_cambio_transformacion]').val(response['tipo_cambio']);
            mostrar_detalle_devolucion();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function mostrar_detalle_devolucion() {
    var html = '';
    var html_ser = '';
    var i = 1;
    var total = 0;
    var subtotal = 0;
    var mnd = $('[name=moneda_devolucion]').val();

    var moneda = (mnd == 1 ? 'S/' : (mnd == 2 ? '$' : ''));

    detalle_devolucion.forEach(function (element) {
        html_ser = '';
        element.series.forEach(function (serie) {
            if (html_ser == '') {
                html_ser += serie;
            } else {
                html_ser += ', ' + serie;
            }
        });
        subtotal = element.cantidad * element.valor_unitario;
        total += subtotal;

        html += `<tr>
            <td>${i}</td>
            <td>${element.codigo}</td>
            <td>${element.cod_prod !== null ?
                (element.cod_prod == '' ? '<label>(por crear)</label>'
                    : `<a href="#" class="verProducto" data-id="${element.id_producto}" >${element.cod_prod}</a>`)
                : '<label class="subtitulo_red">(sin mapear)</label>'}</td>
            <td>${(element.part_number !== null || element.part_number !== 'null') ? element.part_number : ''}</td>
            <td>${element.descripcion + ' <br><strong>' + html_ser + '</strong>'}</td>
            <td class="text-right">${element.cantidad}</td>
            <td>${element.abreviatura !== null ? element.abreviatura : ''}</td>
            <td>
                <div style="display:flex;width:90px;">
                    <input type="number" class="form-control unitarioDev" style="text-align: right;"
                    data-id="${element.id_detalle}" step="0.001" value="${element.valor_unitario}" /></div>
            </td>
            <td class="text-right">${moneda + formatNumber.decimal((subtotal), '', -2)}</td>
            <td width="8%">
                ${element.control_series ?
                `<input type="text" class="oculto" id="series" value="${element.series}" data-partnumber="${element.part_number}"/>
                        <i class="fas fa-bars icon-tabla boton" data-toggle="tooltip" data-placement="bottom" title="Agregar Series" 
                        onClick="agrega_series_devolucion(${"'" + element.id_detalle + "'"});"></i>` : ''}
                </td>
            </tr>`;
        i++;
    });

    html += `<tr>
        <td colSpan="8"></td>
        <td><strong>${(moneda) + (formatNumber.decimal((total), '', -2))}</strong></td>
        <td></td>
    </tr>`;

    $('#detalleOrdenSeleccionadas tbody').html(html);
}

$('#detalleOrdenSeleccionadas tbody').on("change", ".unitarioDev", function () {

    let id = $(this).data('id');
    let unitario = parseFloat($(this).val());

    detalle_devolucion.forEach(element => {
        if (element.id_detalle == id) {
            element.valor_unitario = unitario;
            element.valor_total = (unitario * parseFloat(element.cantidad));
        }
    });
    mostrar_detalle_devolucion();
});

$("[name=moneda_devolucion]").on('change', function () {
    mostrar_detalle_devolucion();
});

function abrirDevolucion(id_devolucion) {
    console.log('abrirDevolucion' + id_devolucion);
    localStorage.setItem("id_devolucion", id_devolucion);
    // location.assign("/logistica/almacen/customizacion/hoja-transformacion/index");
    var win = window.open("/almacen/movimientos/devolucion/index", '_blank');
    win.focus();
}