function iniciar(permiso) {
    // $("#tab-ordenes section:first form").attr('form', 'formulario');
    listarDespachosPendientes(permiso);
    listarDevoluciones();

    $('#myTabDespachosPendientes a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let tab = $(e.target).attr("href")
        if (tab == '#pendientes') {
            $("#despachosPendientes").DataTable().ajax.reload(null, false);
        }
        else if (tab == '#devoluciones') {
            $("#listaDevoluciones").DataTable().ajax.reload(null, false);
        }
        else if (tab == '#salidas') {
            listarDespachosEntregados(permiso);
        }
    });
    // $('ul.nav-tabs li a').on('click', function () {
    //     $('ul.nav-tabs li').removeClass('active');
    //     $(this).parent().addClass('active');
    //     $('.content-tabs section').attr('hidden', true);
    //     $('.content-tabs section form').removeAttr('type');
    //     $('.content-tabs section form').removeAttr('form');

    //     var activeTab = $(this).attr('type');
    //     var activeForm = "form-" + activeTab.substring(1);

    //     $("#" + activeForm).attr('type', 'register');
    //     $("#" + activeForm).attr('form', 'formulario');
    //     changeStateInput(activeForm, true);

    //     // clearDataTable();
    //     if (activeForm == "form-pendientes") {
    //         listarDespachosPendientes(permiso);
    //     }
    //     else if (activeForm == "form-salidas") {
    //         listarDespachosEntregados(permiso);
    //     }
    //     $(activeTab).attr('hidden', false);//inicio botones (estados)
    // });

    vista_extendida();
}

let $tableSalidas;

function listarDespachosPendientes(permiso) {
    var vardataTables = funcDatatables();
    let botones = [];
    // if (acceso == '1') {
    const button_descargar_excel = (array_accesos.find(element => element === 113) ? {
        text: ' Exportar a Excel',
        action: function () {
            exportarDespachosPendientes();
        }, className: 'btn-success btnExportarPendientes'
    } : []);
    botones.push(button_descargar_excel);
    // }

    $("#despachosPendientes").on('search.dt', function () {
        $('#despachosPendientes_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#despachosPendientes").on('processing.dt', function (e, settings, processing) {
        if (processing) {
            $(e.currentTarget).LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                zIndex: 10,
                imageColor: "#3c8dbc"
            });
        } else {
            $(e.currentTarget).LoadingOverlay("hide", true);
        }
    });

    $tableSalidas = $('#despachosPendientes').DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        // destroy: true,
        pageLength: 50,
        serverSide: true,
        initComplete: function (settings, json) {
            const $filter = $("#despachosPendientes_filter");
            const $input = $filter.find("input");
            $filter.append(
                '<button id="btnBuscar" class="btn btn-default btn-sm btn-flat" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>'
            );
            $input.off();
            $input.on("keyup", e => {
                if (e.key == "Enter") {
                    $("#btnBuscar").trigger("click");
                }
            });
            $("#btnBuscar").on("click", e => {
                $tableSalidas.search($input.val()).draw();
            });

            const $form = $('#formFiltrosSalidasPendientes');
            $('#despachosPendientes_wrapper .dt-buttons').append(
                `<div style="display:flex">
                    <label style="text-align: center;margin-top: 7px;margin-left: 10px;margin-right: 10px;">Mostrar: </label>
                    <select class="form-control" id="selectMostrarPendientes">
                        <option value="0" selected>Todos</option>
                        <option value="2" >Los de Hoy</option>
                    </select>
                </div>`
            );

            $("#selectMostrarPendientes").on("change", function (e) {
                var sed = $(this).val();
                console.log('sel ' + sed);
                $('#formFiltrosSalidasPendientes').find('input[name=select_mostrar_pendientes]').val(sed);
                $("#despachosPendientes").DataTable().ajax.reload(null, false);
            });
        },
        drawCallback: function (settings) {
            $("#despachosPendientes_filter input").prop("disabled", false);
            $("#btnBuscar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop("disabled", false);
            $("#despachosPendientes_filter input").trigger("focus");
        },
        ajax: {
            url: 'listarOrdenesDespachoPendientes',
            type: 'POST',
            data: function (params) {
                var x = $('[name=select_mostrar_pendientes]').val();
                console.log(x);
                return Object.assign(params, objectifyForm($('#formFiltrosSalidasPendientes').serializeArray()))
            }
        },
        'columns': [
            { data: 'id_od' },
            {
                data: 'codigo', name: 'orden_despacho.codigo', className: "text-center",
                render:
                    function (data, type, row) {
                        return `<span class="label label-${row['aplica_cambios'] ? 'danger' : 'primary'}">${row['codigo']}</span>`;
                    }
            },
            {
                data: 'codigo_req', name: 'alm_req.codigo', className: "text-center",
                'render': function (data, type, row) {
                    return (row['codigo_req'] !== null ? `<a href="/necesidades/requerimiento/elaboracion/index?id=${row['id_requerimiento']}"
                    target="_blank" title="Abrir Requerimiento">${row['codigo_req'] ?? ''}</a>` : '') + (row['estado_requerimiento'] == 38
                            ? ' <i class="fas fa-exclamation-triangle red" data-toggle="tooltip" data-placement="bottom" title="Requerimiento por regularizar"></i> '
                            : (row['estado_requerimiento'] == 39 ?
                                ' <i class="fas fa-pause orange" data-toggle="tooltip" data-placement="bottom" title="Requerimiento en pausa"></i> ' : ''))
                        + (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '');
                }
            },
            {
                data: 'fecha_despacho', name: 'orden_despacho.fecha_despacho', className: "text-center",
                render:
                    function (data, type, row) {
                        return formatDate(row['fecha_despacho']);
                    }
            },
            { data: 'obs_facturacion', name: 'alm_req.obs_facturacion', className: "text-center" },
            {
                data: 'nro_orden', name: 'oc_propias_view.nro_orden',
                render: function (data, type, row) {
                    if (row["nro_orden"] == null) {
                        return '';
                    } else {
                        return (
                            `<a href="#" class="archivos" data-id="${row["id_oc_propia"]}" data-tipo="${row["tipo"]}">
                            ${row["nro_orden"]}</a>`
                        );
                    }
                }, className: "text-center"
            },
            { data: 'codigo_oportunidad', name: 'oc_propias_view.codigo_oportunidad', className: "text-center" },
            { 'data': 'razon_social', 'name': 'adm_contri.razon_social' },
            { 'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion' },
            // {
            //     data: 'estado_doc', name: 'adm_estado_doc.bootstrap_color', className: "text-center",
            //     'render': function (data, type, row) {
            //         return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>';
            //         // row['suma_reservas'] + ' ' + row['suma_cantidad']
            //     }
            // },
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible' },
            {
                'render': function (data, type, row) {

                    return `` + (array_accesos.find(element => element === 114) ? `<button type="button" class="detalle btn btn-default btn-flat boton" data-toggle="tooltip"
                    data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
                    <i class="fas fa-chevron-down"></i></button>`: []) +
                        (array_accesos.find(element => element === 115) ?
                            `<button type="button" class="guia btn btn-warning btn-flat boton" data-toggle="tooltip"
                            data-placement="bottom" title="Generar Guía"
                            ${(row['estado_requerimiento'] == 39 || row['estado_requerimiento'] == 38) ? 'disabled' : ''} >
                            <i class="fas fa-sign-in-alt"></i></button>`: []) +
                        (array_accesos.find(element => element === 116) ?
                            `<button type="button" class="btn btn-success btn-flat boton ver-adjuntos" title="Ver Adjuntos" data-id="${row['id_requerimiento']}" data-codigo="${row['codigo_req']}">
                            <i class="fas fa-file-archive"></i></button>`: [])

                }, targets: 9
            }
        ],
        'order': [[0, "desc"]],
    });
}

$("#despachosPendientes tbody").on("click", "a.archivos", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    var tipo = $(this).data("tipo");
    obtenerArchivosMgcp(id, tipo);
});
// ${row['estado_doc'] == 'Priorizado' ?
//      `<button type="button" class="despachado btn btn-success btn-flat boton" data-toggle="tooltip"
//      data-placement="bottom" title="Marcar como despachado" data-id="${row['id_requerimiento']}">
//      <i class="fas fa-check"></i></button>`
//      : ''}

// $('#despachosPendientes tbody').on("click", "button.despachado", function () {
//     var data = $('#despachosPendientes').DataTable().row($(this).parents("tr")).data();
//     console.log(data);
//     $.ajax({
//         type: 'GET',
//         url: 'marcar_despachado/' + data.id_od + '/' + data.id_transformacion,
//         dataType: 'JSON',
//         success: function (response) {
//             console.log(response);
//             if (response == 'ok') {
//                 $('#despachosPendientes').DataTable().ajax.reload();
//             }
//         }
//     }).fail(function (jqXHR, textStatus, errorThrown) {
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// });

$('#despachosPendientes tbody').on("click", "button.guia", function () {
    var data = $('#despachosPendientes').DataTable().row($(this).parents("tr")).data();
    console.log('data.id_od' + data.id_od);
    open_guia_create(data);
});

// $('#despachosPendientes tbody').on("click", "button.anular", function () {
//     var id = $(this).data('id');
//     var msj = confirm('¿Está seguro que desea anular la Orden de Despacho ?');
//     if (msj) {
//         anularOrdenDespacho(id);
//     }
// });

// function anularOrdenDespacho(id) {
//     $.ajax({
//         type: 'GET',
//         url: 'anular_orden_despacho/' + id,
//         dataType: 'JSON',
//         success: function (response) {
//             console.log(response);
//             if (response > 0) {
//                 $('#despachosPendientes').DataTable().ajax.reload();
//             }
//         }
//     }).fail(function (jqXHR, textStatus, errorThrown) {
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }


var iTableCounter = 1;
var oInnerTable;

$('#despachosPendientes tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = $tableSalidas.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        format(iTableCounter, id, row);
        tr.addClass('shown');
        oInnerTable = $('#despachosPendientes_' + iTableCounter).dataTable({
            autoWidth: true,
            deferRender: true,
            info: false,
            lengthChange: false,
            ordering: false,
            paging: false,
            scrollX: false,
            scrollY: false,
            searching: false,
            columns: []
        });
        iTableCounter = iTableCounter + 1;
    }
});


function listarDespachosEntregados(permiso) {
    var vardataTables = funcDatatables();
    let botones = [];
    const button_descargr_excel = (array_accesos.find(element => element === 37) ? {
        text: ' Exportar Excel',
        action: function () {
            exportarSalidasProcesadas();
        }, className: 'btn-success btnExportarSalidasProcesadas'
    } : []);
    botones.push(button_descargr_excel);
    $('#despachosEntregados').DataTable({
        'dom': vardataTables[1],
        'buttons': botones,
        'language': vardataTables[0],
        'destroy': true,
        'serverSide': true,
        pageLength: 20,
        'ajax': {
            url: 'listarSalidasDespacho',
            type: 'POST'
        },
        'columns': [
            { 'data': 'id_mov_alm' },
            {
                'data': 'codigo_od', name: 'orden_despacho.codigo', filterable: true,
                'render':
                    function (data, type, row) {
                        if (row['codigo_od'] !== null) {
                            if (row['aplica_cambios']) {
                                return '<span class="label label-danger">' + row['codigo_od'] + '</span>';
                            } else {
                                return '<span class="label label-primary">' + row['codigo_od'] + '</span>';
                            }
                        } else if (row['id_transferencia'] !== null) {
                            return '<span class="label label-success">Transferencia</span>';
                        } else if (row['id_transformacion'] !== null) {
                            return '<span class="label label-warning">Customización</span>';
                        } else if (row['id_devolucion'] !== null) {
                            return '<span class="label label-info">Devolución</span>';
                        }
                    }
            },
            {
                data: 'codigo_requerimiento', name: 'alm_req.codigo',
                'render': function (data, type, row) {
                    if (row['codigo_requerimiento'] !== null) {
                        // return row['codigo_requerimiento'];
                        return (row['codigo_requerimiento'] !== null ? `<a href="/necesidades/requerimiento/elaboracion/index?id=${row['id_requerimiento']}"
                    target="_blank" title="Abrir Requerimiento">${row['codigo_requerimiento'] ?? ''}</a>` : '') + (row['estado_requerimiento'] == 38
                                ? ' <i class="fas fa-exclamation-triangle red" data-toggle="tooltip" data-placement="bottom" title="Requerimiento por regularizar"></i> '
                                : (row['estado_requerimiento'] == 39 ?
                                    ' <i class="fas fa-pause orange" data-toggle="tooltip" data-placement="bottom" title="Requerimiento en pausa"></i> ' : ''))
                            + (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '');
                    }
                    else {
                        return '';
                    }
                }
            },
            {
                'render':
                    function (data, type, row) {
                        if (row['razon_social'] !== null) {
                            return row['razon_social'];
                            // } else if (row['nombre_persona'] !== null) {
                            //     return row['nombre_persona'];
                        } else {
                            return '';
                        }
                    }
            },
            {
                data: 'nro_orden', name: 'oc_propias_view.nro_orden',
                render: function (data, type, row) {
                    if (row["nro_orden"] == null) {
                        return '';
                    } else {
                        return (
                            `<a href="#" class="archivos" data-id="${row["id_oc_propia"]}" data-tipo="${row["tipo"]}">
                            ${row["nro_orden"]}</a>`
                        );
                    }
                }, className: "text-center"
            },
            {
                data: 'codigo_devolucion', name: 'devolucion.codigo',
                'render': function (data, type, row) {
                    return (row['codigo_devolucion'] !== null ? '<label class="lbl-codigo" title="Abrir Devolución" onClick="abrirDevolucion(' + row['id_devolucion'] + ')">' + row['codigo_devolucion'] + '</label>' : '');
                }
            },
            {
                data: 'numero', name: 'guia_ven.numero',
                'render': function (data, type, row) {
                    return (row['serie'] ?? '') + '-' + (row['numero'] ?? '');
                }
            },
            { data: 'fecha_emision', className: "text-center" },
            {
                orderable: false, filterable: false, className: "text-center",
                render:
                    function (data, type, row) {
                        return row.comprobantes_venta;
                    }
            },
            { 'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion' },
            {
                data: 'codigo',
                'render': function (data, type, row) {
                    return (row['codigo'] !== null ?
                        ('<label class="lbl-codigo" title="Abrir Salida" onClick="abrir_salida(' + row['id_mov_alm'] + ')">' + row['codigo'] + '</label>')
                        : '') + (row['estado'] == 7 ? 'Anulada' : '');
                }
            },
            { 'data': 'operacion', 'name': 'tp_ope.descripcion' },
            { 'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto' }
        ],
        'order': [[0, "desc"]],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible' },
            {
                'render': function (data, type, row) {
                    if (permiso == '1') {
                        if (row['estado'] !== 7) {
                            return `<div style="display:flex;">
                                    ${row['id_guia_ven'] == null && row['id_transformacion'] !== null ? '' :
                                    (array_accesos.find(element => element === 119) ? `<button type="button" class="editar btn btn-primary btn-flat boton" data-toggle="tooltip"
                                    data-placement="bottom" title="Editar Guía de Salidaa" data-id="${row['id_mov_alm']}" data-guia="${row['id_guia_ven']}"
                                    data-od="${row['id_od']}"><i class="fas fa-edit"></i></button>` : ``) +
                                    (array_accesos.find(element => element === 120) ?
                                        `<button type="button" class="imprimir btn btn-info btn-flat boton" data-toggle="tooltip"
                                        data-placement="bottom" title="Descargar formato de impresión" data-guia="${row['id_guia_ven']}">
                                        <i class="fas fa-print"></i></button>`: ``) +
                                    (array_accesos.find(element => element === 121) ? `
                                    <button type="button" class="btn btn-success btn-flat boton ver-adjuntos" title="Ver Adjuntos" data-id="${row['id_requerimiento']}">
                                        <i class="fas fa-file-archive"></i></button>
                                        `: ``)}

                                    ${(row['id_guia_ven'] == null && row['id_transformacion'] !== null)
                                    || (row['id_guia_ven'] !== null && row['id_devolucion'] !== null)
                                    || row['estado_od'] == 21 || row['estado_od'] == 1 ?
                                    (array_accesos.find(element => element === 122) ? `<button type="button" class="anular btn btn-danger btn-flat boton" data-toggle="tooltip"
                                        data-placement="bottom" title="Anular Salida" data-id="${row['id_mov_alm']}" data-guia="${row['id_guia_ven']}"
                                        data-od="${row['id_od']}" data-dev="${row['id_devolucion']}"><i class="fas fa-trash"></i></button>` : ``) : ''}
                                </div>`;
                        } else {
                            return (array_accesos.find(element => element === 281) ? `<button type="button" class="anulacion btn btn-default btn-flat boton" data-toggle="tooltip"
                                data-placement="bottom" title="Ver datos de la Anulación" data-id="${row['id_mov_alm']}"
                                data-fecha="${row['fecha_anulacion']}" data-comentario="${row['comentario_anulacion']}"
                                data-usuario="${row['usuario_anulacion_nombre']}"
                                ><i class="fas fa-eye"></i></button>`: ``);
                        }
                    } else {
                        return '';
                    }
                }, targets: 13
            }
        ],
    });
}

$("#despachosEntregados tbody").on("click", "button.anulacion", function (e) {
    var id = $(this).data("id");
    var fec = $(this).data("fecha");
    var com = $(this).data("comentario");
    var usu = $(this).data("usuario");
    Swal.fire({
        title: `Anulado por ` + usu + ` el ` + formatDateHour(fec),
        text: 'Motivo: ' + com,
        icon: "info",
    });
});

$("#despachosEntregados tbody").on("click", "a.archivos", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    var tipo = $(this).data("tipo");
    obtenerArchivosMgcp(id, tipo);
});

$('#despachosEntregados tbody').on("click", "button.editar", function () {
    var data = $("#despachosEntregados").DataTable().row($(this).parents("tr")).data();
    console.log(data);
    abrirSalidaAlmacen(data);
});

function abrir_salida(id_mov_alm) {
    // var id = encode5t(id_mov_alm);
    window.open('imprimir_salida/' + id_mov_alm);
}

function abrirDevolucion(id_devolucion) {
    localStorage.setItem("id_devolucion", id_devolucion);
    var win = window.open("/cas/garantias/devolucionCas/index", '_blank');
    win.focus();
}

$('#despachosEntregados tbody').on("click", "button.anular", function () {
    var id_mov_alm = $(this).data('id');
    var id_guia = $(this).data('guia');
    var id_od = $(this).data('od');
    var id_dev = $(this).data('dev');

    $('#modal-guia_ven_obs').modal({
        show: true
    });

    $('[name=id_salida]').val(id_mov_alm);
    $('[name=id_guia_ven]').val(id_guia);
    $('[name=id_od]').val(id_od);
    $('[name=id_devolucion]').val(id_dev);
    $('[name=observacion_guia_ven]').val('');

    $("#submitGuiaVenObs").removeAttr("disabled");
});

$("#form-guia_ven_obs").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    anular_salida(data);
});

function anular_salida(data) {
    $("#submitGuiaVenObs").attr('disabled', 'true');
    $.ajax({
        type: 'POST',
        url: 'anular_salida',
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
            $('#modal-guia_ven_obs').modal('hide');

            if (response.tipo == 'success') {
                $('#despachosEntregados').DataTable().ajax.reload(null, false);
                $('#nro_despachos').text(response.nroDespachosPendientes);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

$('#despachosEntregados tbody').on("click", "button.imprimir", function () {
    var id_guia = $(this).data('guia');
    // Abrir nuevo tab
    let url = "/almacen/movimientos/pendientes-salida/guia-salida-excel/" + id_guia;
    var win = window.open(url, '_blank');
    win.focus();
});

$('#despachosEntregados tbody').on("click", "button.cambio", function () {
    var id_mov_alm = $(this).data('id');
    var id_guia = $(this).data('guia');
    var id_od = $(this).data('od');

    $('#modal-guia_ven_cambio').modal({
        show: true
    });

    $('[name=id_salida]').val(id_mov_alm);
    $('[name=id_guia_ven]').val(id_guia);
    $('[name=id_od]').val(id_od);
    $('[name=serie_nuevo]').val('');
    $('[name=numero_nuevo]').val('');

    $("#submit_guia_ven_cambio").removeAttr("disabled");
});

function exportarDespachosPendientes() {
    $('#formFiltrosSalidasPendientes').trigger('submit');
}

function exportarSalidasProcesadas() {
    $('#formFiltrosSalidasProcesadas').trigger('submit');
}
$(document).on('click', '.ver-adjuntos', function () {
    var data_id = $(this).attr('data-id'),
        codigo = $(this).attr('data-codigo');
    $('#modal-ver-adjuntos').modal('show');
    $('#modal-ver-adjuntos #codigo').text(codigo);
    verAdjuntos(data_id);
});
function verAdjuntos(id) {
    var html = '';
    $.ajax({
        type: 'GET',
        url: 'atencion-ver-adjuntos',
        data: {
            id: id,
        },
        // processData: false,
        // contentType: false,
        dataType: 'JSON',
        beforeSend: (data) => {
        },
        success: (response) => {
            if (response.status === 200) {
                console.log(response);

                $.each(response.data, function (indexInArray, valueOfElement) {
                    if (valueOfElement.descripcion === 'Contabilidad') {
                        html += '<tr data-key="' + valueOfElement.id_adjuntos + '">'
                        html += '<td>'
                        html += '<a href="/files/tesoreria/adjuntos_facturas/' + valueOfElement.archivo + '" target="_blank"><i class="fa fa-file-download"></i> ' + valueOfElement.archivo + '</a>'
                        html += '</td>'
                        html += '<td>'
                        html += '' + valueOfElement.fecha_registro
                        html += '</td>'
                        html += '</tr>'
                    }

                });
                $('[data-table="ver-table-body"]').html(html);
            }
        },
        fail: (jqXHR, textStatus, errorThrown) => {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
}
