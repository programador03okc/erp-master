var table;
let despachos_seleccionados = [];

function listarRequerimientosPendientes() {
    var vardataTables = funcDatatables();
    let botones = [];
    // if (acceso == '1') {
    botones.push({
        text: ' Priorizar',
        action: function () {
            priorizar();
        }, className: 'btn-primary disabled btnPriorizar'
    });
    // }

    $("#requerimientosEnProceso").on('search.dt', function () {
        $('#requerimientosEnProceso_filter input').prop('disabled', true);
        $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
    });

    $("#requerimientosEnProceso").on('processing.dt', function (e, settings, processing) {
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

    table = $('#requerimientosEnProceso').DataTable({
        dom: vardataTables[1],
        buttons: botones,
        language: vardataTables[0],
        // 'bDestroy': true,
        serverSide: true,
        pageLength: 20,
        initComplete: function (settings, json) {
            const $filter = $("#requerimientosEnProceso_filter");
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
                table.search($input.val()).draw();
            });

            const $form = $('#formFiltrosDespachoInterno');
            const factual = fecha_actual();
            $('#requerimientosEnProceso_wrapper .dt-buttons').append(
                `<div style="display:flex">
                    <input type="date" class="form-control " size="10" id="txtFechaPriorizacion" 
                        style="background-color:#d2effa;" value="${factual}"/>
                    <label style="text-align: center;margin-left: 20px;margin-top: 7px;margin-right: 10px;">Mostrar: </label>
                    <select class="form-control" id="selectMostrar">
                        <option value="0" selected>Todos</option>
                        <option value="1" >Priorizados</option>
                        <option value="2" >Los de Hoy</option>
                    </select>
                    
                </div>`
            );
            // $('input.date-picker').datepicker({
            //     language: "es",
            //     orientation: "bottom auto",
            //     format: 'dd-mm-yyyy',
            //     autoclose: true
            // });

            $("#selectMostrar").on("change", function (e) {
                var sed = $(this).val();
                $('#formFiltrosDespachoInterno').find('input[name=select_mostrar]').val(sed);
                $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
            });
        },
        drawCallback: function (settings) {
            $("#requerimientosEnProceso_filter input").prop("disabled", false);
            $("#btnBuscar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>'
            ).prop("disabled", false);
            //$('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
            $('#requerimientosEnProceso tbody tr td input[type="checkbox"]').iCheck({
                checkboxClass: "icheckbox_flat-blue"
            });
            $("#requerimientosEnProceso_filter input").trigger("focus");
        },
        // 'ajax': 'listarRequerimientosPendientesDespachoInterno',
        ajax: {
            url: 'listarRequerimientosPendientesDespachoInterno',
            type: 'POST',
            data: function (params) {
                return Object.assign(params, objectifyForm($('#formFiltrosDespachoInterno').serializeArray()))
            }
        },
        columns: [
            { data: 'id_od' },
            { data: 'id_requerimiento', name: 'alm_req.id_requerimiento' },
            { data: 'codigo', name: 'alm_req.codigo', className: "text-center" },
            {
                data: 'fecha_despacho', name: 'orden_despacho.fecha_despacho',
                'render': function (data, type, row) {
                    return (row['fecha_despacho'] !== null ? formatDate(row['fecha_despacho']) : '');
                }
            },
            {
                data: 'nro_orden', name: 'oc_propias_view.nro_orden',
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
            { data: 'codigo_oportunidad', name: 'oc_propias_view.codigo_oportunidad' },
            { data: 'cliente_razon_social', name: 'adm_contri.razon_social' },
            { data: 'responsable', name: 'sis_usua.nombre_corto' },
            { data: 'sede_descripcion_req', name: 'sede_req.descripcion', className: "text-center" },
            {
                data: 'estado_doc', name: 'adm_estado_doc.estado_doc',
                'render': function (data, type, row) {
                    return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>'
                }
            },
            { data: 'codigo_transformacion', name: 'transformacion.codigo' },
            {
                'render': function (data, type, row) {
                    return '<span class="label label-' + row['estado_bootstrap_transformacion'] + '">' + row['estado_transformacion'] + '</span>'
                }
            },
            { data: 'estado_od', name: 'est_od.estado_doc' },
        ],
        columnDefs: [
            { targets: [0], className: "invisible" },
            {
                targets: 1,
                searchable: false,
                orderable: false,
                className: "dt-body-center",
                checkboxes: {
                    selectRow: true,
                    selectCallback: function (nodes, selected) {
                        $('input[type="checkbox"]', nodes).iCheck("update");
                    },
                    selectAllCallback: function (
                        nodes,
                        selected,
                        indeterminate
                    ) {
                        $('input[type="checkbox"]', nodes).iCheck("update");
                    }
                }
            },
            {
                render: function (data, type, row) {
                    return (
                        '<a href="#" class="verRequerimiento" data-id="' + row["id_requerimiento"] + '" >' + row["codigo"] + "</a>" + (row['tiene_transformacion'] ? ' <i class="fas fa-random red"></i>' : '')
                    );
                }, targets: 2
            },
            {
                render: function (data, type, row) {
                    return ('<a href="#" class="verTransformacion" data-id="' + row["id_transformacion"] + '" >' + row["codigo_transformacion"] + "</a>");
                }, targets: 10
            },
            {
                'render': function (data, type, row) {
                    return `<div style="display:flex;">
                        <button type="button" class="detalle btn btn-default btn-flat btn-xs boton" data-toggle="tooltip"
                            data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
                            <i class="fas fa-chevron-down"></i></button>

                        <button type="button" class="trazabilidad btn btn-warning btn-flat btn-xs boton" data-toggle="tooltip"
                            data-placement="bottom" title="Ver Trazabilidad de Docs"  data-id="${row['id_requerimiento']}">
                            <i class="fas fa-route"></i></button>
                        
                        </div>`

                }, targets: 13
            }
        ],
        select: "multi",
        order: [[3, "asc"], [0, "desc"]],
    });
    vista_extendida();

    $($("#requerimientosEnProceso").DataTable().table().container()).on("ifChanged", ".dt-checkboxes", function (event) {
        var cell = $("#requerimientosEnProceso").DataTable().cell($(this).closest("td"));
        cell.checkboxes.select(this.checked);

        var data = $("#requerimientosEnProceso").DataTable().row($(this).parents("tr")).data();

        if (data !== null && data !== undefined) {
            if (this.checked) {
                despachos_seleccionados.push(data);
                $('.btnPriorizar').removeClass('disabled');
            } else {
                var index = despachos_seleccionados.findIndex(function (item, i) {
                    return item.id_od == data.id_od;
                });
                if (index !== null) {
                    despachos_seleccionados.splice(index, 1);
                    $('.btnPriorizar').addClass('disabled');
                }
            }
        }
    });
}

$("#requerimientosEnProceso tbody").on("click", "a.verTransformacion", function (e) {
    $(e.preventDefault());
    var id_transformacion = $(this).data("id");
    // localStorage.setItem("id_transfor", id_transformacion);
    var win = window.open("/cas/customizacion/hoja-transformacion/imprimir_transformacion/" + id_transformacion, '_blank');
    win.focus();
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

// $('#requerimientosEnProceso tbody').on("click", "button.adjuntar", function () {
//     var id = $(this).data('id');
//     var cod = $(this).data('cod');
//     $('#modal-despachoAdjuntos').modal({
//         show: true
//     });
//     listarAdjuntos(id);
//     $('[name=id_od]').val(id);
//     $('[name=codigo_od]').val(cod);
//     $('[name=descripcion]').val('');
//     $('[name=archivo_adjunto]').val('');
//     $('[name=proviene_de]').val('enProceso');
// });

// $('#requerimientosEnProceso tbody').on("click", "button.priorizar", function () {
//     var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
//     console.log(data);
//     $('#modal-orden_despacho_priorizacion').modal({
//         show: true
//     });

//     $("#submit_orden_despacho").removeAttr("disabled");
//     $('#codigo_req').text(data.codigo);
//     $('#concepto').text(data.concepto);
//     $('[name=id_requerimiento]').val(data.id_requerimiento);
// });

function priorizar() {
    let fecha = $('#txtFechaPriorizacion').val();

    Swal.fire({
        title: "¿Está seguro que desea priorizar con la fecha: " + formatDate(fecha) + "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#00a65a", //"#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, Guardar"
    }).then(result => {

        if (result.isConfirmed) {
            var data = 'despachos_internos=' + JSON.stringify(despachos_seleccionados)
                + '&fecha_despacho=' + fecha;
            $.ajax({
                type: 'POST',
                url: 'priorizar',
                data: data,
                dataType: 'JSON',
                success: function (response) {
                    if (response == 'ok') {
                        Lobibox.notify("success", {
                            title: false,
                            size: "mini",
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: 'Despachos Internos priorizados correctamente.'
                        });
                        $("#requerimientosEnProceso").DataTable().ajax.reload(null, false);
                    } else {
                        Lobibox.notify("error", {
                            title: false,
                            size: "mini",
                            rounded: true,
                            sound: false,
                            delayIndicator: false,
                            msg: 'Ha ocurrido un error interno. Inténtelo nuevamente.'
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