var table;

function listarRequerimientosPendientes() {
    var vardataTables = funcDatatables();
    table = $('#requerimientosEnProceso').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language': vardataTables[0],
        'bDestroy': true,
        pageLength: 20,
        // 'serverSide' : true,
        'ajax': 'listarRequerimientosPendientesDespachoExterno',
        // 'ajax': {
        //     url: 'listarRequerimientosEnProceso',
        //     type: 'POST'
        // },
        'columns': [
            { 'data': 'id_requerimiento' },
            { 'data': 'codigo', className: "text-center" },
            {
                'render': function (data, type, row) {
                    return (row['fecha_entrega'] !== null ? formatDate(row['fecha_entrega']) : '');
                }
            },
            {
                render: function (data, type, row) {
                    if (row["nro_orden"] == null) {
                        return '';
                    } else {
                        return (
                            '<a href="#" class="archivos" data-id="' + row["id_oc_propia"] + '" data-tipo="' + row["tipo"] + '">' +
                            row["nro_orden"] + "</a>"
                        );
                    }
                }, className: "text-center"
            },
            { 'data': 'codigo_oportunidad', 'name': 'oc_propias_view.codigo_oportunidad' },
            { 'data': 'cliente_razon_social', 'name': 'adm_contri.razon_social' },
            { 'data': 'responsable', 'name': 'sis_usua.nombre_corto' },
            { 'data': 'sede_descripcion_req', name: 'sede_req.descripcion', className: "text-center" },
            {
                'render': function (data, type, row) {
                    return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>'
                }
            },
            {
                'render': function (data, type, row) {
                    return ((row['count_transferencia'] > 0 ?
                        '<button type="button" class="detalle_trans btn btn-success boton" data-toggle="tooltip" ' +
                        'data-placement="bottom" title="Ver Detalle de Transferencias" data-id="' + row['id_requerimiento'] + '">' +
                        '<i class="fas fa-exchange-alt"></i></button>' : ''))
                }
            },
        ],
        'order': [[0, "desc"]],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible' },
            {
                render: function (data, type, row) {
                    return (
                        '<a href="#" class="verRequerimiento" data-id="' + row["id_requerimiento"] + '" >' + row["codigo"] + "</a>" + (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '')
                    );
                }, targets: 1
            },
            {
                'render': function (data, type, row) {
                    return `<div style="display:flex;">
                        <button type="button" class="detalle btn btn-default btn-flat boton" data-toggle="tooltip"
                        data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
                        <i class="fas fa-chevron-down"></i></button>

                        <button type="button" class="trazabilidad btn btn-warning btn-flat boton" data-toggle="tooltip"
                        data-placement="bottom" title="Ver Trazabilidad de Docs" disabled data-id="${row['id_requerimiento']}">
                        <i class="fas fa-route"></i></button>`+
                        ((row['id_od'] == null && row['productos_no_mapeados'] == 0)
                            // ((row['tiene_transformacion'] && row['estado'] == 10) ||
                            //     (!row['tiene_transformacion'] && row['estado'] == 28))
                            ? //venta directa con transferencia
                            `<button type="button" class="despacho btn btn-success btn-flat boton" data-toggle="tooltip"
                                data-placement="bottom" title="Generar Orden de Despacho" >
                                <i class="fas fa-sign-in-alt"></i></button>` : '') +
                        ((row['id_od'] !== null && row['estado_od'] == '1') ?
                            `<button type="button" class="anular_od btn btn-flat btn-danger boton" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Anular Orden Despacho" >
                                    <i class="fas fa-trash"></i></button>` : '') +
                        (row["nro_orden"] !== null && row['productos_no_mapeados'] == 0
                            ? `<button type="button" class="facturar btn btn-flat btn-${row["enviar_facturacion"] ? "info" : "default"} 
                                    boton" data-toggle="tooltip" data-placement="bottom" title="Enviar a Facturación" 
                                    data-id="${row["id_requerimiento"]}" data-cod="${row["codigo"]}" data-envio="${row["enviar_facturacion"]}">
                                    <i class="fas fa-file-upload"></i></button>`
                            : '')
                        + '</div>'

                }, targets: 10
            }
        ],
    });
    vista_extendida();
}

$("#requerimientosEnProceso tbody").on("click", "button.facturar", function () {
    var id = $(this).data("id");
    var cod = $(this).data("cod");
    var envio = $(this).data("envio");

    if (envio) {
        Lobibox.notify("warning", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Ya se envió a facturación.'
        });
    } else {
        Swal.fire({
            title: "¿Está seguro que desea mandar a facturar el " + cod + "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00a65a", //"#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sí, Guardar"
        }).then(result => {
            if (result.isConfirmed) {
                $('#modal-enviarFacturacion').modal({
                    show: true
                });
                $('[name=id_requerimiento]').val(id);
                $('#cod_req').text(cod);
            }
        });
    }
});

$("#form-enviarFacturacion").on("submit", function (e) {
    console.log('submit');
    e.preventDefault();
    var data = $(this).serialize();
    enviarFacturacion(data);
});


$("#requerimientosEnProceso tbody").on("click", "a.verRequerimiento", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    localStorage.setItem("idRequerimiento", id);
    let url = "/necesidades/requerimiento/elaboracion/index";
    var win = window.open(url, "_blank");
    win.focus();
});

$("#requerimientosEnProceso tbody").on("click", "a.archivos", function (e) {
    $(e.preventDefault());
    var id = $(this).data("id");
    var tipo = $(this).data("tipo");
    obtenerArchivosMgcp(id, tipo);
});

$('#requerimientosEnProceso tbody').on("click", "button.detalle_trans", function () {
    var id = $(this).data('id');
    open_detalle_transferencia(id);
});

$('#requerimientosEnProceso tbody').on("click", "button.adjuntar", function () {
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    $('#modal-despachoAdjuntos').modal({
        show: true
    });
    listarAdjuntos(id);
    $('[name=id_od]').val(id);
    $('[name=codigo_od]').val(cod);
    $('[name=descripcion]').val('');
    $('[name=archivo_adjunto]').val('');
    $('[name=proviene_de]').val('enProceso');
});

$('#requerimientosEnProceso tbody').on("click", "button.anular", function () {
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    var origen = 'despacho';
    openRequerimientoObs(id, cod, origen);
});

$('#requerimientosEnProceso tbody').on("click", "button.despacho", function () {
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    tab_origen = 'enProceso';
    open_despacho_create(data);
});

$('#requerimientosEnProceso tbody').on("click", "button.anular_od", function () {
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    Swal.fire({
        title: "¿Está seguro que desea anular la Orden de Transformación " + cod + "?",
        text: "No podrás revertir esto.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Si, anular"
    }).then(result => {
        if (result.isConfirmed) {
            anularOrdenDespacho(id);
        }
    });
});

$("#requerimientosEnProceso tbody").on("click", "button.trazabilidad", function () {
    var id = $(this).data("id");
    // $('#modal-trazabilidadDocs').modal({
    //     show: true
    // });
});

function enviarFacturacion(data) {
    $.ajax({
        type: "POST",
        url: "enviarFacturacion",
        data: data,
        dataType: "JSON",
        success: function (response) {
            console.log(response);
            if (response > 0) {
                $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anularOrdenDespacho(id) {
    $.ajax({
        type: 'GET',
        url: 'anular_orden_despacho/' + id + '/externo',
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            if (response > 0) {
                Lobibox.notify("success", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: "Orden de Despacho anulado con éxito."
                });
                $('#requerimientosEnProceso').DataTable().ajax.reload(null, false);
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function open_detalle_transferencia(id) {
    $('#modal-detalleTransferencia').modal({
        show: true
    });
    $.ajax({
        type: 'GET',
        url: 'listarDetalleTransferencias/' + id,
        dataType: 'JSON',
        success: function (response) {
            $('#detalleTransferencias tbody').html(response);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

var iTableCounter = 1;
var oInnerTable;

$('#requerimientosEnProceso tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var id = $(this).data('id');

    if (row.child.isShown()) {
        //  This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        // Open this row
        //    row.child( format(iTableCounter, id) ).show();
        format(iTableCounter, id, row);
        tr.addClass('shown');
        // try datatable stuff
        oInnerTable = $('#requerimientosEnProceso_' + iTableCounter).dataTable({
            //    data: sections, 
            autoWidth: true,
            deferRender: true,
            info: false,
            lengthChange: false,
            ordering: false,
            paging: false,
            scrollX: false,
            scrollY: false,
            searching: false,
            columns: [
                //   { data:'refCount' },
                //   { data:'section.codeRange.sNumber.sectionNumber' }, 
                //   { data:'section.title' }
            ]
        });
        iTableCounter = iTableCounter + 1;
    }
});

function abrir_requerimiento(id_requerimiento) {
    localStorage.setItem("id_requerimiento", id_requerimiento);
    let url = "/necesidades/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    win.focus();
}