function iniciar(permiso) {
    // $("#tab-ordenes section:first form").attr('form', 'formulario');
    listarDespachosPendientes(permiso);

    $('#myTabDespachosPendientes a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let tab = $(e.target).attr("href")
        if (tab == '#pendientes') {
            $("#despachosPendientes").DataTable().ajax.reload(null, false);
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
    // botones.push({
    //     text: ' Exportar a Excel',
    //     action: function () {
    //         priorizar();
    //     }, className: 'btn-success btnExportarPendientes'
    // });
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
                        <option value="1" >Priorizados</option>
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
            { data: 'fecha_despacho', className: "text-center" },
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
            { 'data': 'codigo_req', 'name': 'alm_req.codigo', className: "text-center" },
            { 'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion' },
            {
                data: 'estado_doc', name: 'adm_estado_doc.bootstrap_color', className: "text-center",
                'render': function (data, type, row) {
                    return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>' +
                        row['suma_reservas'] + ' ' + row['suma_cantidad']
                }
            },
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible' },
            {
                'render': function (data, type, row) {

                    // if (permiso == '1') {
                    return `<button type="button" class="detalle btn btn-default btn-flat boton" data-toggle="tooltip"
                                data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
                                <i class="fas fa-chevron-down"></i></button>` +

                        // (row['suma_reservas'] !== null && row['suma_cantidad'] !== null && row['suma_reservas'] >= row['suma_cantidad'] ?
                        (`<button type="button" class="guia btn btn-warning btn-flat boton" data-toggle="tooltip" 
                                data-placement="bottom" title="Generar Guía" >
                                <i class="fas fa-sign-in-alt"></i></button>`)
                    // : ''
                    // );

                    // } else {
                    //     return '<button type="button" class="detalle btn btn-primary btn-flat boton" data-toggle="tooltip" ' +
                    //         'data-placement="bottom" title="Ver Detalle" >' +
                    //         '<i class="fas fa-list-ul"></i></button>'
                    // }
                }, targets: 10
            }
        ],
        'order': [[0, "desc"]],
    });
}

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
    $('#despachosEntregados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'destroy': true,
        'serverSide': true,
        // "scrollX": true,
        'ajax': {
            url: 'listarSalidasDespacho',
            type: 'POST'
        },
        'columns': [
            { 'data': 'id_mov_alm' },
            {
                'render':
                    function (data, type, row) {
                        if (row['codigo_od'] !== null) {
                            if (row['aplica_cambios']) {
                                return '<span class="label label-danger">' + row['codigo_od'] + '</span>';
                            } else {
                                return '<span class="label label-primary">' + row['codigo_od'] + '</span>';
                            }
                        } else {
                            return '<span class="label label-success">' + row['codigo_trans'] + '</span>';
                        }
                    }
            },
            { 'data': 'fecha_emision' },
            { 'data': 'almacen_descripcion', 'name': 'alm_almacen.descripcion' },
            // {'data': 'codigo'},
            {
                'render': function (data, type, row) {
                    return (row['codigo'] !== null ?
                        ('<label class="lbl-codigo" title="Abrir Salida" onClick="abrir_salida(' + row['id_mov_alm'] + ')">' + row['codigo'] + '</label>')
                        : '');
                }
            },
            {
                'render': function (data, type, row) {
                    return row['serie'] + '-' + row['numero'];
                }
            },
            { 'data': 'operacion' },
            // {'data': 'codigo_requerimiento', 'name': 'alm_req.codigo'},
            {
                'render': function (data, type, row) {
                    if (row['codigo_requerimiento'] !== null) {
                        return row['codigo_requerimiento'];
                    }
                    else if (row['codigo_req_trans'] !== null) {
                        return row['codigo_req_trans'];
                    }
                }
            },
            {
                'render':
                    function (data, type, row) {
                        if (row['razon_social'] !== null) {
                            return row['razon_social'];
                        } else if (row['nombre_persona'] !== null) {
                            return row['nombre_persona'];
                        } else {
                            return '';
                        }
                    }
            },
            // {'data': 'concepto', 'name': 'alm_req.concepto'},
            {
                'render': function (data, type, row) {
                    if (row['concepto'] !== null) {
                        return row['concepto'];
                    }
                    else if (row['concepto_trans'] !== null) {
                        return row['concepto_trans'];
                    }
                }
            },
            { 'data': 'nombre_corto', 'name': 'sis_usua.nombre_corto' }
        ],
        'order': [[0, "desc"]],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible' },
            {
                'render': function (data, type, row) {
                    if (permiso == '1') {
                        return row['id_operacion'] == 11 ? '' :
                            `<div style="display:flex;">
                                <button type="button" class="anular btn btn-danger btn-flat boton" data-toggle="tooltip" 
                                data-placement="bottom" title="Anular Salida" data-id="${row['id_mov_alm']}" data-guia="${row['id_guia_ven']}"
                                data-od="${row['id_od']}"><i class="fas fa-trash"></i></button>
                                
                                <button type="button" class="cambio btn btn-warning btn-flat boton" data-toggle="tooltip" 
                                data-placement="bottom" title="Cambiar Serie-Número" data-id="${row['id_mov_alm']}" data-guia="${row['id_guia_ven']}"
                                data-od="${row['id_od']}"><i class="fas fa-sync-alt"></i></button>
                            </div>`;
                    }
                }, targets: 11
                // '<button type="button" class="detalle btn btn-primary boton" data-toggle="tooltip" '+
                //     'data-placement="bottom" title="Ver Detalle" data-id="'+row.id_mov_alm+'">'+
                //     '<i class="fas fa-list-ul"></i></button>'+
            }
        ],
    });
}

// $('#despachosEntregados tbody').on("click","button.salida", function(){
//     var id_mov_alm = $(this).data('id');
//     var id = encode5t(id_mov_alm);
//     window.open('imprimir_salida/'+id);
// });

function abrir_salida(id_mov_alm) {
    // var id = encode5t(id_mov_alm);
    window.open('imprimir_salida/' + id_mov_alm);
}

$('#despachosEntregados tbody').on("click", "button.anular", function () {
    var id_mov_alm = $(this).data('id');
    var id_guia = $(this).data('guia');
    var id_od = $(this).data('od');

    $('#modal-guia_ven_obs').modal({
        show: true
    });

    $('[name=id_salida]').val(id_mov_alm);
    $('[name=id_guia_ven]').val(id_guia);
    $('[name=id_od]').val(id_od);
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
            if (response.length > 0) {
                alert(response);
                $('#modal-guia_ven_obs').modal('hide');
            } else {
                alert('Salida Almacén anulada con éxito');
                $('#modal-guia_ven_obs').modal('hide');
                $('#despachosEntregados').DataTable().ajax.reload();
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

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