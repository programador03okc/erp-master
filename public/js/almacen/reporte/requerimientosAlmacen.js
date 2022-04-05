let $tableRequerimientos;

function listarRequerimientosAlmacen() {
    console.log('list');
    var vardataTables = funcDatatables();
    let botones = [];

    $("#requerimientosAlmacen").on('search.dt', function () {
        $('#requerimientosAlmacen_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#requerimientosAlmacen").on('processing.dt', function (e, settings, processing) {
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

    $tableRequerimientos = $('#requerimientosAlmacen').DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        // destroy: true,
        pageLength: 10,
        serverSide: true,
        initComplete: function (settings, json) {
            const $filter = $("#requerimientosAlmacen_filter");
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
                $tableRequerimientos.search($input.val()).draw();
            });

            // const $form = $('#formFiltrosSalidasPendientes');
            // $('#requerimientosAlmacen_wrapper .dt-buttons').append(
            //     `<div style="display:flex">
            //         <label style="text-align: center;margin-top: 7px;margin-left: 10px;margin-right: 10px;">Mostrar: </label>
            //         <select class="form-control" id="selectMostrarPendientes">
            //             <option value="0" >Todos</option>
            //             <option value="1" >Priorizados</option>
            //             <option value="2" selected>Los de Hoy</option>
            //         </select>
            //     </div>`
            // );
            // $("#selectMostrarPendientes").on("change", function (e) {
            //     var sed = $(this).val();
            //     console.log('sel ' + sed);
            //     $('#formFiltrosSalidasPendientes').find('input[name=select_mostrar_pendientes]').val(sed);
            //     $("#requerimientosAlmacen").DataTable().ajax.reload(null, false);
            // });

        },
        drawCallback: function (settings) {
            $("#requerimientosAlmacen_filter input").prop("disabled", false);
            $("#btnBuscar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop("disabled", false);
            $("#requerimientosAlmacen_filter input").trigger("focus");
        },
        ajax: {
            url: 'listarRequerimientosAlmacen',
            type: 'POST',
            // data: function (params) {
            //     var x = $('[name=select_mostrar_pendientes]').val();
            //     console.log(x);
            //     return Object.assign(params, objectifyForm($('#formFiltrosSalidasPendientes').serializeArray()))
            // }
        },
        columns: [
            { data: 'id_requerimiento' },
            {
                data: 'codigo', className: "text-center",
                'render': function (data, type, row) {
                    return (row['codigo'] !== null ? `<a href="/necesidades/requerimiento/elaboracion/index?id=${row['id_requerimiento']}" 
                        target="_blank" title="Abrir Requerimiento">${row['codigo'] ?? ''}</a>` : '') +
                        (row['estado'] == 38
                            ? ' <i class="fas fa-exclamation-triangle red" data-toggle="tooltip" data-placement="bottom" title="Requerimiento por regularizar"></i> '
                            : (row['estado'] == 39 ?
                                ' <i class="fas fa-pause orange" data-toggle="tooltip" data-placement="bottom" title="Requerimiento en pausa"></i> ' : ''))
                        + (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '');
                }
            },
            {
                data: 'estado_doc', name: 'adm_estado_doc.estado_doc', className: "text-center",
                'render': function (data, type, row) {
                    return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>'
                }
            },
            { data: 'concepto' },
            { data: 'grupo_descripcion', name: 'sis_grupo.descripcion' },
            { data: 'almacen_descripcion', name: 'alm_almacen.descripcion', className: "text-center" },
            { data: 'fecha_entrega', name: 'alm_req.fecha_entrega', className: "text-center" },
            // { data: 'estado_doc', name: 'adm_estado_doc.estado_doc', className: "text-center" },
            { data: 'nombre_corto', name: 'sis_usua.nombre_corto', className: "text-center" },
            {
                data: 'codigo_despacho_interno', name: 'despachoInterno.codigo', className: "text-center",
                'render': function (data, type, row) {
                    return (row['codigo_despacho_interno'] ?? '') + (row['codigo_transformacion'] !== null ? `<br><label class="lbl-codigo" title="Abrir Transformación" 
                    onClick="abrir_transformacion(${row['id_transformacion']})">${row['codigo_transformacion']}</label>` : '')
                        + (row['estado_di'] ?? '');
                }
            },
            { data: 'codigo_despacho_externo', name: 'orden_despacho.codigo', className: "text-center" },
            {
                data: 'estado_despacho_descripcion', name: 'estado_despacho.estado_doc', className: "text-center",
                'render': function (data, type, row) {
                    return '<span class="label label-default">' + (row['estado_despacho_descripcion'] == 'Aprobado' ? 'Pendiente' : row['estado_despacho_descripcion']) + '</span>'
                }
            },
        ],
        columnDefs: [
            { 'aTargets': [0], 'sClass': 'invisible' },
            {
                'render': function (data, type, row) {

                    return `<button type="button" class="detalle btn btn-default btn-flat btn-xs " data-toggle="tooltip"
                    data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
                    <i class="fas fa-chevron-down"></i></button>
                    
                    ${row['count_transferencias'] > 0 ?
                            `<button type="button" class="transferencia btn btn-success btn-flat btn-xs " data-toggle="tooltip"
                    data-placement="bottom" title="Ver transferencias" data-id="${row['id_requerimiento']}">
                    <i class="fas fa-exchange-alt"></i></button>`: ''
                        }`;

                }, targets: 11
            }
        ],
        'order': [[0, "desc"]],
    });
    vista_extendida();
}

$('#requerimientosAlmacen tbody').on("click", "button.transferencia", function () {
    var id = $(this).data('id');
    if (id !== null) {

        $.ajax({
            type: 'GET',
            url: 'listarDetalleTransferencias/' + id,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                $('#modal-verTransferenciasPorRequerimiento').modal({
                    show: true
                });
                $('#transferenciasPorRequerimiento tbody').html(response);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
});

function abrir_transformacion(id_transformacion) {
    console.log('abrir_transformacio' + id_transformacion);
    localStorage.setItem("id_transfor", id_transformacion);
    var win = window.open("/cas/customizacion/hoja-transformacion/index", '_blank');
    win.focus();
}

var iTableCounter = 1;
var oInnerTable;

$('#requerimientosAlmacen tbody').on('click', 'td button.detalle', function () {
    var tr = $(this).closest('tr');
    var row = $tableRequerimientos.row(tr);
    var id = $(this).data('id');

    const $boton = $(this);
    // $boton.prop('disabled', true);

    if (row.child.isShown()) {
        //  This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        // $boton.prop('disabled', false);
    }
    else {
        format(iTableCounter, id, row, $boton);
        tr.addClass('shown');

        oInnerTable = $('#requerimientosAlmacen_' + iTableCounter).dataTable({
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
