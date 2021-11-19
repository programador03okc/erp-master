var table;
let despachos_seleccionados = [];
let usuarioSesion;

function listarRequerimientosPendientes(usuario) {
    usuarioSesion = usuario;
    var vardataTables = funcDatatables();
    let botones = [];
    // if (acceso == '1') {
    botones.push({
        text: ' Priorizar seleccionados',
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
        pageLength: 20,
        serverSide: true,
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

            const $form = $('#formFiltrosDespachoExterno');
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

            $("#selectMostrar").on("change", function (e) {
                var sed = $(this).val();
                $('#formFiltrosDespachoExterno').find('input[name=select_mostrar]').val(sed);
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
        ajax: {
            url: 'listarRequerimientosPendientesDespachoExterno',
            type: 'POST',
            data: function (params) {
                return Object.assign(params, objectifyForm($('#formFiltrosDespachoExterno').serializeArray()))
            }
        },
        columns: [
            { data: 'id_requerimiento', name: 'alm_req.id_requerimiento' },
            { data: 'id_od', name: 'orden_despacho.id_od' },
            { data: 'codigo', name: 'alm_req.codigo', className: "text-center" },
            {
                data: 'fecha_entrega', name: 'alm_req.fecha_entrega',
                // 'render': function (data, type, row) {
                //     return (row['fecha_entrega'] !== null ? formatDate(row['fecha_entrega']) : '');
                // }
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
            // {
            //     data: 'estado_doc', name: 'adm_estado_doc.bootstrap_color',
            //     'render': function (data, type, row) {
            //         return '<span class="label label-' + row['bootstrap_color'] + '">' + row['estado_doc'] + '</span>'
            //     }
            // },
            {
                data: 'fecha_despacho', name: 'orden_despacho.fecha_despacho',
                'render': function (data, type, row) {
                    return (row['fecha_despacho'] !== null ? formatDate(row['fecha_despacho']) : '');
                }
            },
            { data: 'codigo_od', name: 'orden_despacho.codigo', className: "text-center" },
            {
                'render': function (data, type, row) {
                    return '<span class="label label-' + row['estado_bootstrap_od'] + '">' + row['estado_od'] + '</span>'
                }
            },
            { data: 'id_od', name: 'orden_despacho.id_od' },
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
                'render': function (data, type, row) {//
                    return `<div>
                    <div style="display:flex;"> 
                        <button type="button" class="detalle btn btn-default btn-flat btn-xs " data-toggle="tooltip"
                        data-placement="bottom" title="Ver Detalle" data-id="${row['id_requerimiento']}">
                        <i class="fas fa-chevron-down"></i></button>
                        
                        <button type="button" class="envio_od btn btn-${row['id_od'] !== null ? 'warning' : 'default'} btn-flat btn-xs " data-toggle="tooltip"
                        data-placement="bottom" title="Enviar Orden de Despacho" data-id="${row['id_requerimiento']}"
                        data-fentrega="${row['fecha_entrega']}" data-cdp="${row['codigo_oportunidad']}">
                        <i class="far fa-envelope"></i></button>`+

                        // <button type="button" class="trazabilidad btn btn-warning btn-flat btn-xs " data-toggle="tooltip"
                        //     data-placement="bottom" title="Ver Trazabilidad de Docs"  data-id="${row['id_requerimiento']}">
                        //     <i class="fas fa-route"></i></button>

                        /*(row['id_od'] == null && row['productos_no_mapeados'] == 0)*/
                        `<button type="button" class="contacto btn btn-${(row['id_contacto'] !== null && row['enviar_contacto']) ? 'success' : 'default'} btn-flat btn-xs " 
                            data-toggle="tooltip" data-placement="bottom" data-id="${row['id_od']}" title="Datos del contacto" >
                            <i class="fas fa-id-badge"></i></button>`+
                        (row['id_od'] !== null ?
                            `<button type="button" class="transportista btn btn-${row['id_transportista'] !== null ? 'info' : 'default'} btn-flat btn-xs " data-toggle="tooltip"
                            data-placement="bottom" data-od="${row['id_od']}" data-idreq="${row['id_requerimiento']}" title="Agencia de transporte" >
                            <i class="fas fa-truck"></i></button>
                            
                            <button type="button" class="estados btn btn-${row["count_estados_envios"] > 0 ? 'primary' : 'default'} btn-flat btn-xs" data-toggle="tooltip" 
                            data-placement="bottom" title="Ver Trazabilidad de Envío" data-id="${row["id_od"]}">
                            <i class="fas fa-route"></i></button></div>`: '') +
                        // ((row['id_od'] !== null && parseInt(row['estado_od']) == 1) ?
                        //     `<button type="button" class="anular_od btn btn-flat btn-danger btn-xs boton" data-toggle="tooltip" 
                        //             data-placement="bottom" data-id="${row['id_od']}" data-cod="${row['codigo_od']}" title="Anular Orden Despacho Externo" >
                        //             <i class="fas fa-trash"></i></button>` : '') +
                        /*(row["nro_orden"] !== null && row['productos_no_mapeados'] == 0
                           ? `<button type="button" class="facturar btn btn-flat btn-xs btn-${row["enviar_facturacion"] ? "info" : "default"} 
                                   boton" data-toggle="tooltip" data-placement="bottom" title="Enviar a Facturación" 
                                   data-id="${row["id_requerimiento"]}" data-cod="${row["codigo"]}" data-envio="${row["enviar_facturacion"]}">
                                   <i class="fas fa-file-upload"></i></button>`
                           : '')*/
                        `</div>`
                }, targets: 12
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
            console.log(data.id_od);
            if (data.id_od !== null) {
                if (this.checked) {
                    despachos_seleccionados.push(data.id_od);
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
            } else {
                Lobibox.notify("error", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: 'El requerimiento seleccionado no cuenta con una orden de despacho aún.'
                });
                // this.checked = false;
                // console.log(this.checked);
                // cell.checkboxes.select(this.checked);
            }
        }
    });
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

$('#requerimientosEnProceso tbody').on("click", "button.envio_od", function (e) {
    $(e.preventDefault());
    // var id = $(this).data('id');
    // var fecha = $(this).data('fentrega');
    // var cdp = $(this).data('cdp');
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    openOrdenDespachoEnviar(data);
});

$('#requerimientosEnProceso tbody').on("click", "button.anular", function () {
    var id = $(this).data('id');
    var cod = $(this).data('cod');
    var origen = 'despacho';
    openRequerimientoObs(id, cod, origen);
});

$('#requerimientosEnProceso tbody').on("click", "button.contacto", function () {
    // var id = $(this).data('id');
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    console.log(data);
    // tab_origen = 'enProceso';
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
    mostrarTrazabilidad(id)
});

$("#requerimientosEnProceso tbody").on("click", "button.transportista", function () {
    // var id_od = $(this).data("od");
    // var id_req = $(this).data("idreq");
    var data = $('#requerimientosEnProceso').DataTable().row($(this).parents("tr")).data();
    openAgenciaTransporte(data);
});


function priorizar() {
    var valida = 0;

    despachos_seleccionados.forEach(element => {
        console.log(element);
        if (element == null) {
            valida++;
        }
    });

    if (valida > 0) {
        Lobibox.notify("error", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: 'Hay ' + valida + ' requerimientos que no tienen Despacho Externo.'
        });
    }
    else {
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
                var data = 'despachos_externos=' + JSON.stringify(despachos_seleccionados)
                    + '&fecha_despacho=' + fecha;
                console.log(data);
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
                                msg: 'Despachos Externos priorizados correctamente.'
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
}

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

    const $boton = $(this);
    $boton.prop('disabled', true);

    if (row.child.isShown()) {
        //  This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $boton.prop('disabled', false);
    }
    else {
        format(iTableCounter, id, row, $boton);
        tr.addClass('shown');

        oInnerTable = $('#requerimientosEnProceso_' + iTableCounter).dataTable({
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


$("#requerimientosEnProceso tbody").on("click", "td button.estados", function () {
    var tr = $(this).closest("tr");
    var row = table.row(tr);
    var id = $(this).data("id");

    if (id !== null) {

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass("shown");
        } else {
            formatTimeLine(iTableCounter, id, row);
            tr.addClass("shown");
            oInnerTable = $("#requerimientosEnProceso_" + iTableCounter).dataTable({
                //    data: sections,
                autoWidth: true,
                deferRender: true,
                info: false,
                lengthChange: false,
                ordering: false,
                paging: false,
                scrollX: true,
                scrollY: false,
                searching: false,
                columns: []
            });
            iTableCounter = iTableCounter + 1;
        }
    } else {
        Lobibox.notify("error", {
            title: false,
            size: "mini",
            rounded: true,
            sound: false,
            delayIndicator: false,
            msg: "El requerimiento seleccionado no tiene un despacho externo."
        });
    }
});

function abrir_requerimiento(id_requerimiento) {
    localStorage.setItem("id_requerimiento", id_requerimiento);
    let url = "/necesidades/requerimiento/elaboracion/index";
    var win = window.open(url, '_blank');
    win.focus();
}