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
        'ajax': 'listarRequerimientosEnProceso',
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
                        '<a href="#" class="verRequerimiento" data-id="' + row["id_requerimiento"] + '" >' + row["codigo"] + "</a>"
                    );
                }, targets: 1
            },
            {
                'render': function (data, type, row) {
                    // if (permiso == '1') {
                    console.log(row['codigo'] + '  estado: ' + row['estado_od']);
                    return `<div style="display:flex;">
                        <button type="button" class="detalle btn btn-default btn-flat boton" data-toggle="tooltip"
                        data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
                        <i class="fas fa-chevron-down"></i></button>`+
                        ((
                            // (row['estado'] == 19 && row['id_tipo_requerimiento'] == 1 && row['sede_requerimiento'] == row['sede_orden'] && row['id_od'] == null) || //compra 
                            // (row['estado'] == 19 && row['id_tipo_requerimiento'] == 1 && row['sede_requerimiento'] !== row['sede_orden'] && row['id_transferencia'] !== null && row['id_od'] == null) || //compra con transferencia
                            (row['estado'] == 19 && row['confirmacion_pago'] == true && /*row['id_od'] == null &&*/ row['count_transferencia'] == 0) || //venta directa
                            // (row['estado'] == 10) || (row['estado'] == 22) ||
                            (row['estado'] == 28) || //(row['estado'] == 27) ||
                            (row['estado'] == 19 && row['id_tipo_requerimiento'] !== 1) ||
                            (row['estado'] == 19 && row['confirmacion_pago'] == true && /*row['id_od'] == null &&*/ row['count_transferencia'] > 0 && row['count_transferencia'] == row['count_transferencia_recibida'])) ? //venta directa con transferencia
                            ('<button type="button" class="despacho btn btn-success btn-flat boton" data-toggle="tooltip" ' +
                                'data-placement="bottom" title="Generar Orden de Despacho" >' +
                                '<i class="fas fa-sign-in-alt"></i></button>') :
                            (
                                row['id_od'] !== null && parseInt(row['estado_od']) == 1) ?
                                `<button type="button" class="anular_od btn btn-flat btn-danger boton" data-toggle="tooltip" 
                                    data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Anular Orden Despacho Interno" >
                                    <i class="fas fa-trash"></i></button>` : '')
                        + '</div>'

                }, targets: 10
            }
        ],
    });
    vista_extendida();
}

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

function anularOrdenDespacho(id) {
    $.ajax({
        type: 'GET',
        url: 'anular_orden_despacho/' + id + '/interno',
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
                    msg: "Orden de Transformación anulada con éxito."
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